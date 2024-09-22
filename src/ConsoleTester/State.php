<?php

declare(strict_types=1);

namespace App\ConsoleTester;

enum State
{
    case CHOOSING_TEST;
    case TESTING;
    case RESULTS;
}
