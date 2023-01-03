<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ImportProductReviewJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $productID = '';
    private $review_row = '';
    private $reviews = [];
    private $rating = 0;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($productID, $review_row)
    {
        $this->productID = $productID;
        $this->review_row = $review_row;
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
//            $db_reviews = array_merge(, $this->importReview($this->review_row, 1));


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

           // logger('========== START:: importReview =========');

            $data = (array)$data;

            $queryArr = [
                'api_key' => config('const.rainforest_api_key'),
                'type' => 'reviews',
                'asin' => $data['asin'],
                'amazon_domain' => $data['domain'],
                'page' => $page,
                'sort_by' => 'most_recent'
            ];

            $is_four_star = 0;
            if( $data['import_type'] == 1 ){
                $queryArr['review_media_type'] = 'media_reviews_only';
            }elseif ( $data['import_type'] == 2 ){
                $is_four_star = 1;
                $queryArr['review_stars'] = 'four_star';
            }

            $this->getReviews($queryArr, $is_four_star);
           // logger('========== END:: importReview =========');
            return $this->reviews;
        }catch( \Exception $e ){
            logger('========== ERROR:: importReview =========');
            logger(json_encode($e));
        }
    }

    public function getReviews($queryArr, $is_four_star){
        try{

           // logger('========== START:: getReviews =========');

            $queryString = http_build_query($queryArr);

            $result = rainforestApiRequest($queryString);

            if( @$result['request_info']['success'] ){
                if( $queryArr['page'] == 1 ){
                    $this->rating = $result['summary']['rating'];
                }
                $this->reviews = array_merge($this->reviews, $result['reviews']);

                if( $is_four_star == 1 ){

                    if( count($this->reviews) < 10 ) {
                        if ($result['pagination']['total_pages'] != $result['pagination']['current_page'] ) {
                         //   logger('Page = '.$result['pagination']['current_page']++);
                            $queryArr['page'] = $result['pagination']['current_page']++;
                            $this->getReviews($queryArr, $is_four_star);
                        }
                    }
                }else{

                    if( count($this->reviews) < 20 ){
                        if( $result['pagination']['total_pages'] != $result['pagination']['current_page']){
                           // logger('Page = ' . $result['pagination']['current_page']++);
                            $queryArr['page'] = $result['pagination']['current_page']++;
                            $this->getReviews( $queryArr, $is_four_star);
                        }
                    }

                }
            }else{

            }

            if( $is_four_star == 1 ){
                $queryArr['page'] = 1;
                $queryArr['review_stars'] = 'five_star';

                if( count($this->reviews) < 20 ){
                    $this->getReviews( $queryArr, 0);
                }
            }

            return $this->reviews;
        }catch( \Exception $e ){
            logger('========== ERROR:: getReviews =========');
            logger(json_encode($e));
        }
    }
}
