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
 * Class paypPayPalPlusRefundDataTest
 * Tests for paypPayPalPlusRefundData model.
 *
 * @see paypPayPalPlusRefundData
 */
class paypPayPalPlusRefundDataTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusRefundData
     */
    protected $SUT;


    /**
     * @inheritDoc
     *
     * Set SUT state before test.
     * Import data to test loading methods
     */
    public function setUp()
    {
        parent::setUp();

        importTestdataFile("removeRefundData.sql");
        importTestdataFile("addRefundData.sql");

        $this->SUT = $this->getMock('paypPayPalPlusRefundData', array('__call'));
    }

    /**
     * @inheritDoc
     *
     * Remove test data
     */
    public function tearDown()
    {
        parent::tearDown();

        importTestdataFile("removeRefundData.sql");
    }


    /**
     * test `setSaleId`
     */
    public function testSetSaleId()
    {
        $sSaleId = 'testSaleId';
        $this->SUT->setSaleId($sSaleId);

        $this->assertSame($sSaleId, $this->SUT->getSaleId());
    }

    /**
     * test `getSaleId`
     */
    public function testGetSaleId()
    {
        $this->assertNull($this->SUT->getSaleId());
    }

    /**
     * test `setRefundId`
     */
    public function testSetRefundId()
    {
        $sRefundId = 'testRefundId';
        $this->SUT->setRefundId($sRefundId);

        $this->assertSame($sRefundId, $this->SUT->getRefundId());
    }

    /**
     * test `getRefundId`
     */
    public function testGetRefundId()
    {
        $this->assertNull($this->SUT->getRefundId());
    }

    /**
     * test `setStatus`
     */
    public function testSetStatus()
    {
        $sStatus = 'testStatus';
        $this->SUT->setStatus($sStatus);

        $this->assertSame($sStatus, $this->SUT->getStatus());
    }

    /**
     * test `getStatus`
     */
    public function testGetStatus()
    {
        $this->assertNull($this->SUT->getStatus());
    }

    /**
     * test `setDateCreated`
     */
    public function testSetDateCreated()
    {
        $sDateCreated = '2011-11-11 11:11:11';
        $this->SUT->setDateCreated($sDateCreated);

        $this->assertSame($sDateCreated, $this->SUT->getDateCreated());
    }

    /**
     * test `getDateCreated`
     */
    public function testGetDateCreated()
    {
        $this->assertNull($this->SUT->getDateCreated());
    }

    /**
     * test `setTotal`
     */
    public function testSetTotal()
    {
        $sTotal = '999.99';
        $this->SUT->setTotal($sTotal);

        $this->assertEquals($sTotal, $this->SUT->getTotal());
    }

    /**
     * test `getTotal`
     */
    public function testGetTotal()
    {
        $this->assertNull($this->SUT->getTotal());
    }

    /**
     * test `setCurrency`
     */
    public function testSetCurrency()
    {
        $sCurrency = 'testCurrency';
        $this->SUT->setCurrency($sCurrency);

        $this->assertSame($sCurrency, $this->SUT->getCurrency());
    }

    /**
     * test `getCurrency`
     */
    public function testGetCurrency()
    {
        $this->assertNull($this->SUT->getCurrency());
    }

    /**
     * test `setRefundObject`
     */
    public function testSetRefundObject()
    {
        $sOxid = md5(time());
        $oRefund = new PayPal\Api\Refund();
        $oRefund->setId('testRefundId');
        $this->SUT->setId($sOxid);
        $this->SUT->setRefundObject($oRefund);
        $this->SUT->save();

        $this->SUT->load($sOxid);
        $this->assertEquals($oRefund, $this->SUT->getRefundObject());
    }

    /**
     * test `getRefundObject`
     */
    public function testGetRefundObject()
    {
        $this->assertNull($this->SUT->getRefundObject());
    }

    /**
     * test `loadByRefundId` using integrational testing. Refund found, returning true.
     */
    public function testLoadByRefundId_refundExists_returnTrue()
    {
        $this->assertTrue($this->SUT->loadByRefundId('testRefundId1'));
        $this->assertNotNull($this->SUT->getId());
    }

    /**
     * test `loadByRefundId` using integrational testing. Refund not found, returning false.
     */
    public function testLoadByRefundId_refundDoesNotExist_returnFalse()
    {
        $this->assertFalse($this->SUT->loadByRefundId('testRefundNotExistingId'));
        $this->assertNull($this->SUT->getId());
    }

    /**
     * test `deleteBySaleId`, integration test to check if data is deleted successfully
     *
     * There are 3 records with OXIDs(`testRefundOXID1`, `testRefundOXID2`, `testRefundOXID3`)
     * with saleId `testRefundSaleId` added during setUp process
     */
    public function testDeleteBySaleId()
    {
        $sCountQuery = sprintf(
            "SELECT COUNT(OXID) FROM %s WHERE OXID IN ('testRefundOXID1', 'testRefundOXID2', 'testRefundOXID3')",
            $this->SUT->getCoreTableName()
        );

        $this->assertEquals(3, paypPayPalPlusShop::getShop()->getDb()->getOne($sCountQuery));

        $this->SUT->deleteBySaleId('testRefundSaleId');

        $this->assertEquals(0, paypPayPalPlusShop::getShop()->getDb()->getOne($sCountQuery));
    }
}
