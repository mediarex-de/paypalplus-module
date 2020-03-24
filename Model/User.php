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

namespace OxidEsales\PayPalPlus\Model;

/**
 * Class \OxidEsales\PayPalPlus\Model\User.
 * Overloads User model.
 *
 * @see \OxidEsales\Eshop\Application\Model\User
 */
class User extends User_parent
{
    /**
     * Overloaded parent method.
     * Adds a profile post save hook.
     *
     * @inheritDoc
     */
    public function save()
    {
        $blReturn = $this->_paypPayPalPlusOxUser_save_parent();

        if (!empty($blReturn)) {

            /** @var \OxidEsales\PayPalPlus\Model\Profile $oProfile */
            $oProfile = \OxidEsales\PayPalPlus\Core\Shop::getShop()->getNew(\OxidEsales\PayPalPlus\Model\Profile::class);
            $oProfile->postSave($this);
        }

        return $blReturn;
    }

    /**
     * Returns user isoalpha2 country code if user has a country set
     *
     * @return string|null
     */
    public function getUserCountryCode()
    {
        if ($this->oxuser__oxcountryid->value) {
            $oDb = \OxidEsales\PayPalPlus\Core\Shop::getShop()->getDb();
            $sViewName = getViewName('oxcountry');
            $sQ = "select oxisoalpha2 from {$sViewName} where oxid = " . $oDb->quote($this->oxuser__oxcountryid->value);

            return $oDb->getOne($sQ);
        }

        return null;
    }

    /**
     * Parent `save` call. Method required for mocking.
     *
     * @inheritDocs
     *
     * @codeCoverageIgnore
     */
    protected function _paypPayPalPlusOxUser_save_parent()
    {
        return parent::save();
    }
}
