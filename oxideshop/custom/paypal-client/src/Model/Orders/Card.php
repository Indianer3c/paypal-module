<?php

namespace OxidProfessionalServices\PayPal\Api\Model\Orders;

/**
 * The payment card to use to fund a payment. Can be a credit or debit card.
 */
class Card
{
	/** @var string */
	public $id;

	/** @var string */
	public $name;

	/** @var string */
	public $number;

	/** @var OxidProfessionalServices\PayPal\Api\Model\DateYearMonth */
	public $expiry;

	/** @var string */
	public $security_code;

	/** @var string */
	public $last_digits;

	/** @var OxidProfessionalServices\PayPal\Api\Model\CardBrand */
	public $card_type;

	/** @var OxidProfessionalServices\PayPal\Api\Model\AddressPortable */
	public $billing_address;

	/** @var array */
	public $authentication_results;

	/** @var OxidProfessionalServices\PayPal\Api\Model\CardAttributes */
	public $attributes;
}
