<?php

namespace App\Filament\Pegawai\Resources;

use App\Filament\Pegawai\Resources\MyDinasLuarAttendanceResource\Pages;
use App\Models\Attendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MyDinasLuarAttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Riwayat Dinas Luar';

    protected static ?string $navigationGroup = 'Absensi';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id())
            ->where('attendance_type', 'Dinas Luar')
            ->orderBy('created_at', 'desc');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('check_in')
                    ->label('Absen Pagi')
                    ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('H:i') : '-')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray'),

                Tables\Columns\TextColumn::make('absen_siang')
                    ->label('Absen Siang')
                    ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('H:i') : '-')
                    ->badge()
                    ->color(fn ($state) => $state ? 'warning' : 'gray'),

                Tables\Columns\TextColumn::make('check_out')
                    ->label('Absen Sore')
                    ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('H:i') : '-')
                    ->badge()
                    ->color(fn ($state) => $state ? 'info' : 'gray'),

                Tables\Columns\TextColumn::make('status_kehadiran')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Tepat Waktu' => 'success',
                        'Terlambat' => 'warning',
                        'Tidak Hadir' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('progress')
                    ->label('Progress')
                    ->formatStateUsing(function ($record) {
                        $pagi = !is_null($record->check_in);
                        $siang = !is_null($record->absen_siang);
                        $sore = !is_null($record->check_out);

                        $completed = ($pagi ? 1 : 0) + ($siang ? 1 : 0) + ($sore ? 1 : 0);
                        $percentage = round(($completed / 3) * 100);

                        return $percentage . '%';
                    })
                    ->badge()
                    ->color(function ($record) {
                        $pagi = !is_null($record->check_in);
                        $siang = !is_null($record->absen_siang);
                        $sore = !is_null($record->check_out);

                        $completed = ($pagi ? 1 : 0) + ($siang ? 1 : 0) + ($sore ? 1 : 0);
                        $percentage = round(($completed / 3) * 100);

                        if ($percentage == 100) return 'success';
                        if ($percentage >= 66) return 'warning';
                        if ($percentage >= 33) return 'info';
                        return 'gray';
                    }),

                Tables\Columns\ImageColumn::make('picture_absen_masuk_url')
                    ->label('Foto Pagi')
                    ->height(40)
                    ->width(40)
                    ->circular()
                    ->toggleable()
                    ->tooltip('Foto Absensi Pagi'),

                Tables\Columns\ImageColumn::make('picture_absen_siang_url')
                    ->label('Foto Siang')
                    ->height(40)
                    ->width(40)
                    ->circular()
                    ->toggleable()
                    ->tooltip('Foto Absensi Siang'),

                Tables\Columns\ImageColumn::make('picture_absen_pulang_url')
                    ->label('Foto Sore')
                    ->height(40)
                    ->width(40)
                    ->circular()
                    ->toggleable()
                    ->tooltip('Foto Absensi Sore'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('month')
                    ->label('Bulan')
                    ->options([
                        '1' => 'Januari',
                        '2' => 'Februari',
                        '3' => 'Maret',
                        '4' => 'April',
                        '5' => 'Mei',
                        '6' => 'Juni',
                        '7' => 'Juli',
                        '8' => 'Agustus',
                        '9' => 'September',
                        '10' => 'Oktober',
                        '11' => 'November',
                        '12' => 'Desember',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!empty($data['value'])) {
                            return $query->whereMonth('created_at', $data['value']);
                        }
                        return $query;
                    }),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Kehadiran')
                    ->options([
                        'Tepat Waktu' => 'Tepat Waktu',
                        'Terlambat' => 'Terlambat',
                        'Tidak Hadir' => 'Tidak Hadir',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!empty($data['value'])) {
                            return $query->whereHas('user', function ($q) use ($data) {
                                // This is a computed field, so we'll filter differently
                                // For now, we'll use a basic time-based filter for late status
                                if ($data['value'] === 'Terlambat') {
                                    return $q->whereTime('check_in', '>', '08:30:00');
                                }
                            });
                        }
                        return $query;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum ada riwayat absensi dinas luar')
            ->emptyStateDescription('Riwayat absensi dinas luar Anda akan muncul di sini setelah Anda melakukan absensi.')
            ->emptyStateIcon('heroicon-o-clipboard-document-list');
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
            'index' => Pages\ListMyDinasLuarAttendances::route('/'),
            'view' => Pages\ViewMyDinasLuarAttendance::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
