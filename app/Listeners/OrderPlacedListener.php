<?php

namespace App\Listeners;

use App\Events\OrderPlacedEvent;
use App\Traits\EmailTemplateTrait;
use App\Traits\PushNotificationTrait;
use Illuminate\Support\Facades\Log;

class OrderPlacedListener
{
    use PushNotificationTrait, EmailTemplateTrait;

    /**
     * Handle the event.
     */
    public function handle(OrderPlacedEvent $event): void
    {
        if ($event->email) {
            $this->sendMail($event);
        }
        if ($event->notification) {
            $this->sendNotification($event);
        }
    }

    /**
     * Send the order-placed email with the real template and data.
     */
    private function sendMail(OrderPlacedEvent $event): void
    {
        $email = $event->email;
        $data  = $event->data;

     
        Log::info("OrderPlacedListener: preparing to send order email to {$email}", $data);

        try {
         
            $this->sendingMail(
                sendMailTo:   $email,
                userType:     $data['userType'],
                templateName: $data['templateName'],
                data:         $data
            );

        
            Log::info("OrderPlacedListener: order email sent successfully to {$email}");
        } catch (\Exception $e) {
          
            Log::error("OrderPlacedListener: failed to send order email to {$email} — ".$e->getMessage());
        }
    }

    /**
     * Send push/notification if configured.
     */
    private function sendNotification(OrderPlacedEvent $event): void
    {
        $key   = $event->notification->key;
        $type  = $event->notification->type;
        $order = $event->notification->order;

        $this->sendOrderNotification(
            key:   $key,
            type:  $type,
            order: $order
        );
    }
}
