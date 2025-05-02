<?php

namespace Scarpinocc\Periods;

use Carbon\Carbon;

interface PeriodInterface
{
    public function contains(Carbon $t);
    public function containsNow();
}