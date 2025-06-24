<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PegawaiResource\Pages;
use App\Filament\Resources\PegawaiResource\RelationManagers;
use App\Models\Pegawai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PegawaiResource extends Resource
{
    protected static ?string $model = Pegawai::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Data Pegawai';

    // protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $modelLabel = 'Pegawai';
    protected static ?string $pluralModelLabel = 'Data Pegawai';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Users')
                            ->icon('heroicon-o-user-group')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('nama')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Masukkan nama lengkap'),

                                        Forms\Components\TextInput::make('npp')
                                            ->label('NPP')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255)
                                            ->placeholder('Masukkan NPP'),

                                        Forms\Components\TextInput::make('email')
                                            ->email()
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255)
                                            ->placeholder('email@example.com'),

                                        Forms\Components\TextInput::make('password')
                                            ->password()
                                            ->required(fn (string $context): bool => $context === 'create')
                                            ->minLength(8)
                                            ->placeholder('Masukkan password'),

                                        Forms\Components\TextInput::make('nik')
                                            ->label('NIK')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255)
                                            ->placeholder('Masukkan NIK'),

                                        Forms\Components\Textarea::make('alamat')
                                            ->rows(3)
                                            ->placeholder('Alamat lengkap'),

                                        Forms\Components\Select::make('status_pegawai')
                                            ->label('Status Pegawai')
                                            ->options([
                                                'PTT' => 'PTT',
                                                'LS' => 'LS',
                                            ])
                                            ->required(),

                                        Forms\Components\Select::make('status')
                                            ->options([
                                                'active' => 'Active',
                                                'resign' => 'Resign',
                                            ])
                                            ->default('active')
                                            ->required(),

                                        Forms\Components\Select::make('role_user')
                                            ->label('Role User')
                                            ->options([
                                                'super admin' => 'Super Admin',
                                                'employee' => 'Employee',
                                                'Kepala Bidang' => 'Kepala Bidang',
                                            ])
                                            ->required(),
                                    ]),

                            ]),

                        // Tab Jaminan
                        Forms\Components\Tabs\Tab::make('Jaminan')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Forms\Components\Select::make('id_jaminan')
                                    ->label('Pilih Jaminan')
                                    ->relationship('jaminan', 'nama_jaminan')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('nama_jaminan')
                                            ->required(),
                                        Forms\Components\TextInput::make('nomor_jaminan'),
                                        Forms\Components\Select::make('jenis_jaminan')
                                            ->options([
                                                'BPJS Kesehatan' => 'BPJS Kesehatan',
                                                'BPJS Ketenagakerjaan' => 'BPJS Ketenagakerjaan',
                                                'Asuransi Swasta' => 'Asuransi Swasta',
                                            ]),
                                    ]),

                            ]),

                        // Tab Jabatan
                        Forms\Components\Tabs\Tab::make('Jabatan')
                            ->icon('heroicon-o-briefcase')
                            ->schema([
                                Forms\Components\Select::make('id_jabatan')
                                    ->label('Pilih Jabatan')
                                    ->relationship('jabatan', 'nama_jabatan')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('nama_jabatan')
                                            ->required(),
                                        Forms\Components\Textarea::make('deskripsi_jabatan'),
                                    ]),

                            ]),

                        // Tab Posisi
                        Forms\Components\Tabs\Tab::make('Posisi')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Forms\Components\Select::make('id_posisi')
                                    ->label('Pilih Posisi')
                                    ->relationship('posisi', 'nama_posisi')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('nama_posisi')
                                            ->required(),
                                        Forms\Components\TextInput::make('divisi'),
                                        Forms\Components\TextInput::make('departemen'),
                                    ]),

                            ]),

                        // Tab Pendidikan
                        Forms\Components\Tabs\Tab::make('Pendidikan')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                Forms\Components\Select::make('id_pendidikan')
                                    ->label('Pilih Pendidikan')
                                    ->relationship('pendidikan', 'jenjang_pendidikan')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\Select::make('jenjang_pendidikan')
                                            ->options([
                                                'SD' => 'SD',
                                                'SMP' => 'SMP',
                                                'SMA' => 'SMA',
                                                'D3' => 'D3',
                                                'S1' => 'S1',
                                                'S2' => 'S2',
                                                'S3' => 'S3',
                                            ])
                                            ->required(),
                                        Forms\Components\TextInput::make('nama_institusi'),
                                        Forms\Components\TextInput::make('jurusan'),
                                        Forms\Components\TextInput::make('tahun_lulus')
                                            ->numeric(),
                                    ]),

                            ]),

                        // Tab Nomor Emergency
                        Forms\Components\Tabs\Tab::make('Nomor Emergency')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Forms\Components\Select::make('id_nomor_emergency')
                                    ->label('Pilih Kontak Darurat')
                                    ->relationship('nomorEmergency', 'nama_kontak')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('nama_kontak')
                                            ->required(),
                                        Forms\Components\TextInput::make('nomor_telepon')
                                            ->tel()
                                            ->required(),
                                        Forms\Components\Select::make('hubungan')
                                            ->options([
                                                'Orang Tua' => 'Orang Tua',
                                                'Suami/Istri' => 'Suami/Istri',
                                                'Saudara' => 'Saudara',
                                                'Teman' => 'Teman',
                                            ])
                                            ->required(),
                                        Forms\Components\Textarea::make('alamat_kontak'),
                                    ]),

                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
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
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
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
                    ]),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'resign',
                    ]),

                Tables\Columns\TextColumn::make('role_user')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super admin' => 'danger',
                        'Kepala Bidang' => 'warning',
                        'employee' => 'primary',
                    }),

                Tables\Columns\TextColumn::make('jabatan.nama_jabatan')
                    ->label('Jabatan')
                    ->placeholder('Belum diset'),

                Tables\Columns\TextColumn::make('posisi.nama_posisi')
                    ->label('Posisi')
                    ->placeholder('Belum diset'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_pegawai')
                    ->options([
                        'PTT' => 'PTT',
                        'LS' => 'LS',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'resign' => 'Resign',
                    ]),

                Tables\Filters\SelectFilter::make('role_user')
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
            ]);
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
            'index' => Pages\ListPegawais::route('/'),
            'create' => Pages\CreatePegawai::route('/create'),
            'edit' => Pages\EditPegawai::route('/{record}/edit'),
        ];
    }
}
