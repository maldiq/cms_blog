<?php

namespace App\Domain\Affiliate\Filament\Pages;

use App\Domain\Affiliate\Filament\Resources\AffiliateTrafficSourceResource;
use App\Domain\Affiliate\Models\AffiliateProduct;
use App\Domain\Affiliate\Models\AffiliateTrafficSource;
use App\Domain\Affiliate\Services\TrafficSource;
use App\Domain\Language\Models\Language;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class AffiliateLinkBuilder extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationGroup = 'Affiliate';

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationLabel = 'Link Builder';

    protected static ?string $title = 'Link Builder Sumber Traffic';

    protected static ?string $slug = 'affiliate-link-builder';

    protected static string $view = 'filament.pages.affiliate-link-builder';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->can('affiliate.link-builder.view') ?? false;
    }

    public function mount(): void
    {
        $this->form->fill([
            'locale' => Language::getDefault()?->code ?? 'id',
            'target' => 'home',
            'source' => 'fb_grup_gadget',
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('manageSources')
                ->label('Kelola sumber')
                ->icon('heroicon-o-tag')
                ->url(AffiliateTrafficSourceResource::getUrl('index')),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('saved_source')
                    ->label('Sumber tersimpan')
                    ->options(fn (): array => AffiliateTrafficSource::codeOptions())
                    ->searchable()
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(function (?string $state, Set $set): void {
                        if (filled($state)) {
                            $set('source', $state);
                        }
                    })
                    ->dehydrated(false)
                    ->helperText('Pilih untuk mengisi kode di bawah, atau ketik kode baru untuk kampanye sekali pakai.'),
                Forms\Components\TextInput::make('source')
                    ->label('Kode sumber (src)')
                    ->required()
                    ->regex('/^[a-z0-9_-]+$/')
                    ->maxLength(64)
                    ->helperText('Huruf kecil, angka, garis bawah, atau tanda hubung. Contoh: fb_grup_gadget.'),
                Forms\Components\Select::make('locale')
                    ->label('Bahasa')
                    ->required()
                    ->options(fn (): array => Language::getActive()->pluck('code', 'code')->all()),
                Forms\Components\Select::make('target')
                    ->label('Tujuan link')
                    ->required()
                    ->live()
                    ->options([
                        'home' => 'Beranda',
                        'catalog' => 'Katalog semua produk',
                        'product' => 'Halaman detail produk',
                        'smart_link' => 'Smart link /go/{slug}',
                    ])
                    ->default('home'),
                Forms\Components\Select::make('product_id')
                    ->label('Produk')
                    ->searchable()
                    ->options(fn (): array => AffiliateProduct::query()
                        ->with('translations')
                        ->orderByDesc('created_at')
                        ->limit(200)
                        ->get()
                        ->mapWithKeys(fn (AffiliateProduct $product): array => [
                            $product->getKey() => (string) (
                                $product->translate('id', false)?->title
                                ?? $product->translate('en', false)?->title
                                ?? '#'.$product->getKey()
                            ),
                        ])
                        ->all())
                    ->visible(fn (Forms\Get $get): bool => in_array($get('target'), ['product', 'smart_link'], true))
                    ->required(fn (Forms\Get $get): bool => in_array($get('target'), ['product', 'smart_link'], true)),
                Forms\Components\TextInput::make('generated_url')
                    ->label('URL siap copy')
                    ->readOnly()
                    ->columnSpanFull(),
            ])
            ->columns(2)
            ->statePath('data');
    }

    public function generate(): void
    {
        $state = $this->form->getState();

        $source = app(TrafficSource::class)->normalize($state['source'] ?? null);

        if ($source === null) {
            Notification::make()
                ->title('Sumber tidak valid')
                ->body('Gunakan huruf kecil, angka, garis bawah, atau tanda hubung.')
                ->danger()
                ->send();

            return;
        }

        $locale = (string) ($state['locale'] ?? Language::getDefault()?->code ?? 'id');
        $target = (string) ($state['target'] ?? 'home');
        $product = null;

        if (in_array($target, ['product', 'smart_link'], true)) {
            $product = AffiliateProduct::query()->find($state['product_id'] ?? null);

            if ($product === null) {
                Notification::make()
                    ->title('Produk tidak ditemukan')
                    ->danger()
                    ->send();

                return;
            }
        }

        $url = match ($target) {
            'catalog' => route('products.index', ['locale' => $locale]),
            'product' => $this->productUrl($product, $locale, 'products.show'),
            'smart_link' => $this->productUrl($product, $locale, 'products.redirect'),
            default => route('home', ['locale' => $locale]),
        };

        $separator = str_contains($url, '?') ? '&' : '?';
        $this->data['generated_url'] = $url.$separator.'src='.rawurlencode($source);

        Notification::make()
            ->title('Link dibuat')
            ->success()
            ->send();
    }

    private function productUrl(AffiliateProduct $product, string $locale, string $route): string
    {
        $slug = $product->translate($locale, false)?->slug
            ?? $product->translate('id', false)?->slug
            ?? $product->translate('en', false)?->slug;

        if (blank($slug)) {
            return route('products.index', ['locale' => $locale]);
        }

        return route($route, ['locale' => $locale, 'slug' => $slug]);
    }
}
