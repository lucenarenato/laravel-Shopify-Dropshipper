@extends('layouts.app')

@section('title', ' | Help Center')

@section('content')

<h1> {{isset($page_title) ? $page_title : "Help Center" }}</h1>
<!-- Begin HTML editing here. See https://getbootstrap.com/docs/4.4/getting-started/introduction/ for Bootstrap documentation. -->
<p>
    {{isset($page_contents['top_text1']) ? $page_contents['top_text1'] : "Help section content (topics, FAQ, etc.)" }}
</p>

<!-- -CODE FROM OLD APP BEGINS-->

<div class="container">
               <div class="row custom-row" id="faqs">
                <h3>{{isset($page_contents['video_tutorial1_text']) ? $page_contents['video_tutorial1_text'] : "Video Tutorial #1: Import Products" }}
               </h3><br>
                <iframe width="560" height="315" src="{{isset($page_contents['video_tutorial1_youtube_embed_link']) ? $page_contents['video_tutorial1_youtube_embed_link'] : "https://www.youtube.com/embed/PMTZUpH8xGs" }}" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                <br>
                <br>
                   <h3>{{isset($page_contents['video_tutorial2_text']) ? $page_contents['video_tutorial2_text'] : "Video Tutorial #2: Amazon Affiliate Set Up" }}
                   </h3><br>
				<iframe width="560" height="315" src="{{isset($page_contents['video_tutorial2_youtube_embed_link']) ? $page_contents['video_tutorial2_youtube_embed_link'] : "https://www.youtube.com/embed/jAntA3n6epE" }}" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
				<br>
                <br>
                   <h3>{{isset($page_contents['video_tutorial3_text']) ? $page_contents['video_tutorial3_text'] : "Video Tutorial #3: Add AliExpress Reviews+Images to Imported Product" }}
                   </h3><br>
				<iframe width="560" height="315" src="{{isset($page_contents['video_tutorial3_youtube_embed_link']) ? $page_contents['video_tutorial3_youtube_embed_link'] : "https://www.youtube.com/embed/h3Mqp8OmOQc" }}" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
				<br>
                <br>
                   <h3>{{isset($page_contents['video_tutorial4_text']) ? $page_contents['video_tutorial4_text'] : "Video Tutorial #4: How to Install/Activate AliExpress Reviews and Amazon Associates Program IDs" }}
                   </h3><br>
				<iframe width="560" height="315" src="{{isset($page_contents['video_tutorial4_youtube_embed_link']) ? $page_contents['video_tutorial4_youtube_embed_link'] : "https://www.youtube.com/embed/BUt2qTDtOwc" }}" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
				<br>
                <br>
               </div>
</div>

            <div>
             @include('faqs')
            </div>

{{--              <div class="container">--}}

{{--                <h3>FAQ</h3>--}}

{{--               <H4>General Questions About Amazone DropShipper</h4>--}}

