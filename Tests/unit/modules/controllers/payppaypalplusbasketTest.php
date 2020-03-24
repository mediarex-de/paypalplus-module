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
 * Class paypPayPalPlusBasketTest
 * Tests for controller class paypPayPalPlusBasket.
 *
 * @see paypPayPalPlusBasket
 */
class paypPayPalPlusBasketTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusBasket
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = new paypPayPalPlusBasket();//$this->getMock('paypPayPalPlusBasket', array('__call'));
    }


    /**
     * test `render`, payment is not possible, returning parent function response. Payment id is not in the session
     */
    public function testRender_paymentIsNotPossible_noPaymentInSession()
    {
        $oValidator = $this->getMock('paypPayPalPlusValidator', array('isPaymentPossible'));
        $oValidator->expects($this->once())->method('isPaymentPossible')->will($this->returnValue(false));

        $oShop = $this->getMock('paypPayPalPlusShop', array('getValidator'));
        $oShop->expects($this->once())->method('getValidator')->will($this->returnValue($oValidator));

        oxTestModules::addModuleObject('paypPayPalPlusShop', $oShop);

        $this->SUT = $this->getMock('paypPayPalPlusBasket', array('_paypPayPalPlusOxBasket_render_parent'));
        $this->SUT->expects($this->once())->method('_paypPayPalPlusOxBasket_render_parent')->will(
            $this->returnValue(true)
        );

        $this->assertTrue($this->SUT->render());
        $this->assertNull(modSession::getInstance()->getVar('paypPayPalPlusPaymentId'));
    }

    /**
     * test `render`, payment is possible, returning parent function response. Payment id is in the session
     */
    public function testRender_paymentIsPossible_paymentIsInSession()
    {
        $oValidator = $this->getMock('paypPayPalPlusValidator', array('isPaymentPossible'));
        $oValidator->expects($this->once())->method('isPaymentPossible')->will($this->returnValue(true));

        $oShop = $this->getMock('paypPayPalPlusShop', array('getValidator'));
        $oShop->expects($this->any())->method('getValidator')->will($this->returnValue($oValidator));

        oxTestModules::addModuleObject('paypPayPalPlusShop', $oShop);

        $oPayment = new PayPal\Api\Payment();
        $oPayment->setId('testPaymentId');

        $oPayPalPaymentHandler = $this->getMock('paypPayPalPlusPaymentHandler', array('init', 'create', 'getPayment'));
        $oPayPalPaymentHandler->expects($this->once())->method('getPayment')->will($this->returnValue($oPayment));

        oxTestModules::addModuleObject('paypPayPalPlusPaymentHandler', $oPayPalPaymentHandler);

        $this->SUT = $this->getMock('paypPayPalPlusBasket', array('_paypPayPalPlusOxBasket_render_parent'));
        $this->SUT->expects($this->once())->method('_paypPayPalPlusOxBasket_render_parent')->will(
            $this->returnValue(true)
        );

        $this->assertTrue($this->SUT->render());
        $this->assertSame('testPaymentId', modSession::getInstance()->getVar('paypPayPalPlusPaymentId'));
    }
}
