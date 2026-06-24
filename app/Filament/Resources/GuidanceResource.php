<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuidanceResource\Pages;
use App\Models\Guidance;
use App\Enums\GuidanceStatus;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GuidanceResource extends Resource
{
    protected static ?string $model = Guidance::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';
    protected static string|\UnitEnum|null $navigationGroup = 'Perencanaan';
    protected static ?string $modelLabel = 'Bimbingan';
    protected static ?string $pluralModelLabel = 'Jadwal Bimbingan';
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\DateTimePicker::make('scheduled_at')
                    ->label('Waktu Bimbingan')
                    ->required()
                    ->default(now())
                    ->minDate(fn ($livewire) => $livewire instanceof Pages\CreateGuidance ? now()->startOfDay() : null),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options(collect(GuidanceStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->label()]))
                    ->default(GuidanceStatus::Scheduled->value)
                    ->required(),
                Forms\Components\Textarea::make('agenda')
                    ->label('Agenda / Topik')
                    ->columnSpanFull()
                    ->rows(3),
                Forms\Components\Textarea::make('result')
                    ->label('Hasil / Catatan Bimbingan')
                    ->columnSpanFull()
                    ->rows(3),
                Forms\Components\Textarea::make('action_items')
                    ->label('Tindak Lanjut (Action Items)')
                    ->columnSpanFull()
                    ->rows(3),
                Forms\Components\TextInput::make('location')
                    ->label('Lokasi')
                    ->placeholder('Contoh: Ruang Dosen, Zoom Link')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Waktu Bimbingan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (GuidanceStatus $state): string => $state->color())
                    ->formatStateUsing(fn (GuidanceStatus $state): string => $state->label()),
                Tables\Columns\TextColumn::make('agenda')
                    ->label('Agenda')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('location')
                    ->label('Lokasi')
                    ->toggleable()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(collect(GuidanceStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->label()])),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGuidances::route('/'),
            'create' => Pages\CreateGuidance::route('/create'),
            'edit' => Pages\EditGuidance::route('/{record}/edit'),
        ];
    }
}
