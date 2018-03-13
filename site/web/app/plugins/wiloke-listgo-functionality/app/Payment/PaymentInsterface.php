<?php
/*
 * Payment Interface
 * @since 1.0
 */
namespace WilokeListGoFunctionality\Payment;
interface PaymentInsterface{

	/*
	 * @param @packageID int the ID of package
	 * @since 1.0
	 */
	public function insertPaymentHistory($packageID);

	/**
	 * @param $status string, It should contain one of the following methods: completed, processing, pending, cancel
	 * @param $token string, If customer pay for PayPal, the token should be returned from PayPal, else we will generate a token for it
	 * @param $aAllInfo array Record all payment process
	 */
	public function updatePaymentHistory($status, $token, $aAllInfo);
}