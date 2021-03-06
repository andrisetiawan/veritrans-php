<?php
if(empty($_POST)) 
{
	echo "Empty data.";
	exit;
}

require '../veritrans.php';

//TODO: Change with your actual merchant id and merchant hash key
$MERCHANT_ID = 'T100000000000001001862';
$MERCHANT_HASH_KEY = '21565170a5a85543b98b0e34071656c104f31266ff436be07edb2d5a33983f97';

$veritrans = new Veritrans();
$veritrans->merchant_id       = $MERCHANT_ID;
$veritrans->merchant_hash_key = $MERCHANT_HASH_KEY;

//TODO: Change with your actual order_id.
$veritrans->order_id          = 'order'.rand();

$veritrans->billing_address_different_with_shipping_address = 1;
$veritrans->required_shipping_address = 1;

// Billing info. [Optional]
$veritrans->first_name 	= $_POST['billing_first_name'];
$veritrans->last_name 	= $_POST['billing_last_name'];
$veritrans->address1 	= $_POST['billing_address1'];
$veritrans->address2 	= $_POST['billing_address2'];
$veritrans->city 		= $_POST['billing_city'];
$veritrans->country_code= "IDN";
$veritrans->postal_code = $_POST['billing_postal_code'];
$veritrans->phone 		= $_POST['billing_phone'];

// Shipping info. [Required if required_shipping_address = '1']
$veritrans->shipping_first_name 	= $_POST['shipping_first_name'];
$veritrans->shipping_last_name 		= $_POST['shipping_last_name'];
$veritrans->shipping_address1 		= $_POST['shipping_address1'];
$veritrans->shipping_address2 		= $_POST['shipping_address2'];
$veritrans->shipping_city 			= $_POST['shipping_city'];
$veritrans->shipping_country_code 	= "IDN";
$veritrans->shipping_postal_code 	= $_POST['shipping_postal_code'];
$veritrans->shipping_phone 			= $_POST['shipping_phone'];
$veritrans->email 					= $_POST['email'];

// Configure redirect url. [Optional. Can also be set at Merchant Administration Portal(MAP)]
$veritrans->finish_payment_return_url 	= "http://yoursite.com/finish_payment";
$veritrans->unfinish_payment_return_url	= "http://yahoo.com/unfinish_payment";
$veritrans->error_payment_return_url	= "http://apple.com/error_payment";

// Payment options
$veritrans->enable_3d_secure	= '1';
$veritrans->bank 				= "bni";
$veritrans->installment_banks 	= ["bni", "cimb"];
$veritrans->promo_bins			= ["411111", "444444"];
$veritrans->point_banks			= ["bni", "cimb"];
$veritrans->payment_methods		= ["credit_card", "mandiri_clickpay"];
$veritrans->installment_terms   = array(
	'bni' => ["3","12"],
	'cimb' => ["3", "6", "12"]
	);

// Set commodity items. This is just sample items.
// TODO: Change with your actual items.
$items = array(
			array(
				"item_id" => 'itemsatu',
				"price" => '250000',
				"quantity"   => '1',
				"item_name1" => 'sepatu',
				"item_name2" => 'Shoes'
			),
			array(
				"item_id" => 'itemdua',
				"price" => '500000',
				"quantity"   => '2',
				"item_name1" => 'Tas',
				"item_name2" => 'Bag'
			),
		);

$veritrans->items = $items;

//Call Veritrans VT-Web API Get Token
try {
	$keys = $veritrans->getTokens();

	if(!$keys) {
		print_r($veritrans->errors);
		exit();
	} else {
		//Save this token_merchant on your database to be used for checking veritrans notification response.
		$token_merchant = $keys['token_merchant'];

		//Use this token_browser for redirecting customer to Veritrans payment page.
		$token_browser = $keys['token_browser'];
	}
} catch (Exception $e) {
	var_dump($e);
}

//Redirect customer to Veritrans payment page.

?>

<!DOCTYPE html>
<html>
<head>
	<script language="javascript" type="text/javascript">
	<!--
	function onloadEvent() {
	  document.form_auto_post.submit();
	}
	//-->
	</script>
</head>

<body onload="onloadEvent();">
<form action="<?php echo Veritrans::PAYMENT_REDIRECT_URL ?>" method="post" name='form_auto_post'>
<input type="hidden" name="MERCHANT_ID" value="<?php echo $veritrans->merchant_id ?>" />
<input type="hidden" name="ORDER_ID" value="<?php echo $veritrans->order_id ?>" />
<input type="hidden" name="TOKEN_BROWSER" value="<?php echo $token_browser ?>" />
<span>Please wait. You are being redirected to Veritrans payment page...</span>
</form>

</body>