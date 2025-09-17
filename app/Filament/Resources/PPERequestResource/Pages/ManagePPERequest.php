<?php

namespace App\Filament\Resources\PPERequestResource\Pages;

use App\Filament\Resources\PPERequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePPERequests extends ManageRecords
{
    protected static string $resource = PPERequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Ajukan Penukaran PPE')
                ->icon('heroicon-o-plus-circle'),
                    
        ];
    }
}
