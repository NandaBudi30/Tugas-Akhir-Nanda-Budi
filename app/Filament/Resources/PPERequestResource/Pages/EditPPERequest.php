<?php

namespace App\Filament\Resources\PPERequestResource\Pages;

use App\Filament\Resources\PPERequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPPERequest extends EditRecord
{
    protected static string $resource = PPERequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
