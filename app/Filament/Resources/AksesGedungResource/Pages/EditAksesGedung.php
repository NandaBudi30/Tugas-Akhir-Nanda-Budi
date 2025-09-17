<?php

namespace App\Filament\Resources\AksesGedungResource\Pages;

use App\Filament\Resources\AksesGedungResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAksesGedung extends EditRecord
{
    protected static string $resource = AksesGedungResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
