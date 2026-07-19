<?php

namespace App\Filament\Resources\DocumentSequences;

use App\Filament\Resources\DocumentSequences\Pages\ManageDocumentSequences;
use App\Models\DocumentSequence;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DocumentSequenceResource extends Resource
{
    protected static ?string $model = DocumentSequence::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('document_type')->required(),
                TextInput::make('year')->required()->numeric(),
                TextInput::make('month')->numeric(),
                TextInput::make('last_number')->required()->numeric(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('document_type'),
                TextEntry::make('year')->numeric(),
                TextEntry::make('month')->numeric()->placeholder('-'),
                TextEntry::make('last_number')->numeric(),
                TextEntry::make('created_at')->dateTime(),
                TextEntry::make('updated_at')->dateTime(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('document_type')->searchable(),
                TextColumn::make('year')->numeric()->sortable(),
                TextColumn::make('month')->numeric()->sortable(),
                TextColumn::make('last_number')->numeric()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageDocumentSequences::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery();
    }
}
