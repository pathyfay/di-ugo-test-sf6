<?php

namespace App\Enum;

enum FileStatusEnum: string
{
    case CREATED = 'created';
    case BLOCKED = 'blocked';
    case DELETED = 'deleted';
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';
    case CANCELLED = 'cancelled';
    case ARCHIVED = 'archived';
    case FAILED = 'failed';
    case COMPLETED = 'completed';
}
