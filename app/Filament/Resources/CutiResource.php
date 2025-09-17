<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CutiResource\Pages;
use App\Models\Cuti;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CutiResource extends Resource
{
    protected static ?string $model = Cuti::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';
    protected static ?string $activeNavigationIcon = 'heroicon-s-calendar-date-range';
    protected static ?string $navigationLabel = 'Pengajuan Cuti';
    protected static ?string $pluralLabel = 'Pengajuan Cuti';
    protected static ?string $navigationGroup = 'Pengajuan';
    protected static ?int $navigationSort = 1;

    // ✅ hanya karyawan yang bisa create
    public static function canCreate(): bool
    {
        return auth()->check() && auth()->user()->hasRole('karyawan');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Karyawan')
                    ->relationship('user', 'name')
                    ->required()
                    ->default(fn() => auth()->id())
                    ->disabled(fn() => auth()->user()->hasRole('karyawan')),

                Forms\Components\Textarea::make('alasan')
                    ->label('Alasan')
                    ->rows(5)
                    ->cols(20)
                    ->required(),

                Forms\Components\DatePicker::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->required(),

                Forms\Components\DatePicker::make('tanggal_selesai')
                    ->label('Tanggal Selesai')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Karyawan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.nama_perusahaan')
                    ->label('Nama Perusahaan'),

                Tables\Columns\TextColumn::make('alasan')
                    ->label('Alasan')
                    ->limit(30),

                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->label('Mulai')
                    ->date(),

                Tables\Columns\TextColumn::make('tanggal_selesai')
                    ->label('Selesai')
                    ->date(),

                Tables\Columns\TextColumn::make('status_admin')
                    ->badge()
                    ->label('Status Admin')
                    ->colors([
                        'warning' => fn($state) => $state === 'pending',
                        'success' => fn($state) => $state === 'disetujui',
                        'danger'  => fn($state) => $state === 'ditolak',
                    ]),

                Tables\Columns\TextColumn::make('status_superadmin')
                    ->badge()
                    ->label('Status Superadmin')
                    ->colors([
                        'warning' => fn($state) => $state === 'pending',
                        'success' => fn($state) => $state === 'disetujui',
                        'danger'  => fn($state) => $state === 'ditolak',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve_admin')
                    ->label('Setujui')
                    ->color('success')
                    ->visible(fn() => auth()->user()->hasRole('admin'))
                    ->action(fn(Cuti $record) => $record->update(['status_admin' => 'disetujui'])),

                Tables\Actions\Action::make('reject_admin')
                    ->label('Tolak')
                    ->color('danger')
                    ->visible(fn() => auth()->user()->hasRole('admin'))
                    ->action(fn(Cuti $record) => $record->update(['status_admin' => 'ditolak'])),

                Tables\Actions\Action::make('approve_superadmin')
                    ->label('Setujui')
                    ->color('success')
                    ->visible(fn() => auth()->user()->hasRole('superadmin'))
                    ->action(fn(Cuti $record) => $record->update(['status_superadmin' => 'disetujui'])),

                Tables\Actions\Action::make('reject_superadmin')
                    ->label('Tolak')
                    ->color('danger')
                    ->visible(fn() => auth()->user()->hasRole('superadmin'))
                    ->action(fn(Cuti $record) => $record->update(['status_superadmin' => 'ditolak'])),
            ])
            ->emptyStateHeading('Tidak ada Pengajuan yang ditemukan');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCutis::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->check() && auth()->user()->hasRole('karyawan')) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }
}
