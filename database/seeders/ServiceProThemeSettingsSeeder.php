<?php

namespace Database\Seeders;

use App\Domain\Theme\Models\Theme;
use App\Domain\Theme\Models\ThemeSetting;
use Illuminate\Database\Seeder;

class ServiceProThemeSettingsSeeder extends Seeder
{
    public function run(?Theme $theme = null): void
    {
        $theme ??= Theme::query()->where('slug', 'servicepro')->first();

        if ($theme === null) {
            return;
        }

        ThemeSetting::set($theme->id, 'hero', 'title', [
            'id' => 'Solusi Profesional untuk Bisnis Anda',
            'en' => 'Professional Solutions for Your Business',
        ]);

        ThemeSetting::set($theme->id, 'hero', 'subtitle', [
            'id' => 'Kami membantu brand tumbuh dengan layanan terpercaya.',
            'en' => 'We help brands grow with trusted services.',
        ]);

        ThemeSetting::set($theme->id, 'branding', 'primary_color', '#0f766e');

        ThemeSetting::set($theme->id, 'branding', 'font_heading', 'Montserrat');

        ThemeSetting::set($theme->id, 'branding', 'font_body', 'Inter');

        ThemeSetting::set($theme->id, 'blog', 'title', [
            'id' => 'Artikel Terbaru',
            'en' => 'Latest Articles',
        ]);

        ThemeSetting::set($theme->id, 'blog', 'subtitle', [
            'id' => 'Insight dan tips dari tim kami.',
            'en' => 'Insights and tips from our team.',
        ]);
    }
}
