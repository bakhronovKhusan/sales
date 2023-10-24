<?php

namespace App\Enums;

enum StudentStatus: string
{
    case ACTIVE = 'a';

    case FAILED = 'fs';
    case ARCHIVED = 'ar';

    case IN_GROUP = 'iG';

    case PRESENT_1_LESSON = 'p1';

    case NOT_PAYING_AFTER_1_LESSON = 'np';

    case WAITING = 'w';

    case WAITING_TRIAL = 'wt';

    case WAITING_ACTIVE = 'wa';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::ARCHIVED => 'Archived',
            self::IN_GROUP => 'In group',
            self::PRESENT_1_LESSON => 'Present first lesson',
            self::NOT_PAYING_AFTER_1_LESSON => 'Not paying after 1st lesson',
            self::WAITING => 'Waiting',
            self::WAITING_TRIAL => 'Waiting trial',
            self::WAITING_ACTIVE => 'Waiting active'
        };
    }
}
