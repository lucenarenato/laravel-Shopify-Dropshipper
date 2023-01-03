<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

/**
 * Trait AutoUpdate.
 */
trait ImportReview
{
    public $reviews = [];
    public function importReview($data, $page = 1){
        try{
            logger('========== START:: importReview =========');
            $queryArr = [
                'api_key' => config('const.rainforest_api_key'),
                'type' => 'reviews',
                'asin' => $data->asin,
                'amazon_domain' => $data->domain,
                'page' => $page,
                'sort_by' => 'most_recent'
            ];

            $is_four_star = false;
            if( $data->import_type == 1 ){
                $queryArr['review_media_type'] = 'media_reviews_only';
            }elseif ( $data->import_type == 2 ){
                $is_four_star = true;
                $queryArr['review_stars'] = 'four_star';
            }

            $this->getReviews($queryArr, $is_four_star);

            return $this->reviews;
        }catch( \Exception $e ){
            logger('========== ERROR:: importReview =========');
            logger(json_encode($e));
        }
    }

    public function getReviews($queryArr, $is_four_star){
        try{
            logger('========== START:: getReviews =========');

            $queryString = http_build_query($queryArr);

            $result = rainforestApiRequest($queryString);

            if( @$result['request_info']['success'] ){
                $this->reviews = array_merge($this->reviews, $result['reviews']);
                if( $result['pagination']['total_pages'] != $result['pagination']['current_page'] ){
                    $queryArr['page'] = $result['pagination']['current_page']++;
                    $this->getReviews( $queryArr, $is_four_star);
                }
            }

            if( $is_four_star ){
                $queryArr['page'] = 1;
                $queryArr['review_stars'] = 'five_star';
                $this->getReviews( $queryArr, false);
            }
        }catch( \Exception $e ){
            logger('========== ERROR:: getReviews =========');
            logger(json_encode($e));
        }
    }
}
