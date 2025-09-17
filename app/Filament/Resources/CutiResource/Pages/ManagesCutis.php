<?php

namespace App\Filament\Resources\CutiResource\Pages;

use App\Filament\Resources\CutiResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCutis extends ManageRecords
{
    protected static string $resource = CutiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Ajukan Cuti Baru')
                ->icon('heroicon-o-plus-circle')
                ->mutateFormDataUsing(function (array $data): array {
                    // kalau role karyawan, set otomatis user_id
                    if (auth()->user()->hasRole('karyawan')) {
                        $data['user_id'] = auth()->id();
                    }
                    return $data;
                })
                ->visible(fn() => auth()->user()->hasRole('karyawan') || auth()->user()->hasRole('superadmin')),
        ];
    }
}
