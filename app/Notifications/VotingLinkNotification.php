<?php

namespace App\Notifications;

use App\Models\VotingLink;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VotingLinkNotification extends Notification
{
    use Queueable;

    public function __construct(private VotingLink $votingLink) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('vote.show', $this->votingLink->token);
        $campaign = $this->votingLink->campaign;

        return (new MailMessage)
            ->subject("دعوة تصويت: {$campaign->title}")
            ->greeting("مرحباً {$notifiable->name}")
            ->line("تم إرسال دعوة تصويت لك في حملة: {$campaign->title}")
            ->line($campaign->description ?? '')
            ->action('ابدأ التصويت الآن', $url)
            ->line('هذا الرابط صالح لمرة واحدة فقط.')
            ->line($campaign->ends_at ? "ينتهي التصويت في: {$campaign->ends_at->format('Y-m-d H:i')}" : '')
            ->salutation('منصة التصويت الرياضي');
    }
}
