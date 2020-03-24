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
 * Class paypPayPalPlusThankyouTest
 * Tests for controller class paypPayPalPlusThankyou.
 *
 * @see paypPayPalPlusThankyou
 */
class paypPayPalPlusThankyouTest extends OxidTestCase
{

    /**
     * System under the test.
     *
     * @var paypPayPalPlusThankyou|PHPUnit_Framework_MockObject_MockObject
     */
    protected $SUT;

    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock(
            'paypPayPalPlusThankyou',
            array(
                '__call',
                '_paypPayPalPlusPayment_render_parent',
                '_getOrderId',
                '_getDateToday'
            )
        );
    }

    public function testRender_callParentRender()
    {
        $this->SUT->expects($this->once())->method('_paypPayPalPlusPayment_render_parent');
        $this->SUT->render();
    }

    public function testGetPaymentInstructions_returnsNull_onOrderNotAPui()
    {
        importTestdataFile("removePaymentData.sql");

        $this->SUT->render();
        $oPaymentInstructions = $this->SUT->getPaymentInstructions();
        $this->assertNull($oPaymentInstructions);
    }

    public function testGetPaymentInstructions_returnsPaymentInstructions_onOrderIsPui()
    {
        $sExpectedPaymentId = 'testPaymentId';

        $sOrderId = 'testPaymentOrderId';

        $this->_removeTestData();
        $this->_addTestData();

        $this->SUT->expects($this->once())->method('_getOrderId')->will($this->returnValue($sOrderId));
        $this->SUT->render();
        $oPaymentInstructions = $this->SUT->getPaymentInstructions();
        $sActualPaymentId = $oPaymentInstructions->getPaymentId();
        $this->assertEquals($sExpectedPaymentId, $sActualPaymentId);

        $this->_removeTestData();
    }

    public function testGetTotalPrice_returnsTotalPrice()
    {
        /** Set language to DE */
        $this->setLanguage(0); // TODO Get language id by language code
        $sExpectedTotalPrice = "100,00";

        $sOrderId = 'testPaymentOrderId';

        $this->_removeTestData();
        $this->_addTestData();

        $this->SUT->expects($this->once())->method('_getOrderId')->will($this->returnValue($sOrderId));
        $this->SUT->init();
        $this->SUT->render();
        $oActualTotalPrice = $this->SUT->getTotalPrice();

        $this->assertEquals($sExpectedTotalPrice, $oActualTotalPrice);

        $this->_removeTestData();
    }


    public function testGetDueDate_returnsExpectedDueDate_forLangDe()
    {
        /** Set language to DE */
        $this->setLanguage(0); // TODO Get language id by language code
        $sExpectedDueDate = '8.11.2015';

        $sOrderId = 'testPaymentOrderId';

        $this->_removeTestData();
        $this->_addTestData();

        $this->SUT->expects($this->once())->method('_getOrderId')->will($this->returnValue($sOrderId));
        $this->SUT->render();
        $oActualDueDate = $this->SUT->getDueDate();

        $this->assertEquals($sExpectedDueDate, $oActualDueDate);

        $this->_removeTestData();
    }

    public function testGetDueDate_returnsExpectedDueDate_forLangEn()
    {
        /** Set language to EN */
        $this->setLanguage(1); // TODO Get language id by language code
        $sExpectedDueDate = '2015-11-08';

        $sOrderId = 'testPaymentOrderId';

        $this->_removeTestData();
        $this->_addTestData();

        $this->SUT->expects($this->once())->method('_getOrderId')->will($this->returnValue($sOrderId));
        $this->SUT->render();
        $oActualDueDate = $this->SUT->getDueDate();

        $this->assertEquals($sExpectedDueDate, $oActualDueDate);

        $this->_removeTestData();

        $this->setLanguage(0); // TODO Get language id by language code
    }

    protected function _removeTestData()
    {
        importTestdataFile("removePaymentOrderData.sql");
        importTestdataFile("removePaymentData.sql");
        importTestdataFile("removePaymentPuiData.sql");
    }

    protected function _addTestData()
    {
        importTestdataFile("addPaymentOrderData.sql");
        importTestdataFile("addPaymentData.sql");
        importTestdataFile("addPaymentPuiData.sql");
    }
}