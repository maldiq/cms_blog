<?php

namespace App\Domain\Role\Filament\Resources;

use App\Domain\Role\Filament\Resources\RoleResource\Pages;
use App\Domain\Role\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Permission;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Pengguna';

    protected static ?string $modelLabel = 'Role';

    protected static ?string $pluralModelLabel = 'Role';

    /**
     * @var list<string>
     */
    protected static array $permissionPrefixes = ['user', 'role', 'setting'];

    public static function form(Form $form): Form
    {
        $permissionFields = [];

        foreach (self::$permissionPrefixes as $prefix) {
            $permissionFields[] = Forms\Components\Fieldset::make(strtoupper($prefix))
                ->schema([
                    Forms\Components\CheckboxList::make("permissions_{$prefix}")
                        ->hiddenLabel()
                        ->options(
                            Permission::query()
                                ->where('name', 'like', "{$prefix}.%")
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all()
                        )
                        ->columns(2)
                        ->bulkToggleable(),
                ]);
        }

        return $form
            ->schema([
                Forms\Components\Section::make('Role')
                    ->schema([
                        Forms\Components\Hidden::make('guard_name')
                            ->default('web'),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        ...$permissionFields,
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->label('Jumlah Permission')
                    ->counts('permissions')
                    ->sortable(),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Jumlah User')
                    ->counts('users')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
