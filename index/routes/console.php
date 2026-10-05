<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Pembersihan otomatis log kunjungan lama (>60 hari) agar database tetap ramping
Schedule::command('model:prune', [
    '--model' => [\App\Models\VisitorLog::class],
])->daily();
