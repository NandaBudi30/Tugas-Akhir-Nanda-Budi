<?php

namespace App\Filament\Widgets;

use App\Models\Cuti;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CutiStats extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        $user = Filament::auth()->user();
        return $user && $user->hasRole(['karyawan', 'admin', 'superadmin']);
    }

    protected function getStats(): array
    {
        $user = Filament::auth()->user();

        // Jika karyawan -> tampilkan data cuti miliknya saja
        if ($user->hasRole('karyawan')) {
            return [
                Stat::make('Total Pengajuan Saya', Cuti::where('user_id', $user->id)->count())
                    ->description('Semua pengajuan cuti saya')
                    ->color('primary'),

                Stat::make(
                    'Pending',
                    Cuti::where('user_id', $user->id)
                        ->where(function ($q) {
                            $q->where('status_admin', 'pending')
                                ->orWhere('status_superadmin', 'pending');
                        })->count()
                )
                    ->description('Menunggu persetujuan')
                    ->color('warning'),

                Stat::make(
                    'Disetujui',
                    Cuti::where('user_id', $user->id)
                        ->where('status_admin', 'disetujui')
                        ->where('status_superadmin', 'disetujui')
                        ->count()
                )
                    ->description('Cuti disetujui penuh')
                    ->color('success'),

                Stat::make(
                    'Ditolak',
                    Cuti::where('user_id', $user->id)
                        ->where(function ($q) {
                            $q->where('status_admin', 'ditolak')
                                ->orWhere('status_superadmin', 'ditolak');
                        })->count()
                )
                    ->description('Cuti saya ditolak')
                    ->color('danger'),
            ];
        }

        // Jika admin -> fokus ke status_admin
        if ($user->hasRole('admin')) {
            return [
                Stat::make('Total Pengajuan Cuti', Cuti::count())
                    ->description('Semua pengajuan cuti karyawan')
                    ->color('primary'),

                Stat::make('Pending Admin', Cuti::where('status_admin', 'pending')->count())
                    ->description('Belum diproses admin')
                    ->color('warning'),

                Stat::make('Disetujui Admin', Cuti::where('status_admin', 'disetujui')->count())
                    ->description('Sudah disetujui admin')
                    ->color('success'),

                Stat::make('Ditolak Admin', Cuti::where('status_admin', 'ditolak')->count())
                    ->description('Ditolak admin')
                    ->color('danger'),
            ];
        }

        // Jika superadmin -> fokus ke status_superadmin
        if ($user->hasRole('superadmin')) {
            return [
                Stat::make(
                    'Total Masuk Superadmin',
                    Cuti::where('status_admin', 'disetujui')->count()
                )
                    ->description('Semua pengajuan cuti karyawan')
                    ->color('primary'),

                Stat::make(
                    'Pending Superadmin',
                    Cuti::where('status_admin', 'disetujui')
                        ->where('status_superadmin', 'pending')
                        ->count()
                )
                    ->description('Belum diproses superadmin')
                    ->color('warning'),

                Stat::make(
                    'Disetujui Superadmin',
                    Cuti::where('status_admin', 'disetujui')
                        ->where('status_superadmin', 'disetujui')
                        ->count()
                )
                    ->description('Sudah disetujui superadmin')
                    ->color('success'),

                Stat::make(
                    'Ditolak Superadmin',
                    Cuti::where('status_admin', 'disetujui')
                        ->where('status_superadmin', 'ditolak')
                        ->count()
                )
                    ->description('Ditolak superadmin')
                    ->color('danger'),
            ];
        }

        return [];
    }
}
