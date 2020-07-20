<?php

namespace OxidProfessionalServices\PayPal\Model\Orders;

/**
 * Client configuration that captures the product flows and specific experiences that a user completes a paypal transaction.
 */
class Clientconfiguration
{
	/** @var string */
	public $product_code;

	/** @var string */
	public $product_feature;

	/** @var string */
	public $api;

	/** @var string */
	public $integration_artifact;
}
