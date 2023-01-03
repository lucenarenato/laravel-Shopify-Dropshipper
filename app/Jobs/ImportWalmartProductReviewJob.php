<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ImportWalmartProductReviewJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $productID = '';
    private $review_row = '';
    private $reviews = [];
    private $walmartReviews = [];
    private $rating = 0;
    private $walmart_rating = 0;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($productID, $review_row,$walmart_rating=null,$walmartReviews)
    {
        $this->productID = $productID;
        $this->review_row = $review_row;
        $this->walmart_rating = $walmart_rating;
        $this->walmartReviews = json_decode($walmartReviews, true);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
           // logger('========== START:: ImportProductReviewJob =========');
            $db_reviews = (array)$this->review_row;
            $db_reviews['reviews'] = $this->importReview($this->review_row, 1);
            $db_reviews['status'] = 'Yes';
            $db_reviews['rating'] = $this->rating;
            $db_reviews['is_show_front'] = true;
            $db_reviews['reviews'] = ( count($db_reviews['reviews']) > 20 ) ? array_slice($db_reviews['reviews'], 0, 20) : $db_reviews['reviews'];
            DB::table('products')
                ->where('id', $this->productID)
                ->update(['reviews' => $db_reviews]);

        }catch( \Exception $e ){
            logger('========== ERROR:: ImportProductReviewJob =========');
            logger(json_encode($e));
        }
    }

    public function importReview($data, $page = 1){
        try{
          //  logger('========== START:: importReview =========');

                $queryArr = [
                    'asin' => $data->asin
                ];

                $is_four_star = 0;
                if( $data->import_type == 1 ){
                    $queryArr['review_media_type'] = 'media_reviews_only';
                }elseif ( $data->import_type == 2 ){
                    $is_four_star = 1;
                    $queryArr['review_stars'] = 'four_star';
                }

            $this->getWalmartReviews($queryArr, $is_four_star);
            return $this->reviews;

        }catch( \Exception $e ){
            logger('========== ERROR:: importReview =========');
           // logger(json_encode($e));
        }
    }


    public function getWalmartReviews($queryArr, $is_four_star){
        try{
          //  logger('is_four_star' . $is_four_star);
         //   logger('========== START:: getWalmartReviews =========');

            $result = $this->walmartReviews;

        //    logger($result);

            if(count($result)>0){

             //   logger("total review ".count($result));

                $this->rating = $this->walmart_rating;

                $new_reviews = [];

                for($i=0;$i<min(100, count($result));$i++){

                  //  logger("i ".$i);
                  //  logger("Review Item");
                  //  logger($result[$i]);
                  //  logger($result[$i]['author_name']);

                    $profile = [
                        "name" => (isset($result[$i]['author_name'])) ? $result[$i]['author_name'] : '',
                        "link" =>  ''
                    ];

                    $review_data = [
                        "id" => $this->productID,
                        "asin" => $queryArr['asin'],
                        "body" =>  (@$result[$i]['review_title']) ? $result[$i]['review_title'] : '',
                        "date" => [
                           "raw" => (@$result[$i]['reviewed_date']) ? $result[$i]['reviewed_date'] : ''
                        ],
                        "link" =>  (@$result[$i]['review_url']) ? $result[$i]['review_url'] : '',
                        "title" => (@$result[$i]['review_title']) ? $result[$i]['review_title'] : '',
                        "images" => [],
                        "rating" =>  (@$result[$i]['rating']) ? $result[$i]['rating'] : '',
                        "position" => $i,
                        "body_html" => (@$result[$i]['review_text']) ? $result[$i]['review_text'] : '',
                        "attributes" => [],
                        "vine_program" => "false",
                        "review_country" => '',
                        "attributes_flat" => '',
                        "is_global_review" => "false",
                        "verified_purchase" =>  (@$result[$i]['badge']) ? $result[$i]['badge'] : '',
                        "profile" => $profile
                    ];

                    if( $is_four_star == 1 ){
                      //  logger("============ Import only 4 and 5 star =============");
                        if($result[$i]['rating'] >= 4){

                            array_push($new_reviews,$review_data);
                        }

                    }else{
                      //  logger("============ Import All star =============");
                        array_push($new_reviews,$review_data);
                    }
                }

                $this->reviews = array_merge($this->reviews, $new_reviews);

            }else{
               // logger(json_encode($result));
            }

            return $this->reviews;
        }catch( \Exception $e ){
            logger('========== ERROR:: getWalmartReviews =========');
           // logger(json_encode($e));
        }
    }

}
