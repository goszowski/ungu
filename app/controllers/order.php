<?php
// load_view('order');
// exit;

if(isset($_GET['paid']))
{
	$parameters = NULL;
	parse_str($_POST['payment'], $parameters);

	$order = $parameters['order'];
	$state = $parameters['state'];


	if($state == 'test' or $state == 'ok')
	{
		$Statement = new Statement($connection);
		$Statement->execute("UPDATE dbm_nfv_order SET paid=1 WHERE token='{$order}' LIMIT 1");

		//load_view('order', array('token'=>$order));
		header('location: /your-order-is-successfully-issued');
	}
	else
	{
		header('location: /');
	}


	exit;
}



if(isset($_GET['confirm']))
{

	$contact_name = $_POST['contact_name'];
	$contact_phone = $_POST['contact_phone'];
	$delivery = $_POST['delivery'];

	$token = $_POST['token'];

	$contact_name = str_replace("'", "''", $contact_name);
	$contact_phone = str_replace("'", "''", $contact_phone);
	$delivery = str_replace("'", "''", $delivery);

	$Statement = new Statement($connection);
	$sql = "UPDATE dbm_nfv_order SET contact_name='{$contact_name}', contact_phone='{$contact_phone}', delivery='{$delivery}' WHERE token='{$token}' LIMIT 1";
	$Statement->execute($sql);

	header('location: /your-order-is-successfully-issued');

	exit;
}













$do = $_POST['do'];

if($do == 'create_order')
{// 281

	// Отримання даних
	$product_id = $_POST['product_id'];
	$size_id = $_POST['size_id'];
	$delivery_option = $_POST['delivery_option'];
	$payment_option = $_POST['payment_option'];

	$contact_name = $_POST['contact_name'];
	$country = $_POST['country'];
	$surname = $_POST['surname'];
	$city = $_POST['city'];
	$contact_phone = $_POST['contact_phone'];
	$zip = $_POST['zip'];
	$email = $_POST['email'];
	$adress = $_POST['adress'];
	// ======================================


	// Завантаження товару
	$product = load_model('product');
	$product_item = $product->getById($product_id);
	$product_name = $product_item['name'];
	$product_price = $product_item['price_with_sale'] ? $product_item['price_with_sale'] : $product_item['price'];
	$product_reference = $product_item['reference'];
	// ======================================

	// Підготовка даних до інсерту
	$insertData = [
		'product'=>$product_id,
		'size'=>$size_id,
		'cost'=>$product_price,
		'paid'=>0,
		'delivered'=>0,
		'order_date'=>date('Y-m-d H:i:s'),
		'delivery_option'=>$delivery_option,
		'payment_option'=>$payment_option,
		'contact_name'=>$contact_name,
		'country'=>$country,
		'surname'=>$surname,
		'city'=>$city,
		'contact_phone'=>$contact_phone,
		'zip'=>$zip,
		'email'=>$email,
		'adress'=>$adress,
	];

	if($payment_option == 281) {
		// --- ОПЛАТА ЧЕРЕЗ ПРИВАТ24 --- //
		// Завантаження даних по мерчанту
		$pb_data = getSimpleList('index', array('merchant_id', 'merchant_pwd'), false, false, 1)[0];
		$merchant = $pb_data['merchant_id'];
		$merchant_pwd = $pb_data['merchant_pwd'];
		// ======================================

		// create token
		$token = sha1(md5( date('Y-m-d H:i:s') . mt_rand(100, 999) . $product_id . $size_id . $_SERVER['REMOTE_ADDR'] ));
		// ======================================

		// create $signature
		$payment = "amt={$product_price}&ccy=UAH&details={$product_name}&ext_details={$product_reference}&pay_way=privat24&order={$token}&merchant={$merchant}";
		$signature = sha1(md5($payment.$merchant_pwd));
		// ======================================

		$insertData['token'] = $token;

		// --- [*** END ***] ОПЛАТА ЧЕРЕЗ ПРИВАТ24 --- //
	}



	//_dump($insertData);

	// Створюємо замовлення в базі
	$shortname = md5(date('Y-m-d H:i:s'));
	//exit;
	_addNode('order', $shortname, 28, $shortname, $insertData);

	if($payment_option == 281) {
	?>
	<form action="https://api.privatbank.ua/p24api/ishop" method="POST" accept-charset="UTF-8" name="pb_form">
		<input type="hidden" name="amt" value="<?=$product_price?>"/>
		<input type="hidden" name="ccy" value="UAH" />
		<input type="hidden" name="merchant" value="<?=$merchant?>" />
		<input type="hidden" name="order" value="<?=$token?>" />
		<input type="hidden" name="details" value="<?=$product_name?>" />
		<input type="hidden" name="ext_details" value="<?=$product_reference?>" />
		<input type="hidden" name="pay_way" value="privat24" />
		<input type="hidden" name="return_url" value="http://ungu.club/order?paid" />
		<input type="hidden" name="server_url" value="http://ungu.club/order?paid" />
		<input type="hidden" name="signature" value="<?=$signature?>" />
	</form>
	<script>
		window.pb_form.submit();
	</script>
	<?
	}
	else {
		header('location: /your-order-is-successfully-issued');
	}
}
