<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ManajemenIzinResource\Pages;
use App\Models\ManajemenIzin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\Card;

class ManajemenIzinResource extends Resource
{
    protected static ?string $model = ManajemenIzin::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Manajemen Izin';

    protected static ?string $modelLabel = 'Jenis Izin';

    protected static ?string $pluralModelLabel = 'Manajemen Jenis Izin';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Dasar Jenis Izin')
                    ->description('Pengaturan dasar untuk jenis izin')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('nama_izin')
                                    ->label('Nama Jenis Izin')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('contoh: Izin Sakit')
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('kode_izin')
                                    ->label('Kode Izin')
                                    ->required()
                                    ->unique(ManajemenIzin::class, 'kode_izin', ignoreRecord: true)
                                    ->maxLength(50)
                                    ->placeholder('contoh: sakit')
                                    ->helperText('Kode unik tanpa spasi (lowercase)')
                                    ->columnSpan(1),

                                Forms\Components\Textarea::make('deskripsi')
                                    ->label('Deskripsi')
                                    ->rows(3)
                                    ->placeholder('Deskripsi detail tentang jenis izin ini...')
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Section::make('Pengaturan Aturan')
                    ->description('Aturan dan batasan untuk jenis izin')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('max_hari')
                                    ->label('Maksimal Hari')
                                    ->numeric()
                                    ->minValue(1)
                                    ->placeholder('contoh: 3')
                                    ->helperText('Kosongkan jika tidak ada batasan')
                                    ->columnSpan(1),

                                Forms\Components\Select::make('kategori')
                                    ->label('Kategori')
                                    ->options([
                                        'cuti' => 'Cuti',
                                        'izin_khusus' => 'Izin Khusus',
                                        'sakit' => 'Sakit',
                                        'dinas' => 'Dinas',
                                    ])
                                    ->required()
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('urutan_tampil')
                                    ->label('Urutan Tampil')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->required()
                                    ->columnSpan(1),
                            ]),

                        Grid::make(3)
                            ->schema([
                                Forms\Components\Toggle::make('perlu_dokumen')
                                    ->label('Memerlukan Dokumen Pendukung')
                                    ->helperText('Wajib upload dokumen saat pengajuan')
                                    ->columnSpan(1),

                                Forms\Components\Toggle::make('auto_approve')
                                    ->label('Otomatis Disetujui')
                                    ->helperText('Langsung disetujui tanpa perlu approval')
                                    ->columnSpan(1),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Status Aktif')
                                    ->default(true)
                                    ->helperText('Tampilkan di pilihan izin pegawai')
                                    ->columnSpan(1),
                            ]),
                    ]),

                Section::make('Pengaturan Tampilan')
                    ->description('Pengaturan tampilan dan visual')
                    ->schema([
                        Forms\Components\Select::make('warna_badge')
                            ->label('Warna Badge')
                            ->options([
                                'primary' => 'Primary (Biru)',
                                'success' => 'Success (Hijau)',
                                'warning' => 'Warning (Kuning)',
                                'danger' => 'Danger (Merah)',
                                'info' => 'Info (Cyan)',
                                'secondary' => 'Secondary (Abu-abu)',
                            ])
                            ->default('primary')
                            ->required(),
                    ]),

                Section::make('Syarat Pengajuan')
                    ->description('Daftar syarat dan ketentuan untuk pengajuan izin ini')
                    ->schema([
                        Forms\Components\Repeater::make('syarat_pengajuan')
                            ->label('Daftar Syarat')
                            ->schema([
                                Forms\Components\TextInput::make('syarat')
                                    ->label('Syarat')
                                    ->required()
                                    ->placeholder('contoh: Surat keterangan dokter wajib untuk izin lebih dari 1 hari')
                                    ->columnSpanFull(),
                            ])
                            ->defaultItems(1)
                            ->addActionLabel('Tambah Syarat')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('urutan_tampil')
                    ->label('Urutan')
                    ->sortable()
                    ->width(80),

                TextColumn::make('nama_izin')
                    ->label('Nama Jenis Izin')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('kode_izin')
                    ->label('Kode')
                    ->searchable()
                    ->badge()
                    ->color('gray')
                    ->size('sm'),

                BadgeColumn::make('kategori')
                    ->label('Kategori')
                    ->colors([
                        'primary' => 'cuti',
                        'warning' => 'izin_khusus',
                        'danger' => 'sakit',
                        'info' => 'dinas',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cuti' => 'Cuti',
                        'izin_khusus' => 'Izin Khusus',
                        'sakit' => 'Sakit',
                        'dinas' => 'Dinas',
                        default => $state,
                    }),

                TextColumn::make('max_hari')
                    ->label('Max Hari')
                    ->formatStateUsing(fn (?int $state): string => $state ? $state . ' hari' : 'Tanpa batas')
                    ->color(fn (?int $state): string => $state ? 'warning' : 'success')
                    ->weight('medium'),

                BooleanColumn::make('perlu_dokumen')
                    ->label('Butuh Dokumen')
                    ->trueColor('warning')
                    ->falseColor('success'),

                BooleanColumn::make('auto_approve')
                    ->label('Auto Approve')
                    ->trueColor('success')
                    ->falseColor('warning'),

                BooleanColumn::make('is_active')
                    ->label('Status')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('createdBy.nama')
                    ->label('Dibuat Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kategori')
                    ->label('Kategori')
                    ->options([
                        'cuti' => 'Cuti',
                        'izin_khusus' => 'Izin Khusus',
                        'sakit' => 'Sakit',
                        'dinas' => 'Dinas',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif'),

                Tables\Filters\TernaryFilter::make('perlu_dokumen')
                    ->label('Memerlukan Dokumen')
                    ->placeholder('Semua')
                    ->trueLabel('Ya')
                    ->falseLabel('Tidak'),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('urutan_tampil', 'asc')
            ->poll('60s');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
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
            'index' => Pages\ListManajemenIzins::route('/'),
            'create' => Pages\CreateManajemenIzin::route('/create'),
            'view' => Pages\ViewManajemenIzin::route('/{record}'),
            'edit' => Pages\EditManajemenIzin::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::active()->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }
}
