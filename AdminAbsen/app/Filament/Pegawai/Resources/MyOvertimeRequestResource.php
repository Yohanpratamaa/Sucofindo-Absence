<?php

namespace App\Filament\Pegawai\Resources;

use App\Filament\Pegawai\Resources\MyOvertimeRequestResource\Pages;
use App\Models\OvertimeAssignment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class MyOvertimeRequestResource extends Resource
{
    protected static ?string $model = OvertimeAssignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Pengajuan Lembur';

    protected static ?string $modelLabel = 'Pengajuan Lembur';

    protected static ?string $pluralModelLabel = 'Pengajuan Lembur';

    protected static ?string $navigationGroup = 'Lembur';

    protected static ?int $navigationSort = 31;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Pengajuan Lembur')
                    ->description('Ajukan lembur untuk pekerjaan di luar jam kerja reguler')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('overtime_id')
                                    ->label('ID Lembur')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('Contoh: OT-2025-001')
                                    ->helperText('ID unik untuk identifikasi lembur ini')
                                    ->columnSpan(1),

                                Forms\Components\DateTimePicker::make('assigned_at')
                                    ->label('Waktu Mulai Lembur')
                                    ->required()
                                    ->default(now())
                                    ->helperText('Kapan lembur akan dilaksanakan')
                                    ->columnSpan(1),

                                Forms\Components\Textarea::make('keterangan')
                                    ->label('Keterangan Lembur')
                                    ->required()
                                    ->rows(4)
                                    ->placeholder('Jelaskan alasan dan deskripsi pekerjaan lembur yang akan dilakukan...')
                                    ->columnSpanFull(),

                                Forms\Components\Hidden::make('user_id')
                                    ->default(Auth::id()),

                                Forms\Components\Hidden::make('assigned_by')
                                    ->default(Auth::id()),

                                Forms\Components\Hidden::make('status')
                                    ->default('Assigned'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('overtime_id')
                    ->label('ID Lembur')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('assigned_at')
                    ->label('Waktu Lembur')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'Assigned',
                        'success' => 'Accepted',
                        'danger' => 'Rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'Assigned' => 'Menunggu Persetujuan',
                        'Accepted' => 'Disetujui',
                        'Rejected' => 'Ditolak',
                        default => ucfirst($state),
                    }),

                Tables\Columns\TextColumn::make('approvedBy.nama')
                    ->label('Diproses Oleh')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('approved_at')
                    ->label('Tanggal Diproses')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('durasi_assignment')
                    ->label('Waktu Pengajuan')
                    ->placeholder('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Assigned' => 'Menunggu Persetujuan',
                        'Accepted' => 'Disetujui',
                        'Rejected' => 'Ditolak',
                    ]),

                Tables\Filters\Filter::make('bulan_ini')
                    ->label('Bulan Ini')
                    ->query(fn (Builder $query): Builder =>
                        $query->whereMonth('assigned_at', now()->month)
                              ->whereYear('assigned_at', now()->year)
                    ),

                Tables\Filters\Filter::make('minggu_ini')
                    ->label('Minggu Ini')
                    ->query(fn (Builder $query): Builder =>
                        $query->whereBetween('assigned_at', [
                            now()->startOfWeek(),
                            now()->endOfWeek()
                        ])
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Detail'),

                Tables\Actions\EditAction::make()
                    ->label('Ubah')
                    ->visible(fn (OvertimeAssignment $record): bool => $record->status === 'Assigned'),

                Tables\Actions\Action::make('cancel')
                    ->label('Batalkan')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Batalkan Pengajuan Lembur')
                    ->modalDescription('Apakah Anda yakin ingin membatalkan pengajuan lembur ini?')
                    ->action(function (OvertimeAssignment $record): void {
                        $record->delete();

                        Notification::make()
                            ->success()
                            ->title('Pengajuan Dibatalkan')
                            ->body('Pengajuan lembur berhasil dibatalkan.')
                            ->send();
                    })
                    ->visible(fn (OvertimeAssignment $record): bool => $record->status === 'Assigned'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('cancel_selected')
                    ->label('Batalkan Terpilih')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Batalkan Pengajuan Lembur')
                    ->modalDescription('Apakah Anda yakin ingin membatalkan semua pengajuan lembur yang dipilih?')
                    ->action(function ($records): void {
                        $cancelled = 0;
                        foreach ($records as $record) {
                            if ($record->status === 'Assigned') {
                                $record->delete();
                                $cancelled++;
                            }
                        }

                        Notification::make()
                            ->success()
                            ->title('Pengajuan Dibatalkan')
                            ->body("$cancelled pengajuan lembur berhasil dibatalkan.")
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum Ada Pengajuan Lembur')
            ->emptyStateDescription('Anda belum mengajukan lembur. Klik tombol "Ajukan Lembur" untuk membuat pengajuan baru.')
            ->emptyStateIcon('heroicon-o-clock');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyOvertimeRequests::route('/'),
            'create' => Pages\CreateMyOvertimeRequest::route('/create'),
            'view' => Pages\ViewMyOvertimeRequest::route('/{record}'),
            'edit' => Pages\EditMyOvertimeRequest::route('/{record}/edit'),
        ];
    }

    public static function canEdit($record): bool
    {
        return $record->status === 'Assigned';
    }

    public static function canDelete($record): bool
    {
        return $record->status === 'Assigned';
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('user_id', Auth::id())
            ->where('status', 'Assigned')
            ->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }
}
