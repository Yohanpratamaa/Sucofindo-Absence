<?php

namespace App\Filament\Widgets;

use App\Models\Izin;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentApprovalActivityWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function getTableHeading(): string
    {
        return 'Aktivitas Persetujuan Terbaru';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Izin::query()
                    ->whereNotNull('approved_by')
                    ->with(['user', 'approvedBy'])
                    ->latest('updated_at')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.nama')
                    ->label('Pegawai')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('jenis_izin')
                    ->label('Jenis')
                    ->colors([
                        'primary' => 'cuti',
                        'warning' => 'sakit',
                        'info' => 'izin',
                    ]),

                Tables\Columns\TextColumn::make('periode_izin')
                    ->label('Periode'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function (Izin $record): string {
                        return $record->status_badge['label'];
                    })
                    ->colors([
                        'success' => fn ($state) => $state === 'Disetujui',
                        'danger' => fn ($state) => $state === 'Ditolak',
                    ]),

                Tables\Columns\TextColumn::make('approvedBy.nama')
                    ->label('Diproses Oleh')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Waktu')
                    ->since()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (Izin $record): string => route('filament.admin.resources.izins.view', $record)),
            ])
            ->paginated(false);
    }
}
