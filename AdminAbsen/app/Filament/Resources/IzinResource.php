<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IzinResource\Pages;
use App\Filament\Resources\IzinResource\RelationManagers;
use App\Models\Izin;
use App\Models\Pegawai;
use App\Models\ManajemenIzin;
use App\Helpers\UserHelper;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Filament\Facades\Filament;

class IzinResource extends Resource
{
    protected static ?string $model = Izin::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Data Izin';

    protected static ?string $modelLabel = 'Izin';

    protected static ?string $pluralModelLabel = 'Data Izin';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Izin')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->label('Nama Pegawai')
                                    ->relationship('user', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->disabled(),

                                Forms\Components\Select::make('jenis_izin')
                                    ->label('Jenis Izin')
                                    ->options(ManajemenIzin::getSelectOptions())
                                    ->disabled(),

                                Forms\Components\DatePicker::make('tanggal_mulai')
                                    ->label('Tanggal Mulai')
                                    ->disabled(),

                                Forms\Components\DatePicker::make('tanggal_akhir')
                                    ->label('Tanggal Akhir')
                                    ->disabled(),

                                Forms\Components\Textarea::make('keterangan')
                                    ->label('Keterangan')
                                    ->rows(3)
                                    ->columnSpanFull()
                                    ->disabled(),

                                Forms\Components\FileUpload::make('dokumen_pendukung')
                                    ->label('Dokumen Pendukung')
                                    ->directory('izin-documents')
                                    ->acceptedFileTypes(['pdf', 'jpg', 'jpeg', 'png'])
                                    ->maxSize(2048)
                                    ->disabled()
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Forms\Components\Section::make('Status Persetujuan')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('approved_by')
                                    ->label('Disetujui Oleh')
                                    ->relationship('approvedBy', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->disabled(),

                                Forms\Components\DateTimePicker::make('approved_at')
                                    ->label('Tanggal Disetujui')
                                    ->disabled(),
                            ]),
                    ])
                    ->visible(fn (Izin $record): bool => $record->approved_by !== null),
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
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('jenis_izin')
                    ->label('Jenis Izin')
                    ->getStateUsing(function (Izin $record): string {
                        $jenisIzinData = ManajemenIzin::where('kode_izin', $record->jenis_izin)->first();
                        return $jenisIzinData ? $jenisIzinData->nama_izin : ucfirst($record->jenis_izin);
                    })
                    ->colors([
                        'primary' => fn (Izin $record) => $record->jenisIzinData?->kategori === 'cuti',
                        'warning' => fn (Izin $record) => $record->jenisIzinData?->kategori === 'izin_khusus',
                        'danger' => fn (Izin $record) => $record->jenisIzinData?->kategori === 'sakit',
                        'info' => fn (Izin $record) => $record->jenisIzinData?->kategori === 'dinas',
                    ]),

                Tables\Columns\TextColumn::make('periode_izin')
                    ->label('Periode Izin')
                    ->sortable('tanggal_mulai'),

                Tables\Columns\TextColumn::make('durasi_hari')
                    ->label('Durasi')
                    ->formatStateUsing(fn (int $state): string => $state . ' hari')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function (Izin $record): string {
                        return $record->status_badge['label'];
                    })
                    ->colors([
                        'warning' => fn ($state) => $state === 'Menunggu',
                        'success' => fn ($state) => $state === 'Disetujui',
                        'danger' => fn ($state) => $state === 'Ditolak',
                    ]),

                Tables\Columns\TextColumn::make('approval_info')
                    ->label('Info Persetujuan')
                    ->getStateUsing(function (Izin $record): string {
                        return $record->approval_info;
                    })
                    ->wrap()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('approvedBy.nama')
                    ->label('Disetujui Oleh')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('approved_at')
                    ->label('Tanggal Disetujui')
                    ->dateTime()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Diajukan Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_izin')
                    ->label('Jenis Izin')
                    ->options(ManajemenIzin::getSelectOptions())
                    ->native(false),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            function (Builder $query, $value): Builder {
                                return match($value) {
                                    'pending' => $query->pending(),
                                    'approved' => $query->approved(),
                                    'rejected' => $query->rejected(),
                                    default => $query
                                };
                            }
                        );
                    })
                    ->native(false),

                Tables\Filters\Filter::make('tanggal_mulai')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_mulai', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_akhir', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Izin')
                    ->modalDescription(function (Izin $record): string {
                        $currentUser = UserHelper::getCurrentUser();
                        $displayName = UserHelper::getDisplayName();
                        return "Apakah Anda yakin ingin menyetujui izin ini?\n\nIzin akan tercatat disetujui oleh: {$displayName}";
                    })
                    ->action(function (Izin $record): void {
                        $validation = UserHelper::validateForApproval();
                        
                        if (!$validation['success']) {
                            Notification::make()
                                ->danger()
                                ->title('Akses Ditolak')
                                ->body($validation['message'])
                                ->send();
                            return;
                        }

                        $record->approve(Filament::auth()->id());

                        Notification::make()
                            ->success()
                            ->title('Izin Disetujui')
                            ->body("Izin telah berhasil disetujui oleh {$validation['user']->nama}")
                            ->send();
                    })
                    ->visible(function (Izin $record): bool {
                        return $record->status === 'pending' && UserHelper::canApprove();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Izin')
                    ->modalDescription(function (Izin $record): string {
                        $displayName = UserHelper::getDisplayName();
                        return "Apakah Anda yakin ingin menolak izin ini?\n\nIzin akan tercatat ditolak oleh: {$displayName}";
                    })
                    ->action(function (Izin $record): void {
                        $validation = UserHelper::validateForApproval();
                        
                        if (!$validation['success']) {
                            Notification::make()
                                ->danger()
                                ->title('Akses Ditolak')
                                ->body($validation['message'])
                                ->send();
                            return;
                        }

                        $record->reject(Filament::auth()->id());

                        Notification::make()
                            ->success()
                            ->title('Izin Ditolak')
                            ->body("Izin telah berhasil ditolak oleh {$validation['user']->nama}")
                            ->send();
                    })
                    ->visible(function (Izin $record): bool {
                        return $record->status === 'pending' && UserHelper::canApprove();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_approve')
                        ->label('Setujui Terpilih')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Setujui Izin Terpilih')
                        ->modalDescription(function (Collection $records): string {
                            $displayName = UserHelper::getDisplayName();
                            $pendingCount = $records->filter(fn (Izin $record) => $record->status === 'pending')->count();
                            return "Apakah Anda yakin ingin menyetujui {$pendingCount} izin?\n\nSemua izin akan tercatat disetujui oleh: {$displayName}";
                        })
                        ->action(function (Collection $records): void {
                            $validation = UserHelper::validateForApproval();
                            
                            if (!$validation['success']) {
                                Notification::make()
                                    ->danger()
                                    ->title('Akses Ditolak')
                                    ->body($validation['message'])
                                    ->send();
                                return;
                            }

                            $pending = $records->filter(fn (Izin $record) => $record->status === 'pending');

                            foreach ($pending as $record) {
                                $record->approve(Filament::auth()->id());
                            }

                            Notification::make()
                                ->success()
                                ->title('Izin Disetujui')
                                ->body($pending->count() . " izin telah berhasil disetujui oleh {$validation['user']->nama}")
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('bulk_reject')
                        ->label('Tolak Terpilih')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Tolak Izin Terpilih')
                        ->modalDescription(function (Collection $records): string {
                            $displayName = UserHelper::getDisplayName();
                            $pendingCount = $records->filter(fn (Izin $record) => $record->status === 'pending')->count();
                            return "Apakah Anda yakin ingin menolak {$pendingCount} izin?\n\nSemua izin akan tercatat ditolak oleh: {$displayName}";
                        })
                        ->action(function (Collection $records): void {
                            $validation = UserHelper::validateForApproval();
                            
                            if (!$validation['success']) {
                                Notification::make()
                                    ->danger()
                                    ->title('Akses Ditolak')
                                    ->body($validation['message'])
                                    ->send();
                                return;
                            }

                            $pending = $records->filter(fn (Izin $record) => $record->status === 'pending');

                            foreach ($pending as $record) {
                                $record->reject(Filament::auth()->id());
                            }

                            Notification::make()
                                ->success()
                                ->title('Izin Ditolak')
                                ->body($pending->count() . " izin telah berhasil ditolak oleh {$validation['user']->nama}")
                                ->send();
                        }),
                ])->visible(function (): bool {
                    return UserHelper::canApprove();
                }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIzins::route('/'),
            'view' => Pages\ViewIzin::route('/{record}'),
            // Tidak ada create/edit karena izin hanya untuk approve/reject
        ];
    }
}
