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
 * Class paypPayPalPlusEventsTest
 * Tests for paypPayPalPlusEvents
 *
 * @see paypPayPalPlusEvents
 */
class paypPayPalPlusEventsTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusEvents
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock('paypPayPalPlusEvents', array('__call', '_registerPaypalWebhook'));
    }


    /**
     * test `activate` when module was never installed. Installs module, adds required tables, adds payment method,
     * assigns all countries, user groups and shipping methods to it, disables default duplicate methods.
     */
    public function testActivate_moduleIsNotInstalled_installsModule()
    {
        $aDuplicateMethods = (array) paypPayPalPlusShop::getShop()->getPayPalPlusConfig()->getExternalMethodsExceptions(
            false
        );

        importTestdataFile("uninstallModule.sql");

        foreach ($aDuplicateMethods as $sPaymentMethodId) {
            /** @var oxPayment $oPayment */
            $oPayment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);

            if ($oPayment->load($sPaymentMethodId)) {
                $oPayment->oxpayments__oxactive = new oxField(1);
                $oPayment->save();
            }
        }

        $this->assertEmpty(\OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getOne("SELECT OXID FROM `oxpayments` WHERE OXID = 'payppaypalplus'"));
        $this->assertTrue(
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getOne("SELECT COUNT(OXID) FROM `oxobject2payment` WHERE OXPAYMENTID = 'payppaypalplus'") == 0
        );
        $this->assertTrue(
            oxDb::getDb()->getOne("SELECT COUNT(OXID) FROM `oxobject2group` WHERE OXOBJECTID = 'payppaypalplus'") == 0
        );
        $this->assertFalse($this->_tableExists('payppaypalpluspayment'));
        $this->assertFalse($this->_tableExists('payppaypalplusrefund'));
        $this->assertFalse($this->_tableExists('payppaypalpluspui'));

        $this->SUT->activate();

        $this->assertNotEmpty(\OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getOne("SELECT OXID FROM `oxpayments` WHERE OXID = 'payppaypalplus'"));
        $this->assertTrue(
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getOne(
                "SELECT COUNT(OXID) FROM `oxobject2payment` WHERE OXPAYMENTID = 'payppaypalplus'"
            ) > 0
        );
        $this->assertTrue(
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getOne("SELECT COUNT(OXID) FROM `oxobject2group` WHERE OXOBJECTID = 'payppaypalplus'") > 0
        );
        $this->assertTrue($this->_tableExists('payppaypalpluspayment'));
        $this->assertTrue($this->_tableExists('payppaypalplusrefund'));

        foreach ($aDuplicateMethods as $sPaymentMethodId) {
            /** @var \OxidEsales\Eshop\Application\Model\Payment $oPayment */
            $oPayment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);

            if ($oPayment->load($sPaymentMethodId)) {
                $this->assertTrue($oPayment->oxpayments__oxactive->value == 0);
            }
        }
    }

    /**
     * test `activate` when module is installed. Sets payment method to active.
     */
    public function testActivate_moduleIsInstalled_moduleIsNotActive_activatesModule()
    {
        $sMethodId = (string) paypPayPalPlusShop::getShop()->getPayPalPlusConfig()->getPayPalPlusMethodId();
        $oPayment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);

        if ($oPayment->load($sMethodId)) {
            $oPayment->oxpayments__oxactive = new oxField(0);
            $oPayment->save();
        }

        $this->SUT->activate();

        if ($oPayment->load($sMethodId)) {
            $this->assertTrue($oPayment->oxpayments__oxactive->value == 1);
        }
    }

    /**
     * test `deactivate`. Sets payment method to disabled.
     */
    public function testDeactivate_deactivatesModule()
    {
        $sMethodId = (string) paypPayPalPlusShop::getShop()->getPayPalPlusConfig()->getPayPalPlusMethodId();
        $oPayment = oxNew(\OxidEsales\Eshop\Application\Model\Payment::class);

        if ($oPayment->load($sMethodId)) {
            $oPayment->oxpayments__oxactive = new oxField(1);
            $oPayment->save();
        }

        $this->SUT->deactivate();

        if ($oPayment->load($sMethodId)) {
            $this->assertTrue($oPayment->oxpayments__oxactive->value == 0);
        }
    }

    /**
     * test `clearTmp`.
     */
    public function testClearTmp()
    {
        $sTempFolderPath = (string) oxRegistry::getConfig()->getConfigParam('sCompileDir');
        $sSmartyFolderPath = $sTempFolderPath . '/smarty';

        //adding test files to tmp and smarty folders
        file_put_contents($sTempFolderPath . '/test.txt', 'test text');

        if (file_exists($sSmartyFolderPath)) {
            file_put_contents($sSmartyFolderPath . '/test.txt', 'test text');
        }

        $this->SUT->clearTmp();

        $this->assertFileNotExists($sTempFolderPath . '/test.txt');
        $this->assertFileNotExists($sSmartyFolderPath . '/test.txt');
    }


    /**
     * Check if table exists in the database
     *
     * @param string $sTable Table name
     *
     * @return bool
     */
    protected function _tableExists($sTable)
    {
        $oConfig = oxRegistry::getConfig();
        $sDbName = $oConfig->getConfigParam('dbName');

        $sQuery = "
            SELECT 1
            FROM information_schema.TABLES
            WHERE TABLE_NAME = ?
            AND TABLE_SCHEMA = ?
        ";

        return (bool) \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getOne($sQuery, array($sTable, $sDbName));
    }


    public function testCreatePaypalWebhook_willNotCreateIfWebhooAlreadyExists() {

        $oWebhookList = $this->getMock('PayPal\Api\WebhookList', array('getWebhooks'));
        $oWebhookList->expects($this->once())->method('getWebhooks')->will($this->returnValue(true));

        $this->SUT = $this->getMock(
            'paypPayPalPlusEvents',
            array(
                '__call',
                '_isInstalled',
                '_getWebHookList',
                '_webHookListContainsShopWebhook',
                '_createPaypalWebhook',
                '_togglePaymentMethod',
                'clearTmp',
                'updateViews',
                '_checkApiSettings',
            ));
        $this->SUT->expects($this->once())->method('_getWebHookList')->will($this->returnValue($oWebhookList));
        $this->SUT->expects($this->once())->method('_webHookListContainsShopWebhook')->will($this->returnValue(true));
        $this->SUT->expects($this->once())->method('_isInstalled')->will($this->returnValue(true));
        $this->SUT->expects($this->never())->method('_createPaypalWebhook');

        $this->SUT->activate();
    }

    /**
     * If there are no webhooks at all registered with PAyPal, register a new webhook
     */
    public function testCreatePaypalWebhook_createsWebhookIfNoWebhookExistsAtAll() {

        $oWebhookList = $this->getMock('PayPal\Api\WebhookList', array('getWebhooks'));
        $oWebhookList->expects($this->once())->method('getWebhooks')->will($this->returnValue(false));

        $this->SUT = $this->getMock(
            'paypPayPalPlusEvents',
            array(
                '__call',
                '_isInstalled',
                '_getWebHookList',
                '_createPaypalWebhook',
                '_togglePaymentMethod',
                'clearTmp',
                'updateViews',
                '_checkApiSettings',
            ));
        $this->SUT->expects($this->once())->method('_getWebHookList')->will($this->returnValue($oWebhookList));
        $this->SUT->expects($this->once())->method('_isInstalled')->will($this->returnValue(true));
        $this->SUT->expects($this->once())->method('_createPaypalWebhook');

        $this->SUT->activate();
    }

    /**
     * If there are webhooks registered, but we do not own them register a webhook
     */
    public function testCreatePaypalWebhook_createsWebhookIfNoOwnWebhookExists() {

        $oWebhookList = $this->getMock('PayPal\Api\WebhookList', array('getWebhooks'));
        $oWebhookList->expects($this->once())->method('getWebhooks')->will($this->returnValue(true));

        $this->SUT = $this->getMock(
            'paypPayPalPlusEvents',
            array(
                '__call',
                '_isInstalled',
                '_getWebHookList',
                '_createPaypalWebhook',
                '_webHookListContainsShopWebhook',
                '_togglePaymentMethod',
                'clearTmp',
                'updateViews',
                '_checkApiSettings',
            ));
        $this->SUT->expects($this->once())->method('_getWebHookList')->will($this->returnValue($oWebhookList));
        $this->SUT->expects($this->once())->method('_isInstalled')->will($this->returnValue(true));
        $this->SUT->expects($this->once())->method('_webHookListContainsShopWebhook')->will($this->returnValue(false));
        $this->SUT->expects($this->once())->method('_createPaypalWebhook');

        $this->SUT->activate();
    }

    public function testWebHookListContainsShopWebhook_returnsFalseIfDoesNotContain()
    {
        $expectedUrl = "https://some.domain.com/index.php?cl=somecontroller";
        $unExpectedUrl = "https://some.otherdomain.com/index.php?cl=someothercontroller";

        $oWebhook = $this->getMock('PayPal\Api\Webhook', array('getUrl'));
        $oWebhook->expects($this->any())->method('getUrl')->will($this->returnValue($expectedUrl));

        $oWebhookList = $this->getMock('PayPal\Api\WebhookList', array('getWebhooks'));
        $oWebhookList->expects($this->any())->method('getWebhooks')->will($this->returnValue(array($oWebhook)));

        $this->SUT = $this->getMock(
            'paypPayPalPlusEvents',
            array(
                '__call',
                '_isInstalled',
                '_getWebHookList',
                '_createPaypalWebhook',
                '_getWebhookUrl',
                '_togglePaymentMethod',
                'clearTmp',
                'updateViews',
                '_checkApiSettings',
            ));
        $this->SUT->expects($this->once())->method('_getWebHookList')->will($this->returnValue($oWebhookList));
        $this->SUT->expects($this->once())->method('_isInstalled')->will($this->returnValue(true));
        $this->SUT->expects($this->once())->method('_getWebhookUrl')->will($this->returnValue($unExpectedUrl));
        $this->SUT->expects($this->once())->method('_createPaypalWebhook');

        $this->SUT->activate();
    }

    public function testWebHookListContainsShopWebhook_returnsTrueIfDoesContain()
    {
        $expectedUrl = "https://some.domain.com/index.php?cl=somecontroller";

        $oWebhook = $this->getMock('PayPal\Api\Webhook', array('getUrl'));
        $oWebhook->expects($this->any())->method('getUrl')->will($this->returnValue($expectedUrl));

        $oWebhookList = $this->getMock('PayPal\Api\WebhookList', array('getWebhooks'));
        $oWebhookList->expects($this->any())->method('getWebhooks')->will($this->returnValue(array($oWebhook)));

        $this->SUT = $this->getMock(
            'paypPayPalPlusEvents',
            array(
                '__call',
                '_isInstalled',
                '_getWebHookList',
                '_createPaypalWebhook',
                '_getWebhookUrl',
                '_togglePaymentMethod',
                'clearTmp',
                'updateViews',
                '_checkApiSettings',
            ));
        $this->SUT->expects($this->once())->method('_getWebHookList')->will($this->returnValue($oWebhookList));
        $this->SUT->expects($this->once())->method('_isInstalled')->will($this->returnValue(true));
        $this->SUT->expects($this->once())->method('_getWebhookUrl')->will($this->returnValue($expectedUrl));
        $this->SUT->expects($this->never())->method('_createPaypalWebhook');

        $this->SUT->activate();
    }

    /**
     * test `_checkApiSettings` method throught activate when cliend id is empty
     * It throws exception because you can not work with webhooks without API Credentials set
     */
    public function testCreateWebhook_clientIdEmpty_throwsException()
    {
        $this->setExpectedException('oxException');

        $oPayPalPlusConfig = $this->getMock('paypPayPalPlusConfig', array('getClientId', 'getSecret'));
        $oPayPalPlusConfig->expects($this->once())->method('getClientId')->will($this->returnValue(''));
        $oPayPalPlusConfig->expects($this->once())->method('getSecret')->will($this->returnValue('justASecret'));
        oxTestModules::addModuleObject('paypPayPalPlusConfig', $oPayPalPlusConfig);

        $this->SUT = $this->getMock(
            'paypPayPalPlusEvents',
            array(
                '__call',
                '_isInstalled',
                'clearTmp',
                'updateViews',
            ));
        $this->SUT->expects($this->once())->method('_isInstalled')->will($this->returnValue(true));

        $this->SUT->activate();
    }

    /**
     * test `_checkApiSettings` method throught activate when cliend secret is empty
     * It throws exception because you can not work with webhooks without API Credentials set
     */
    public function testCreateWebhook_clientSecretIsEmpty_throwsException()
    {
        $oPayPalPlusConfig = $this->getMock('paypPayPalPlusConfig', array('getClientId', 'getSecret'));
        $oPayPalPlusConfig->expects($this->once())->method('getClientId')->will($this->returnValue('clientId'));
        $oPayPalPlusConfig->expects($this->once())->method('getSecret')->will($this->returnValue(''));
        oxTestModules::addModuleObject('paypPayPalPlusConfig', $oPayPalPlusConfig);

        $this->setExpectedException('oxException');

        $this->SUT = $this->getMock(
            'paypPayPalPlusEvents',
            array(
                '__call',
                '_isInstalled',
                'clearTmp',
                'updateViews',
            ));
        $this->SUT->expects($this->once())->method('_isInstalled')->will($this->returnValue(true));

        $this->SUT->activate();
    }

    public function testCreateWebhook_throwsExceptionIfNoSslUrlIsAvailable()
    {
        $oPayPalPlusConfig = $this->getMock('paypPayPalPlusConfig', array('getClientId', 'getSecret'));
        $oPayPalPlusConfig->expects($this->any())->method('getClientId')->will($this->returnValue('someClientId'));
        $oPayPalPlusConfig->expects($this->any())->method('getSecret')->will($this->returnValue('someSecret'));
        oxTestModules::addModuleObject('paypPayPalPlusConfig', $oPayPalPlusConfig);

        $this->setExpectedException('oxException');

        $oWebhookList = $this->getMock('PayPal\Api\WebhookList', array('getWebhooks'));
        $oWebhookList->expects($this->once())->method('getWebhooks')->will($this->returnValue(false));

        $this->SUT = $this->getMock(
            'paypPayPalPlusEvents',
            array(
                '__call',
                '_isInstalled',
                '_getWebHookList',
                '_togglePaymentMethod',
                'clearTmp',
                'updateViews',
                '_isSslShopUrl',
            ));
        $this->SUT->expects($this->once())->method('_getWebHookList')->will($this->returnValue($oWebhookList));
        $this->SUT->expects($this->once())->method('_isInstalled')->will($this->returnValue(true));
        $this->SUT->expects($this->any())->method('_isSslShopUrl')->will($this->returnValue(false));

        $this->SUT->activate();
    }

    public function testCreateWebhook_throwsExceptionIfSslUrlIsNotAFqdn()
    {
        $this->setExpectedException('InvalidArgumentException');

        $sSslUrl = 'https://';
        $this->setConfigParam('sSSLShopURL', $sSslUrl);
        $oWebhookList = $this->getMock('PayPal\Api\WebhookList', array('getWebhooks'));
        $oWebhookList->expects($this->once())->method('getWebhooks')->will($this->returnValue(false));

        $oWebhook = $this->getMock('PayPal\Api\Webhook', array('create'));
        $oWebhook->expects($this->never())->method('create');

        $oSdk =  $this->getMock('paypPayPalPlusSdk', array('newWebhook'));
        $oSdk->expects($this->once())->method('newWebhook')->will($this->returnValue($oWebhook));

        $this->SUT = $this->getMock(
            'paypPayPalPlusEvents',
            array(
                '__call',
                '_isInstalled',
                '_togglePaymentMethod',
                'clearTmp',
                'updateViews',
                '_getWebHookList',
                'getSdk',
                '_getSubscribedEventTypes',
                '_checkApiSettings',
            ));
        $this->SUT->expects($this->once())->method('_getWebHookList')->will($this->returnValue($oWebhookList));
        $this->SUT->expects($this->once())->method('_isInstalled')->will($this->returnValue(true));
        $this->SUT->expects($this->once())->method('getSdk')->will($this->returnValue($oSdk));

        $this->SUT->activate();
    }

    public function testCreateWebhook_createsWebhookIfSslUrlIsAvailable()
    {
        $sSslUrl = 'https://www.google.com';
        $this->setConfigParam('sSSLShopURL', $sSslUrl);
        $oWebhookList = $this->getMock('PayPal\Api\WebhookList', array('getWebhooks'));
        $oWebhookList->expects($this->once())->method('getWebhooks')->will($this->returnValue(false));

        $oWebhook = $this->getMock('PayPal\Api\Webhook', array('create'));
        $oWebhook->expects($this->once())->method('create')->will($this->returnValue(true));


        $oSdk =  $this->getMock('paypPayPalPlusSdk', array('newWebhook'));
        $oSdk->expects($this->once())->method('newWebhook')->will($this->returnValue($oWebhook));

        $this->SUT = $this->getMock(
            'paypPayPalPlusEvents',
            array(
                '__call',
                '_isInstalled',
                '_togglePaymentMethod',
                'clearTmp',
                'updateViews',
                '_getWebHookList',
                'getSdk',
                '_getSubscribedEventTypes',
                '_checkApiSettings',
            ));
        $this->SUT->expects($this->once())->method('_getWebHookList')->will($this->returnValue($oWebhookList));
        $this->SUT->expects($this->once())->method('_isInstalled')->will($this->returnValue(true));
        $this->SUT->expects($this->once())->method('getSdk')->will($this->returnValue($oSdk));
        $this->SUT->expects($this->once())->method('_getSubscribedEventTypes')->will($this->returnValue(array()));

        $this->SUT->activate();
    }
}
