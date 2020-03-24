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
 * Class Admin_paypPayPalPlusModule_configTest.
 * Unit tests for admin controller class Admin_paypPayPalPlusModule_configTest.
 *
 * @see Admin_paypPayPalPlusModule_config
 */
class Admin_paypPayPalPlusModule_configTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var Admin_paypPayPalPlusModule_config|PHPUnit_Framework_MockObject_MockObject $SUT
     */
    protected $SUT;

    /**
     * @inheritDoc
     *
     * Set SUT state before test and import test data.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock('Admin_paypPayPalPlusModule_config', array('_returnParent', '_doRequest'));
        modConfig::setRequestParameter('oxid', 'payppaypalplus');
        $this->assertInstanceOf('paypPayPalPlusShop', $this->SUT->_getShop());
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    public function testsaveConfVars_notPaypalPlusModule()
    {
        modConfig::setRequestParameter('oxid', 'none');
        $this->SUT->expects($this->once())->method('_returnParent');
        $this->SUT->saveConfVars();
    }

    public function testsaveConfVars_noParams()
    {
        $this->SUT->expects($this->never())->method('_doRequest');
        $this->SUT->saveConfVars();
    }

    public function testsaveConfVars_retrieveProfileFound()
    {
        $aParams['paypPayPalPlusExpProfileId'] = 'fake_id';
        modConfig::setRequestParameter('confstrs', $aParams);

        $oPresentation = $this->getMock('\PayPal\Api\Presentation', array('getBrandName', 'getLogoImage', 'getLocaleCode'));
        $oPresentation->expects($this->once())->method('getBrandName');
        $oPresentation->expects($this->once())->method('getLogoImage');
        $oPresentation->expects($this->once())->method('getLocaleCode');

        $oWebProfile = $this->getMock('WebProfile', array('getPresentation', 'getName'));
        $oWebProfile->expects($this->exactly(4))->method('getPresentation')->willReturn($oPresentation);
        $oWebProfile->expects($this->once())->method('getName');

        $this->SUT->expects($this->once())->method('_doRequest')->with('get', 'fake_id')->willReturn($oWebProfile);

        $this->SUT->saveConfVars();
    }

    public function testsaveConfVars_retrieveProfileNotFound()
    {
        $aParams['paypPayPalPlusExpProfileId'] = 'fake_id';
        modConfig::setRequestParameter('confstrs', $aParams);

        $this->SUT->expects($this->once())->method('_doRequest')->with('get', 'fake_id')->willReturn(false);

        $this->SUT->saveConfVars();
    }

    public function testsaveConfVars_updateProfile_invalidValues()
    {
        $aParams['paypPayPalPlusExpProfileId'] = 'fake_id';
        $aParams['paypPayPalPlusExpLogo'] = 'logo';
        modConfig::setRequestParameter('confstrs', $aParams);

        $this->SUT->expects($this->never())->method('_doRequest');

        $this->SUT->saveConfVars();
    }

    public function testsaveConfVars_updateProfile_success()
    {
        $aParams['paypPayPalPlusExpProfileId'] = 'fake_id';
        $aParams['paypPayPalPlusExpName'] = 'Name';
        modConfig::setRequestParameter('confstrs', $aParams);

        $aParams = array(
            'ExpProfileId'=>'fake_id',
            'ExpName'=> 'Name',
            'ExpBrand' => null,
            'ExpLogo' => null,
            'ExpLocale' => null,
        );
        $this->SUT->expects($this->once())->method('_doRequest')->with('update', $aParams)->willReturn(true);

        $this->SUT->saveConfVars();
    }

    public function testsaveConfVars_updateProfile_failed()
    {
        $aParams['paypPayPalPlusExpProfileId'] = 'fake_id';
        $aParams['paypPayPalPlusExpName'] = 'Name';
        modConfig::setRequestParameter('confstrs', $aParams);

        $aParams = array(
            'ExpProfileId'=>'fake_id',
            'ExpName'=> 'Name',
            'ExpBrand' => null,
            'ExpLogo' => null,
            'ExpLocale' => null,
        );
        $this->SUT->expects($this->once())->method('_doRequest')->with('update', $aParams)->willReturn(false);

        $this->SUT->saveConfVars();
    }

    public function testsaveConfVars_createProfile_invalidValues()
    {
        $aParams['paypPayPalPlusExpLogo'] = 'logo';
        modConfig::setRequestParameter('confstrs', $aParams);

        $this->SUT->expects($this->never())->method('_doRequest');

        $this->SUT->saveConfVars();
    }

    public function testsaveConfVars_createProfile_success()
    {
        $aParams['paypPayPalPlusExpName'] = 'Name';
        modConfig::setRequestParameter('confstrs', $aParams);

        $oWebProfile = $this->getMock('WebProfile', array('getId'));
        $oWebProfile->expects($this->once())->method('getId');

        $aParams = array(
            'ExpProfileId' => null,
            'ExpName'=> 'Name',
            'ExpBrand' => null,
            'ExpLogo' => null,
            'ExpLocale' => null,
        );
        $this->SUT->expects($this->once())->method('_doRequest')->with('create', $aParams)->willReturn($oWebProfile);

        $this->SUT->saveConfVars();
    }

    public function testsaveConfVars_createProfile_failed()
    {
        $aParams['paypPayPalPlusExpName'] = 'Name';
        modConfig::setRequestParameter('confstrs', $aParams);

        $oWebProfile = $this->getMock('WebProfile', array('getId'));
        $oWebProfile->expects($this->never())->method('getId');

        $aParams = array(
            'ExpProfileId' => null,
            'ExpName'=> 'Name',
            'ExpBrand' => null,
            'ExpLogo' => null,
            'ExpLocale' => null,
        );

        $this->SUT->expects($this->once())->method('_doRequest')->with('create', $aParams)->willReturn(false);

        $this->SUT->saveConfVars();
    }

    public function testSetErrorMessage(){
        $oError = new \stdClass();
        $oError->message = 'Error title';
        $oItem = new \stdClass();
        $oItem->issue = 'Error description';
        $oError->details[] = $oItem;
        $this->invokeMethod($this->SUT, '_setErrorMessage', array($oError));

        $this->assertSame('<br/>Error title:<br/>Error description',  $this->SUT->getViewDataElement('payppaypalpluserror'));
    }

}