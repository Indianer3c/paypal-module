<?php

namespace OxidProfessionalServices\PayPal\Api\Model\Disputes;

/**
 * A set of filters that you can use to filter the disputes in the response.
 */
class Filter implements \JsonSerializable
{
    /** @var string */
    public $email;

    /** @var string */
    public $name;

    /** @var string */
    public $reasons;

    /** @var string */
    public $statuses;

    /** @var string */
    public $create_time_before;

    /** @var string */
    public $create_time_after;

    /** @var string */
    public $update_time_before;

    /** @var string */
    public $update_time_after;

    /** @var string */
    public $response_due_date_before;

    /** @var string */
    public $response_due_date_after;

    /** @var Money */
    public $dispute_amount_gte;

    /** @var Money */
    public $dispute_amount_lte;

    public function jsonSerialize()
    {
        return array_filter((array) $this,static function($var){return isset($var);});
    }
}
