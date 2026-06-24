<?php

namespace App\Filament\Resources\GuidanceResource\Pages;

use App\Filament\Resources\GuidanceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGuidance extends CreateRecord
{
    protected static string $resource = GuidanceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}
