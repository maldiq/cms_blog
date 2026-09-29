<?php

namespace App\Domain\Menu\Filament\Resources\MenuResource\RelationManagers;

use App\Domain\Menu\Filament\Concerns\HasTranslatableLabelFields;
use App\Domain\Menu\Models\MenuItem;
use App\Domain\Role\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class MenuItemsRelationManager extends RelationManager
{
    use HasTranslatableLabelFields;

    protected static string $relationship = 'items';

    protected static ?string $title = 'Item Menu';

    protected static ?string $modelLabel = 'Item';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return 'Item Menu';
    }

    public function form(Form $form): Form
    {
        $types = [
            MenuItem::TYPE_LINK => 'Link',
            MenuItem::TYPE_POST => 'Post',
            MenuItem::TYPE_PAGE => 'Halaman',
            MenuItem::TYPE_CATEGORY => 'Kategori',
            MenuItem::TYPE_SERIES => 'Series',
            MenuItem::TYPE_CUSTOM => 'Custom URL',
        ];

        return $form
            ->schema([
                Forms\Components\Section::make('Item')
                    ->schema([
                        ...self::translatableLabelFields(),
                        Forms\Components\Select::make('parent_id')
                            ->label('Parent')
                            ->options(function (): array {
                                $menuId = $this->getOwnerRecord()->getKey();

                                return MenuItem::query()
                                    ->where('menu_id', $menuId)
                                    ->orderBy('sort_order')
                                    ->get()
                                    ->mapWithKeys(fn (MenuItem $item): array => [
                                        $item->id => $item->getTranslation('label', app()->getLocale(), false)
                                            ?: $item->getTranslation('label', 'id', false)
                                            ?: "Item #{$item->id}",
                                    ])
                                    ->all();
                            })
                            ->searchable()
                            ->nullable()
                            ->native(false),
                        Forms\Components\Select::make('type')
                            ->label('Tipe')
                            ->options($types)
                            ->required()
                            ->live()
                            ->native(false),
                        Forms\Components\TextInput::make('target_id')
                            ->label('Target ID')
                            ->numeric()
                            ->visible(fn (Get $get): bool => in_array($get('type'), [
                                MenuItem::TYPE_POST,
                                MenuItem::TYPE_PAGE,
                                MenuItem::TYPE_CATEGORY,
                                MenuItem::TYPE_SERIES,
                            ], true)),
                        Forms\Components\TextInput::make('url')
                            ->label('URL')
                            ->maxLength(2048)
                            ->visible(fn (Get $get): bool => in_array($get('type'), [
                                MenuItem::TYPE_LINK,
                                MenuItem::TYPE_CUSTOM,
                            ], true)),
                        Forms\Components\TextInput::make('icon')
                            ->label('Icon (Heroicon)')
                            ->maxLength(255),
                        Forms\Components\Select::make('target')
                            ->label('Target window')
                            ->options([
                                '_self' => 'Tab sama (_self)',
                                '_blank' => 'Tab baru (_blank)',
                            ])
                            ->default('_self')
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('roles')
                            ->label('Role visibility')
                            ->multiple()
                            ->options(fn (): array => Role::query()->orderBy('name')->pluck('name', 'name')->all())
                            ->helperText('Kosongkan = tampil untuk semua pengguna login yang memenuhi parent.')
                            ->native(false),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Label')
                    ->formatStateUsing(function (MenuItem $record): string {
                        $label = $record->getTranslation('label', app()->getLocale(), false)
                            ?: $record->getTranslation('label', 'id', false)
                            ?: '-';

                        $depth = 0;
                        $parentId = $record->parent_id;

                        while ($parentId !== null) {
                            $depth++;
                            $parentId = MenuItem::query()->whereKey($parentId)->value('parent_id');
                        }

                        return str_repeat('— ', $depth).$label;
                    }),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge(),
                Tables\Columns\TextColumn::make('resolved_url')
                    ->label('URL')
                    ->limit(40),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(fn (array $data): array => self::mergeLabelTranslations($data)),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(fn (array $data): array => self::mergeLabelTranslations($data)),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
