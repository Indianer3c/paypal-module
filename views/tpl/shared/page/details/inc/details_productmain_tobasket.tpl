[{assign var="config" value=$oViewConf->getPayPalCheckoutConfig()}]
[{if $blCanBuy && $config->isActive() && !$oViewConf->isPayPalExpressSessionActive() && $config->showPayPalProductDetailsButton()}]
    [{include file="modules/osc/paypal/paymentbuttons.tpl" buttonId="PayPalButtonProductMain" buttonClass="paypal-button-wrapper large" aid=$oDetailsProduct->oxarticles__oxid->value}]
[{/if}]
