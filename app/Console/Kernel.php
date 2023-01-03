<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
       // logger('=============== Auto update frequency setting ===============');
        try{

            //START :: Auto Update product job Disabled

//            $products = DB::table('products')->get();
//
//            foreach( $products as $pkey=>$pval ){
//                $autoUpdate = json_decode($pval->auto_update);
//                $frequency = (int)$autoUpdate->frequency;
//                $status = $autoUpdate->status;
//                if( $status == 1){
//                    if( $frequency != 1 && $frequency != 0 ){
//                        if( $autoUpdate->frequency == 24 ){
//                            $schedule->command('autoupdate:products', [$pval->id])
//                                ->dailyAt('00:00');
//                        }else{
//                            $days = ( $autoUpdate->frequency == 48 ) ? 2 : 3;
//                            $schedule->command('autoupdate:products', [$pval->id])
//                                ->cron("0 0 */$days * *");
//                        }
//
//                    }else{
//                        if( $frequency != 0 ){
//                            $schedule->command('autoupdate:products', [$pval->id])
//                                ->weekly();
//                        }
//                    }
//                }
//            }

        //END :: Auto Update product job Disabled


            $schedule->command('track:category')->dailyAt('23:00');
            $schedule->command('track:charge')->hourlyAt(30);
            // $schedule->command('test:scheduler')->everyMinute();
            //  $schedule->command('test:scheduler')->everyMinute()->when(function () {
            //     return true; // TODO: check if the connection's good
            // });

        }catch ( \Exception $e ){
            logger($e);
        }

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
