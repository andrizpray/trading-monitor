<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('snapshot:collect-daily')->dailyAt('00:01');
