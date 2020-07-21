<?php

namespace OxidProfessionalServices\PayPal\Api\Model\Orders;

/**
 * Information used to pay Bancontact.
 */
class Bancontact
{
	/** @var OxidProfessionalServices\PayPal\Api\Model\FullName */
	public $name;

	/** @var OxidProfessionalServices\PayPal\Api\Model\CountryCode */
	public $country_code;

	/** @var OxidProfessionalServices\PayPal\Api\Model\Bic */
	public $bic;

	/** @var OxidProfessionalServices\PayPal\Api\Model\IbanLastChars */
	public $iban_last_chars;

	/** @var string */
	public $card_last_digits;
}
