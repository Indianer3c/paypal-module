<?php

namespace OxidProfessionalServices\PayPal\Api\Model\Orders;

use JsonSerializable;
use OxidProfessionalServices\PayPal\Api\Model\BaseModel;

/**
 * Payment Directives for transaction.
 */
class PaymentDirectives implements JsonSerializable
{
    use BaseModel;

    const DISBURSEMENT_TYPE_INSTANT = 'INSTANT';
    const DISBURSEMENT_TYPE_DELAYED = 'DELAYED';
    const LIABILITY_TYPE_FULL = 'FULL';
    const LIABILITY_TYPE_PARTIAL = 'PARTIAL';
    const CURRENCY_RECEIVING_DIRECTIVE_ACCEPT = 'ACCEPT';
    const CURRENCY_RECEIVING_DIRECTIVE_DENY = 'DENY';
    const CURRENCY_RECEIVING_DIRECTIVE_HOLD = 'HOLD';
    const CURRENCY_RECEIVING_DIRECTIVE_ACCEPT_OPEN = 'ACCEPT_OPEN';

    /**
     * @var string
     * Disbursement type.
     *
     * use one of constants defined in this class to set the value:
     * @see DISBURSEMENT_TYPE_INSTANT
     * @see DISBURSEMENT_TYPE_DELAYED
     */
    public $disbursement_type;

    /** @var string */
    public $linked_group_id;

    /** @var string */
    public $settlement_account_number;

    /** @var string */
    public $loss_account_number;

    /**
     * @var string
     * Liability type defined by PayPal Risk.
     *
     * use one of constants defined in this class to set the value:
     * @see LIABILITY_TYPE_FULL
     * @see LIABILITY_TYPE_PARTIAL
     */
    public $liability_type;

    /** @var boolean */
    public $special_deny;

    /** @var boolean */
    public $allow_duplicate_invoice_id;

    /** @var array<PolicyDirective> */
    public $policy_directives;

    /** @var array<PaymentMethodDirective> */
    public $payment_method_directives;

    /** @var array<PricingDirective> */
    public $pricing_directives;

    /**
     * @var AuthorizationDirectives
     * Auth directives for the transaction.
     */
    public $authorization_directives;

    /**
     * @var string
     * Currency receiving type defines the options when receiving payment in a currency not held by the reciver.
     *
     * use one of constants defined in this class to set the value:
     * @see CURRENCY_RECEIVING_DIRECTIVE_ACCEPT
     * @see CURRENCY_RECEIVING_DIRECTIVE_DENY
     * @see CURRENCY_RECEIVING_DIRECTIVE_HOLD
     * @see CURRENCY_RECEIVING_DIRECTIVE_ACCEPT_OPEN
     */
    public $currency_receiving_directive;

    /** @var boolean */
    public $immediate_payment_required;
}
