<?php

namespace App\Domain\Language\Livewire;

use App\Domain\Language\Models\Language;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class LocaleSwitcher extends Component
{
    /**
     * @return Collection<int, Language>
     */
    public function getLanguagesProperty(): Collection
    {
        return Language::getActive();
    }

    public function switchLocale(string $code): mixed
    {
        $language = Language::query()->active()->where('code', $code)->first();

        if ($language === null) {
            abort(404);
        }

        $segments = request()->segments();

        if (count($segments) > 0 && Language::query()->where('code', $segments[0])->exists()) {
            $segments[0] = $code;
        } else {
            array_unshift($segments, $code);
        }

        session(['locale' => $code]);

        return redirect('/' . implode('/', $segments));
    }

    public function render(): View
    {
        return view('livewire.locale-switcher');
    }
}
