<?php

namespace App\Filament\KepalaBidang\Resources;

use App\Filament\KepalaBidang\Resources\IzinApprovalResource\Pages;
use App\Models\Izin;
use App\Models\Pegawai;
use App\Helpers\DocumentHelper;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class IzinApprovalResource extends Resource
{
    protected static ?string $model = Izin::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Persetujuan Izin';

    protected static ?string $modelLabel = 'Persetujuan Izin';

    protected static ?string $pluralModelLabel = 'Persetujuan Izin';

    protected static ?string $navigationGroup = 'Persetujuan';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        // Show izin from team members (for demo, show all employee izin)
        $teamMembers = Pegawai::where('role_user', 'employee')
            ->where('status', 'active')
            ->pluck('id');

        return parent::getEloquentQuery()->whereIn('user_id', $teamMembers);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Pegawai')
                    ->relationship('user', 'nama')
                    ->disabled(),

                Forms\Components\Select::make('jenis_izin')
                    ->label('Jenis Izin')
                    ->options([
                        'sakit' => 'Sakit',
                        'cuti' => 'Cuti',
                        'izin' => 'Izin',
                    ])
                    ->disabled(),

                Forms\Components\DatePicker::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->disabled(),

                Forms\Components\DatePicker::make('tanggal_akhir')
                    ->label('Tanggal Akhir')
                    ->disabled(),

                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan/Alasan')
                    ->disabled()
                    ->rows(3),

                Forms\Components\FileUpload::make('dokumen_pendukung')
                    ->label('Dokumen Pendukung')
                    ->disabled(),

                Forms\Components\Section::make('Status Persetujuan')
                    ->schema([
                        Forms\Components\Select::make('approved_by')
                            ->label('Disetujui Oleh')
                            ->relationship('approvedBy', 'nama')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('approved_at')
                            ->label('Tanggal Disetujui')
                            ->disabled(),
                    ])
                    ->visible(fn ($record) => $record && !is_null($record->approved_by)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.nama')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.npp')
                    ->label('NPP')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_akhir')
                    ->label('Tanggal Akhir')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('jenis_izin')
                    ->label('Jenis Izin')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'sakit' => 'danger',
                        'cuti' => 'success',
                        'izin' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50),

                Tables\Columns\TextColumn::make('dokumen_pendukung')
                    ->label('Dokumen')
                    ->formatStateUsing(function (?string $state, Izin $record): string {
                        return DocumentHelper::getDocumentPreviewHtml($state, $record);
                    })
                    ->html()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('approval_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($record): string => match (true) {
                        is_null($record->approved_by) => 'warning',
                        !is_null($record->approved_at) => 'success',
                        default => 'danger',
                    })
                    ->formatStateUsing(fn ($record): string => match (true) {
                        is_null($record->approved_by) => 'Menunggu',
                        !is_null($record->approved_at) => 'Disetujui',
                        default => 'Ditolak',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Pengajuan')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_izin')
                    ->options([
                        'sakit' => 'Sakit',
                        'cuti' => 'Cuti',
                        'izin' => 'Izin',
                    ]),

                Tables\Filters\Filter::make('status_pending')
                    ->label('Menunggu Persetujuan')
                    ->query(fn (Builder $query): Builder => $query->whereNull('approved_by')),

                Tables\Filters\Filter::make('status_approved')
                    ->label('Disetujui')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('approved_by')->whereNotNull('approved_at')),

                Tables\Filters\Filter::make('status_rejected')
                    ->label('Ditolak')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('approved_by')->whereNull('approved_at')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(function ($record) {
                        $currentUser = Auth::user();
                        return is_null($record->approved_by) && $currentUser->role_user !== 'super admin';
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Izin')
                    ->modalDescription('Apakah Anda yakin ingin menyetujui izin ini?')
                    ->action(function ($record) {
                        $record->approve(Auth::id());

                        Notification::make()
                            ->success()
                            ->title('Izin Disetujui')
                            ->body("Izin {$record->user->nama} telah disetujui.")
                            ->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(function ($record) {
                        $currentUser = Auth::user();
                        return is_null($record->approved_by) && $currentUser->role_user !== 'super admin';
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Izin')
                    ->modalDescription('Apakah Anda yakin ingin menolak izin ini?')
                    ->action(function ($record) {
                        $record->reject(Auth::id());

                        Notification::make()
                            ->success()
                            ->title('Izin Ditolak')
                            ->body("Izin {$record->user->nama} telah ditolak.")
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('bulk_approve')
                    ->label('Setujui yang Dipilih')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Izin Terpilih')
                    ->modalDescription('Apakah Anda yakin ingin menyetujui semua izin yang dipilih?')
                    ->action(function ($records) {
                        $currentUser = Auth::user();

                        // Cek apakah user adalah super admin
                        if ($currentUser->role_user === 'super admin') {
                            Notification::make()
                                ->danger()
                                ->title('Akses Ditolak')
                                ->body('Super Admin tidak diperbolehkan melakukan approval/reject izin.')
                                ->send();
                            return;
                        }

                        $count = 0;
                        foreach ($records as $record) {
                            if (is_null($record->approved_by)) {
                                $record->approve(Auth::id());
                                $count++;
                            }
                        }

                        Notification::make()
                            ->success()
                            ->title('Izin Disetujui')
                            ->body("{$count} izin telah disetujui.")
                            ->send();
                    })
                    ->visible(function () {
                        $currentUser = Auth::user();
                        return $currentUser->role_user !== 'super admin';
                    }),

                Tables\Actions\BulkAction::make('bulk_reject')
                    ->label('Tolak yang Dipilih')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Izin Terpilih')
                    ->modalDescription('Apakah Anda yakin ingin menolak semua izin yang dipilih?')
                    ->action(function ($records) {
                        $currentUser = Auth::user();

                        // Cek apakah user adalah super admin
                        if ($currentUser->role_user === 'super admin') {
                            Notification::make()
                                ->danger()
                                ->title('Akses Ditolak')
                                ->body('Super Admin tidak diperbolehkan melakukan approval/reject izin.')
                                ->send();
                            return;
                        }

                        $count = 0;
                        foreach ($records as $record) {
                            if (is_null($record->approved_by)) {
                                $record->reject(Auth::id());
                                $count++;
                            }
                        }

                        Notification::make()
                            ->success()
                            ->title('Izin Ditolak')
                            ->body("{$count} izin telah ditolak.")
                            ->send();
                    })
                    ->visible(function () {
                        $currentUser = Auth::user();
                        return $currentUser->role_user !== 'super admin';
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIzinApprovals::route('/'),
            'view' => Pages\ViewIzinApproval::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
