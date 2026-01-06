<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AksesGedungResource\Pages;
use App\Models\AksesGedung;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AksesGedungResource extends Resource
{
    protected static ?string $model = AksesGedung::class;
    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $activeNavigationIcon = 'heroicon-s-key';
    protected static ?string $navigationLabel = 'Pengajuan Akses Gedung';
    protected static ?string $pluralLabel = 'Pengajuan Akses Gedung';
    protected static ?string $navigationGroup = 'Pengajuan';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('user_id')
                ->default(fn() => auth()->id()),

            Forms\Components\TextInput::make('no_kartu')
                ->label('No Kartu')
                ->rules(['required'])
                ->validationMessages([
                    'required' => 'Kolom wajib diisi!',
                ]),

            Forms\Components\Hidden::make('status')
                ->default('pending'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('user.name')
                ->label('Nama')
                ->when(
                        auth()->user()->hasRole(['superadmin']),
                        fn($column) => $column->searchable()
                    ),
            Tables\Columns\TextColumn::make('user.no_karyawan')
                ->label('No Karyawan'),
            Tables\Columns\TextColumn::make('user.nama_perusahaan')
                ->label('Perusahaan'),
            Tables\Columns\TextColumn::make('no_kartu')
                ->label('No Kartu')
                ->alignCenter(),
            Tables\Columns\TextColumn::make('status')
                ->badge()
                ->alignCenter()
                ->colors([
                    'warning' => 'pending',
                    'success' => 'disetujui',
                    'danger'  => 'ditolak',
                ]),
        ])

            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->color('success')
                    ->visible(fn() => auth()->user()->hasRole('superadmin'))
                    ->action(fn(AksesGedung $record) => $record->update(['status' => 'disetujui'])),

                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->color('danger')
                    ->visible(fn() => auth()->user()->hasRole('superadmin'))
                    ->action(fn(AksesGedung $record) => $record->update(['status' => 'ditolak'])),
            ])
            ->emptyStateHeading('Tidak ada Pengajuan yang ditemukan');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ManageAksesGedungs::route('/'),
            'create' => Pages\CreateAksesGedung::route('/create'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Urutkan data TERBARU di atas
        $query->orderByDesc('created_at');

        // membatasi karyawan hanya bisa melihat pengajuannya sendiri
        if (auth()->check() && auth()->user()->hasRole('karyawan')) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }

    // membatasi menu hanya muncul untuk Karyawan & Superadmin
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && (
            auth()->user()->hasRole('karyawan') ||
            auth()->user()->hasRole('superadmin')
        );
    }
}
