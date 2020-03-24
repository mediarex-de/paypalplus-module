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
 * Class paypPayPalPlusOxPaymentGatewayTest
 * Tests for core class paypPayPalPlusOxPaymentGateway.
 *
 * @see paypPayPalPlusOxPaymentGateway
 */
class paypPayPalPlusOxPaymentGatewayTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusOxPaymentGateway
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock('paypPayPalPlusOxPaymentGateway', array('__call'));
    }


    /**
     * test `getShop`. Always returns an instance of paypPayPalPlusShop
     */
    public function testGetShop()
    {
        $this->assertInstanceOf('paypPayPalPlusShop', $this->SUT->getShop());
    }


    public function testExecutePayment_userPaymentIdDoesNotEqualPayPalPlusMethodId_callParentFunction()
    {
        $dAmount = 5;
        $oOrder = null;

        $oUserPayment = $this->getMock('oxUserPayment', array('__call'));
        $oUserPayment->oxuserpayments__oxpaymentsid = new oxField('somePaymentId');

        $oPayPalPlusConfig = $this->getMock('paypPayPalPlusConfig', array('getPayPalPlusMethodId'));
        $oPayPalPlusConfig->expects($this->once())->method('getPayPalPlusMethodId')->will($this->returnValue('somePaymentMethodId'));

        $oShop = $this->getMock('paypPayPalPlusShop', array('getPayPalPlusConfig'));
        $oShop->expects($this->once())->method('getPayPalPlusConfig')->will($this->returnValue($oPayPalPlusConfig));

        $this->SUT = $this->getMock(
            'paypPayPalPlusOxPaymentGateway',
            array('_getUserPayment', '_paypPayPalPlusOxPaymentGateway_executePayment_parent', '_executePayment', 'getShop')
        );
        $this->SUT->expects($this->once())->method('_getUserPayment')->will($this->returnValue($oUserPayment));
        $this->SUT->expects($this->once())->method('getShop')->will($this->returnValue($oShop));
        $this->SUT->expects($this->never())->method('_executePayment');
        $this->SUT->expects($this->once())->method('_paypPayPalPlusOxPaymentGateway_executePayment_parent')->with($dAmount, $oOrder);

        $this->SUT->executePayment($dAmount, $oOrder);
    }

    public function testExecutePayment_paymentNotCreated_returnFalse()
    {
        $dAmount = 5;
        $oOrder = null;

        $oUserPayment = $this->getMock('oxUserPayment', array('__call'));
        $oUserPayment->oxuserpayments__oxpaymentsid = new oxField('somePaymentId');

        $oPayPalPlusConfig = $this->getMock('paypPayPalPlusConfig', array('getPayPalPlusMethodId'));
        $oPayPalPlusConfig->expects($this->once())->method('getPayPalPlusMethodId')->will($this->returnValue('somePaymentId'));

        $oValidator = $this->getMock('paypPayPalPlusValidator', array('isPaymentCreated'));
        $oValidator->expects($this->once())->method('isPaymentCreated')->will($this->returnValue(false));

        $oPayPalPlusSession = $this->getMock('paypPayPalPlusValidator', array('getApprovedPayment', 'reset'));
        $oPayPalPlusSession->expects($this->once())->method('getApprovedPayment');
        $oPayPalPlusSession->expects($this->never())->method('reset');

        $oShop = $this->getMock('paypPayPalPlusShop', array('getPayPalPlusConfig', 'getValidator', 'getPayPalPlusSession'));
        $oShop->expects($this->once())->method('getPayPalPlusSession')->will($this->returnValue($oPayPalPlusSession));
        $oShop->expects($this->once())->method('getPayPalPlusConfig')->will($this->returnValue($oPayPalPlusConfig));
        $oShop->expects($this->once())->method('getValidator')->will($this->returnValue($oValidator));

        $this->SUT = $this->getMock(
            'paypPayPalPlusOxPaymentGateway',
            array('_getUserPayment', '_paypPayPalPlusOxPaymentGateway_executePayment_parent', '_executePayment', 'getShop')
        );
        $this->SUT->expects($this->once())->method('_getUserPayment')->will($this->returnValue($oUserPayment));
        $this->SUT->expects($this->once())->method('getShop')->will($this->returnValue($oShop));
        $this->SUT->expects($this->never())->method('_executePayment');

        $this->assertFalse($this->SUT->executePayment($dAmount, $oOrder));
    }

    public function testExecutePayment_paymentCreated_paymentNotExecuted_returnFalse()
    {
        $dAmount = 5;
        $oOrder = $this->getMock('oxOrder', array('__call'));

        $oUserPayment = $this->getMock('oxUserPayment', array('__call'));
        $oUserPayment->oxuserpayments__oxpaymentsid = new oxField('somePaymentId');

        $oPayPalPlusConfig = $this->getMock('paypPayPalPlusConfig', array('getPayPalPlusMethodId'));
        $oPayPalPlusConfig->expects($this->once())->method('getPayPalPlusMethodId')->will($this->returnValue('somePaymentId'));

        $oValidator = $this->getMock('paypPayPalPlusValidator', array('isPaymentCreated'));
        $oValidator->expects($this->any())->method('isPaymentCreated')->will($this->returnValue(true));

        $oPayPalPlusSession = $this->getMock('paypPayPalPlusSession', array('getApprovedPayment', 'reset', 'getPayerId', 'unsetApprovedPayment'));
        $oPayPalPlusSession->expects($this->once())->method('getPayerId')->will($this->returnValue(null));
        $oPayPalPlusSession->expects($this->once())->method('unsetApprovedPayment');
        $oPayPalPlusSession->expects($this->any())->method('getApprovedPayment');
        $oPayPalPlusSession->expects($this->never())->method('reset');

        $oShop = $this->getMock('paypPayPalPlusShop', array('getPayPalPlusConfig', 'getValidator', 'getPayPalPlusSession'));
        $oShop->expects($this->any())->method('getPayPalPlusSession')->will($this->returnValue($oPayPalPlusSession));
        $oShop->expects($this->once())->method('getPayPalPlusConfig')->will($this->returnValue($oPayPalPlusConfig));
        $oShop->expects($this->any())->method('getValidator')->will($this->returnValue($oValidator));

        $this->SUT = $this->getMock(
            'paypPayPalPlusOxPaymentGateway',
            array('_getUserPayment', '_paypPayPalPlusOxPaymentGateway_executePayment_parent', 'getShop')
        );
        $this->SUT->expects($this->once())->method('_getUserPayment')->will($this->returnValue($oUserPayment));
        $this->SUT->expects($this->any())->method('getShop')->will($this->returnValue($oShop));

        $this->assertFalse($this->SUT->executePayment($dAmount, $oOrder));
    }

    public function testExecutePayment_paymentCreated_paymentExecuted_resetsPayPalSession_callsParentFunction()
    {
        $dAmount = 5;
        $oOrder = $this->getMock('oxOrder', array('__call'));

        $oUserPayment = $this->getMock('oxUserPayment', array('__call'));
        $oUserPayment->oxuserpayments__oxpaymentsid = new oxField('somePaymentId');

        $oPayPalPlusConfig = $this->getMock('paypPayPalPlusConfig', array('getPayPalPlusMethodId'));
        $oPayPalPlusConfig->expects($this->once())->method('getPayPalPlusMethodId')->will($this->returnValue('somePaymentId'));

        $oValidator = $this->getMock('paypPayPalPlusValidator', array('isPaymentCreated'));
        $oValidator->expects($this->any())->method('isPaymentCreated')->will($this->returnValue(true));

        $oPayPalPlusSession = $this->getMock('paypPayPalPlusSession', array('getApprovedPayment', 'reset', 'getPayerId', 'unsetApprovedPayment', 'getApiContext'));
        $oPayPalPlusSession->expects($this->once())->method('getApiContext')->will($this->returnValue(new PayPal\Rest\ApiContext()));
        $oPayPalPlusSession->expects($this->once())->method('unsetApprovedPayment');
        $oPayPalPlusSession->expects($this->once())->method('getPayerId')->will($this->returnValue('somePayerId'));
        $oPayPalPlusSession->expects($this->any())->method('getApprovedPayment');
        $oPayPalPlusSession->expects($this->once())->method('reset');

        $oPayPalPlusPaymentHandler = $this->getMock('paypPayPalPlusPaymentHandler', array('setPayment', 'execute'));
        $oPayPalPlusPaymentHandler->expects($this->once())->method('execute')->will($this->returnValue(true));

        oxTestModules::addModuleObject('paypPayPalPlusPaymentHandler', $oPayPalPlusPaymentHandler);

        $oShop = $this->getMock('paypPayPalPlusShop', array('getPayPalPlusConfig', 'getValidator', 'getPayPalPlusSession'));
        $oShop->expects($this->any())->method('getPayPalPlusSession')->will($this->returnValue($oPayPalPlusSession));
        $oShop->expects($this->once())->method('getPayPalPlusConfig')->will($this->returnValue($oPayPalPlusConfig));
        $oShop->expects($this->any())->method('getValidator')->will($this->returnValue($oValidator));

        $this->SUT = $this->getMock(
            'paypPayPalPlusOxPaymentGateway',
            array('_getUserPayment', '_paypPayPalPlusOxPaymentGateway_executePayment_parent', 'getShop')
        );
        $this->SUT->expects($this->once())->method('_getUserPayment')->will($this->returnValue($oUserPayment));
        $this->SUT->expects($this->any())->method('getShop')->will($this->returnValue($oShop));
        $this->SUT->expects($this->once())->method('_paypPayPalPlusOxPaymentGateway_executePayment_parent')->with($dAmount, $oOrder);

        $this->SUT->executePayment($dAmount, $oOrder);
    }
}
