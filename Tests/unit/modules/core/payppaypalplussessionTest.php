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
 * Class paypPayPalPlusSessionTest
 * Tests for paypPayPalPlusSession
 *
 * @see paypPayPalPlusSession
 */
class paypPayPalPlusSessionTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusSession
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock('paypPayPalPlusSession', array('__call'));
    }


    /**
     * test `setBasketStamp`
     */
    public function testSetBasketStamp()
    {
        $sBasketHash = 'someRandomBasketHash';

        $this->SUT->setBasketStamp($sBasketHash);

        $this->assertSame($sBasketHash, $this->SUT->getBasketStamp());
    }

    /**
     * test `getBasketStamp`
     */
    public function testGetBasketStamp()
    {
        $this->assertFalse($this->SUT->getBasketStamp());
    }

    /**
     * test `setApiContext`
     */
    public function testSetApiContext()
    {
        $oApiContext = new PayPal\Rest\ApiContext();

        $this->SUT->setApiContext($oApiContext);

        $this->assertEquals($oApiContext, $this->SUT->getApiContext());
    }

    /**
     * test `getApiContext`
     */
    public function testGetApiContext()
    {
        $this->assertFalse($this->SUT->getApiContext());
    }

    /**
     * test `setPayment`, payment id is not set so it is also not set to the session
     */
    public function testSetPayment_noPaymentId_paymentIdNotSetInSession()
    {
        $oPayment = new PayPal\Api\Payment();

        $this->SUT->setPayment($oPayment);

        $this->assertEquals($oPayment, $this->SUT->getPayment());
        $this->assertEmpty($this->SUT->getPaymentId());
    }

    /**
     * test `setPayment`, payment id is set so it is also set to the session
     */
    public function testSetPayment_paymentHasId_paymentIdIsSetInSession()
    {
        $sPaymentId = 'testPaymentId';
        $oPayment = new PayPal\Api\Payment();
        $oPayment->setId($sPaymentId);

        $this->SUT->setPayment($oPayment);

        $this->assertEquals($oPayment, $this->SUT->getPayment());
        $this->assertSame($sPaymentId, $this->SUT->getPaymentId());
    }

    /**
     * test `setApprovedPayment`, approved payment id is not set so it is also not set to the session
     */
    public function testSetApprovedPayment_noPaymentId_approvedPaymentIdNotSetInSession()
    {
        $oPayment = new PayPal\Api\Payment();

        $this->SUT->setApprovedPayment($oPayment);

        $this->assertEquals($oPayment, $this->SUT->getApprovedPayment());
        $this->assertEmpty($this->SUT->getApprovedPaymentId());
    }

    /**
     * test `setApprovedPayment`, approved payment id is set so it is also set to the session
     */
    public function testSetApprovedPayment_paymentHasId_approvedPaymentIdIsSetInSession()
    {
        $sPaymentId = 'testPaymentId';
        $oPayment = new PayPal\Api\Payment();
        $oPayment->setId($sPaymentId);

        $this->SUT->setApprovedPayment($oPayment);

        $this->assertEquals($oPayment, $this->SUT->getApprovedPayment());
        $this->assertSame($sPaymentId, $this->SUT->getApprovedPaymentId());
    }

    /**
     * test `unsetApprovedPayment`
     */
    public function testUnsetApprovedPayment_paymentIdIsSetInSession()
    {
        $sPaymentId = 'testPaymentId';
        $oPayment = new PayPal\Api\Payment();
        $oPayment->setId($sPaymentId);

        $this->SUT->setApprovedPayment($oPayment);

        $this->assertEquals($oPayment, $this->SUT->getApprovedPayment());
        $this->assertSame($sPaymentId, $this->SUT->getApprovedPaymentId());

        $this->SUT->unsetApprovedPayment();

        $this->assertFalse($this->SUT->getApprovedPayment());
        $this->assertEmpty($this->SUT->getApprovedPaymentId());
    }

    /**
     * test `getPayment`
     */
    public function testGetPayment()
    {
        $this->assertFalse($this->SUT->getPayment());
    }

    /**
     * test `getPayment`
     */
    public function testGetApprovedPayment()
    {
        $this->assertFalse($this->SUT->getApprovedPayment());
    }

    /**
     * test `getPaymentId`
     */
    public function testGetPaymentId()
    {
        $this->assertEmpty($this->SUT->getPaymentId());
    }

    /**
     * test `getApprovedPaymentId`
     */
    public function testGetApprovedPaymentId()
    {
        $this->assertEmpty($this->SUT->getApprovedPaymentId());
    }

    /**
     * test `setPayerId`
     */
    public function testSetPayerId()
    {
        $sPayerId = 'somePayerId';

        $this->SUT->setPayerId($sPayerId);

        $this->assertSame($sPayerId, $this->SUT->getPayerId());
    }

    /**
     * test `getPayerId`
     */
    public function testGetPayerId()
    {
        $this->assertNull($this->SUT->getPayerId());
    }

    /**
     * test `reset`
     */
    public function testReset()
    {
        $sBasketHash = 'someRandomBasketHash';
        $sPaymentId = 'somePaymentId';
        $sPayerId = 'somePayerId';
        $oApiContext = new PayPal\Rest\ApiContext();
        $oPayment = new PayPal\Api\Payment();
        $oPayment->setId($sPaymentId);

        $this->SUT->setBasketStamp($sBasketHash);
        $this->SUT->setApiContext($oApiContext);
        $this->SUT->setPayment($oPayment);
        $this->SUT->setApprovedPayment($oPayment);
        $this->SUT->setPayerId($sPayerId);

        $this->assertSame($sBasketHash, $this->SUT->getBasketStamp());
        $this->assertEquals($oApiContext, $this->SUT->getApiContext());
        $this->assertEquals($oPayment, $this->SUT->getPayment());
        $this->assertEquals($oPayment, $this->SUT->getApprovedPayment());
        $this->assertSame($sPaymentId, $this->SUT->getPaymentId());
        $this->assertSame($sPaymentId, $this->SUT->getApprovedPaymentId());
        $this->assertSame($sPayerId, $this->SUT->getPayerId());

        $this->SUT->reset();

        $this->assertFalse($this->SUT->getBasketStamp());
        $this->assertFalse($this->SUT->getApiContext());
        $this->assertFalse($this->SUT->getPayment());
        $this->assertFalse($this->SUT->getApprovedPayment());
        $this->assertEmpty($this->SUT->getPaymentId());
        $this->assertEmpty($this->SUT->getApprovedPaymentId());
        $this->assertNull($this->SUT->getPayerId());
    }


    /**
     * test `init`
     */
    public function testInit_emptyApiContext_createsNewToken()
    {
        $sClientId = 'testClientId';
        $sClientSecret = 'testClientSecret';
        $oToken = $this->getMock(
            'PayPal\Auth\OAuthTokenCredential',
            array('getAccessToken'),
            array($sClientId, $sClientSecret)
        );
        $oToken->expects($this->once())->method('getAccessToken');

        $oSdk = $this->getMock('paypPayPalPlusSdk', array('newTokenCredential'));
        $oSdk->expects($this->once())->method('newTokenCredential')->will($this->returnValue($oToken));

        $this->SUT = $this->getMock('paypPayPalPlusSession', array('getSdk'));
        $this->SUT->expects($this->once())->method('getSdk')->will($this->returnValue($oSdk));

        $this->SUT->init();

        $oSetToken = $this->SUT->getApiContext()->getCredential();
        $this->assertEquals($oToken, $oSetToken);
        $this->assertSame($sClientId, $oSetToken->getClientId());
        $this->assertSame($sClientSecret, $oSetToken->getClientSecret());
    }
}
