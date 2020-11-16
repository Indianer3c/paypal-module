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

namespace OxidProfessionalServices\PayPal\Component;

use OxidEsales\Eshop\Core\Registry;

/**
 * @mixin \OxidEsales\Eshop\Application\Component\UserComponent
 */
class UserComponent extends UserComponent_parent
{
    public function render()
    {
        $return = parent::render();

        $this->getSession()->deleteVariable('paypalRedirect');

        $redirect = Registry::getRequest()->getRequestEscapedParameter('return');
        if ($redirect) {
            $this->getSession()->setVariable('paypalRedirect', $redirect);
        }

        return $return;
    }

    public function login_noredirect()
    {
        $return = parent::login_noredirect();
        $redirect = $this->getSession()->getVariable('paypalRedirect');
        if ($redirect) {
            $this->getSession()->deleteVariable('paypalRedirect');
            Registry::getUtils()->redirect($redirect, true, 302);
        }

        return $return;
    }
}
