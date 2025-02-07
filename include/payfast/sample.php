
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>POST Transaction Form</title>
</head>
    <body>
        <form name="payfast_transaction" method="post" action= "https://ipg1.apps.net.pk/Ecommerce/api/Transaction/PostTransaction" id="myForm" >
            <input type="hidden" name="MERCHANT_ID" value="4498"/>
            <input type="hidden" name="MERCHANT_NAME" value="audionic-the-sound-master.myshopify.com"/>
            <input type="hidden" name="TOKEN" value="lYmJQjC7tJ3qtdhSbmMQxCOOK6Rd0kSnVMkwEVdqH_s" />
            <input type="hidden" name="TXNAMT" value="5490.0" />
            <input type="hidden" name="PROCCODE" value="00" />
            <input type="hidden" name="CUSTOMER_MOBILE_NO" value="03000000000" />
            <input type="hidden" name="CUSTOMER_EMAIL_ADDRESS" value="rahia307@gmail.com" />
            <input type="hidden" name="SIGNATURE" value="" />
            <input type="hidden" name="VERSION" value="MY_VER_1.0" />
            <input type="hidden" name="TXNDESC" value="Shopping From Audionic" />
            <input type="hidden" name="SUCCESS_URL" value="https://shopifyapp.apps.net.pk/payfast/place-order" />
            <input type="hidden" name="FAILURE_URL" value="https://shopifyapp.apps.net.pk/payfast/place-order" />
            <input type="hidden" name="BASKET_ID" value="rcYLhbUgeNIqHAnz5pQgJEFIn" />
            <input type="hidden" name="ORDER_DATE" value="2022-10-12" />
            <input type="hidden" name="CHECKOUT_URL" value="https://shopifyapp.apps.net.pk/payfast/place-order" />
            <input type="hidden" name="CURRENCY_CODE" value="PKR" />

        </form>
        <script>
            document.getElementById("myForm").submit()
        </script>

    <script defer src="https://static.cloudflareinsights.com/beacon.min.js/v652eace1692a40cfa3763df669d7439c1639079717194" integrity="sha512-Gi7xpJR8tSkrpF7aordPZQlW2DLtzUlZcumS8dMQjwDHEnw9I7ZLyiOj/6tZStRBGtGgN6ceN6cMH8z7etPGlw==" data-cf-beacon='{"rayId":"758fab807d336c77","token":"bc84381afbd64c26861463f6ddfd8d9c","version":"2022.8.1","si":100}' crossorigin="anonymous"></script>
</body>
</html>