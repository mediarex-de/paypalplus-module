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
 * Class paypPayPalPlusWebProfileHandlerTest
 * Unit tests for paypPayPalPlusWebProfileHandlerTest helper class.
 *
 * @see paypPayPalPlusRefundHandler
 */
class paypPayPalPlusWebProfileHandlerTest extends OxidTestCase
{
    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusWebProfileHandler|PHPUnit_Framework_MockObject_MockObject
     */
    protected $SUT;

    /** @var \PayPal\Rest\ApiContext|PHPUnit_Framework_MockObject_MockObject $oApiContext */
    protected $oApiContext;

    /** @var \PayPal\Api\WebProfile|PHPUnit_Framework_MockObject_MockObject $oWebProfileMock */
    protected $oWebProfileMock;

    /** @var \PayPal\Api\FlowConfig|PHPUnit_Framework_MockObject_MockObject $oFlowConfigMock */
    protected $oFlowConfigMock;

    /** @var \PayPal\Api\Presentation|PHPUnit_Framework_MockObject_MockObject $oPresentationMock */
    protected $oPresentationMock;

    /** @var \PayPal\Api\InputFields|PHPUnit_Framework_MockObject_MockObject $oInputFieldsMock */
    protected $oInputFieldsMock;

    /** @var paypPayPalPlusSdk|PHPUnit_Framework_MockObject_MockObject $oSdkMock */
    protected $oSdkMock;

    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock('paypPayPalPlusWebProfileHandler', array('__call', 'getSdk'));
        $this->oSdkMock = $this->getMock('paypPayPalPlusSdk', array('newWebProfile', 'newFlowConfig', 'newPresentation', 'newInputFields'));
        $this->oApiContext = $this->getMock('PayPal\Rest\ApiContext');

        $this->oWebProfileMock = $this->getMock('PayPal\Api\WebProfile', array('update', 'create', 'setId', 'setName'));
        $this->oFlowConfigMock = $this->getMock('\PayPal\Api\FlowConfig', array('setFlowConfig'));
        $this->oPresentationMock = $this->getMock('PayPal\Api\Presentation', array('setPresentation', 'setLogoImage', 'setBrandName', 'setLocaleCode'));
        $this->oInputFieldsMock = $this->getMock('PayPal\Api\InputFields', array('setInputFields'));

        $this->oSdkMock->expects($this->any())->method('newWebProfile')->will($this->returnValue($this->oWebProfileMock));
        $this->oSdkMock->expects($this->any())->method('newFlowConfig')->will($this->returnValue($this->oFlowConfigMock));
        $this->oSdkMock->expects($this->any())->method('newPresentation')->will($this->returnValue($this->oPresentationMock));
        $this->oSdkMock->expects($this->any())->method('newInputFields')->will($this->returnValue($this->oInputFieldsMock));

        $this->SUT->expects($this->any())->method('getSdk')->will($this->returnValue($this->oSdkMock));
    }

    public function testUpdate_SuccessCase_returnTrue()
    {
        $this->oPresentationMock->expects($this->once())->method('setBrandName')->with('Brand');
        $this->oPresentationMock->expects($this->once())->method('setLocaleCode')->with('US');
        $this->oPresentationMock->expects($this->once())->method('setLogoImage')->with('image.jpeg');

        $this->oWebProfileMock->expects($this->once())->method('setName')->with('Name');
        $this->oWebProfileMock->expects($this->once())->method('setId')->with('fake_id');

        $aParams['ExpLogo'] = 'image.jpeg';
        $aParams['ExpBrand'] = 'Brand';
        $aParams['ExpLocale'] = 'US';
        $aParams['ExpName'] = 'Name';
        $aParams['ExpProfileId'] = 'fake_id';

        $this->oWebProfileMock->expects($this->once())->method('update')->with($this->oApiContext)->will($this->returnValue(true));
        $this->assertTrue($this->SUT->update($this->oApiContext, $aParams));
    }

    public function testUpdate_throwException()
    {
        $this->oWebProfileMock->expects($this->once())->method('update')->with($this->oApiContext)->will($this->throwException(new PayPal\Exception\PayPalConnectionException(null,null)));
        $this->setExpectedException('PayPal\Exception\PayPalConnectionException');
        $this->SUT->update($this->oApiContext, array());
    }

    public function testCreate_Success_returnWebProfileObject()
    {
        $this->oPresentationMock->expects($this->once())->method('setBrandName')->with('Brand');
        $this->oPresentationMock->expects($this->never())->method('setLocaleCode');
        $this->oPresentationMock->expects($this->once())->method('setLogoImage')->with('image.jpeg');

        $this->oWebProfileMock->expects($this->once())->method('setName')->with('Name');
        $this->oWebProfileMock->expects($this->never())->method('setId');
        $this->oWebProfileMock->expects($this->once())->method('create')->with($this->oApiContext)->will($this->returnValue(new PayPal\Api\CreateProfileResponse()));

        $aParams['ExpLogo'] = 'image.jpeg';
        $aParams['ExpBrand'] = 'Brand';
        $aParams['ExpName'] = 'Name';
        $aParams['ExpProfileId'] = 'fake_id';
        $this->assertInstanceOf('PayPal\Api\CreateProfileResponse', $this->SUT->create($this->oApiContext, $aParams));
    }

    public function testCreate_throwException()
    {
        $this->oWebProfileMock->expects($this->once())->method('create')->with($this->oApiContext)->will($this->throwException(new PayPal\Exception\PayPalConnectionException(null,null)));
        $this->setExpectedException('PayPal\Exception\PayPalConnectionException');
        $this->SUT->create($this->oApiContext, array());
    }
}