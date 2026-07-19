<?php

namespace App\Filament\Resources\Equipment\RelationManagers;

use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EquipmentMaintenanceHistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'maintenanceHistories';

    protected static ?string $title = 'Maintenance History';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('history_number')->required(),
                TextInput::make('work_order_number'),
                DateTimePicker::make('reported_at'),
                DateTimePicker::make('scheduled_at'),
                DateTimePicker::make('started_at'),
                DateTimePicker::make('completed_at'),
                Select::make('maintenance_type')->options(MaintenanceType::class)->required(),
                Select::make('status')->options(MaintenanceStatus::class)->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('history_number')
            ->columns([
                TextColumn::make('history_number')->searchable(),
                TextColumn::make('work_order_number')->searchable(),
                TextColumn::make('maintenance_type')->color('primary')->badge(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(MaintenanceStatus $state): string => match ($state) {
                        MaintenanceStatus::COMPLETED => 'success',
                        MaintenanceStatus::IN_PROGRESS => 'warning',
                        MaintenanceStatus::CANCELLED => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('completed_at')->dateTime()->sortable(),
                TextColumn::make('total_cost')->money()->sortable(),
            ]);
    }
}
