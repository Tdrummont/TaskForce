<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserLoginNotification extends Notification
{
    use Queueable;

    public $user;
    public $loginTime;
    public $ipAddress;
    public $userAgent;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, $ipAddress = null, $userAgent = null)
    {
        $this->user = $user;
        $this->loginTime = now();
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $locale = app()->getLocale() ?: config('app.locale');
        $url = route('dashboard', ['locale' => $locale], false);
        $loginTime = $this->loginTime->format('d/m/Y H:i:s');
        $loginDate = $this->loginTime->format('d/m/Y');
        $loginHour = $this->loginTime->format('H:i:s');

        return (new MailMessage)
            ->greeting(" Olá {$this->user->name}!")
            ->subject("🔔 Nova Atividade Detectada - YggdraTasks")
            ->line("**Detectamos um novo acesso à sua conta Iron Force Tasks.**")
            ->line("")
            ->line("**Data:** {$loginDate}")
            ->line("**Horário:** {$loginHour}")
            ->line("**Usuário:** {$this->user->name}")
            ->line("**Email:** {$this->user->email}")
            ->when($this->ipAddress, function ($message) {
                return $message->line("🌐 **Endereço IP:** {$this->ipAddress}");
            })
            ->when($this->userAgent, function ($message) {
                return $message->line("🌍 **Navegador:** {$this->userAgent}");
            })
            ->line("")
            ->line("**Este é um acesso autorizado?**")
            ->line("Se sim, você pode ignorar este email.")
            ->line("")
            ->line("**Se você não fez este login:**")
            ->line("• Altere sua senha imediatamente")
            ->line("• Entre em contato com o suporte")
            ->line("• Ative a verificação em duas etapas")
            ->action('Acessar Dashboard', $url)
            ->line("")
            ->line("**Sua segurança é nossa prioridade!**")
            ->line("Obrigado por usar o YggdraTask.")
            ->salutation('Atenciosamente, Equipe Yggdra');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'user_email' => $this->user->email,
            'login_time' => $this->loginTime,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'message' => "Login detectado para {$this->user->name}",
            'type' => 'user_login',
            'created_at' => now(),
        ];
    }
} 