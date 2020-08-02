<?php

namespace OxidProfessionalServices\PayPal\Api\Model\Disputes;

use JsonSerializable;
use OxidProfessionalServices\PayPal\Api\Model\BaseModel;
use OxidProfessionalServices\PayPal\Api\Model\CommonV3\CommonV3Money;
use Webmozart\Assert\Assert;

/**
 * The partner-provided details that were used for adjudication on the partner's side.
 *
 * generated from: referred-adjudication_info.json
 */
class AdjudicationInfo implements JsonSerializable
{
    use BaseModel;

    /** The customer did not receive the merchandise or service. */
    public const DISPUTE_REASON_MERCHANDISE_OR_SERVICE_NOT_RECEIVED = 'MERCHANDISE_OR_SERVICE_NOT_RECEIVED';

    /** The customer reports that the merchandise or service is not as described. */
    public const DISPUTE_REASON_MERCHANDISE_OR_SERVICE_NOT_AS_DESCRIBED = 'MERCHANDISE_OR_SERVICE_NOT_AS_DESCRIBED';

    /**
     * The currency and amount for a financial transaction, such as a balance or payment due.
     *
     * @var CommonV3Money | null
     */
    public $dispute_amount;

    /**
     * An array of items in the transaction that is in dispute.
     *
     * @var ItemInfo2[] | null
     */
    public $items;

    /**
     * The outcome of the dispute case.
     *
     * @var Outcome | null
     */
    public $outcome;

    /**
     * The extended properties for the dispute. Includes additional information for a dispute category, such as
     * billing disputes, the original transaction ID, correct amount, and so on.
     *
     * @var Extensions2 | null
     */
    public $extensions;

    /**
     * An array of partner-submitted evidences, such as tracking information.
     *
     * @var Evidence2[] | null
     */
    public $evidences;

    /**
     * The reason for the item-level dispute. For information about the required information for each dispute reason
     * and associated evidence type, see <a
     * href="/docs/integration/direct/customer-disputes/integration-guide/#dispute-reasons">dispute reasons</a>.
     *
     * use one of constants defined in this class to set the value:
     * @see DISPUTE_REASON_MERCHANDISE_OR_SERVICE_NOT_RECEIVED
     * @see DISPUTE_REASON_MERCHANDISE_OR_SERVICE_NOT_AS_DESCRIBED
     * @var string | null
     * minLength: 1
     * maxLength: 255
     */
    public $dispute_reason;

    /**
     * The reason that the dispute was closed.
     *
     * @var string | null
     * minLength: 1
     * maxLength: 2000
     */
    public $closure_reason;

    /**
     * An array of customer- or merchant-posted messages.
     *
     * @var Message2[] | null
     */
    public $messages;

    public function validate($from = null)
    {
        $within = isset($from) ? "within $from" : "";
        !isset($this->dispute_amount) || Assert::isInstanceOf(
            $this->dispute_amount,
            CommonV3Money::class,
            "dispute_amount in AdjudicationInfo must be instance of CommonV3Money $within"
        );
        !isset($this->dispute_amount) ||  $this->dispute_amount->validate(AdjudicationInfo::class);
        !isset($this->items) || Assert::isArray(
            $this->items,
            "items in AdjudicationInfo must be array $within"
        );
        if (isset($this->items)) {
            foreach ($this->items as $item) {
                $item->validate(AdjudicationInfo::class);
            }
        }
        !isset($this->outcome) || Assert::isInstanceOf(
            $this->outcome,
            Outcome::class,
            "outcome in AdjudicationInfo must be instance of Outcome $within"
        );
        !isset($this->outcome) ||  $this->outcome->validate(AdjudicationInfo::class);
        !isset($this->extensions) || Assert::isInstanceOf(
            $this->extensions,
            Extensions2::class,
            "extensions in AdjudicationInfo must be instance of Extensions2 $within"
        );
        !isset($this->extensions) ||  $this->extensions->validate(AdjudicationInfo::class);
        !isset($this->evidences) || Assert::isArray(
            $this->evidences,
            "evidences in AdjudicationInfo must be array $within"
        );
        if (isset($this->evidences)) {
            foreach ($this->evidences as $item) {
                $item->validate(AdjudicationInfo::class);
            }
        }
        !isset($this->dispute_reason) || Assert::minLength(
            $this->dispute_reason,
            1,
            "dispute_reason in AdjudicationInfo must have minlength of 1 $within"
        );
        !isset($this->dispute_reason) || Assert::maxLength(
            $this->dispute_reason,
            255,
            "dispute_reason in AdjudicationInfo must have maxlength of 255 $within"
        );
        !isset($this->closure_reason) || Assert::minLength(
            $this->closure_reason,
            1,
            "closure_reason in AdjudicationInfo must have minlength of 1 $within"
        );
        !isset($this->closure_reason) || Assert::maxLength(
            $this->closure_reason,
            2000,
            "closure_reason in AdjudicationInfo must have maxlength of 2000 $within"
        );
        !isset($this->messages) || Assert::isArray(
            $this->messages,
            "messages in AdjudicationInfo must be array $within"
        );
        if (isset($this->messages)) {
            foreach ($this->messages as $item) {
                $item->validate(AdjudicationInfo::class);
            }
        }
    }

    private function map(array $data)
    {
        if (isset($data['dispute_amount'])) {
            $this->dispute_amount = new CommonV3Money($data['dispute_amount']);
        }
        if (isset($data['items'])) {
            $this->items = [];
            foreach ($data['items'] as $item) {
                $this->items[] = new ItemInfo2($item);
            }
        }
        if (isset($data['outcome'])) {
            $this->outcome = new Outcome($data['outcome']);
        }
        if (isset($data['extensions'])) {
            $this->extensions = new Extensions2($data['extensions']);
        }
        if (isset($data['evidences'])) {
            $this->evidences = [];
            foreach ($data['evidences'] as $item) {
                $this->evidences[] = new Evidence2($item);
            }
        }
        if (isset($data['dispute_reason'])) {
            $this->dispute_reason = $data['dispute_reason'];
        }
        if (isset($data['closure_reason'])) {
            $this->closure_reason = $data['closure_reason'];
        }
        if (isset($data['messages'])) {
            $this->messages = [];
            foreach ($data['messages'] as $item) {
                $this->messages[] = new Message2($item);
            }
        }
    }

    public function __construct(array $data = null)
    {
        if (isset($data)) {
            $this->map($data);
        }
    }
}
