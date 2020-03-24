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
 * Class paypPayPalPlusOrderTest
 * Tests for core class paypPayPalPlusOrder.
 *
 * @see paypPayPalPlusOrder
 */
class paypPayPalPlusOrderTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusOrder
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     *
     * Importing data for testing
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock('paypPayPalPlusOrder', array('__call', '_paypPayPalPlusOrder_init_parent'));
    }


    /**
     * test `init`, payment id is not set in the request, no payment is set to the basket.
     */
    public function testInit_paymentIdEmpty_paymentNotSetToTheBasket()
    {
        $oShop = $this->getMock('paypPayPalPlusShop', array('__call'));

        oxTestModules::addModuleObject('paypPayPalPlusShop', $oShop);

        modConfig::setRequestParameter('payppaypalplussuccess', false);

        $this->SUT->init();

        $this->assertNull($oShop->getBasket()->getPaymentId());
    }

    /**
     * test `init`, payment id is set in the request, but it could not be loaded.
     * No payment is set to the basket.
     */
    public function testInit_paymentIdNotEmpty_paymentNotLoaded_paymentNotSetToTheBasket()
    {
        $sTestPaymentId = 'testSomeRandomPaymentId';

        $oShop = $this->getMock('paypPayPalPlusShop', array('__call'));

        oxTestModules::addModuleObject('paypPayPalPlusShop', $oShop);

        modConfig::setRequestParameter('force_paymentid', $sTestPaymentId);
        modConfig::setRequestParameter('payppaypalplussuccess', false);

        $this->SUT->init();

        $this->assertNull($oShop->getBasket()->getPaymentId());
    }

    /**
     * test `init`, payment id is set in the request and it was loaded.
     * This payment is set to the basket.
     */
    public function testInit_paymentIdNotEmpty_paymentLoaded_paymentSetToBasket()
    {
        $sPaymentId = 'payppaypalplus';

        $oShop = $this->getMock('paypPayPalPlusShop', array('__call'));

        oxTestModules::addModuleObject('paypPayPalPlusShop', $oShop);

        modConfig::setRequestParameter('force_paymentid', $sPaymentId);
        modConfig::setRequestParameter('payppaypalplussuccess', false);

        $this->SUT->init();

        $this->assertSame($sPaymentId, $oShop->getBasket()->getPaymentId());
    }

    /**
     * test `init`, payment id is set in the request and it was loaded.
     * User returned from paypal, but payment id in request is not equal to the payment id in session
     * Approved payment is not set to the session and error handling function is called
     */
    public function testInit_paymentLoaded_returnedFromPayPal_paymentIdsNotEqual_approvedPaymentIsNotSet()
    {
        $sPaymentId = 'payppaypalplus';
        $sPayerId = 'PayerId';

        $oPayPalPlusErrorHandler = $this->getMock('paypPayPalPlusErrorHandler', array('setPaymentErrorAndRedirect'));
        $oPayPalPlusErrorHandler->expects($this->once())->method('setPaymentErrorAndRedirect');

        $oShop = $this->getMock('paypPayPalPlusShop', array('getErrorHandler'));
        $oShop->expects($this->once())->method('getErrorHandler')->will($this->returnValue($oPayPalPlusErrorHandler));

        oxTestModules::addModuleObject('paypPayPalPlusShop', $oShop);

        modConfig::setRequestParameter('force_paymentid', $sPaymentId);
        modConfig::setRequestParameter('payppaypalplussuccess', true);
        modConfig::setRequestParameter('PayerID', $sPayerId);
        modConfig::setRequestParameter('paymentId', $sPaymentId);
        modSession::getInstance()->setVar('paypPayPalPlusPaymentId', 'someTestPaymentId');

        $oPayPalPlusPayment = new PayPal\Api\Payment();
        $oPayPalPlusPayment->setId($sPaymentId);

        $paypPayPalPlusSession = $this->getMock('paypPayPalPlusSession', array('getPayment'));
        $paypPayPalPlusSession->expects($this->any())->method('getPayment')->will($this->returnValue($oPayPalPlusPayment));

        oxTestModules::addModuleObject('paypPayPalPlusSession', $paypPayPalPlusSession);

        $this->SUT->init();

        $this->assertNull(modSession::getInstance()->getVar('paypPayPalPlusApprovedPaymentId'));
        $this->assertNull(modSession::getInstance()->getVar('paypPayPalPlusPayerId'));
    }

    /**
     * test `init`, payment id is set in the request and it was loaded.
     * User returned from paypal, bpayment id in request is equal to the payment id in session.
     * Approved payment and payer id are set to the session.
     */
    public function testInit_paymentLoaded_returnedFromPayPal_paymentIdsEqual_approvedPaymentIsSet()
    {
        $sPaymentId = 'payppaypalplus';
        $sPayerId = 'PayerId';

        $oShop = $this->getMock('paypPayPalPlusShop', array('__call'));

        oxTestModules::addModuleObject('paypPayPalPlusShop', $oShop);

        modConfig::setRequestParameter('force_paymentid', $sPaymentId);
        modConfig::setRequestParameter('payppaypalplussuccess', true);
        modConfig::setRequestParameter('PayerID', $sPayerId);
        modConfig::setRequestParameter('paymentId', $sPaymentId);
        modSession::getInstance()->setVar('paypPayPalPlusPaymentId', $sPaymentId);

        $oPayPalPlusPayment = new PayPal\Api\Payment();
        $oPayPalPlusPayment->setId($sPaymentId);

        $paypPayPalPlusSession = $this->getMock('paypPayPalPlusSession', array('getPayment'));
        $paypPayPalPlusSession->expects($this->any())->method('getPayment')->will($this->returnValue($oPayPalPlusPayment));

        oxTestModules::addModuleObject('paypPayPalPlusSession', $paypPayPalPlusSession);

        $this->SUT->init();

        $this->assertSame($sPaymentId, modSession::getInstance()->getVar('paypPayPalPlusApprovedPaymentId'));
        $this->assertSame($sPayerId, modSession::getInstance()->getVar('paypPayPalPlusPayerId'));
    }
}
