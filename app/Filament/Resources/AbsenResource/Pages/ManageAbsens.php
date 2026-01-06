<?php

namespace App\Filament\Resources\AbsenResource\Pages;

use App\Filament\Resources\AbsenResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use App\Models\Absen;
use Filament\Notifications\Notification;

class ManageAbsens extends ManageRecords
{
    protected static string $resource = AbsenResource::class;

    protected function getHeaderActions(): array
    {
        $user = auth()->user();

        return array_filter([
            // =======================
            // ABSEN MASUK
            // =======================
            $user->hasRole('karyawan')
                ? Actions\Action::make('Absen Masuk')
                    ->color('success')
                    ->action(function () use ($user) {

                        $today = now()->toDateString();

                        // 🔒 Sudah absen masuk & pulang hari ini
                        $sudahSelesaiHariIni = Absen::where('user_id', $user->id)
                            ->where('tanggal', $today)
                            ->whereNotNull('jam_masuk')
                            ->whereNotNull('jam_pulang')
                            ->exists();

                        if ($sudahSelesaiHariIni) {
                            Notification::make()
                                ->title('Anda sudah melakukan absen masuk')
                                ->danger()
                                ->send();
                            return;
                        }

                        // ❗ Masih ada absen belum pulang (shift malam)
                        $masihAda = Absen::where('user_id', $user->id)
                            ->whereNull('jam_pulang')
                            ->exists();

                        if ($masihAda) {
                            Notification::make()
                                ->title('Anda belum melakukan absen pulang')
                                ->danger()
                                ->send();
                            return;
                        }

                        // ✅ Absen masuk
                        Absen::create([
                            'user_id'   => $user->id,
                            'tanggal'   => $today,
                            'jam_masuk' => now()->format('H:i:s'),
                        ]);

                        Notification::make()
                            ->title('Berhasil Absen Masuk')
                            ->success()
                            ->send();
                    })
                : null,

            // =======================
            // ABSEN PULANG
            // =======================
            $user->hasRole('karyawan')
                ? Actions\Action::make('Absen Pulang')
                    ->color('danger')
                    ->action(function () use ($user) {

                        $absen = Absen::where('user_id', $user->id)
                            ->whereNull('jam_pulang')
                            ->orderBy('jam_masuk', 'desc')
                            ->first();

                        if (! $absen) {
                            Notification::make()
                                ->title('Anda belum melakukan absen masuk')
                                ->danger()
                                ->send();
                            return;
                        }

                        $absen->update([
                            'jam_pulang' => now()->format('H:i:s'),
                        ]);

                        Notification::make()
                            ->title('Berhasil Absen Pulang')
                            ->success()
                            ->send();
                    })
                : null,
        ]);
    }
}
