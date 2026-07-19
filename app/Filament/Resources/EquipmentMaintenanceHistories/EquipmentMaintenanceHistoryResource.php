<?php

namespace App\Filament\Resources\EquipmentMaintenanceHistories;

use App\Enums\EquipmentCondition;
use App\Enums\ExecutorType;
use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use App\Filament\Resources\EquipmentMaintenanceHistories\Pages\CreateEquipmentMaintenanceHistory;
use App\Filament\Resources\EquipmentMaintenanceHistories\Pages\EditEquipmentMaintenanceHistory;
use App\Filament\Resources\EquipmentMaintenanceHistories\Pages\ManageEquipmentMaintenanceHistories;
use App\Filament\Resources\EquipmentMaintenanceHistories\Pages\ViewEquipmentMaintenanceHistory;
use App\Models\EquipmentMaintenanceHistory;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EquipmentMaintenanceHistoryResource extends Resource
{
    protected static ?string $model = EquipmentMaintenanceHistory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('equipment_id')
                    ->relationship('equipment', 'id')
                    ->required(),
                TextInput::make('history_number')
                    ->required(),
                TextInput::make('work_order_number'),
                DateTimePicker::make('reported_at'),
                DateTimePicker::make('scheduled_at'),
                DateTimePicker::make('started_at'),
                DateTimePicker::make('completed_at'),
                Select::make('maintenance_type')
                    ->options(MaintenanceType::class)
                    ->required(),
                Select::make('status')
                    ->options(MaintenanceStatus::class)
                    ->default('draft')
                    ->required(),
                Select::make('executor_type')
                    ->options(ExecutorType::class)
                    ->required(),
                Select::make('vendor_id')
                    ->relationship('vendor', 'name'),
                TextInput::make('internal_pic_user_id')
                    ->numeric(),
                TextInput::make('technician_name'),
                TextInput::make('component'),
                Textarea::make('problem_description')
                    ->columnSpanFull(),
                Textarea::make('root_cause')
                    ->columnSpanFull(),
                Textarea::make('action_taken')
                    ->columnSpanFull(),
                Textarea::make('recommendation')
                    ->columnSpanFull(),
                Select::make('condition_before')
                    ->options(EquipmentCondition::class),
                Select::make('condition_after')
                    ->options(EquipmentCondition::class),
                TextInput::make('downtime_minutes')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('labor_cost')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                TextInput::make('material_cost')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                TextInput::make('other_cost')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                TextInput::make('total_cost')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                DateTimePicker::make('next_maintenance_at'),
                Textarea::make('notes')
                    ->columnSpanFull(),
                Textarea::make('cancellation_reason')
                    ->columnSpanFull(),
                DateTimePicker::make('cancelled_at'),
                TextInput::make('created_by')
                    ->numeric(),
                TextInput::make('updated_by')
                    ->numeric(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('equipment.id')
                    ->label('Equipment'),
                TextEntry::make('history_number'),
                TextEntry::make('work_order_number')
                    ->placeholder('-'),
                TextEntry::make('reported_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('scheduled_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('started_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('completed_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('maintenance_type')
                    ->badge(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('executor_type')
                    ->badge(),
                TextEntry::make('vendor.name')
                    ->label('Vendor')
                    ->placeholder('-'),
                TextEntry::make('internal_pic_user_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('technician_name')
                    ->placeholder('-'),
                TextEntry::make('component')
                    ->placeholder('-'),
                TextEntry::make('problem_description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('root_cause')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('action_taken')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('recommendation')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('condition_before')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('condition_after')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('downtime_minutes')
                    ->numeric(),
                TextEntry::make('labor_cost')
                    ->money(),
                TextEntry::make('material_cost')
                    ->money(),
                TextEntry::make('other_cost')
                    ->money(),
                TextEntry::make('total_cost')
                    ->money(),
                TextEntry::make('next_maintenance_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('cancellation_reason')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('cancelled_at')
                    ->dateTime()
                    ->placeholder('-'),
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('equipment.technical_no')
                    ->searchable(),
                TextColumn::make('history_number')
                    ->searchable(),
                TextColumn::make('work_order_number')
                    ->searchable(),
                TextColumn::make('reported_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('scheduled_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('maintenance_type')
                    ->badge()
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                TextColumn::make('executor_type')
                    ->badge()
                    ->searchable(),
                TextColumn::make('vendor.name')
                    ->searchable(),
                TextColumn::make('internal_pic_user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('technician_name')
                    ->searchable(),
                TextColumn::make('component')
                    ->searchable(),
                TextColumn::make('condition_before')
                    ->badge()
                    ->searchable(),
                TextColumn::make('condition_after')
                    ->badge()
                    ->searchable(),
                TextColumn::make('downtime_minutes')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('labor_cost')
                    ->money()
                    ->sortable(),
                TextColumn::make('material_cost')
                    ->money()
                    ->sortable(),
                TextColumn::make('other_cost')
                    ->money()
                    ->sortable(),
                TextColumn::make('total_cost')
                    ->money()
                    ->sortable(),
                TextColumn::make('next_maintenance_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('cancelled_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_by')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('updated_by')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEquipmentMaintenanceHistories::route('/'),
            'create' => CreateEquipmentMaintenanceHistory::route('/create'),
            'view' => ViewEquipmentMaintenanceHistory::route('/{record}'),
            'edit' => EditEquipmentMaintenanceHistory::route('/{record}/edit'),
        ];
    }
}
