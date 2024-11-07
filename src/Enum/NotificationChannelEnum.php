<?php
namespace App\Enum;

enum NotificationChannelEnum: string
{
    case EMAIL = 'email';
    case SMS = 'sms';
    case PUSH = 'push';
}
