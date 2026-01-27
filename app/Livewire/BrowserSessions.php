<?php

namespace App\Livewire;

use Filament\Facades\Filament;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class BrowserSessions extends Component implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;

    protected string $view = 'livewire.browser-sessions';

    public $user;
    public $sessions = [];
    public $password = '';

    public static $sort = 30;

    public function mount()
    {
        $this->user = Filament::getCurrentPanel()->auth()->user();
        $this->loadSessions();
    }

    public function loadSessions()
    {
        $currentSessionId = Session::getId();
        $userId = $this->user->id;
        
        $sessions = DB::table('sessions')
            ->where('user_id', $userId)
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) use ($currentSessionId) {
                $payload = unserialize(base64_decode($session->payload));
                $userAgent = $session->user_agent ?? 'Unknown';
                
                return [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $userAgent,
                    'device' => $this->getDevice($userAgent),
                    'browser' => $this->getBrowser($userAgent),
                    'platform' => $this->getPlatform($userAgent),
                    'last_activity' => $session->last_activity,
                    'is_current_device' => $session->id === $currentSessionId,
                ];
            });

        $this->sessions = $sessions->toArray();
    }

    protected function getDevice($userAgent)
    {
        if (preg_match('/Mobile|Android|iPhone|iPad/', $userAgent)) {
            return 'Mobile';
        }
        return 'Desktop';
    }

    protected function getBrowser($userAgent)
    {
        if (preg_match('/Chrome\/([0-9.]+)/', $userAgent, $matches)) {
            return 'Chrome';
        } elseif (preg_match('/Firefox\/([0-9.]+)/', $userAgent, $matches)) {
            return 'Firefox';
        } elseif (preg_match('/Safari\/([0-9.]+)/', $userAgent, $matches)) {
            return 'Safari';
        } elseif (preg_match('/Edge\/([0-9.]+)/', $userAgent, $matches)) {
            return 'Edge';
        }
        return 'Unknown';
    }

    protected function getPlatform($userAgent)
    {
        if (preg_match('/Windows/', $userAgent)) {
            return 'Windows';
        } elseif (preg_match('/Mac OS X/', $userAgent)) {
            return 'macOS';
        } elseif (preg_match('/Linux/', $userAgent)) {
            return 'Linux';
        } elseif (preg_match('/Android/', $userAgent)) {
            return 'Android';
        } elseif (preg_match('/iPhone|iPad/', $userAgent)) {
            return 'iOS';
        }
        return 'Unknown';
    }

    public function logoutOtherSessionsAction(): Action
    {
        return Action::make('logoutOtherSessions')
            ->label(__('profile.browser_sessions.logout_other_sessions'))
            ->requiresConfirmation()
            ->form([
                TextInput::make('password')
                    ->label(__('filament-breezy::default.fields.password'))
                    ->password()
                    ->required()
                    ->rules(['required', function ($attribute, $value, $fail) {
                        if (!Hash::check($value, $this->user->password)) {
                            $fail(__('profile.browser_sessions.password_incorrect'));
                        }
                    }]),
            ])
            ->action(function (array $data) {
                $this->logoutOtherSessions($data['password']);
            });
    }

    protected function logoutOtherSessions($password)
    {
        if (!Hash::check($password, $this->user->password)) {
            Notification::make()
                ->danger()
                ->title(__('profile.browser_sessions.password_incorrect'))
                ->send();
            return;
        }

        $currentSessionId = Session::getId();
        $userId = $this->user->id;

        DB::table('sessions')
            ->where('user_id', $userId)
            ->where('id', '!=', $currentSessionId)
            ->delete();

        $this->loadSessions();

        Notification::make()
            ->success()
            ->title(__('profile.browser_sessions.other_sessions_logged_out'))
            ->send();
    }

    public static function canView(): bool
    {
        return true;
    }

    public static function getSort(): int
    {
        return static::$sort;
    }

    public function render()
    {
        return view($this->view);
    }
}

