<?php

namespace App\Enums;

enum LeadStatus: string
{
    case MY_LIST = 'm';
    case RECALL = 'r';
    case DIDNT_PICK_UP = 'dp';
    case FIRST_CALL = 'fc';
    case CONTACTED_COMING = 'c';
    case CALL = 'call';
    case ARCHIVE = 'ar';
    case FINISHED = 'f';
    case NOT_PAYING_AFTER_1_LESSON = 'np';
    case RETURN_TO_POOL = 'ret';
    case CHANGE_STATUS = 'chs';

    public function label(): string
    {
        return match ($this) {
            self::MY_LIST => 'My list',
            self::RECALL => 'Recall',
            self::DIDNT_PICK_UP => 'Didn`t pick up',
            self::CONTACTED_COMING => 'Contacted coming',
            self::ARCHIVE => 'Archive',
            self::FINISHED => 'Finished',
            self::RETURN_TO_POOL => 'Return to pool',
            self::FIRST_CALL => 'first call',
            self::CALL => 'Call',
            self::CHANGE_STATUS => 'change status',
        };
    }
}
