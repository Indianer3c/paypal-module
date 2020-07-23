<?php

namespace OxidProfessionalServices\PayPal\Api\Tests\Integration;

use OxidProfessionalServices\PayPal\Api\Client;
use OxidProfessionalServices\PayPal\Api\Model\Orders\AddressName;
use OxidProfessionalServices\PayPal\Api\Model\Orders\AddressPortable;
use OxidProfessionalServices\PayPal\Api\Model\Orders\AmountBreakdown;
use OxidProfessionalServices\PayPal\Api\Model\Orders\AmountWithBreakdown;
use OxidProfessionalServices\PayPal\Api\Model\Orders\DateNoTime;
use OxidProfessionalServices\PayPal\Api\Model\Orders\Name;
use OxidProfessionalServices\PayPal\Api\Model\Orders\Order;
use OxidProfessionalServices\PayPal\Api\Model\Orders\OrderRequest;
use OxidProfessionalServices\PayPal\Api\Model\Orders\Payer;
use OxidProfessionalServices\PayPal\Api\Model\Orders\Phone;
use OxidProfessionalServices\PayPal\Api\Model\Orders\PhoneWithType;
use OxidProfessionalServices\PayPal\Api\Model\Orders\PurchaseUnitRequest;
use OxidProfessionalServices\PayPal\Api\Model\Orders\TaxInfo;
use OxidProfessionalServices\PayPal\Api\Service\Orders;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    /**
     * @var Client
     */
    private $client;

    public function setUp()
    {
        parent::setUp();
        $client = ClientFactory::createClient(Client::class);
        $this->client = $client;
    }

    //fixme: move this test to the unittest folder
    public function testPaypalOrderResponseCanBeMapped()
    {
        $json = <<<'JSON'
{"id":"9R353891UB6120313","links":[{"href":"https:\/\/api.sandbox.paypal.com\/v2\/checkout\/orders\/9R353891UB6120313","rel":"self","method":"GET"},{"href":"https:\/\/www.sandbox.paypal.com\/checkoutnow?token=9R353891UB6120313","rel":"approve","method":"GET"},{"href":"https:\/\/api.sandbox.paypal.com\/v2\/checkout\/orders\/9R353891UB6120313","rel":"update","method":"PATCH"},{"href":"https:\/\/api.sandbox.paypal.com\/v2\/checkout\/orders\/9R353891UB6120313\/capture","rel":"capture","method":"POST"}],"status":"CREATED"}
JSON;
        $json = \GuzzleHttp\json_decode($json);
        $mapper = new \JsonMapper();
        $order = $mapper->map($json, new Order());
        $this->assertEquals($order->id, "9R353891UB6120313");
        $this->assertEquals($order->status, "CREATED");
    }

    public function testCreateOrder()
    {
        $orderService = new Orders($this->client);
        $orderRequest = new OrderRequest();

        $orderRequest->payer = new Payer();
        $orderRequest->payer->name = new Name();
        $orderRequest->payer->name->given_name = "Johannes";
        $orderRequest->payer->phone = new PhoneWithType();
        $orderRequest->payer->phone->phone_number = new Phone();
        $orderRequest->payer->phone->phone_number->national_number = "09812943";
//        $orderRequest->payer->phone->phone_number->country_code = "DE";

        //$orderRequest->payer->tax_info = new TaxInfo();
        //$orderRequest->payer->tax_info->tax_id= "";
        $orderRequest->payer->address = new AddressPortable();
        $orderRequest->payer->address->country_code = "DE";

        $purchaseUnitRequest = new PurchaseUnitRequest();
        $purchaseUnitRequest->amount = new AmountWithBreakdown();
        $purchaseUnitRequest->amount->value = "1";
        $purchaseUnitRequest->amount->currency_code = "EUR";

        $orderRequest->purchase_units = [$purchaseUnitRequest];
        $orderRequest->intent = Order::INTENT_CAPTURE;
        $orderRequest->validate();
        $order = $orderService->createOrder($orderRequest, "", "");
        $this->assertEquals($order->status, "CREATED");
        $id = $order->id;
        $order = $orderService->showOrderDetails($id);
        $this->assertEquals($id, $order->id);
    }
}
