<?php

namespace App\Filament\Resources\EquipmentMaintenanceHistories\RelationManagers;

use App\Models\SparePart;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SparePartsRelationManager extends RelationManager
{
    protected static string $relationship = 'sparePartUsages';

    protected static ?string $title = 'Spare Parts';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('spare_part_id')
                    ->label('Spare Part')
                    ->options(SparePart::query()->pluck('name', 'id'))
                    ->required(),
                TextInput::make('quantity')->numeric()->required(),
                TextInput::make('unit_price')->numeric()->required(),
                TextInput::make('notes'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('spare_part_id')
            ->columns([
                TextColumn::make('sparePart.name')->label('Spare Part')->searchable(),
                TextColumn::make('quantity')->numeric()->sortable(),
                TextColumn::make('unit_price')->money()->sortable(),
                TextColumn::make('total_price')->money()->sortable(),
                TextColumn::make('notes')->searchable(),
            ]);
    }
}
