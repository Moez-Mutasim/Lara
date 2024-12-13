<?php

namespace App\Console\Commands;

use Illuminate\Database\Console\Seeds\SeedCommand;

class CustomSeedCommand extends SeedCommand
{
    
    protected function configure()
    {
        parent::configure();

        $this->getDefinition()->addOption(
            new \Symfony\Component\Console\Input\InputOption(
                'seeders',
                null,
                \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL,
                'Comma-separated list of seeders to run.'
            )
        );
    }
}
