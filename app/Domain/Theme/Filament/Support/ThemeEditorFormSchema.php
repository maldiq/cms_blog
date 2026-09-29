<?php

namespace App\Domain\Theme\Filament\Support;

use App\Domain\Theme\Filament\Concerns\InteractsWithThemeLocaleTabs;
use App\Domain\Theme\Filament\Forms\Components\MediaPickerField;
use Filament\Forms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;

class ThemeEditorFormSchema
{
    use InteractsWithThemeLocaleTabs;

    /**
     * @return list<string>
     */
    public static function settingsGroups(): array
    {
        return [
            'hero',
            'about',
            'services',
            'portfolio',
            'team',
            'testimonial',
            'process',
            'pricing',
            'faq',
            'blog',
            'cta',
            'stats',
            'contact',
            'newsletter',
            'footer',
            'branding',
        ];
    }

    /**
     * @return list<Tab>
     */
    public function tabs(): array
    {
        return [
            Tab::make('hero')->label('Hero')->schema($this->heroSchema()),
            Tab::make('about')->label('About')->schema($this->aboutSchema()),
            Tab::make('services')->label('Services')->schema($this->servicesSchema()),
            Tab::make('portfolio')->label('Portfolio')->schema($this->portfolioSchema()),
            Tab::make('team')->label('Team')->schema($this->teamSchema()),
            Tab::make('testimonial')->label('Testimonial')->schema($this->testimonialSchema()),
            Tab::make('process')->label('Process')->schema($this->processSchema()),
            Tab::make('pricing')->label('Pricing')->schema($this->pricingSchema()),
            Tab::make('faq')->label('FAQ')->schema($this->faqSchema()),
            Tab::make('blog')->label('Blog')->schema($this->blogSchema()),
            Tab::make('cta')->label('CTA')->schema($this->ctaSchema()),
            Tab::make('stats')->label('Stats')->schema($this->statsSchema()),
            Tab::make('contact')->label('Contact')->schema($this->contactSchema()),
            Tab::make('newsletter')->label('Newsletter')->schema($this->newsletterSchema()),
            Tab::make('footer')->label('Footer')->schema($this->footerSchema()),
            Tab::make('branding')->label('Branding')->schema($this->brandingSchema()),
        ];
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected function heroSchema(): array
    {
        return [
            $this->themeLocaleTabs('hero', fn (string $locale): array => [
                Forms\Components\TextInput::make("hero.title.{$locale}")->label('Title')->maxLength(255),
                Forms\Components\TextInput::make("hero.subtitle.{$locale}")->label('Subtitle')->maxLength(500),
                Forms\Components\TextInput::make("hero.cta_label.{$locale}")->label('CTA label')->maxLength(255),
            ]),
            Forms\Components\TextInput::make('hero.cta_url')->label('CTA URL')->url()->maxLength(500),
            MediaPickerField::make('hero.background_image_id')->label('Background image'),
            Forms\Components\Repeater::make('hero.features')
                ->label('Features')
                ->schema([
                    Forms\Components\TextInput::make('icon')->label('Icon')->maxLength(100),
                    $this->themeLocaleTabs('hero_feature', fn (string $locale): array => [
                        Forms\Components\TextInput::make("title.{$locale}")->label('Title')->maxLength(255),
                        Forms\Components\Textarea::make("description.{$locale}")->label('Description')->rows(2),
                    ]),
                ])
                ->columnSpanFull()
                ->defaultItems(0)
                ->collapsible(),
        ];
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected function aboutSchema(): array
    {
        return [
            $this->themeLocaleTabs('about', fn (string $locale): array => [
                Forms\Components\TextInput::make("about.title.{$locale}")->label('Title')->maxLength(255),
                Forms\Components\TextInput::make("about.subtitle.{$locale}")->label('Subtitle')->maxLength(500),
                Forms\Components\RichEditor::make("about.content.{$locale}")->label('Content')->columnSpanFull(),
            ]),
            MediaPickerField::make('about.image_id')->label('Image'),
            Forms\Components\Repeater::make('about.points')
                ->label('Points')
                ->schema([
                    $this->themeLocaleTabs('about_point', fn (string $locale): array => [
                        Forms\Components\TextInput::make("text.{$locale}")->label('Text')->maxLength(500),
                    ]),
                ])
                ->defaultItems(0)
                ->collapsible()
                ->columnSpanFull(),
        ];
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected function titleSubtitleCtaSchema(string $group): array
    {
        return [
            $this->themeLocaleTabs($group, fn (string $locale): array => [
                Forms\Components\TextInput::make("{$group}.title.{$locale}")->label('Title')->maxLength(255),
                Forms\Components\TextInput::make("{$group}.subtitle.{$locale}")->label('Subtitle')->maxLength(500),
                Forms\Components\TextInput::make("{$group}.cta_label.{$locale}")->label('CTA label')->maxLength(255),
            ]),
        ];
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected function titleSubtitleSchema(string $group): array
    {
        return [
            $this->themeLocaleTabs($group, fn (string $locale): array => [
                Forms\Components\TextInput::make("{$group}.title.{$locale}")->label('Title')->maxLength(255),
                Forms\Components\TextInput::make("{$group}.subtitle.{$locale}")->label('Subtitle')->maxLength(500),
            ]),
        ];
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected function servicesSchema(): array
    {
        return $this->titleSubtitleCtaSchema('services');
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected function portfolioSchema(): array
    {
        return $this->titleSubtitleCtaSchema('portfolio');
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected function teamSchema(): array
    {
        return $this->titleSubtitleSchema('team');
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected function testimonialSchema(): array
    {
        return $this->titleSubtitleSchema('testimonial');
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected function processSchema(): array
    {
        return [
            ...$this->titleSubtitleSchema('process'),
            Forms\Components\Repeater::make('process.steps')
                ->label('Steps')
                ->schema([
                    Forms\Components\TextInput::make('number')->label('Number')->maxLength(20),
                    $this->themeLocaleTabs('process_step', fn (string $locale): array => [
                        Forms\Components\TextInput::make("title.{$locale}")->label('Title')->maxLength(255),
                        Forms\Components\Textarea::make("description.{$locale}")->label('Description')->rows(2),
                    ]),
                ])
                ->defaultItems(0)
                ->collapsible()
                ->columnSpanFull(),
        ];
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected function pricingSchema(): array
    {
        return [
            ...$this->titleSubtitleSchema('pricing'),
            Forms\Components\Repeater::make('pricing.plans')
                ->label('Plans')
                ->schema([
                    $this->themeLocaleTabs('pricing_plan', fn (string $locale): array => [
                        Forms\Components\TextInput::make("name.{$locale}")->label('Name')->maxLength(255),
                        Forms\Components\TextInput::make("cta_label.{$locale}")->label('CTA label')->maxLength(255),
                    ]),
                    Forms\Components\TextInput::make('price')->label('Price')->maxLength(50),
                    Forms\Components\TextInput::make('period')->label('Period')->maxLength(50),
                    Forms\Components\TextInput::make('cta_url')->label('CTA URL')->url()->maxLength(500),
                    Forms\Components\Toggle::make('is_popular')->label('Popular'),
                    Forms\Components\Repeater::make('features')
                        ->label('Features')
                        ->schema([
                            $this->themeLocaleTabs('pricing_feature', fn (string $locale): array => [
                                Forms\Components\TextInput::make("text.{$locale}")->label('Text')->maxLength(500),
                            ]),
                        ])
                        ->defaultItems(0)
                        ->collapsible(),
                ])
                ->defaultItems(0)
                ->collapsible()
                ->columnSpanFull(),
        ];
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected function faqSchema(): array
    {
        return [
            ...$this->titleSubtitleSchema('faq'),
            Forms\Components\Repeater::make('faq.items')
                ->label('Items')
                ->schema([
                    $this->themeLocaleTabs('faq_item', fn (string $locale): array => [
                        Forms\Components\TextInput::make("question.{$locale}")->label('Question')->maxLength(500),
                        Forms\Components\Textarea::make("answer.{$locale}")->label('Answer')->rows(3),
                    ]),
                ])
                ->defaultItems(0)
                ->collapsible()
                ->columnSpanFull(),
        ];
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected function blogSchema(): array
    {
        return $this->titleSubtitleSchema('blog');
    }

    protected function ctaSchema(): array
    {
        return [
            $this->themeLocaleTabs('cta', fn (string $locale): array => [
                Forms\Components\TextInput::make("cta.title.{$locale}")->label('Title')->maxLength(255),
                Forms\Components\TextInput::make("cta.subtitle.{$locale}")->label('Subtitle')->maxLength(500),
                Forms\Components\TextInput::make("cta.cta_label.{$locale}")->label('CTA label')->maxLength(255),
            ]),
            Forms\Components\TextInput::make('cta.cta_url')->label('CTA URL')->url()->maxLength(500),
            MediaPickerField::make('cta.background_image_id')->label('Background image'),
        ];
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected function statsSchema(): array
    {
        return [
            Forms\Components\Repeater::make('stats.items')
                ->label('Items')
                ->schema([
                    Forms\Components\TextInput::make('number')->label('Number')->maxLength(50),
                    Forms\Components\TextInput::make('suffix')->label('Suffix')->maxLength(50),
                    $this->themeLocaleTabs('stats_item', fn (string $locale): array => [
                        Forms\Components\TextInput::make("label.{$locale}")->label('Label')->maxLength(255),
                    ]),
                ])
                ->defaultItems(0)
                ->collapsible()
                ->columnSpanFull(),
        ];
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected function contactSchema(): array
    {
        return [
            Forms\Components\Textarea::make('contact.address')->label('Address')->rows(2),
            Forms\Components\TextInput::make('contact.phone')->label('Phone')->tel()->maxLength(50),
            Forms\Components\TextInput::make('contact.email')->label('Email')->email()->maxLength(255),
            Forms\Components\Textarea::make('contact.map_embed')->label('Map embed')->rows(4),
            Forms\Components\TextInput::make('contact.working_hours')->label('Working hours')->maxLength(255),
        ];
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected function newsletterSchema(): array
    {
        return [
            $this->themeLocaleTabs('newsletter', fn (string $locale): array => [
                Forms\Components\TextInput::make("newsletter.title.{$locale}")->label('Title')->maxLength(255),
                Forms\Components\TextInput::make("newsletter.subtitle.{$locale}")->label('Subtitle')->maxLength(500),
                Forms\Components\TextInput::make("newsletter.placeholder.{$locale}")->label('Placeholder')->maxLength(255),
                Forms\Components\TextInput::make("newsletter.button_label.{$locale}")->label('Button label')->maxLength(255),
            ]),
        ];
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected function footerSchema(): array
    {
        return [
            $this->themeLocaleTabs('footer', fn (string $locale): array => [
                Forms\Components\Textarea::make("footer.about_text.{$locale}")->label('About text')->rows(3),
                Forms\Components\TextInput::make("footer.copyright.{$locale}")->label('Copyright')->maxLength(255),
            ]),
            Forms\Components\Repeater::make('footer.columns')
                ->label('Columns')
                ->schema([
                    $this->themeLocaleTabs('footer_column', fn (string $locale): array => [
                        Forms\Components\TextInput::make("title.{$locale}")->label('Title')->maxLength(255),
                    ]),
                    Forms\Components\Select::make('menu_location')
                        ->label('Menu location')
                        ->options([
                            'header' => 'Header',
                            'footer' => 'Footer',
                        ])
                        ->native(false),
                ])
                ->defaultItems(0)
                ->collapsible()
                ->columnSpanFull(),
        ];
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected function brandingSchema(): array
    {
        return [
            MediaPickerField::make('branding.logo_id')->label('Logo'),
            MediaPickerField::make('branding.logo_dark_id')->label('Logo dark'),
            MediaPickerField::make('branding.favicon_id')->label('Favicon'),
            Forms\Components\ColorPicker::make('branding.primary_color')->label('Primary color'),
            Forms\Components\ColorPicker::make('branding.secondary_color')->label('Secondary color'),
            Forms\Components\ColorPicker::make('branding.accent_color')->label('Accent color'),
            Forms\Components\Select::make('branding.font_heading')
                ->label('Font heading')
                ->options($this->googleFontOptions())
                ->searchable()
                ->native(false),
            Forms\Components\Select::make('branding.font_body')
                ->label('Font body')
                ->options($this->googleFontOptions())
                ->searchable()
                ->native(false),
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function googleFontOptions(): array
    {
        return [
            'Inter' => 'Inter',
            'Roboto' => 'Roboto',
            'Open Sans' => 'Open Sans',
            'Lato' => 'Lato',
            'Montserrat' => 'Montserrat',
            'Poppins' => 'Poppins',
            'Playfair Display' => 'Playfair Display',
            'Merriweather' => 'Merriweather',
            'Source Sans 3' => 'Source Sans 3',
            'Nunito' => 'Nunito',
        ];
    }

    /**
     * State kosong per group untuk mount().
     *
     * @return array<string, mixed>
     */
    public function defaultState(): array
    {
        $empty = $this->emptyTranslatable();

        return [
            'hero' => [
                'title' => $empty,
                'subtitle' => $empty,
                'cta_label' => $empty,
                'cta_url' => null,
                'background_image_id' => null,
                'features' => [],
            ],
            'about' => [
                'title' => $empty,
                'subtitle' => $empty,
                'content' => $empty,
                'image_id' => null,
                'points' => [],
            ],
            'services' => [
                'title' => $empty,
                'subtitle' => $empty,
                'cta_label' => $empty,
            ],
            'portfolio' => [
                'title' => $empty,
                'subtitle' => $empty,
                'cta_label' => $empty,
            ],
            'team' => [
                'title' => $empty,
                'subtitle' => $empty,
            ],
            'testimonial' => [
                'title' => $empty,
                'subtitle' => $empty,
            ],
            'process' => [
                'title' => $empty,
                'subtitle' => $empty,
                'steps' => [],
            ],
            'pricing' => [
                'title' => $empty,
                'subtitle' => $empty,
                'plans' => [],
            ],
            'faq' => [
                'title' => $empty,
                'subtitle' => $empty,
                'items' => [],
            ],
            'blog' => [
                'title' => $empty,
                'subtitle' => $empty,
            ],
            'cta' => [
                'title' => $empty,
                'subtitle' => $empty,
                'cta_label' => $empty,
                'cta_url' => null,
                'background_image_id' => null,
            ],
            'stats' => [
                'items' => [],
            ],
            'contact' => [
                'address' => null,
                'phone' => null,
                'email' => null,
                'map_embed' => null,
                'working_hours' => null,
            ],
            'newsletter' => [
                'title' => $empty,
                'subtitle' => $empty,
                'placeholder' => $empty,
                'button_label' => $empty,
            ],
            'footer' => [
                'about_text' => $empty,
                'copyright' => $empty,
                'columns' => [],
            ],
            'branding' => [
                'logo_id' => null,
                'logo_dark_id' => null,
                'favicon_id' => null,
                'primary_color' => null,
                'secondary_color' => null,
                'accent_color' => null,
                'font_heading' => null,
                'font_body' => null,
            ],
        ];
    }

    /**
     * Gabungkan default group dengan data dari database.
     *
     * @param  array<string, mixed>  $groupData
     * @return array<string, mixed>
     */
    public function mergeGroupDefaults(string $group, array $groupData): array
    {
        $defaults = $this->defaultState()[$group] ?? [];

        return array_replace_recursive($defaults, $groupData);
    }
}
