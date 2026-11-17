<?php

namespace App\Domain\Affiliate\Filament\Pages;

use App\Domain\Affiliate\Models\AffiliateProduct;
use App\Domain\Affiliate\Services\AffiliateProductLinkImporter;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class AffiliateCatalogImport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static ?string $navigationGroup = 'Affiliate';

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationLabel = 'Impor Link Produk';

    protected static ?string $title = 'Impor Link & Gambar Produk';

    protected static ?string $slug = 'affiliate-import';

    protected static string $view = 'filament.pages.affiliate-catalog-import';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    /**
     * @var array{updated: int, images: int, skipped: int, errors: list<string>}|null
     */
    public ?array $lastResult = null;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user !== null
            && ($user->can('affiliate.product.update') || $user->hasAnyRole(['super-admin', 'admin']));
    }

    public function mount(): void
    {
        $this->form->fill([
            'download_images' => true,
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('downloadTemplate')
                ->label('Unduh template CSV')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->action(fn () => $this->downloadTemplate()),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('file')
                    ->label('File CSV')
                    ->disk('local')
                    ->directory('affiliate-imports')
                    ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel'])
                    ->maxSize(5120)
                    ->required()
                    ->helperText('Kolom wajib: slug dan affiliate_url. Opsional: image_url, price, original_price.'),
                Forms\Components\Toggle::make('download_images')
                    ->label('Unduh gambar dari kolom image_url')
                    ->helperText('Gambar disalin ke perpustakaan media. Kosongkan jika hanya ingin memperbarui link.')
                    ->default(true),
            ])
            ->statePath('data');
    }

    public function import(): void
    {
        $state = $this->form->getState();
        $disk = Storage::disk('local');
        $relativePath = (string) ($state['file'] ?? '');

        if ($relativePath === '') {
            Notification::make()->title('Pilih file CSV dulu')->warning()->send();

            return;
        }

        try {
            $result = app(AffiliateProductLinkImporter::class)->importFile(
                $disk->path($relativePath),
                (bool) ($state['download_images'] ?? true),
            );
        } catch (InvalidArgumentException $exception) {
            Notification::make()
                ->title('Impor gagal')
                ->body($exception->getMessage())
                ->danger()
                ->send();

            return;
        } finally {
            if ($disk->exists($relativePath)) {
                $disk->delete($relativePath);
            }
        }

        $this->lastResult = $result;

        $notification = Notification::make()
            ->title(sprintf('%d link diperbarui, %d gambar diunduh', $result['updated'], $result['images']));

        if ($result['errors'] !== []) {
            $notification
                ->body(sprintf('%d baris dilewati atau bermasalah. Lihat detail di bawah.', $result['skipped']))
                ->warning();
        } else {
            $notification->success();
        }

        $notification->send();

        $this->form->fill(['download_images' => true]);
    }

    private function downloadTemplate(): mixed
    {
        $rows = AffiliateProduct::query()
            ->where('is_active', true)
            ->with('translations')
            ->orderBy('id')
            ->get()
            ->map(function (AffiliateProduct $product): array {
                $slug = $product->translate('id', false)?->slug
                    ?? $product->translate('en', false)?->slug
                    ?? '';

                return [
                    $slug,
                    $product->usesPlaceholderSearchLink() ? '' : (string) $product->affiliate_url,
                    '',
                    $product->price === null ? '' : (int) $product->price,
                    $product->original_price === null ? '' : (int) $product->original_price,
                ];
            })
            ->all();

        return response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fputcsv($handle, ['slug', 'affiliate_url', 'image_url', 'price', 'original_price']);

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, 'template-link-produk.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
