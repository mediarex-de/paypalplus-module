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
 * Class paypPayPalPlusPaymentDataTest
 * Tests for paypPayPalPlusPaymentData model.
 *
 * @see paypPayPalPlusPaymentData
 */
class paypPayPalPlusPaymentDataTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusPaymentData
     */
    protected $SUT;


    /**
     * @inheritDoc
     *
     * Set SUT state before test.
     * Import data to test loading methods
     */
    public function setUp()
    {
        parent::setUp();

        importTestdataFile("removePaymentData.sql");
        importTestdataFile("addPaymentData.sql");

        $this->SUT = $this->getMock('paypPayPalPlusPaymentData', array('__call'));
    }

    /**
     * @inheritDoc
     *
     * Remove test data
     */
    public function tearDown()
    {
        parent::tearDown();

        importTestdataFile("removePaymentData.sql");
    }


    /**
     * test `setOrderId`
     */
    public function testSetOrderId()
    {
        $sOrderId = 'testOrderId';
        $this->SUT->setOrderId($sOrderId);

        $this->assertSame($sOrderId, $this->SUT->getOrderId());
    }

    /**
     * test `_getOrderId`
     */
    public function testGetOrderId()
    {
        $this->assertNull($this->SUT->getOrderId());
    }

    /**
     * test `setSaleId`
     */
    public function testSetSaleId()
    {
        $sSaleId = 'testSaleId';
        $this->SUT->setSaleId($sSaleId);

        $this->assertSame($sSaleId, $this->SUT->getSaleId());
    }

    /**
     * test `getSaleId`
     */
    public function testGetSaleId()
    {
        $this->assertNull($this->SUT->getSaleId());
    }

    /**
     * test `setPaymentId`
     */
    public function testSetPaymentId()
    {
        $sPaymentId = 'testPaymentId';
        $this->SUT->setPaymentId($sPaymentId);

        $this->assertSame($sPaymentId, $this->SUT->getPaymentId());
    }

    /**
     * test `getPaymentId`
     */
    public function testGetPaymentId()
    {
        $this->assertNull($this->SUT->getPaymentId());
    }

    /**
     * test `setStatus`
     */
    public function testSetStatus()
    {
        $sStatus = 'testStatus';
        $this->SUT->setStatus($sStatus);

        $this->assertSame($sStatus, $this->SUT->getStatus());
    }

    /**
     * test `getStatus`
     */
    public function testGetStatus()
    {
        $this->assertNull($this->SUT->getStatus());
    }

    /**
     * test `setDateCreated`
     */
    public function testSetDateCreated()
    {
        $sDateCreated = '2011-11-11 11:11:11';
        $this->SUT->setDateCreated($sDateCreated);

        $this->assertSame($sDateCreated, $this->SUT->getDateCreated());
    }

    /**
     * test `getDateCreated`
     */
    public function testGetDateCreated()
    {
        $this->assertNull($this->SUT->getDateCreated());
    }

    /**
     * test `setTotal`
     */
    public function testSetTotal()
    {
        $sTotal = '999.999';
        $this->SUT->setTotal($sTotal);

        $this->assertEquals($sTotal, $this->SUT->getTotal());
    }

    /**
     * test `getTotal`
     */
    public function testGetTotal()
    {
        $this->assertNull($this->SUT->getTotal());
    }

    /**
     * test `setCurrency`
     */
    public function testSetCurrency()
    {
        $sCurrency = 'LTU';
        $this->SUT->setCurrency($sCurrency);

        $this->assertSame($sCurrency, $this->SUT->getCurrency());
    }

    /**
     * test `getCurrency`
     */
    public function testGetCurrency()
    {
        $this->assertNull($this->SUT->getCurrency());
    }

    /**
     * test `setPaymentObject`
     */
    public function testSetPaymentObject()
    {
        $oPayment = new PayPal\Api\Payment();
        $oPayment->setId('testPaymentId');
        $this->SUT->setPaymentObject($oPayment);

        $this->assertEquals($oPayment, $this->SUT->getPaymentObject());
    }

    /**
     * test `getPaymentObject`
     */
    public function testGetPaymentObject()
    {
        $this->assertNull($this->SUT->getPaymentObject());
    }

    /**
     * test `isRefundable`. Status is okay to refund payment, returns true
     */
    public function testIsRefundable_statusIsGood_returnsTrue()
    {
        $this->SUT->setStatus('completed');

        $this->assertTrue($this->SUT->isRefundable());
    }

    /**
     * test `isRefundable`. Status is not okay to refund payment, returns false
     */
    public function testIsRefundable_statusIsNotGood_returnsFalse()
    {
        $this->SUT->setStatus('notCompletedOrAnyOtherStatus');

        $this->assertFalse($this->SUT->isRefundable());
    }

    /**
     * test `getShop`. Always returns an instance of paypPayPalPlusShop
     */
    public function testGetShop()
    {
        $this->assertInstanceOf('paypPayPalPlusShop', $this->SUT->getShop());
    }

    /**
     * test `loadByOrderId` using integrational testing. Payment found, returning true.
     */
    public function testLoadByOrderId_paymentExists_returnTrue()
    {
        $this->assertTrue($this->SUT->loadByOrderId('testPaymentOrderId'));
        $this->assertNotNull($this->SUT->getId());
    }

    /**
     * test `loadByOrderId` using integrational testing. Payment not found, returning false.
     */
    public function testLoadByOrderId_paymentDoesNotExist_returnsFalse()
    {
        $this->assertFalse($this->SUT->loadByOrderId('testPaymentNotExistingOrderId'));
        $this->assertNull($this->SUT->getId());
    }

    /**
     * test `loadBySaleId` using integrational testing. Payment found, returning true.
     */
    public function testLoadBySaleId_paymentExists_returnTrue()
    {
        $this->assertTrue($this->SUT->loadBySaleId('testPaymentSaleId'));
        $this->assertNotNull($this->SUT->getId());
    }

    /**
     * test `_loadBy` when wrong field name is passed. Returning false.
     */
    public function testLoadBy_wrongLoadByFieldName_returnFalse()
    {
        $this->assertFalse($this->invokeMethod($this->SUT, '_loadBy', array('someRandomFieldName', 'someFieldValue')));
    }

    /**
     * test `loadBySaleId` using integrational testing. Payment not found, returning false.
     */
    public function testLoadBySaleId_paymentDoesNotExist_returnsFalse()
    {
        $this->assertFalse($this->SUT->loadBySaleId('testPaymentNotExistingSaleId'));
        $this->assertNull($this->SUT->getId());
    }

    /**
     * test `loadByPaymentId` using integrational testing. Payment found, returning true.
     */
    public function testLoadByPaymentId_paymentExists_returnTrue()
    {
        $this->assertTrue($this->SUT->loadByPaymentId('testPaymentId'));
        $this->assertNotNull($this->SUT->getId());
    }

    /**
     * test `loadByPaymentId` using integrational testing. Payment not found, returning false.
     */
    public function testLoadByPaymentId_paymentDoesNotExist_returnsFalse()
    {
        $this->assertFalse($this->SUT->loadByPaymentId('testPaymentNotExistingPaymentId'));
        $this->assertNull($this->SUT->getId());
    }

    /**
     * test `getOrder`. Payment rder could not be loaded, exception is thrown, because it should never happen
     * as payment has 1:1 relation with the order
     */
    public function testGetOrder_couldNotLoadOrder_throwException()
    {
        $oOrder = $this->getMock('oxOrder', array('load'));
        $oOrder->expects($this->once())->method('load')->will($this->returnValue(false));

        $oException = $this->getMock('oxException', array('__construct'));

        $oShop = $this->getMock('paypPayPalPlusShop', array('getNew'));
        $oShop->expects($this->any())->method('getNew')->will($this->onConsecutiveCalls($oOrder, $oException));

        $this->SUT = $this->getMock('paypPayPalPlusPaymentData', array('__call', 'getShop'));
        $this->SUT->expects($this->any())->method('getShop')->will($this->returnValue($oShop));

        $this->setExpectedException(
            'oxException',
            oxRegistry::getLang()->translateString('PAYP_PAYPALPLUS_ERROR_NO_ORDER')
        );
        $this->SUT->getOrder();
    }

    /**
     * test `getOrder`. Payment order is loaded and returned.
     */
    public function testGetOrder_orderLoaded_returnsOrder()
    {
        $oOrder = $this->getMock('oxOrder', array('load'));
        $oOrder->expects($this->once())->method('load')->will($this->returnValue(true));

        $oShop = $this->getMock('paypPayPalPlusShop', array('getNew'));
        $oShop->expects($this->once())->method('getNew')->will($this->returnValue($oOrder));

        $this->SUT = $this->getMock('paypPayPalPlusPaymentData', array('__call', 'getShop', '_throwCouldNotLoadOrderError'));
        $this->SUT->expects($this->once())->method('getShop')->will($this->returnValue($oShop));
        $this->SUT->expects($this->never())->method('_throwCouldNotLoadOrderError');

        $this->assertSame($oOrder, $this->SUT->getOrder());
    }

    /**
     * test `getRefundsList`. Payment has no refunds, returns null
     */
    public function testGetRefundsList_noRefundsLoaded_returnsNull()
    {
        $oRefundDataList = $this->getMock('paypPayPalPlusRefundDataList', array('loadRefundsBySaleId', 'count'));
        $oRefundDataList->expects($this->once())->method('loadRefundsBySaleId');
        $oRefundDataList->expects($this->once())->method('count')->will($this->returnValue(0));

        $oShop = $this->getMock('paypPayPalPlusShop', array('getNew'));
        $oShop->expects($this->once())->method('getNew')->will($this->returnValue($oRefundDataList));

        $this->SUT = $this->getMock('paypPayPalPlusPaymentData', array('__call', 'getShop'));
        $this->SUT->expects($this->once())->method('getShop')->will($this->returnValue($oShop));

        $this->assertNull($this->SUT->getRefundsList());
    }

    /**
     * test `getRefundsList`. Payment has refunds, they are loaded and returned in the paypPayPalPlusRefundDataList object
     */
    public function testGetRefundsList_loadsRefunds_returnsRefundsList()
    {
        $oRefundDataList = $this->getMock('paypPayPalPlusRefundDataList', array('loadRefundsBySaleId', 'count'));
        $oRefundDataList->expects($this->once())->method('loadRefundsBySaleId');
        $oRefundDataList->expects($this->once())->method('count')->will($this->returnValue(3));

        $oShop = $this->getMock('paypPayPalPlusShop', array('getNew'));
        $oShop->expects($this->once())->method('getNew')->will($this->returnValue($oRefundDataList));

        $this->SUT = $this->getMock('paypPayPalPlusPaymentData', array('__call', 'getShop'));
        $this->SUT->expects($this->once())->method('getShop')->will($this->returnValue($oShop));

        $this->assertSame($oRefundDataList, $this->SUT->getRefundsList());
    }


    /**
     * test `getTotalAmountRefunded`. Payment has no refunds, returns zero.
     */
    public function testGetTotalAmountRefunded_thereWereNoRefundsForThePayment_returnsZero()
    {
        $oRefundList = $this->getMock(
            'paypPayPalPlusRefundDataList',
            array('__construct', '__call', 'getRefundedSumBySaleId')
        );
        $oRefundList->expects($this->once())->method('getRefundedSumBySaleId')->with('SALE-123-ID')->will(
            $this->returnValue(0.0)
        );
        oxTestModules::addModuleObject('paypPayPalPlusRefundDataList', $oRefundList);

        $this->SUT->setSaleId('SALE-123-ID');

        $this->assertSame(0.0, $this->SUT->getTotalAmountRefunded());
    }

    /**
     * test `getTotalAmountRefunded`. Payment has refunds, returns the sum of all refunds.
     */
    public function testGetTotalAmountRefunded_paymentHasSuccessfulRefunds_returnsCalculatedSumOfAllPaymentRefunds()
    {
        $oRefundList = $this->getMock(
            'paypPayPalPlusRefundDataList',
            array('__construct', '__call', 'getRefundedSumBySaleId')
        );
        $oRefundList->expects($this->once())->method('getRefundedSumBySaleId')->with('SALE-ID')->will(
            $this->returnValue(15)
        );
        oxTestModules::addModuleObject('paypPayPalPlusRefundDataList', $oRefundList);

        $this->SUT->setSaleId('SALE-ID');

        $this->assertSame(15.0, $this->SUT->getTotalAmountRefunded());
    }

    /**
     * test `delete`. Parent method could not delete the payment, other deletions are not continued, returns false
     */
    public function testDelete_parentNotDeleted_returnsFalse()
    {
        $this->SUT = $this->getMock('paypPayPalPlusPaymentData', array('__call', '_paypPayPalPlusPaymentData_delete_parent'));
        $this->SUT->expects($this->once())->method('_paypPayPalPlusPaymentData_delete_parent')->will($this->returnValue(false));

        $this->assertFalse($this->SUT->delete());
    }

    /**
     * test `delete`. Parent method deletes the payment, refunds deletion is called, returns true
     */
    public function testDelete_parentDeleted_deleteBySaleIdCalled()
    {
        $oRefundData = $this->getMock('paypPayPalPlusRefundData', array('deleteBySaleId'));
        $oRefundData->expects($this->once())->method('deleteBySaleId')->will($this->returnValue(true));

        $oShop = $this->getMock('paypPayPalPlusShop', array('getNew'));
        $oShop->expects($this->once())->method('getNew')->will($this->returnValue($oRefundData));

        $this->SUT = $this->getMock('paypPayPalPlusPaymentData', array('__call', 'getShop', '_paypPayPalPlusPaymentData_delete_parent'));
        $this->SUT->expects($this->once())->method('getShop')->will($this->returnValue($oShop));
        $this->SUT->expects($this->once())->method('_paypPayPalPlusPaymentData_delete_parent')->will($this->returnValue(true));

        $this->assertTrue($this->SUT->delete());
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

    /**
     * test that status is changed for allowed event types.
     */
    public function testSetStatusByEventType_changesStatusForAllowedTypes() {
        $aAllowedEventTypes = array(
            'PAYMENT.SALE.COMPLETED',
            'PAYMENT.SALE.PENDING',
            'PAYMENT.SALE.REFUNDED',
            'PAYMENT.SALE.REVERSED',
        );


        foreach($aAllowedEventTypes as $sEventType) {
            $this->SUT->setStatusByEventType($sEventType);
            list($domain, $method, $sExpectedStatus) = array_map('strtolower', explode('.', $sEventType));
            $sActualStatus = $this->SUT->getStatus();
            $this->assertEquals($sExpectedStatus, $sActualStatus);
        }
    }

    /**
     * test that status is only changed for allowed event types.
     */
    public function testSetStatusByEventType_doesNotChangeStatusForNotAllowedTypes() {
        $aNotAllowedEventTypes = array(
            'PAYMENT.AUTHORIZATION.CREATED',
            'PAYMENT.AUTHORIZATION.VOIDED',
            'PAYMENT.CAPTURE.COMPLETED',
            'PAYMENT.CAPTURE.PENDING',
            'PAYMENT.CAPTURE.REFUNDED',
            'PAYMENT.CAPTURE.REVERSED',
            'RISK.DISPUTE.CREATED',
        );


        foreach($aNotAllowedEventTypes as $sEventType) {
            $this->SUT->setStatusByEventType($sEventType);
            $sActualStatus = $this->SUT->getStatus();
            $this->assertNull($sActualStatus);
        }
    }

    /**
     * Test that the setOrdePaid functionality
     */
    public function testSetOrderPaid()
    {
        $sExpectedDatetime = '2015-10-10 12:00:00';
        $sOrderId = md5(time());

        $oOrder = new oxOrder();
        if (! $oOrder->load($sOrderId)) {
            $oOrder->setId($sOrderId);
            $oOrder->save();
        }
        $this->SUT->setOrderId($sOrderId);
        $blResult = $this->SUT->setOrderPaid('2015-10-10T10:00Z');

        /** Re-Load the order */
        $oOrder->load($sOrderId);
        $sActualDatetime = $oOrder->oxorder__oxpaid->value;
        // clean up
        $oOrder->delete();

        $this->assertTrue($blResult);
        $this->assertEquals($sExpectedDatetime, $sActualDatetime);
    }
}
