<?php

/**
 * This file is part of OXID eSales Paypal module.
 *
 * OXID eSales Paypal module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales Paypal module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales Paypal module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2020
 */

namespace OxidProfessionalServices\PayPal\Core;

use DateTime;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use OxidEsales\Eshop\Application\Model\Address;
use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\BasketItem;
use OxidEsales\Eshop\Application\Model\Country;
use OxidEsales\Eshop\Application\Model\State;
use OxidEsales\Eshop\Core\Registry;
use OxidProfessionalServices\PayPal\Api\Model\Orders\AddressPortable;
use OxidProfessionalServices\PayPal\Api\Model\Orders\AmountBreakdown;
use OxidProfessionalServices\PayPal\Api\Model\Orders\AmountWithBreakdown;
use OxidProfessionalServices\PayPal\Api\Model\Orders\Item;
use OxidProfessionalServices\PayPal\Api\Model\Orders\Name;
use OxidProfessionalServices\PayPal\Api\Model\Orders\OrderApplicationContext;
use OxidProfessionalServices\PayPal\Api\Model\Orders\OrderRequest;
use OxidProfessionalServices\PayPal\Api\Model\Orders\Payer;
use OxidProfessionalServices\PayPal\Api\Model\Orders\PhoneWithType;
use OxidProfessionalServices\PayPal\Api\Model\Orders\PurchaseUnit;
use OxidProfessionalServices\PayPal\Api\Model\Orders\PurchaseUnitRequest;
use OxidProfessionalServices\PayPal\Api\Model\Orders\ShippingDetail;
use OxidProfessionalServices\PayPal\Core\Utils\PriceToMoney;

/**
 * Class OrderRequestBuilder
 * @package OxidProfessionalServices\PayPal\Core
 */
class OrderRequestFactory
{
    /**
     * After you redirect the customer to the PayPal payment page, a Continue button appears.
     * Use this option when the final amount is not known when the checkout flow is initiated and you want to
     * redirect the customer to the merchant page without processing the payment.
     */
    public const USER_ACTION_CONTINUE = 'CONTINUE';

    /**
     * After you redirect the customer to the PayPal payment page, a Pay Now button appears. Use this option when the
     * final amount is known when the checkout is initiated and you want to process the payment immediately when the
     * customer clicks Pay Now
     */
    public const USER_ACTION_PAY_NOW = 'PAY_NOW';

    /**
     * @var OrderRequest
     */
    private $request;

    /**
     * @var Basket
     */
    private $basket;

    /**
     * @param Basket $basket
     * @param string $intent Order::INTENT_CAPTURE or Order::INTENT_AUTHORIZE constant values
     * @param string $userAction USER_ACTION_CONTINUE OR USER_ACTION_PAY_NOW constant values
     * @param null|string $transactionId transaction id
     * @param null|string $invoiceId custom invoice number
     *
     * @return OrderRequest
     */
    public function getRequest(
        Basket $basket,
        string $intent,
        string $userAction,
        ?string $transactionId = null,
        ?string $invoiceId = null
    ): OrderRequest
    {
        $request = $this->request = new OrderRequest();
        $this->basket = $basket;

        $request->intent = $intent;
        if ($user = $basket->getUser()) {
            $request->payer = $this->getPayer();
        }
        $request->purchase_units = $this->getPurchaseUnits($transactionId, $invoiceId);
        $request->application_context = $this->getApplicationContext($userAction);

        return $request;
    }

    /**
     * Sets application context
     *
     * @param string $userAction
     *
     * @return OrderApplicationContext
     */
    protected function getApplicationContext(string $userAction): OrderApplicationContext
    {
        $context = new OrderApplicationContext();
        $context->brand_name = Registry::getConfig()->getActiveShop()->getFieldData('oxname');
        $context->shipping_preference = 'SET_PROVIDED_ADDRESS';
        $context->landing_page = 'LOGIN';
        $context->user_action = $userAction;

        return $context;
    }

