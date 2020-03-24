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
 * Class paypPayPalPlusConfigTest
 * Tests for paypPayPalPlusConfig
 *
 * @see paypPayPalPlusConfig
 */
class paypPayPalPlusConfigTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusConfig
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = new paypPayPalPlusConfig();
    }


    /**
     * @dataProvider magicPropertyGetterDataProvider
     */
    public function testMagicPropertyGetter($sGetterMethod, $mExpectedReturn)
    {
        $this->assertSame($mExpectedReturn, $this->SUT->$sGetterMethod());
    }

    public function magicPropertyGetterDataProvider()
    {
        return array(

            // Invalid calls
            array('anyCall', null),
            array('newPayPalPlusMethodId', null),
            array('getSomething', null),

            // Valid properties getters
            array('getPayPalPlusMethodId', 'payppaypalplus'),
            array('getModuleSettingsPrefix', 'paypPayPalPlus'),
            array('getSandboxSettingsPrefix', 'Sandbox'),
            array('getModeNameSandbox', 'SANDBOX'),
            array('getModeNameLive', 'LIVE'),
            array('getPayPalPlusJsUri', 'https://www.paypalobjects.com/webstatic/ppplus/ppplus.min.js'),
            array('getRefundablePaymentStatus', 'completed'),
            array('getExecutedPaymentSuccessStatus', 'approved'),
            array('getTransactionPendingState', 'pending'),
            array('getSuccessfulReturnParameter', 'payppaypalplussuccess'),
            array('getCancellationReturnParameter', 'payppaypalpluscancel'),
            array('getForcedPaymentParameter', 'force_paymentid'),
            array('getPayPalPaymentIdParameter', 'paymentId'),
            array('getPayPalPayerIdParameter', 'PayerID'),
            array('getPaymentIntent', 'sale'),
            array('getPayerPaymentMethod', 'paypal'),
            array('getDecimalsSeparator', '.'),
            array('getThousandsSeparator', ''),
        );
    }


    public function testIsSandbox_isSandBoxMode_returnTrue()
    {
        modConfig::getInstance()->setConfigParam('paypPayPalPlusSandbox', true);

        $this->assertTrue($this->SUT->isSandbox());
    }

    public function testIsSandbox_isNotSandBoxMode_returnFalse()
    {
        modConfig::getInstance()->setConfigParam('paypPayPalPlusSandbox', false);

        $this->assertFalse($this->SUT->isSandbox());
    }


    public function testGetMode_isSandBoxMode_returnSandbox()
    {
        modConfig::getInstance()->setConfigParam('paypPayPalPlusSandbox', true);

        $this->assertSame('SANDBOX', $this->SUT->getMode());
    }

    public function testGetMode_isNotSandBoxMode_returnLive()
    {
        modConfig::getInstance()->setConfigParam('paypPayPalPlusSandbox', false);

        $this->assertSame('LIVE', $this->SUT->getMode());
    }


    public function testGetBaseUri_isSandBoxMode_returnSandboxUrl()
    {
        modConfig::getInstance()->setConfigParam('paypPayPalPlusSandbox', true);

        $this->assertSame('https://api.sandbox.paypal.com', $this->SUT->getBaseUri());
    }

    public function testGetBaseUri_isNotSandBoxMode_returnLiveUrl()
    {
        modConfig::getInstance()->setConfigParam('paypPayPalPlusSandbox', false);

        $this->assertSame('https://api.paypal.com', $this->SUT->getBaseUri());
    }


    public function testGetMaxNumberRefundsPerPayment()
    {
        $this->assertSame(10, $this->SUT->getMaxNumberRefundsPerPayment());
    }


    public function testGetSetting_settingsAreSet_returnSettingsValue()
    {
        modConfig::getInstance()->setConfigParam('test1', 'test1setting');
        modConfig::getInstance()->setConfigParam('test2', 'test2setting');
        modConfig::getInstance()->setConfigParam('test3', 'test3setting');

        $this->assertSame('test1setting', $this->SUT->getSetting('test1'));
        $this->assertSame('test2setting', $this->SUT->getSetting('test2'));
        $this->assertSame('test3setting', $this->SUT->getSetting('test3'));
    }

    public function testGetSetting_settingsAreNotSet_returnNull()
    {
        $this->assertNull($this->SUT->getSetting('test1'));
        $this->assertNull($this->SUT->getSetting('test2'));
        $this->assertNull($this->SUT->getSetting('test3'));
    }


    public function testGetModuleSetting_moduleSettingsAreSet_returnSettingsValue()
    {
        modConfig::getInstance()->setConfigParam('paypPayPalPlustest1', 'test1setting');
        modConfig::getInstance()->setConfigParam('paypPayPalPlustest2', 'test2setting');
        modConfig::getInstance()->setConfigParam('paypPayPalPlustest3', 'test3setting');

        $this->assertSame('test1setting', $this->SUT->getModuleSetting('test1'));
        $this->assertSame('test2setting', $this->SUT->getModuleSetting('test2'));
        $this->assertSame('test3setting', $this->SUT->getModuleSetting('test3'));
    }

    public function testGetModuleSetting_moduleSettingsAreNotSet_returnNull()
    {
        $this->assertNull($this->SUT->getModuleSetting('test1'));
        $this->assertNull($this->SUT->getModuleSetting('test2'));
        $this->assertNull($this->SUT->getModuleSetting('test3'));
    }

    public function testGetModuleSetting_settingIsEmpty_returnAlternativeValue()
    {
        modConfig::getInstance()->setConfigParam('paypPayPalPlusLogFile', '');

        $this->assertSame('my_log.txt', $this->SUT->getModuleSetting('LogFile', 'my_log.txt'));
    }


    public function testGetClientId_isSandBoxMode_returnSandboxClientId()
    {
        $sClientId = 'someRandomSandboxClientIdString';

        modConfig::getInstance()->setConfigParam('paypPayPalPlusSandboxClientId', $sClientId);
        modConfig::getInstance()->setConfigParam('paypPayPalPlusSandbox', true);

        $this->assertSame($sClientId, $this->SUT->getClientId());
    }

    public function testGetClientId_isNotSandBoxMode_returnLiveClientId()
    {
        $sClientId = 'someRandomClientIdString';

        modConfig::getInstance()->setConfigParam('paypPayPalPlusClientId', $sClientId);
        modConfig::getInstance()->setConfigParam('paypPayPalPlusSandbox', false);

        $this->assertSame($sClientId, $this->SUT->getClientId());
    }


    public function testGetSecret_isSandBoxMode_returnSandboxSecret()
    {
        $sSecret = 'someRandomSandboxSecret';

        modConfig::getInstance()->setConfigParam('paypPayPalPlusSandboxSecret', $sSecret);
        modConfig::getInstance()->setConfigParam('paypPayPalPlusSandbox', true);

        $this->assertSame($sSecret, $this->SUT->getSecret());
    }

    public function testGetSecret_isNotSandBoxMode_returnLiveSecret()
    {
        $sSecret = 'someRandomSecret';

        modConfig::getInstance()->setConfigParam('paypPayPalPlusSecret', $sSecret);
        modConfig::getInstance()->setConfigParam('paypPayPalPlusSandbox', false);

        $this->assertSame($sSecret, $this->SUT->getSecret());
    }


    public function testGetInternalTransactionToken()
    {
        session_id('testSessionId');
        $sCurrentDate = date('Y-m-d');
        $sSessionId = (string) session_id();

        $this->assertSame(md5($sCurrentDate . $sSessionId), $this->SUT->getInternalTransactionToken());
    }


    public function testGetPayPalPlusCounties()
    {
        $this->assertSame(array('DE'), $this->SUT->getPayPalPlusCounties());
    }


    public function testGetConfiguredExternalMethods()
    {
        $aExternalMethods = array('externalMethod1', 'externalMethod2');

        modConfig::getInstance()->setConfigParam('paypPayPalPlusExternalMethods', $aExternalMethods);

        $this->assertSame($aExternalMethods, $this->SUT->getConfiguredExternalMethods());
    }


    public function testGetExternalMethodsExceptions_notIncludingPayPalPlus_returnArray()
    {
        $aExternalMethodsExceptions = array('oxiddebitnote', 'oxidcreditcard');

        $this->assertSame($aExternalMethodsExceptions, $this->SUT->getExternalMethodsExceptions(false));
    }

    public function testGetExternalMethodsExceptions_includingPayPalPlus_returnArray()
    {
        $aExternalMethodsExceptionsWithPayPalPlus = array('oxiddebitnote', 'oxidcreditcard', 'payppaypalplus');

        $this->assertSame($aExternalMethodsExceptionsWithPayPalPlus, $this->SUT->getExternalMethodsExceptions());
    }


    public function testGetExternalMethods_noExceptionsInMethods_returnArray()
    {
        $aExternalMethods = array('externalMethod1', 'externalMethod2');

        modConfig::getInstance()->setConfigParam('paypPayPalPlusExternalMethods', $aExternalMethods);

        $this->assertSame($aExternalMethods, $this->SUT->getExternalMethods());
    }

    public function testGetExternalMethods_oneExceptionInMethods_returnArrayWithNoExceptions()
    {
        $aExternalMethods = array('externalMethod1', 'externalMethod2', 'oxiddebitnote');

        modConfig::getInstance()->setConfigParam('paypPayPalPlusExternalMethods', $aExternalMethods);

        $this->assertNotContains('oxiddebitnote', $this->SUT->getExternalMethods());
        $this->assertNotContains('oxidcreditcard', $this->SUT->getExternalMethods());
    }

    public function testGetExternalMethods_twoExceptionsInMethods_returnArrayWithNoExceptions()
    {
        $aExternalMethods = array('oxidcreditcard', 'externalMethod1', 'externalMethod2', 'oxiddebitnote');

        modConfig::getInstance()->setConfigParam('paypPayPalPlusExternalMethods', $aExternalMethods);

        $this->assertNotContains('oxiddebitnote', $this->SUT->getExternalMethods());
        $this->assertNotContains('oxidcreditcard', $this->SUT->getExternalMethods());
    }


    public function testGetShopBaseLink()
    {
        $sShopSecureUrl = 'http://www.test.lt/index.php?test1=1&amp;test2=2&amp;test3=3';

        $oConfig = $this->getMock('oxConfig', array('getShopSecureHomeURL'));
        $oConfig->expects($this->once())->method('getShopSecureHomeURL')->will($this->returnValue($sShopSecureUrl));

        $oShop = $this->getMock('paypPayPalPlusShop', array('getConfig'));
        $oShop->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));

        $this->SUT = $this->getMock('paypPayPalPlusConfig', array('getShop'));
        $this->SUT->expects($this->once())->method('getShop')->will($this->returnValue($oShop));

        $this->assertSame('http://www.test.lt/index.php?test1=1&test2=2&test3=3', $this->SUT->getShopBaseLink());
    }

    public function testIsTemplateValidationNeeded_returnTrue()
    {
        modConfig::getInstance()->setConfigParam('paypPayPalPlusValidateTemplate', true);

        $this->assertTrue($this->SUT->isTemplateValidationNeeded());
    }

    public function testIsTemplateValidationNeeded_returnFalse()
    {
        modConfig::getInstance()->setConfigParam('paypPayPalPlusValidateTemplate', false);

        $this->assertFalse($this->SUT->isTemplateValidationNeeded());
    }

    public function testGetModuleSettingsPrefix(){
        $this->assertSame('paypPayPalPlus', $this->SUT->getModuleSettingsPrefix());
    }

    public function testIsMobile_ThemeSwitcherDeactivated_returnFalse(){
        $this->assertFalse($this->SUT->isMobile());
    }

    public function testIsMobile_ThemeSwitcherActivated_returnTrue(){
        if (class_exists('oeThemeSwitcherThemeManager')) {
            $oeThemeSwitcherThemeManager = $this->getMock(
                'oeThemeSwitcherThemeManager',
                array('isMobileThemeRequested')
            );
            $oeThemeSwitcherThemeManager->expects($this->once())->method('isMobileThemeRequested')->will(
                $this->returnValue(true)
            );

            $oShop = $this->getMock('paypPayPalPlusShop', array('getFromRegistry'));
            $oShop->expects($this->once())->method('getFromRegistry')->with('oeThemeSwitcherThemeManager')->will($this->returnValue($oeThemeSwitcherThemeManager));

            $this->SUT = $this->getMock('paypPayPalPlusConfig', array('getShop'));
            $this->SUT->expects($this->once())->method('getShop')->will($this->returnValue($oShop));

            $this->assertTrue($this->SUT->isMobile());
        }
    }

    public function testIsMobile_ThemeSwitcherActivated_returnFalse(){
        if (class_exists('oeThemeSwitcherThemeManager')) {
            $oeThemeSwitcherThemeManager = $this->getMock(
                'oeThemeSwitcherThemeManager',
                array('isMobileThemeRequested')
            );
            $oeThemeSwitcherThemeManager->expects($this->once())->method('isMobileThemeRequested')->will(
                $this->returnValue(false)
            );

            $oShop = $this->getMock('paypPayPalPlusShop', array('getFromRegistry'));
            $oShop->expects($this->once())->method('getFromRegistry')->with('oeThemeSwitcherThemeManager')->will($this->returnValue($oeThemeSwitcherThemeManager));

            $this->SUT = $this->getMock('paypPayPalPlusConfig', array('getShop'));
            $this->SUT->expects($this->once())->method('getShop')->will($this->returnValue($oShop));

            $this->assertFalse($this->SUT->isMobile());
        }
    }

    public function testIsFlowTheme_returnTrue(){
        modConfig::getInstance()->setConfigParam('sTheme', 'flow');
        $this->assertTrue($this->SUT->isFlowTheme());

    }

    public function testIsFlowTheme_returnFalse(){
        modConfig::getInstance()->setConfigParam('sTheme', 'azure');
        $this->assertFalse($this->SUT->isFlowTheme());
    }
}
