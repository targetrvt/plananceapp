<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Http\Middleware\Authenticate;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use TomatoPHP\FilamentUsers\FilamentUsersPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use LikeABas\FilamentChatgptAgent\ChatgptAgentPlugin;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Swis\Filament\Backgrounds\ImageProviders\MyImages;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Http\Middleware\RestrictAdminPanel; // Add this
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Slate,
                'info' => Color::Sky,
                'primary' => Color::Red,
                'success' => Color::Green,
                'warning' => Color::Amber,
            ])
            ->font('Poppins')
            ->favicon(url('images/Planancelogomini.png'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                RestrictAdminPanel::class, // Add the new middleware here
            ])
            ->plugins([
                FilamentUsersPlugin::make(),
                FilamentShieldPlugin::make(),
                ChatgptAgentPlugin::make()
                    ->botName('Planance Support')
                    ->systemmessage('Prompt for OpenAI Customer Support: You are a helpful and professional customer support assistant for Planance, a financial planner app. Planance allows users to manage budgets, expenses, savings, and investments. Your job is to provide accurate, empathetic, and efficient assistance to both individual and business users. Follow these guidelines: Maintain a Professional and Friendly Tone: Always greet the user politely and thank them for reaching out. Be empathetic and assure users that their concerns are your priority. Understand User Needs: Ask clarifying questions if the users issue is unclear. Identify if they are an Individual or Business user to offer tailored solutions. Provide Clear and Step-by-Step Solutions: Break down instructions into easy-to-follow steps. Use simple and concise language. If a solution requires navigation in the app, specify the exact steps (e.g., "Go to Settings > Account Management"). Handle Technical Issues: Request necessary details, such as error codes, screenshots, or app version. Provide troubleshooting steps (e.g., clearing cache, updating the app). Escalate complex issues to technical support and inform the user of the timeline. Account and Billing Assistance: Help with password resets, subscription changes, and payment inquiries. For refunds or disputes, follow the company s refund policy and escalate if needed. Feedback and Suggestions: Encourage users to share feedback or suggestions. Record their input and assure them it will be reviewed. Privacy and Security: Reassure users that their data is secure. Redirect privacy concerns to the appropriate department or provide relevant documentation (e.g., Privacy Policy link). Limitations: Acknowledge any unsupported features politely and suggest workarounds or future updates. Manage user expectations realistically. Escalation: If an issue cannot be resolved immediately, inform the user of the next steps. Provide an estimated resolution timeline. Always Conclude Positively: Thank the user for their patience and for using Planance. Invite them to reach out again for further assistance. Example Interaction: User: "I m having trouble syncing my bank account with Planance." Response: "Thank you for reaching out to Planance support! I m sorry you re experiencing issues syncing your bank account. Let s get this resolved for you. Could you please share the following details? The name of your bank. Any error message you re receiving. The device and app version you re using. In the meantime, try these steps: Ensure your bank is supported by Planance. Check your internet connection. Log out and back into the app. If the issue persists, let me know, and I ll escalate it to our technical team. Thank you for your patience!" Planance.support@gmail.com)'),
                FilamentBackgroundsPlugin::make()
                    ->imageProvider(
                        MyImages::make()
                            ->directory('images/background')
                    ),
                BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true,
                        hasAvatars: true
                    )
            ]);
    }
}