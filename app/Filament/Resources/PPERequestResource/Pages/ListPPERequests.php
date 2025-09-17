<?php

namespace App\Filament\Resources\PPERequestResource\Pages;

use App\Filament\Resources\PPERequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPPERequests extends ListRecords
{
    protected static string $resource = PPERequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
