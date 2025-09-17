<?php

namespace App\Filament\Resources\AksesGedungResource\Pages;

use App\Filament\Resources\AksesGedungResource;
use Filament\Resources\Pages\ManageRecords;
use Filament\Actions;

class ManageAksesGedungs extends ManageRecords
{
    protected static string $resource = AksesGedungResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Ajukan Akses Gedung')
                ->icon('heroicon-o-plus-circle')
                ->visible(fn () => auth()->user()->hasRole('karyawan')), // hanya karyawan yang bisa lihat tombol ini
        ];
    }
}