    /**
     * @return PurchaseUnit[]
     */
    protected function getPurchaseUnits(?string $transactionId, ?string $invoiceId): array
    {
        $purchaseUnit = new PurchaseUnitRequest();
        $shopName = Registry::getConfig()->getActiveShop()->getFieldData('oxname');
        $lang = Registry::getLang();

        $purchaseUnit->custom_id = $transactionId;
        $purchaseUnit->invoice_id =  $invoiceId;
        $description = sprintf($lang->translateString('OXPS_PAYPAL_DESCRIPTION'), $shopName);
        $purchaseUnit->description = $description;

        $purchaseUnit->amount = $this->getAmount();
        $purchaseUnit->items = $this->getItems();
        if ($this->basket->getBasketUser())
            $purchaseUnit->shipping = $this->getShippingAddress();

        return [$purchaseUnit];
    }

    /**
     * @return AmountWithBreakdown
     */
    protected function getAmount(): AmountWithBreakdown
    {
        $amount = new AmountWithBreakdown();
        $basket = $this->basket;
        $currency = $this->basket->getBasketCurrency();

        //Total amount
        $amount->value = $this->basket->getSumOfCostOfAllItemsPayPalBasket();
        $amount->currency_code = $this->basket->getBasketCurrency()->name;

        //Cost breakdown
        $breakdown = $amount->breakdown = new AmountBreakdown();

        //Item total cost
        $itemTotal = $basket->getSumOfCostOfAllItemsPayPalBasket();
        $breakdown->item_total = PriceToMoney::convert($itemTotal, $currency);

        if ($basket->isCalculationModeNetto()) {
            //Item tax sum
            $tax = $basket->getPayPalProductVat();
            $breakdown->tax_total = PriceToMoney::convert($tax, $currency);
        }

        if ($basket->getDeliveryCost()) {
            //Shipping cost
            $shippingCost = $basket->getDeliveryCost();
            $breakdown->shipping = PriceToMoney::convert($shippingCost, $currency);
        }

        if ($discount = $basket->getDiscountSumPayPalBasket()) {
            //Discount
            $breakdown->discount = PriceToMoney::convert($discount, $currency);
        }

        return $amount;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        $basket = $this->basket;
        $currency = $basket->getBasketCurrency();
        $language = Registry::getLang();
        $items = [];
        $nettoPrices = $basket->isCalculationModeNetto();


        /** @var BasketItem $basketItem */
        foreach ($basket->getContents() as $basketItem) {
            $item = new Item();
            $item->name = $basketItem->getTitle();
            $itemUnitPrice = $basketItem->getUnitPrice();
            $item->unit_amount = PriceToMoney::convert($itemUnitPrice->getPrice(), $currency);

            if ($nettoPrices)
                $item->tax = PriceToMoney::convert($itemUnitPrice->getVatValue(), $currency);

            $item->quantity = $basketItem->getAmount();
            $items[] = $item;
        }

        if ($wrapping = $basket->getPayPalWrappingCosts()) {
            $item = new Item();
            $item->name = $language->translateString('GIFT_WRAPPING');
            $item->unit_amount = PriceToMoney::convert($wrapping, $currency);
            if ($nettoPrices)
                $item->tax = PriceToMoney::convert($basket->getPayPalWrappingVat(), $currency);
            $item->quantity = 1;
            $items[] = $item;
        }

        if ($giftCard = $basket->getPayPalGiftCardCosts()) {
            $item = new Item();
            $item->name = $language->translateString('GREETING_CARD');
            $item->unit_amount = PriceToMoney::convert($giftCard, $currency);
            if ($nettoPrices)
                $item->tax = PriceToMoney::convert($basket->getPayPalGiftCardVat(), $currency);
            $item->quantity = 1;
            $items[] = $item;
        }

        if (($payment = $basket->getPayPalPaymentCosts()) > 0) {
            $item = new Item();
            $item->name = $language->translateString('PAYMENT_METHOD');
            $item->unit_amount = PriceToMoney::convert($payment, $currency);
            if ($nettoPrices)
                $item->tax = PriceToMoney::convert($basket->getPayPalPayCostVat(), $currency);
            $item->quantity = 1;
            $items[] = $item;
        }

        return $items;
    }

