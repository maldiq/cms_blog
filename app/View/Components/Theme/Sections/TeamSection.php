<?php

namespace App\View\Components\Theme\Sections;

use App\Domain\Team\Models\Team;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class TeamSection extends Component
{
    public ?string $title;

    public ?string $subtitle;

    /**
     * @var Collection<int, Team>
     */
    public Collection $teams;

    public function __construct()
    {
        $this->title = theme_locale('team.title');
        $this->subtitle = theme_locale('team.subtitle');

        $this->teams = Team::query()
            ->with(['translations', 'photo'])
            ->active()
            ->ordered()
            ->get();
    }

    public function render(): View
    {
        return view('theme::components.team-section');
    }
}
