[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign box="list"}]
[{assign var="filters" value=$oView->getFilter()}]
<div class="container-fluid">
    <br />
    <button id="toggleFilter" class="btn btn-info">[{oxmultilang ident="OXPS_PAYPAL_FILTER"}]</button>
    [{capture assign="sPayPalSubscriptionsJS"}]
        [{strip}]
            jQuery(document).ready(function(){
                jQuery("#filters").hide();
                jQuery("#results").show();
                jQuery("#toggleFilter").click(function(e) {
                    e.preventDefault();
                    jQuery("#filters").toggle();
                    jQuery("#results").toggle();
                });
            });
        [{/strip}]
    [{/capture}]
    [{oxscript add=$sPayPalSubscriptionsJS}]
    <br />
    <div id="filters">
        <form method="post" action="[{$oViewConf->getSelfLink()}]">
            [{include file="_formparams.tpl" cl="PayPalSubscriptionController" lstrt=$lstrt actedit=$actedit oxid=$oxid fnc="" language=$actlang editlanguage=$actlang}]

            [{if !empty($error)}]
        <div class="alert alert-danger" role="alert">
            [{$error}]
        </div>
        [{/if}]
        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="subscriptionIdFilter">[{oxmultilang ident="OXPS_PAYPAL_SUBSCRIPTION_ID"}]</label>
                    <input type="text"
                           id="subscriptionIdFilter"
                           class="form-control"
                           name="filters[oxps_paypal_subscription][paypalbillingagreementid]"
                           value="[{if $filters.oxps_paypal_subscription.paypalbillingagreementid}][{$filters.oxps_paypal_subscription.paypalbillingagreementid}][{/if}]">
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="subscriptionPlanIdFilter">[{oxmultilang ident="OXPS_PAYPAL_SUBSCRIPTION_PLAN_ID"}]</label>
                    <input type="text"
                           id="subscriptionPlanIdFilter"
                           class="form-control"
                           name="filters[oxps_paypal_subscription_product][paypalsubscriptionplanid]"
                           value="[{if $filters.oxps_paypal_subscription_product.paypalsubscriptionplanid}][{$filters.oxps_paypal_subscription_product.paypalsubscriptionplanid}][{/if}]">
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="subscriptionEmailFilter">[{oxmultilang ident="OXPS_PAYPAL_EMAIL"}]</label>
                    <input type="text"
                           id="subscriptionEmailFilter"
                           class="form-control"
                           name="filters[oxorder][oxbillemail]"
                           value="[{if $filters.oxorder.oxbillemail}][{$filters.oxorder.oxbillemail}][{/if}]">
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="subscriptionCreatedFilter">[{oxmultilang ident="OXPS_PAYPAL_SUBSCRIPTION_CREATED"}]</label>
                    <input type="date"
                           id="subscriptionCreatedFilter"
                           class="form-control"
                           name="filters[oxorder][oxorderdate]"
                           value="[{if $filters.oxorder.oxorderdate}][{$filters.oxorder.oxorderdate}][{/if}]">
                </div>
            </div>
        </div>
        <div class="row ppmessages">
            <button class="btn btn-primary" type="submit">[{oxmultilang ident="OXPS_PAYPAL_APPLY"}]</button>
            </div>
        </form>
    </div>

    [{include file="pspaypallistpagination.tpl"}]
    <div id="results">
        <table class="table table-sm">
            <thead>
                <tr class="ppaltmessages">
                    <th>[{oxmultilang ident="OXPS_PAYPAL_SUBSCRIPTION_ID"}]</th>
                    <th>[{oxmultilang ident="OXPS_PAYPAL_SUBSCRIPTION_PLAN_ID"}]</th>
                    <th>[{oxmultilang ident="OXPS_PAYPAL_SUBSCRIPTION_EMAIL"}]</th>
                    <th colspan="2">[{oxmultilang ident="OXPS_PAYPAL_SUBSCRIPTION_CREATED"}]</th>
                </tr>
            </thead>
            <tbody>
                [{foreach from=$subscriptions item="subscription" name="subscriptions"}]
                    [{cycle values='ppmessages,ppaltmessages' assign=cellClass}]
                    <tr class="[{$cellClass}]">
                        <td>[{$subscription->oxps_paypal_subscription__paypalbillingagreementid->value}]</td>
                        <td>[{$subscription->oxps_paypal_subscription__paypalsubscriptionplanid->value}]</td>
                        <td>[{$subscription->oxps_paypal_subscription__oxbillemail->value}]</td>
                        <td>[{$subscription->oxps_paypal_subscription__oxorderdate->value}]</td>
                        <td>
                            <a href="[{$detailsLink|cat:"&amp;billingagreementid="|cat:$subscription->oxps_paypal_subscription__paypalbillingagreementid->value}]">
                                [{oxmultilang ident="OXPS_PAYPAL_MORE"}]
                            </a>
                        </td>
                    </tr>
                [{/foreach}]
            </tbody>
        </table>
    </div>
    [{include file="pspaypallistpagination.tpl"}]
</div>
[{include file="bottomitem.tpl"}]
