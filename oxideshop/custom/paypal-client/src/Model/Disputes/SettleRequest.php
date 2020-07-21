<?php

namespace OxidProfessionalServices\PayPal\Model\Disputes;

/**
 * A request to settle a dispute in either the customer's or merchant's favor.
 */
class SettleRequest
{
	/** @var string */
	public $adjudication_outcome;
}
