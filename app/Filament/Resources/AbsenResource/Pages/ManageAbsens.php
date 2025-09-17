<?php

namespace App\Filament\Resources\AbsenResource\Pages;

use App\Filament\Resources\AbsenResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use App\Models\Absen;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class ManageAbsens extends ManageRecords
{
    protected static string $resource = AbsenResource::class;

    protected function getHeaderActions(): array
    {
        $user = auth()->user();

        return array_filter([
            $user->hasRole('karyawan')
                ? Actions\Action::make('Absen Masuk')
                ->action(function () use ($user) {
                    $today = now()->toDateString();

                    $absen = Absen::firstOrCreate(
                        ['user_id' => $user->id, 'tanggal' => $today],
                        ['jam_masuk' => now()->format('H:i:s')]
                    );

                    if ($absen->wasRecentlyCreated) {
                        Notification::make()->title('Berhasil Absen Masuk')->success()->send();
                    } else {
                        Notification::make()->title('Anda sudah absen masuk hari ini')->danger()->send();
                    }
                })
                ->color('success')
                : null,

            $user->hasRole('karyawan')
                ? Actions\Action::make('Absen Pulang')
                ->action(function () use ($user) {
                    $today = now()->toDateString();

                    $absen = Absen::where('user_id', $user->id)
                        ->where('tanggal', $today)
                        ->first();

                    if ($absen && !$absen->jam_pulang) {
                        $absen->update(['jam_pulang' => now()->format('H:i:s')]);
                        Notification::make()->title('Berhasil Absen Pulang')->success()->send();
                    } else {
                        Notification::make()->title('Anda belum absen masuk / sudah absen pulang')->danger()->send();
                    }
                })
                ->color('danger')
                : null,
        ]);
    }
}
