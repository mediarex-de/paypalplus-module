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
 * @link          http://www.oxid-esales.com
 * @copyright (C) PayPal (Europe) S.à r.l. et Cie, S.C.A. 2015
 */

namespace OxidEsales\PayPalPlus\Core;

/**
 * Class \OxidEsales\PayPalPlus\Core\PdfArticleSummary
 *
 * Extend PdfArticleSummary to be able to display additional payment instructions after the article summary.
 *
 * Third party integration and not testable in all shop versions
 *
 * @codeCoverageIgnore
 */
if (class_exists('PdfArticleSummary')) {
    class PdfArticleSummary extends PdfArticleSummary_parent
    {
        /**
         * @inheritdoc
         *
         * Add the possibility to add payment instructions to the due date or 'PayUntilInfo', as it is called in the parent
         * function.
         */
        protected function _setPayUntilInfo(&$iStartPos)
        {
            $oPaymentInstructions = $this->_getPaymentInstructions();

            if ($oPaymentInstructions) {
                $iLang = $this->_oData->getSelectedLang();
                $oPdfArticleSummaryPaymentInstructions = new paypPayPalPlusPdfArticleSummaryPaymentInstructions();
                $oPdfArticleSummaryPaymentInstructions->setPdfArticleSummary($this);
                $oPdfArticleSummaryPaymentInstructions->setPaymentInstructions($oPaymentInstructions);
                $oPdfArticleSummaryPaymentInstructions->setOrder($this->_getOrder());
                $oPdfArticleSummaryPaymentInstructions->addPaymentInstructions($iStartPos, $iLang);
            } else {
                $text = $this->_oData->translate('ORDER_OVERVIEW_PDF_PAYUPTO') . date('d.m.Y', strtotime('+' . $this->_oData->getPaymentTerm() . ' day', strtotime($this->_oData->oxorder__oxbilldate->value)));
                $this->font($this->getFont(), '', 10);
                $this->text(15, $iStartPos + 4, $text);
                $iStartPos += 4;
            }
        }

        /**
         * Return an instance of the related order.
         *
         * @return \OxidEsales\Eshop\Application\Model\Order
         */
        protected function _getOrder()
        {
            $sOrderId = $this->_getOrderId();
            $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
            $oOrder->load($sOrderId);

            return $oOrder;
        }

        /**
         * Get the payment instructions from the order.
         *
         * @return null|\OxidEsales\PayPalPlus\Model\PuiData|void
         */
        protected function _getPaymentInstructions()
        {
            $oPaymentInstructions = null;

            $sOrderId = $this->_getOrderId();

            $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
            if ($oOrder->load($sOrderId)) {
                $oPaymentInstructions = $oOrder->getPaymentInstructions();
            }

            return $oPaymentInstructions;
        }

        /**
         * Return the ID or the current order.
         * Needed for testing.
         *
         * @codeCoverageIgnore
         *
         * @return mixed
         */
        protected function _getOrderId()
        {
            $sOrderId = $this->_oData->getId();

            return $sOrderId;
        }
    }
}