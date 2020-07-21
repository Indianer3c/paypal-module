<?php

namespace OxidProfessionalServices\PayPal\Model\Payments;

/**
 * Basic vault instruction specification that can be extended by specific payment sources that supports vaulting.
 */
class BasevaultInstructionparameters
{
	/** @var string */
	public $confirm_payment_token;
}
