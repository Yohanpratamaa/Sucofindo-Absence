<?php

namespace App\Filament\KepalaBidang\Resources;

use App\Filament\KepalaBidang\Resources\PegawaiResource\Pages;
use App\Models\Pegawai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;

class PegawaiResource extends Resource
{
    protected static ?string $model = Pegawai::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Manajemen Pegawai';

    protected static ?string $modelLabel = 'Pegawai';

    protected static ?string $pluralModelLabel = 'Pegawai';

    protected static ?string $navigationGroup = 'Manajemen Data';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        // Kepala Bidang hanya bisa mengelola pegawai dengan role 'employee'
        return parent::getEloquentQuery()->where('role_user', 'employee');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Personal')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('npp')
                            ->label('NPP (Nomor Pokok Pegawai)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('no_hp')
                            ->label('Nomor HP')
                            ->tel()
                            ->maxLength(15),

                        Forms\Components\DatePicker::make('tanggal_lahir')
                            ->label('Tanggal Lahir'),

                        Forms\Components\Select::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ]),

                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat')
                            ->rows(3)
                            ->maxLength(500),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Data Kepegawaian')
                    ->schema([
                        Forms\Components\TextInput::make('jabatan')
                            ->label('Jabatan')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('divisi')
                            ->label('Divisi/Departemen')
                            ->maxLength(255),

                        Forms\Components\DatePicker::make('tanggal_masuk')
                            ->label('Tanggal Masuk')
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label('Status Pegawai')
                            ->options([
                                'active' => 'Aktif',
                                'inactive' => 'Non-Aktif',
                                'resigned' => 'Mengundurkan Diri',
                            ])
                            ->required()
                            ->default('active'),

                        Forms\Components\Hidden::make('role_user')
                            ->default('employee'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Akun Login')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->helperText('Kosongkan jika tidak ingin mengubah password'),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Konfirmasi Password')
                            ->password()
                            ->same('password')
                            ->dehydrated(false)
                            ->required(fn (string $context): bool => $context === 'create'),
                    ])
                    ->columns(2)
                    ->visible(fn (string $context): bool => $context === 'create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('npp')
                    ->label('NPP')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('no_hp')
                    ->label('No. HP')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('jabatan')
                    ->label('Jabatan')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('divisi')
                    ->label('Divisi')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('tanggal_masuk')
                    ->label('Tanggal Masuk')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'inactive',
                        'danger' => 'resigned',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Non-Aktif',
                        'resigned' => 'Mengundurkan Diri',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Non-Aktif',
                        'resigned' => 'Mengundurkan Diri',
                    ]),

                Tables\Filters\Filter::make('tanggal_masuk')
                    ->form([
                        Forms\Components\DatePicker::make('tanggal_dari')
                            ->label('Tanggal Masuk Dari'),
                        Forms\Components\DatePicker::make('tanggal_sampai')
                            ->label('Tanggal Masuk Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['tanggal_dari'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_masuk', '>=', $date),
                            )
                            ->when(
                                $data['tanggal_sampai'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_masuk', '<=', $date),
                            );
                    }),

                Tables\Filters\Filter::make('jenis_kelamin')
                    ->form([
                        Forms\Components\Select::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['jenis_kelamin'],
                            fn (Builder $query, $gender): Builder => $query->where('jenis_kelamin', $gender),
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat'),

                Tables\Actions\EditAction::make()
                    ->label('Edit'),

                Tables\Actions\Action::make('reset_password')
                    ->label('Reset Password')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Reset Password Pegawai')
                    ->modalDescription(fn ($record) => "Apakah Anda yakin ingin mereset password untuk {$record->nama}? Password baru akan sama dengan NPP.")
                    ->action(function ($record) {
                        $record->update([
                            'password' => Hash::make($record->npp)
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Password Berhasil Direset')
                            ->body("Password {$record->nama} telah direset menjadi NPP: {$record->npp}")
                            ->send();
                    }),

                Tables\Actions\Action::make('toggle_status')
                    ->label(fn ($record) => $record->status === 'active' ? 'Non-Aktifkan' : 'Aktifkan')
                    ->icon(fn ($record) => $record->status === 'active' ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn ($record) => $record->status === 'active' ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->modalHeading(fn ($record) => $record->status === 'active' ? 'Non-Aktifkan Pegawai' : 'Aktifkan Pegawai')
                    ->modalDescription(fn ($record) => "Apakah Anda yakin ingin " . ($record->status === 'active' ? 'menonaktifkan' : 'mengaktifkan') . " {$record->nama}?")
                    ->action(function ($record) {
                        $newStatus = $record->status === 'active' ? 'inactive' : 'active';
                        $record->update(['status' => $newStatus]);

                        Notification::make()
                            ->success()
                            ->title('Status Berhasil Diubah')
                            ->body("{$record->nama} telah " . ($newStatus === 'active' ? 'diaktifkan' : 'dinonaktifkan'))
                            ->send();
                    })
                    ->visible(fn ($record) => in_array($record->status, ['active', 'inactive'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('bulk_activate')
                    ->label('Aktifkan yang Dipilih')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Aktifkan Pegawai Terpilih')
                    ->modalDescription('Apakah Anda yakin ingin mengaktifkan semua pegawai yang dipilih?')
                    ->action(function ($records) {
                        $count = 0;
                        foreach ($records as $record) {
                            if ($record->status !== 'active') {
                                $record->update(['status' => 'active']);
                                $count++;
                            }
                        }

                        Notification::make()
                            ->success()
                            ->title('Pegawai Berhasil Diaktifkan')
                            ->body("{$count} pegawai telah diaktifkan.")
                            ->send();
                    }),

                Tables\Actions\BulkAction::make('bulk_deactivate')
                    ->label('Non-Aktifkan yang Dipilih')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Non-Aktifkan Pegawai Terpilih')
                    ->modalDescription('Apakah Anda yakin ingin menonaktifkan semua pegawai yang dipilih?')
                    ->action(function ($records) {
                        $count = 0;
                        foreach ($records as $record) {
                            if ($record->status === 'active') {
                                $record->update(['status' => 'inactive']);
                                $count++;
                            }
                        }

                        Notification::make()
                            ->success()
                            ->title('Pegawai Berhasil Dinonaktifkan')
                            ->body("{$count} pegawai telah dinonaktifkan.")
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPegawais::route('/'),
            'create' => Pages\CreatePegawai::route('/create'),
            'view' => Pages\ViewPegawai::route('/{record}'),
            'edit' => Pages\EditPegawai::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('role_user', 'employee')->where('status', 'active')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
