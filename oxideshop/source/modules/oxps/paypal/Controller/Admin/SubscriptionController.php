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

use OxidEsales\Eshop\Application\Controller\Admin\AdminListController;
use OxidEsales\Eshop\Core\Registry;
use OxidProfessionalServices\PayPal\Model\Subscription;

class SubscriptionController extends AdminListController
{
    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->_sListClass = Subscription::class;
        $this->setTemplateName('pspaypalsubscriptions.tpl');

        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->addTplParam('subscriptions', $this->getItemList());
        $this->addTplParam('detailsLink', $this->getDetailsLink());

        return parent::render();
    }

    /**
     * Get link for subscription details page
     *
     * @return string
     */
    private function getDetailsLink(): string
    {
        $viewConfig = $this->getViewConfig();
        $request = Registry::getRequest();

        $params = [
            'cl' => 'PayPalSubscriptionDetailsController',
            'jumppage' => $request->getRequestEscapedParameter('jumppage'),
            'filters' => $this->getListFilter() ? json_encode($this->getListFilter()) : null,
        ];

        return $viewConfig->getSelfLink() . http_build_query(array_filter($params));
    }

    /**
     * @inheritDoc
     */
    public function getListFilter()
    {
        return Registry::getRequest()->getRequestEscapedParameter('filters') ?? parent::getListFilter();
    }
}
