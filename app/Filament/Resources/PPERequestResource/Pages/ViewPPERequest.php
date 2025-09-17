<?php

namespace App\Filament\Resources\PPERequestResource\Pages;

use App\Filament\Resources\PPERequestResource;
use Filament\Resources\Pages\ViewRecord;

class ViewPPERequest extends ViewRecord
{
    protected static string $resource = PPERequestResource::class;

    protected static ?string $title = 'Detail Pengajuan PPE';
}
