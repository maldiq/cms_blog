<?php

namespace App\Domain\Affiliate\Services;

use App\Domain\Affiliate\Models\AffiliateProduct;
use App\Domain\Media\Services\MediaUploadService;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;
use Throwable;

/**
 * Impor massal tautan affiliate (dan opsional gambar) dari file CSV.
 *
 * Kolom wajib: slug, affiliate_url.
 * Kolom opsional: image_url, price, original_price.
 */
class AffiliateProductLinkImporter
{
    public function __construct(
        private readonly MediaUploadService $media,
    ) {}

    /**
     * @return array{updated: int, images: int, skipped: int, errors: list<string>}
     */
    public function importFile(string $path, bool $downloadImages = false): array
    {
        if (! is_file($path) || ! is_readable($path)) {
            throw new InvalidArgumentException('File tidak ditemukan atau tidak bisa dibaca: '.$path);
        }

        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new InvalidArgumentException('File tidak bisa dibuka: '.$path);
        }

        $delimiter = $this->detectDelimiter($path);
        $header = fgetcsv($handle, null, $delimiter, '"', '');

        if ($header === false) {
            fclose($handle);

            throw new InvalidArgumentException('File CSV kosong.');
        }

        $header = array_map(fn (mixed $column): string => $this->normalizeHeader((string) $column), $header);
        $missing = array_diff(['slug', 'affiliate_url'], $header);

        if ($missing !== []) {
            fclose($handle);

            throw new InvalidArgumentException('Kolom wajib tidak ditemukan: '.implode(', ', $missing).'.');
        }

        $rows = [];
        $line = 1;

        while (($data = fgetcsv($handle, null, $delimiter, '"', '')) !== false) {
            $line++;

            if ($data === [null] || implode('', array_map('trim', $data)) === '') {
                continue;
            }

            $rows[] = ['__line' => $line] + $this->combine($header, $data);
        }

        fclose($handle);

        return $this->importRows($rows, $downloadImages);
    }

    /**
     * @param  iterable<int, array<string, mixed>>  $rows
     * @return array{updated: int, images: int, skipped: int, errors: list<string>}
     */
    public function importRows(iterable $rows, bool $downloadImages = false): array
    {
        $summary = [
            'updated' => 0,
            'images' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        $index = 0;

        foreach ($rows as $row) {
            $index++;
            $line = (int) ($row['__line'] ?? ($index + 1));

            $slug = trim((string) ($row['slug'] ?? ''));
            $url = trim((string) ($row['affiliate_url'] ?? ''));

            if ($slug === '') {
                $summary['skipped']++;
                $summary['errors'][] = "Baris {$line}: kolom slug kosong.";

                continue;
            }

            if (! $this->isHttpUrl($url)) {
                $summary['skipped']++;
                $summary['errors'][] = "Baris {$line}: link affiliate tidak valid (harus http/https).";

                continue;
            }

            $product = $this->findProduct($slug);

            if ($product === null) {
                $summary['skipped']++;
                $summary['errors'][] = "Baris {$line}: produk dengan slug \"{$slug}\" tidak ditemukan.";

                continue;
            }

            $attributes = ['affiliate_url' => $url];

            $price = $this->parsePrice($row['price'] ?? null);

            if ($price !== null) {
                $attributes['price'] = $price;
            }

            $originalPrice = $this->parsePrice($row['original_price'] ?? null);

            if ($originalPrice !== null) {
                $attributes['original_price'] = $originalPrice;
            }

            $product->update($attributes);
            $summary['updated']++;

            $imageUrl = trim((string) ($row['image_url'] ?? ''));

            if (! $downloadImages || $imageUrl === '') {
                continue;
            }

            try {
                $media = $this->media->uploadFromUrl($imageUrl);
                $product->update(['image_id' => $media->getKey()]);
                $summary['images']++;
            } catch (Throwable $exception) {
                $summary['errors'][] = "Baris {$line}: link tersimpan, gambar gagal (".$exception->getMessage().').';
            }
        }

        return $summary;
    }

    private function findProduct(string $slug): ?AffiliateProduct
    {
        return AffiliateProduct::query()
            ->whereHas('translations', fn (Builder $query): Builder => $query->where('slug', $slug))
            ->first();
    }

    private function isHttpUrl(string $url): bool
    {
        if ($url === '' || filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        return in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true);
    }

    private function parsePrice(mixed $value): ?float
    {
        if ($value === null) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', (string) $value);

        return $digits === '' || $digits === null ? null : (float) $digits;
    }

    private function detectDelimiter(string $path): string
    {
        $handle = fopen($path, 'rb');
        $firstLine = $handle === false ? '' : (string) fgets($handle);

        if ($handle !== false) {
            fclose($handle);
        }

        $counts = [
            ',' => substr_count($firstLine, ','),
            ';' => substr_count($firstLine, ';'),
            "\t" => substr_count($firstLine, "\t"),
        ];

        arsort($counts);

        return (string) array_key_first($counts);
    }

    /**
     * @param  list<string>  $header
     * @param  list<string|null>  $data
     * @return array<string, string|null>
     */
    private function combine(array $header, array $data): array
    {
        $data = array_pad($data, count($header), null);

        return array_combine($header, array_slice($data, 0, count($header))) ?: [];
    }

    private function normalizeHeader(string $header): string
    {
        $header = preg_replace('/^\xEF\xBB\xBF/', '', $header) ?? $header;
        $header = strtolower(trim($header));
        $header = str_replace([' ', '-'], '_', $header);

        return match ($header) {
            'produk_slug', 'product_slug', 'slug_produk' => 'slug',
            'link', 'url', 'tautan', 'link_affiliate', 'affiliate_link' => 'affiliate_url',
            'gambar', 'image', 'foto', 'image_link' => 'image_url',
            'harga' => 'price',
            'harga_awal', 'harga_sebelum_diskon', 'original' => 'original_price',
            default => $header,
        };
    }
}
