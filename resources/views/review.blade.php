<html>
@empty($reviews)
    @else
        <head>
            <style>
                .review-profile-img{
                    width: 40px;
                    border-radius: 30px;
                }
                .product-review-main {
                    margin: 40px 40px;
                    padding: 0px 40px;
                }
                .product-review-main .modal-body {
                    padding: 1rem !important;
                }
                .product-review-main .modal-header {

                    border-color: #d8dbe0 !important;
                }
                .product-review-main .modal-title {
                    font-weight: 500 !important;
                    font-size: 1.8rem;
                    color: #302f2ea1 !important;
                }
                .review-profile{
                    display: flex;
                    align-items: center;
                }
                .review-profile > img{
                    margin-right: 20px;
                }
                .review-varified{
                    color: #a35c1e;
                }
                .review-date{
                    display: block;
                }
                .review-body {
                    margin: 12px 0;
                }
                .product-review-data {
                    margin: 0px 0 30px 0;
                }
                .review-images img {
                    width: 50px !important;
                    margin: 10px 10px 0 0; !important;
                }
                .avg-ratings{
                    margin: 0px 0 20px 0;
                }
                span.total-rating{
                    color: #007185;
                }
                :root {
                    --star-size: 20px;
                    --star-color: #c5c1bd;
                    --star-background: #f1a033;
                }
                .Stars {
                    --percent: calc(var(--rating) / 5 * 100%);
                    display: inline-block;
                    font-size: var(--star-size);
                    font-family: Times;
                    line-height: 1;
                    margin-right: 5px;
                }
                .Stars::before {
                    content: '★★★★★';
                    letter-spacing: 0px;
                    background: linear-gradient(90deg, var(--star-background) var(--percent), var(--star-color) var(--percent));
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                }
                .a{
                    text-decoration: none;
                    background-color: transparent;
                    color: #321fdb;
                    border: none;
                    padding: 0;
                    cursor: pointer;
                }
                .a:focus{
                    outline: none;
                }
                .modal-open {
                    overflow: auto !important;
                }

            </style>
        </head>
        <body>
            <div class="product-review-main">
                <?php $totalRatings = count($reviews['reviews']); ?>
                <h2>Product Top Reviews</h2>
                <div class="avg-ratings">
                    <span class="Stars" style="--rating:{{ $reviews['rating'] }};"></span>
                    &nbsp;&nbsp;<span class="total-rating">{{ $totalRatings}} ratings</span>
                </div>
                <?php $disp_review = array_slice($reviews['reviews'], 0, 10); ?>
                @foreach( $disp_review as $key=>$review )
                    @if( @$review->title )
                        <div class="product-review-data">
                            <div class="review-profile">
                                @if( @$review->profile->id )
                                    <img src="https://www.amazon.com/avatar/default/amzn1.account.{{  $review->profile->id }}?square=true&max_width=460" alt="Profile" class="review-profile-img"/>
                                @else
                                    <img v-else src="{{ asset('/images/static/no-user.jpg') }}" alt="Profile" class="review-profile-img"/>
                                @endif
                                @if( @$review->profile )
                                        <p>{{ $review->profile->name }}</p>
                                @endif
                            </div>
                            <div class="review-body">
                                @if( @$review->rating )
                                    <span class="Stars" style="--rating:{{ $review->rating }};"></span>
                                    <b>{{ $review->title }}</b>
                                @endif
                                @if( @$review->date )
                                    <span class="review-date">{{ $review->date->raw }}</span>
                                @endif
                                @if( @$review->verified_purchase )
                                    <span class="review-varified"><b>Verified Purchase</b></span>
                                @endif
                                @if( @$review->bodyHtml )
                                    <p> {!! $review->bodyHtml !!} </p>
                                @endif
                                    @if( @$review->body_html )
                                        <p> {!! $review->body_html !!} </p>
                                    @endif
                                @if( @$review->images )
                                    <div class="review-images">
                                        @foreach( $review->images as $imgkey=>$img )
                                            <a href="{{$img->link}}" target="_blank"><img src="{{ $img->link }}"></a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach

                @if( $totalRatings > 10 )
                    <button type="button" class="a" data-toggle="modal" data-target="#review-modal">See all reviews&nbsp;></button>
{{--                        <!--        review modal-->--}}
                    <!--        review modal-->

                        <!-- Modal -->
                        <div class="modal fade" id="review-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                        <h4 class="modal-title" id="myModalLabel">Reviews</h4>
                                    </div>
                                    <div class="modal-body">
                                        @foreach( $reviews['reviews'] as $key=>$review )
                                            @if( @$review->title )
                                                <div class="product-review-data">
                                                    <div class="review-profile">
                                                        @if( @$review->profile->id )
                                                            <img src="https://www.amazon.com/avatar/default/amzn1.account.{{  $review->profile->id }}?square=true&max_width=460" alt="Profile" class="review-profile-img"/>
                                                        @else
                                                            <img v-else src="{{ asset('/images/static/no-user.jpg') }}" alt="Profile" class="review-profile-img"/>
                                                        @endif
                                                        @if( @$review->profile )
                                                            <p>{{ $review->profile->name }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="review-body">
                                                        @if( @$review->rating )
                                                            <span class="Stars" style="--rating:{{ $review->rating }};"></span>
                                                            <b>{{ $review->title }}</b>
                                                        @endif
                                                        @if( @$review->date )
                                                            <span class="review-date">{{ $review->date->raw }}</span>
                                                        @endif
                                                        @if( @$review->verified_purchase )
                                                            <span class="review-varified"><b>Verified Purchase</b></span>
                                                        @endif
                                                        @if( @$review->bodyHtml )
                                                            <p> {!! $review->bodyHtml !!} </p>
                                                        @endif
                                                        @if( @$review->body_html )
                                                            <p> {!! $review->body_html !!} </p>
                                                        @endif
                                                        @if( @$review->images )
                                                            <div class="review-images">
                                                                @foreach( $review->images as $imgkey=>$img )
                                                                    <a href="{{$img->link}}" target="_blank"><img src="{{ $img->link }}"></a>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                @endif
            </div>
        </body>
    @endempty
</html>
