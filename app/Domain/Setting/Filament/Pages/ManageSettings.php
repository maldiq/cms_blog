<?php

namespace App\Domain\Setting\Filament\Pages;

use App\Domain\Language\Models\Language;
use App\Domain\Setting\Services\BrandingMediaService;
use App\Domain\Setting\Settings\CommentSettings;
use App\Domain\Setting\Settings\GeneralSettings;
use App\Domain\Setting\Settings\MailSettings;
use App\Domain\Setting\Settings\SeoSettings;
use App\Domain\Setting\Settings\SocialSettings;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithFormActions;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Settings';

    protected static ?string $title = 'Settings';

    protected static ?string $slug = 'settings';

    protected static string $view = 'domain.setting.filament.pages.manage-settings';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user !== null && Gate::forUser($user)->allows('manage-settings');
    }

    public function mount(BrandingMediaService $brandingMediaService): void
    {
        $general = app(GeneralSettings::class);
        $social = app(SocialSettings::class);
        $seo = app(SeoSettings::class);
        $comment = app(CommentSettings::class);
        $mail = app(MailSettings::class);

        $this->form->fill([
            'site_name' => $general->site_name,
            'site_description' => $general->site_description,
            'logo_upload' => $brandingMediaService->uploadPathForMediaId($general->site_logo),
            'favicon_upload' => $brandingMediaService->uploadPathForMediaId($general->site_favicon),
            'default_locale' => $general->default_locale,
            'timezone' => $general->timezone,
            'date_format' => $general->date_format,
            'facebook' => $social->facebook,
            'twitter' => $social->twitter,
            'instagram' => $social->instagram,
            'youtube' => $social->youtube,
            'linkedin' => $social->linkedin,
            'tiktok' => $social->tiktok,
            'default_meta_title' => $seo->default_meta_title,
            'default_meta_description' => $seo->default_meta_description,
            'default_og_image' => $seo->default_og_image,
            'google_analytics_id' => $seo->google_analytics_id,
            'google_search_console' => $seo->google_search_console,
            'auto_approve' => $comment->auto_approve,
            'max_depth' => $comment->max_depth,
            'require_email' => $comment->require_email,
            'notify_admin' => $comment->notify_admin,
            'admin_email' => $comment->admin_email,
            'blocked_words' => $comment->blocked_words,
            'driver' => $mail->driver,
            'host' => $mail->host,
            'port' => $mail->port,
            'username' => $mail->username,
            'password' => $mail->password,
            'encryption' => $mail->encryption,
            'from_address' => $mail->from_address,
            'from_name' => $mail->from_name,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('SettingsTabs')
                    ->tabs([
                        Tabs\Tab::make('General')
                            ->schema($this->generalTabSchema()),
                        Tabs\Tab::make('Social')
                            ->schema($this->socialTabSchema()),
                        Tabs\Tab::make('SEO')
                            ->schema($this->seoTabSchema()),
                        Tabs\Tab::make('Comment')
                            ->schema($this->commentTabSchema()),
                        Tabs\Tab::make('Mail')
                            ->schema($this->mailTabSchema()),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected function generalTabSchema(): array
    {
        return [
            Forms\Components\TextInput::make('site_name')
                ->label('Nama situs')
                ->required()
                ->maxLength(255),
            Forms\Components\Textarea::make('site_description')
                ->label('Deskripsi situs')
                ->rows(3),
            Forms\Components\FileUpload::make('logo_upload')
                ->label('Logo')
                ->image()
                ->disk('public')
                ->directory('branding/logo')
                ->maxSize(2048),
            Forms\Components\FileUpload::make('favicon_upload')
                ->label('Favicon')
                ->image()
                ->disk('public')
                ->directory('branding/favicon')
                ->maxSize(1024),
            Forms\Components\Select::make('default_locale')
                ->label('Locale default')
                ->options(
                    fn (): array => Language::getActive()
                        ->pluck('native_name', 'code')
                        ->all()
                )
                ->required(),
            Forms\Components\TextInput::make('timezone')
                ->label('Timezone')
                ->required(),
            Forms\Components\TextInput::make('date_format')
                ->label('Format tanggal')
                ->required(),
        ];
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected function socialTabSchema(): array
    {
        return [
            Forms\Components\TextInput::make('facebook')->label('Facebook')->url(),
            Forms\Components\TextInput::make('twitter')->label('Twitter / X')->url(),
            Forms\Components\TextInput::make('instagram')->label('Instagram')->url(),
            Forms\Components\TextInput::make('youtube')->label('YouTube')->url(),
            Forms\Components\TextInput::make('linkedin')->label('LinkedIn')->url(),
            Forms\Components\TextInput::make('tiktok')->label('TikTok')->url(),
        ];
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected function seoTabSchema(): array
    {
        return [
            Forms\Components\TextInput::make('default_meta_title')->label('Meta title default'),
            Forms\Components\Textarea::make('default_meta_description')->label('Meta description default')->rows(3),
            Forms\Components\TextInput::make('default_og_image')->label('OG image default')->url(),
            Forms\Components\TextInput::make('google_analytics_id')->label('Google Analytics ID'),
            Forms\Components\TextInput::make('google_search_console')->label('Google Search Console'),
        ];
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected function commentTabSchema(): array
    {
        return [
            Forms\Components\Toggle::make('auto_approve')->label('Auto approve'),
            Forms\Components\TextInput::make('max_depth')->label('Kedalaman maksimal')->numeric()->minValue(1),
            Forms\Components\Toggle::make('require_email')->label('Wajib email'),
            Forms\Components\Toggle::make('notify_admin')->label('Notifikasi admin'),
            Forms\Components\TextInput::make('admin_email')->label('Email admin')->email(),
            Forms\Components\Textarea::make('blocked_words')
                ->label('Kata terlarang (pisah koma)')
                ->rows(2)
                ->helperText('Komentar yang mengandung kata ini otomatis ditandai spam.'),
        ];
    }

    /**
     * @return list<Forms\Components\Component>
     */
    protected function mailTabSchema(): array
    {
        return [
            Forms\Components\Select::make('driver')
                ->label('Driver')
                ->options([
                    'log' => 'Log',
                    'smtp' => 'SMTP',
                    'sendmail' => 'Sendmail',
                ])
                ->required(),
            Forms\Components\TextInput::make('host')->label('Host'),
            Forms\Components\TextInput::make('port')->label('Port')->numeric(),
            Forms\Components\TextInput::make('username')->label('Username'),
            Forms\Components\TextInput::make('password')->label('Password')->password()->revealable(),
            Forms\Components\Select::make('encryption')
                ->label('Enkripsi')
                ->options([
                    null => 'None',
                    'tls' => 'TLS',
                    'ssl' => 'SSL',
                ]),
            Forms\Components\TextInput::make('from_address')->label('From address')->email(),
            Forms\Components\TextInput::make('from_name')->label('From name'),
            Forms\Components\Actions::make([
                Forms\Components\Actions\Action::make('testSendEmail')
                    ->label('Test Send Email')
                    ->action(fn () => $this->sendTestEmail()),
            ]),
        ];
    }

    public function save(BrandingMediaService $brandingMediaService): void
    {
        $state = $this->form->getState();

        $general = app(GeneralSettings::class);
        $general->site_name = $state['site_name'];
        $general->site_description = $state['site_description'];
        $general->default_locale = $state['default_locale'];
        $general->timezone = $state['timezone'];
        $general->date_format = $state['date_format'];

        $logoId = $brandingMediaService->storeFromUpload($state['logo_upload'] ?? null, 'logo');
        if ($logoId !== null) {
            $general->site_logo = $logoId;
        }

        $faviconId = $brandingMediaService->storeFromUpload($state['favicon_upload'] ?? null, 'favicon');
        if ($faviconId !== null) {
            $general->site_favicon = $faviconId;
        }

        $general->save();

        $social = app(SocialSettings::class);
        $social->facebook = $state['facebook'] ?? null;
        $social->twitter = $state['twitter'] ?? null;
        $social->instagram = $state['instagram'] ?? null;
        $social->youtube = $state['youtube'] ?? null;
        $social->linkedin = $state['linkedin'] ?? null;
        $social->tiktok = $state['tiktok'] ?? null;
        $social->save();

        $seo = app(SeoSettings::class);
        $seo->default_meta_title = $state['default_meta_title'] ?? null;
        $seo->default_meta_description = $state['default_meta_description'] ?? null;
        $seo->default_og_image = $state['default_og_image'] ?? null;
        $seo->google_analytics_id = $state['google_analytics_id'] ?? null;
        $seo->google_search_console = $state['google_search_console'] ?? null;
        $seo->save();

        $comment = app(CommentSettings::class);
        $comment->auto_approve = (bool) ($state['auto_approve'] ?? false);
        $comment->max_depth = (int) ($state['max_depth'] ?? 3);
        $comment->require_email = (bool) ($state['require_email'] ?? true);
        $comment->notify_admin = (bool) ($state['notify_admin'] ?? true);
        $comment->admin_email = $state['admin_email'] ?? null;
        $comment->blocked_words = $state['blocked_words'] ?? null;
        $comment->save();

        $mail = app(MailSettings::class);
        $mail->driver = $state['driver'];
        $mail->host = $state['host'] ?? null;
        $mail->port = isset($state['port']) ? (int) $state['port'] : null;
        $mail->username = $state['username'] ?? null;
        if (filled($state['password'] ?? null)) {
            $mail->password = $state['password'];
        }
        $mail->encryption = $state['encryption'] ?? null;
        $mail->from_address = $state['from_address'] ?? null;
        $mail->from_name = $state['from_name'] ?? null;
        $mail->save();

        Notification::make()
            ->title('Settings berhasil disimpan')
            ->success()
            ->send();
    }

    public function sendTestEmail(): void
    {
        $state = $this->form->getState();
        $recipient = auth()->user()?->email;

        if (blank($recipient)) {
            Notification::make()
                ->title('Email pengguna tidak ditemukan')
                ->danger()
                ->send();

            return;
        }

        $mailerName = 'settings_test_' . Str::random(6);

        config([
            "mail.mailers.{$mailerName}" => [
                'transport' => $state['driver'] ?? 'log',
                'host' => $state['host'] ?? null,
                'port' => $state['port'] ?? null,
                'encryption' => $state['encryption'] ?? null,
                'username' => $state['username'] ?? null,
                'password' => $state['password'] ?? null,
                'timeout' => null,
            ],
            'mail.from.address' => $state['from_address'] ?? config('mail.from.address'),
            'mail.from.name' => $state['from_name'] ?? config('mail.from.name'),
        ]);

        try {
            Mail::mailer($mailerName)->raw(
                'Test email dari CMS Blog Settings.',
                function ($message) use ($recipient): void {
                    $message->to($recipient)->subject('Test Email — CMS Blog');
                }
            );

            Notification::make()
                ->title('Test email berhasil dikirim')
                ->body("Dikirim ke {$recipient}")
                ->success()
                ->send();
        } catch (\Throwable $exception) {
            Notification::make()
                ->title('Gagal mengirim test email')
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * @return array<int, Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan')
                ->submit('save'),
        ];
    }
}
