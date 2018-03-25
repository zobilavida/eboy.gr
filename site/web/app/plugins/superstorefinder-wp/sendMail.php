<?php
include("ssf-wp-inc/includes/ssf-wp-env.php");
$name=$_POST['name'];
$phone=$_POST['phone'];
$email=$_POST['email'];
$message=$_POST['message'];

$to = $_POST['rcvEmail'];
$subject = $_POST['subject']. ' enquiry';
$body = 'Sender Name: '.$name.'<br/>';
$body .= 'Sender Email: '.$email.'<br/>';
if(!empty($phone)){
	$body .= 'Telephone: '.$phone.'<br/>';
}
$body .= 'Message: '.$message.'<br/>';
$headers = array('Content-Type: text/html; charset=UTF-8');
wp_mail( $to, $subject, $body, $headers );
?>