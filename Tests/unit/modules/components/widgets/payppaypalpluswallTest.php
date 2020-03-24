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
 * Class paypPayPalPlusWallTest
 * Tests for core class paypPayPalPlusWall.
 *
 * @see paypPayPalPlusWall
 */
class paypPayPalPlusWallTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusWall
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock('paypPayPalPlusWall', array('__call'));

        modConfig::getInstance()->setConfigParam('sTheme', 'azure');
    }


    /**
     * test `init`, validation of payment returns false, _oPayment variable is not set
     * eShop wrapper helper is set
     */
    public function testInit_paymentIsCreatedValidationFailed_paymentNotSet()
    {
        $this->assertNull($this->SUT->getShop());

        $oPayPalPlusPayment = $this->getMock('PayPal\Api\Payment', array('getId'));

        $paypPayPalPlusSession = $this->getMock('paypPayPalPlusSession', array('getPayment'));
        $paypPayPalPlusSession->expects($this->any())->method('getPayment')->will(
            $this->returnValue($oPayPalPlusPayment)
        );

        oxTestModules::addModuleObject('paypPayPalPlusSession', $paypPayPalPlusSession);

        $this->SUT->init();
        $this->assertNull(PHPUnit_Framework_Assert::readAttribute($this->SUT, '_oPayment'));
        $this->assertInstanceOf('paypPayPalPlusShop', $this->SUT->getShop());
    }

    /**
     * test `init`, validation of payment returns true, _oPayment variable is set
     * eShop wrapper helper is set
     */
    public function testInit_paymentIsCreatedValidationPassed_callsGetPayment()
    {
        $this->assertNull($this->SUT->getShop());

        /** @var PHPUnit_Framework_MockObject_MockObject|PayPal\Api\Payment $oPayPalPlusPayment */
        $oPayPalPlusPayment = $this->getMock('PayPal\Api\Payment', array('getId'));
        $oPayPalPlusPayment->expects($this->once())->method('getId')->will($this->returnValue('testPaymentId'));

        $paypPayPalPlusSession = $this->getMock('paypPayPalPlusSession', array('getPayment'));
        $paypPayPalPlusSession->expects($this->any())->method('getPayment')->will(
            $this->returnValue($oPayPalPlusPayment)
        );

        oxTestModules::addModuleObject('paypPayPalPlusSession', $paypPayPalPlusSession);

        $this->SUT->init();

        $this->assertEquals($oPayPalPlusPayment, PHPUnit_Framework_Assert::readAttribute($this->SUT, '_oPayment'));
        $this->assertInstanceOf('paypPayPalPlusShop', $this->SUT->getShop());
    }


    /**
     * test `isCacheable`. Always returns false
     */
    public function testIsCacheable()
    {
        $this->assertFalse($this->SUT->isCacheable());
    }


    /**
     * test `getShop`. Shop object not initialized yet, returns null.
     */
    public function testGetShop_objectNotInitialized_returnNull()
    {
        $this->assertNull($this->SUT->getShop());
    }

    /**
     * test `getShop`. Shop object is initialized, returns shop object instance.
     */
    public function testGetShop_objectInitialized_returnShopWrapperInstance()
    {
        $this->SUT->init();
        $this->assertInstanceOf('paypPayPalPlusShop', $this->SUT->getShop());
    }


    /**
     * test `getPayPalPlusLibraryUrl`. Returns url of PayPal plus library.
     */
    public function testGetPayPalPlusLibraryUrl()
    {
        $this->SUT->init();
        $this->assertSame(
            'https://www.paypalobjects.com/webstatic/ppplus/ppplus.min.js',
            $this->SUT->getPayPalPlusLibraryUrl()
        );
    }


    /**
     * test `getApprovalUrl`. Returns PayPal Plus Payment object approval url
     */
    public function testGetApprovalUrl()
    {
        $sTestApprovalLink = 'test_approval_link';

        $oPayPalPlusLinks = new PayPal\Api\Links();
        $oPayPalPlusLinks->setRel('approval_url');
        $oPayPalPlusLinks->setHref($sTestApprovalLink);

        $oPayPalPlusPayment = new PayPal\Api\Payment();
        $oPayPalPlusPayment->setId('testPaymentId');
        $oPayPalPlusPayment->setLinks(array($oPayPalPlusLinks));

        $paypPayPalPlusSession = $this->getMock('paypPayPalPlusSession', array('getPayment'));
        $paypPayPalPlusSession->expects($this->any())->method('getPayment')->will(
            $this->returnValue($oPayPalPlusPayment)
        );

        oxTestModules::addModuleObject('paypPayPalPlusSession', $paypPayPalPlusSession);

        $this->SUT->init();

        $this->assertSame($sTestApprovalLink, $this->SUT->getApprovalUrl());
    }


    /**
     * test `getApiMode`. Api mode can only be SANDBOX or LIVE. Both of them pass the test.
     */
    public function testGetApiMode()
    {
        $this->SUT->init();
        $this->assertRegExp('/^sandbox|live$/', $this->SUT->getApiMode());
    }


    /**
     * test `getLanguageCode`. Mocking a lot, because there is core functions and tested methods
     */
    public function testGetLanguageCode_localeCodeNotSet_returnsDefaultValue()
    {
        $oLang = $this->getMock('oxLang', array('__call', 'getLanguageAbbr'));
        $oLang->expects($this->once())->method('getLanguageAbbr')->will($this->returnValue('en'));

        $oShop = $this->getMock('paypPayPalPlusShop', array('__call', 'getLang'));
        $oShop->expects($this->once())->method('getLang')->will($this->returnValue($oLang));

        $oSUT = $this->getMock('paypPayPalPlusWall', array('__call', 'getShop', 'getCountryCode'));
        $oSUT->expects($this->any())->method('getShop')->will($this->returnValue($oShop));

        $aLanguageParamsMock = array('en' => array(''));
        modConfig::getInstance()->setConfigParam('aLanguageParams', $aLanguageParamsMock);

        $this->assertSame('de_DE', $oSUT->getLanguageCode());
    }

    /**
     * test `getLanguageCode`. Mocking a lot, because there is core functions and tested methods
     */
    public function testGetLanguageCode_localeCodeIsSet_returnsValueSet()
    {
        $oLang = $this->getMock('oxLang', array('__call', 'getLanguageAbbr'));
        $oLang->expects($this->once())->method('getLanguageAbbr')->will($this->returnValue('en'));

        $oShop = $this->getMock('paypPayPalPlusShop', array('__call', 'getLang'));
        $oShop->expects($this->once())->method('getLang')->will($this->returnValue($oLang));

        $oSUT = $this->getMock('paypPayPalPlusWall', array('__call', 'getShop', 'getCountryCode'));
        $oSUT->expects($this->any())->method('getShop')->will($this->returnValue($oShop));

        $aLanguageParamsMock = array('en' => array('payppaypalplus_localecode' => 'en_US'));
        modConfig::getInstance()->setConfigParam('aLanguageParams', $aLanguageParamsMock);

        $this->assertSame('en_US', $oSUT->getLanguageCode());
    }

    /**
     * test `getCountryCode`. Setting the user billing country to germany and expecting a correct
     * return value from getUserCountryCode
     */
    public function testGetCountryCode_userHasCountryCode_returnsCountryCode()
    {
        $oUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $oUser->oxuser__oxcountryid = new oxField('a7c40f631fc920687.20179984');

        $oShop = $this->getMock('paypPayPalPlusShop', array('__call', 'getUser'));
        $oShop->expects($this->once())->method('getUser')->will($this->returnValue($oUser));

        $oSUT = $this->getMock('paypPayPalPlusWall', array('__call', 'getShop'));
        $oSUT->expects($this->once())->method('getShop')->will($this->returnValue($oShop));

        $this->assertSame('DE', $oSUT->getCountryCode());
    }

    /**
     * test `getCountryCode`. Setting the user billing country to none and expecting a
     * default value to be returned
     */
    public function testGetCountryCode_userDoesNotHaveCountryCode_returnsDefaultCode()
    {
        $oUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $oUser->oxuser__oxcountryid = new oxField('');

        $oShop = $this->getMock('paypPayPalPlusShop', array('__call', 'getUser'));
        $oShop->expects($this->once())->method('getUser')->will($this->returnValue($oUser));

        $oSUT = $this->getMock('paypPayPalPlusWall', array('__call', 'getShop'));
        $oSUT->expects($this->once())->method('getShop')->will($this->returnValue($oShop));

        $this->assertSame('US', $oSUT->getCountryCode());
    }


    /**
     * test `getErrorMessage`. eShop return a translation of `PAYP_PAYPALPLUS_ERROR_NOPAYMENT` string.
     */
    public function testGetErrorMessage()
    {
        $sTranslatedString = 'Translated string for testing';

        $oShop = $this->getMock('paypPayPalPlusShop', array('translate'));
        $oShop->expects($this->once())->method('translate')->with('PAYP_PAYPALPLUS_ERROR_NOPAYMENT')->will(
            $this->returnValue($sTranslatedString)
        );

        $this->SUT = $this->getMock('paypPayPalPlusWall', array('__call', 'getShop'));
        $this->SUT->expects($this->once())->method('getShop')->will($this->returnValue($oShop));

        $this->assertSame($sTranslatedString, $this->SUT->getErrorMessage());
    }


    /**
     * test `getGeneralErrorCode`. Returns a general error code.
     */
    public function testGetGeneralErrorCode()
    {
        $this->SUT->init();
        $this->assertSame('_PAYP_PAYPALPLUS_ERROR_', $this->SUT->getGeneralErrorCode());
    }


    public function testGetExternalButtonId()
    {
        modConfig::getInstance()->setConfigParam('paypPayPalPlusNextButtonId', 'testExternalButtonId');

        $this->SUT->init();

        $this->assertSame('testExternalButtonId', $this->SUT->getExternalButtonId());
    }


    public function testGetNextStepLink()
    {
        modConfig::getInstance()->setConfigParam('paypPayPalPlusNextLink', 'a');

        $this->SUT->init();

        $this->assertSame('a', $this->SUT->getNextStepLink());
    }


    public function testGetNextStepLinkParent()
    {
        modConfig::getInstance()->setConfigParam('paypPayPalPlusNextLinkParent', 'i');

        $this->SUT->init();

        $this->assertSame('i', $this->SUT->getNextStepLinkParent());
    }


    public function testGetPaymentRadioButton()
    {
        modConfig::getInstance()->setConfigParam('paypPayPalPlusPaymentRadio', 'input[name="payment_radio"]');

        $this->SUT->init();

        $this->assertSame('input[name="payment_radio"]', $this->SUT->getPaymentRadioButton());
    }


    public function testGetPaymentListItem()
    {
        modConfig::getInstance()->setConfigParam('paypPayPalPlusListItem', 'li');

        $this->SUT->init();

        $this->assertSame('li', $this->SUT->getPaymentListItem());
    }


    public function testGetPaymentListItemTitle()
    {
        modConfig::getInstance()->setConfigParam('paypPayPalPlusListItemTitle', 'span.caption');

        $this->SUT->init();

        $this->assertSame('span.caption', $this->SUT->getPaymentListItemTitle());
    }


    public function testGetPaymentLabelFormat()
    {
        modConfig::getInstance()->setConfigParam('paypPayPalPlusLabelFormat', 'p#method_%s');

        $this->SUT->init();

        $this->assertSame('p#method_%s', $this->SUT->getPaymentLabelFormat());
    }


    public function testGetPaymentLabelChild()
    {
        modConfig::getInstance()->setConfigParam('paypPayPalPlusLabelChild', 'strong');

        $this->SUT->init();

        $this->assertSame('strong', $this->SUT->getPaymentLabelChild());
    }


    public function testGetPaymentDescription()
    {
        modConfig::getInstance()->setConfigParam('paypPayPalPlusDescription', '.info');

        $this->SUT->init();

        $this->assertSame('.info', $this->SUT->getPaymentDescription());
    }


    public function testGetPaymentIdPrefix()
    {
        modConfig::getInstance()->setConfigParam('paypPayPalPlusMethodIdPrefix', 'pay_with_');

        $this->SUT->init();

        $this->assertSame('pay_with_', $this->SUT->getPaymentIdPrefix());
    }


    /**
     * test `getAjaxResponseToken`. Testing if ajax response token is formatted correctly.
     */
    public function testGetAjaxResponseToken()
    {
        $this->SUT->init();

        session_id('testSessionId');
        $sExpectedToken = md5(date('Y-m-d') . (string) session_id());

        $this->assertSame($sExpectedToken, $this->SUT->getAjaxResponseToken());
    }


    /**
     * test `getExternalMethods` using a data provider. Scenarios are defined in the data provider.
     *
     * @param string $sTestingCondition
     * @param array  $aConfiguredExternalMethods
     * @param string $sExpectedResult
     *
     * @dataProvider externalMethodsDataProvider
     */
    public function testGetExternalMethods($sTestingCondition, array $aConfiguredExternalMethods, $sExpectedResult)
    {
        $this->SUT->init();

        $paypPayPalPlusConfig = $this->getMock('paypPayPalPlusConfig', array('getConfiguredExternalMethods'));
        $paypPayPalPlusConfig->expects($this->any())->method('getConfiguredExternalMethods')->will(
            $this->returnValue($aConfiguredExternalMethods)
        );

        oxTestModules::addModuleObject('paypPayPalPlusConfig', $paypPayPalPlusConfig);

        $this->assertSame($sExpectedResult, $this->SUT->getExternalMethods(), $sTestingCondition);
    }

    /**
     * Data provider for testing `getExternalMethods`
     *
     * @return array
     */
    public function externalMethodsDataProvider()
    {
        return array(
            array(
                'No methods configured',
                array(),
                array(),
            ),
            array(
                'One method configured',
                array('testMethod'),
                array('testMethod'),
            ),
            array(
                'One method configured and there is one exception method which is removed',
                array('testMethod', 'oxiddebitnote'),
                array('testMethod'),
            ),
            array(
                'Only exception methods configured, they are removed',
                array('oxiddebitnote', 'oxidcreditcard'),
                array(),
            ),
            array(
                'More methods are configured',
                array("testMethod1", "testMethod2", "testMethod3"),
                array("testMethod1", "testMethod2", "testMethod3"),
            ),
        );
    }


    /**
     * test `getExternalMethodsRedirectUrl` using a data provider. Scenarios are defined in the data provider.
     *
     * @param string $sTestingCondition
     * @param string $sHomeUrl
     * @param string $sExpectedResult
     *
     * @dataProvider externalMethodsRedirectUrlDataProvider
     */
    public function testGetExternalMethodsRedirectUrl($sTestingCondition, $sHomeUrl, $sExpectedResult)
    {
        $this->SUT->init();

        $oConfig = $this->getMock('oxConfig', array('getShopSecureHomeUrl'));
        $oConfig->expects($this->any())->method('getShopSecureHomeUrl')->will($this->returnValue($sHomeUrl));

        oxTestModules::addModuleObject('oxConfig', $oConfig);

        $this->assertSame($sExpectedResult, $this->SUT->getExternalMethodsRedirectUrl(), $sTestingCondition);
    }

    /**
     * Data provider for testing `getExternalMethodsRedirectUrl`
     *
     * @return array
     */
    public function externalMethodsRedirectUrlDataProvider()
    {
        $sAddedString = "cl=payment&fnc=routePayment&paymentid=";

        return array(
            array(
                'Simple home url',
                'http://www.test.lt/index.php?',
                'http://www.test.lt/index.php?' . $sAddedString,
            ),
            array(
                'With html entities',
                'http://www.test.lt/index.php?test=1&amp;test1=1&amp;',
                'http://www.test.lt/index.php?test=1&test1=1&' . $sAddedString,
            ),
        );
    }

    /**
     * test 'isMobile', should be always false when running tests
     */
    public function testIsMobileWhenRunningTests_returnFalse()
    {
        $this->SUT->init();
        $this->assertFalse($this->SUT->isMobile());
    }

    public function testIsTemplateValidationNeeded_returnTrue(){
        modConfig::getInstance()->setConfigParam('paypPayPalPlusValidateTemplate', true);
        $this->SUT->init();
        $this->assertTrue($this->SUT->isTemplateValidationNeeded());

    }

    public function testIsTemplateValidationNeeded_returnFalse(){
        modConfig::getInstance()->setConfigParam('paypPayPalPlusValidateTemplate', false);
        $this->SUT->init();
        $this->assertFalse($this->SUT->isTemplateValidationNeeded());
    }

    public function testIsFlow_returnTrue(){
        modConfig::getInstance()->setConfigParam('sTheme', 'flow');
        $this->SUT->init();
        $this->assertTrue($this->SUT->isFlow());

    }

    public function testIsFlow_returnFalse(){
        modConfig::getInstance()->setConfigParam('sTheme', 'azure');
        $this->SUT->init();
        $this->assertFalse($this->SUT->isFlow());
    }

    public function testIsMobile_returnTrue(){
        $oPaypPayPalPlusConfig = $this->getMock('paypPayPalPlusConfig', array('_call', 'isMobile'));
        $oPaypPayPalPlusConfig->expects($this->once())->method('isMobile')->will($this->returnValue(true));

        $oShop = $this->getMock('paypPayPalPlusShop', array('getPayPalPlusConfig'));
        $oShop->expects($this->once())->method('getPayPalPlusConfig')->will($this->returnValue($oPaypPayPalPlusConfig));

        $this->SUT = $this->getMock('paypPayPalPlusWall', array('__call', 'getShop'));
        $this->SUT->expects($this->once())->method('getShop')->will($this->returnValue($oShop));

        $this->assertTrue($this->SUT->isMobile());
    }

    public function testIsMobile_returnFalse(){
        $oPaypPayPalPlusConfig = $this->getMock('paypPayPalPlusConfig', array('_call', 'isMobile'));
        $oPaypPayPalPlusConfig->expects($this->once())->method('isMobile')->will($this->returnValue(false));

        $oShop = $this->getMock('paypPayPalPlusShop', array('getPayPalPlusConfig'));
        $oShop->expects($this->once())->method('getPayPalPlusConfig')->will($this->returnValue($oPaypPayPalPlusConfig));

        $this->SUT = $this->getMock('paypPayPalPlusWall', array('__call', 'getShop'));
        $this->SUT->expects($this->once())->method('getShop')->will($this->returnValue($oShop));

        $this->assertFalse($this->SUT->isMobile());
    }
}
