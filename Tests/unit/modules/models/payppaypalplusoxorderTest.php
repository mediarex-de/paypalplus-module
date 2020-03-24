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
 * Class paypPayPalPlusOxOrderTest
 * Tests for paypPayPalPlusOxOrder model.
 *
 * @see paypPayPalPlusOxOrder
 */
class paypPayPalPlusOxOrderTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusOxOrder
     */
    protected $SUT;


    /**
     * @inheritDoc
     *
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock('paypPayPalPlusOxOrder', array('_paypPayPalPlusOxOrder_delete_parent'));
    }


    /**
     * test `setPaymentDateAndTime` setting the value on setter and checking if it is correct on object
     */
    public function testSetPaymentDateAndTime()
    {
        $sTestPaymentDateAndTime = '2011-11-11 11:11:11';

        $this->SUT->setPaymentDateAndTime($sTestPaymentDateAndTime);

        $this->assertSame($sTestPaymentDateAndTime, $this->SUT->oxorder__oxpaid->value);
    }


    /**
     * test `delete`, parent function returns false, no other actions are made
     */
    public function testDelete_parentReturnsFalse_returnsFalse()
    {
        $this->SUT->expects($this->once())->method('_paypPayPalPlusOxOrder_delete_parent')->will(
            $this->returnValue(false)
        );

        $this->assertFalse($this->SUT->delete());
    }

    /**
     * test `delete`, parent function returns true, but payment data could not be loaded,
     * returning parent function response.
     * Order id was not passed.
     */
    public function testDelete_parentDeleted_noOrderId_payPalPaymentNotLoaded_returnParentResponse()
    {
        $blParentDeleteResponse = true;
        $sTestOrderId = 'testOrderId';

        $oPayPalPaymentData = $this->getMock('paypPayPalPlusPaymentData', array('loadByOrderId', 'delete'));
        $oPayPalPaymentData->expects($this->once())->method('loadByOrderId')->with($sTestOrderId)->will(
            $this->returnValue(false)
        );
        $oPayPalPaymentData->expects($this->never())->method('delete');
        oxTestModules::addModuleObject('paypPayPalPlusPaymentData', $oPayPalPaymentData);

        $this->SUT = $this->getMock('paypPayPalPlusOxOrder', array('_paypPayPalPlusOxOrder_delete_parent', 'getId'));
        $this->SUT->expects($this->once())->method('_paypPayPalPlusOxOrder_delete_parent')->will(
            $this->returnValue($blParentDeleteResponse)
        );
        $this->SUT->expects($this->once())->method('getId')->will($this->returnValue($sTestOrderId));

        $this->assertSame($blParentDeleteResponse, $this->SUT->delete());
    }

    /**
     * test `delete`, parent function returns true, but payment data was loaded,
     * returning response from the payment data deletion.
     * Order id was passed to the function.
     */
    public function testDelete_parentDeleted_payPalPaymentLoaded_payPalPaymentDeleteCalled_returnPaymentDeletionResponse()
    {
        $blPaymentDeleteResponse = true;
        $sTestOrderId = 'testOrderId';

        $oPayPalPaymentData = $this->getMock('paypPayPalPlusPaymentData', array('loadByOrderId', 'delete'));
        $oPayPalPaymentData->expects($this->once())->method('loadByOrderId')->with($sTestOrderId)->will(
            $this->returnValue(true)
        );
        $oPayPalPaymentData->expects($this->once())->method('delete')->will(
            $this->returnValue($blPaymentDeleteResponse)
        );
        oxTestModules::addModuleObject('paypPayPalPlusPaymentData', $oPayPalPaymentData);

        $this->SUT = $this->getMock('paypPayPalPlusOxOrder', array('_paypPayPalPlusOxOrder_delete_parent'));
        $this->SUT->expects($this->once())->method('_paypPayPalPlusOxOrder_delete_parent')->will(
            $this->returnValue(true)
        );

        $this->assertSame($blPaymentDeleteResponse, $this->SUT->delete($sTestOrderId));
    }

    /**
     * Test that related PayPal Plus status is set to complete, when an order is set to paid
     */
    public function testSave_callsSetPayPalPaymentPlusStatusCompleted_onOrderWasPaidWithPayPalPlus()
    {
        $sExpectedPaymentStatus =  paypPayPalPlusShop::getShop()->getPayPalPlusConfig()->getRefundablePaymentStatus();

        /**
         * System under the test.
         *
         * @var paypPayPalPlusOxOrder|PHPUnit_Framework_MockObject_MockObject $SUT
         */
        $SUT = $this->getMock('paypPayPalPlusOxOrder', array('__construct'));
        $sOrderId = md5(time());

        $SUT->setId($sOrderId);
        $SUT->oxorder__oxpaid = new oxField('2015-10-01 10:10:10');
        $SUT->oxorder__oxpaymenttype = new oxField(paypPayPalPlusShop::getShop()->getPayPalPlusConfig()->getPayPalPlusMethodId());

        $sPaymentDataId = md5(time());
        $oPaymentData = new paypPayPalPlusPaymentData();
        $oPaymentData->setId($sPaymentDataId);
        $oPaymentData->payppaypalpluspayment__oxsaleid = new oxField(md5(time()));
        $oPaymentData->payppaypalpluspayment__oxorderid = new oxField($sOrderId);
        $oPaymentData->payppaypalpluspayment__oxstatus = new oxField('pending');
        $oPaymentData->save();

        $SUT->save();

        $oPaymentData = new paypPayPalPlusPaymentData();
        $oPaymentData->load($sPaymentDataId);
        
        $sActualPaymentStatus = $oPaymentData->getStatus();
        /** Clean up */
        $oPaymentData->delete();

        $this->assertEquals($sExpectedPaymentStatus, $sActualPaymentStatus);
    }

    /**
     * Test that related PayPal Plus status is not set to complete, when an order was not paid
     */
    public function testSave_doesNotCallSetPayPalPaymentPlusStatusCompleted_onOrderWasNotPaid()
    {
        /**
         * System under the test.
         *
         * @var paypPayPalPlusOxOrder|PHPUnit_Framework_MockObject_MockObject $SUT
         */
        $SUT = $this->getMock(
            'paypPayPalPlusOxOrder',
            array(
                '_setPayPalPaymentPlusStatusCompleted',
            )
        );

        $SUT->oxorder__oxpaid = new oxField('0000-00-00 00:00:00');
        $SUT->oxorder__oxpaymenttype = new oxField(paypPayPalPlusShop::getShop()->getPayPalPlusConfig()->getPayPalPlusMethodId());
        $SUT->expects($this->never())->method('_setPayPalPaymentPlusStatusCompleted');

        $SUT->save();
    }

    /**
     * Test that related PayPal Plus status is not set to complete, when the payment is not PayPal Plus
     */
    public function testSave_doesNotCallSetPayPalPaymentPlusStatusCompleted_onPaymentIsNotPayPalPlus()
    {
        /**
         * System under the test.
         *
         * @var paypPayPalPlusOxOrder|PHPUnit_Framework_MockObject_MockObject $SUT
         */
        $SUT = $this->getMock(
            'paypPayPalPlusOxOrder',
            array(
                '_setPayPalPaymentPlusStatusCompleted',
            )
        );
        $SUT->oxorder__oxpaid = new oxField('2015-10-01 10:10:10');
        $SUT->oxorder__oxpaymenttype = new oxField('something funny');
        $SUT->expects($this->never())->method('_setPayPalPaymentPlusStatusCompleted');

        $SUT->save();
    }

    /**
     * Test that an error message is set, when a related order payment cannot be updated
     */
    public function testSave_callsSetErrorMessage_onOrderPaymentNotSaved()
    {
        /**
         * System under the test.
         *
         * @var paypPayPalPlusOxOrder|PHPUnit_Framework_MockObject_MockObject $SUT
         */
        $SUT = $this->getMock(
            'paypPayPalPlusOxOrder',
            array(
                'getOrderPayment',
                '_setErrorMessage'
            )
        );

        $oPaymentMock = $this->getMock('paypPayPalPlusPaymentData', array('save'));
        $oPaymentMock->expects($this->once())->method('save')->will($this->returnValue(false));
        $SUT->oxorder__oxpaid = new oxField('2015-10-01 10:10:10');
        $SUT->oxorder__oxpaymenttype = new oxField(paypPayPalPlusShop::getShop()->getPayPalPlusConfig()->getPayPalPlusMethodId());
        $SUT->expects($this->once())->method('getOrderPayment')->will($this->returnValue($oPaymentMock));
        $SUT->expects($this->once())->method('_setErrorMessage');

        $SUT->save();
    }

    public function testGetPaymentInstructions_returnsNull_forNoPaymentInstructions ()
    {
        $oActualPaymentInstructions = $this->SUT->getPaymentInstructions();

        $this->assertNull($oActualPaymentInstructions);
    }

    public function testGetPaymentInstructions_returnsExpectedPaymentInstructions ()
    {
        $sExpectedId = 'testPuiOXID';
        $this->_removeTestData();
        $this->_addTestData();

        $this->SUT->setId('testPaymentOrderId');
        $oActualPaymentInstructions = $this->SUT->getPaymentInstructions();

        $this->assertEquals($sExpectedId, $oActualPaymentInstructions->getId());

        $this->_removeTestData();
    }


    protected function _removeTestData()
    {
        importTestdataFile("removePaymentData.sql");
        importTestdataFile("removePaymentPuiData.sql");
    }

    protected function _addTestData()
    {
        importTestdataFile("addPaymentData.sql");
        importTestdataFile("addPaymentPuiData.sql");
    }
}
