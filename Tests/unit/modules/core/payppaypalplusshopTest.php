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
 * Class paypPayPalPlusShopTest
 * Tests for paypPayPalPlusShop wrapper class.
 *
 * @see paypPayPalPlusShop
 */
class paypPayPalPlusShopTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusShop
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock('paypPayPalPlusShop', array('__call'));
    }


    public function testGetShop()
    {
        $this->assertInstanceOf('paypPayPalPlusShop', $this->SUT->getShop());
    }


    public function testGetRequestParameter()
    {
        modConfig::setRequestParameter('test_param', 'test_VALUE!');

        $this->assertSame('test_VALUE!', $this->SUT->getRequestParameter('test_param'));
    }


    public function testGetSetting()
    {
        modConfig::getInstance()->setConfigParam('config_param', 'CONF-VALUE');

        $this->assertSame('CONF-VALUE', $this->SUT->getSetting('config_param'));
    }


    public function testSetSessionVariable()
    {
        modSession::getInstance()->setVar('some_session_var', 'old_value');

        $this->SUT->setSessionVariable('some_session_var', 'new_value');

        $this->assertSame('new_value', modSession::getInstance()->getVar('some_session_var'));
    }


    public function testGetSessionVariable()
    {
        modSession::getInstance()->setVar('other_val', 888);

        $this->assertSame(888, $this->SUT->getSessionVariable('other_val'));
    }


    public function testDeleteSessionVariable()
    {
        modSession::getInstance()->setVar('my_var', 'something');

        $this->SUT->deleteSessionVariable('my_var');

        $this->assertNull(modSession::getInstance()->getVar('my_var'));
    }


    public function testGetRequestOrSessionParameter_requestValueIsSet_useRequestValue()
    {
        modConfig::setRequestParameter('param', 'request');
        modSession::getInstance()->setVar('param', 'session');

        $this->assertSame('request', $this->SUT->getRequestOrSessionParameter('param'));
    }

    public function testGetRequestOrSessionParameter_requestValueIsNotSet_useSessionValue()
    {
        modSession::getInstance()->setVar('other_param', 'session');

        $this->assertSame('session', $this->SUT->getRequestOrSessionParameter('other_param'));
    }

    public function testGetRequestOrSessionParameter_requestValueIsEmpty_useSessionValue()
    {
        modConfig::setRequestParameter('another_param', '');
        modSession::getInstance()->setVar('another_param', 'session');

        $this->assertSame('session', $this->SUT->getRequestOrSessionParameter('another_param'));
    }


    public function testSetBasket()
    {
        $oBasket = $this->getMock('oxBasket', array('__call'));

        $this->SUT->setBasket($oBasket);

        $this->assertSame($oBasket, oxRegistry::getSession()->getBasket());
    }


    public function testGetBasket()
    {
        $oBasket = $this->getMock('oxBasket', array('__call'));

        oxRegistry::getSession()->setBasket($oBasket);

        $this->assertSame($oBasket, $this->SUT->getBasket());
    }


    public function testGetUser()
    {
        $oUser = $this->getMock('oxUser', array('__construct', '__call', '__get'));

        $this->SUT->unitCustModUser = $oUser;

        $this->assertSame($oUser, $this->SUT->getUser());
    }


    public function testGetNew()
    {
        $this->assertInstanceOf('stdClass', $this->SUT->getNew('stdClass'));
    }


    public function testGetFromRegistry()
    {
        $this->assertInstanceOf('oxSession', $this->SUT->getFromRegistry('oxSession'));
    }


    public function testGetDb()
    {
        $this->assertInstanceOf('oxLegacyDb', $this->SUT->getDb());
    }


    public function testGetStr()
    {
        $oStr = $this->SUT->getStr();

        $this->assertTrue(($oStr instanceof oxStrRegular) or ($oStr instanceof oxStrMb));
    }


    public function testGetConfig()
    {
        $this->assertInstanceOf('oxConfig', $this->SUT->getConfig());
    }


    public function testGetUtils()
    {
        $this->assertInstanceOf('oxUtils', $this->SUT->getUtils());
    }


    public function testGetLang()
    {
        $this->assertInstanceOf('oxLang', $this->SUT->getLang());
    }


    public function testTranslate()
    {
        $this->assertSame(':', $this->SUT->translate('COLON'));
    }


    public function testGetPayPalPlusModule()
    {
        $this->assertInstanceOf('paypPayPalPlusModule', $this->SUT->getPayPalPlusModule());
    }


    public function testGetPayPalPlusConfig()
    {
        $this->assertInstanceOf('paypPayPalPlusConfig', $this->SUT->getPayPalPlusConfig());
    }


    public function testGetPayPalPlusSession()
    {
        $oSession = $this->getMock('paypPayPalPlusSession', array('__call', 'init'));
        $oSession->expects($this->once())->method('init');
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\PayPalPlus\Core\Session::class, $oSession);

        $this->assertSame($oSession, $this->SUT->getPayPalPlusSession());
    }


    public function testGetDataAccess()
    {
        $this->assertInstanceOf('paypPayPalPlusDataAccess', $this->SUT->getDataAccess());
    }


    public function testGetConverter()
    {
        $this->assertInstanceOf('paypPayPalPlusDataConverter', $this->SUT->getConverter());
    }


    public function testGetValidator()
    {
        $this->assertInstanceOf('paypPayPalPlusValidator', $this->SUT->getValidator());
    }


    public function testGetErrorHandler()
    {
        $this->assertInstanceOf('paypPayPalPlusErrorHandler', $this->SUT->getErrorHandler());
    }
}
