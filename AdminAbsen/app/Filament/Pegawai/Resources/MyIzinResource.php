<?php

namespace App\Filament\Pegawai\Resources;

use App\Filament\Pegawai\Resources\MyIzinResource\Pages;
use App\Models\Izin;
use App\Models\ManajemenIzin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MyIzinResource extends Resource
{
    protected static ?string $model = Izin::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Izin Saya';

    protected static ?string $modelLabel = 'Izin';

    protected static ?string $pluralModelLabel = 'Data Izin';

    public static function getNavigationGroup(): ?string
    {
        return ' Izin';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Pengajuan Izin')
                    ->description('Ajukan izin tidak hadir untuk mendapatkan persetujuan resmi')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('jenis_izin')
                                    ->label('Jenis Izin')
                                    ->options(ManajemenIzin::getSelectOptions())
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $jenisIzin = ManajemenIzin::where('kode_izin', $state)->first();
                                            if ($jenisIzin) {
                                                // Set helper text with requirements
                                                $helperText = $jenisIzin->deskripsi;
                                                if ($jenisIzin->max_hari) {
                                                    $helperText .= " (Maksimal {$jenisIzin->max_hari} hari)";
                                                }
                                                $set('helper_text', $helperText);

                                                // Update dokumen pendukung helper text
                                                if ($jenisIzin->perlu_dokumen) {
                                                    $set('dokumen_helper', 'Dokumen pendukung WAJIB untuk jenis izin ini');
                                                } else {
                                                    $set('dokumen_helper', 'Dokumen pendukung tidak diperlukan untuk jenis izin ini');
                                                }
                                            }
                                        }
                                    })
                                    ->columnSpan(1),

                                Forms\Components\DatePicker::make('tanggal_mulai')
                                    ->label('Tanggal Mulai')
                                    ->required()
                                    ->default(now())
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        // Auto set tanggal_akhir if not set
                                        if ($state && !$get('tanggal_akhir')) {
                                            $set('tanggal_akhir', $state);
                                        }
                                    })
                                    ->columnSpan(1),

                                Forms\Components\DatePicker::make('tanggal_akhir')
                                    ->label('Tanggal Akhir')
                                    ->required()
                                    ->default(now())
                                    ->afterOrEqual('tanggal_mulai')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $get, Forms\Set $set) {
                                        $jenisIzin = $get('jenis_izin');
                                        $tanggalMulai = $get('tanggal_mulai');

                                        if ($jenisIzin && $tanggalMulai && $state) {
                                            $jenisIzinData = ManajemenIzin::where('kode_izin', $jenisIzin)->first();
                                            if ($jenisIzinData && $jenisIzinData->max_hari) {
                                                $durasi = \Carbon\Carbon::parse($tanggalMulai)->diffInDays(\Carbon\Carbon::parse($state)) + 1;
                                                if ($durasi > $jenisIzinData->max_hari) {
                                                    $set('tanggal_akhir', \Carbon\Carbon::parse($tanggalMulai)->addDays($jenisIzinData->max_hari - 1)->format('Y-m-d'));
                                                }
                                            }
                                        }
                                    })
                                    ->columnSpan(1),

                                Forms\Components\Textarea::make('keterangan')
                                    ->label('Keterangan/Alasan')
                                    ->required()
                                    ->rows(4)
                                    ->placeholder('Jelaskan alasan izin Anda...')
                                    ->columnSpanFull(),

                                // Info dokumen pendukung berdasarkan jenis izin
                                Forms\Components\Placeholder::make('dokumen_info')
                                    ->label('Info Dokumen Pendukung')
                                    ->content(function (callable $get) {
                                        $jenisIzin = $get('jenis_izin');
                                        if (!$jenisIzin) {
                                            return '💡 Pilih jenis izin terlebih dahulu untuk melihat apakah dokumen pendukung diperlukan.';
                                        }

                                        $jenisIzinData = ManajemenIzin::where('kode_izin', $jenisIzin)->first();
                                        if ($jenisIzinData) {
                                            if ($jenisIzinData->perlu_dokumen) {
                                                return '📁 ⚠️ DOKUMEN PENDUKUNG WAJIB untuk jenis izin ini. Form upload akan muncul di bawah.';
                                            } else {
                                                return '✅ Dokumen pendukung TIDAK DIPERLUKAN untuk jenis izin ini.';
                                            }
                                        }
                                        return '';
                                    })
                                    ->visible(function (callable $get) {
                                        return $get('jenis_izin') !== null;
                                    })
                                    ->columnSpanFull(),

                                Forms\Components\FileUpload::make('dokumen_pendukung')
                                    ->label('Dokumen Pendukung')
                                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                                    ->maxSize(2048) // 2MB in KB
                                    ->directory('izin-documents')
                                    ->disk('public')
                                    ->visibility('private')
                                    ->downloadable()
                                    ->openable()
                                    ->previewable()
                                    ->helperText(function (callable $get) {
                                        $jenisIzin = $get('jenis_izin');
                                        if ($jenisIzin) {
                                            $jenisIzinData = ManajemenIzin::where('kode_izin', $jenisIzin)->first();
                                            if ($jenisIzinData && $jenisIzinData->perlu_dokumen) {
                                                return '⚠️ DOKUMEN WAJIB: Upload surat dokter, surat keterangan, atau dokumen pendukung lainnya (PDF/JPG/PNG, Max: 2MB)';
                                            }
                                        }
                                        return 'Upload dokumen pendukung jika diperlukan (PDF/JPG/PNG, Max: 2MB)';
                                    })
                                    ->required(function (callable $get) {
                                        $jenisIzin = $get('jenis_izin');
                                        if ($jenisIzin) {
                                            $jenisIzinData = ManajemenIzin::where('kode_izin', $jenisIzin)->first();
                                            return $jenisIzinData ? $jenisIzinData->perlu_dokumen : false;
                                        }
                                        return false;
                                    })
                                    ->visible(function (callable $get) {
                                        $jenisIzin = $get('jenis_izin');
                                        if ($jenisIzin) {
                                            $jenisIzinData = ManajemenIzin::where('kode_izin', $jenisIzin)->first();
                                            return $jenisIzinData ? $jenisIzinData->perlu_dokumen : false;
                                        }
                                        return false;
                                    })
                                    ->columnSpanFull(),

                                // Display syarat pengajuan
                                Forms\Components\Placeholder::make('syarat_info')
                                    ->label('Syarat Pengajuan')
                                    ->content(function (callable $get) {
                                        $jenisIzin = $get('jenis_izin');
                                        if ($jenisIzin) {
                                            $jenisIzinData = ManajemenIzin::where('kode_izin', $jenisIzin)->first();
                                            if ($jenisIzinData && !empty($jenisIzinData->syarat_pengajuan)) {
                                                return implode("\n", array_map(fn($syarat) => "• " . $syarat, $jenisIzinData->syarat_pengajuan));
                                            }
                                        }
                                        return 'Pilih jenis izin untuk melihat syarat pengajuan.';
                                    })
                                    ->visible(function (callable $get) {
                                        $jenisIzin = $get('jenis_izin');
                                        if ($jenisIzin) {
                                            $jenisIzinData = ManajemenIzin::where('kode_izin', $jenisIzin)->first();
                                            return $jenisIzinData && !empty($jenisIzinData->syarat_pengajuan);
                                        }
                                        return false;
                                    })
                                    ->columnSpanFull(),

                                Forms\Components\Hidden::make('user_id')
                                    ->default(Auth::id()),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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

                Tables\Columns\TextColumn::make('approvedBy.nama')
                    ->label('Diproses Oleh')
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('approved_at')
                    ->label('Tanggal Diproses')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-')
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
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => is_null($record->approved_by)), // Only editable if not processed
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyIzins::route('/'),
            'create' => Pages\CreateMyIzin::route('/create'),
            'view' => Pages\ViewMyIzin::route('/{record}'),
            'edit' => Pages\EditMyIzin::route('/{record}/edit'),
        ];
    }

    public static function canEdit($record): bool
    {
        return is_null($record->approved_by);
    }

    public static function canDelete($record): bool
    {
        return is_null($record->approved_by);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('user_id', Auth::id())
            ->whereNull('approved_by')
            ->count();
    }

    /**
     * Handle file upload validation and cleanup
     */
    public static function validateFileUpload($file): bool
    {
        try {
            if (!$file) return true; // No file is okay if not required

            // Check if file exists in temporary storage
            if (is_string($file)) {
                return \Illuminate\Support\Facades\Storage::disk('public')->exists($file);
            }

            // For uploaded file objects
            if (is_object($file) && method_exists($file, 'isValid')) {
                return $file->isValid();
            }

            return false;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('File validation error', [
                'error' => $e->getMessage(),
                'file' => $file
            ]);
            return false;
        }
    }

    /**
     * Clean up orphaned temporary files
     */
    public static function cleanupTempFiles(): void
    {
        try {
            $tempPath = storage_path('app/livewire-tmp');
            if (is_dir($tempPath)) {
                $files = glob($tempPath . '/*');
                $cutoff = time() - (60 * 60); // 1 hour ago

                foreach ($files as $file) {
                    if (is_file($file) && filemtime($file) < $cutoff) {
                        unlink($file);
                    }
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Temp file cleanup error', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
