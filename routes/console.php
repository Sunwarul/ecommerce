<?php

// Installment late fees + SMS reminders (requires `php artisan schedule:run` in cron)
\Illuminate\Support\Facades\Schedule::command('installments:process')->dailyAt('09:00')->withoutOverlapping();
