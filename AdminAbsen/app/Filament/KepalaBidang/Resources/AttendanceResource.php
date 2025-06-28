<?php

namespace App\Filament\KepalaBidang\Resources;

use App\Filament\KepalaBidang\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use App\Models\Pegawai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Data Absensi';

    protected static ?string $modelLabel = 'Absensi';

    protected static ?string $pluralModelLabel = 'Data Absensi';

    protected static ?string $navigationGroup = 'Manajemen Data';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        // Kepala Bidang hanya bisa melihat absensi pegawai (role employee)
        $teamMembers = Pegawai::where('role_user', 'employee')
            ->where('status', 'active')
            ->pluck('id');

        return parent::getEloquentQuery()->whereIn('user_id', $teamMembers);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Absensi')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Pegawai')
                            ->relationship('user', 'nama', fn (Builder $query) => $query->where('role_user', 'employee'))
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\DatePicker::make('created_at')
                            ->label('Tanggal')
                            ->required()
                            ->default(now()),

                        Forms\Components\TimePicker::make('check_in')
                            ->label('Jam Masuk')
                            ->required(),

                        Forms\Components\TimePicker::make('check_out')
                            ->label('Jam Keluar'),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.nama')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('user.npp')
                    ->label('NPP')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('check_in')
                    ->label('Jam Masuk')
                    ->time('H:i')
                    ->badge()
                    ->color(function ($record): string {
                        if (!$record->check_in) return 'gray';
                        $checkIn = \Carbon\Carbon::parse($record->check_in);
                        $deadline = \Carbon\Carbon::parse('08:00:00');
                        return $checkIn->gt($deadline) ? 'danger' : 'success';
                    }),

                Tables\Columns\TextColumn::make('check_out')
                    ->label('Jam Keluar')
                    ->time('H:i')
                    ->placeholder('Belum absen keluar')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('work_hours')
                    ->label('Jam Kerja')
                    ->state(function ($record) {
                        if ($record->check_in && $record->check_out) {
                            $checkIn = \Carbon\Carbon::parse($record->check_in);
                            $checkOut = \Carbon\Carbon::parse($record->check_out);
                            $diff = $checkIn->diff($checkOut);
                            return $diff->format('%H:%I');
                        }
                        return '-';
                    })
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(function ($record): string {
                        if (!$record->check_in) return 'gray';
                        $checkIn = \Carbon\Carbon::parse($record->check_in);
                        $deadline = \Carbon\Carbon::parse('08:00:00');
                        return $checkIn->gt($deadline) ? 'danger' : 'success';
                    })
                    ->formatStateUsing(function ($record): string {
                        if (!$record->check_in) return 'Tidak Hadir';
                        $checkIn = \Carbon\Carbon::parse($record->check_in);
                        $deadline = \Carbon\Carbon::parse('08:00:00');
                        return $checkIn->gt($deadline) ? 'Terlambat' : 'Tepat Waktu';
                    }),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(30)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Pegawai')
                    ->relationship('user', 'nama', fn (Builder $query) => $query->where('role_user', 'employee'))
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('Tanggal Dari'),
                        Forms\Components\DatePicker::make('date_until')
                            ->label('Tanggal Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),

                Tables\Filters\Filter::make('late_arrivals')
                    ->label('Terlambat')
                    ->query(function (Builder $query): Builder {
                        return $query->whereTime('check_in', '>', '08:00:00');
                    }),

                Tables\Filters\Filter::make('no_checkout')
                    ->label('Belum Absen Keluar')
                    ->query(function (Builder $query): Builder {
                        return $query->whereNull('check_out');
                    }),

                Tables\Filters\Filter::make('today')
                    ->label('Hari Ini')
                    ->query(function (Builder $query): Builder {
                        return $query->whereDate('created_at', today());
                    }),

                Tables\Filters\Filter::make('this_week')
                    ->label('Minggu Ini')
                    ->query(function (Builder $query): Builder {
                        return $query->whereBetween('created_at', [
                            now()->startOfWeek(),
                            now()->endOfWeek()
                        ]);
                    }),

                Tables\Filters\Filter::make('this_month')
                    ->label('Bulan Ini')
                    ->query(function (Builder $query): Builder {
                        return $query->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year);
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat'),

                Tables\Actions\EditAction::make()
                    ->label('Edit')
                    ->visible(fn ($record) => !$record->check_out), // Hanya bisa edit jika belum checkout

                Tables\Actions\Action::make('checkout')
                    ->label('Absen Keluar')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('success')
                    ->visible(fn ($record) => $record->check_in && !$record->check_out)
                    ->requiresConfirmation()
                    ->modalHeading('Absen Keluar Pegawai')
                    ->modalDescription(fn ($record) => "Absenkan keluar {$record->user->nama} untuk hari ini?")
                    ->action(function ($record) {
                        $record->update([
                            'check_out' => now()->format('H:i:s')
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Absen Keluar Berhasil')
                            ->body("{$record->user->nama} telah diabsen keluar pada " . now()->format('H:i'))
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('bulk_checkout')
                    ->label('Absen Keluar Terpilih')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Absen Keluar Pegawai Terpilih')
                    ->modalDescription('Apakah Anda yakin ingin mengabsen keluar semua pegawai yang dipilih?')
                    ->action(function ($records) {
                        $count = 0;
                        foreach ($records as $record) {
                            if ($record->check_in && !$record->check_out) {
                                $record->update([
                                    'check_out' => now()->format('H:i:s')
                                ]);
                                $count++;
                            }
                        }

                        Notification::make()
                            ->success()
                            ->title('Absen Keluar Berhasil')
                            ->body("{$count} pegawai telah diabsen keluar.")
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'view' => Pages\ViewAttendance::route('/{record}'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $today = now()->format('Y-m-d');
        return static::getModel()::whereDate('created_at', $today)
            ->whereHas('user', function (Builder $query) {
                $query->where('role_user', 'employee');
            })
            ->whereNull('check_out')
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit($record): bool
    {
        return !$record->check_out; // Hanya bisa edit jika belum checkout
    }

    public static function canDelete($record): bool
    {
        return false; // Kepala bidang tidak bisa menghapus data absensi
    }
}
