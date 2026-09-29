<?php

namespace App\Domain\Team\Models;

use App\Domain\Blog\Support\Concerns\GeneratesTranslationSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamTranslation extends Model
{
    use GeneratesTranslationSlug;

    protected string $slugSource = 'name';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'team_id',
        'locale',
        'name',
        'slug',
        'position',
        'bio',
    ];

    /**
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
