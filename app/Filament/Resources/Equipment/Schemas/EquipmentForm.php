<?php

namespace App\Filament\Resources\Equipment\Schemas;

use App\Enums\EquipmentCondition;
use App\Enums\EquipmentStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class EquipmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('tag_no')
                    ->required(),
                TextInput::make('technical_no'),
                TextInput::make('description')
                    ->required(),
                TextInput::make('functional_location'),
                TextInput::make('manufacturer'),
                TextInput::make('model_type'),
                TextInput::make('serial_number'),
                TextInput::make('category'),
                DatePicker::make('installation_date'),
                Select::make('status')
                    ->options(EquipmentStatus::class)
                    ->default('operational')
                    ->required(),
                Select::make('latest_condition')
                    ->options(EquipmentCondition::class),
                DateTimePicker::make('last_maintenance_at'),
                DateTimePicker::make('next_maintenance_at'),
                Textarea::make('notes')
                    ->columnSpanFull(),
                TextInput::make('created_by')
                    ->numeric(),
                TextInput::make('updated_by')
                    ->numeric(),
            ]);
    }
}
