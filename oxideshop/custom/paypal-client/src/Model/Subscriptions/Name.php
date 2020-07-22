<?php

namespace OxidProfessionalServices\PayPal\Api\Model\Subscriptions;

use JsonSerializable;
use OxidProfessionalServices\PayPal\Api\Model\BaseModel;

/**
 * The name of the party.
 *
 * generated from: merchant.CommonComponentsSpecification-v1-schema-common_components-v3-schema-json-openapi-2.0-name.json
 */
class Name implements JsonSerializable
{
    use BaseModel;

    /**
     * @var string
     * The prefix, or title, to the party's name.
     *
     * maxLength: 140
     */
    public $prefix;

    /**
     * @var string
     * When the party is a person, the party's given, or first, name.
     *
     * maxLength: 140
     */
    public $given_name;

    /**
     * @var string
     * When the party is a person, the party's surname or family name. Also known as the last name. Required when the
     * party is a person. Use also to store multiple surnames including the matronymic, or mother's, surname.
     *
     * maxLength: 140
     */
    public $surname;

    /**
     * @var string
     * When the party is a person, the party's middle name. Use also to store multiple middle names including the
     * patronymic, or father's, middle name.
     *
     * maxLength: 140
     */
    public $middle_name;

    /**
     * @var string
     * The suffix for the party's name.
     *
     * maxLength: 140
     */
    public $suffix;

    /**
     * @var string
     * DEPRECATED. The party's alternate name. Can be a business name, nickname, or any other name that cannot be
     * split into first, last name. Required when the party is a business.
     *
     * maxLength: 300
     */
    public $alternate_full_name;

    /**
     * @var string
     * When the party is a person, the party's full name.
     *
     * maxLength: 300
     */
    public $full_name;
}
