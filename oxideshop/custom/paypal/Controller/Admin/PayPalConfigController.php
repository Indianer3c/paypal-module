<?php

/**
 * This file is part of OXID eSales PayPal module.
 *
 * OXID eSales PayPal module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales PayPal module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales PayPal module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2020
 */

namespace OxidProfessionalServices\PayPal\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminController;
use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\Eshop\Core\Registry;
use OxidProfessionalServices\PayPal\Core\Config;
use OxidProfessionalServices\PayPal\Core\Request;

/**
 * Controller for admin > PayPal/Configuration page
 */
class PayPalConfigController extends AdminController
{
    public const MODULE_ID = 'module:oxscpaypal';
    public const SIGN_UP_HOST = 'https://www.sandbox.paypal.com/bizsignup/partner/entry';

    public function __construct()
    {
        parent::__construct();

        $this->_sThisTemplate = 'pspaypalconfig.tpl';
    }

    /**
     * Get webhook controller url
     *
     * @return string
     */
    public function getWebhookControllerUrl(): string
    {
        return Registry::getUtilsUrl()->getActiveShopHost() . '/index.php?cl=PayPalWebhookController';
    }

    /**
     * @return string
     */
    public function render()
    {
        $thisTemplate = parent::render();

        $config = new Config();
        $this->addTplParam('config', $config);

        try {
            $config->checkHealth();
        } catch (StandardException $e) {
            Registry::getUtilsView()->addErrorToDisplay($e, false, true, 'paypal_error');
        }

        return $thisTemplate;
    }

    /**
     * Template Getter: Get a Link for SignUp the Live Merchant Integration
     * see getSignUpMerchantIntegrationLink
     * @return string
     */
    public function getLiveSignUpMerchantIntegrationLink(): string
    {
        $config = new Config();

        return $this->buildSignUpLink(
            $config->getLiveOxidPartnerId(),
            $config->getLiveOxidClientId()
        );
    }

    /**
     * Template Getter: Get a Link for SignUp the Live Merchant Integration
     * see getSignUpMerchantIntegrationLink
     * @return string
     */
    public function getSandboxSignUpMerchantIntegrationLink(): string
    {
        $config = new Config();

        return $this->buildSignUpLink(
            $config->getSandboxOxidPartnerId(),
            $config->getSandboxOxidClientId()
        );
    }

    /**
     * Maps arguments and constants to request parameters, generates a sign up url
     *
     * @param string $partnerId
     * @param string $clientId
     *
     * @return string
     */
    private function buildSignUpLink(string $partnerId, string $clientId): string
    {
        $params = [
            'sellerNonce' => $this->createNonce(),
            'partnerId' => $partnerId,
            'product' => 'EXPRESS_CHECKOUT',
            'integrationType' => 'FO',
            'partnerClientId' => $clientId,
            //'partnerLogoUrl' => '',
            'displayMode' => 'minibrowser',
            'features' => 'PAYMENT,REFUND,ADVANCED_TRANSACTIONS_SEARCH'
        ];

        return self::SIGN_UP_HOST . '?' . http_build_query($params);
    }

    /**
     * create a unique Seller Nonce to check your own transactions
     *
     * @return string
     */
    public function createNonce(): string
    {
        if (!empty(Registry::getSession()->getVariable('PAYPAL_MODULE_NONCE'))) {
            return Registry::getSession()->getVariable('PAYPAL_MODULE_NONCE');
        }

        try {
            // throws Exception if it was not possible to gather sufficient entropy.
            $nonce = bin2hex(random_bytes(42));
        } catch (\Exception $e) {
            $nonce = md5(uniqid('', true) . '|' . microtime()) . substr(md5(mt_rand()), 0, 24);
        }

        Registry::getSession()->setVariable('PAYPAL_MODULE_NONCE', $nonce);

        return $nonce;
    }


    /**
     * Saves configuration values
     */
    public function save()
    {
        $confArr = Registry::getRequest()->getRequestEscapedParameter('conf');
        $shopId = Registry::getConfig()->getShopId();

        $confArr = $this->handleSpecialFields($confArr);
        $this->saveConfig($confArr, $shopId);

        parent::save();
    }

    /**
     * Saves configuration values
     *
     * @param array $conf
     * @param int   $shopId
     */
    protected function saveConfig(array $conf, int $shopId): void
    {
        foreach ($conf as $confName => $value) {
            $value = trim($value);
            if (strpos($confName, 'bl') === 0) {
                Registry::getConfig()->saveShopConfVar('bool', $confName, $value, $shopId, self::MODULE_ID);
            } else {
                Registry::getConfig()->saveShopConfVar('str', $confName, $value, $shopId, self::MODULE_ID);
            }
        }
    }

    /**
     * Handles checkboxes/dropdowns
     *
     * @param array $conf
     *
     * @return array
     */
    protected function handleSpecialFields(array $conf): array
    {
        if ($conf['blPayPalSandboxMode'] === 'sandbox') {
            $conf['blPayPalSandboxMode'] = 1;
        } else {
            $conf['blPayPalSandboxMode'] = 0;
        }

        if (!isset($conf['blPayPalShowProductDetailsButton'])) {
            $conf['blPayPalShowProductDetailsButton'] = 0;
        }

        if (!isset($conf['blPayPalShowBasketButton'])) {
            $conf['blPayPalShowBasketButton'] = 0;
        }

        if (!isset($conf['oePayPalBannersShowAll'])) {
            $conf['oePayPalBannersShowAll'] = 0;
        }
        if (!isset($conf['oePayPalBannersStartPage'])) {
            $conf['oePayPalBannersStartPage'] = 0;
        }
        if (!isset($conf['oePayPalBannersCategoryPage'])) {
            $conf['oePayPalBannersCategoryPage'] = 0;
        }
        if (!isset($conf['oePayPalBannersSearchResultsPage'])) {
            $conf['oePayPalBannersSearchResultsPage'] = 0;
        }
        if (!isset($conf['oePayPalBannersProductDetailsPage'])) {
            $conf['oePayPalBannersProductDetailsPage'] = 0;
        }
        if (!isset($conf['oePayPalBannersCheckoutPage'])) {
            $conf['oePayPalBannersCheckoutPage'] = 0;
        }

        return $conf;
    }

    /**
     * @return array
     */
    public function getTotalCycleDefaults()
    {
        $array = [];

        for ($i = 1; $i < 1000; $i++) {
            $array[] = $i;
        }

        return $array;
    }
}
