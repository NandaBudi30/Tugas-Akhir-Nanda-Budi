<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbsenResource\Pages;
use App\Models\Absen;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AbsenResource extends Resource
{
    protected static ?string $model = Absen::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $activeNavigationIcon = 'heroicon-s-clipboard-document-check';
    protected static ?string $navigationLabel = 'Absensi';
    protected static ?string $pluralLabel = 'Absensi';
    protected static ?string $navigationGroup = 'Absensi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('tanggal')->required(),
                Forms\Components\TimePicker::make('jam_masuk'),
                Forms\Components\TimePicker::make('jam_pulang'),
            ])
            ->mutateFormDataBeforeCreate(function (array $data): array {
                $user = auth()->user();

                // Kalau karyawan → otomatis user_id nya sendiri
                if ($user->hasRole('karyawan')) {
                    $data['user_id'] = $user->id;
                }

                return $data;
            });
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user();

        return $table
            ->modifyQueryUsing(function ($query) use ($user) {
                // Jika role karyawan → hanya lihat datanya sendiri
                if ($user->hasRole('karyawan')) {
                    $query->where('user_id', $user->id);
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Nama Karyawan'),
                Tables\Columns\TextColumn::make('tanggal')->date(),
                Tables\Columns\TextColumn::make('jam_masuk'),
                Tables\Columns\TextColumn::make('jam_pulang'),
            ])
            ->filters(
                array_filter([
                    // Filter rentang tanggal
                    Tables\Filters\Filter::make('tanggal')
                        ->form([
                            Forms\Components\DatePicker::make('from')->label('Dari'),
                            Forms\Components\DatePicker::make('until')->label('Sampai'),
                        ])
                        ->query(function ($query, array $data) {
                            return $query
                                ->when($data['from'], fn($q) => $q->whereDate('tanggal', '>=', $data['from']))
                                ->when($data['until'], fn($q) => $q->whereDate('tanggal', '<=', $data['until']));
                        }),

                    // Filter nama karyawan (hanya untuk admin/superadmin)
                    $user->hasAnyRole(['admin', 'superadmin'])
                        ? Tables\Filters\SelectFilter::make('user_id')
                        ->label('Nama Karyawan')
                        ->options(User::pluck('name', 'id')->toArray())
                        : null,
                ])
            )
            ->emptyStateHeading('Tidak ada absensi yang ditemukan');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAbsens::route('/'),
        ];
    }
}
