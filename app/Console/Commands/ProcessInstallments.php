<?php

namespace App\Console\Commands;

use App\Services\InstallmentService;
use Illuminate\Console\Command;

class ProcessInstallments extends Command
{
    protected $signature = 'installments:process';

    protected $description = 'Apply installment late fees and send due/overdue SMS reminders';

    public function handle(InstallmentService $service): int
    {
        $stats = $service->processDaily();

        $this->info(sprintf(
            'Late fees charged: %s | Reminders: %d | Overdue notices: %d',
            number_format($stats['late_fees'], 2),
            $stats['reminders'],
            $stats['overdue_notices'],
        ));

        return self::SUCCESS;
    }
}
