[{if $sPaymentID == "oxidpaypal"}]
    <input id="payment_[{$sPaymentID}]" type="radio" name="paymentid" value="[{$sPaymentID}]" [{if $oView->getCheckedPaymentId() == $paymentmethod->oxpayments__oxid->value}]checked[{/if}] style="display:none;">
[{else}]
    [{$smarty.block.parent}]
[{/if}]
