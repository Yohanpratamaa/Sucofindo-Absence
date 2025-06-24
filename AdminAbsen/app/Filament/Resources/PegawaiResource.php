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
                        Forms\Components\Tabs\Tab::make('Akses')
                            ->icon('heroicon-o-key')
                            ->schema([
                                Forms\Components\Grid::make(1)
                                    ->schema([

                                        Forms\Components\Select::make('role')
                                            ->options([
                                                'admin' => 'Admin',
                                                'hr' => 'HR',
                                                'manager' => 'Manager',
                                                'supervisor' => 'Supervisor',
                                                'staff' => 'Staff',
                                            ])
                                            ->required()
                                            ->default('Select Role'),

                                        Forms\Components\TextInput::make('email')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(50)
                                            ->placeholder('Masukkan email'),

                                        Forms\Components\TextInput::make('password')
                                            ->password()
                                            ->required(fn (string $context): bool => $context === 'create')
                                            ->minLength(8)
                                            ->placeholder('Masukkan password'),

                                        Forms\Components\DateTimePicker::make('last_login')
                                            ->label('Login Terakhir')
                                            ->disabled(),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Informasi Umum')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('nip')
                                            ->label('NIP')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(20)
                                            ->placeholder('Masukkan NIP'),

                                        Forms\Components\TextInput::make('nama')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Masukkan nama lengkap'),

                                        Forms\Components\TextInput::make('email')
                                            ->email()
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255)
                                            ->placeholder('email@example.com'),

                                        Forms\Components\TextInput::make('phone')
                                            ->label('No. Telepon')
                                            ->tel()
                                            ->maxLength(15)
                                            ->placeholder('08123456789'),

                                        Forms\Components\Select::make('gender')
                                            ->label('Jenis Kelamin')
                                            ->options([
                                                'L' => 'Laki-laki',
                                                'P' => 'Perempuan',
                                            ])
                                            ->required(),

                                        Forms\Components\DatePicker::make('tanggal_lahir')
                                            ->label('Tanggal Lahir')
                                            ->required(),

                                        Forms\Components\TextInput::make('tempat_lahir')
                                            ->label('Tempat Lahir')
                                            ->maxLength(100)
                                            ->placeholder('Kota kelahiran'),

                                        Forms\Components\Select::make('agama')
                                            ->options([
                                                'islam' => 'Islam',
                                                'kristen' => 'Kristen',
                                                'katolik' => 'Katolik',
                                                'hindu' => 'Hindu',
                                                'buddha' => 'Buddha',
                                                'konghucu' => 'Konghucu',
                                            ])
                                            ->required(),

                                        Forms\Components\Select::make('status_perkawinan')
                                            ->label('Status Perkawinan')
                                            ->options([
                                                'belum_kawin' => 'Belum Kawin',
                                                'kawin' => 'Kawin',
                                                'cerai_hidup' => 'Cerai Hidup',
                                                'cerai_mati' => 'Cerai Mati',
                                            ])
                                            ->required(),

                                        Forms\Components\TextInput::make('kewarganegaraan')
                                            ->default('Indonesia')
                                            ->required()
                                            ->maxLength(50),
                                    ]),

                                Forms\Components\Textarea::make('alamat')
                                    ->rows(3)
                                    ->columnSpanFull()
                                    ->placeholder('Alamat lengkap'),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('jabatan')
                                            ->options([
                                                'manager' => 'Manager',
                                                'supervisor' => 'Supervisor',
                                                'staff' => 'Staff',
                                                'intern' => 'Intern',
                                            ])
                                            ->required(),

                                        Forms\Components\Select::make('divisi')
                                            ->options([
                                                'it' => 'IT',
                                                'hr' => 'Human Resources',
                                                'finance' => 'Finance',
                                                'operations' => 'Operations',
                                                'marketing' => 'Marketing',
                                            ])
                                            ->required(),

                                        Forms\Components\DatePicker::make('tanggal_masuk')
                                            ->label('Tanggal Masuk')
                                            ->required(),

                                        Forms\Components\Select::make('status_karyawan')
                                            ->label('Status Karyawan')
                                            ->options([
                                                'tetap' => 'Karyawan Tetap',
                                                'kontrak' => 'Karyawan Kontrak',
                                                'magang' => 'Magang',
                                                'freelance' => 'Freelance',
                                            ])
                                            ->required(),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Pendidikan')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('pendidikan_terakhir')
                                            ->label('Pendidikan Terakhir')
                                            ->options([
                                                'SD' => 'SD/Sederajat',
                                                'SMP' => 'SMP/Sederajat',
                                                'SMA' => 'SMA/Sederajat',
                                                'D1' => 'Diploma 1',
                                                'D2' => 'Diploma 2',
                                                'D3' => 'Diploma 3',
                                                'S1' => 'Sarjana (S1)',
                                                'S2' => 'Magister (S2)',
                                                'S3' => 'Doktor (S3)',
                                            ])
                                            ->required(),

                                        Forms\Components\TextInput::make('nama_sekolah')
                                            ->label('Nama Sekolah/Universitas')
                                            ->maxLength(255)
                                            ->placeholder('Nama institusi pendidikan'),

                                        Forms\Components\TextInput::make('jurusan')
                                            ->maxLength(255)
                                            ->placeholder('Jurusan/Program Studi'),

                                        Forms\Components\TextInput::make('tahun_lulus')
                                            ->label('Tahun Lulus')
                                            ->numeric()
                                            ->minValue(1980)
                                            ->maxValue(date('Y'))
                                            ->placeholder('YYYY'),

                                        Forms\Components\TextInput::make('ipk_nilai')
                                            ->label('IPK/Nilai')
                                            ->numeric()
                                            ->step(0.01)
                                            ->minValue(0)
                                            ->maxValue(4)
                                            ->placeholder('3.50'),

                                        Forms\Components\Select::make('akreditasi')
                                            ->options([
                                                'A' => 'A (Unggul)',
                                                'B' => 'B (Baik Sekali)',
                                                'C' => 'C (Baik)',
                                                'tidak_terakreditasi' => 'Tidak Terakreditasi',
                                            ])
                                            ->placeholder('Pilih akreditasi'),
                                    ]),

                                Forms\Components\Textarea::make('sertifikat_keahlian')
                                    ->label('Sertifikat/Keahlian Khusus')
                                    ->rows(3)
                                    ->columnSpanFull()
                                    ->placeholder('Daftar sertifikat atau keahlian khusus yang dimiliki'),
                            ]),

                        Forms\Components\Tabs\Tab::make('Nomer Emergency')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('emergency_contact_name')
                                            ->label('Nama Kontak Darurat')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Nama lengkap kontak darurat'),

                                        Forms\Components\Select::make('emergency_contact_relation')
                                            ->label('Hubungan')
                                            ->options([
                                                'orangtua' => 'Orang Tua',
                                                'suami' => 'Suami',
                                                'istri' => 'Istri',
                                                'anak' => 'Anak',
                                                'saudara' => 'Saudara',
                                                'kerabat' => 'Kerabat',
                                                'teman' => 'Teman',
                                            ])
                                            ->required(),

                                        Forms\Components\TextInput::make('emergency_contact_phone')
                                            ->label('No. Telepon Darurat')
                                            ->tel()
                                            ->required()
                                            ->maxLength(15)
                                            ->placeholder('08123456789'),

                                        Forms\Components\TextInput::make('emergency_contact_phone_2')
                                            ->label('No. Telepon Darurat 2')
                                            ->tel()
                                            ->maxLength(15)
                                            ->placeholder('08123456789'),
                                    ]),

                                Forms\Components\Textarea::make('emergency_contact_address')
                                    ->label('Alamat Kontak Darurat')
                                    ->rows(3)
                                    ->columnSpanFull()
                                    ->placeholder('Alamat lengkap kontak darurat'),

                                Forms\Components\Section::make('Kontak Darurat Kedua')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('emergency_contact_name_2')
                                                    ->label('Nama Kontak Darurat 2')
                                                    ->maxLength(255)
                                                    ->placeholder('Nama lengkap kontak darurat kedua'),

                                                Forms\Components\Select::make('emergency_contact_relation_2')
                                                    ->label('Hubungan')
                                                    ->options([
                                                        'orangtua' => 'Orang Tua',
                                                        'suami' => 'Suami',
                                                        'istri' => 'Istri',
                                                        'anak' => 'Anak',
                                                        'saudara' => 'Saudara',
                                                        'kerabat' => 'Kerabat',
                                                        'teman' => 'Teman',
                                                    ]),

                                                Forms\Components\TextInput::make('emergency_contact_phone_alt')
                                                    ->label('No. Telepon Darurat 2')
                                                    ->tel()
                                                    ->maxLength(15)
                                                    ->placeholder('08123456789'),
                                            ]),
                                    ])
                                    ->collapsible(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Jaminan')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('no_bpjs_kesehatan')
                                            ->label('No. BPJS Kesehatan')
                                            ->maxLength(20)
                                            ->placeholder('0001XXXXXXXX'),

                                        Forms\Components\TextInput::make('no_bpjs_ketenagakerjaan')
                                            ->label('No. BPJS Ketenagakerjaan')
                                            ->maxLength(20)
                                            ->placeholder('XXXXXXXXXX'),

                                        Forms\Components\TextInput::make('no_ktp')
                                            ->label('No. KTP')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(16)
                                            ->placeholder('3201XXXXXXXXXXXX'),

                                        Forms\Components\TextInput::make('no_npwp')
                                            ->label('No. NPWP')
                                            ->maxLength(20)
                                            ->placeholder('XX.XXX.XXX.X-XXX.XXX'),

                                        Forms\Components\TextInput::make('no_rekening')
                                            ->label('No. Rekening Bank')
                                            ->maxLength(20)
                                            ->placeholder('Nomor rekening'),

                                        Forms\Components\TextInput::make('nama_bank')
                                            ->label('Nama Bank')
                                            ->maxLength(100)
                                            ->placeholder('Bank Mandiri, BCA, dll'),

                                        Forms\Components\TextInput::make('nama_pemilik_rekening')
                                            ->label('Nama Pemilik Rekening')
                                            ->maxLength(255)
                                            ->placeholder('Sesuai nama di buku tabungan'),

                                        Forms\Components\Select::make('jenis_rekening')
                                            ->label('Jenis Rekening')
                                            ->options([
                                                'tabungan' => 'Tabungan',
                                                'giro' => 'Giro',
                                                'deposito' => 'Deposito',
                                            ])
                                            ->default('tabungan'),
                                    ]),

                                Forms\Components\Section::make('Asuransi Tambahan')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('asuransi_nama')
                                                    ->label('Nama Asuransi')
                                                    ->maxLength(255)
                                                    ->placeholder('Nama perusahaan asuransi'),

                                                Forms\Components\TextInput::make('asuransi_no_polis')
                                                    ->label('No. Polis')
                                                    ->maxLength(50)
                                                    ->placeholder('Nomor polis asuransi'),

                                                Forms\Components\DatePicker::make('asuransi_mulai')
                                                    ->label('Berlaku Mulai'),

                                                Forms\Components\DatePicker::make('asuransi_berakhir')
                                                    ->label('Berlaku Hingga'),
                                            ]),
                                    ])
                                    ->collapsible(),
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
                Tables\Columns\TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jabatan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'manager' => 'success',
                        'supervisor' => 'warning',
                        'staff' => 'primary',
                        'intern' => 'gray',
                    }),

                Tables\Columns\TextColumn::make('divisi')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jabatan')
                    ->options([
                        'manager' => 'Manager',
                        'supervisor' => 'Supervisor',
                        'staff' => 'Staff',
                        'intern' => 'Intern',
                    ]),

                Tables\Filters\SelectFilter::make('divisi')
                    ->options([
                        'it' => 'IT',
                        'hr' => 'Human Resources',
                        'finance' => 'Finance',
                        'operations' => 'Operations',
                        'marketing' => 'Marketing',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
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
