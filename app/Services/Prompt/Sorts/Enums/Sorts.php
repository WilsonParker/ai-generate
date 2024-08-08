<?php

namespace App\Services\Prompt\Sorts\Enums;

enum Sorts: string
{
    case Relevance = 'relevance';
    case Hottest = 'hottest';
    case TOP = 'top';
    case Newest = 'newest';
    case Oldest = 'oldest';
}
