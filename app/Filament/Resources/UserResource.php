<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $activeNavigationIcon = 'heroicon-s-user-group';
    protected static ?string $navigationLabel = 'Manajemen User';
    protected static ?string $pluralLabel = 'Manajemen User';
    protected static ?string $navigationGroup = 'Manajemen User';
    protected static ?int $navigationSort = 4;

    // 🔹 Hanya superadmin yang bisa lihat menu di sidebar
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->hasRole('superadmin');
        
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required(),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required(fn($record) => $record === null)
                    ->dehydrateStateUsing(fn($state) => bcrypt($state))
                    ->label('Password'),

                Forms\Components\TextInput::make('no_telepon')
                    ->label('No Telepon')
                    ->required(),

                Forms\Components\TextInput::make('no_karyawan')
                    ->label('No Karyawan')
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('nama_perusahaan')
                    ->label('Nama Perusahaan')
                    ->required(),

                Forms\Components\Select::make('roles')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('nama_perusahaan')->label('Nama Perusahaan'),
                Tables\Columns\TextColumn::make('roles.name')->label('Role'),
            ])
            ->filters([
                // 🔹 Filter berdasarkan Role
                Tables\Filters\SelectFilter::make('roles')
                    ->label('Role')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload(),

                // 🔹 Filter berdasarkan Nama Perusahaan
                Tables\Filters\SelectFilter::make('nama_perusahaan')
                    ->label('Nama Perusahaan')
                    ->options(
                        User::query()
                            ->distinct()
                            ->pluck('nama_perusahaan', 'nama_perusahaan')
                            ->toArray()
                    )
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }

    // 🔹 Batasi akses hanya superadmin
    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('superadmin');
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasRole('superadmin');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->hasRole('superadmin');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->hasRole('superadmin');
    }
}
