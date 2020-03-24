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
 * Class \OxidProfessionalServices\PayPalPlus\Model\PaymentDataProvider.
 * A data provider for PlusPaymentData model.
 *
 * @see \OxidProfessionalServices\PayPalPlusModel\PlusDataProvider
 */
class PaymentDataProvider extends \OxidProfessionalServices\PayPalPlus\Model\PlusDataProvider
{
    /**
     * Fields names for paypPayPalPlusPaymentData model.
     *
     * @var array
     */
    protected $_aFields = array(
        'OrderId', 'SaleId', 'PaymentObject', 'DateCreated', 'Total', 'Currency', 'PaymentId', 'Status'
    );

    /**
     * OXID eShop order instance.
     *
     * @var null|\OxidEsales\Eshop\Application\Model\Order
     */
    protected $_oOrder = null;

    /**
     * PayPal SDK Payment instance.
     *
     * @var null|PayPal\Api\Payment
     */
    protected $_oPayment = null;

    /**
     * Initialize the data provider with data source objects.
     *
     * @param \OxidEsales\Eshop\Application\Model\Order     $oOrder
     * @param \PayPal\Api\Payment                           $oPayment
     */
    public function init(\OxidEsales\Eshop\Application\Model\Order $oOrder, PayPal\Api\Payment $oPayment)
    {
        $this->_oOrder = $oOrder;
        $this->_oPayment = $oPayment;
    }

    /**
     * Get all field values for \OxidProfessionalServices\PayPalPlus\Model\PaymentData model.
     *
     * @return array
     */
    public function getData()
    {
        $oUtils = $this->getDataUtils();
        $oConvert = $this->getConverter();

        /** @var \OxidEsales\Eshop\Application\Model\Order $oOrder */
        $oOrder = $this->_getSourceObject(\OxidEsales\Eshop\Application\Model\Order::class);

        /** @var PayPal\Api\Payment $oPayment */
        $oPayment = $this->_getSourceObject('PayPal\Api\Payment');

        return array(
            'OrderId'       => $oConvert->string($oUtils->invokeGet($oOrder, 'getId')),
            'SaleId'        => $oConvert->string(
                $oUtils->invokeGet(
                    $oPayment, 'getTransactions', '0:[]', 'getRelatedResources', '0:[]', 'getSale', 'getId'
                )
            ),
            'PaymentObject' => $oPayment,
            'DateCreated'   => $oConvert->date($oUtils->invokeGet($oPayment, 'getCreateTime')),
            'Total'         => $oConvert->price(
                $oUtils->invokeGet($oPayment, 'getTransactions', '0:[]', 'getAmount', 'getTotal')
            ),
            'Currency'      => $oConvert->string(
                $oUtils->invokeGet($oPayment, 'getTransactions', '0:[]', 'getAmount', 'getCurrency')
            ),
            'PaymentId'     => $oConvert->string($oUtils->invokeGet($oPayment, 'getId')),
            'Status'        => $oConvert->string(
                $oUtils->invokeGet(
                    $oPayment, 'getTransactions', '0:[]', 'getRelatedResources', '0:[]', 'getSale', 'getState'
                )
            )
        );
    }

    /**
     * Get OXID order object and related PayPal payment instance.
     *
     * @return array
     */
    protected function _getSources()
    {
        return array(
            \OxidEsales\Eshop\Application\Model\Order::class            => $this->_oOrder,
            'PayPal\Api\Payment'                                        => $this->_oPayment,
        );
    }
}
