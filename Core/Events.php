<?php
/**
 * This file is part of OXID eSales PayPal Plus module.
 *
 * OXID eSales PayPal Plus module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales PayPal Plus module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales PayPal Plus module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.paypal.com
 * @copyright (C) PayPal (Europe) S.à r.l. et Cie, S.C.A. 2015
 */

namespace OxidEsales\PayPalPlus\Core;

/**
 * Class \OxidEsales\PayPalPlus\Core\Events
 * Extends oxModule class handles module setup.
 * Class is also used to get module-related parameters from.
 */
class Events extends \OxidEsales\Eshop\Core\Module\Module
{
    /**
     * Class constructor.
     * Sets main module data and load additional data.
     */
    public function __construct()
    {
        $sModuleId = 'payppaypalplus';

        $this->setModuleData(
            array(
                'id'          => $sModuleId,
                'title'       => 'PayPal Plus',
                'description' => 'PayPal Plus payments module for OXID eShop',
            )
        );

        $this->load($sModuleId);
    }

    /**
     * Module activation event.
     */
    public static function onActivate()
    {
        /** @var \OxidEsales\PayPalPlus\Core\PaypalPlusEvents $oEventHandler */
        $oEventHandler = \OxidEsales\PayPalPlus\Core\Shop::getShop()->getFromRegistry(\OxidEsales\PayPalPlus\Core\PaypalPlusEvents::class);
        $oEventHandler->activate();
    }

    /**
     * Module deactivation event.
     */
    public static function onDeactivate()
    {
        /** @var \OxidEsales\PayPalPlus\Core\PaypalPlusEvents $oEventHandler */
        $oEventHandler = \OxidEsales\PayPalPlus\Core\Shop::getShop()->getFromRegistry(\OxidEsales\PayPalPlus\Core\PaypalPlusEvents::class);
        $oEventHandler->deactivate();
    }
}
