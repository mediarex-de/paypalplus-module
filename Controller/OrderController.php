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

namespace OxidEsales\PayPalPlus\Controller;

/**
 * Class \OxidEsales\PayPalPlus\Controller\OrderController.
 * Overloads Order controller.
 *
 * @see \OxidEsales\Eshop\Application\Controller\OrderController
 */
class OrderController extends OrderController_parent
{
    /**
     * Overloaded parent method.
     * Collect and save PayPal parameters if any returned.
     * Optionally resets payment method if it was requested.
     *
     * @return mixed
     */
    public function init()
    {
        $oShop = \OxidEsales\PayPalPlus\Core\Shop::getShop();
        $oPayPalConfig = $oShop->getPayPalPlusConfig();

        // Set payment if it was requested
        $this->_setPayment();

        $blReturnedFromPayPal = (bool) $oShop->getRequestParameter($oPayPalConfig->getSuccessfulReturnParameter());
        $sPaymentId = (string) $oShop->getRequestParameter($oPayPalConfig->getPayPalPaymentIdParameter());
        $sPayerId = (string) $oShop->getRequestParameter($oPayPalConfig->getPayPalPayerIdParameter());

        if ($blReturnedFromPayPal) {
            $oPayPalSession = $oShop->getPayPalPlusSession();

            if ($oShop->getValidator()->isPaymentCreated() and ($sPaymentId === $oPayPalSession->getPaymentId())) {
                $oPayPalSession->setApprovedPayment($oPayPalSession->getPayment());
                $oPayPalSession->setPayerId($sPayerId);
            } else {
                $oShop->getErrorHandler()->setPaymentErrorAndRedirect(5);
            }
        }

        $this->_paypPayPalPlusOrder_init_parent();
    }

    /**
     * Check forced payment ID parameter to load and set a payment method on basket (recalculation triggered).
     */
    protected function _setPayment()
    {
        $oShop = \OxidEsales\PayPalPlus\Core\Shop::getShop();

        $sPaymentId = (string) $oShop->getRequestParameter($oShop->getPayPalPlusConfig()->getForcedPaymentParameter());

        if (!empty($sPaymentId)) {
            $oBasket = $oShop->getBasket();

            /** @var \OxidEsales\Eshop\Application\Model\Payment $oPayment */
            $oPayment = $oShop->getNew(\OxidEsales\Eshop\Application\Model\Payment::class);

            if ($oPayment->load($sPaymentId) and $oPayment->getId()) {
                $oPayment->calculate($oBasket);

                $oBasket->setPayment($sPaymentId);
                $oBasket->calculateBasket(true);

                $oShop->setBasket($oBasket);
            }
        }
    }

    /**
     * Parent `init` call. Method required for mocking.
     *
     * @codeCoverageIgnore
     */
    protected function _paypPayPalPlusOrder_init_parent()
    {
        parent::init();
    }
}
