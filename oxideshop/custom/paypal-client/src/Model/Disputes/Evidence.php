<?php

namespace OxidProfessionalServices\PayPal\Api\Model\Disputes;

/**
 * A merchant- or customer-submitted evidence document.
 */
class Evidence implements \JsonSerializable
{
    /** @var string */
    public $evidence_type;

    /** @var EvidenceInfo */
    public $evidence_info;

    /** @var array */
    public $documents;

    /** @var string */
    public $notes;

    /** @var string */
    public $source;

    /** @var string */
    public $date;

    /** @var string */
    public $item_id;

    public function jsonSerialize()
    {
        return array_filter((array) $this,static function($var){return isset($var);});
    }
}
