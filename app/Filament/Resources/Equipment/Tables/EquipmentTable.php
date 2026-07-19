<?php

namespace App\Filament\Resources\Equipment\Tables;

use App\Filament\Resources\Equipment\RelationManagers\EquipmentMaintenanceHistoriesRelationManager;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Guava\FilamentModalRelationManagers\Actions\RelationManagerAction;

class EquipmentTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->paginated([25, 50, 100, 'all'])
            ->columns([
                TextColumn::make('tag_no')
                    ->searchable(),
                TextColumn::make('technical_no')
                    ->searchable(),
                TextColumn::make('description')
                    ->searchable(),
                TextColumn::make('functional_location')
                    ->searchable(),
                TextColumn::make('manufacturer')
                    ->searchable(),
                TextColumn::make('model_type')
                    ->searchable(),
                TextColumn::make('serial_number')
                    ->searchable(),
                TextColumn::make('category')
                    ->searchable(),
                TextColumn::make('installation_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                TextColumn::make('latest_condition')
                    ->badge()
                    ->searchable(),
                TextColumn::make('last_maintenance_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('next_maintenance_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_by')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_by')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                RelationManagerAction::make('maintenance-histories-relation-manager')
                    ->label('View Maintenance Histories')
                    ->relationManager(EquipmentMaintenanceHistoriesRelationManager::make()),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
