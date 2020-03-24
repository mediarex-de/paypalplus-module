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

namespace OxidProfessionalServices\PayPalPlus\Model;

/**
 * Class \OxidProfessionalServices\PayPalPlus\Model\Profile.
 * User profile related objects handler.
 */
class Profile extends \OxidProfessionalServices\PayPalPlus\Core\SuperCfg
{
    /**
     * Check if the user or address is a part of the currently logged in user profile.
     * Trigger post save procedures on a positive result.
     *
     * @param \OxidEsales\Eshop\Application\Model\User|\OxidEsales\Eshop\Application\Model\Address|\OxidEsales\Eshop\Core\Model\BaseModel $oObject
     */
    public function postSave(\OxidEsales\Eshop\Core\Model\BaseModel $oObject)
    {
        if ($oObject instanceof \OxidEsales\Eshop\Application\Model\User) {
            $this->_checkIfCurrentUserAndPostSave($oObject);
        } elseif ($oObject instanceof \OxidEsales\Eshop\Application\Model\Address) {
            $this->_checkIfAddressBelongsToCurrentUserAndPostSave($oObject);
        }
    }

    /**
     * Check if given user object is the same as current logged in user.
     * Trigger post save procedures on a positive result.
     *
     * @param \OxidEsales\Eshop\Application\Model\User $oUser
     */
    protected function _checkIfCurrentUserAndPostSave($oUser)
    {
        $oCurrentUser = $this->getShop()->getUser();

        if (($oCurrentUser instanceof \OxidEsales\Eshop\Application\Model\User) and ($oCurrentUser->getId() === $oUser->getId())) {
            $this->_postSave();
        }
    }

    /**
     * Check if given address object the active shipping addresses of the current logged in user.
     * Trigger post save procedures on a positive result.
     *
     * @param \OxidEsales\Eshop\Application\Model\Address $oAddress
     */
    protected function _checkIfAddressBelongsToCurrentUserAndPostSave(\OxidEsales\Eshop\Application\Model\Address $oAddress)
    {
        $oShop = $this->getShop();
        $oCurrentUser = $oShop->getUser();

        if (($oCurrentUser instanceof \OxidEsales\Eshop\Application\Model\User) and
            $oShop->getSessionVariable('blshowshipaddress') and
            $this->_isItUserActiveShippingAddress($oCurrentUser, $oAddress)
        ) {
            $this->_postSave();
        }
    }

    /**
     * Check if an address matches current user active shipping address.
     *
     * @param \OxidEsales\Eshop\Application\Model\User    $oCurrentUser
     * @param \OxidEsales\Eshop\Application\Model\Address $oAddress
     *
     * @return bool
     */
    protected function _isItUserActiveShippingAddress(\OxidEsales\Eshop\Application\Model\User $oCurrentUser, \OxidEsales\Eshop\Application\Model\Address $oAddress)
    {
        $oShippingAddress = $oCurrentUser->getSelectedAddress();

        return (($oShippingAddress instanceof \OxidEsales\Eshop\Application\Model\Address) and ($oShippingAddress->getId() === $oAddress->getId()));
    }

    /**
     * User profile post save hook.
     * Triggers on current active user changes of their selected shipping address changes.
     * Completely resets PayPal Plus payment session.
     */
    protected function _postSave()
    {
        $this->getShop()->getPayPalPlusSession()->reset();
    }
}
