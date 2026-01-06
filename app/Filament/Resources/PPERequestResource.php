<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\PPERequest;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PPERequestResource\Pages;

class PPERequestResource extends Resource
{
    protected static ?string $model = PPERequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $activeNavigationIcon = 'heroicon-s-shield-check';
    protected static ?string $navigationLabel = 'Pengajuan Penukaran PPE';
    protected static ?string $pluralLabel = 'Pengajuan Penukaran PPE';
    protected static ?string $navigationGroup = 'Pengajuan';
    protected static ?int $navigationSort = 3;

    // ⛔ hanya superadmin & karyawan
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['superadmin', 'karyawan']);
    }

    // ⛔ Hanya karyawan yang bisa create
    public static function canCreate(): bool
    {
        return auth()->check() && auth()->user()->hasRole('karyawan');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('user_id')
                ->default(fn() => auth()->id()),

            Forms\Components\TextInput::make('nama_barang')
                ->label('Nama Barang')
                ->rules(['required'])
                ->validationMessages([
                    'required' => 'Kolom wajib diisi!',
                ]),

            Forms\Components\FileUpload::make('foto_barang')
                ->label('Foto Barang')
                ->disk('public') // 🔹 gunakan disk public
                ->visibility('public') // 🔹 pastikan bisa diakses langsung
                ->directory('ppe-requests')
                ->image()
                ->imagePreviewHeight('100') // 🔹 thumbnail kecil biar cepat
                ->multiple() // bisa upload banyak gambar
                ->openable() // klik untuk lihat ukuran asli
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
                ->label('Nama Karyawan')
                ->when(
                        auth()->user()->hasRole(['superadmin']),
                        fn($column) => $column->searchable()
                    ),
            Tables\Columns\TextColumn::make('nama_barang')
                ->label('Nama Barang'),
            Tables\Columns\ImageColumn::make('foto_barang')
                ->label('Foto Barang')
                ->alignCenter()
                ->circular()
                ->stacked()
                ->limit(3)
                ->limitedRemainingText('+{count} foto lagi'),

            Tables\Columns\TextColumn::make('status')
                ->badge()
                ->alignCenter()
                ->colors([
                    'warning' => 'pending',
                    'success' => 'disetujui',
                    'danger' => 'ditolak',
                ]),
        ])

            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->color('success')
                    ->visible(fn() => auth()->user()->hasRole('superadmin'))
                    ->action(fn(PPERequest $record) => $record->update(['status' => 'disetujui'])),

                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->color('danger')
                    ->visible(fn() => auth()->user()->hasRole('superadmin'))
                    ->action(fn(PPERequest $record) => $record->update(['status' => 'ditolak'])),

                Tables\Actions\ViewAction::make(),

                Tables\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->modalHeading('Hapus Pengajuan')
                    ->modalDescription('Apakah Anda yakin ingin menghapus pengajuan ini?')
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->successNotificationTitle('Pengajuan berhasil dihapus!')
                    ->visible(fn() => auth()->user()->hasRole('superadmin')),
            ])
            ->emptyStateHeading('Tidak ada Pengajuan yang ditemukan');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePPERequests::route('/'),
            'create' => Pages\CreatePPERequest::route('/create'),
            'view' => Pages\ViewPPERequest::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Urutkan data TERBARU di atas
        $query->orderByDesc('created_at');

        // Karyawan hanya bisa lihat miliknya
        if (auth()->check() && auth()->user()->hasRole('karyawan')) {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }
}
