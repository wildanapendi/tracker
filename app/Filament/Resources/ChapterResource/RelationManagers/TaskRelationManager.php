<?php

namespace App\Filament\Resources\ChapterResource\RelationManagers;

use App\Models\ChapterTask;
use App\Enums\TaskStatus;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TaskRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Nama Task')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options(collect(TaskStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->label()]))
                    ->default(TaskStatus::NotStarted->value)
                    ->required(),
                Forms\Components\TextInput::make('order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Forms\Components\DatePicker::make('due_date')
                    ->label('Tenggat Waktu'),
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label('Urutan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Nama Task')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (TaskStatus $state): string => $state->color())
                    ->formatStateUsing(fn (TaskStatus $state): string => $state->label()),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Tenggat Waktu')
                    ->date('d M Y')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Selesai Pada')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-')
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Actions\CreateAction::make(),
            ])
            ->actions([
                Actions\Action::make('toggleStatus')
                    ->label('Ubah Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->action(function (ChapterTask $record) {
                        $newStatus = match ($record->status) {
                            TaskStatus::NotStarted => TaskStatus::InProgress,
                            TaskStatus::InProgress => TaskStatus::Completed,
                            TaskStatus::Completed => TaskStatus::NotStarted,
                        };
                        $record->update(['status' => $newStatus]);
                    }),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
