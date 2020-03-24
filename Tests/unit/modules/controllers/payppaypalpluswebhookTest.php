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
 * Class paypPayPalPlusWebhookTest
 * Tests for controller class paypPayPalPlusWebhook.
 *
 * @see paypPayPalPlusWebhook
 */
class paypPayPalPlusWebhookTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusWebhook $SUT
     */
    protected $SUT;

    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock(
            'paypPayPalPlusWebhook',
            array('__call',
                '_paypPayPalPlusWebhook_render_parent'
            )
        );
    }

    /**
     *
     */
    public function testRender_callsParentFunction()
    {
        $this->SUT = $this->getMock(
            'paypPayPalPlusWebhook',
            array('__call',
                '_paypPayPalPlusWebhook_render_parent',
                '_sendResponseHeader',
            )
        );

        $this->SUT->expects($this->once())->method('_paypPayPalPlusWebhook_render_parent')->will(
            $this->returnValue(true)
        );
        $this->assertTrue($this->SUT->render());
    }

    /**
     * test `getShop`. Always returns an instance of paypPayPalPlusSuperCfg
     */
    public function testGetPayPalPlusSuperCfg_returnspaypPayPalPlusSuperCfg()
    {
        $this->assertInstanceOf('paypPayPalPlusSuperCfg', $this->SUT->getPayPalPlusSuperCfg());
    }

    /**
     * test `getShop`. Always returns an instance of paypPayPalPlusShop
     */
    public function testGetShop_returnspaypPayPalPlusShopInstance()
    {
        $this->assertInstanceOf('paypPayPalPlusShop', $this->SUT->getShop());
    }

    /**
     *
     */
    public function testRender_sendsHttp503Status_onNoBodyData()
    {

        $this->SUT = $this->getMock(
            'paypPayPalPlusWebhook',
            array('__call',
                '_paypPayPalPlusWebhook_render_parent',
                '_throwInvalidArgumentException',
                '_sendResponseHeader',
            )
        );

        $this->SUT->expects($this->once())->method('_throwInvalidArgumentException')->will($this->throwException(new Exception()));
        $this->SUT->expects($this->once())->method('_sendResponseHeader')->with(paypPayPalPlusWebhook::HTTP_HEADER_503);
        $this->SUT->expects($this->once())->method('_paypPayPalPlusWebhook_render_parent')->will(
            $this->returnValue(true)
        );
        $this->SUT->expects($this->once())->method('_paypPayPalPlusWebhook_render_parent')->will(
            $this->returnValue(true)
        );
        $this->assertTrue($this->SUT->render());
    }

    /**
     *
     */
    public function testRender_sendsHttp503Status_onFakedBodyData()
    {
        $this->SUT = $this->getMock(
            'paypPayPalPlusWebhook',
            array('__call',
                '_paypPayPalPlusWebhook_render_parent',
                '_getRequestBody',
                '_throwPayPalConnectionException',
                '_sendResponseHeader',
            )
        );

        $this->SUT->expects($this->once())->method('_getRequestBody')->will($this->returnValue($this->_getRequestBody()));
        $this->SUT->expects($this->once())->method('_sendResponseHeader')->with(paypPayPalPlusWebhook::HTTP_HEADER_503);
        $this->SUT->expects($this->once())->method('_paypPayPalPlusWebhook_render_parent')->will(
            $this->returnValue(true)
        );
        $this->assertTrue($this->SUT->render());
    }

    /**
     *
     */
    public function testRender_getPaymentIdWillReturnExpectedValue()
    {
        $this->SUT = $this->getMock(
            'paypPayPalPlusWebhook',
            array('__call',
                '_paypPayPalPlusWebhook_render_parent',
                '_getWebhookEvent',
                '_throwPayPalConnectionException',
                '_getPaymentDataModel',
                '_throwNoPaymentFoundException',
                '_sendResponseHeader',
            )
        );

        $oPaymentDataInstanceMock = $this->getMock(
            'paypPayPalPlusPaymentData', array(
                'loadByPaymentId',
                'isLoaded',
                'setStatusByEventType',
                'save',
            )
        );
        $oPaymentDataInstanceMock->expects($this->once())->method('loadByPaymentId')->with('testPaymentId');
        $oPaymentDataInstanceMock->expects($this->any())->method('isLoaded')->will($this->returnValue(false));
        $oPaymentDataInstanceMock->expects($this->never())->method('setStatusByEventType');
        $oPaymentDataInstanceMock->expects($this->never())->method('save');

        $this->SUT->expects($this->once())->method('_getWebhookEvent')->will($this->returnValue($this->_getWebhookEvent()));
        $this->SUT->expects($this->never())->method('_throwPayPalConnectionException');
        $this->SUT->expects($this->once())->method('_getPaymentDataModel')->will($this->returnValue($oPaymentDataInstanceMock));
        $this->SUT->expects($this->once())->method('_throwNoPaymentFoundException')->will($this->throwException(new Exception()));
        $this->SUT->expects($this->once())->method('_sendResponseHeader')->with(paypPayPalPlusWebhook::HTTP_HEADER_503);
        $this->SUT->expects($this->once())->method('_paypPayPalPlusWebhook_render_parent')->will(
            $this->returnValue(true)
        );
        $this->assertTrue($this->SUT->render());
    }

    /**
     *
     */
    public function testRender_throwsNoPaymentFoundException_onNoPaymentNoLoaded()
    {
        $this->SUT = $this->getMock(
            'paypPayPalPlusWebhook',
            array('__call',
                '_paypPayPalPlusWebhook_render_parent',
                '_getWebhookEvent',
                '_throwPayPalConnectionException',
                '_getPaymentDataModel',
                '_throwNoPaymentFoundException',
                '_sendResponseHeader',
            )
        );

        $oPaymentDataInstanceMock = $this->getMock(
            'paypPayPalPlusPaymentData', array(
                'setStatusByEventType',
                'save',
                'isLoaded')
        );
        $oPaymentDataInstanceMock->expects($this->once())->method('isLoaded')->will($this->returnValue(false));
        $oPaymentDataInstanceMock->expects($this->never())->method('setStatusByEventType');
        $oPaymentDataInstanceMock->expects($this->never())->method('save');

        $this->SUT->expects($this->once())->method('_getWebhookEvent')->will($this->returnValue($this->_getWebhookEvent()));
        $this->SUT->expects($this->never())->method('_throwPayPalConnectionException');
        $this->SUT->expects($this->once())->method('_getPaymentDataModel')->will($this->returnValue($oPaymentDataInstanceMock));
        $this->SUT->expects($this->once())->method('_throwNoPaymentFoundException')->will($this->throwException(new Exception()));
        $this->SUT->expects($this->once())->method('_sendResponseHeader')->with(paypPayPalPlusWebhook::HTTP_HEADER_503);
        $this->SUT->expects($this->once())->method('_paypPayPalPlusWebhook_render_parent')->will(
            $this->returnValue(true)
        );
        $this->assertTrue($this->SUT->render());
    }

    public function testRender_throwsPaymentDataSaveException_onPaymentDataNotSaved() {
        $this->SUT = $this->getMock(
            'paypPayPalPlusWebhook',
            array('__call',
                '_paypPayPalPlusWebhook_render_parent',
                '_getWebhookEvent',
                '_throwPayPalConnectionException',
                '_getPaymentDataModel',
                '_throwNoPaymentFoundException',
                '_throwPaymentDataSaveException',
                '_sendResponseHeader',
                '_setViewData',
            )
        );

        $oPaymentDataInstanceMock = $this->getMock(
            'paypPayPalPlusPaymentData', array(
                'loadByPaymentId',
                'isLoaded',
                'setStatusByEventType',
                'save',
            )
        );
        $oPaymentDataInstanceMock->expects($this->once())->method('loadByPaymentId')->with('testPaymentId');
        $oPaymentDataInstanceMock->expects($this->any())->method('isLoaded')->will($this->returnValue(true));
        $oPaymentDataInstanceMock->expects($this->once())->method('setStatusByEventType');
        $oPaymentDataInstanceMock->expects($this->once())->method('save')->will($this->returnValue(false));

        $this->SUT->expects($this->once())->method('_getWebhookEvent')->will($this->returnValue($this->_getWebhookEvent()));
        $this->SUT->expects($this->never())->method('_throwPayPalConnectionException');
        $this->SUT->expects($this->once())->method('_getPaymentDataModel')->will($this->returnValue($oPaymentDataInstanceMock));
        $this->SUT->expects($this->never())->method('_throwNoPaymentFoundException');
        $this->SUT->expects($this->once())->method('_throwPaymentDataSaveException')->will($this->throwException(new Exception()));
        $this->SUT->expects($this->once())->method('_sendResponseHeader')->with(paypPayPalPlusWebhook::HTTP_HEADER_503);
        $this->SUT->expects($this->once())->method('_setViewData');
        $this->SUT->expects($this->once())->method('_paypPayPalPlusWebhook_render_parent')->will(
            $this->returnValue(true)
        );
        $this->assertTrue($this->SUT->render());
    }

    /**
     *
     */
    public function testRender_sends202Header_onSuccess()
    {
        $this->SUT = $this->getMock(
            'paypPayPalPlusWebhook',
            array('__call',
                '_paypPayPalPlusWebhook_render_parent',
                '_getWebhookEvent',
                '_throwPayPalConnectionException',
                '_getPaymentDataModel',
                '_throwNoPaymentFoundException',
                '_sendResponseHeader',
                '_setViewData',
            )
        );

        $oPaymentDataInstanceMock = $this->getMock(
            'paypPayPalPlusPaymentData', array(
                'loadByPaymentId',
                'isLoaded',
                'setStatusByEventType',
                'save',
            )
        );
        $oPaymentDataInstanceMock->expects($this->once())->method('loadByPaymentId')->with('testPaymentId');
        $oPaymentDataInstanceMock->expects($this->any())->method('isLoaded')->will($this->returnValue(true));
        $oPaymentDataInstanceMock->expects($this->once())->method('setStatusByEventType');
        $oPaymentDataInstanceMock->expects($this->once())->method('save')->will($this->returnValue(true));

        $this->SUT->expects($this->once())->method('_getWebhookEvent')->will($this->returnValue($this->_getWebhookEvent()));
        $this->SUT->expects($this->never())->method('_throwPayPalConnectionException');
        $this->SUT->expects($this->once())->method('_getPaymentDataModel')->will($this->returnValue($oPaymentDataInstanceMock));
        $this->SUT->expects($this->never())->method('_throwNoPaymentFoundException');
        $this->SUT->expects($this->once())->method('_sendResponseHeader')->with(paypPayPalPlusWebhook::HTTP_HEADER_202);
        $this->SUT->expects($this->once())->method('_setViewData');
        $this->SUT->expects($this->once())->method('_paypPayPalPlusWebhook_render_parent')->will(
            $this->returnValue(true)
        );
        $this->assertTrue($this->SUT->render());
    }

    public function testIsSimulationMode_returnsTrue_onDebugAndSimulate () {
        $this->setConfigParam('iDebug', 1);
        $this->setRequestParam('simulate', '1');
        $this->assertTrue($this->SUT->isSimulationMode());
    }

    public function testIsSimulationMode_returnsFalse_onNotDebug () {
        $this->setConfigParam('iDebug', 0);
        $this->setRequestParam('simulate', '1');
        $this->assertFalse($this->SUT->isSimulationMode());
    }

    public function testIsSimulationMode_returnsFalse_onNotSimulate () {
        $this->setConfigParam('iDebug', 1);
        $this->setRequestParam('simulate', '0');
        $this->assertFalse($this->SUT->isSimulationMode());
    }

    public function testGetWebhookEvent_validatesRequest_onSimulationOff () {
        $this->setConfigParam('iDebug', 0);
        $this->setRequestParam('simulate', '0');

        $sRequestBody = $this->_getRequestBody();
        $blValidateRequest = true;

        $this->SUT = $this->getMock(
            'paypPayPalPlusWebhook',
            array('__call',
                '_paypPayPalPlusWebhook_render_parent',
                '_getRequestBody',
                '_getWebhookEvent',
                '_throwPayPalConnectionException',
                '_getPaymentDataModel',
                '_throwNoPaymentFoundException',
                '_sendResponseHeader',
                '_setViewData',
            )
        );

        $oPaymentDataInstanceMock = $this->getMock(
            'paypPayPalPlusPaymentData', array(
                'loadByPaymentId',
                'isLoaded',
                'setStatusByEventType',
                'save',
            )
        );
        $oPaymentDataInstanceMock->expects($this->once())->method('loadByPaymentId')->with('testPaymentId');
        $oPaymentDataInstanceMock->expects($this->any())->method('isLoaded')->will($this->returnValue(true));
        $oPaymentDataInstanceMock->expects($this->once())->method('setStatusByEventType');
        $oPaymentDataInstanceMock->expects($this->once())->method('save')->will($this->returnValue(true));

        $this->SUT->expects($this->once())->method('_getRequestBody')->will($this->returnValue($sRequestBody));
        $this->SUT->expects($this->once())->method('_getWebhookEvent')->with($sRequestBody, $blValidateRequest)->will($this->returnValue($this->_getWebhookEvent()));
        $this->SUT->expects($this->never())->method('_throwPayPalConnectionException');
        $this->SUT->expects($this->once())->method('_getPaymentDataModel')->will($this->returnValue($oPaymentDataInstanceMock));
        $this->SUT->expects($this->never())->method('_throwNoPaymentFoundException');
        $this->SUT->expects($this->once())->method('_sendResponseHeader')->with(paypPayPalPlusWebhook::HTTP_HEADER_202);
        $this->SUT->expects($this->once())->method('_setViewData');
        $this->SUT->expects($this->once())->method('_paypPayPalPlusWebhook_render_parent')->will(
            $this->returnValue(true)
        );
        $this->assertTrue($this->SUT->render());
    }

    public function testGetWebhookEvent_doesNotValidateRequest_onSimulationOn () {
        $this->setConfigParam('iDebug', 1);
        $this->setRequestParam('simulate', '1');

        $sRequestBody = $this->_getRequestBody();
        $blValidateRequest = false;

        $this->SUT = $this->getMock(
            'paypPayPalPlusWebhook',
            array('__call',
                '_paypPayPalPlusWebhook_render_parent',
                '_getRequestBody',
                '_getWebhookEvent',
                '_throwPayPalConnectionException',
                '_getPaymentDataModel',
                '_throwNoPaymentFoundException',
                '_sendResponseHeader',
                '_setViewData',
            )
        );

        $oPaymentDataInstanceMock = $this->getMock(
            'paypPayPalPlusPaymentData', array(
                'loadByPaymentId',
                'isLoaded',
                'setStatusByEventType',
                'save',
            )
        );
        $oPaymentDataInstanceMock->expects($this->once())->method('loadByPaymentId')->with('testPaymentId');
        $oPaymentDataInstanceMock->expects($this->any())->method('isLoaded')->will($this->returnValue(true));
        $oPaymentDataInstanceMock->expects($this->once())->method('setStatusByEventType');
        $oPaymentDataInstanceMock->expects($this->once())->method('save')->will($this->returnValue(true));

        $this->SUT->expects($this->once())->method('_getRequestBody')->will($this->returnValue($sRequestBody));
        $this->SUT->expects($this->once())->method('_getWebhookEvent')->with($sRequestBody, $blValidateRequest)->will($this->returnValue($this->_getWebhookEvent()));
        $this->SUT->expects($this->never())->method('_throwPayPalConnectionException');
        $this->SUT->expects($this->once())->method('_getPaymentDataModel')->will($this->returnValue($oPaymentDataInstanceMock));
        $this->SUT->expects($this->never())->method('_throwNoPaymentFoundException');
        $this->SUT->expects($this->once())->method('_sendResponseHeader')->with(paypPayPalPlusWebhook::HTTP_HEADER_202);
        $this->SUT->expects($this->once())->method('_setViewData');
        $this->SUT->expects($this->once())->method('_paypPayPalPlusWebhook_render_parent')->will(
            $this->returnValue(true)
        );
        $this->assertTrue($this->SUT->render());
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getPaymentDataInstanceMockBuilder()
    {
        return $this->getMock('paypPayPalPlusPaymentData');
    }

    /**
     * Returns a sample request body for a event sent to a webhook
     */
    protected function _getRequestBody()
    {
        $sFilePath = getTestsBasePath() . '/unit/testdata/WebhookEvent.json';
        $sRequestBody = file_get_contents($sFilePath);

        return $sRequestBody;
    }

    /**
     * Returns a sample event for a event sent to a webhook
     */
    protected function _getWebhookEvent()
    {
        $oWebhookEvent = new PayPal\Api\WebhookEvent();
        $oWebhookEvent->fromJson($this->_getRequestBody());

        return $oWebhookEvent;
    }


}