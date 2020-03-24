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
 * Class paypPayPalPlusSdkTest
 * Integration tests for paypPayPalPlusSdk wrapper class.
 *
 * @see paypPayPalPlusSdk
 */
class paypPayPalPlusSdkTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusSdk
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = new paypPayPalPlusSdk();
    }


    public function testGetSdkConfig_argumentIsTrue_returnAuthenticationIdAndSecret()
    {
        modConfig::getInstance()->setConfigParam('paypPayPalPlusSandbox', '1');
        modConfig::getInstance()->setConfigParam('paypPayPalPlusSandboxClientId', '#secret_ID');
        modConfig::getInstance()->setConfigParam('paypPayPalPlusSandboxSecret', '#very+secret+hash');

        $this->assertSame(array('#secret_ID', '#very+secret+hash'), $this->SUT->getSdkConfig(true));
    }

    public function testGetSdkConfig_argumentIsEmpty_returnSdkConfigurationArray()
    {
        modConfig::getInstance()->setConfigParam('paypPayPalPlusSandbox', '1');
        modConfig::getInstance()->setConfigParam('paypPayPalPlusTimeout', 0);
        modConfig::getInstance()->setConfigParam('paypPayPalPlusRetry', 2);
        modConfig::getInstance()->setConfigParam('paypPayPalPlusLogEnabled', '1');
        modConfig::getInstance()->setConfigParam('paypPayPalPlusLogFile', 'ppp.log');
        modConfig::getInstance()->setConfigParam('paypPayPalPlusLogLevel', 'INFO');
        modConfig::getInstance()->setConfigParam('paypPayPalPlusValidation', 'disabled');

        $aSdkConfig = $this->SUT->getSdkConfig();

        $this->assertInternalType('array', $aSdkConfig);
        $this->assertArrayHasKey('http.ConnectionTimeOut', $aSdkConfig);
        $this->assertSame(60, $aSdkConfig['http.ConnectionTimeOut']);
        $this->assertArrayHasKey('http.Retry', $aSdkConfig);
        $this->assertSame(2, $aSdkConfig['http.Retry']);
        $this->assertArrayHasKey('http.headers.PayPal-Partner-Attribution-Id', $aSdkConfig);
        $this->assertSame('Oxid_Cart_Plus', $aSdkConfig['http.headers.PayPal-Partner-Attribution-Id']);
        $this->assertArrayHasKey('mode', $aSdkConfig);
        $this->assertSame('SANDBOX', $aSdkConfig['mode']);
        $this->assertArrayHasKey('service.EndPoint', $aSdkConfig);
        $this->assertSame('https://api.sandbox.paypal.com', $aSdkConfig['service.EndPoint']);
        $this->assertArrayHasKey('log.LogEnabled', $aSdkConfig);
        $this->assertTrue($aSdkConfig['log.LogEnabled']);
        $this->assertArrayHasKey('log.FileName', $aSdkConfig);
        $this->assertStringEndsWith('ppp.log', $aSdkConfig['log.FileName']);
        $this->assertArrayHasKey('log.LogLevel', $aSdkConfig);
        $this->assertSame('INFO', $aSdkConfig['log.LogLevel']);
        $this->assertArrayHasKey('validation.level', $aSdkConfig);
        $this->assertSame('disabled', $aSdkConfig['validation.level']);
    }


    public function testNewTokenCredential()
    {
        modConfig::getInstance()->setConfigParam('paypPayPalPlusSandbox', '');
        modConfig::getInstance()->setConfigParam('paypPayPalPlusClientId', '#id');
        modConfig::getInstance()->setConfigParam('paypPayPalPlusSecret', '#key');

        $oTokenCredential = $this->SUT->newTokenCredential();

        $this->assertInstanceOf('PayPal\Auth\OAuthTokenCredential', $oTokenCredential);
        $this->assertSame('#id', $oTokenCredential->getClientId());
        $this->assertSame('#key', $oTokenCredential->getClientSecret());
    }


    public function testNewApiContext()
    {
        modConfig::getInstance()->setConfigParam('paypPayPalPlusSandbox', '1');
        modConfig::getInstance()->setConfigParam('paypPayPalPlusSandboxClientId', 'user');
        modConfig::getInstance()->setConfigParam('paypPayPalPlusSandboxSecret', 'pass');
        modConfig::getInstance()->setConfigParam('paypPayPalPlusTimeout', 30);
        modConfig::getInstance()->setConfigParam('paypPayPalPlusRetry', 3);
        modConfig::getInstance()->setConfigParam('paypPayPalPlusLogEnabled', '0');
        modConfig::getInstance()->setConfigParam('paypPayPalPlusLogFile', '');
        modConfig::getInstance()->setConfigParam('paypPayPalPlusLogLevel', 'DEBUG');
        modConfig::getInstance()->setConfigParam('paypPayPalPlusValidation', 'log');

        $oTokenCredentials = $this->SUT->newTokenCredential();
        $oApiContext = $this->SUT->newApiContext($oTokenCredentials);

        $this->assertInstanceOf('PayPal\Rest\ApiContext', $oApiContext);

        $oGotTokenCredentials = $oApiContext->getCredential();
        $this->assertSame($oTokenCredentials, $oGotTokenCredentials);
        $this->assertSame('user', $oGotTokenCredentials->getClientId());
        $this->assertSame('pass', $oGotTokenCredentials->getClientSecret());

        $aSdkConfig = $oApiContext->getConfig();
        $this->assertInternalType('array', $aSdkConfig);

        $this->assertArrayHasKey('http.ConnectionTimeOut', $aSdkConfig);
        $this->assertSame(30, $aSdkConfig['http.ConnectionTimeOut']);
        $this->assertArrayHasKey('http.Retry', $aSdkConfig);
        $this->assertSame(3, $aSdkConfig['http.Retry']);
        $this->assertArrayHasKey('http.headers.PayPal-Partner-Attribution-Id', $aSdkConfig);
        $this->assertSame('Oxid_Cart_Plus', $aSdkConfig['http.headers.PayPal-Partner-Attribution-Id']);
        $this->assertArrayHasKey('mode', $aSdkConfig);
        $this->assertSame('SANDBOX', $aSdkConfig['mode']);
        $this->assertArrayHasKey('service.EndPoint', $aSdkConfig);
        $this->assertSame('https://api.sandbox.paypal.com', $aSdkConfig['service.EndPoint']);
        $this->assertArrayHasKey('log.LogEnabled', $aSdkConfig);
        $this->assertFalse($aSdkConfig['log.LogEnabled']);
        $this->assertArrayHasKey('log.FileName', $aSdkConfig);
        $this->assertStringEndsWith('paypalplus.log', $aSdkConfig['log.FileName']);
        $this->assertArrayHasKey('log.LogLevel', $aSdkConfig);
        $this->assertSame('DEBUG', $aSdkConfig['log.LogLevel']);
        $this->assertArrayHasKey('validation.level', $aSdkConfig);
        $this->assertSame('log', $aSdkConfig['validation.level']);
    }


    /**
     * @dataProvider magicCallNotParsedDataProvider
     */
    public function testMagicCall_methodIsNotParsedAsNewSdkModelCreator_returnNull($sMethodToCall)
    {
        $this->assertNull($this->SUT->$sMethodToCall());
    }

    public function magicCallNotParsedDataProvider()
    {
        return array(
            array('someField'),
            array('getSomeField'),
            array('getAmount'),
            array('new'),
            array('getNewAmount'),
            array('NEWAmount'),
            array('newNothing'),
            array('newgetSdkConfig'),
        );
    }

    /**
     * @dataProvider magicCallParsedDataProvider
     */
    public function testMagicCall_methodIsParsedAsNewSdkModelCreator_returnInstanceOfTheSdkModel($sMethodToCall,
                                                                                                 $sExpectedClass)
    {
        $this->assertInstanceOf($sExpectedClass, $this->SUT->$sMethodToCall());
    }

    public function magicCallParsedDataProvider()
    {
        return array(
            array('newAmount', 'PayPal\Api\Amount'),
            array('newDetails', 'PayPal\Api\Details'),
            array('newItem', 'PayPal\Api\Item'),
            array('newItemList', 'PayPal\Api\ItemList'),
            array('newPatch', 'PayPal\Api\Patch'),
            array('newPatchRequest', 'PayPal\Api\PatchRequest'),
            array('newPayer', 'PayPal\Api\Payer'),
            array('newPayerInfo', 'PayPal\Api\PayerInfo'),
            array('newPayment', 'PayPal\Api\Payment'),
            array('newPaymentExecution', 'PayPal\Api\PaymentExecution'),
            array('newRedirectUrls', 'PayPal\Api\RedirectUrls'),
            array('newRefund', 'PayPal\Api\Refund'),
            array('newSale', 'PayPal\Api\Sale'),
            array('newShippingAddress', 'PayPal\Api\ShippingAddress'),
            array('newTransaction', 'PayPal\Api\Transaction'),
        );
    }
}
