<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Console\Commands\ImportSalesCommand;
use App\Console\Commands\ImportOrdersCommand;
use App\Console\Commands\ImportStocksCommand;
use App\Console\Commands\ImportIncomesCommand;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        ImportSalesCommand::class,
        ImportOrdersCommand::class,
        ImportStocksCommand::class,
        ImportIncomesCommand::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        //
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
