<?php

namespace OxidProfessionalServices\PayPal\Api\Model\Orders;

/**
 * Any additional payment instructions for PayPal Commerce Platform customers. Enables features for the PayPal Commerce Platform, such as delayed disbursement and collection of a platform fee. Applies during order creation for captured payments or during capture of authorized payments.
 */
class PaymentInstruction
{
	/** @var array */
	public $platform_fees;

	/** @var OxidProfessionalServices\PayPal\Api\Model\DisbursementMode */
	public $disbursement_mode;
}
