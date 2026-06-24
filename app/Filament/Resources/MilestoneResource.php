<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MilestoneResource\Pages;
use App\Filament\Resources\MilestoneResource\RelationManagers;
use App\Models\Milestone;
use App\Enums\MilestoneStatus;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MilestoneResource extends Resource
{
    protected static ?string $model = Milestone::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-flag';
    protected static string|\UnitEnum|null $navigationGroup = 'Evaluasi';
    protected static ?string $modelLabel = 'Milestone';
    protected static ?string $pluralModelLabel = 'Milestone';
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Judul Milestone')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options(collect(MilestoneStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->label()]))
                    ->default(MilestoneStatus::NotStarted->value)
                    ->required(),
                Forms\Components\DatePicker::make('target_date')
                    ->label('Target Tanggal'),
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
                    ->label('Judul Milestone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (MilestoneStatus $state): string => $state->color())
                    ->formatStateUsing(fn (MilestoneStatus $state): string => $state->label()),
                Tables\Columns\TextColumn::make('target_date')
                    ->label('Target Tanggal')
                    ->date('d M Y')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('completion')
                    ->label('Kelengkapan Berkas')
                    ->html()
                    ->state(function (Milestone $record) {
                        $service = new \App\Services\ProgressService();
                        $progress = $service->calculateMilestoneCompletion($record);
                        return "
                            <div class='flex items-center gap-2 min-w-[120px]'>
                                <div class='w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5'>
                                    <div class='bg-emerald-600 dark:bg-emerald-500 h-2.5 rounded-full' style='width: {$progress}%'></div>
                                </div>
                                <span class='text-xs font-medium text-gray-700 dark:text-gray-300'>{$progress}%</span>
                            </div>
                        ";
                    }),
                Tables\Columns\TextColumn::make('documents_count')
                    ->label('Jumlah Berkas')
                    ->counts('documents'),
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
            ->where('user_id', auth()->id());
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DocumentRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMilestones::route('/'),
            'create' => Pages\CreateMilestone::route('/create'),
            'edit' => Pages\EditMilestone::route('/{record}/edit'),
        ];
    }
}
