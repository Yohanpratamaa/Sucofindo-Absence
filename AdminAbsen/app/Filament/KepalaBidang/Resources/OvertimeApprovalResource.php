<?php

namespace App\Filament\KepalaBidang\Resources;

use App\Filament\KepalaBidang\Resources\OvertimeApprovalResource\Pages;
use App\Models\OvertimeAssignment;
use App\Models\Pegawai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;

class OvertimeApprovalResource extends Resource
{
    protected static ?string $model = OvertimeAssignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Pengajuan Lembur';

    protected static ?string $modelLabel = 'Pengajuan Lembur';

    protected static ?string $pluralModelLabel = 'Pengajuan Lembur';

    protected static ?string $navigationGroup = 'Persetujuan';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        // Show overtime from team members (for demo, show all employee overtime)
        $teamMembers = Pegawai::where('role_user', 'employee')
            ->where('status', 'active')
            ->pluck('id');

        return parent::getEloquentQuery()->whereIn('user_id', $teamMembers);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Penugasan Lembur')
                    ->description('Assign lembur untuk pegawai di luar jam kerja reguler. Lembur yang di-assign kepala bidang akan langsung disetujui otomatis.')
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
                                        $lastRecord = OvertimeAssignment::whereDate('created_at', now())
                                            ->orderBy('id', 'desc')
                                            ->first();

