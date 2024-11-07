<?php
namespace App\Enum;

enum NotificationTypeEnum: string
{
    case BIRTHDAY = 'birthday';
    case REMINDER = 'reminder';
    case SYSTEM = 'system';
    case CUSTOM = 'custom';
}
