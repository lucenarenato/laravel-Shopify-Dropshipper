<?php

namespace App\Console\Commands;

use App\Jobs\TrackChargeJob;
use Illuminate\Console\Command;

class TrackCharge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'track:charge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will track all charge and check recurring is activate or not';

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
        try{
           // logger('=============== START:: TrackCharge =============');

            TrackChargeJob::dispatchNow();

           // logger('=============== END:: TrackCharge =============');
        }catch( \Exception $e ){
            logger('=============== ERROR:: TrackCharge =============');
            logger($e);
        }
    }
}
