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
 * Class paypPayPalPlusPaymentHandlerTest
 * Unit tests for paypPayPalPlusPaymentHandler helper class.
 *
 * @see paypPayPalPlusPaymentHandler
 */
class paypPayPalPlusPaymentHandlerTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusPaymentHandler
     */
    protected $SUT;

    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock('paypPayPalPlusPaymentHandler', array('__call'));
    }

    public function testGetPayment_nothingSet_returnNull()
    {
        $this->assertNull($this->SUT->getPayment());
    }

    public function testGetPayment_paymentObjectSet_returnTheObject()
    {
        $oPayment = new PayPal\Api\Payment();

        $this->SUT->setPayment($oPayment);

        $this->assertSame($oPayment, $this->SUT->getPayment());
    }

    public function testGetTaxationHandler()
    {
        $this->assertInstanceOf('paypPayPalPlusTaxationHandler', $this->SUT->getTaxationHandler());
    }

    public function testInit_shopIsInB2BMode_fillPaymentDataAndUseTaxValue()
    {
        modConfig::getInstance()->setConfigParam('blShowNetPrice', '1');

        $oArticle                       = $this->getMock('oxArticle', array('__construct', '__call'));
        $oArticle->oxarticles__oxtitle  = new oxField('Item One');
        $oArticle->oxarticles__oxartnum = new oxField('ITM-1');

        $oBasketItemPrice = $this->getMock('oxPrice', array('__construct', 'getNettoPrice', 'getVatValue'));
        $oBasketItemPrice->expects($this->once())->method('getNettoPrice')->will($this->returnValue(99.99));
        $oBasketItemPrice->expects($this->once())->method('getVatValue')->will($this->returnValue(4.02));

        $oBasketItem = $this->getMock('oxBasketItem', array('__call', 'getArticle', 'getUnitPrice', 'getAmount'));
        $oBasketItem->expects($this->exactly(2))->method('getArticle')->will($this->returnValue($oArticle));
        $oBasketItem->expects($this->exactly(2))->method('getUnitPrice')->will($this->returnValue($oBasketItemPrice));
        $oBasketItem->expects($this->once())->method('getAmount')->will($this->returnValue(1));

        $oBasket = $this->getMock(
            'oxBasket',
            array('__call', 'getPrice', 'getBasketCurrency', 'getCosts', 'getTotalDiscountSum', 'getContents')
        );
        $oBasket->expects($this->once())->method('getPrice')->will($this->returnValue(new oxPrice(100.0)));
        $oBasket->expects($this->exactly(2))->method('getBasketCurrency')->will(
            $this->returnValue((object)array('name' => 'CHF'))
        );
        $oBasket->expects($this->at(2))->method('getCosts')->with('oxpayment')->will(
            $this->returnValue(new oxPrice(1.23))
        );
        $oBasket->expects($this->at(3))->method('getCosts')->with('oxgiftcard')->will(
            $this->returnValue(new oxPrice(3.00))
        );
        $oBasket->expects($this->at(4))->method('getCosts')->with('oxwrapping')->will(
            $this->returnValue(new oxPrice(2.85))
        );
        $oBasket->expects($this->at(5))->method('getCosts')->with('oxtsprotection')->will(
            $this->returnValue(null)
        );
        $oBasket->expects($this->at(6))->method('getCosts')->with('oxdelivery')->will(
            $this->returnValue(new oxPrice(3.90))
        );
        $oBasket->expects($this->once())->method('getTotalDiscountSum')->will($this->returnValue(15.00));
        $oBasket->expects($this->once())->method('getContents')->will($this->returnValue(array($oBasketItem)));
        $this->SUT->getShop()->setBasket($oBasket);

        $this->assertNull($this->SUT->getPayment());

        $this->SUT->init();

        $oPayment = $this->SUT->getPayment();
        $this->assertInstanceOf('PayPal\Api\Payment', $oPayment);
        $this->assertSame('sale', $oPayment->getIntent());

        $oPayer = $oPayment->getPayer();
        $this->assertInstanceOf('PayPal\Api\Payer', $oPayer);
        $this->assertSame('paypal', $oPayer->getPaymentMethod());

        $aTransactions = $oPayment->getTransactions();
        $this->assertInternalType('array', $aTransactions);
        $this->assertArrayHasKey(0, $aTransactions);

        $oTransaction = $aTransactions[0];
        $this->assertInstanceOf('PayPal\Api\Transaction', $oTransaction);

        $oAmount = $oTransaction->getAmount();
        $this->assertInstanceOf('PayPal\Api\Amount', $oAmount);
        $this->assertSame('100.00', $oAmount->getTotal());
        $this->assertSame('CHF', $oAmount->getCurrency());

        $oDetail = $oAmount->getDetails();
        $this->assertInstanceOf('PayPal\Api\Details', $oDetail);
        $this->assertSame('99.99', $oDetail->getSubtotal());
        $this->assertSame('4.02', $oDetail->getTax());
        $this->assertSame('7.09', $oDetail->getHandlingFee());
        $this->assertSame('0.00', $oDetail->getInsurance());
        $this->assertSame('3.90', $oDetail->getShipping());
        $this->assertSame('-15.00', $oDetail->getShippingDiscount());

        $oItemList = $oTransaction->getItemList();
        $this->assertInstanceOf('PayPal\Api\ItemList', $oItemList);

        $aItems = $oItemList->getItems();
        $this->assertInternalType('array', $aItems);
        $this->assertArrayHasKey(0, $aItems);

        $oItem = $aItems[0];
        $this->assertInstanceOf('PayPal\Api\Item', $oItem);
        $this->assertSame('Item One', $oItem->getName());
        $this->assertSame('CHF', $oItem->getCurrency());
        $this->assertSame('99.99', $oItem->getPrice());
        $this->assertSame('1', $oItem->getQuantity());
        $this->assertSame('4.02', $oItem->getTax());
        $this->assertSame('ITM-1', $oItem->getSku());

        $mShippingAddress = $oItemList->getShippingAddress();
        $this->assertNull($mShippingAddress);

        $oRedirectUrls = $oPayment->getRedirectUrls();
        $this->assertInstanceOf('PayPal\Api\RedirectUrls', $oRedirectUrls);
        $this->assertStringEndsWith('cl=payment&payppaypalpluscancel=1', $oRedirectUrls->getCancelUrl());
        $this->assertStringEndsWith(
            'cl=order&payppaypalplussuccess=1&force_paymentid=payppaypalplus',
            $oRedirectUrls->getReturnUrl()
        );
    }

    public function testInit_shopIsInB2CMode_fillPaymentDataUsePricesWithTaxIncluded()
    {
        modConfig::getInstance()->setConfigParam('blShowNetPrice', '');

        $oArticle                       = $this->getMock('oxArticle', array('__construct', '__call'));
        $oArticle->oxarticles__oxtitle  = new oxField('Item One');
        $oArticle->oxarticles__oxartnum = new oxField('ITM-1');

        $oBasketItemPrice = $this->getMock('oxPrice', array('__construct', 'getNettoPrice', 'getVatValue'));
        $oBasketItemPrice->expects($this->once())->method('getNettoPrice')->will($this->returnValue(99.99));
        $oBasketItemPrice->expects($this->once())->method('getVatValue')->will($this->returnValue(4.02));

        $oBasketItem = $this->getMock('oxBasketItem', array('__call', 'getArticle', 'getUnitPrice', 'getAmount'));
        $oBasketItem->expects($this->exactly(2))->method('getArticle')->will($this->returnValue($oArticle));
        $oBasketItem->expects($this->exactly(2))->method('getUnitPrice')->will($this->returnValue($oBasketItemPrice));
        $oBasketItem->expects($this->once())->method('getAmount')->will($this->returnValue(1));

        $oBasket = $this->getMock(
            'oxBasket',
            array('__call', 'getPrice', 'getBasketCurrency', 'getCosts', 'getTotalDiscountSum', 'getContents')
        );
        $oBasket->expects($this->once())->method('getPrice')->will($this->returnValue(new oxPrice(100.0)));
        $oBasket->expects($this->exactly(2))->method('getBasketCurrency')->will(
            $this->returnValue((object)array('name' => 'CHF'))
        );
        $oBasket->expects($this->at(2))->method('getCosts')->with('oxpayment')->will(
            $this->returnValue(new oxPrice(1.23))
        );
        $oBasket->expects($this->at(3))->method('getCosts')->with('oxgiftcard')->will(
            $this->returnValue(new oxPrice(3.00))
        );
        $oBasket->expects($this->at(4))->method('getCosts')->with('oxwrapping')->will(
            $this->returnValue(new oxPrice(2.85))
        );
        $oBasket->expects($this->at(5))->method('getCosts')->with('oxtsprotection')->will(
            $this->returnValue(null)
        );
        $oBasket->expects($this->at(6))->method('getCosts')->with('oxdelivery')->will(
            $this->returnValue(new oxPrice(3.90))
        );
        $oBasket->expects($this->once())->method('getTotalDiscountSum')->will($this->returnValue(15.00));
        $oBasket->expects($this->once())->method('getContents')->will($this->returnValue(array($oBasketItem)));
        $this->SUT->getShop()->setBasket($oBasket);

        $this->assertNull($this->SUT->getPayment());

        $this->SUT->init();

        $oPayment = $this->SUT->getPayment();
        $this->assertInstanceOf('PayPal\Api\Payment', $oPayment);

        $aTransactions = $oPayment->getTransactions();
        $this->assertInternalType('array', $aTransactions);
        $this->assertArrayHasKey(0, $aTransactions);

        $oTransaction = $aTransactions[0];
        $this->assertInstanceOf('PayPal\Api\Transaction', $oTransaction);

        $oAmount = $oTransaction->getAmount();
        $this->assertInstanceOf('PayPal\Api\Amount', $oAmount);
        $this->assertSame('100.00', $oAmount->getTotal());
        $this->assertSame('CHF', $oAmount->getCurrency());

        $oDetail = $oAmount->getDetails();
        $this->assertInstanceOf('PayPal\Api\Details', $oDetail);
        $this->assertSame('104.01', $oDetail->getSubtotal());
        $this->assertSame('0.00', $oDetail->getTax());
        $this->assertSame('7.09', $oDetail->getHandlingFee());
        $this->assertSame('0.00', $oDetail->getInsurance());
        $this->assertSame('3.90', $oDetail->getShipping());
        $this->assertSame('-15.00', $oDetail->getShippingDiscount());

        $oItemList = $oTransaction->getItemList();
        $this->assertInstanceOf('PayPal\Api\ItemList', $oItemList);

        $aItems = $oItemList->getItems();
        $this->assertInternalType('array', $aItems);
        $this->assertArrayHasKey(0, $aItems);

        $oItem = $aItems[0];
        $this->assertInstanceOf('PayPal\Api\Item', $oItem);
        $this->assertSame('Item One', $oItem->getName());
        $this->assertSame('CHF', $oItem->getCurrency());
        $this->assertSame('104.01', $oItem->getPrice());
        $this->assertSame('1', $oItem->getQuantity());
        $this->assertSame('0.00', $oItem->getTax());
        $this->assertSame('ITM-1', $oItem->getSku());
    }

    public function testCreate_apiThrowsException_processTheExceptionWithErrorHandler()
    {
        $oApiContext = $this->getMock('PayPal\Rest\ApiContext');

        $oException = new Exception('Error while creating payment.');

        $oPayment = $this->getMock('PayPal\Api\Payment', array('create'));
        $oPayment->expects($this->once())->method('create')->with($oApiContext)->will(
            $this->throwException($oException)
        );

        $oErrorHandler = $this->getMock('paypPayPalPlusErrorHandler', array('__call', 'debug'));
        $oErrorHandler->expects($this->once())->method('debug')->with($oException, $oPayment);
        \OxidEsales\Eshop\Core\Registry::set(\OxidProfessionalServices\PayPalPlus\Core\ErrorHandler::class, $oErrorHandler);

        /** @var PHPUnit_Framework_MockObject_MockObject|paypPayPalPlusPaymentHandler $SUT */
        $SUT = $this->getMock('paypPayPalPlusPaymentHandler', array('__call', 'getPayment'));
        $SUT->expects($this->exactly(2))->method('getPayment')->will($this->returnValue($oPayment));

        $SUT->create($oApiContext);
    }

    public function testCreate_noExceptionThrown_doNoAdditionalActions()
    {
        $oApiContext = $this->getMock('PayPal\Rest\ApiContext');

        $oPayment = $this->getMock('PayPal\Api\Payment', array('create'));
        $oPayment->expects($this->once())->method('create')->with($oApiContext);

        $oErrorHandler = $this->getMock('paypPayPalPlusErrorHandler', array('__call', 'debug'));
        $oErrorHandler->expects($this->never())->method('debug');
        \OxidEsales\Eshop\Core\Registry::set(\OxidProfessionalServices\PayPalPlus\Core\ErrorHandler::class, $oErrorHandler);

        /** @var PHPUnit_Framework_MockObject_MockObject|paypPayPalPlusPaymentHandler $SUT */
        $SUT = $this->getMock('paypPayPalPlusPaymentHandler', array('__call', 'getPayment'));
        $SUT->expects($this->once())->method('getPayment')->will($this->returnValue($oPayment));

        $SUT->create($oApiContext);
    }

    public function testUpdate_apiThrowsException_processTheExceptionWithErrorHandler()
    {
        $oApiContext = $this->getMock('PayPal\Rest\ApiContext');

        $oException = new Exception('Error while updating payment.');

        $oPayment = $this->getMock('PayPal\Api\Payment', array('toArray', 'update'));
        $oPayment->expects($this->once())->method('toArray')->will($this->returnValue(array('some_data' => 1)));
        $oPayment->expects($this->once())->method('update')
            ->with($this->isInstanceOf('PayPal\Api\PatchRequest'), $oApiContext)
            ->will($this->throwException($oException));

        $oErrorHandler = $this->getMock('paypPayPalPlusErrorHandler', array('__call', 'debug', 'parseError'));
        $oErrorHandler->expects($this->once())->method('debug')->with($oException);
        $oErrorHandler->expects($this->once())->method('parseError')->with($oException);
        \OxidEsales\Eshop\Core\Registry::set(\OxidProfessionalServices\PayPalPlus\Core\ErrorHandler::class, $oErrorHandler);

        /** @var PHPUnit_Framework_MockObject_MockObject|paypPayPalPlusPaymentHandler $SUT */
        $SUT = $this->getMock('paypPayPalPlusPaymentHandler', array('__call', 'getPayment'));
        $SUT->expects($this->exactly(2))->method('getPayment')->will($this->returnValue($oPayment));

        $SUT->update($oApiContext);
    }

    public function testUpdate_noExceptionThrown_fillPaymentWithUserData()
    {
        $oApiContext = $this->getMock('PayPal\Rest\ApiContext');

        $oUser = $this->getMock('oxUser', array('__construct', '__call', 'getSelectedAddress'));

        $oShop = $this->getMock('paypPayPalPlusShop', array('getUser'));
        $oShop->expects($this->once())->method('getUser')->will($this->returnValue($oUser));

        $oUserDataProvider = $this->getMock('paypPayPalPlusUserData', array('getShop'));
        $oUserDataProvider->expects($this->any())->method('getShop')->will($this->returnValue($oShop));
        oxTestModules::addModuleObject('paypPayPalPlusUserData', $oUserDataProvider);

        $oPayment = $this->getMock('PayPal\Api\Payment', array('toArray', 'update'));
        $oPayment->expects($this->once())->method('toArray')->will(
            $this->returnValue(
                array(
                    'transactions' => array(
                        array(
                            'amount'    => array('total' => 100.0),
                            'item_list' => array(
                                'shipping_address' => array('line1' => 'Empty st. 0'),
                            ),
                        ),
                    ),
                    'payer'        => array(
                        'payer_info' => array(
                            'FirstName'      => 'John',
                            'LastName'       => 'Smith',
                            'Email'          => 'john@smith.com',
                            'BillingAddress' => array('line1' => 'bla'),

                        )
                    ),
                )
            )
        );
        $oPayment->expects($this->once())->method('update')->with(
            $this->isInstanceOf('PayPal\Api\PatchRequest'),
            $oApiContext
        );

        $oErrorHandler = $this->getMock('paypPayPalPlusErrorHandler', array('__call', 'debug', 'parseError'));
        $oErrorHandler->expects($this->never())->method('debug');
        $oErrorHandler->expects($this->never())->method('parseError');
        \OxidEsales\Eshop\Core\Registry::set(\OxidProfessionalServices\PayPalPlus\Core\ErrorHandler::class, $oErrorHandler);

        /** @var PHPUnit_Framework_MockObject_MockObject|paypPayPalPlusPaymentHandler $SUT */
        $SUT = $this->getMock('paypPayPalPlusPaymentHandler', array('__call', 'getPayment'));
        $SUT->expects($this->exactly(2))->method('getPayment')->will($this->returnValue($oPayment));

        $SUT->update($oApiContext);
    }

    public function testExecute_apiThrowsException_processTheExceptionWithErrorHandlerAndReturnFalse()
    {
        $oApiContext = $this->getMock('PayPal\Rest\ApiContext');

        $oExecution = $this->getMock('PayPal\Api\PaymentExecution', array('setPayerId'));
        $oExecution->expects($this->once())->method('setPayerId')->with('Payer-ONE');

        $oSdk = $this->getMock('paypPayPalPlusSdk', array('newPaymentExecution'));
        $oSdk->expects($this->once())->method('newPaymentExecution')->will($this->returnValue($oExecution));

        $oException = new Exception('Error while executing payment.');

        $oPayment = $this->getMock('PayPal\Api\Payment', array('execute'));
        $oPayment->expects($this->once())->method('execute')->with($oExecution, $oApiContext)->will(
            $this->throwException($oException)
        );

        $oErrorHandler = $this->getMock('paypPayPalPlusErrorHandler', array('__call', 'debug'));
        $oErrorHandler->expects($this->once())->method('debug')->with($oException);
        \OxidEsales\Eshop\Core\Registry::set(\OxidProfessionalServices\PayPalPlus\Core\ErrorHandler::class, $oErrorHandler);

        /** @var PHPUnit_Framework_MockObject_MockObject|paypPayPalPlusPaymentHandler $SUT */
        $SUT = $this->getMock('paypPayPalPlusPaymentHandler', array('__call', 'getPayment', 'getSdk'));
        $SUT->expects($this->once())->method('getPayment')->will($this->returnValue($oPayment));
        $SUT->expects($this->once())->method('getSdk')->will($this->returnValue($oSdk));

        $oOrder = $this->getMock('oxOrder', array('__construct', '__call'));

        $this->assertFalse($SUT->execute('Payer-ONE', $oApiContext, $oOrder));
    }

    public function testExecute_apiReturnsNull_returnFalse()
    {
        $oApiContext = $this->getMock('PayPal\Rest\ApiContext');

        $oExecution = $this->getMock('PayPal\Api\PaymentExecution', array('setPayerId'));
        $oExecution->expects($this->once())->method('setPayerId')->with('Payer-ONE');

        $oSdk = $this->getMock('paypPayPalPlusSdk', array('newPaymentExecution'));
        $oSdk->expects($this->once())->method('newPaymentExecution')->will($this->returnValue($oExecution));

        $oPayment = $this->getMock('PayPal\Api\Payment', array('execute'));
        $oPayment->expects($this->once())->method('execute')->with($oExecution, $oApiContext)->will(
            $this->returnValue(null)
        );

        $oErrorHandler = $this->getMock('paypPayPalPlusErrorHandler', array('__call', 'debug'));
        $oErrorHandler->expects($this->once())->method('debug');
        \OxidEsales\Eshop\Core\Registry::set(\OxidProfessionalServices\PayPalPlus\Core\ErrorHandler::class, $oErrorHandler);

        /** @var PHPUnit_Framework_MockObject_MockObject|paypPayPalPlusPaymentHandler $SUT */
        $SUT = $this->getMock('paypPayPalPlusPaymentHandler', array('__call', 'getPayment', 'getSdk'));
        $SUT->expects($this->once())->method('getPayment')->will($this->returnValue($oPayment));
        $SUT->expects($this->once())->method('getSdk')->will($this->returnValue($oSdk));

        $oOrder = $this->getMock('oxOrder', array('__construct', '__call'));

        $this->assertFalse($SUT->execute('Payer-ONE', $oApiContext, $oOrder));
    }

    /**
     * If getPayment returns null
     * - an InvalidArgumentException is thrown and catched
     * - the exception is passed to the error handler in debug level
     * - the method execute returns false
     */
    public function testExecute_getPaymentReturnsNull_returnFalse()
    {
        $oApiContext = $this->getMock('PayPal\Rest\ApiContext');

        $oExecution = $this->getMock('PayPal\Api\PaymentExecution', array('setPayerId'));
        $oExecution->expects($this->once())->method('setPayerId')->with('Payer-ONE');

        $oSdk = $this->getMock('paypPayPalPlusSdk', array('newPaymentExecution'));
        $oSdk->expects($this->once())->method('newPaymentExecution')->will($this->returnValue($oExecution));

        $oErrorHandler = $this->getMock('paypPayPalPlusErrorHandler', array('__call', 'debug'));
        $oErrorHandler->expects($this->once())->method('debug');
        \OxidEsales\Eshop\Core\Registry::set(\OxidProfessionalServices\PayPalPlus\Core\ErrorHandler::class, $oErrorHandler);

        /** @var PHPUnit_Framework_MockObject_MockObject|paypPayPalPlusPaymentHandler $SUT */
        $SUT = $this->getMock(
            'paypPayPalPlusPaymentHandler',
            array('__call', 'getPayment', 'getSdk', '_throwInvalidArgumentException')
        );
        $SUT->expects($this->once())->method('getPayment')->will($this->returnValue(null));
        $SUT->expects($this->once())->method('getSdk')->will($this->returnValue($oSdk));
        $SUT->expects($this->once())->method('_throwInvalidArgumentException')->will(
            $this->throwException(new Exception())
        );

        $oOrder = $this->getMock('oxOrder', array('__construct', '__call'));

        $this->assertFalse($SUT->execute('Payer-ONE', $oApiContext, $oOrder));
    }

    /**
     * If the payment is not approved by PayPal
     * - an PaymentValidationException is thrown and catched
     * - the exception is passed to the error handler in debug level
     * - the method execute returns false
     */
    public function testExecute_paymentIsNotApproved_returnFalse()
    {
        $sPaymentState     = 'rejected';
        $sTransactionState = 'completed';

        $oApiContext = $this->getMock('PayPal\Rest\ApiContext');

        $oExecution = $this->getMock('PayPal\Api\PaymentExecution', array('setPayerId'));
        $oExecution->expects($this->once())->method('setPayerId')->with('Payer-ONE');

        $oSdk = $this->getMock('paypPayPalPlusSdk', array('newPaymentExecution'));
        $oSdk->expects($this->once())->method('newPaymentExecution')->will($this->returnValue($oExecution));

        $oExecutedPayment = $this->getMock('PayPal\Api\Payment', array('getState'));
        $oExecutedPayment->expects($this->any())->method('getState')->will($this->returnValue($sPaymentState));

        $oPayment = $this->getMock('PayPal\Api\Payment', array('execute'));
        $oPayment->expects($this->once())->method('execute')->with($oExecution, $oApiContext)->will(
            $this->returnValue($oExecutedPayment)
        );

        $oErrorHandler = $this->getMock('paypPayPalPlusErrorHandler', array('__call', 'debug'));
        $oErrorHandler->expects($this->once())->method('debug');
        \OxidEsales\Eshop\Core\Registry::set(\OxidProfessionalServices\PayPalPlus\Core\ErrorHandler::class, $oErrorHandler);

        /** @var PHPUnit_Framework_MockObject_MockObject|paypPayPalPlusPaymentHandler $SUT */
        $SUT = $this->getMock(
            'paypPayPalPlusPaymentHandler',
            array('__call', 'getPayment', 'getSdk', '_getTransactionState', '_throwPaymentValidationException')
        );
        $SUT->expects($this->any())->method('_getTransactionState')->will($this->returnValue($sTransactionState));
        $SUT->expects($this->once())->method('_throwPaymentValidationException')->will(
            $this->throwException(new Exception())
        );
        $SUT->expects($this->once())->method('getPayment')->will($this->returnValue($oPayment));
        $SUT->expects($this->once())->method('getSdk')->will($this->returnValue($oSdk));

        $oOrder = $this->getMock('oxOrder', array('__construct', '__call'));

        $this->assertFalse($SUT->execute('Payer-ONE', $oApiContext, $oOrder));
    }

    public function testExecute_paymentIsApproved_setOrderPaidSavePaymentAndReturnTrue()
    {
        $sPaymentInstructionInstructionType = 'PAY_UPON_INVOICE';
        $sPaymentState                      = 'approved';
        $sTransactionState                  = 'completed';

        $oApiContext = $this->getMock('PayPal\Rest\ApiContext');

        $oExecution = $this->getMock('PayPal\Api\PaymentExecution', array('setPayerId'));
        $oExecution->expects($this->once())->method('setPayerId')->with('Payer-Two');

        $oSdk = $this->getMock('paypPayPalPlusSdk', array('newPaymentExecution'));
        $oSdk->expects($this->once())->method('newPaymentExecution')->will($this->returnValue($oExecution));

        $oExecutedPayment = $this->getMock('PayPal\Api\Payment', array('getState', 'getUpdateTime', 'getId'));
        $this->assertInstanceOf('PayPal\Api\Payment', $oExecutedPayment);
        $oExecutedPayment->expects($this->any())->method('getState')->will($this->returnValue($sPaymentState));
        $oExecutedPayment->expects($this->once())->method('getUpdateTime')->will(
            $this->returnValue('2015-01-01T00:01:01Z')
        );

        $oPayment = $this->getMock('PayPal\Api\Payment', array('execute'));
        $oPayment->expects($this->once())->method('execute')->with($oExecution, $oApiContext)->will(
            $this->returnValue($oExecutedPayment)
        );

        $oErrorHandler = $this->getMock('paypPayPalPlusErrorHandler', array('__call', 'debug'));
        $oErrorHandler->expects($this->never())->method('debug');
        \OxidEsales\Eshop\Core\Registry::set(\OxidProfessionalServices\PayPalPlus\Core\ErrorHandler::class, $oErrorHandler);

        $oOrder = $this->getMock('oxOrder', array('__construct', '__call'));

        $oPaymentData = $this->getMock(
            'paypPayPalPlusPaymentData',
            array('__construct', '__call', 'save', 'setOrderPaid', 'getOrder')
        );
        $oPaymentData->expects($this->once())->method('setOrderPaid')->with('2015-01-01T00:01:01Z');
        oxTestModules::addModuleObject('paypPayPalPlusPaymentData', $oPaymentData);

        $oPayPalPlusPaymentDataProvider = $this->getMock('paypPayPalPlusPaymentDataProvider', array('init', 'getData'));
        $oPayPalPlusPaymentDataProvider->expects($this->any())->method('getData')->will(
            $this->returnValue(array('Status' => $sTransactionState))
        );

        $oPayPalPlusPuiDataModel = $this->getMock('paypPayPalPlusPuiData', array('save'));
        $oPayPalPlusPuiDataModel->expects($this->once())->method('save')->will($this->returnValue(true));

        $oPayPalPlusPuiDataProvider = $this->getMock(
            'paypPayPalPlusPuiDataProvider',
            array('getPaymentInstructionInstructionType')
        );
        $oPayPalPlusPuiDataProvider->expects($this->once())->method('getPaymentInstructionInstructionType')->will(
            $this->returnValue($sPaymentInstructionInstructionType)
        );

        /** @var PHPUnit_Framework_MockObject_MockObject|paypPayPalPlusPaymentHandler $SUT */
        $SUT = $this->getMock(
            'paypPayPalPlusPaymentHandler',
            array(
                '__call',
                'getPayment',
                'getSdk',
                '_logToFile',
                '_savePaymentData',
                '_getPayPalPlusPuiDataProvider',
                '_getPayPalPlusPaymentDataProvider',
                '_getPayPalPlusPuiDataModel'
            )
        );
        $SUT->expects($this->once())->method('getPayment')->will($this->returnValue($oPayment));
        $SUT->expects($this->once())->method('getSdk')->will($this->returnValue($oSdk));
        $SUT->expects($this->once())->method('_savePaymentData')->will($this->returnValue($oPaymentData));
        $SUT->expects($this->once())->method('_getPayPalPlusPuiDataProvider')->will(
            $this->returnValue($oPayPalPlusPuiDataProvider)
        );
        $SUT->expects($this->once())->method('_getPayPalPlusPuiDataModel')->will(
            $this->returnValue($oPayPalPlusPuiDataModel)
        );
        $SUT->expects($this->any())->method('_getPayPalPlusPaymentDataProvider')->will(
            $this->returnValue($oPayPalPlusPaymentDataProvider)
        );

        $SUT->expects($this->once())->method('_logToFile')->with($oExecutedPayment);

        $this->assertTrue($SUT->execute('Payer-Two', $oApiContext, $oOrder));
    }

    /**
     * If the sale transaction state is pending
     * - save the payment data
     * - do not set the order as paid
     * - return true
     */
    public function testExecute_executedPaymentTransactionStateIsPending_DoNotSetOrderPaidSavePaymentAndReturnTrue()
    {
        $sPaymentState     = 'approved';
        $sTransactionState = 'pending';

        $oApiContext = $this->getMock('PayPal\Rest\ApiContext');

        $oExecution = $this->getMock('PayPal\Api\PaymentExecution', array('setPayerId'));
        $oExecution->expects($this->once())->method('setPayerId')->with('Payer-Two');

        $oSdk = $this->getMock('paypPayPalPlusSdk', array('newPaymentExecution'));
        $oSdk->expects($this->once())->method('newPaymentExecution')->will($this->returnValue($oExecution));

        $oExecutedPayment = $this->getMock('PayPal\Api\Payment', array('getState', 'getId'));
        $this->assertInstanceOf('PayPal\Api\Payment', $oExecutedPayment);
        $oExecutedPayment->expects($this->any())->method('getState')->will($this->returnValue($sPaymentState));

        $oPayment = $this->getMock('PayPal\Api\Payment', array('execute'));
        $oPayment->expects($this->once())->method('execute')->with($oExecution, $oApiContext)->will(
            $this->returnValue($oExecutedPayment)
        );

        $oErrorHandler = $this->getMock('paypPayPalPlusErrorHandler', array('__call', 'debug'));
        $oErrorHandler->expects($this->never())->method('debug');
        \OxidEsales\Eshop\Core\Registry::set(\OxidProfessionalServices\PayPalPlus\Core\ErrorHandler::class, $oErrorHandler);

        $oOrder = $this->getMock('oxOrder', array('__construct', '__call', 'setPaymentDateAndTime', 'save', 'getId'));
        $oOrder->expects($this->once())->method('save')->will($this->returnValue(true));

        $oPaymentDataModel = $this->getMock(
            'paypPayPalPlusPaymentData',
            array('__construct', '__call', 'save', 'setOrderPaid', 'getOrder')
        );
        $oPaymentDataModel->expects($this->once())->method('save')->will($this->returnValue(true));
        $oPaymentDataModel->expects($this->never())->method('setOrderPaid');
        oxTestModules::addModuleObject('paypPayPalPlusPaymentData', $oPaymentDataModel);

        /** @var PHPUnit_Framework_MockObject_MockObject|paypPayPalPlusPaymentHandler $SUT */
        $SUT = $this->getMock(
            'paypPayPalPlusPaymentHandler',
            array('__call', 'getPayment', 'getSdk', '_logToFile', '_getTransactionState')
        );
        $SUT->expects($this->any())->method('_getTransactionState')->will($this->returnValue($sTransactionState));
        $SUT->expects($this->once())->method('getPayment')->will($this->returnValue($oPayment));
        $SUT->expects($this->once())->method('getSdk')->will($this->returnValue($oSdk));
        $SUT->expects($this->once())->method('_logToFile')->with($oExecutedPayment);

        $this->assertTrue($SUT->execute('Payer-Two', $oApiContext, $oOrder));
    }

    /**
     * If the sale transaction state nor pending neither completed
     * - throw exception
     */
    public function testExecute_returnsFalse_onExecutedPaymentTransactionStateIsSomethingFunny()
    {
        $sPaymentState     = 'approved';
        $sTransactionState = 'Something funny';

        $oApiContext = $this->getMock('PayPal\Rest\ApiContext');

        $oExecution = $this->getMock('PayPal\Api\PaymentExecution', array('setPayerId'));
        $oExecution->expects($this->once())->method('setPayerId')->with('Payer-Two');

        $oSdk = $this->getMock('paypPayPalPlusSdk', array('newPaymentExecution'));
        $oSdk->expects($this->once())->method('newPaymentExecution')->will($this->returnValue($oExecution));

        $oExecutedPayment = $this->getMock('PayPal\Api\Payment', array('getState', 'getId'));
        $this->assertInstanceOf('PayPal\Api\Payment', $oExecutedPayment);
        $oExecutedPayment->expects($this->any())->method('getState')->will($this->returnValue($sPaymentState));

        $oPayment = $this->getMock('PayPal\Api\Payment', array('execute'));
        $oPayment->expects($this->once())->method('execute')->with($oExecution, $oApiContext)->will(
            $this->returnValue($oExecutedPayment)
        );

        $oErrorHandler = $this->getMock('paypPayPalPlusErrorHandler', array('__call', 'debug'));
        $oErrorHandler->expects($this->once())->method('debug');
        \OxidEsales\Eshop\Core\Registry::set(\OxidProfessionalServices\PayPalPlus\Core\ErrorHandler::class, $oErrorHandler);

        $oOrder = $this->getMock('oxOrder', array('__construct', '__call', 'setPaymentDateAndTime', 'save', 'getId'));

        /** @var PHPUnit_Framework_MockObject_MockObject|paypPayPalPlusPaymentHandler $SUT */
        $SUT = $this->getMock(
            'paypPayPalPlusPaymentHandler',
            array('__call', 'getPayment', 'getSdk', '_getTransactionState')
        );
        $SUT->expects($this->any())->method('_getTransactionState')->will($this->returnValue($sTransactionState));
        $SUT->expects($this->once())->method('getPayment')->will($this->returnValue($oPayment));
        $SUT->expects($this->once())->method('getSdk')->will($this->returnValue($oSdk));

        $this->assertFalse($SUT->execute('Payer-Two', $oApiContext, $oOrder));
    }

    public function testGetApprovalUrl_noPaymentSet_returnEmptyString()
    {
        $this->assertSame('', $this->SUT->getApprovalUrl());
    }

    public function testGetApprovalUrl_paymentHasNoLinksSet_returnEmptyString()
    {
        $oPayment = new PayPal\Api\Payment();

        $this->SUT->setPayment($oPayment);

        $this->assertSame('', $this->SUT->getApprovalUrl());
    }

    public function testGetApprovalUrl_paymentHasNoApprovalLink_returnEmptyString()
    {
        $oLink = new PayPal\Api\Links();
        $oLink->setRel('some_link');
        $oLink->setHref('www.example.com/some/action');

        $oPayment = new PayPal\Api\Payment();
        $oPayment->setLinks(array($oLink));

        $this->SUT->setPayment($oPayment);

        $this->assertSame('', $this->SUT->getApprovalUrl());
    }

    public function testGetApprovalUrl_paymentHasAnApprovalLink_returnTheUrl()
    {
        $oLink = new PayPal\Api\Links();
        $oLink->setRel('approval_url');
        $oLink->setHref('www.example.com/approve/action');

        $oPayment = new PayPal\Api\Payment();
        $oPayment->setLinks(array($oLink));

        $this->SUT->setPayment($oPayment);

        $this->assertSame('www.example.com/approve/action', $this->SUT->getApprovalUrl());
    }
}
