<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OvertimeAssignmentResource\Pages;
use App\Filament\Resources\OvertimeAssignmentResource\RelationManagers;
use App\Models\OvertimeAssignment;
use App\Models\Pegawai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Filament\Facades\Filament;

class OvertimeAssignmentResource extends Resource
{
    protected static ?string $model = OvertimeAssignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Manajemen Lembur';

    protected static ?string $modelLabel = 'Penugasan Lembur';

    protected static ?string $pluralModelLabel = 'Manajemen Lembur';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Penugasan Lembur')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->label('Pegawai yang Ditugaskan')
                                    ->relationship('user', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->disabled(),

                                Forms\Components\Select::make('assigned_by')
                                    ->label('Ditugaskan Oleh')
                                    ->relationship('assignedBy', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->disabled(),

                                Forms\Components\TextInput::make('overtime_id')
                                    ->label('ID Proyek/Task Lembur')
                                    ->disabled(),

                                Forms\Components\DateTimePicker::make('assigned_at')
                                    ->label('Waktu Penugasan')
                                    ->disabled(),

                                Forms\Components\Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'Assigned' => 'Ditugaskan',
                                        'Accepted' => 'Diterima',
                                        'Rejected' => 'Ditolak',
                                    ])
                                    ->disabled(),
                            ]),
                    ]),

                Forms\Components\Section::make('Status Persetujuan')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('approved_by')
                                    ->label('Disetujui Oleh')
                                    ->relationship('approvedBy', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->disabled(),

                                Forms\Components\DateTimePicker::make('approved_at')
                                    ->label('Waktu Persetujuan')
                                    ->disabled(),

                                Forms\Components\Select::make('assign_by')
                                    ->label('Di-assign Ulang Oleh')
                                    ->relationship('assignBy', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->disabled(),
                            ]),
                    ])
                    ->visible(fn (OvertimeAssignment $record): bool => $record->approved_by !== null || $record->assign_by !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.nama')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.npp')
                    ->label('NPP')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('overtime_id')
                    ->label('ID Lembur')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('assignedBy.nama')
                    ->label('Ditugaskan Oleh')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('assigned_at_formatted')
                    ->label('Waktu Penugasan')
                    ->sortable('assigned_at'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(function (OvertimeAssignment $record): string {
                        return $record->status_badge['label'];
                    })
                    ->color(function (OvertimeAssignment $record): string {
                        return $record->status_badge['color'];
                    }),

                Tables\Columns\TextColumn::make('approval_info')
                    ->label('Info Persetujuan')
                    ->getStateUsing(function (OvertimeAssignment $record): string {
                        return $record->approval_info;
                    })
                    ->wrap()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('durasi_assignment')
                    ->label('Durasi')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('approvedBy.nama')
                    ->label('Disetujui Oleh')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('approved_at_formatted')
                    ->label('Waktu Persetujuan')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Assigned' => 'Ditugaskan',
                        'Accepted' => 'Diterima',
                        'Rejected' => 'Ditolak',
                    ])
                    ->native(false),

                Tables\Filters\SelectFilter::make('assigned_by')
                    ->label('Ditugaskan Oleh')
                    ->relationship('assignedBy', 'nama')
                    ->searchable()
                    ->preload()
                    ->native(false),

                Tables\Filters\Filter::make('assigned_at')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('assigned_at', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('assigned_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('accept')
                    ->label('Terima')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Terima Penugasan Lembur')
                    ->modalDescription(function (OvertimeAssignment $record): string {
                        $currentUser = Filament::auth()->user();
                        return "Apakah Anda yakin ingin menerima penugasan lembur ini?\n\nLembur akan tercatat diterima oleh: {$currentUser->nama}";
                    })
                    ->action(function (OvertimeAssignment $record): void {
                        $currentUser = Filament::auth()->user();
                        $record->accept(Filament::auth()->id());

                        Notification::make()
                            ->success()
                            ->title('Lembur Diterima')
                            ->body("Penugasan lembur telah berhasil diterima oleh {$currentUser->nama}")
                            ->send();
                    })
                    ->visible(fn (OvertimeAssignment $record): bool => $record->canChangeStatus()),

                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Penugasan Lembur')
                    ->modalDescription(function (OvertimeAssignment $record): string {
                        $currentUser = Filament::auth()->user();
                        return "Apakah Anda yakin ingin menolak penugasan lembur ini?\n\nLembur akan tercatat ditolak oleh: {$currentUser->nama}";
                    })
                    ->action(function (OvertimeAssignment $record): void {
                        $currentUser = Filament::auth()->user();
                        $record->reject(Filament::auth()->id());

                        Notification::make()
                            ->success()
                            ->title('Lembur Ditolak')
                            ->body("Penugasan lembur telah berhasil ditolak oleh {$currentUser->nama}")
                            ->send();
                    })
                    ->visible(fn (OvertimeAssignment $record): bool => $record->canChangeStatus()),

                Tables\Actions\Action::make('reassign')
                    ->label('Assign Ulang')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->form([
                        Forms\Components\Select::make('new_user_id')
                            ->label('Pegawai Baru')
                            ->options(Pegawai::active()->pluck('nama', 'id'))
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (OvertimeAssignment $record, array $data): void {
                        $record->reassign($data['new_user_id'], Filament::auth()->id());

                        Notification::make()
                            ->success()
                            ->title('Lembur Di-assign Ulang')
                            ->body('Penugasan lembur telah berhasil di-assign ulang.')
                            ->send();
                    })
                    ->visible(fn (OvertimeAssignment $record): bool => $record->status === 'Rejected' || $record->status === 'Assigned'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_accept')
                        ->label('Terima Terpilih')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $assignedRecords = $records->filter(fn (OvertimeAssignment $record) => $record->canChangeStatus());

                            foreach ($assignedRecords as $record) {
                                $record->accept(Filament::auth()->id());
                            }

                            Notification::make()
                                ->success()
                                ->title('Lembur Diterima')
                                ->body($assignedRecords->count() . ' penugasan lembur telah berhasil diterima.')
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('bulk_reject')
                        ->label('Tolak Terpilih')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $assignedRecords = $records->filter(fn (OvertimeAssignment $record) => $record->canChangeStatus());

                            foreach ($assignedRecords as $record) {
                                $record->reject(Filament::auth()->id());
                            }

                            Notification::make()
                                ->success()
                                ->title('Lembur Ditolak')
                                ->body($assignedRecords->count() . ' penugasan lembur telah berhasil ditolak.')
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListOvertimeAssignments::route('/'),
            'view' => Pages\ViewOvertimeAssignment::route('/{record}'),
            // Admin tidak bisa create/edit lembur, hanya view dan approve/reject
        ];
    }
}
