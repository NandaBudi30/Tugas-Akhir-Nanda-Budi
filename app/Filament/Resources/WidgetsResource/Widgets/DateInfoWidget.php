<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Carbon\Carbon;

class DateInfoWidget extends Widget
{
    protected static string $view = 'filament.widgets.date-info-widget';


    // Atur posisi widget di header (menggantikan FilamentInfoWidget)
    public static function canView(): bool
    {
        return true;
    } 

    public function getDate(): string
    {
        return Carbon::now()->translatedFormat('l, d F Y'); 
        // contoh: Rabu, 4 September 2025
    }
}
