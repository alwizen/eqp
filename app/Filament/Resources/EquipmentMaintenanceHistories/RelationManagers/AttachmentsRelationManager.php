<?php

namespace App\Filament\Resources\EquipmentMaintenanceHistories\RelationManagers;

use App\Enums\AttachmentCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    protected static ?string $title = 'Attachments';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category')->options(AttachmentCategory::class)->required(),
                TextInput::make('original_name')->required(),
                TextInput::make('file_name')->required(),
                TextInput::make('file_path')->required(),
                TextInput::make('disk')->required(),
                TextInput::make('mime_type'),
                TextInput::make('file_size')->numeric(),
                Textarea::make('description')->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('original_name')
            ->columns([
                TextColumn::make('original_name')->searchable(),
                TextColumn::make('category')->badge(),
                TextColumn::make('file_name')->searchable(),
                TextColumn::make('file_path')->searchable(),
                TextColumn::make('disk')->searchable(),
                TextColumn::make('file_size')->numeric(),
                TextColumn::make('uploaded_by')->numeric(),
            ]);
    }
}
