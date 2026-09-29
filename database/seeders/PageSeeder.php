<?php

namespace Database\Seeders;

use App\Domain\Page\Models\Page;
use App\Domain\Page\Models\PageTranslation;
use App\Domain\User\Models\User;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->first();

        if ($user === null) {
            return;
        }

        if (PageTranslation::query()->where('locale', 'id')->where('slug', 'about')->exists()) {
            return;
        }

        $pages = [
            [
                'template' => 'default',
                'id' => [
                    'title' => 'Tentang Kami',
                    'slug' => 'about',
                    'content' => '<p>Kami membangun CMS Blog modular dengan Laravel dan Filament.</p>',
                    'meta_title' => 'Tentang Kami',
                ],
                'en' => [
                    'title' => 'About Us',
                    'slug' => 'about',
                    'content' => '<p>We build a modular CMS Blog with Laravel and Filament.</p>',
                    'meta_title' => 'About Us',
                ],
            ],
            [
                'template' => 'contact',
                'id' => [
                    'title' => 'Kontak',
                    'slug' => 'contact',
                    'content' => '<p>Email: hello@example.com<br>Telepon: +62 812-0000-0000</p>',
                ],
                'en' => [
                    'title' => 'Contact',
                    'slug' => 'contact',
                    'content' => '<p>Email: hello@example.com<br>Phone: +62 812-0000-0000</p>',
                ],
            ],
            [
                'template' => 'default',
                'id' => [
                    'title' => 'Kebijakan Privasi',
                    'slug' => 'privacy-policy',
                    'content' => '<p>Privasi pengguna adalah prioritas kami.</p>',
                ],
                'en' => [
                    'title' => 'Privacy Policy',
                    'slug' => 'privacy-policy',
                    'content' => '<p>User privacy is our priority.</p>',
                ],
            ],
        ];

        foreach ($pages as $definition) {
            $page = Page::query()->create([
                'user_id' => $user->id,
                'template' => $definition['template'],
                'is_homepage' => false,
                'status' => Page::STATUS_PUBLISHED,
                'published_at' => now(),
            ]);

            $page->translateOrNew('id')->fill($definition['id'])->save();
            $page->translateOrNew('en')->fill($definition['en'])->save();
        }
    }
}
