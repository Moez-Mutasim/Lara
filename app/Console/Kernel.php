<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        CustomSeedCommand::class,
    ];
    
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
