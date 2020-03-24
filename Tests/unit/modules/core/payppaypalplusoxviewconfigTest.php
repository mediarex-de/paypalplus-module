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
 * Class paypPayPalPlusOxViewConfigTest
 * Tests for core class paypPayPalPlusOxViewConfig.
 *
 * @see paypPayPalPlusOxViewConfig
 */
class paypPayPalPlusOxViewConfigTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusOxViewConfig
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock('paypPayPalPlusOxViewConfig', array('__call'));
    }


    /**
     * test `getPayPalPlusShop`
     */
    public function testGetPayPalPlusShop()
    {
        $this->assertInstanceOf('paypPayPalPlusShop', $this->SUT->getPayPalPlusShop());
    }


    public function testGetPayPalPlusMethodId()
    {
        $this->assertSame('payppaypalplus', $this->SUT->getPayPalPlusMethodId());
    }


    public function testGetPayPalPlusMethodLabel()
    {
        $this->assertSame(
            oxRegistry::getLang()->translateString('PAYP_PAYPALPLUS_METHOD_LABEL'),
            $this->SUT->getPayPalPlusMethodLabel()
        );
    }


    /**
     * @dataProvider payPalPlusActiveDataProvider
     */
    public function testIsPayPalPlusActive($sTestingCondition, $blModuleActive, $blPaymentActive, $blExpectedResult)
    {
        $oValidator = $this->getMock('paypPayPalPlusValidator', array('isModuleActive', 'isPaymentActive'));
        $oValidator->expects($this->once())->method('isModuleActive')->will($this->returnValue($blModuleActive));
        $oValidator->expects($this->any())->method('isPaymentActive')->will($this->returnValue($blPaymentActive));

        $oShop = $this->getMock('paypPayPalPlusShop', array('getFromRegistry'));
        $oShop->expects($this->once())->method('getFromRegistry')->with('paypPayPalPlusValidator')->will(
            $this->returnValue($oValidator)
        );

        $this->SUT = $this->getMock('paypPayPalPlusOxViewConfig', array('getPayPalPlusShop'));
        $this->SUT->expects($this->once())->method('getPayPalPlusShop')->will($this->returnValue($oShop));

        $this->assertSame($blExpectedResult, $this->SUT->isPayPalPlusActive(), $sTestingCondition);
    }

    /**
     * Data provider to test `isPayPalPlusActive`
     *
     * @return array
     */
    public function payPalPlusActiveDataProvider()
    {
        return array(
            array('Module active and payment active', true, true, true),
            array('Module active and payment is not active', true, false, false),
            array('Module is not active and payment active', false, true, false),
            array('Module is not active and payment is not active', false, false, false),
        );
    }


    /**
     * test `isPayPalPlusAvailable` when validator returns that payment created
     */
    public function testIsPayPalPlusAvailable_validatorSaysPaymentCreated_returnTrue()
    {
        $oValidator = $this->getMock('paypPayPalPlusValidator', array('isPaymentCreated'));
        $oValidator->expects($this->once())->method('isPaymentCreated')->will($this->returnValue(true));

        $oShop = $this->getMock('paypPayPalPlusShop', array('getFromRegistry'));
        $oShop->expects($this->once())->method('getFromRegistry')->with('paypPayPalPlusValidator')->will($this->returnValue($oValidator));

        $this->SUT = $this->getMock('paypPayPalPlusOxViewConfig', array('getPayPalPlusShop'));
        $this->SUT->expects($this->once())->method('getPayPalPlusShop')->will($this->returnValue($oShop));

        $this->assertTrue($this->SUT->isPayPalPlusAvailable());
    }

    /**
     * test `isPayPalPlusAvailable` when validator returns that payment is not created
     */
    public function testIsPayPalPlusAvailable_validatorSaysPaymentIsNotCreated_returnFalsee()
    {
        $oValidator = $this->getMock('paypPayPalPlusValidator', array('isPaymentCreated'));
        $oValidator->expects($this->once())->method('isPaymentCreated')->will($this->returnValue(false));

        $oShop = $this->getMock('paypPayPalPlusShop', array('getFromRegistry'));
        $oShop->expects($this->once())->method('getFromRegistry')->with('paypPayPalPlusValidator')->will($this->returnValue($oValidator));

        $this->SUT = $this->getMock('paypPayPalPlusOxViewConfig', array('getPayPalPlusShop'));
        $this->SUT->expects($this->once())->method('getPayPalPlusShop')->will($this->returnValue($oShop));

        $this->assertFalse($this->SUT->isPayPalPlusAvailable());
    }


    /**
     * test `getPayPalPlusSrcUrl`
     */
    public function testGetPayPalPlusSrcUrl()
    {
        $oModule = $this->getMock('paypPayPalPlusModule', array('getModulePath'));
        $oModule->expects($this->once())->method('getModulePath')->will($this->returnValue('payp/paypalplus'));

        $oShop = $this->getMock('paypPayPalPlusShop', array('getFromRegistry'));
        $oShop->expects($this->once())->method('getFromRegistry')->with('paypPayPalPlusModule')->will(
            $this->returnValue($oModule)
        );

        $this->SUT = $this->getMock('paypPayPalPlusOxViewConfig', array('getPayPalPlusShop', 'getModuleUrl'));
        $this->SUT->expects($this->once())->method('getPayPalPlusShop')->will($this->returnValue($oShop));
        $this->SUT->expects($this->once())->method('getModuleUrl')
            ->with('payp/paypalplus', 'out/src/js/some_file.js')
            ->will($this->returnValue('http://shop/modules/payp/paypalplus/out/src/js/some_file.js'));

        $this->assertSame(
            'http://shop/modules/payp/paypalplus/out/src/js/some_file.js',
            $this->SUT->getPayPalPlusSrcUrl('js/some_file.js')
        );
    }
}
