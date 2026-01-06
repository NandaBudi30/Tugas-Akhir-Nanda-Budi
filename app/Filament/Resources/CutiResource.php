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
use Filament\Tables\Actions\ViewAction;
use Carbon\Carbon;


class CutiResource extends Resource
{
    protected static ?string $model = Cuti::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';
    protected static ?string $activeNavigationIcon = 'heroicon-s-calendar-date-range';
    protected static ?string $navigationLabel = 'Pengajuan Cuti';
    protected static ?string $pluralLabel = 'Pengajuan Cuti';
    protected static ?string $navigationGroup = 'Pengajuan';
    protected static ?int $navigationSort = 1;

    //  hanya karyawan yang bisa create
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
                    ->rules(['required'])
                    ->validationMessages([
                        'required' => 'Kolom wajib diisi!',
                    ]),

                Forms\Components\DatePicker::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->rules(['required'])
                    ->reactive()
                    ->minDate(now()->addDay()->toDateString()) // H+1
                    ->rule('after:today')
                    ->validationMessages([
                        'required' => 'Kolom wajib diisi!',
                    ]),
                    

                Forms\Components\DatePicker::make('tanggal_selesai')
                    ->label('Tanggal Selesai')
                    ->rules(['required'])
                    ->reactive()
                    ->validationMessages([
                        'required' => 'Kolom wajib diisi!',
                    ])
                    

                    // Minimal = tanggal mulai
                    ->minDate(fn(callable $get) => $get('tanggal_mulai'))

                    // Maksimal = tanggal mulai + 6 hari (total 7 hari)
                    ->maxDate(function (callable $get) {
                        $mulai = $get('tanggal_mulai');

                        if (! filled($mulai)) {
                            return null;
                        }

                        return Carbon::createFromFormat('Y-m-d', $mulai)
                            ->addDays(6)
                            ->format('Y-m-d');
                    })

                    // Validasi backend (ANTI BYPASS + ANTI ERROR)
                    ->rule(function (callable $get) {
                        return function ($attribute, $value, $fail) use ($get) {

                            $mulai = $get('tanggal_mulai');

                            // 🛑 Cegah error Livewire / mountedActionsData
                            if (
                                ! filled($mulai) ||
                                ! filled($value) ||
                                ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)
                            ) {
                                return;
                            }

                            $start = Carbon::createFromFormat('Y-m-d', $mulai);
                            $end   = Carbon::createFromFormat('Y-m-d', $value);

                            if ($end->lt($start)) {
                                $fail('Tanggal selesai tidak boleh lebih kecil dari tanggal mulai.');
                                return;
                            }

                            if ($start->diffInDays($end) + 1 > 7) {
                                $fail('Pengajuan cuti maksimal 7 hari.');
                            }
                        };
                    }),



            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Karyawan')
                    ->when(
                        auth()->user()->hasRole(['admin', 'superadmin']),
                        fn($column) => $column->searchable()
                    ),

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
                    ->alignCenter()
                    ->label('Status Admin')
                    ->colors([
                        'warning' => fn($state) => $state === 'pending',
                        'success' => fn($state) => $state === 'disetujui',
                        'danger'  => fn($state) => $state === 'ditolak',
                    ]),

                Tables\Columns\TextColumn::make('status_superadmin')
                    ->badge()
                    ->alignCenter()
                    ->label('Status Superadmin')
                    ->colors([
                        'warning' => fn($state) => $state === 'pending',
                        'success' => fn($state) => $state === 'disetujui',
                        'danger'  => fn($state) => $state === 'ditolak',
                    ]),
            ])

            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('approve_admin')
                    ->label('Setujui')
                    ->color('success')
                    ->visible(fn() => auth()->user()->hasRole('admin'))
                    ->action(function (Cuti $record) {
                        $record->update([
                            'status_admin'      => 'disetujui',
                            'status_superadmin' => 'pending',
                        ]);
                    }),

                Tables\Actions\Action::make('reject_admin')
                    ->label('Tolak')
                    ->color('danger')
                    ->visible(fn() => auth()->user()->hasRole('admin'))
                    ->action(function (Cuti $record) {
                        $record->update([
                            'status_admin'      => 'ditolak',
                            'status_superadmin' => 'ditolak',
                        ]);
                    }),

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
            'view' => Pages\ViewCuti::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Urutkan data TERBARU di atas
        $query->orderByDesc('created_at');

        if (! auth()->check()) {
            return $query;
        }

        $user = auth()->user();

        // Karyawan hanya lihat miliknya
        if ($user->hasRole('karyawan')) {
            $query->where('user_id', $user->id);
        }

        // Superadmin hanya lihat yang SUDAH DISETUJUI admin
        if ($user->hasRole('superadmin')) {
            $query->where('status_admin', 'disetujui');
        }

        return $query;
    }
}
