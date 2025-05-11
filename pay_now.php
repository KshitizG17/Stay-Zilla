<?php

require('admin/inc/db_config.php');
require('admin/inc/essentials.php');

require('razorpay-api/config.php');
require('razorpay-api/razorpay-php/Razorpay.php');


date_default_timezone_set("Asia/Kolkata");

session_start();

// Create the Razorpay Order

use Razorpay\Api\Api;

$api = new Api($keyId, $keySecret);

//
// We create an razorpay order using orders api
// Docs: https://docs.razorpay.com/docs/orders
//

$price = $_SESSION['room']['payment'];

if (isset($_POST['pay_now'])) {
    // Access the form data
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $name = $_POST['name'];
    $phonenum = $_POST['phonenum'];
    $address = $_POST['address'];
  
    // Use the form data as needed
    // ...

  }

$orderData = [
    'receipt'         => 3456,
    'amount'          => $price * 100, // 2000 rupees in paise
    'currency'        => 'INR',
    'payment_capture' => 1 // auto capture
];

$razorpayOrder = $api->order->create($orderData);

$razorpayOrderId = $razorpayOrder['id'];

$_SESSION['razorpay_order_id'] = $razorpayOrderId;

$displayAmount = $amount = $orderData['amount'];

if ($displayCurrency !== 'INR')
{
    $url = "https://api.fixer.io/latest?symbols=$displayCurrency&base=INR";
    $exchange = json_decode(file_get_contents($url), true);

    $displayAmount = $exchange['rates'][$displayCurrency] * $amount / 100;
}


$data = [
    "key"               => $keyId,
    "amount"            => $amount,
    "name"              => "STAY-Zilla",
    "description"       => "LIVE IN ROYALITY",
    "image"             => "images/SZ.jpg",
    "prefill"           => [
    "name"              => $name,
    "email"             => 'email@gmail.com',
    "contact"           => '77777777' ,
    ],
    "notes"             => [
    "address"           => "Hello World",
    "merchant_order_id" => "12312321",
    ],
    "theme"             => [
    "color"             => "#F37254"
    ],
    "order_id"          => $razorpayOrderId,
];

if ($displayCurrency !== 'INR')
{
    $data['display_currency']  = $displayCurrency;
    $data['display_amount']    = $displayAmount;
}

$json = json_encode($data);


?>

<form action="pay_verify.php" method="POST">
  <script
    src="https://checkout.razorpay.com/v1/checkout.js"
    data-key="<?php echo $data['key']?>"
    data-amount="<?php echo $data['amount']?>"
    data-currency="INR"
    data-name="<?php echo $data['name']?>"
    data-image="<?php echo $data['image']?>"
    data-description="<?php echo $data['description']?>"
    data-prefill.name="<?php echo $data['prefill']['name']?>"
    data-prefill.email="<?php echo $data['prefill']['email']?>"
    data-prefill.contact="<?php echo $data['prefill']['contact']?>"
    data-notes.shopping_order_id="3456"
    data-order_id="<?php echo $data['order_id']?>"
    <?php if ($displayCurrency !== 'INR') { ?> data-display_amount="<?php echo $data['display_amount']?>" <?php } ?>
    <?php if ($displayCurrency !== 'INR') { ?> data-display_currency="<?php echo $data['display_currency']?>" <?php } ?>
  >
  </script>
  <!-- Any extra fields to be submitted with the form but not sent to Razorpay -->
  <input type="hidden" name="checkin" value="<?php echo $checkin; ?>">
  <input type="hidden" name="checkout" value="<?php echo $checkout; ?>">
  <input type="hidden" name="name" value="<?php echo $name; ?>">
  <input type="hidden" name="phonenum" value="<?php echo $phonenum; ?>">
  <input type="hidden" name="address" value="<?php echo $address; ?>">
  
</form>




