<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ManageRecords;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected function afterCreate($record): void
    {
        if (request()->has('roles')) {
            $record->syncRoles(request('roles'));
        }
    }

    protected function afterSave($record): void
    {
        if (request()->has('roles')) {
            $record->syncRoles(request('roles'));
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah User Baru')
                ->icon('heroicon-o-user-plus')
                ->createAnother(false)
                ->visible(fn () => auth()->user()?->hasRole('superadmin')),
        ];
    }
}
