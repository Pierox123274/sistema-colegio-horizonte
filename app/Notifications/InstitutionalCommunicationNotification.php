<?php

namespace App\Notifications;

use App\Enums\NotificationCategory;
use App\Enums\NotificationPriority;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InstitutionalCommunicationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  list<string>  $channels
     * @param  array<string, mixed>  $meta
     */
    public function __construct(
        private readonly string $title,
        private readonly string $message,
        private readonly NotificationCategory $category,
        private readonly NotificationPriority $priority = NotificationPriority::Medium,
        private readonly ?string $actionUrl = null,
        private readonly ?string $actionLabel = null,
        private readonly array $channels = ['database'],
        private readonly array $meta = [],
        private readonly ?string $mailTemplate = null,
    ) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return $this->channels;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'category' => $this->category->value,
            'priority' => $this->priority->value,
            'action_url' => $this->actionUrl,
            'action_label' => $this->actionLabel,
            'meta' => $this->meta,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $markdown = $this->mailTemplate !== null
            ? 'mail.'.$this->mailTemplate
            : 'mail.institutional-notification';

        return (new MailMessage)
            ->subject($this->title)
            ->markdown($markdown, [
                'title' => $this->title,
                'message' => $this->message,
                'category' => $this->category->value,
                'priority' => $this->priority->value,
                'actionUrl' => $this->actionUrl,
                'actionLabel' => $this->actionLabel ?? 'Ver detalle',
            ]);
    }
}
