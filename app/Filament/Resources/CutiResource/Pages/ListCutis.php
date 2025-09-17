<?php

namespace App\Filament\Resources\CutiResource\Pages;

use App\Filament\Resources\CutiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCutis extends ListRecords
{
    protected static string $resource = CutiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
