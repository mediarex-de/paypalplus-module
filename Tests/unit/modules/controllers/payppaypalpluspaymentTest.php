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
 * Class paypPayPalPlusPaymentTest
 * Tests for core class paypPayPalPlusPayment.
 *
 * @see paypPayPalPlusPayment
 */
class paypPayPalPlusPaymentTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusPayment
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock(
            'paypPayPalPlusPayment',
            array(
                '__call', '_ajaxResponseSuccess',
                '_paypPayPalPlusPayment_render_parent', '_paypPayPalPlusPayment_validatePayment_parent'
            )
        );
    }


    /**
     * test `getShop`. Always returns an instance of paypPayPalPlusShop
     */
    public function testGetShop()
    {
        $this->assertInstanceOf('paypPayPalPlusShop', $this->SUT->getShop());
    }


    public function testGetSuccessControllerName()
    {
        $this->assertSame('order', $this->SUT->getSuccessControllerName());
    }


    public function testGetPaymentIdParameterName()
    {
        $this->assertSame('paymentid', $this->SUT->getPaymentIdParameterName());
    }


    public function testRender_paymentToBeCreatedVariableIsTrue_newPaymentCreated()
    {
        $oPrice = $this->getMock('oxPrice', array('getPrice'));
        $oPrice->expects($this->any())->method('getPrice')->will($this->returnValue(15.5));

        $oBasket = $this->getMock('oxBasket', array('getProductsCount', 'getPrice', 'getBasketHash', 'getPaymentCost'));
        $oBasket->expects($this->once())->method('getProductsCount')->will($this->returnValue(3));
        $oBasket->expects($this->once())->method('getPrice')->will($this->returnValue($oPrice));
        $oBasket->expects($this->any())->method('getBasketHash')->will($this->returnValue('testBasketHash'));
        $oBasket->expects($this->any())->method('getPaymentCost')->will($this->returnValue($oPrice));

        $oShop = $this->getMock('paypPayPalPlusShop', array('getBasket'));
        $oShop->expects($this->any())->method('getBasket')->will($this->returnValue($oBasket));

        oxTestModules::addModuleObject('paypPayPalPlusShop', $oShop);

        $oPayment = new PayPal\Api\Payment();
        $oPayment->setId('testPaymentId');

        $oPayPalPaymentHandler = $this->getMock('paypPayPalPlusPaymentHandler', array('init', 'create', 'getPayment'));
        $oPayPalPaymentHandler->expects($this->once())->method('getPayment')->will($this->returnValue($oPayment));

        oxTestModules::addModuleObject('paypPayPalPlusPaymentHandler', $oPayPalPaymentHandler);

        $oPaymentPrice = $this->getMock('oxPrice', array('getPrice'));
        $oPaymentPrice->expects($this->any())->method('getPrice')->will($this->returnValue(15.6));
        $oPaymentMethod = $this->getMock('oxPayment', array('getPrice'));
        $oPaymentMethod->expects($this->any())->method('getPrice')->will($this->returnValue($oPaymentPrice));

        oxTestModules::addModuleObject('oxPayment', $oPaymentMethod);

        $this->SUT->render();
        $this->assertSame('testPaymentId', modSession::getInstance()->getVar('paypPayPalPlusPaymentId'));
        $this->assertSame('s:14:"testBasketHash";', modSession::getInstance()->getVar('paypPayPalPlusBasketHash'));
        $this->assertSame('payppaypalplus', $oBasket->getPaymentId());
    }

    public function testRender_paymentHasError_newPaymentCreated()
    {
        $oShop = $this->getMock('paypPayPalPlusShop', array('__call'));

        oxTestModules::addModuleObject('paypPayPalPlusShop', $oShop);

        $oPayment = new PayPal\Api\Payment();
        $oPayment->setId('testPaymentId');

        $oPayPalPaymentHandler = $this->getMock('paypPayPalPlusPaymentHandler', array('init', 'create', 'getPayment'));
        $oPayPalPaymentHandler->expects($this->once())->method('getPayment')->will($this->returnValue($oPayment));

        oxTestModules::addModuleObject('paypPayPalPlusPaymentHandler', $oPayPalPaymentHandler);

        modSession::getInstance()->setVar('payerror', true);

        $this->SUT->render();
        $this->assertSame('testPaymentId', modSession::getInstance()->getVar('paypPayPalPlusPaymentId'));
        $this->assertNotNull(modSession::getInstance()->getVar('paypPayPalPlusBasketHash'));
    }

    public function testRender_orderCancelledVariableIsTrue_newPaymentCreated()
    {
        $oShop = $this->getMock('paypPayPalPlusShop', array('__call'));

        oxTestModules::addModuleObject('paypPayPalPlusShop', $oShop);

        $oPayment = new PayPal\Api\Payment();
        $oPayment->setId('testPaymentId');

        $oPayPalPaymentHandler = $this->getMock('paypPayPalPlusPaymentHandler', array('init', 'create', 'getPayment'));
        $oPayPalPaymentHandler->expects($this->once())->method('getPayment')->will($this->returnValue($oPayment));

        oxTestModules::addModuleObject('paypPayPalPlusPaymentHandler', $oPayPalPaymentHandler);

        modConfig::setRequestParameter('payppaypalpluscancel', true);

        $this->SUT->render();
        $this->assertSame('testPaymentId', modSession::getInstance()->getVar('paypPayPalPlusPaymentId'));
        $this->assertNotNull(modSession::getInstance()->getVar('paypPayPalPlusBasketHash'));
    }

    public function testRender_approvedPaymentExists_countryIsUsButNoState_resetsPaymentAndShowsError()
    {
        $oPayment = new PayPal\Api\Payment();
        modSession::getInstance()->setVar('paypPayPalPlusApprovedPayment', serialize($oPayment));

        $oPayPalPlusUserData = $this->getMock(
            'paypPayPalPlusUserData',
            array('getShippingAddressValueCountryCode', 'getShippingAddressValueState')
        );
        $oPayPalPlusUserData->expects($this->once())->method('getShippingAddressValueCountryCode')->will(
            $this->returnValue('US')
        );
        $oPayPalPlusUserData->expects($this->once())->method('getShippingAddressValueState')->will(
            $this->returnValue(false)
        );

        oxTestModules::addModuleObject('paypPayPalPlusUserData', $oPayPalPlusUserData);

        $oPayPalPlusErrorHandler = $this->getMock('paypPayPalPlusErrorHandler', array('setPaymentErrorAndRedirect'));
        $oPayPalPlusErrorHandler->expects($this->any())->method('setDataValidationNotice');

        $oShop = $this->getMock('paypPayPalPlusShop', array('getErrorHandler'));
        $oShop->expects($this->any())->method('getErrorHandler')->will($this->returnValue($oPayPalPlusErrorHandler));

        oxTestModules::addModuleObject('paypPayPalPlusShop', $oShop);

        $this->SUT->render();

        $this->assertNull(modSession::getInstance()->getVar('paypPayPalPlusApprovedPayment'));
    }


    public function testValidatePayment_parentFunctionReturnIsNotSuccessControllerName_returnParentResponse()
    {
        $sParentResponse = 'someControllerName';

        $this->SUT->expects($this->once())->method('_paypPayPalPlusPayment_validatePayment_parent')->will(
            $this->returnValue($sParentResponse)
        );

        $this->assertSame($sParentResponse, $this->SUT->validatePayment());
    }

    public function testValidatePayment_controllerNameCheckPassed_returnParentResponse()
    {
        $sParentResponse = 'order';

        $oPayPalPlusPayment = new PayPal\Api\Payment();
        $oPayPalPlusPayment->setId('testPaymentId');

        $oPayPalPaymentHandler = $this->getMock(
            'paypPayPalPlusPaymentHandler',
            array('update', 'setPayment', 'getPayment')
        );
        $oPayPalPaymentHandler->expects($this->once())->method('getPayment')->will(
            $this->returnValue($oPayPalPlusPayment)
        );

        oxTestModules::addModuleObject('paypPayPalPlusPaymentHandler', $oPayPalPaymentHandler);

        $paypPayPalPlusSession = $this->getMock('paypPayPalPlusSession', array('getPayment'));
        $paypPayPalPlusSession->expects($this->any())->method('getPayment')->will(
            $this->returnValue($oPayPalPlusPayment)
        );

        oxTestModules::addModuleObject('paypPayPalPlusSession', $paypPayPalPlusSession);

        $this->SUT->expects($this->once())->method('_paypPayPalPlusPayment_validatePayment_parent')->will(
            $this->returnValue($sParentResponse)
        );
        modConfig::setRequestParameter('paymentid', 'payppaypalplus');
        modConfig::setRequestParameter('ajax', true);

        $this->SUT->expects($this->once())->method('_ajaxResponseSuccess');
        $this->assertSame($sParentResponse, $this->SUT->validatePayment());
    }

    public function testValidatePayment_controllerNameCheckPassed_notAjaxCall_setsPaymentErrorAndRedirects()
    {
        $sParentResponse = 'order';

        $oPayPalPlusErrorHandler = $this->getMock('paypPayPalPlusErrorHandler', array('setPaymentErrorAndRedirect'));
        $oPayPalPlusErrorHandler->expects($this->once())->method('setPaymentErrorAndRedirect');

        $oShop = $this->getMock('paypPayPalPlusShop', array('getErrorHandler'));
        $oShop->expects($this->once())->method('getErrorHandler')->will($this->returnValue($oPayPalPlusErrorHandler));

        oxTestModules::addModuleObject('paypPayPalPlusShop', $oShop);

        $this->SUT->expects($this->once())->method('_paypPayPalPlusPayment_validatePayment_parent')->will(
            $this->returnValue($sParentResponse)
        );
        modConfig::setRequestParameter('paymentid', 'payppaypalplus');

        $this->assertSame($sParentResponse, $this->SUT->validatePayment());
    }


    public function testRoutePayment_paymentNotValid_redirectWithError()
    {
        modConfig::setRequestParameter('paymentid', 'someRandomPaymentId');

        $oPayPalPlusErrorHandler = $this->getMock('paypPayPalPlusErrorHandler', array('setPaymentErrorAndRedirect'));
        $oPayPalPlusErrorHandler->expects($this->once())->method('setPaymentErrorAndRedirect');

        $oUtils = $this->getMock('oxUtils', array('redirect'));

        $oShop = $this->getMock('paypPayPalPlusShop', array('getErrorHandler', 'getUtils'));
        $oShop->expects($this->once())->method('getUtils')->will($this->returnValue($oUtils));
        $oShop->expects($this->once())->method('getErrorHandler')->will($this->returnValue($oPayPalPlusErrorHandler));

        oxTestModules::addModuleObject('paypPayPalPlusShop', $oShop);

        $this->SUT->routePayment();
    }

    public function testRoutePayment_paymentIsValid_redirectToSuccessController()
    {
        $aConfiguredExternalMethods = array('paymentMethod1', 'paymentMethod2');
        modConfig::setRequestParameter('paymentid', 'paymentMethod1');

        $paypPayPalPlusConfig = $this->getMock('paypPayPalPlusConfig', array('getConfiguredExternalMethods'));
        $paypPayPalPlusConfig->expects($this->any())->method('getConfiguredExternalMethods')->will(
            $this->returnValue($aConfiguredExternalMethods)
        );

        oxTestModules::addModuleObject('paypPayPalPlusConfig', $paypPayPalPlusConfig);

        $oUtils = $this->getMock('oxUtils', array('redirect'));
        $oUtils->expects($this->once())->method('redirect')->will($this->returnValue(true));

        $oShop = $this->getMock('paypPayPalPlusShop', array('getUtils'));
        $oShop->expects($this->once())->method('getUtils')->will($this->returnValue($oUtils));

        oxTestModules::addModuleObject('paypPayPalPlusShop', $oShop);

        $this->SUT->routePayment();
    }
}
