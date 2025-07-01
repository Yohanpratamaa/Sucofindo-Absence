<?php

namespace App\Filament\Pegawai\Resources;

use App\Filament\Pegawai\Resources\MyDinasLuarResource\Pages;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Enums\FontWeight;

class MyDinasLuarResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationLabel = 'Riwayat Dinas Luar';

    protected static ?string $modelLabel = 'Dinas Luar';

    protected static ?string $pluralModelLabel = 'Riwayat Dinas Luar';

    // Hide from navigation - using combined resource instead
    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationGroup(): ?string
    {
        return 'Data Absensi';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id())
            ->where('attendance_type', 'Dinas Luar');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Placeholder::make('info')
                    ->content('Data dinas luar hanya dapat dilihat, tidak dapat diubah.')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dinas')
                    ->date('d M Y')
                    ->sortable()
                    ->weight(FontWeight::Medium),

                Tables\Columns\TextColumn::make('check_in')
                    ->label('Check In')
                    ->time('H:i')
                    ->sortable()
                    ->icon('heroicon-m-arrow-right-on-rectangle')
                    ->iconColor('success'),

                Tables\Columns\TextColumn::make('absen_siang')
                    ->label('Absen Siang')
                    ->time('H:i')
                    ->placeholder('Belum absen siang')
                    ->icon('heroicon-m-sun')
                    ->iconColor('warning'),

                Tables\Columns\TextColumn::make('check_out')
                    ->label('Check Out')
                    ->time('H:i')
                    ->placeholder('Belum check out')
                    ->sortable()
                    ->icon('heroicon-m-arrow-left-on-rectangle')
                    ->iconColor('danger'),

                Tables\Columns\TextColumn::make('durasi_kerja')
                    ->label('Durasi Kerja')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('status_kehadiran')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Tepat Waktu' => 'success',
                        'Terlambat' => 'warning',
                        'Tidak Hadir' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('kelengkapan_status')
                    ->label('Kelengkapan Absensi')
                    ->getStateUsing(function (?Attendance $record): string {
                        if (!$record) return '-';
                        $kelengkapan = $record->kelengkapan_absensi;
                        return "{$kelengkapan['completed']}/{$kelengkapan['total']} - {$kelengkapan['status']}";
                    })
                    ->badge()
                    ->color(function (?Attendance $record): string {
                        if (!$record) return 'gray';
                        $kelengkapan = $record->kelengkapan_absensi;
                        return $kelengkapan['status'] === 'Lengkap' ? 'success' : 'warning';
                    }),

                Tables\Columns\ImageColumn::make('picture_absen_masuk_url')
                    ->label('Foto Check In')
                    ->height(50)
                    ->width(50)
                    ->circular()
                    ->toggleable()
                    ->tooltip('Foto saat check in')
                    ->extraAttributes(['style' => 'object-fit: cover;']),

                Tables\Columns\ImageColumn::make('picture_absen_siang_url')
                    ->label('Foto Absen Siang')
                    ->height(50)
                    ->width(50)
                    ->circular()
                    ->placeholder('Belum absen siang')
                    ->toggleable()
                    ->tooltip('Foto saat absen siang')
                    ->extraAttributes(['style' => 'object-fit: cover;']),

                Tables\Columns\ImageColumn::make('picture_absen_pulang_url')
                    ->label('Foto Check Out')
                    ->height(50)
                    ->width(50)
                    ->circular()
                    ->placeholder('Belum check out')
                    ->toggleable()
                    ->tooltip('Foto saat check out')
                    ->extraAttributes(['style' => 'object-fit: cover;']),

                Tables\Columns\TextColumn::make('lokasi_info')
                    ->label('Info Lokasi')
                    ->getStateUsing(function (?Attendance $record): string {
                        if (!$record) return '-';

                        $locations = [];
                        if ($record->latitude_absen_masuk && $record->longitude_absen_masuk) {
                                                    $locations[] = 'Check In ✓';
                        }
                        if ($record->latitude_absen_siang && $record->longitude_absen_siang) {
                            $locations[] = 'Siang ✓';
                        }
                        if ($record->latitude_absen_pulang && $record->longitude_absen_pulang) {
                            $locations[] = 'Check Out ✓';
                        }

                        return empty($locations) ? 'Tidak ada data lokasi' : implode(' | ', $locations);
                    })
                    ->color('warning')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_kehadiran')
                    ->label('Status Kehadiran')
                    ->options([
                        'Tepat Waktu' => 'Tepat Waktu',
                        'Terlambat' => 'Terlambat',
                        'Tidak Hadir' => 'Tidak Hadir',
                    ]),

                Tables\Filters\Filter::make('kelengkapan')
                    ->label('Kelengkapan Absensi')
                    ->form([
                        Forms\Components\Select::make('kelengkapan_status')
                            ->label('Status Kelengkapan')
                            ->options([
                                'lengkap' => 'Lengkap (3/3)',
                                'tidak_lengkap' => 'Tidak Lengkap',
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['kelengkapan_status'])) {
                            return $query;
                        }

                        if ($data['kelengkapan_status'] === 'lengkap') {
                            // Dinas Luar lengkap: check_in, absen_siang, dan check_out
                            return $query->whereNotNull('check_in')
                                        ->whereNotNull('absen_siang')
                                        ->whereNotNull('check_out');
                        } else {
                            // Tidak lengkap: ada yang null
                            return $query->where(function ($q) {
                                $q->whereNull('check_in')
                                  ->orWhereNull('absen_siang')
                                  ->orWhereNull('check_out');
                            });
                        }
                    }),

                Tables\Filters\Filter::make('bulan_ini')
                    ->label('Bulan Ini')
                    ->query(fn (Builder $query) => $query->whereMonth('created_at', now()->month)
                                                         ->whereYear('created_at', now()->year))
                    ->toggle(),

                Tables\Filters\Filter::make('belum_checkout')
                    ->label('Belum Check Out')
                    ->query(fn (Builder $query) => $query->whereNull('check_out'))
                    ->toggle(),

                Tables\Filters\Filter::make('belum_absen_siang')
                    ->label('Belum Absen Siang')
                    ->query(fn (Builder $query) => $query->whereNull('absen_siang'))
                    ->toggle(),

                Tables\Filters\Filter::make('tanggal')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Detail')
                    ->icon('heroicon-o-eye'),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc')
            ->poll('60s'); // Auto refresh every 60 seconds
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyDinasLuars::route('/'),
            'view' => Pages\ViewMyDinasLuar::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getEloquentQuery()->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }
}
