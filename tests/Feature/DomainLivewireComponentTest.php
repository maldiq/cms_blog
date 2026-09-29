<?php

namespace Tests\Feature;

use App\Domain\Comment\Livewire\CommentForm;
use Database\Seeders\LanguageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Mechanisms\ComponentRegistry;
use Tests\TestCase;

class DomainLivewireComponentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LanguageSeeder::class);
    }

    public function test_registry_resolves_domain_livewire_snapshot_names(): void
    {
        $registry = app(ComponentRegistry::class);

        $this->assertSame(
            CommentForm::class,
            $registry->getClass('app.domain.comment.livewire.comment-form'),
        );
    }

}
