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

    protected static ?string $modelLabel = 'Pegawai';

    protected static ?string $pluralModelLabel = 'Data Pegawai';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Tabs')
                    ->id('pegawai-tabs') // Add ID for JavaScript targeting
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Users')
                            ->id('users-tab') // Add ID for each tab
                            ->icon('heroicon-o-user')
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

                                        Forms\Components\Select::make('status_pegawai')
                                            ->label('Status Pegawai')
                                            ->options([
                                                'PTT' => 'PTT',
                                                'LS' => 'LS',
                                            ])
                                            ->required(),

                                        Forms\Components\TextInput::make('nomor_handphone')
                                            ->numeric()
                                            ->label('Nomor Handphone')
                                            ->required()
                                            ->maxLength(15)
                                            ->placeholder('Masukkan nomor handphone'),

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

                                        Forms\Components\Textarea::make('alamat')
                                            ->rows(3)
                                            ->columnSpanFull()
                                            ->placeholder('Alamat lengkap'),
                                    ]),
                            ]),

                        // Tab Jabatan - Form Manual
                        Forms\Components\Tabs\Tab::make('Jabatan')
                            ->id('jabatan-tab')
                            ->icon('heroicon-o-briefcase')
                            ->schema([
                                Forms\Components\Section::make('Data Jabatan')
                                    ->description('Isi data jabatan pegawai')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('jabatan_nama')
                                                    ->label('Nama Jabatan')
                                                    ->placeholder('Masukkan nama jabatan')
                                                    ->required(),

                                                Forms\Components\TextInput::make('jabatan_tunjangan')
                                                    ->label('Tunjangan Jabatan')
                                                    ->numeric()
                                                    ->prefix('Rp')
                                                    ->placeholder('0')
                                                    ->default(0),
                                            ]),
                                    ]),
                            ]),

                        // Tab Posisi - Form Manual
                        Forms\Components\Tabs\Tab::make('Posisi')
                            ->id('posisi-tab')
                            ->icon('heroicon-o-users')
                            ->schema([
                                Forms\Components\Section::make('Data Posisi')
                                    ->description('Isi data posisi pegawai')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('posisi_nama')
                                                    ->label('Nama Posisi')
                                                    ->placeholder('Masukkan nama posisi')
                                                    ->required(),

                                                Forms\Components\TextInput::make('posisi_tunjangan')
                                                    ->label('Tunjangan Posisi')
                                                    ->numeric()
                                                    ->prefix('Rp')
                                                    ->placeholder('0')
                                                    ->default(0),
                                            ]),
                                    ]),
                            ]),

                        // Tab Pendidikan - Sesuai dengan gambar
                        Forms\Components\Tabs\Tab::make('Pendidikan')
                            ->id('pendidikan-tab')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                Forms\Components\Repeater::make('pendidikan_list')
                                    ->label('Data Pendidikan')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Select::make('jenjang')
                                                    ->label('Jenjang')
                                                    ->options([
                                                        'SD' => 'SD',
                                                        'SMP' => 'SMP',
                                                        'SMA' => 'SMA',
                                                        'SMK' => 'SMK',
                                                        'D3' => 'D3',
                                                        'S1' => 'S1',
                                                        'S2' => 'S2',
                                                        'S3' => 'S3',
                                                    ])
                                                    ->required()
                                                    ->placeholder('Pilih jenjang'),

                                                Forms\Components\TextInput::make('sekolah_univ')
                                                    ->label('Sekolah / Univ')
                                                    ->required()
                                                    ->placeholder('Nama sekolah/universitas'),

                                                Forms\Components\TextInput::make('fakultas_program_studi')
                                                    ->label('Fakultas')
                                                    ->placeholder('Nama fakultas '),

                                                Forms\Components\TextInput::make('jurusan')
                                                    ->label('Jurusan')
                                                    ->placeholder('Nama jurusan'),

                                                Forms\Components\DatePicker::make('thn_masuk')
                                                    ->label('Thn Masuk')
                                                    ->displayFormat('Y')
                                                    ->format('Y-m-d'),

                                                Forms\Components\DatePicker::make('thn_lulus')
                                                    ->label('Thn Lulus')
                                                    ->displayFormat('Y')
                                                    ->format('Y-m-d'),

                                                Forms\Components\TextInput::make('ipk_nilai')
                                                    ->label('IPK / Nilai')
                                                    ->placeholder('Contoh: 3.50 atau 85'),

                                                Forms\Components\FileUpload::make('ijazah')
                                                    ->label('Ijazah')
                                                    ->acceptedFileTypes(['application/pdf'])
                                                    ->maxSize(5120) // 5MB
                                                    ->helperText('File dengan format PDF')
                                                    ->directory('ijazah')
                                                    ->visibility('private'),
                                            ]),
                                    ])
                                    ->addActionLabel('Tambah Pendidikan')
                                    ->defaultItems(1)
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string =>
                                        isset($state['jenjang']) && isset($state['sekolah_univ'])
                                            ? "{$state['jenjang']} - {$state['sekolah_univ']}"
                                            : 'Pendidikan Baru'
                                    ),
                            ]),

                        // Tab Nomor Emergency - Sesuai dengan gambar
                        Forms\Components\Tabs\Tab::make('Nomor Emergency')
                            ->id('emergency-tab')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Forms\Components\Repeater::make('emergency_contacts')
                                    ->label('Kontak Darurat')
                                    ->schema([
                                        Forms\Components\Grid::make(1)
                                            ->schema([
                                                Forms\Components\Select::make('relationship')
                                                    ->label('Hubungan')
                                                    ->options([
                                                        'Ayah' => 'Ayah',
                                                        'Ibu' => 'Ibu',
                                                        'Suami' => 'Suami',
                                                        'Istri' => 'Istri',
                                                        'Anak' => 'Anak',
                                                        'Saudara Kandung' => 'Saudara Kandung',
                                                        'Saudara' => 'Saudara',
                                                        'Teman' => 'Teman',
                                                        'Lainnya' => 'Lainnya',
                                                    ])
                                                    ->required()
                                                    ->placeholder('Pilih hubungan'),

                                                Forms\Components\TextInput::make('nama_kontak')
                                                    ->label('Nama')
                                                    ->required()
                                                    ->placeholder('Nama lengkap kontak darurat'),

                                                Forms\Components\TextInput::make('no_emergency')
                                                    ->label('No Emergency')
                                                    ->tel()
                                                    ->required()
                                                    ->placeholder('Contoh: 081234567890')
                                                    ->maxLength(15),
                                            ]),
                                    ])
                                    ->addActionLabel('Tambah Kontak Darurat')
                                    ->defaultItems(1)
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string =>
                                        isset($state['nama_kontak']) && isset($state['relationship'])
                                            ? "{$state['nama_kontak']} ({$state['relationship']})"
                                            : 'Kontak Darurat Baru'
                                    ),
                            ]),

                        // Tab Fasilitas - Dengan Repeater seperti Pendidikan
                        Forms\Components\Tabs\Tab::make('Fasilitas')
                            ->id('fasilitas-tab')
                            ->icon('heroicon-o-gift')
                            ->schema([
                                Forms\Components\Repeater::make('fasilitas_list')
                                    ->label('Data Fasilitas')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('nama_jaminan')
                                                    ->label('Nama Jaminan')
                                                    ->placeholder('Contoh: BPJS Kesehatan, BPJS Ketenagakerjaan, Asuransi Jiwa'),

                                                Forms\Components\TextInput::make('no_jaminan')
                                                    ->label('No Jaminan')
                                                    ->placeholder('Nomor kartu jaminan')
                                                    ->maxLength(50),

                                                Forms\Components\Select::make('jenis_fasilitas')
                                                    ->label('Jenis Fasilitas')
                                                    ->options([
                                                        'BPJS Kesehatan' => 'BPJS Kesehatan',
                                                        'BPJS Ketenagakerjaan' => 'BPJS Ketenagakerjaan',
                                                        'Asuransi Jiwa' => 'Asuransi Jiwa',
                                                        'Asuransi Kesehatan' => 'Asuransi Kesehatan',
                                                        'Tunjangan Transport' => 'Tunjangan Transport',
                                                        'Tunjangan Makan' => 'Tunjangan Makan',
                                                        'Tunjangan Komunikasi' => 'Tunjangan Komunikasi',
                                                        'Lainnya' => 'Lainnya',
                                                    ])
                                                    ->searchable()
                                                    ->placeholder('Pilih jenis fasilitas'),

                                                Forms\Components\TextInput::make('provider')
                                                    ->label('Provider/Penyedia')
                                                    ->placeholder('Contoh: BPJS, Prudential, Allianz, dll'),

                                                Forms\Components\TextInput::make('nilai_fasilitas')
                                                    ->label('Nominal')
                                                    ->numeric()
                                                    ->prefix('Rp')
                                                    ->placeholder('0')
                                                    ->helperText('Nilai dalam rupiah per bulan'),

                                            ]),
                                    ])
                                    ->addActionLabel('Tambah Fasilitas')
                                    ->defaultItems(1)
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string =>
                                        isset($state['nama_jaminan']) && isset($state['jenis_fasilitas'])
                                            ? "{$state['jenis_fasilitas']} - {$state['nama_jaminan']}"
                                            : 'Fasilitas Baru'
                                    )
                                    ->reorderable()
                                    ->cloneable(),
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

                Tables\Columns\TextColumn::make('nomor_handphone')
                    ->label('No HP')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

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

                Tables\Columns\TextColumn::make('jabatan_nama')
                    ->label('Jabatan')
                    ->placeholder('Belum diset'),

                Tables\Columns\TextColumn::make('posisi_nama')
                    ->label('Posisi')
                    ->placeholder('Belum diset'),

                // Kolom untuk fasilitas dari JSON
                Tables\Columns\TextColumn::make('total_nilai_fasilitas')
                    ->label('Total Fasilitas')
                    ->money('IDR')
                    ->getStateUsing(function ($record) {
                        return $record->total_nilai_fasilitas ?? 0;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('jumlah_fasilitas')
                    ->label('Jml Fasilitas')
                    ->getStateUsing(function ($record) {
                        return $record->jumlah_fasilitas;
                    })
                    ->badge()
                    ->color('success')
                    ->toggleable(isToggledHiddenByDefault: true),

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
            'view' => Pages\ViewPegawai::route('/{record}'),
            'edit' => Pages\EditPegawai::route('/{record}/edit'),
        ];
    }
}
