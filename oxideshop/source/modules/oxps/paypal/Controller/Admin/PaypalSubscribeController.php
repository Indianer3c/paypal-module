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

namespace OxidProfessionalServices\PayPal\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminController;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\Exception\DatabaseConnectionException;
use OxidEsales\Eshop\Core\Exception\DatabaseErrorException;
use OxidEsales\Eshop\Core\Registry;
use OxidProfessionalServices\PayPal\Api\Exception\ApiException;
use OxidProfessionalServices\PayPal\Api\Model\Catalog\Product;
use OxidProfessionalServices\PayPal\Api\Model\Subscriptions\Frequency;
use OxidProfessionalServices\PayPal\Api\Model\Subscriptions\Plan;
use OxidProfessionalServices\PayPal\Controller\Admin\Service\CatalogService;
use OxidProfessionalServices\PayPal\Controller\Admin\Service\SubscriptionService;
use OxidProfessionalServices\PayPal\Core\Currency;
use OxidProfessionalServices\PayPal\Core\ServiceFactory;
use OxidProfessionalServices\PayPal\Model\Category;
use OxidProfessionalServices\PayPal\Repository\SubscriptionRepository;

/**
 * Controller for admin > Paypal/Configuration page
 */
class PaypalSubscribeController extends AdminController
{
    /**
     * The Product from Paypal's API
     * Caching the linked object to reduce calls to paypal api
     * @var Product
     */
    private $linkedObject;

    /**
     * The Linked data stored in OXID db
     * Caching the linked object to reduce calls to paypal api
     * @var array
     */
    private $linkedProduct;


    /**
     * The lined subscription plan called from Paypal API
     * @var Plan
     */
    private $subscriptionPlan;

    /**
     * @var SubscriptionRepository
     */
    private $repository;

    public function __construct()
    {
        parent::__construct();
        $this->_sThisTemplate = 'subscribe.tpl';
        $this->repository = new SubscriptionRepository();
    }

    /**
     * @return bool
     */
    public function isPayPalProductLinked()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isPayPalProductLinkedByParentOnly()
    {
        return false;
    }

    /**
     * @return object
     */
    public function getEditObject()
    {
        return $this->repository->getEditObject(Registry::getRequest()->getRequestParameter('oxid'));
    }

    /**
     * @return array
     */
    public function getIntervalDefaults()
    {
        return [
            Frequency::INTERVAL_UNIT_DAY,
            Frequency::INTERVAL_UNIT_WEEK,
            Frequency::INTERVAL_UNIT_SEMI_MONTH,
            Frequency::INTERVAL_UNIT_MONTH,
            Frequency::INTERVAL_UNIT_YEAR
        ];
    }

    /**
     * @return array|string[]
     */
    public function getCurrencyCodes()
    {
        return Currency::getCurrencyCodes();
    }

    /**
     * @return array
     */
    public function getTenureTypeDefaults()
    {
        return [
            'REGULAR',
            'TRIAL'
        ];
    }

    /**
     * @return array
     */
    public function getSequenceDefaults()
    {
        $array = [];

        for ($i = 1; $i < 100; $i++) {
            $array[] = $i;
        }

        return $array;
    }

    /**
     * @return array
     */
    public function getTotalCycleDefaults()
    {
        $array = [];

        for ($i = 0; $i < 1000; $i++) {
            $array[] = $i;
        }

        return $array;
    }

    /**
     * @return bool
     * @throws DatabaseConnectionException
     * @throws DatabaseErrorException
     * @throws ApiException
     */
    public function hasSubscriptionPlan()
    {
        $this->setSubscriptionPlan();
        if (!empty($this->subscriptionPlan)) {
            return true;
        }

        return false;
    }

    /**
     * @return bool|Plan
     * @throws DatabaseConnectionException
     * @throws DatabaseErrorException
     * @throws ApiException
     */
    public function setSubscriptionPlan()
    {
        if (!empty($this->subscriptionPlan)) {
            return $this->subscriptionPlan;
        }

        $result = $this->repository->getSubscriptionIdPlanByProductId($this->linkedObject->id);
        $subscriptionPlanId = $result[0]['OXPS_PAYPAL_SUBSCRIPTION_PLAN_ID'];

        if (empty($subscriptionPlanId)) {
            return false;
        }

        /** @var ServiceFactory $sf */
        $sf = Registry::get(ServiceFactory::class);
        $subscriptionPlan = $sf->getSubscriptionService()->showPlanDetails('string', $subscriptionPlanId, 1);

        if ($subscriptionPlan !== null) {
            $this->subscriptionPlan = $subscriptionPlan;
        }

        return $this->subscriptionPlan;
    }

    /**
     * @return bool
     * @throws ApiException
     */
    public function hasLinkedObject()
    {
        $this->setLinkedObject();
        if (!empty($this->linkedObject)) {
            return true;
        }

        return false;
    }

    /**
     * @return Product
     * @throws ApiException
     */
    public function getLinkedObject()
    {
        $this->setLinkedObject();
        return $this->linkedObject;
    }

