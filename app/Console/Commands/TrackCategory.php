<?php

namespace App\Console\Commands;

use App\Models\BestsellerCategory;
use App\Models\Counters;
use App\Traits\AutoUpdate;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TrackCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'track:category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
           // logger('=============== START:: TrackCategory =============');

            $queryString = http_build_query([
                'api_key' => config('const.rainforest_api_key'),
                'type' => 'bestsellers',
                'url' => 'https://www.amazon.com/Best-Sellers/zgbs',
            ]);
            $result = rainforestApiRequest($queryString);
            if( $result['request_info']['success'] ){
                $categories = (@$result['bestsellers_info']['child_categories']) ? $result['bestsellers_info']['child_categories'] : [];
                if( !empty($categories) ){
                    foreach ( $categories as $key=>$val ){
                        $is_exist = BestsellerCategory::where('category_id', $val['id'])->first();
                        $db_category = ( $is_exist ) ? $is_exist : new BestsellerCategory;
                        $db_category->category_id = $val['id'];
                        $db_category->name = $val['name'];
                        $db_category->url = $val['link'];
                        $db_category->save();
                    }
                }
            }
           // logger('=============== END:: TrackCategory =============');
        }catch( \Exception $e ){
            logger('=============== ERROR:: TrackCategory =============');
            logger($e);
        }
    }
}
