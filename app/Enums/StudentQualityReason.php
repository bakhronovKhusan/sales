<?php

namespace App\Enums;

enum StudentQualityReason: string
{
    case REGION_POTENTIAL = 'lead region potencial';

    case PRICE = 'lead price';

    case LOCATION = 'lead location';
}