    /**
     * @return Payer
     */
    protected function getPayer(): Payer
    {
        $payer = new Payer();
        $user = $this->basket->getBasketUser();

        $name = $payer->name = new Name();
        $name->given_name = $user->getFieldData('oxfname');
        $name->surname = $user->getFieldData('oxlname');

        $payer->email_address = $user->getFieldData('oxusername');
        $payer->phone = $this->getPayerPhone();

        $birthDate = $user->getFieldData('oxbirthdate');
        if ($birthDate && $birthDate !== '0000-00-00') {
            /** @var DateTime $birthDate */
            $birthDate = oxNew(DateTime::class, $user->getFieldData('oxbirthdate'));
            $payer->birth_date = $birthDate->format('Y-m-d');
        }

        $payer->address = $this->getBillingAddress();

        return $payer;
    }

    /**
     * @return AddressPortable
     */
    protected function getBillingAddress(): AddressPortable
    {
        $user = $this->basket->getBasketUser();

        $state = oxNew(State::class);
        $state->load($user->getFieldData('oxstateid'));

        $country = oxNew(Country::class);
        $country->load($user->getFieldData('oxcountryid'));

        $address = new AddressPortable();
        $addressLine = $user->getFieldData('oxstreet') . " " . $user->getFieldData('oxstreetnr');
        $address->address_line_1 = $addressLine;
        $address->admin_area_1 = $state->getFieldData('oxtitle');
        $address->admin_area_2 = $user->getFieldData('oxcity');
        $address->country_code = $country->oxcountry__oxisoalpha2->value;
        $address->postal_code = $user->getFieldData('oxzip');

        return $address;
    }

    /**
     * @return ShippingDetail|null
     */
    protected function getShippingAddress(): ?ShippingDetail
    {
        $user = $this->basket->getBasketUser();
        $deliveryId = Registry::getSession()->getVariable("deladrid");
        $deliveryAddress = oxNew(Address::class);
        $shipping = new ShippingDetail();
        $name = $shipping->name = new Name();
        if ($deliveryId && $deliveryAddress->load($deliveryId)) {
            $fullName = $deliveryAddress->oxaddress__oxfname->value . " " . $deliveryAddress->oxaddress__oxlname->value;
            $name->full_name = $fullName;

            $address = new AddressPortable();

            $state = oxNew(State::class);
            $state->load($deliveryAddress->getFieldData('oxstateid'));

            $country = oxNew(Country::class);
            $country->load($deliveryAddress->getFieldData('oxcountryid'));

            $addressLine =
                $deliveryAddress->getFieldData('oxstreet') . " " . $deliveryAddress->getFieldData('oxstreetnr');
            $address->address_line_1 = $addressLine;
            $address->admin_area_1 = $state->getFieldData('oxtitle');
            $address->admin_area_2 = $deliveryAddress->getFieldData('oxcity');
            $address->country_code = $country->oxcountry__oxisoalpha2->value;
            $address->postal_code = $deliveryAddress->getFieldData('oxzip');

            $shipping->address = $address;
        } else {
            $fullName = $user->getFieldData('oxstreet') . " " . $user->getFieldData('oxstreetnr');
            $name->full_name = $fullName;

            $shipping->address = $this->getBillingAddress();
        }

        return $shipping;
    }

    /**
     * @return PhoneWithType|null
     */
    protected function getPayerPhone(): ?PhoneWithType
    {
        $user = $this->basket->getBasketUser();
        $phoneUtils = PhoneNumberUtil::getInstance();

        //Array of phone numbers to use in the request, using the first from the sequence that is available and valid.
        $userPhoneFields = [
            'oxmobfon' => 'MOBILE',
            'oxprivfon' => 'MOBILE',
            'oxfon' => 'HOME',
            'oxfax' => 'FAX'
        ];

        $country = oxNew(Country::class);
        $country->load($user->getFieldData('oxcountryid'));
        $countryCode = $country->oxcountry__oxisoalpha2->value;

        $number = null;

        foreach ($userPhoneFields as $numberField => $numberType) {
            $number = $user->getFieldData($numberField);
            if (!$number) continue;
            try {
                $phoneNumber = $phoneUtils->parse($number, $countryCode);
                if ($phoneUtils->isValidNumber($phoneNumber)) {
                    $number = ltrim($phoneUtils->format($phoneNumber, PhoneNumberFormat::E164), '+');
                    $type = $numberType;
                    break;
                }
            } catch (NumberParseException $exception) {}
        }

        if (!$number) {
            return null;
        }

        $phone = new PhoneWithType();
        $phone->phone_type = $type;
        $phone->phone_number->national_number = $number;

        return $phone;
    }
}