                                        $sequence = $lastRecord ? (int)substr($lastRecord->overtime_id, -4) + 1 : 1;
                                        return 'OT-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
                                    })
                                    ->readOnly()
                                    ->dehydrated()
                                    ->helperText('ID otomatis dibuat oleh sistem')
                                    ->columnSpan(1),

                                Forms\Components\Select::make('user_id')
                                    ->label('Pilih Pegawai')
                                    ->options(function () {
                                        return Pegawai::where('role_user', 'employee')
                                            ->where('status', 'active')
                                            ->pluck('nama', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->placeholder('Pilih pegawai yang akan ditugaskan lembur')
                                    ->columnSpan(1),

                                Forms\Components\DatePicker::make('tanggal_lembur')
                                    ->label('Tanggal Lembur')
                                    ->required()
                                    ->default(now())
                                    ->reactive()
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
                                    ->readOnly()
                                    ->dehydrated()
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
                                    ->label('Waktu Penugasan')
                                    ->required()
                                    ->default(now())
                                    ->readOnly()
                                    ->dehydrated()
                                    ->helperText('Waktu saat penugasan dibuat')
                                    ->columnSpan(1),

                                Forms\Components\Textarea::make('keterangan')
                                    ->label('Keterangan Lembur')
                                    ->required()
                                    ->rows(4)
                                    ->placeholder('Jelaskan alasan dan deskripsi pekerjaan lembur yang akan ditugaskan kepada pegawai...')
                                    ->columnSpanFull(),

                                Forms\Components\Hidden::make('assigned_by')
                                    ->default(fn () => Auth::id()),

                                Forms\Components\Hidden::make('status')
                                    ->default('Accepted'), // Kepala bidang assign langsung approved

                                Forms\Components\Hidden::make('approved_by')
                                    ->default(fn () => Auth::id()),

                                Forms\Components\Hidden::make('approved_at')
                                    ->default(fn () => now()),
                            ]),
                    ]),

                Forms\Components\Section::make('Status Persetujuan')
                    ->description('Karena Anda sebagai kepala bidang yang menugaskan, lembur ini akan langsung disetujui otomatis.')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Placeholder::make('status_info')
                                    ->label('Status')
                                    ->content('Disetujui Otomatis')
                                    ->columnSpan(1),

                                Forms\Components\Placeholder::make('approved_info')
                                    ->label('Disetujui Oleh')
                                    ->content(fn () => Auth::user()->nama ?? 'Kepala Bidang')
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord),
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

                Tables\Columns\TextColumn::make('overtime_id')
                    ->label('ID Lembur')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('tanggal_lembur')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('jam_mulai')
                    ->label('Jam Mulai')
                    ->time('H:i')
                    ->color('success')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('jam_selesai')
                    ->label('Jam Selesai')
                    ->time('H:i')
                    ->color('danger')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('total_jam_formatted')
                    ->label('Total Jam')
                    ->badge()
                    ->color('warning')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan Lembur')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (OvertimeAssignment $record): ?string {
                        return $record->keterangan;
                    })
                    ->wrap(),

                Tables\Columns\TextColumn::make('assignedBy.nama')
                    ->label('Ditugaskan Oleh')
                    ->searchable(),

                Tables\Columns\TextColumn::make('assigned_at')
                    ->label('Waktu Penugasan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Assigned' => 'warning',
                        'Accepted' => 'success',
                        'Rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'Assigned' => 'Ditugaskan',
                        'Accepted' => 'Diterima',
                        'Rejected' => 'Ditolak',
                        default => ucfirst($state),
                    }),

                Tables\Columns\TextColumn::make('approval_info')
                    ->label('Info Persetujuan')
                    ->getStateUsing(function (OvertimeAssignment $record): string {
                        return $record->approval_info;
                    })
                    ->wrap()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Assigned' => 'Ditugaskan',
                        'Accepted' => 'Diterima',
                        'Rejected' => 'Ditolak',
                    ]),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Pegawai')
                    ->relationship('user', 'nama')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('assigned_at')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('assigned_at', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('assigned_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (OvertimeAssignment $record): bool => in_array($record->status, ['Assigned', 'Accepted'])),

                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(function (OvertimeAssignment $record) {
                        $currentUser = Auth::user();
                        return $record->status === 'Assigned' && !$currentUser->isSuperAdmin();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Lembur')
                    ->modalDescription('Apakah Anda yakin ingin menyetujui pengajuan lembur ini?')
                    ->action(function (OvertimeAssignment $record) {
                        $record->accept(Auth::id());

                        Notification::make()
                            ->success()
                            ->title('Lembur Disetujui')
                            ->body("Pengajuan lembur {$record->user->nama} telah disetujui.")
                            ->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(function (OvertimeAssignment $record) {
                        $currentUser = Auth::user();
                        return $record->status === 'Assigned' && !$currentUser->isSuperAdmin();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Lembur')
                    ->modalDescription('Apakah Anda yakin ingin menolak pengajuan lembur ini?')
                    ->action(function (OvertimeAssignment $record) {
                        $record->reject(Auth::id());

                        Notification::make()
                            ->success()
                            ->title('Lembur Ditolak')
                            ->body("Pengajuan lembur {$record->user->nama} telah ditolak.")
                            ->send();
                    }),

                Tables\Actions\Action::make('reassign')
                    ->label('Assign Ulang')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->form([
                        Forms\Components\Select::make('new_user_id')
                            ->label('Pegawai Baru')
                            ->options(function () {
                                return Pegawai::where('role_user', 'employee')
                                    ->where('status', 'active')
                                    ->pluck('nama', 'id');
                            })
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (OvertimeAssignment $record, array $data): void {
                        $record->reassign($data['new_user_id'], Auth::id());

                        Notification::make()
                            ->success()
                            ->title('Lembur Di-assign Ulang')
                            ->body('Penugasan lembur telah berhasil di-assign ulang.')
                            ->send();
                    })
                    ->visible(fn (OvertimeAssignment $record): bool => in_array($record->status, ['Rejected', 'Assigned', 'Accepted'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('bulk_approve')
                    ->label('Setujui yang Dipilih')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Lembur Terpilih')
                    ->modalDescription('Apakah Anda yakin ingin menyetujui semua pengajuan lembur yang dipilih?')
                    ->action(function ($records) {
                        $currentUser = Auth::user();

                        // Cek apakah user adalah super admin
                        if ($currentUser->isSuperAdmin()) {
                            Notification::make()
                                ->danger()
                                ->title('Akses Ditolak')
                                ->body('Super Admin tidak diperbolehkan melakukan approval/reject lembur.')
                                ->send();
                            return;
                        }

                        $count = 0;
                        foreach ($records as $record) {
                            if ($record->status === 'Assigned') {
                                $record->accept(Auth::id());
                                $count++;
                            }
                        }

                        Notification::make()
                            ->success()
                            ->title('Lembur Disetujui')
                            ->body("{$count} pengajuan lembur telah disetujui.")
                            ->send();
                    })
                    ->visible(function () {
                        $currentUser = Auth::user();
                        return !$currentUser->isSuperAdmin();
                    }),

                Tables\Actions\BulkAction::make('bulk_reject')
                    ->label('Tolak yang Dipilih')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Lembur Terpilih')
                    ->modalDescription('Apakah Anda yakin ingin menolak semua pengajuan lembur yang dipilih?')
                    ->action(function ($records) {
                        $currentUser = Auth::user();

                        // Cek apakah user adalah super admin
                        if ($currentUser->isSuperAdmin()) {
                            Notification::make()
                                ->danger()
                                ->title('Akses Ditolak')
                                ->body('Super Admin tidak diperbolehkan melakukan approval/reject lembur.')
                                ->send();
                            return;
                        }

                        $count = 0;
                        foreach ($records as $record) {
                            if ($record->status === 'Assigned') {
                                $record->reject(Auth::id());
                                $count++;
                            }
                        }

                        Notification::make()
                            ->success()
                            ->title('Lembur Ditolak')
                            ->body("{$count} pengajuan lembur telah ditolak.")
                            ->send();
                    })
                    ->visible(function () {
                        $currentUser = Auth::user();
                        return !$currentUser->isSuperAdmin();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOvertimeApprovals::route('/'),
            'create' => Pages\CreateOvertimeApproval::route('/create'),
            'view' => Pages\ViewOvertimeApproval::route('/{record}'),
            // Edit functionality handled via view page header actions
        ];
    }

    public static function canCreate(): bool
    {
        return true; // Enable create functionality
    }

    public static function canEdit($record): bool
    {
        return in_array($record->status, ['Assigned', 'Accepted']);
    }

    public static function canDelete($record): bool
    {
        return in_array($record->status, ['Assigned', 'Accepted']);
    }
}
