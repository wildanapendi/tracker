<?php

namespace App\Filament\Widgets;

use App\Models\Milestone;
use App\Enums\MilestoneStatus;
use App\Services\ProgressService;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class MilestoneStatusWidget extends BaseWidget
{
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';
    
    // Properti ini tetap static di TableWidget
    protected static ?string $heading = 'Status Milestone';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Milestone::query()
                    ->where('user_id', Auth::id())
                    ->orderBy('target_date', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Nama Milestone'),
                Tables\Columns\TextColumn::make('target_date')
                    ->label('Target Tanggal')
                    ->date('d M Y')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (MilestoneStatus $state): string => $state->color())
                    ->formatStateUsing(fn (MilestoneStatus $state): string => $state->label()),
                Tables\Columns\TextColumn::make('progress')
                    ->label('Progres Dokumen')
                    ->state(function (Milestone $record): string {
                        return app(ProgressService::class)->calculateMilestoneCompletion($record) . '%';
                    }),
            ])
            ->paginated(false);
    }
}
