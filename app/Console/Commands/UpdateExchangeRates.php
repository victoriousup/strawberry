<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Utils\ExchangeRates;

class UpdateExchangeRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-exchange-rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the current exchange rates';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if(ExchangeRates::update())
        {
            $this->info('Exchange rates have been updated');

        }
        else
        {
            $this->error('An error occured while updating the exchange rates');
        }
    }
}
