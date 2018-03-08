<?php

namespace NotificationChannels\Asana;

use Exception;
use Torann\LaravelAsana\Facade\Asana;
use Illuminate\Notifications\Notification;
use NotificationChannels\Asana\Events\MessageWasSent;
use NotificationChannels\Asana\Events\SendingMessage;
use NotificationChannels\Asana\Exceptions\InvalidConfiguration;
use NotificationChannels\Asana\Exceptions\CouldNotSendNotification;


class AsanaChannel
{


    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @throws \NotificationChannels\Asana\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        if (! $routing = collect($notifiable->routeNotificationFor('Asana'))) {
            return;
        }

        $accessToken = config('asana.accessToken');
        if (is_null($accessToken)) {
            throw InvalidConfiguration::configurationNotSet();
        }

        $asanaParameters = $notification->toAsana($notifiable)->toArray();

        try {
            $task = Asana::createTask($asanaParameters);
        } catch (Exception $e) {
            throw CouldNotSendNotification::serviceRespondedWithAnError($e->getMessage());
        }
    }
}
