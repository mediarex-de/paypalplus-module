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
 * @copyright (C) OXID eSales AG 2003-2015
 */

/**
 * Class paypPayPalPlusRefundHandlerTest
 * Unit tests for paypPayPalPlusRefundHandler helper class.
 *
 * @see paypPayPalPlusRefundHandler
 */
class paypPayPalPlusRefundHandlerTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusRefundHandler
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock('paypPayPalPlusRefundHandler', array('__call',));
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    public function testGetRefund_nothingSet_returnNull()
    {
        $this->assertNull($this->SUT->getRefund());
    }

    public function testGetRefund_refundObjectSet_returnTheObject()
    {
        $oRefund = new PayPal\Api\Refund();

        $this->SUT->setRefund($oRefund);

        $this->assertSame($oRefund, $this->SUT->getRefund());
    }

    public function testInit()
    {
        $this->assertNull($this->SUT->getRefund());

        $this->SUT->init('150.05', 'EUR', 'SALE-ID-123');

        $oRefund = $this->SUT->getRefund();
        $this->assertInstanceOf('PayPal\Api\Refund', $oRefund);
        $this->assertSame('SALE-ID-123', $oRefund->getSaleId());

        $oAmount = $oRefund->getAmount();
        $this->assertInstanceOf('PayPal\Api\Amount', $oAmount);
        $this->assertSame('150.05', $oAmount->getTotal());
        $this->assertSame('EUR', $oAmount->getCurrency());
    }

    public function testRefund_exceptionThrown_returnErrorAsString()
    {
        $oApiContext = $this->getMock('PayPal\Rest\ApiContext');

        $oException = new oxException();

        $oSale = $this->getMock('PayPal\Api\Sale', array('setId', 'refund'));
        $oSale->expects($this->once())->method('setId')->with('BAD-SALE-ID');
        $oSale->expects($this->once())->method('refund')
            ->with($this->isInstanceOf('PayPal\Api\Refund'), $oApiContext)
            ->will($this->throwException($oException));

        $oSdk = $this->getMock('paypPayPalPlusSdk', array('newSale'));
        $oSdk->expects($this->once())->method('newSale')->will($this->returnValue($oSale));

        $oRefundData = $this->getMock('paypPayPalPlusRefundData', array('__call', '__construct', 'save'));
        $oRefundData->expects($this->never())->method('save');

        oxTestModules::addModuleObject('paypPayPalPlusRefundData', $oRefundData);

        /** @var PHPUnit_Framework_MockObject_MockObject|paypPayPalPlusRefundHandler $SUT */
        $SUT = $this->getMock('paypPayPalPlusRefundHandler', array('__call', 'getSdk', '_getOrderBySaleId'));
        $SUT->expects($this->any())->method('_getOrderBySaleId')->will($this->returnValue(new paypPayPalPlusOxOrder()));
        $SUT->expects($this->any())->method('getSdk')->will($this->returnValue($oSdk));

        $SUT->init('10.0', 'USD', 'BAD-SALE-ID');

        $this->assertSame('_PAYP_PAYPALPLUS_ERROR_', $SUT->refund($oApiContext));
    }

    public function testRefund_refundWasSuccessful_saveTheRefundObjectAndReturnTrue()
    {
        //@Todo: Test relies on config setting from module in shop(RefundOnInvoice). "wrong" setting -> test fails, need to be refactored.
        $oApiContext = $this->getMock('PayPal\Rest\ApiContext');

        $oSuccessfulRefund = $this->getMock('PayPal\Api\Refund');

        $oSale = $this->getMock('PayPal\Api\Sale', array('setId', 'refund'));
        $oSale->expects($this->once())->method('setId')->with('GOOD-SALE-ID');
        $oSale->expects($this->once())->method('refund')
            ->with($this->isInstanceOf('PayPal\Api\Refund'), $oApiContext)
            ->will($this->returnValue($oSuccessfulRefund));

        $oSdk = $this->getMock('paypPayPalPlusSdk', array('newSale'));
        $oSdk->expects($this->once())->method('newSale')->will($this->returnValue($oSale));

        $oRefundData = $this->getMock('paypPayPalPlusRefundData', array('__call', '__construct', 'save'));
        $oRefundData->expects($this->once())->method('save')->will($this->returnValue('testRefundOXID1'));

        oxTestModules::addModuleObject('paypPayPalPlusRefundData', $oRefundData);

        /** @var PHPUnit_Framework_MockObject_MockObject|paypPayPalPlusRefundHandler $SUT */
        $SUT = $this->getMock('paypPayPalPlusRefundHandler', array('__call', 'getSdk', '_discountRefund', '_getOrderBySaleId'));
        $SUT->expects($this->any())->method('_getOrderBySaleId')->will($this->returnValue(new paypPayPalPlusOxOrder()));
        $SUT->expects($this->any())->method('getSdk')->will($this->returnValue($oSdk));
        $SUT->expects($this->once())->method('_discountRefund');

        $SUT->init('49.99', 'EUR', 'GOOD-SALE-ID');

        $this->assertTrue($SUT->refund($oApiContext));
    }

    public function testRefund_returnsErrorAsString_onSuccesfulRefundAndFailingSaveRefundObject()
    {
        $oApiContext = $this->getMock('PayPal\Rest\ApiContext');

        $oSuccessfulRefund = $this->getMock('PayPal\Api\Refund');

        $oSale = $this->getMock('PayPal\Api\Sale', array('setId', 'refund'));
        $oSale->expects($this->once())->method('setId')->with('GOOD-SALE-ID');
        $oSale->expects($this->once())->method('refund')
            ->with($this->isInstanceOf('PayPal\Api\Refund'), $oApiContext)
            ->will($this->returnValue($oSuccessfulRefund));

        $oSdk = $this->getMock('paypPayPalPlusSdk', array('newSale'));
        $oSdk->expects($this->once())->method('newSale')->will($this->returnValue($oSale));

        /** @var PHPUnit_Framework_MockObject_MockObject|paypPayPalPlusRefundHandler $SUT */
        $SUT = $this->getMock('paypPayPalPlusRefundHandler', array('__call', 'getSdk', '_saveRefundData',  '_discountRefund', '_getOrderBySaleId'));
        $SUT->expects($this->any())->method('_getOrderBySaleId')->will($this->returnValue(new paypPayPalPlusOxOrder()));
        $SUT->expects($this->any())->method('getSdk')->will($this->returnValue($oSdk));
        $SUT->expects($this->once())->method('_saveRefundData')->will($this->returnValue(false));
        $SUT->expects($this->never())->method('_discountRefund');

        $SUT->init('49.99', 'EUR', 'GOOD-SALE-ID');

        $sActualResult = $SUT->refund($oApiContext);
        $this->assertSame('_PAYP_PAYPALPLUS_ERROR_', $sActualResult);
    }

    public function testDiscountRefund_throwsExpectedException_onRefundError() {

        $sExpectedException = 'Exception';
        $this->setExpectedException($sExpectedException, 'PAYP_PAYPALPLUS_ERROR_REFUND_COULD_NOT_BE_DISCOUNTED');

        $SUT = $this->getMockBuilder('paypPayPalPlusRefundHandler')
            ->setMethods(
                array('_getRefundDataById', '_getPayPalApiRefundObject', '_getOrderBySaleId'))
            ->getMock()
        ;

        $oPayPalPlusRefundDataMock = $this->getMock('paypPayPalPlusRefundData', array('__call', '__construct',));
        $SUT->expects($this->once())->method('_getRefundDataById')->will($this->returnValue($oPayPalPlusRefundDataMock));

        $oPayPalApiRefundMock = $this->getMock('\PayPal\Api\Refund', array('getAmount'));
        $oPayPalApiRefundMock->expects($this->once())->method('getAmount')->will($this->returnValue( new PayPal\Api\Amount()));
        $SUT->expects($this->once())->method('_getPayPalApiRefundObject')->will($this->returnValue($oPayPalApiRefundMock));

        $oOrderMock = $this->getMock('paypPayPalPlusOxOrder', array('__call', '__construct', 'discountRefund'));
        $oOrderMock->expects($this->once())->method('discountRefund')->will($this->returnValue(false));
        $SUT->expects($this->once())->method('_getOrderBySaleId')->will($this->returnValue($oOrderMock));

        $this->invokeMethod($SUT, '_discountRefund', array('Something funny'));

    }

    public function testGetOrderBySaleId_throwsException_onInvalidId()
    {
        importTestdataFile("removePaymentOrderData.sql");
        importTestdataFile("removePaymentData.sql");
        importTestdataFile("addPaymentData.sql");

        $sExpectedException = 'Exception';
        $this->setExpectedException($sExpectedException, 'PAYP_PAYPALPLUS_ERROR_ORDER_COULD_NOT_BE_LOADED_FROM_DATABASE');

        $this->invokeMethod($this->SUT, '_getOrderBySaleId', array('testPaymentSaleId'));

        importTestdataFile("removePaymentData.sql");
    }

    public function testGetOrderBySaleId_returnsInstanceOfpaypPayPalPlusOxOrder_onValidId()
    {
        importTestdataFile("removePaymentOrderData.sql");
        importTestdataFile("removePaymentData.sql");

        importTestdataFile("addPaymentOrderData.sql");
        importTestdataFile("addPaymentData.sql");

        $oResult = $this->invokeMethod($this->SUT, '_getOrderBySaleId', array('testPaymentSaleId'));

        $this->assertInstanceOf('paypPayPalPlusOxOrder', $oResult);

        importTestdataFile("removePaymentOrderData.sql");
        importTestdataFile("removePaymentData.sql");
    }

    public function testGetRefundDataById_throwsException_onInvalidId()
    {
        $sExpectedException = 'Exception';
        $this->setExpectedException($sExpectedException, 'PAYP_PAYPALPLUS_ERROR_REFUND_DATA_COULD_NOT_BE_LOADED_FROM_DATABASE');

        $this->invokeMethod($this->SUT, '_getRefundDataById', array('NONEXISTING_ID'));
    }

    public function testGetRefundDataById_returnsInstanceOfpaypPayPalPlusRefundData_onValidId()
    {
        importTestdataFile("removeRefundData.sql");
        importTestdataFile("addRefundData.sql");

        $oResult = $this->invokeMethod($this->SUT, '_getRefundDataById', array('testRefundOXID1'));

        $this->assertInstanceOf('paypPayPalPlusRefundData', $oResult);

        importTestdataFile("removeRefundData.sql");
    }

    public function testGetPaymentDataBySaleId_throwsException_onInvalidId()
    {
        $sExpectedException = 'Exception';
        $this->setExpectedException($sExpectedException, 'PAYP_PAYPALPLUS_ERROR_PAYMENT_DATA_COULD_NOT_BE_LOADED_FROM_DATABASE');

        $this->invokeMethod($this->SUT, '_getPaymentDataBySaleId', array('NONEXISTING_ID'));
    }

    public function testGetPaymentDataBySaleId_returnspaypPayPalPlusPaymentData_onValidId()
    {
        importTestdataFile("removePaymentData.sql");
        importTestdataFile("addPaymentData.sql");

        $oResult = $this->invokeMethod($this->SUT, '_getPaymentDataBySaleId', array('testPaymentSaleId'));

        $this->assertInstanceOf('paypPayPalPlusPaymentData', $oResult);

        importTestdataFile("removePaymentData.sql");
    }

    public function testGetPayPalApiRefundObject_throwsException_onNonrestorableRefundObject()
    {
        $sExpectedException = 'Exception';
        $this->setExpectedException($sExpectedException, 'PAYP_PAYPALPLUS_ERROR_REFUND_OBJECT_COULD_NOT_BE_RESTORED');

        $this->invokeMethod($this->SUT, '_getPayPalApiRefundObject', array(new paypPayPalPlusRefundData()));
    }

    public function testGetPayPalApiRefundObject_returnsInstanceOfPayPalApiRefund_onValidData()
    {
        $oRefundData = $this->getMock('paypPayPalPlusRefundData', array('__call', '__construct', 'getRefundObject'));
        $oRefundData->expects($this->once())->method('getRefundObject')->will($this->returnValue(new \PayPal\Api\Refund()));

        $oResult = $this->invokeMethod($this->SUT, '_getPayPalApiRefundObject', array($oRefundData));

        $this->assertInstanceOf('\PayPal\Api\Refund', $oResult);
    }
}
