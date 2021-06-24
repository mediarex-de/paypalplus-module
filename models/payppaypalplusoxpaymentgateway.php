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

/**
 * Class paypPayPalPlusOxPaymentGateway.
 * Overloaded oxPaymentGateway model.
 *
 * @see oxPaymentGateway
 */
class paypPayPalPlusOxPaymentGateway extends paypPayPalPlusOxPaymentGateway_parent
{

    /**
     * OXID eShop methods wrapper instance.
     *
     * @var null|paypPayPalPlusShop
     */
    protected $_oShop = null;


    /**
     * Error Message.
     */
    protected $_lastPaypalPlusError = 'PAYP_PAYPALPLUS_ERROR_PAYPAL_ERROR_OR_SESSION_EXPIRED';

    /**
     * error number.
     *
     * @var int
     */
    protected $_LastPaypalPlusErrorNo = -1;

    /**
     * Get OXID eShop wrapper.
     *
     * @return paypPayPalPlusShop
     */
    public function getShop()
    {
        if (is_null($this->_oShop)) {
            $this->_oShop = paypPayPalPlusShop::getShop();
        }

        return $this->_oShop;
    }


    /**
     * Overloaded parent method.
     * Check if it is a valid PayPal payment and execute it within PayPal API.
     *
     * @inheritDoc
     */
    public function executePayment($dAmount, & $oOrder)
    {
        /** @var oxUserPayment $oUserPayment */
        $oUserPayment = $this->_getUserPayment();

        $oShop = $this->getShop();
        $oDataUtils = $oShop->getDataAccess();

        $sUserPaymentId = $oDataUtils->invokeGet($oUserPayment, 'oxuserpayments__oxpaymentsid:$', 'value:$');

        if ($sUserPaymentId === $oShop->getPayPalPlusConfig()->getPayPalPlusMethodId()) {
            $oApprovedPayment = $oShop->getPayPalPlusSession()->getApprovedPayment();

            // Check payment and call payment execution
            if (!$oShop->getValidator()->isPaymentCreated($oApprovedPayment)) {
                $this->_iLastErrorNo = $this->_LastPaypalPlusErrorNo;
                $this->_sLastError = $this->_lastPaypalPlusError;

                return false;
            }

            $oPayPalSession = $oShop->getPayPalPlusSession();
            if ($sInvoiceNumber = $oPayPalSession->getInvoiceNumber()) {
                $oOrder->oxorder__oxordernr = new oxField($sInvoiceNumber);
            }
            if (!$this->_executePayment($oOrder)) {
                $this->_iLastErrorNo = $this->_LastPaypalPlusErrorNo;
                $this->_sLastError = $this->_lastPaypalPlusError;

                return false;
            }

            // Reset the PayPal session.
            $oShop->getPayPalPlusSession()->reset();
        }

        return $this->_paypPayPalPlusOxPaymentGateway_executePayment_parent($dAmount, $oOrder);
    }

    /**
     * Issue PayPal payment execution (confirmation) within the API.
     *
     * @param oxOrder $oOrder
     *
     * @return bool
     */
    protected function _executePayment(oxOrder $oOrder)
    {
        $blSuccess = false;

        $oShop = $this->getShop();
        $oPayPalSession = $oShop->getPayPalPlusSession();

        $oApprovedPayment = $oPayPalSession->getApprovedPayment();
        $sPayerId = $oPayPalSession->getPayerId();

        // Immediately reset session since payment would not be considered approved anymore
        $oPayPalSession->unsetApprovedPayment();

        if ($oShop->getValidator()->isPaymentCreated($oApprovedPayment) and !empty($sPayerId)) {

            /** @var paypPayPalPlusPaymentHandler $oPayPalPaymentHandler */
            $oPayPalPaymentHandler = $oShop->getFromRegistry('paypPayPalPlusPaymentHandler');
            $oPayPalPaymentHandler->setPayment($oApprovedPayment);
            $blSuccess = (bool) $oPayPalPaymentHandler->execute($sPayerId, $oPayPalSession->getApiContext(), $oOrder);
        }

        return $blSuccess;
    }

    /**
     * Get variable from extended class
     *
     * @return oxPayment|null
     *
     * @codeCoverageIgnore
     */
    protected function _getUserPayment()
    {
        return $this->_oPaymentInfo;
    }


    /**
     * Parent `executePayment` call. Method required for mocking.
     *
     * @codeCoverageIgnore
     *
     * @inheritDoc
     */
    protected function _paypPayPalPlusOxPaymentGateway_executePayment_parent($dAmount, & $oOrder)
    {
        return parent::executePayment($dAmount, $oOrder);
    }
}
