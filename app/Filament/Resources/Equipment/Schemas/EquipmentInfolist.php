<?php

namespace App\Filament\Resources\Equipment\Schemas;

use App\Models\Equipment;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EquipmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('tag_no'),
                TextEntry::make('technical_no')
                    ->placeholder('-'),
                TextEntry::make('description'),
                TextEntry::make('functional_location')
                    ->placeholder('-'),
                TextEntry::make('manufacturer')
                    ->placeholder('-'),
                TextEntry::make('model_type')
                    ->placeholder('-'),
                TextEntry::make('serial_number')
                    ->placeholder('-'),
                TextEntry::make('category')
                    ->placeholder('-'),
                TextEntry::make('installation_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('latest_condition')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('last_maintenance_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('next_maintenance_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_by')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('updated_by')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Equipment $record): bool => $record->trashed()),
            ]);
    }
}
