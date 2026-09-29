<?php

namespace Database\Seeders;

use App\Domain\Menu\Models\Menu;
use App\Domain\Menu\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Menu dummy navigasi header (Indonesia + English).
     */
    public function run(): void
    {
        $menu = Menu::query()->updateOrCreate(
            ['location' => 'header', 'is_active' => true],
            ['name' => 'Menu Header Utama']
        );

        MenuItem::query()->where('menu_id', $menu->id)->delete();

        $items = [
            [
                'sort_order' => 1,
                'label' => ['id' => 'Beranda', 'en' => 'Home'],
                'url' => '/id',
                'children' => [],
            ],
            [
                'sort_order' => 2,
                'label' => ['id' => 'Portfolio', 'en' => 'Portfolio'],
                'url' => '/id/portfolio',
                'children' => [],
            ],
            [
                'sort_order' => 3,
                'label' => ['id' => 'Layanan', 'en' => 'Services'],
                'url' => '#layanan',
                'children' => [
                    [
                        'sort_order' => 1,
                        'label' => ['id' => 'Backup', 'en' => 'Backup'],
                        'url' => '/id/layanan/backup',
                    ],
                    [
                        'sort_order' => 2,
                        'label' => ['id' => 'Jasa pembuatan website', 'en' => 'Website development'],
                        'url' => '/id/layanan/pembuatan-website',
                    ],
                    [
                        'sort_order' => 3,
                        'label' => ['id' => 'Jasa perbaikan website', 'en' => 'Website maintenance'],
                        'url' => '/id/layanan/perbaikan-website',
                    ],
                    [
                        'sort_order' => 4,
                        'label' => ['id' => 'Install server', 'en' => 'Server installation'],
                        'url' => '/id/layanan/install-server',
                    ],
                ],
            ],
            [
                'sort_order' => 4,
                'label' => ['id' => 'Tentang kami', 'en' => 'About us'],
                'url' => '/id/tentang-kami',
                'children' => [],
            ],
            [
                'sort_order' => 5,
                'label' => ['id' => 'Kontak kami', 'en' => 'Contact us'],
                'url' => '/id/kontak',
                'children' => [],
            ],
        ];

        foreach ($items as $itemData) {
            $this->createItem($menu->id, $itemData);
        }
    }

    /**
     * @param  array{sort_order: int, label: array<string, string>, url: string, children?: list<array{sort_order: int, label: array<string, string>, url: string}>}  $data
     */
    protected function createItem(int $menuId, array $data, ?int $parentId = null): void
    {
        $item = MenuItem::query()->create([
            'menu_id' => $menuId,
            'parent_id' => $parentId,
            'type' => MenuItem::TYPE_LINK,
            'target_id' => null,
            'url' => $data['url'],
            'label' => $data['label'],
            'icon' => null,
            'target' => '_self',
            'roles' => null,
            'sort_order' => $data['sort_order'],
            'is_active' => true,
        ]);

        foreach ($data['children'] ?? [] as $child) {
            $this->createItem($menuId, $child, $item->id);
        }
    }
}
