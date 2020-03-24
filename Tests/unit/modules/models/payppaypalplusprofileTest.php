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
 * Class paypPayPalPlusProfileTest
 * Tests for paypPayPalPlusProfile model.
 *
 * @see paypPayPalPlusProfile
 */
class paypPayPalPlusProfileTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusProfile
     */
    protected $SUT;


    /**
     * @inheritDoc
     *
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        modSession::getInstance()->setVar('paypPayPalPlusPaymentId', 'PAY-ID-123');
    }


    /**
     * test `postSave` object passed to the function is an instance of oxUser, but current user is not, nothing is done.
     */
    public function testPostSave_isUserInstance_currentUserIsNotInstanceOfUser_nothingDone()
    {
        $oShop = $this->getMock('paypPayPalPlusShop', array('getUser'));
        $oShop->expects($this->once())->method('getUser')->will($this->returnValue($this));

        $this->SUT = $this->getMock('paypPayPalPlusProfile', array('getShop', '_postSave'));
        $this->SUT->expects($this->once())->method('getShop')->will($this->returnValue($oShop));
        $this->SUT->expects($this->never())->method('_postSave');

        $oUser = $this->getMock('oxUser', array('__construct', '__call'));

        $this->SUT->postSave($oUser);
    }

    /**
     * test `postSave` object passed to the function is an instance of oxUser same as the current user,
     * but their ids do not match, nothing is done.
     */
    public function testPostSave_isUserInstance_currentUserIsInstanceOfUser_idsDoNotMatch_nothingDone()
    {
        $oCurrentUser = $this->getMock('oxUser', array('__construct', '__call', 'getId'));
        $oCurrentUser->expects($this->once())->method('getId')->will($this->returnValue('testUserId'));

        $oShop = $this->getMock('paypPayPalPlusShop', array('getUser'));
        $oShop->expects($this->once())->method('getUser')->will($this->returnValue($oCurrentUser));

        $this->SUT = $this->getMock('paypPayPalPlusProfile', array('getShop'));
        $this->SUT->expects($this->any())->method('getShop')->will($this->returnValue($oShop));

        $oUser = $this->getMock('oxUser', array('getId'));
        $oUser->expects($this->once())->method('getId')->will($this->returnValue('testUserId2'));

        $this->SUT->postSave($oUser);

        $this->assertSame('PAY-ID-123', modSession::getInstance()->getVar('paypPayPalPlusPaymentId'));
    }

    /**
     * test `postSave` object passed to the function is an instance of oxUser same as the current user and
     * their ids do match, post save function is called.
     */
    public function testPostSave_isUserInstance_currentUserIsInstanceOfUser_idsDoMatch_postSaveCalled()
    {
        $oCurrentUser = $this->getMock('oxUser', array('__construct', '__call', 'getId'));
        $oCurrentUser->expects($this->exactly(2))->method('getId')->will($this->returnValue('testUserId'));

        $oShop = $this->getMock('paypPayPalPlusShop', array('getUser'));
        $oShop->expects($this->once())->method('getUser')->will($this->returnValue($oCurrentUser));

        $this->SUT = $this->getMock('paypPayPalPlusProfile', array('getShop'));
        $this->SUT->expects($this->any())->method('getShop')->will($this->returnValue($oShop));

        $this->SUT->postSave($oCurrentUser);

        $this->assertNull(modSession::getInstance()->getVar('paypPayPalPlusPaymentId'));
    }

    /**
     * test `postSave` object passed to the function is an instance of oxAddress,
     * but the current user is not an instance of oxUser, nothing is done.
     */
    public function testPostSave_isAddressInstance_currentUserIsNotInstanceOfUser_nothingDone()
    {
        $oAddress = $this->getMock('oxAddress', array('__construct', '__call'));

        $oShop = $this->getMock('paypPayPalPlusShop', array('getUser'));
        $oShop->expects($this->once())->method('getUser')->will($this->returnValue(false));

        $this->SUT = $this->getMock('paypPayPalPlusProfile', array('getShop'));
        $this->SUT->expects($this->any())->method('getShop')->will($this->returnValue($oShop));

        $this->SUT->postSave($oAddress);

        $this->assertSame('PAY-ID-123', modSession::getInstance()->getVar('paypPayPalPlusPaymentId'));
    }

    /**
     * test `postSave` object passed to the function is an instance of oxAddress, and current user
     * is an instance of oxUser, but session variable `blshowshipaddress` is false, nothing is done.
     */
    public function testPostSave_isAddressInstance_currentUserIsInstanceOfUser_notShowingShipAddress_nothingDone()
    {
        $oAddress = $this->getMock('oxAddress', array('__construct', '__call'));

        $oCurrentUser = $this->getMock('oxUser', array('__construct', '__call'));

        $oShop = $this->getMock('paypPayPalPlusShop', array('getUser', 'getSessionVariable'));
        $oShop->expects($this->once())->method('getUser')->will($this->returnValue($oCurrentUser));
        $oShop->expects($this->once())->method('getSessionVariable')->will($this->returnValue(false));

        $this->SUT = $this->getMock('paypPayPalPlusProfile', array('getShop'));
        $this->SUT->expects($this->any())->method('getShop')->will($this->returnValue($oShop));

        $this->SUT->postSave($oAddress);

        $this->assertSame('PAY-ID-123', modSession::getInstance()->getVar('paypPayPalPlusPaymentId'));
    }

    /**
     * test `postSave` object passed to the function is an instance of oxAddress, and current user
     * is an instance of oxUser, session variable `blshowshipaddress` is true, but shipping address
     * is not an instance of oxAddress, nothing is done.
     */
    public function testPostSave_isAddressInstance_currentUserIsInstanceOfUser_showingShipAddress_shipAddressIsNotInstanceOfAddress_nothingDone()
    {
        $oAddress = $this->getMock('oxAddress', array('__construct', '__call'));

        $oCurrentUser = $this->getMock('oxUser', array('__construct', '__call', 'getSelectedAddress'));
        $oCurrentUser->expects($this->once())->method('getSelectedAddress')->will($this->returnValue($this));

        $oShop = $this->getMock('paypPayPalPlusShop', array('getUser', 'getSessionVariable'));
        $oShop->expects($this->once())->method('getUser')->will($this->returnValue($oCurrentUser));
        $oShop->expects($this->once())->method('getSessionVariable')->will($this->returnValue(true));

        $this->SUT = $this->getMock('paypPayPalPlusProfile', array('getShop'));
        $this->SUT->expects($this->any())->method('getShop')->will($this->returnValue($oShop));

        $this->SUT->postSave($oAddress);

        $this->assertSame('PAY-ID-123', modSession::getInstance()->getVar('paypPayPalPlusPaymentId'));
    }

    /**
     * test `postSave` object passed to the function is an instance of oxAddress, and current user
     * is an instance of oxUser, session variable `blshowshipaddress` is true, shipping address
     * is an instance of oxAddress, but it does not belong to the current user, nothing is done.
     */
    public function testPostSave_isAddressInstance_addressDoesNotBelongToCurrentUser_nothingDone()
    {
        $oAddress = $this->getMock('oxAddress', array('__construct', '__call', 'getId'));
        $oAddress->expects($this->exactly(2))->method('getId')->will($this->onConsecutiveCalls('testAddressId1', 'testAddressId2'));

        $oCurrentUser = $this->getMock('oxUser', array('__construct', '__call', 'getSelectedAddress'));
        $oCurrentUser->expects($this->once())->method('getSelectedAddress')->will($this->returnValue($oAddress));

        $oShop = $this->getMock('paypPayPalPlusShop', array('getUser', 'getSessionVariable'));
        $oShop->expects($this->once())->method('getUser')->will($this->returnValue($oCurrentUser));
        $oShop->expects($this->once())->method('getSessionVariable')->will($this->returnValue(true));

        $this->SUT = $this->getMock('paypPayPalPlusProfile', array('getShop'));
        $this->SUT->expects($this->any())->method('getShop')->will($this->returnValue($oShop));

        $this->SUT->postSave($oAddress);

        $this->assertSame('PAY-ID-123', modSession::getInstance()->getVar('paypPayPalPlusPaymentId'));
    }

    /**
     * test `postSave` object passed to the function is an instance of oxAddress, and current user
     * is an instance of oxUser, session variable `blshowshipaddress` is true, shipping address
     * is an instance of oxAddress and it belongs to the current users, post save function is called.
     */
    public function testPostSave_isAddressInstance_addressBelongsToCurrentUser_postSaveCalled()
    {
        $oAddress = $this->getMock('oxAddress', array('__construct', '__call', 'getId'));
        $oAddress->expects($this->exactly(2))->method('getId')->will($this->returnValue('testAddressId'));

        $oCurrentUser = $this->getMock('oxUser', array('__construct', '__call', 'getSelectedAddress'));
        $oCurrentUser->expects($this->once())->method('getSelectedAddress')->will($this->returnValue($oAddress));

        $oShop = $this->getMock('paypPayPalPlusShop', array('getUser', 'getSessionVariable'));
        $oShop->expects($this->once())->method('getUser')->will($this->returnValue($oCurrentUser));
        $oShop->expects($this->once())->method('getSessionVariable')->will($this->returnValue(true));

        $this->SUT = $this->getMock('paypPayPalPlusProfile', array('getShop'));
        $this->SUT->expects($this->any())->method('getShop')->will($this->returnValue($oShop));

        $this->SUT->postSave($oAddress);

        $this->assertNull(modSession::getInstance()->getVar('paypPayPalPlusPaymentId'));
    }

    /**
     * test `postSave` object passed to the function is not an instance of oxUser or oxAddress, nothing is done.
     */
    public function testPostSave_isNotUserInstance_isNotAddressInstance_nothingDone()
    {
        $this->SUT = new paypPayPalPlusProfile();
        $this->SUT->postSave($this->getMock('oxBase', array('__construct', '__call')));

        $this->assertSame('PAY-ID-123', modSession::getInstance()->getVar('paypPayPalPlusPaymentId'));
    }
}
