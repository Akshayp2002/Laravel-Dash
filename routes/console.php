<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();


Schedule::command('daily:cron')->daily();
Schedule::command('weekly:cron')->weekly();
Schedule::command('monthly:cron')->monthly();
Schedule::command('yearly:cron')->yearly();
