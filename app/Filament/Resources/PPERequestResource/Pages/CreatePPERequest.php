<?php

namespace App\Filament\Resources\PPERequestResource\Pages;

use App\Filament\Resources\PPERequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePPERequest extends CreateRecord
{
    protected static string $resource = PPERequestResource::class;

    public function getTitle(): string
    {
        return 'Pengajuan Penukaran PPE';
    }
}
