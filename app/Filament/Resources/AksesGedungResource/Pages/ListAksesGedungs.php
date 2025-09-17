<?php

namespace App\Filament\Resources\AksesGedungResource\Pages;

use App\Filament\Resources\AksesGedungResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAksesGedungs extends ListRecords
{
    protected static string $resource = AksesGedungResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
