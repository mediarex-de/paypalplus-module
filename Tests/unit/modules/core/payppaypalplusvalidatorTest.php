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
 * Class paypPayPalPlusValidatorTest
 * Tests for paypPayPalPlusValidator helper class.
 *
 * @see paypPayPalPlusValidator
 */
class paypPayPalPlusValidatorTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusValidator
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock('paypPayPalPlusValidator', array('__call'));
    }


    public function testIsPaymentPossible_moduleNotActive_returnFalse()
    {
        /** @var paypPayPalPlusValidator|PHPUnit_Framework_MockObject_MockObject $SUT */
        $SUT = $this->getMock(
            'paypPayPalPlusValidator',
            array('__call', 'isModuleActive', 'isPaymentActive', 'isBasketPayable', 'isApiAvailable')
        );
        $SUT->expects($this->once())->method('isModuleActive')->will($this->returnValue(false));
        $SUT->expects($this->never())->method('isPaymentActive');
        $SUT->expects($this->never())->method('isBasketPayable');
        $SUT->expects($this->never())->method('isApiAvailable');

        $this->assertFalse($SUT->isPaymentPossible(false));
    }

    public function testIsPaymentPossible_paymentNotActive_returnFalse()
    {
        /** @var paypPayPalPlusValidator|PHPUnit_Framework_MockObject_MockObject $SUT */
        $SUT = $this->getMock(
            'paypPayPalPlusValidator',
            array('__call', 'isModuleActive', 'isPaymentActive', 'isBasketPayable', 'isApiAvailable')
        );
        $SUT->expects($this->once())->method('isModuleActive')->will($this->returnValue(true));
        $SUT->expects($this->once())->method('isPaymentActive')->will($this->returnValue(false));
        $SUT->expects($this->never())->method('isBasketPayable');
        $SUT->expects($this->never())->method('isApiAvailable');

        $this->assertFalse($SUT->isPaymentPossible(false));
    }

    public function testIsPaymentPossible_basketNotPayable_returnFalse()
    {
        /** @var paypPayPalPlusValidator|PHPUnit_Framework_MockObject_MockObject $SUT */
        $SUT = $this->getMock(
            'paypPayPalPlusValidator',
            array('__call', 'isModuleActive', 'isPaymentActive', 'isBasketPayable', 'isApiAvailable')
        );
        $SUT->expects($this->once())->method('isModuleActive')->will($this->returnValue(true));
        $SUT->expects($this->once())->method('isPaymentActive')->will($this->returnValue(true));
        $SUT->expects($this->once())->method('isBasketPayable')->will($this->returnValue(false));
        $SUT->expects($this->never())->method('isApiAvailable');

        $this->assertFalse($SUT->isPaymentPossible(false));
    }

    public function testIsPaymentPossible_apiConnectionIsNotAvailable_returnFalse()
    {
        /** @var paypPayPalPlusValidator|PHPUnit_Framework_MockObject_MockObject $SUT */
        $SUT = $this->getMock(
            'paypPayPalPlusValidator',
            array('__call', 'isModuleActive', 'isPaymentActive', 'isBasketPayable', 'isApiAvailable')
        );
        $SUT->expects($this->once())->method('isModuleActive')->will($this->returnValue(true));
        $SUT->expects($this->once())->method('isPaymentActive')->will($this->returnValue(true));
        $SUT->expects($this->once())->method('isBasketPayable')->will($this->returnValue(true));
        $SUT->expects($this->once())->method('isApiAvailable')->will($this->returnValue(false));

        $this->assertFalse($SUT->isPaymentPossible(false));
    }

    public function testIsPaymentPossible_moduleAndPaymentAreActiveBasketIsPayableAndApiConnectionAvailable_returnTrue()
    {
        /** @var paypPayPalPlusValidator|PHPUnit_Framework_MockObject_MockObject $SUT */
        $SUT = $this->getMock(
            'paypPayPalPlusValidator',
            array('__call', 'isModuleActive', 'isPaymentActive', 'isBasketPayable', 'isApiAvailable')
        );
        $SUT->expects($this->once())->method('isModuleActive')->will($this->returnValue(true));
        $SUT->expects($this->once())->method('isPaymentActive')->will($this->returnValue(true));
        $SUT->expects($this->once())->method('isBasketPayable')->will($this->returnValue(true));
        $SUT->expects($this->once())->method('isApiAvailable')->will($this->returnValue(true));

        $this->assertTrue($SUT->isPaymentPossible(false));
    }

    public function testIsPaymentPossible_moduleAndPaymentAreActiveBasketIsPayableAndApiConnectionNotAvailableNoArgument_returnTrue()
    {
        /** @var paypPayPalPlusValidator|PHPUnit_Framework_MockObject_MockObject $SUT */
        $SUT = $this->getMock(
            'paypPayPalPlusValidator',
            array('__call', 'isModuleActive', 'isPaymentActive', 'isBasketPayable', 'isApiAvailable')
        );
        $SUT->expects($this->once())->method('isModuleActive')->will($this->returnValue(true));
        $SUT->expects($this->once())->method('isPaymentActive')->will($this->returnValue(true));
        $SUT->expects($this->once())->method('isBasketPayable')->will($this->returnValue(true));
        $SUT->expects($this->any())->method('isApiAvailable')->will($this->returnValue(false));

        $this->assertTrue($SUT->isPaymentPossible());
    }


    public function testIsModuleActive_moduleIsNotActive_returnFalse()
    {
        $oModule = $this->getMock('paypPayPalPlusModule', array('__construct', 'isActive', 'isRegistered'));
        $oModule->expects($this->any())->method('isActive')->will($this->returnValue(false));
        $oModule->expects($this->any())->method('isRegistered')->will($this->returnValue(true));

        \OxidEsales\Eshop\Core\Registry::set('paypPayPalPlusModule', $oModule);

        $this->assertFalse($this->SUT->isModuleActive());
    }

    public function testIsModuleActive_moduleIsNotRegistered_returnFalse()
    {
        $oModule = $this->getMock('paypPayPalPlusModule', array('__construct', 'isActive', 'isRegistered'));
        $oModule->expects($this->any())->method('isActive')->will($this->returnValue(true));
        $oModule->expects($this->any())->method('isRegistered')->will($this->returnValue(false));

        \OxidEsales\Eshop\Core\Registry::set('paypPayPalPlusModule', $oModule);

        $this->assertFalse($this->SUT->isModuleActive());
    }

    public function testIsModuleActive_moduleIsActiveAndRegistered_returnTrue()
    {
        $oModule = $this->getMock('paypPayPalPlusModule', array('__construct', 'isActive', 'isRegistered'));
        $oModule->expects($this->any())->method('isActive')->will($this->returnValue(true));
        $oModule->expects($this->any())->method('isRegistered')->will($this->returnValue(true));

        \OxidEsales\Eshop\Core\Registry::set('paypPayPalPlusModule', $oModule);

        $this->assertTrue($this->SUT->isModuleActive());
    }


    public function testIsPaymentActive_paymentMethodCouldNotBeLoaded_returnFalse()
    {
        /** @var PHPUnit_Framework_MockObject_MockObject|oxPayment $oPayment */
        $oPayment = $this->getMock('oxPayment', array('__construct', 'load', 'getId'));
        $oPayment->expects($this->any())->method('load')->with('payppaypalplus')->will($this->returnValue(false));
        $oPayment->expects($this->any())->method('getId')->will($this->returnValue('payppaypalplus'));
        $oPayment->oxpayments__oxactive = new oxField(1);

        oxTestModules::addModuleObject('oxPayment', $oPayment);

        $this->assertFalse($this->SUT->isPaymentActive());
    }

    public function testIsPaymentActive_paymentIsCorruptAndHasNoId_returnFalse()
    {
        /** @var PHPUnit_Framework_MockObject_MockObject|oxPayment $oPayment */
        $oPayment = $this->getMock('oxPayment', array('__construct', 'load', 'getId'));
        $oPayment->expects($this->any())->method('load')->with('payppaypalplus')->will($this->returnValue(true));
        $oPayment->expects($this->any())->method('getId')->will($this->returnValue(''));
        $oPayment->oxpayments__oxactive = new oxField(1);

        oxTestModules::addModuleObject('oxPayment', $oPayment);

        $this->assertFalse($this->SUT->isPaymentActive());
    }

    public function testIsPaymentActive_paymentIsDisabled_returnFalse()
    {
        /** @var PHPUnit_Framework_MockObject_MockObject|oxPayment $oPayment */
        $oPayment = $this->getMock('oxPayment', array('__construct', 'load', 'getId'));
        $oPayment->expects($this->any())->method('load')->with('payppaypalplus')->will($this->returnValue(true));
        $oPayment->expects($this->any())->method('getId')->will($this->returnValue('payppaypalplus'));
        $oPayment->oxpayments__oxactive = new oxField(0);

        oxTestModules::addModuleObject('oxPayment', $oPayment);

        $this->assertFalse($this->SUT->isPaymentActive());
    }

    public function testIsPaymentActive_paymentIsLoadedAndActive_returnTrue()
    {
        /** @var PHPUnit_Framework_MockObject_MockObject|oxPayment $oPayment */
        $oPayment = $this->getMock('oxPayment', array('__construct', 'load', 'getId'));
        $oPayment->expects($this->any())->method('load')->with('payppaypalplus')->will($this->returnValue(true));
        $oPayment->expects($this->any())->method('getId')->will($this->returnValue('payppaypalplus'));
        $oPayment->oxpayments__oxactive = new oxField(1);

        oxTestModules::addModuleObject('oxPayment', $oPayment);

        $this->assertTrue($this->SUT->isPaymentActive());
    }


    public function testIsBasketPayable_basketHasNoItems_returnFalse()
    {
        /** @var PHPUnit_Framework_MockObject_MockObject|oxBasket $oBasket */
        $oBasket = $this->getMock('oxBasket', array('__call', 'getProductsCount', 'getPrice'));
        $oBasket->expects($this->any())->method('getProductsCount')->will($this->returnValue(0));
        $oBasket->expects($this->any())->method('getPrice')->will($this->returnValue(new oxPrice(100.0)));

        $this->SUT->getShop()->setBasket($oBasket);

        $this->assertFalse($this->SUT->isBasketPayable());
    }

    public function testIsBasketPayable_basketHasNoPriceSet_returnFalse()
    {
        /** @var PHPUnit_Framework_MockObject_MockObject|oxBasket $oBasket */
        $oBasket = $this->getMock('oxBasket', array('__call', 'getProductsCount', 'getPrice'));
        $oBasket->expects($this->any())->method('getProductsCount')->will($this->returnValue(10));
        $oBasket->expects($this->any())->method('getPrice')->will($this->returnValue(null));

        $this->SUT->getShop()->setBasket($oBasket);

        $this->assertFalse($this->SUT->isBasketPayable());
    }

    public function testIsBasketPayable_basketHasOnlyFreeItems_returnFalse()
    {
        /** @var PHPUnit_Framework_MockObject_MockObject|oxBasket $oBasket */
        $oBasket = $this->getMock('oxBasket', array('__call', 'getProductsCount', 'getPrice'));
        $oBasket->expects($this->any())->method('getProductsCount')->will($this->returnValue(2));
        $oBasket->expects($this->any())->method('getPrice')->will($this->returnValue(new oxPrice(0.0)));

        $this->SUT->getShop()->setBasket($oBasket);

        $this->assertFalse($this->SUT->isBasketPayable());
    }

    public function testIsBasketPayable_basketHasItemsAndPrice_returnTrue()
    {
        /** @var PHPUnit_Framework_MockObject_MockObject|oxBasket $oBasket */
        $oBasket = $this->getMock('oxBasket', array('__call', 'getProductsCount', 'getPrice'));
        $oBasket->expects($this->any())->method('getProductsCount')->will($this->returnValue(1));
        $oBasket->expects($this->any())->method('getPrice')->will($this->returnValue(new oxPrice(100.0)));

        $this->SUT->getShop()->setBasket($oBasket);

        $this->assertTrue($this->SUT->isBasketPayable());
    }


    public function testIsApiAvailable_noApiContextInSession_returnFalse()
    {
        $oSession = $this->getMock('paypPayPalPlusSession', array('__call', 'init', 'getApiContext'));
        $oSession->expects($this->once())->method('init');
        $oSession->expects($this->once())->method('getApiContext')->will($this->returnValue(null));

        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\PayPalPlus\Core\Session::class, $oSession);

        $this->assertFalse($this->SUT->isApiAvailable());
    }

    public function testIsApiAvailable_noAuthenticationTokenObjectAvailable_returnFalse()
    {
        $oApiContext = $this->getMock('PayPal\Rest\ApiContext', array('getCredential'));
        $oApiContext->expects($this->once())->method('getCredential')->will($this->returnValue(null));

        $oSession = $this->getMock('paypPayPalPlusSession', array('__call', 'init', 'getApiContext'));
        $oSession->expects($this->once())->method('init');
        $oSession->expects($this->once())->method('getApiContext')->will($this->returnValue($oApiContext));

        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\PayPalPlus\Core\Session::class, $oSession);

        $this->assertFalse($this->SUT->isApiAvailable());
    }

    public function testIsApiAvailable_noAccessTokenValueAvailable_returnFalse()
    {
        $oToken = $this->getMock('PayPal\Auth\OAuthTokenCredential', array('getAccessToken'), array('client', 'secret'));
        $oToken->expects($this->once())->method('getAccessToken')->with($this->isType('array'))->will(
            $this->returnValue('')
        );

        $oApiContext = $this->getMock('PayPal\Rest\ApiContext', array('getCredential'));
        $oApiContext->expects($this->once())->method('getCredential')->will($this->returnValue($oToken));

        $oSession = $this->getMock('paypPayPalPlusSession', array('__call', 'init', 'getApiContext'));
        $oSession->expects($this->once())->method('init');
        $oSession->expects($this->once())->method('getApiContext')->will($this->returnValue($oApiContext));

        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\PayPalPlus\Core\Session::class, $oSession);

        $this->assertFalse($this->SUT->isApiAvailable());
    }

    public function testIsApiAvailable_accessTokenValueAvailable_returnTrue()
    {
        $oToken = $this->getMock('PayPal\Auth\OAuthTokenCredential', array('getAccessToken'), array('client', 'secret'));
        $oToken->expects($this->once())->method('getAccessToken')->with($this->isType('array'))->will(
            $this->returnValue('_secret_auth_token_')
        );

        $oApiContext = $this->getMock('PayPal\Rest\ApiContext', array('getCredential'));
        $oApiContext->expects($this->once())->method('getCredential')->will($this->returnValue($oToken));

        $oSession = $this->getMock('paypPayPalPlusSession', array('__call', 'init', 'getApiContext'));
        $oSession->expects($this->once())->method('init');
        $oSession->expects($this->once())->method('getApiContext')->will($this->returnValue($oApiContext));

        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\PayPalPlus\Core\Session::class, $oSession);

        $this->assertTrue($this->SUT->isApiAvailable());
    }


    public function testIsPaymentCreated_invalidObjectAsArgument_returnFalse()
    {
        $this->assertFalse($this->SUT->isPaymentCreated(new stdClass()));
    }

    public function testIsPaymentCreated_paymentWithNoIdAsArgument_returnFalse()
    {
        $oPayment = $this->getMock('PayPal\Api\Payment', array('getId'));
        $oPayment->expects($this->once())->method('getId')->will($this->returnValue(''));

        $this->assertFalse($this->SUT->isPaymentCreated($oPayment));
    }

    public function testIsPaymentCreated_paymentWithAValidIdAsArgument_returnTrue()
    {
        $oPayment = $this->getMock('PayPal\Api\Payment', array('getId'));
        $oPayment->expects($this->once())->method('getId')->will($this->returnValue('#ID-123'));

        $this->assertTrue($this->SUT->isPaymentCreated($oPayment));
    }

    public function testIsPaymentCreated_noArgumentAndNoPaymentInSession_returnFalse()
    {
        $oSession = $this->getMock('paypPayPalPlusSession', array('__call', 'init', 'getPayment'));
        $oSession->expects($this->once())->method('init');
        $oSession->expects($this->once())->method('getPayment')->will($this->returnValue(null));

        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\PayPalPlus\Core\Session::class, $oSession);

        $this->assertFalse($this->SUT->isPaymentCreated());
    }

    public function testIsPaymentCreated_noArgumentAndPaymentInSessionHasNoId_returnFalse()
    {
        $oPayment = $this->getMock('PayPal\Api\Payment', array('getId'));
        $oPayment->expects($this->once())->method('getId')->will($this->returnValue(''));

        $oSession = $this->getMock('paypPayPalPlusSession', array('__call', 'init', 'getPayment'));
        $oSession->expects($this->once())->method('init');
        $oSession->expects($this->once())->method('getPayment')->will($this->returnValue($oPayment));

        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\PayPalPlus\Core\Session::class, $oSession);

        $this->assertFalse($this->SUT->isPaymentCreated());
    }

    public function testIsPaymentCreated_noArgumentAndPaymentInSessionHasAValidId_returnTrue()
    {
        $oPayment = $this->getMock('PayPal\Api\Payment', array('getId'));
        $oPayment->expects($this->once())->method('getId')->will($this->returnValue('Some_value'));

        $oSession = $this->getMock('paypPayPalPlusSession', array('__call', 'init', 'getPayment'));
        $oSession->expects($this->once())->method('init');
        $oSession->expects($this->once())->method('getPayment')->will($this->returnValue($oPayment));

        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\PayPalPlus\Core\Session::class, $oSession);

        $this->assertTrue($this->SUT->isPaymentCreated());
    }
}