{{--			  <div class="accordion" id="accordionExample">--}}
{{--				<div class="card">--}}
{{--					<div class="card-header" id="headingOne">--}}
{{--					<h2 class="mb-0">--}}
{{--					<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">--}}
{{--					What is Amazone Dropshipper app and what it does ?--}}
{{--					</button>--}}
{{--					</h2>--}}
{{--					</div>--}}

{{--				    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">--}}
{{--						<div class="card-body">--}}
{{--							Amazone DropShipper  is an application that helps Shopify store owners to increase their inventory and online sales in two ways: <br>--}}
{{--						 1) <b>Allow users to Import detailed information (variants, prices, images and descriptions) from millions of available products from Amazon USA, Canada, Australia, UK, Germany, France, Spain, Italy, Brazil, Mexico, India, Japan and from Walmart USA to the user Shopify Store.</b><br>--}}
{{--						 App users can decide to Dropship these products for their clients (on their own discretion). We recommend reading the  <a href="https://sellercentral.amazon.com/gp/help/external/201808410?language=en-US&ref=mpbc_201808430_cont_201808410" target="_blank">AMAZON POLICY ON DROPSHIPPING</a>.--}}

{{--						 <br><br>IMPORTANT NOTE: If you decide to dropship, Amazone DropShipper has nothing to do with buying, fulfilling or delivering products to your clients, our app is a tool that helps you with the importing of detailed product information  and  then helps you managing the order in an easy way. <br><br>Not clear yet on what is DropShiping ?  Please read the following information to learn about <a href="https://www.entrepreneur.com/article/297744" target="_blank"> dropshipping business model- CLICK HERE </a>--}}
{{--						 <br><br>--}}
{{--						  2) <b>Import products to work with Amazon Affiliates/Associates Program to earn commissions</b>. This way you do not sell the product directly to your client (you don't dropship) , you just redirect  (refer) your clients--}}
{{--						  from your Shopify product page (with the use of our app) to the original Amazon product page, and this way you make money by earning commission (up to 10% depending on the product category) if the client you redirected make any qualified purchase  in Amazon during the next 24 hours. <br><br>--}}
{{--							Read more about Amazon Affiliate/Associate Program below: <br>--}}
{{--							<a href="https://affiliate-program.amazon.com/welcome/getstarted">https://affiliate-program.amazon.com/welcome/getstarted</a> <br>--}}
{{--							For Amazon Affiliates Standard Commission Rates, read here: <br>--}}
{{--							<a href="https://affiliate-program.amazon.com/help/operating/schedule">https://affiliate-program.amazon.com/help/operating/schedule</a> <br>--}}
{{--							<br>--}}
{{--							Learn more about Amazon Affiliate Program here: <a href="AmazonAssociatesHelp.php">AmazoneDropShipper Affiliate/Associate Learning Page</a></li>--}}

{{--						 <br><br>IMPORTANT NOTE: If you will use our app for Amazon's Affiliate program, be aware that our app has no direct relationship with Amazon.  Our app just help users by importing detailed products information and showing these products in their Shopify store.--}}
{{--						 <br>--}}

{{--						 </div>--}}
{{--					 </div>--}}
{{--                 </div>--}}




{{--			<div class="card">--}}
{{--				<div class="card-header" id="headingTwo">--}}
{{--					<h2 class="mb-0">--}}
{{--					<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">--}}
{{--					Does Amazone DropShipper app have any relationship with Amazon or Walmart ?--}}
{{--                    </button>--}}
{{--				    </h2>--}}
{{--				</div>--}}

{{--				 <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">--}}
{{--					<div class="card-body">--}}
{{--						Amazone DropShipper is a tool for Shopify Merchants to import products info from Amazon and Walmart however we have no relationship with Amazon or Walmart.--}}
{{--				     </div>--}}
{{--				  </div>--}}
{{--			 </div>--}}


{{--			<div class="card">--}}
{{--				<div class="card-header" id="headingC">--}}
{{--					<h2 class="mb-0">--}}
{{--					<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseC" aria-expanded="true" aria-controls="collapseC">--}}
{{--					For what Amazon Marketplaces (Countries) does AmazoneDropShipper works  ?--}}
{{--                    </button>--}}
{{--				    </h2>--}}
{{--				</div>--}}

{{--				 <div id="collapseC" class="collapse" aria-labelledby="headingC" data-parent="#accordionExample">--}}
{{--					<div class="card-body">--}}
{{--					                  Amazone DropShipper  works for Amazon 12 Amazon Countries.: USA, Canada, Australia, UK, Germany, France, Spain, Italy, Brazil, Mexico, Japan and India.--}}
{{--									  The most exciting thing is that the app can be used from any Country in the world to import the info from any or all the 12 Amazon marketplaces.--}}
{{--				     </div>--}}
{{--				  </div>--}}
{{--			 </div>--}}

{{--			<div class="card">--}}
{{--				<div class="card-header" id="headingD">--}}
{{--					<h2 class="mb-0">--}}
{{--					<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseD" aria-expanded="true" aria-controls="collapseD">--}}
{{--					For what Walmart Marketplaces (Countries) does AmazoneDropShipper works  ?--}}
{{--                    </button>--}}
{{--				    </h2>--}}
{{--				</div>--}}

{{--				 <div id="collapseD" class="collapse" aria-labelledby="headingD" data-parent="#accordionExample">--}}
{{--					<div class="card-body">--}}
{{--					                  The app works for Walmart.com (USA) , however, the most exciting thing is that the app can be used from any Country in the world to import the info from Walmart.com--}}
{{--				     </div>--}}
{{--				  </div>--}}
{{--			 </div>--}}

{{--			<div class="card">--}}
{{--				<div class="card-header" id="headingD">--}}
{{--					<h2 class="mb-0">--}}
{{--					<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseD" aria-expanded="true" aria-controls="collapseD">--}}
{{--					Do I need to setup Amazone Dropshipper ?--}}
{{--                    </button>--}}
{{--				    </h2>--}}
{{--				</div>--}}

{{--				 <div id="collapseD" class="collapse" aria-labelledby="headingD" data-parent="#accordionExample">--}}
{{--					<div class="card-body">--}}
{{--						 Amazone Dropshipper don't need any setup to work for product information Import!.	Is easy and simple.  However, to Import for Amazon Affiliate Program  you do need to setup your Amazon Affiliates credentials in our App Settings section. In our app you have 4 sections.<br><br>--}}
{{--							1-	Import Products: to import products info from any Amazon marketplace/country and Walmart US<br>--}}
{{--							2-	Manage My Products: to edit/manage your Amazon/Walmart added products.<br>--}}
{{--							3-	View orders: to assist you placing any order in Amazon/Walmart if you decide to Dropship<br>--}}
{{--							4-	App Settings: to assist you with Amazon Affiliates credentials,  Subscription plans and others settings<br>--}}
{{--				     </div>--}}
{{--				  </div>--}}
{{--			 </div>--}}

{{--				<div class="card">--}}
{{--				<div class="card-header" id="headingE">--}}
{{--					<h2 class="mb-0">--}}
{{--					<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseE" aria-expanded="true" aria-controls="collapseE">--}}
{{--				      Do I need an Amazon account to use Amazone DropShipper ?--}}
{{--                    </button>--}}
{{--				    </h2>--}}
{{--				</div>--}}

{{--				 <div id="collapseE" class="collapse" aria-labelledby="headingE" data-parent="#accordionExample">--}}
{{--					<div class="card-body">--}}
{{--					No.  You don't need an Amazon or Walmart account in order to import the products info to your Shopify Store, however, if you decide to dropship and a client place an order in your store for one of the products added through our app, in order for you to fulfill this order you will need an Amazon or Walmart account.<br>--}}
{{--            		<br>IMPORTANT NOTE: For dropshippers, Amazone DropShipper has nothing to do with buying, fulfilling or delivering products to your clients. If you receive an order for a product, you (the app user) are responsible for placing the order in Amazon or in Walmart and shipping to your client. Our App just help merchants importing products to their site.--}}
{{--            		<br>--}}
{{--            		Please be aware of and read: <span style="font-weight:bold"><a href="https://sellercentral.amazon.com/gp/help/external/201808410?language=en-US&ref=mpbc_201808430_cont_201808410" target="_blank">AMAZON POLICY ON DROPSHIPPING</a></span>--}}
{{--				     </div>--}}
{{--				  </div>--}}
{{--			 </div>--}}

{{--			<div class="card">--}}
{{--				<div class="card-header" id="headingF">--}}
{{--					<h2 class="mb-0">--}}
{{--					<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseF" aria-expanded="true" aria-controls="collapseF">--}}
{{--					How much Amazone  DropShipper cost ?--}}
{{--                    </button>--}}
{{--				    </h2>--}}
{{--				</div>--}}

{{--				 <div id="collapseF" class="collapse" aria-labelledby="headingF" data-parent="#accordionExample">--}}
{{--					<div class="card-body">--}}
{{--						Our app has the best prices in Shopify compared with the value we give you and what others app offer. Since June 25, 2019 we have a Freemium Plan (0$), Pro Plan ($4.99), Gold Plan ($9.99) , Ultimate Plan ($25.99). You can see each plan Details on the App SEETINGS > SUBSCRIPTION section. The Freemium plan is to be used just once, it does not renew.--}}
{{--				     </div>--}}
{{--				  </div>--}}
{{--			 </div>--}}

{{--		      <H4>How to Use Amazone DropShipper</h4>--}}

{{--			<div class="card">--}}
{{--				<div class="card-header" id="headingG">--}}
{{--					<h2 class="mb-0">--}}
{{--					<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseG" aria-expanded="true" aria-controls="collapseG">--}}
{{--					How to Import a Product from any amazon marketplace or Walmart.com using Amazone Dropshipper ?--}}
{{--                    </button>--}}
{{--				    </h2>--}}
{{--				</div>--}}

{{--				 <div id="collapseG" class="collapse" aria-labelledby="headingG" data-parent="#accordionExample">--}}
{{--					<div class="card-body">--}}
{{--						Once you know the product from Amazon or Walmart you want to import just copy the product URL.--}}

{{--					   Then, Go to the 'Import Product' section of the app and simply paste the URL of the Amazon product or Walmart Product you want to publish in your store and click the GET PRODUCT button. If you want to import for Amazon affiliate Program you have to  first click the switch box "Import for Affiliate Program" and then click GET PRODUCT.<br>--}}
{{--						Then:<br>--}}
{{--						<ul>--}}
{{--						<li> You can change the title of the product *</li>--}}
{{--						<li> Assign the product to a Collection*</li>--}}
{{--						<li> Assign tags to the product (comma separated)*</li>--}}
{{--						<li> Assign/Add a product Type for the imported product.*</li>--}}

{{--						<li> Enter/change the description of the product.*</li>--}}
{{--						<li> Set if the Product is a Prime Product! (This is automatically set by the app however you can change it if you want or if the info is not accurate).</li>--}}
{{--						<li> Select the product images you want. </li>--}}
{{--						<li> Review all product Variants details and price. </li>--}}
{{--						<li> Set the Price Increase: Set the percentage (%) of the price you want to Publish in your store. Please be aware of and read: <a href="https://sellercentral.amazon.com/gp/help/external/201808410?language=en-US&ref=mpbc_201808430_cont_201808410" target="_blank">AMAZON POLICY ON DROPSHIPPING</a></li>--}}
{{--						<li> Inventory: Set the quantity of the Inventory you want to show in your Shopify store. Note: Our App does not get the Inventory from the Product in Amazon</li>--}}
{{--						</ul>--}}
{{--						*Optional<br>--}}
{{--						Finally press "Add to My Store" button--}}
{{--				     </div>--}}
{{--				  </div>--}}
{{--			 </div>--}}




{{--			 			<div class="card">--}}
{{--				<div class="card-header" id="headingH">--}}
{{--					<h2 class="mb-0">--}}
{{--					<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseH" aria-expanded="true" aria-controls="collapseH">--}}
{{--					How to edit/manage my imported products ?--}}
{{--                    </button>--}}
{{--				    </h2>--}}
{{--				</div>--}}

{{--				 <div id="collapseH" class="collapse" aria-labelledby="headingH" data-parent="#accordionExample">--}}
{{--					<div class="card-body">--}}
{{--						To edit the imported products go to the 'My Products' section and find all your added products. Here you can:--}}
{{--							<ul>--}}
{{--							<li>- Open the original Amazon/Walmart product</li>--}}
{{--							<li>- Open the product in your Shopify Store</li>--}}
{{--							<li>- Change your Shopify product price whether by entering a new price or by changing the percentage in difference with the Amazon/Walmart product price.</li>--}}
{{--							<li>- Edit the product in Shopify editor.</li>--}}
{{--							<li>- You can delete the product from your store.</li>--}}
{{--							</ul>--}}
{{--				     </div>--}}
{{--				  </div>--}}
{{--			 </div>--}}




{{--			<div class="card">--}}
{{--				<div class="card-header" id="headingJ">--}}
{{--					<h2 class="mb-0">--}}
{{--					<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseJ" aria-expanded="true" aria-controls="collapseJ">--}}
{{--				      How do I uninstall Amazone Dropshipper ?--}}
{{--                    </button>--}}
{{--				    </h2>--}}
{{--				</div>--}}

{{--				 <div id="collapseJ" class="collapse" aria-labelledby="headingJ" data-parent="#accordionExample">--}}
{{--					<div class="card-body">--}}
{{--						   Just as any other app in shopify, go to your Shopify administration panel click on the "Apps" section and click Uninstall.--}}
{{--				     </div>--}}
{{--				  </div>--}}
{{--			 </div>--}}


{{--			    <H4>About Clients Orders Handling and Fulfillment </h4>--}}


{{--			<div class="card">--}}
{{--				<div class="card-header" id="headingI">--}}
{{--					<h2 class="mb-0">--}}
{{--					<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseI" aria-expanded="true" aria-controls="collapseI">--}}
{{--					What happens and What to do when you receive an order for a product imported with our app ?--}}
{{--                    </button>--}}
{{--				    </h2>--}}
{{--				</div>--}}

{{--				 <div id="collapseI" class="collapse" aria-labelledby="headingI" data-parent="#accordionExample">--}}
{{--					<div class="card-body">--}}
{{--				    1) <b>If you receive an order for a product you added through our app and if you decided to dropship your products from Amazon</b>: we help you to manage this order by providing you all the info you need, however <u>we are not involved in any way in the process of buying or delivering the product to your client.</u> You (the user) are responsible for placing the order in Amazon or in Walmart and shipping to your client (see below):<br>--}}
{{--                   <br>--}}
{{--                    If you want to review the orders you have received for the products you have added through our app just go to the app "Orders" screen and:<br><br>--}}
{{--						 <ul>--}}
{{--						 <li>Click in the product source link to open the original product in Amazon/Walmart.</li>--}}
{{--						 <li>Proceed to add the product to your cart.</li>--}}
{{--						 <li>The product delivery address is your client's address and details. Just go to our app ORDERS section and COPY each required field easily: Client Name, Address, Zip Code, Phone Number. </li>--}}
{{--						 <li>Complete the order and you are done!. </li>--}}
{{--						 <li>  To come soon: We are developing an auto-order from Amazon! Keep posted</li>--}}
{{--						<br>--}}
{{--						IMPORTANT NOTE: <b> For dropshippers</b>, Amazone DropShipper has nothing to do with buying, fulfilling or delivering products to your clients. If you receive an order for a product, you (the app user) are responsible for placing the order in Amazon or in Walmart and shipping to your client. For ore info please be aware of and read: <span style="font-weight:bold"><a href="https://sellercentral.amazon.com/gp/help/external/201808410?language=en-US&ref=mpbc_201808430_cont_201808410" target="_blank">AMAZON POLICY ON DROPSHIPPING</a></span></li><br><br>--}}
{{--						</ul>--}}
{{--					2) <b> If you import for Amazon Affiliate Program</b>, Then your store visitors will not place any order on your site since they will be redirected from your product page to the Amazon Product page (by using our Amazon Affiliate Button), from there, once they place an order, Amazon will take care of the shipping, warranty and any return! and you will receive your referral commission ($) from Amazon . <br><br>For more info see <a href="AmazonAssociatesHelp.php">AmazoneDropShipper Affiliate/Associate Learning Page</a></li>--}}

{{--				     </div>--}}
{{--				  </div>--}}
{{--			 </div>--}}


{{--			<div class="card">--}}
{{--				<div class="card-header" id="headingK">--}}
{{--					<h2 class="mb-0">--}}
{{--					<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseK" aria-expanded="true" aria-controls="collapseK">--}}
{{--					Is Amazone DropShipper responsible for my store orders and delivering products to clients ?--}}
{{--                    </button>--}}
{{--				    </h2>--}}
{{--				</div>--}}

{{--				 <div id="collapseK" class="collapse" aria-labelledby="headingK" data-parent="#accordionExample">--}}
{{--					<div class="card-body">--}}
{{--					No. Amazone DropShipper has nothing to do with buying, fulfilling or delivering products to your clients. Our App just help merchants importing products to their site. App users are responsible for their store's orders.--}}
{{--				     </div>--}}
{{--				  </div>--}}
{{--			 </div>--}}

{{--			    <H4>Amazon Associates/Affiliate Program + AmazoneDropShipper</h4>--}}


{{--			<div class="card">--}}
{{--				<div class="card-header" id="headingL">--}}
{{--					<h2 class="mb-0">--}}
{{--					<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseL" aria-expanded="true" aria-controls="collapseL">--}}
{{--					What is and how Amazon Affiliates/Associate Program work ?--}}
{{--                    </button>--}}
{{--				    </h2>--}}
{{--				</div>--}}

{{--				 <div id="collapseL" class="collapse" aria-labelledby="headingL" data-parent="#accordionExample">--}}
{{--					<div class="card-body">--}}
{{--					To understand how it works and how to make money with Amazon Affiliate/Associate program, please read a complete <a href="AmazonAssociatesHelp.php">AmazoneDropShipper Affiliate/Associate Learning Page</a>. <a href="AmazonAssociatesHelp.php">Click here</a>--}}
{{--				     </div>--}}
{{--				  </div>--}}

{{--        </div>--}}



{{--			<div class="card">--}}
{{--				<div class="card-header" id="headingM">--}}
{{--					<h2 class="mb-0">--}}
{{--					<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseM" aria-expanded="true" aria-controls="collapseM">--}}
{{--					What are the Steps to Earn Money with Amazon Associate/Affiliate Program referral fees ?--}}
{{--                    </button>--}}
{{--				    </h2>--}}
{{--				</div>--}}

{{--				 <div id="collapseM" class="collapse" aria-labelledby="headingM" data-parent="#accordionExample">--}}
{{--					<div class="card-body">--}}
{{--					Find a complete <a href="AmazonAssociatesHelp.php">AmazoneDropShipper Affiliate/Associate Learning Page</a>. <a href="AmazonAssociatesHelp.php">Click here</a>--}}
{{--				     </div>--}}
{{--				  </div>--}}
{{--					</div>--}}

{{--				<H4> Troubleshoot / Having Problems ?</H4>--}}


{{--			<div class="card">--}}
{{--				<div class="card-header" id="headingN">--}}
{{--					<h2 class="mb-0">--}}
{{--					<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseN" aria-expanded="true" aria-controls="collapseN">--}}
{{--					I am getting an error while importing/adding a product!.--}}
{{--                    </button>--}}
{{--				    </h2>--}}
{{--				</div>--}}

{{--				 <div id="collapseN" class="collapse" aria-labelledby="headingN" data-parent="#accordionExample">--}}
{{--					<div class="card-body">--}}
{{--					If you get an error please send us an email to info@AmazoneDropshipping.com with the product URL you cannot add so we can review/test it.  It is expected that Amazon will change its layout from time to time, that's why we are always updating our app.--}}
{{--				     </div>--}}
{{--				  </div>--}}
{{--				</div>--}}

{{--			<div class="card">--}}
{{--				<div class="card-header" id="headingO">--}}
{{--					<h2 class="mb-0">--}}
{{--					<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseO" aria-expanded="true" aria-controls="collapseO">--}}
{{--					Can't see the products in MANAGE PRODUCTS section or can't see orders placed in the VIEW ORDERS section!--}}
{{--                    </button>--}}
{{--				    </h2>--}}
{{--				</div>--}}

{{--				 <div id="collapseO" class="collapse" aria-labelledby="headingO" data-parent="#accordionExample">--}}
{{--					<div class="card-body">--}}
{{--					Please try refreshing the browser tab or simply reload the app.  If doesn't work email us at info@AmazoneDropshipping.com--}}
{{--				     </div>--}}
{{--				  </div>--}}
{{--			</div>--}}



@endsection
<!-- End HTML editing here -->
</div>


