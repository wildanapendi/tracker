<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChapterResource\Pages;
use App\Filament\Resources\ChapterResource\RelationManagers;
use App\Models\Chapter;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ChapterResource extends Resource
{
    protected static ?string $model = Chapter::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static string|\UnitEnum|null $navigationGroup = 'Tracking';

    protected static ?string $modelLabel = 'Bab';

    protected static ?string $pluralModelLabel = 'Progres Bab';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Judul Bab')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Forms\Components\TextInput::make('weight')
                    ->label('Bobot Kontribusi')
                    ->numeric()
                    ->default(1.00)
                    ->rules(['numeric', 'min:0.01'])
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label('Urutan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Bab')
                    ->searchable(),
                Tables\Columns\TextColumn::make('progress')
                    ->label('Progres')
                    ->html()
                    ->state(function (Chapter $record) {
                        $service = new \App\Services\ProgressService();
                        $progress = $service->calculateChapterProgress($record);
                        return "
                            <div class='flex items-center gap-2 min-w-[120px]'>
                                <div class='w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5'>
                                    <div class='bg-indigo-600 dark:bg-indigo-500 h-2.5 rounded-full' style='width: {$progress}%'></div>
                                </div>
                                <span class='text-xs font-medium text-gray-700 dark:text-gray-300'>{$progress}%</span>
                            </div>
                        ";
                    }),
                Tables\Columns\TextColumn::make('tasks_count')
                    ->label('Jumlah Task')
                    ->counts('tasks'),
                Tables\Columns\TextColumn::make('weight')
                    ->label('Bobot')
                    ->numeric(2)
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TaskRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChapters::route('/'),
            'create' => Pages\CreateChapter::route('/create'),
            'edit' => Pages\EditChapter::route('/{record}/edit'),
        ];
    }
}
