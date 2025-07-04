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
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('status_pegawai')
                    ->label('Status Pegawai')
                    ->colors([
                        'primary' => 'PTT',
                        'success' => 'LS',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'PTT' => 'PTT',
                        'LS' => 'LS',
                        default => $state,
                    }),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'non-active',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'active',
                        'non-active' => 'non-active',
                        'inactive' => 'non-active',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('role_user')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super admin' => 'danger',
                        'Kepala Bidang' => 'warning',
                        'employee' => 'primary',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('jabatan')
                    ->label('Jabatan')
                    ->placeholder('Belum diset')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_pegawai')
                    ->label('Status Pegawai')
                    ->options([
                        'PTT' => 'PTT',
                        'LS' => 'LS',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'non-active' => 'Non-Active',
                    ]),

                Tables\Filters\SelectFilter::make('role_user')
                    ->label('Role')
                    ->options([
                        'super admin' => 'Super Admin',
                        'employee' => 'Employee',
                        'Kepala Bidang' => 'Kepala Bidang',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPegawais::route('/'),
            'view' => Pages\ViewPegawai::route('/{record}'),
            'edit' => Pages\EditPegawai::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
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
