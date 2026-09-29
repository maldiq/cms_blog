<?php

namespace App\Domain\Theme\Observers;

use App\Domain\Theme\Models\ThemeSetting;
use App\Support\Theme\HomeSectionCache;

class ThemeSettingObserver
{
    public function saved(ThemeSetting $themeSetting): void
    {
        $this->clearSectionCache($themeSetting);
    }

    public function deleted(ThemeSetting $themeSetting): void
    {
        $this->clearSectionCache($themeSetting);
    }

    protected function clearSectionCache(ThemeSetting $themeSetting): void
    {
        if (! in_array($themeSetting->group, ['stats', 'pricing', 'faq'], true)) {
            return;
        }

        HomeSectionCache::forgetAll();
    }
}
