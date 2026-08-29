<?php

namespace App\Filament\Widgets;

use App\Models\Guidance;
use App\Enums\GuidanceStatus;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingGuidancesWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    
    // Properti ini tetap static di TableWidget
    protected static ?string $heading = 'Bimbingan Mendatang';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Guidance::query()
                    ->where('user_id', auth()->id())
                    ->where('status', GuidanceStatus::Scheduled->value)
                    ->where('scheduled_at', '>=', now()->startOfDay())
                    ->orderBy('scheduled_at', 'asc')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i'),
                Tables\Columns\TextColumn::make('agenda')
                    ->label('Agenda')
                    ->limit(50),
                Tables\Columns\TextColumn::make('location')
                    ->label('Lokasi'),
            ])
            ->paginated(false);
    }
}
