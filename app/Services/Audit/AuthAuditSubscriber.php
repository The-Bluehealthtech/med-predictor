<?php

namespace App\Services\Audit;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Events\Dispatcher;

/** Événements d'authentification dans l'audit trail : connexions, échecs, déconnexions. */
final class AuthAuditSubscriber
{
    public function __construct(private readonly Auditor $auditor)
    {
    }

    public function subscribe(Dispatcher $events): array
    {
        return [
            Login::class => 'onLogin',
            Failed::class => 'onFailed',
            Logout::class => 'onLogout',
            Lockout::class => 'onLockout',
            PasswordReset::class => 'onPasswordReset',
        ];
    }

    public function onLogin(Login $event): void
    {
        $this->auditor->record(['event_type' => 'security', 'action' => 'login', 'module' => 'security',
            'description' => 'Connexion réussie', 'model' => $event->user, 'metadata' => ['guard' => $event->guard, 'remember' => $event->remember]]);
    }

    public function onFailed(Failed $event): void
    {
        $this->auditor->record(['event_type' => 'security', 'action' => 'login_failed', 'module' => 'security', 'severity' => 'warning',
            'description' => 'Échec de connexion', 'metadata' => ['guard' => $event->guard, 'attempted_email' => $event->credentials['email'] ?? null, 'known_account' => $event->user !== null]]);
    }

    public function onLogout(Logout $event): void
    {
        if ($event->user) {
            $this->auditor->record(['event_type' => 'security', 'action' => 'logout', 'module' => 'security',
                'description' => 'Déconnexion', 'model' => $event->user, 'metadata' => ['guard' => $event->guard]]);
        }
    }

    public function onLockout(Lockout $event): void
    {
        $this->auditor->record(['event_type' => 'security', 'action' => 'lockout', 'module' => 'security', 'severity' => 'error',
            'description' => 'Trop de tentatives de connexion : compte temporairement bloqué', 'metadata' => ['attempted_email' => $event->request->input('email')]]);
    }

    public function onPasswordReset(PasswordReset $event): void
    {
        $this->auditor->record(['event_type' => 'security', 'action' => 'password_reset', 'module' => 'security',
            'description' => 'Mot de passe réinitialisé', 'model' => $event->user]);
    }
}
