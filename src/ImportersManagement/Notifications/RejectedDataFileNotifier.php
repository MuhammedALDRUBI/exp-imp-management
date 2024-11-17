<?php

namespace ExpImpManagement\ImportersManagement\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RejectedDataFileNotifier extends Notification
{
    use Queueable;
    protected string $rejectedDataFileAssetPath;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $rejectedDataFileAssetPath)
    {
       $this->rejectedDataFileAssetPath = $rejectedDataFileAssetPath;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject("Data Importing Result ... Failed to import some data")
                    ->line('Hello , You Send A Request To Import Some Data ')
                    ->line('The valid data has benn already imported .... And failed to import some data rows found in theis file ')
                    ->action('Click To Download File', $this->rejectedDataFileAssetPath)
                    ->line('Note : You Can Download The Data Within 7 Days From Now  , After 3 Days It Will Be Removed From System Storage')
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
