<?php

namespace OxidProfessionalServices\PayPal\Api\Model\Partner;

use JsonSerializable;
use OxidProfessionalServices\PayPal\Api\Model\BaseModel;

/**
 * The individual owner of the account.
 */
class IndividualOwner extends Person implements JsonSerializable
{
    use BaseModel;

    const TYPE_PRIMARY = 'PRIMARY';

    /**
     * @var string
     * Role of the person party played in the account.
     *
     * use one of constants defined in this class to set the value:
     * @see TYPE_PRIMARY
     */
    public $type;
}
