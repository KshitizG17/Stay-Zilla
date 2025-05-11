<?php

require('razorpay-api/config.php');

require('admin/inc/db_config.php');
require('admin/inc/essentials.php');


date_default_timezone_set("Asia/Kolkata");

session_start();

$con = mysqli_connect($host, $username, $password, $dbname);
if($con){
    alert('success',' Database conneected successfully!');
}
else{
    alert('error',' Database not conneected successfully!');
}

require('razorpay-api/razorpay-php/Razorpay.php');
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

$success = true;

$error = "Payment Failed";

if (empty($_POST['razorpay_payment_id']) === false)
{
    $api = new Api($keyId, $keySecret);

    try
    {
        // Please note that the razorpay order ID must
        // come from a trusted source (session here, but
        // could be database or something else)
        $attributes = array(
            'razorpay_order_id' => $_SESSION['razorpay_order_id'],
            'razorpay_payment_id' => $_POST['razorpay_payment_id'],
            'razorpay_signature' => $_POST['razorpay_signature']
        );

        $api->utility->verifyPaymentSignature($attributes);
    }
    catch(SignatureVerificationError $e)
    {
        $success = false;
        $error = 'Razorpay Error : ' . $e->getMessage();
    }
}

if ($success === true)
{
    $razorpay_order_id = $_SESSION['razorpay_order_id'];
    $razorpay_payment_id = $_POST['razorpay_payment_id'];

    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $name = $_POST['name'];
    $phonenum = $_POST['phonenum'];
    $address = $_POST['address'];

    $sql1 = "INSERT INTO `booking_order`(`user_id`, `room_id`, `check_in`, `check_out`, `booking_status`, `order_id`, `trans_id`, `trans_amt`,
          `trans_status`) VALUES ('{$_SESSION['uId']}', '{$_SESSION['room']['id']}', '{$checkin}', '{$checkout}',
           'booked', '{$razorpay_order_id}', '{$razorpay_payment_id}', '{$_SESSION['room']['payment']}', 'Transaction Successful')";

    if (mysqli_query($con, $sql1))
     {
        $booking_id = mysqli_insert_id($con);
    
        // Use the retrieved `$booking_id` in the subsequent query
        $sql2 = "INSERT INTO `booking_details`(`booking_id`, `room_name`, `price`, `total_pay`, `user_name`, `phonenum`, `address`)
            VALUES ('{$booking_id}', '{$_SESSION['room']['name']}', '{$_SESSION['room']['price']}', '{$_SESSION['room']['payment']}',
             '{$name}', '{$phonenum}', '{$address}')";
        // Execute the query for `booking_details` insertion
        if (mysqli_query($con, $sql2)) {
            // Success
            redirect('pay_status.php?order='.$razorpay_order_id);
        } else {
            // Error inserting into `booking_details`
            echo "Error: " . mysqli_error($con);
        }
     } 
    else {
        // Error inserting into `booking_order`
        echo "Error: " . mysqli_error($con);
    }  
}
else{
    header("Location: index.php");
    exit;
}

?>
