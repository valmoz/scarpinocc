<?php

namespace Scarpinocc\Periods;

use Carbon\Carbon;

interface EdgeInterface
{
    public function before(Carbon $t);
    public function after(Carbon $t);
    public function equals(Carbon $t);
    public function beforeOrEquals(Carbon $t);
    public function afterOrEquals(Carbon $t);
}