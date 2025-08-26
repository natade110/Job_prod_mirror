<?php 

error_reporting(1);

include('./stripe_lib_old/Stripe.php');

?>

<form action="stripe_charge.php" method="POST">
  <script
    src="https://checkout.stripe.com/checkout.js" class="stripe-button"
    data-key="pk_test_xpLRTDHwKxSR1jcPPYXQxIhY"
    data-amount="1099"
    data-name="AtlanticThai Premium Sales (Dublin) LTD."
    data-description="Widget"
    data-image="https://s3.amazonaws.com/stripe-uploads/acct_1ABNDPBbzc62hv24merchant-icon-1493278216792-atlanticthai-logo-emblem-bigger-250.gif"
    data-locale="auto"
    data-currency="eur">
  </script>
</form>