    /**
     * @return Plan
     * @throws DatabaseConnectionException
     * @throws DatabaseErrorException
     * @throws ApiException
     */
    public function getSubscriptionPlan()
    {
        $this->setSubscriptionPlan();
        return $this->subscriptionPlan;
    }

    /**
     * @throws ApiException
     */
    public function setLinkedObject()
    {
        if (!empty($this->linkedObject)) {
            return;
        }

        $article = oxNew(Article::class);
        $oxid = Registry::getRequest()->getRequestParameter('oxid');
        $article->load($oxid);

        try {
            $this->getLinkedProductByOxid($oxid);
        } catch (DatabaseConnectionException $e) {
            return;
        } catch (DatabaseErrorException $e) {
            return;
        }

        if (empty($this->linkedProduct)) {
            return;
        }

        $this->linkedObject = $this->getPaypalProductDetail($this->linkedProduct[0]['OXPS_PAYPAL_PRODUCT_ID']);
    }

    /**
     * @throws DatabaseConnectionException
     * @throws DatabaseErrorException
     * @throws ApiException
     */
    public function unlink()
    {
        $this->setLinkedObject();

        if (empty($this->linkedObject)) {
            return;
        }

        $this->repository->deleteLinkedProduct($this->linkedObject->id);
    }

    /**
     * @param $id
     * @return Product
     * @throws ApiException
     */
    public function getPaypalProductDetail($id): Product
    {
        /**
         * @var ServiceFactory $sf
         */
        $sf = Registry::get(ServiceFactory::class);
        $cs = $sf->getCatalogService();

        return $cs->showProductDetails($id);
    }

    /**
     * @throws ApiException
     */
    public function getCatalogEntries()
    {
        /**
         * @var ServiceFactory $sf
         */
        $sf = Registry::get(ServiceFactory::class);
        $cs = $sf->getCatalogService();

        $products = $cs->listProducts();

        $filteredProducts = [];
        foreach ($products as $product) {
            $filteredProducts = $product;
        }

        return $filteredProducts;
    }

    /**
     * @return array
     */
    public function getCategories()
    {
        $category = new Category();
        $categories = $category->getCategories();

        $categoryArray = [];
        foreach ($categories as $type => $value) {
            $categoryArray[] = $value;
        }

        return $categoryArray;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        $category = new Category();
        $types = $category->getTypes();

        $typeArray = [];
        foreach ($types as $type => $value) {
            $typeArray[] = $value;
        }

        return $typeArray;
    }

    /**
     * @return mixed
     */
    public function getProductUrl()
    {
        return $this->getEditObject()->getBaseStdLink($this->_iEditLang);
    }

    /**
     * @return array
     */
    public function getDisplayImages(): array
    {
        $editObject = $this->getEditObject();

        $images = [];

        for ($i = 1; $i < 10; $i++) {
            $field = 'oxarticles__oxpic' . $i;
            $rawValue = $editObject->$field->rawValue;

            if (empty($rawValue)) {
                continue;
            }

            $img = $this->formatImageUrl(
                $editObject->ssl_dimagedir,
                $editObject->$field->rawValue,
                $i
            );

            if ($this->imgexists($img)) {
                $images[] = $img;
            }
        }

        return $images;
    }

    /**
     * @param $url
     * @return bool
     */
    private function imgexists($url)
    {
        if (!$fp = curl_init($url)) {
            return false;
        }
        return true;
    }

    /**
     * @param string $url
     * @param string $file
     * @param int $num
     * @return string
     */
    private function formatImageUrl($url, $file, int $num)
    {
        return str_replace(':/out', '/out', $url) . 'master/product/' . $num . '/' . $file;
    }

    /**
     * @throws DatabaseConnectionException
     * @throws DatabaseErrorException
     * @throws ApiException
     */
    public function save()
    {
        $subscriptionService = new SubscriptionService();
        $catalogService = new CatalogService($this->linkedObject);
        $productId = Registry::getRequest()->getRequestEscapedParameter('paypalProductId', "");

        if ($this->hasLinkedObject()) {
            $this->setLinkedObject();
            $catalogService->updateProduct($productId);
            $subscriptionService->saveNewSubscriptionPlan($productId);
        } else {
            $catalogService->createProduct();
        }
    }

    /**
     * @throws DatabaseConnectionException
     * @throws DatabaseErrorException
     * @throws ApiException
     */
    public function patch()
    {
        $subscriptionService = new SubscriptionService();
        $catalogService = new CatalogService($this->linkedObject);
        $productId = Registry::getRequest()->getRequestEscapedParameter('paypalProductId', "");
        $this->setLinkedObject();
        $catalogService->updateProduct($productId);

        if ($this->hasSubscriptionPlan()) {
            $subscriptionService->update($this->subscriptionPlan);
        } else {
            $subscriptionService->saveNewSubscriptionPlan($productId);
        }
    }

    /**
     * @param $oxid
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseErrorException
     */
    private function getLinkedProductByOxid($oxid): void
    {
        if (!empty($this->linkedProduct)) {
            return;
        }

        $this->linkedProduct = $this->repository->getLinkedProductByOxid($oxid);
    }
}
