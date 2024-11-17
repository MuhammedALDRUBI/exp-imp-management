<?php

namespace ExpImpManagement\ImportersManagement\Importer\Traits;

use ExpImpManagement\ImportersManagement\Notifications\SuccessfulImportingNotification;
use Illuminate\Notifications\Notification;

trait NotificationMethods
{

    public function getConvinientNotification() : Notification
    {
        return new SuccessfulImportingNotification();
    }
}