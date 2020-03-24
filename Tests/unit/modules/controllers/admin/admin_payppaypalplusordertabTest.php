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
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2015
 */

/**
 * Class Admin_paypPayPalPlusOrderTabTest.
 * Integration tests for admin controller class Admin_paypPayPalPlusOrderTab.
 *
 * @see Admin_paypPayPalPlusOrderTab
 */
class Admin_paypPayPalPlusOrderTabTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var Admin_paypPayPalPlusOrderTab
     */
    protected $SUT;


    /**
     * @inheritDoc
     *
     * Set SUT state before test and import test data.
     */
    public function setUp()
    {
        parent::setUp();

        importTestdataFile('removeBackendControllerTestData.sql');
        importTestdataFile('addBackendControllerTestData.sql');

        $this->SUT = new Admin_paypPayPalPlusOrderTab();
    }

    /**
     * @inheritDoc
     *
     * Remove test data.
     */
    public function tearDown()
    {
        parent::tearDown();

        importTestdataFile('removeBackendControllerTestData.sql');
    }


    public function testGetShop()
    {
        $this->assertInstanceOf('paypPayPalPlusShop', $this->SUT->getShop());
    }


    public function testGetDataUtils()
    {
        $this->assertInstanceOf('paypPayPalPlusDataAccess', $this->SUT->getDataUtils());
    }


    public function testGetDataConverter()
    {
        $this->assertInstanceOf('paypPayPalPlusDataConverter', $this->SUT->getDataConverter());
    }


    public function testGetOrder_orderCouldNotBeLoaded_returnNull()
    {
        $this->assertNull($this->SUT->getOrder());
    }

    public function testGetOrder_orderIsLoaded_returnTheOrderObject()
    {
        modConfig::setRequestParameter('oxid', 'payp_pay_pal_plus_test_order_1');

        $oOrder = $this->SUT->getOrder();

        $this->assertInstanceOf('oxOrder', $oOrder);
        $this->assertSame('payp_pay_pal_plus_test_order_1', $oOrder->getId());
    }


    public function testGetOrderPayment_relatedOrderCouldNotBeLoaded_returnNull()
    {
        $this->assertNull($this->SUT->getOrderPayment());
    }

    public function testGetOrderPayment_paymentDataCouldNotBeLoaded_returnNull()
    {
        modConfig::setRequestParameter('oxid', 'payp_pay_pal_plus_test_order_1');

        $this->assertNull($this->SUT->getOrderPayment());
    }

    public function testGetOrderPayment_paymentDataAndRelatedOrderAreLoaded_returnThePaymentObject()
    {
        modConfig::setRequestParameter('oxid', 'payp_pay_pal_plus_test_order_2');

        $oPaymentData = $this->SUT->getOrderPayment();

        $this->assertInstanceOf('paypPayPalPlusPaymentData', $oPaymentData);
        $this->assertSame('payp_pay_pal_plus_test_payment_2', $oPaymentData->getId());
    }


    public function testGetRefundErrorMessage_nothingSet_returnEmptyString()
    {
        $this->assertSame('', $this->SUT->getRefundErrorMessage());
    }

    public function testGetRefundErrorMessage_errorMessageIsSet_returnTheErrorMessage()
    {
        $this->SUT->setRefundErrorMessage(404);

        $this->assertSame('404', $this->SUT->getRefundErrorMessage());
    }


    public function testGetPaymentCurrencyCode_paymentNotLoaded_returnEmptyString()
    {
        $this->assertSame('', $this->SUT->getPaymentCurrencyCode());
    }

    public function testGetPaymentCurrencyCode_paymentIsLoaded_returnThePaymentCurrencyCode()
    {
        modConfig::setRequestParameter('oxid', 'payp_pay_pal_plus_test_order_3');

        $this->assertSame('USD', $this->SUT->getPaymentCurrencyCode());
    }


    public function testGetRemainingRefundsCount_paymentNotLoaded_returnMaxValue()
    {
        $this->assertSame(10, $this->SUT->getRemainingRefundsCount());
    }

    public function testGetRemainingRefundsCount_paymentHasNoRefundsYet_returnMaxValue()
    {
        modConfig::setRequestParameter('oxid', 'payp_pay_pal_plus_test_order_2');

        $this->assertSame(10, $this->SUT->getRemainingRefundsCount());
    }

    public function testGetRemainingRefundsCount_paymentHasSomeRefunds_returnRemainingNumberOrRefundsLeft()
    {
        modConfig::setRequestParameter('oxid', 'payp_pay_pal_plus_test_order_3');

        $this->assertSame(8, $this->SUT->getRemainingRefundsCount());
    }

    public function testGetRemainingRefundsCount_paymentHasExceededMaxRefundsCount_returnZero()
    {
        modConfig::setRequestParameter('oxid', 'payp_pay_pal_plus_test_order_4');

        $this->assertSame(0, $this->SUT->getRemainingRefundsCount());
    }


    public function testGetRemainingRefundAmount_paymentIsNotLoaded_returnZero()
    {
        $this->assertSame(0.0, $this->SUT->getRemainingRefundAmount());
    }

    public function testGetRemainingRefundAmount_paymentHasNoRefunds_returnPaymentAmount()
    {
        modConfig::setRequestParameter('oxid', 'payp_pay_pal_plus_test_order_2');

        $this->assertSame(15.01, $this->SUT->getRemainingRefundAmount());
    }

    public function testGetRemainingRefundAmount_paymentHasSomeRefunds_returnRemainingAmountStillPossibleToRefund()
    {
        modConfig::setRequestParameter('oxid', 'payp_pay_pal_plus_test_order_3');

        $this->assertSame(5.7, $this->SUT->getRemainingRefundAmount());
    }

    public function testGetRemainingRefundAmount_allWasRefunded_returnZero()
    {
        modConfig::setRequestParameter('oxid', 'payp_pay_pal_plus_test_order_6');

        $this->assertSame(0.0, $this->SUT->getRemainingRefundAmount());
    }

    public function testGetRemainingRefundAmount_moreThanAllWasRefunded_returnZero()
    {
        modConfig::setRequestParameter('oxid', 'payp_pay_pal_plus_test_order_7');

        $this->assertSame(0.0, $this->SUT->getRemainingRefundAmount());
    }


    public function testIsPayPalPlusOrder_orderNotLoaded_returnFalse()
    {
        $this->assertFalse($this->SUT->isPayPalPlusOrder());
    }

    public function testIsPayPalPlusOrder_orderPaymentMethodIsNotPayPalPlus_returnFalse()
    {
        modConfig::setRequestParameter('oxid', 'payp_pay_pal_plus_test_order_1');

        $this->assertFalse($this->SUT->isPayPalPlusOrder());
    }

    public function testIsPayPalPlusOrder_orderPaymentMethodIsPayPalPlus_returnTrue()
    {
        modConfig::setRequestParameter('oxid', 'payp_pay_pal_plus_test_order_2');

        $this->assertTrue($this->SUT->isPayPalPlusOrder());
    }


    public function testIsRefundPossible_orderPaymentMethodIsNotPayPalPlus_returnFalse()
    {
        modConfig::setRequestParameter('oxid', 'payp_pay_pal_plus_test_order_1');

        $this->assertFalse($this->SUT->isRefundPossible());
    }

    public function testIsRefundPossible_orderPaymentHasNotCompletedStatus_returnFalse()
    {
        modConfig::setRequestParameter('oxid', 'payp_pay_pal_plus_test_order_5');

        $this->assertFalse($this->SUT->isRefundPossible());
    }

    public function testIsRefundPossible_orderPaymentHasReachedMaximumRefundsCount_returnFalse()
    {
        modConfig::setRequestParameter('oxid', 'payp_pay_pal_plus_test_order_4');

        $this->assertFalse($this->SUT->isRefundPossible());
    }

    public function testIsRefundPossible_orderPaymentWasFullyRefunded_returnFalse()
    {
        modConfig::setRequestParameter('oxid', 'payp_pay_pal_plus_test_order_6');

        $this->assertFalse($this->SUT->isRefundPossible());
    }

    public function testIsRefundPossible_orderIsPayerWithCompletedPayPalPlusPaymentAndStillHasRefundsAndAmountLeft_returnTrue()
    {
        modConfig::setRequestParameter('oxid', 'payp_pay_pal_plus_test_order_3');

        $this->assertTrue($this->SUT->isRefundPossible());
    }


    /**
     * @dataProvider formatPriceDataProvider
     */
    public function testFormatPrice($sCondition, $sOrderId, $mPrice, $sExpectedReturn)
    {
        modConfig::setRequestParameter('oxid', $sOrderId);

        $this->assertSame($sExpectedReturn, $this->SUT->formatPrice($mPrice), $sCondition);
    }

    public function formatPriceDataProvider()
    {
        return array(
            array('No order loaded', '', 1, '1.00 <small></small>'),
            array('No payment data loaded', 'payp_pay_pal_plus_test_order_1', '10.999', '11.00 <small></small>'),
            array('Payment was in CHF', 'payp_pay_pal_plus_test_order_2', '15.01', '15.01 <small>CHF</small>'),
            array('Payment was in USD', 'payp_pay_pal_plus_test_order_3', '19.79', '19.79 <small>USD</small>'),
            array('Payment was in EUR', 'payp_pay_pal_plus_test_order_4', '100.00', '100.00 <small>EUR</small>'),
            array('Price rounding down', 'payp_pay_pal_plus_test_order_4', '100.0039', '100.00 <small>EUR</small>'),
            array('Price rounding up', 'payp_pay_pal_plus_test_order_4', 99.9999999, '100.00 <small>EUR</small>'),
            array('Price is integer', 'payp_pay_pal_plus_test_order_4', 5, '5.00 <small>EUR</small>'),
            array('Price is string', 'payp_pay_pal_plus_test_order_4', '12.1 ', '12.10 <small>EUR</small>'),
            array('Price is empty', 'payp_pay_pal_plus_test_order_4', '', '0.00 <small>EUR</small>'),
        );
    }


    /**
     * @dataProvider actionRefundDataProvider
     */
    public function testActionRefund($sCondition, $sOrderId, $sSaleId, $sRefundAmount, $blRefundSent,
                                     $sExpectedError, $mRefundRetunr = null, $sAmountSet = '', $sCurrencySet = '')
    {
        modConfig::setRequestParameter('oxid', $sOrderId);
        modConfig::setRequestParameter('saleId', $sSaleId);
        modConfig::setRequestParameter('refundAmount', $sRefundAmount);

        $oRefundHandler = $this->getMock('paypPayPalPlusRefundHandler', array('__call', 'init', 'refund'));

        if (empty($blRefundSent)) {
            $oRefundHandler->expects($this->never())->method('init');
            $oRefundHandler->expects($this->never())->method('refund');
        } else {
            $oApiContext = $this->getMock('PayPal\Rest\ApiContext', array(), array('clinet_id', 'secret'));

            $oPayPalPlusSession = $this->getMock('paypPayPalPlusSession', array('__call', 'init', 'getApiContext'));
            $oPayPalPlusSession->expects($this->once())->method('getApiContext')->will(
                $this->returnValue($oApiContext)
            );

            \OxidEsales\Eshop\Core\Registry::set(\OxidProfessionalServices\PayPalPlus\Core\Session::class, $oPayPalPlusSession);

            $oRefundHandler->expects($this->once())->method('init')->with($sAmountSet, $sCurrencySet, $sSaleId);
            $oRefundHandler->expects($this->once())->method('refund')->with($oApiContext)->will(
                $this->returnValue($mRefundRetunr)
            );
        }

        \OxidEsales\Eshop\Core\Registry::set(\OxidProfessionalServices\PayPalPlus\Core\RefundHandler::class, $oRefundHandler);

        $this->SUT->actionRefund();

        $this->assertSame(
            oxRegistry::getLang()->translateString($sExpectedError),
            $this->SUT->getRefundErrorMessage(),
            $sCondition
        );
    }

    public function actionRefundDataProvider()
    {
        return array(
            array(
                'Order is not loaded',
                '',
                'payp_pay_pal_plus_test_sale_2',
                '1',
                false,
                'PAYP_PAYPALPLUS_ERR_INVALID_REQUEST'
            ),

            array(
                'Order has no payment data',
                'payp_pay_pal_plus_test_order_1',
                'payp_pay_pal_plus_test_sale_1',
                '1',
                false,
                'PAYP_PAYPALPLUS_ERR_INVALID_REQUEST'
            ),

            array(
                'Sale ID does not match order payment',
                'payp_pay_pal_plus_test_order_2',
                'payp_pay_pal_plus_test_sale_1',
                '1',
                false,
                'PAYP_PAYPALPLUS_ERR_INVALID_REQUEST'
            ),

            array(
                'Amount is empty',
                'payp_pay_pal_plus_test_order_2',
                'payp_pay_pal_plus_test_sale_2',
                '',
                false,
                'PAYP_PAYPALPLUS_ERR_INVALID_AMOUNT'
            ),

            array(
                'Amount is not a number',
                'payp_pay_pal_plus_test_order_2',
                'payp_pay_pal_plus_test_sale_2',
                'ten',
                false,
                'PAYP_PAYPALPLUS_ERR_INVALID_AMOUNT'
            ),

            array(
                'Amount is negative',
                'payp_pay_pal_plus_test_order_2',
                'payp_pay_pal_plus_test_sale_2',
                '-10',
                false,
                'PAYP_PAYPALPLUS_ERR_INVALID_AMOUNT'
            ),

            array(
                'Amount is zero',
                'payp_pay_pal_plus_test_order_2',
                'payp_pay_pal_plus_test_sale_2',
                '0.001',
                false,
                'PAYP_PAYPALPLUS_ERR_INVALID_AMOUNT'
            ),

            array(
                'A number of refunds has already reached maximum',
                'payp_pay_pal_plus_test_order_4',
                'payp_pay_pal_plus_test_sale_4',
                '0.01',
                false,
                'PAYP_PAYPALPLUS_ERR_REFUND_NOT_POSSIBLE'
            ),

            array(
                'Validation passed, but refund returned general error code',
                'payp_pay_pal_plus_test_order_2',
                'payp_pay_pal_plus_test_sale_2',
                '1,115',
                true,
                'PAYP_PAYPALPLUS_ERR_REFUND_API_EXCEPTION',
                '_PAYP_PAYPALPLUS_ERROR_',
                '1.12',
                'CHF'
            ),

            array(
                'Validation passed, but refund returned an error',
                'payp_pay_pal_plus_test_order_2',
                'payp_pay_pal_plus_test_sale_2',
                '10',
                true,
                'Some validation error. Please try again!',
                'Some validation error. Please try again!',
                '10.00',
                'CHF'
            ),

            array(
                'Validation passed, refund was successful',
                'payp_pay_pal_plus_test_order_2',
                'payp_pay_pal_plus_test_sale_2',
                '10',
                true,
                '',
                true,
                '10.00',
                'CHF'
            ),
        );
    }
}
