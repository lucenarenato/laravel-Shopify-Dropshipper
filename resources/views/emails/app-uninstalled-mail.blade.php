<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <title>Mozilla</title>

    <style>

        p{
            margin:0 0 16px;
        }
        h4{
            margin:0 0 12px;
        }
        .title{
            color:#2d2f69;
            font-size:18px;
        }
        .email_logo_top{
            display: block;
            margin: 20px auto;
            width: 70px;
        }

    </style>
</head>

<body bgcolor="#f5f5f5" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" offset="0" style="padding:70px 0 70px 0;">
<table width="600" height="auto" align="center" cellpadding="0" cellspacing="0" style="background-color:#fdfdfd; border:1px solid #dcdcdc; border-radius:3px !important;">
    <tr>
        <td width="600" height="auto" bgcolor="#000" border="0" style="padding:36px 48px; display:block; margin: 0px auto;">
            <h1 style="color:#ffffff; font-family:&quot; Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif; font-size:30px; line-height:150%; font-weight:300; margin:0; text-align:left;">AmaZone DropShipper</h1>
        </td>
    </tr>
    <tr>
        <td ><img src="{{asset('images/AZ-email-Logo.png')}}" class="email_logo_top" alt="" width="200px"></td>
    </tr>
    <tr>
        <td width="600" bgcolor="#fdfdfd" border="0" style="color:#737373; font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif; font-size:14px; line-height:150%; text-align:left; padding:48px;padding-top: 15px;">
            <p>Hi!</p>

            <p>Is sad to see you go. If you have any suggestion, recommendation or good idea (or something you didn´t like about our app) please reply to this email and let us know!.</p>

            <p>In regards to your application Subscription {{$params['user_plan']}} Plan there is something you should have clear to avoid confusions.  Now that you have uninstalled our app on {{$params['app_uninstall_date']}} you could potentially still be charged for the ongoing month given that you have uninstalled the app during the regular billing cycle, it all depends on what date Shopify bills you. </p>

            <p>Example: You subscribed to a paid plan in our app in January 01 and then you uninstall the app in January 20th  but Shopify charges/bills you every 27th  of the month, then you will see the subscription charge (from our app) only on January 27th  (seven days after you uninstalled the app) the reason for this is that Shopify charges/bills you for your Store and all apps subscriptions in the 27th  of the month, and even when you uninstalled the app on the 20th you still have to pay for the period January 01 to January 20th.</p>

            <p>If you feel that you are being charged for too many days after your uninstall date please email us and we can review your case. We can provide a refund as long as you have not used the complete plan during those days.</p>

            <p>Example: You subscribed to the GOLD plan on January 1st and imported only one product, and then you uninstalled the app in January 5th.  Then you will see the charge on January 27th, (when Shopify bills you). In this case as you imported only 1 product during 4 days then please email us. We can review your case and grant a refund.</p>

            <p>Note: Your app Subscription billing cycle with us starts on the {{$params['first_day_of_paid_plan']}} of the month.</p>

            <p>Hope this helps and hope to see you back soon!</p>

            <a style="color:#2d2f69;" href="www.AmazoneDropShipping.com" target="_blank">www.AmazoneDropShipping.com</a>
            <p>Amazon: US, Canada, Australia, UK, Germany, France, Italy Spain, Mexico and Brazil, Walmart: US</p>

            <p>Thanks.</p>

        </td>
    </tr>

</table>
</body>
</html>
