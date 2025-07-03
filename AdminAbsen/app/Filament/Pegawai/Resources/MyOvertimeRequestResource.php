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

    public static function getNavigationGroup(): ?string
    {
        return ' Lembur';
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

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
                                    ->default(function () {
                                        // Auto-generate overtime ID dengan format: OT-YYYYMMDD-XXXX
                                        $date = now()->format('Ymd');
                                        $lastRecord = \App\Models\OvertimeAssignment::whereDate('created_at', now())
                                            ->orderBy('id', 'desc')
                                            ->first();

                                        $sequence = $lastRecord ? (int)substr($lastRecord->overtime_id, -4) + 1 : 1;
                                        return 'OT-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
                                    })
                                    ->disabled()
                                    ->helperText('ID otomatis dibuat oleh sistem')
                                    ->columnSpan(1),

                                Forms\Components\Select::make('hari_lembur')
                                    ->label('Hari Lembur')
                                    ->options([
                                        'Senin' => 'Senin',
                                        'Selasa' => 'Selasa',
                                        'Rabu' => 'Rabu',
                                        'Kamis' => 'Kamis',
                                        'Jumat' => 'Jumat',
                                        'Sabtu' => 'Sabtu',
                                        'Minggu' => 'Minggu',
                                    ])
                                    ->required()
                                    ->reactive()
                                    ->columnSpan(1),

                                Forms\Components\DatePicker::make('tanggal_lembur')
                                    ->label('Tanggal Lembur')
                                    ->required()
                                    ->default(now())
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $dayName = \Carbon\Carbon::parse($state)->locale('id')->dayName;
                                            $set('hari_lembur', ucfirst($dayName));
                                        }
                                    })
                                    ->columnSpan(1),

                                Forms\Components\TimePicker::make('jam_mulai')
                                    ->label('Jam Mulai')
                                    ->required()
                                    ->default('17:00')
                                    ->seconds(false)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        $jamSelesai = $get('jam_selesai');
                                        if ($state && $jamSelesai) {
                                            $totalJam = \App\Models\OvertimeAssignment::calculateTotalJam($state, $jamSelesai);
                                            $set('total_jam', $totalJam);
                                        }
                                    })
                                    ->columnSpan(1),

                                Forms\Components\TimePicker::make('jam_selesai')
                                    ->label('Jam Selesai')
                                    ->required()
                                    ->default('20:00')
                                    ->seconds(false)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        $jamMulai = $get('jam_mulai');
                                        if ($state && $jamMulai) {
                                            $totalJam = \App\Models\OvertimeAssignment::calculateTotalJam($jamMulai, $state);
                                            $set('total_jam', $totalJam);
                                        }
                                    })
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('total_jam')
                                    ->label('Total Jam Lembur')
                                    ->disabled()
                                    ->formatStateUsing(function ($state) {
                                        if (!$state) return '0 jam 0 menit';

                                        $hours = floor($state / 60);
                                        $minutes = $state % 60;

                                        if ($hours > 0 && $minutes > 0) {
                                            return "{$hours} jam {$minutes} menit";
                                        } elseif ($hours > 0) {
                                            return "{$hours} jam";
                                        } else {
                                            return "{$minutes} menit";
                                        }
                                    })
                                    ->helperText('Dihitung otomatis berdasarkan jam mulai dan selesai')
                                    ->columnSpanFull(),

                                Forms\Components\DateTimePicker::make('assigned_at')
                                    ->label('Waktu Pengajuan')
                                    ->required()
                                    ->default(now())
                                    ->disabled()
                                    ->helperText('Waktu saat pengajuan dibuat')
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

                Tables\Columns\TextColumn::make('hari_lembur')
                    ->label('Hari')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('tanggal_lembur')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('jam_mulai')
                    ->label('Jam Mulai')
                    ->time('H:i')
                    ->color('success'),

                Tables\Columns\TextColumn::make('jam_selesai')
                    ->label('Jam Selesai')
                    ->time('H:i')
                    ->color('danger'),

                Tables\Columns\TextColumn::make('total_jam_formatted')
                    ->label('Total Jam')
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
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
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('approved_at')
                    ->label('Tanggal Diproses')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('assigned_at')
                    ->label('Waktu Pengajuan')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Assigned' => 'Menunggu Persetujuan',
                        'Accepted' => 'Disetujui',
                        'Rejected' => 'Ditolak',
                    ]),

                Tables\Filters\SelectFilter::make('hari_lembur')
                    ->label('Hari Lembur')
                    ->options([
                        'Senin' => 'Senin',
                        'Selasa' => 'Selasa',
                        'Rabu' => 'Rabu',
                        'Kamis' => 'Kamis',
                        'Jumat' => 'Jumat',
                        'Sabtu' => 'Sabtu',
                        'Minggu' => 'Minggu',
                    ]),

                Tables\Filters\Filter::make('bulan_ini')
                    ->label('Bulan Ini')
                    ->query(fn (Builder $query): Builder =>
                        $query->whereMonth('tanggal_lembur', now()->month)
                              ->whereYear('tanggal_lembur', now()->year)
                    ),

                Tables\Filters\Filter::make('minggu_ini')
                    ->label('Minggu Ini')
                    ->query(fn (Builder $query): Builder =>
                        $query->whereBetween('tanggal_lembur', [
                            now()->startOfWeek(),
                            now()->endOfWeek()
                        ])
                    ),

                Tables\Filters\Filter::make('tanggal_lembur')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_lembur', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_lembur', '<=', $date),
                            );
                    }),
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
