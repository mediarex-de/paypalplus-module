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
 * Class paypPayPalPlusRefundDataListTest
 * Tests for paypPayPalPlusRefundDataList model.
 *
 * @see paypPayPalPlusRefundDataList
 */
class paypPayPalPlusRefundDataListTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusRefundDataList
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

        $this->SUT = $this->getMock('paypPayPalPlusRefundDataList', array('__call'));
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
     * test `loadRefundsBySaleId`. This is integration test to check if data is loaded successfully with existing saleId
     * There are 3 records with OXIDs(`testRefundOXID1`, `testRefundOXID2`, `testRefundOXID3`)
     * with saleId `testRefundSaleId` added during setUp process
     */
    public function testLoadRefundsBySaleId_refundsExist_refundsLoaded()
    {
        $this->SUT->loadRefundsBySaleId('testRefundSaleId');

        $this->assertSame(3, $this->SUT->count());
    }

    /**
     * test `loadRefundsBySaleId`. This is integration test to check if data is loaded successfully with not existing saleId
     */
    public function testLoadRefundsBySaleId_refundsDoNotExist_refundsNotLoaded()
    {
        $this->SUT->loadRefundsBySaleId('testRefundNotExistingSaleId');

        $this->assertSame(0, $this->SUT->count());
    }


    /**
     * test `getRefundedSumBySaleId`. This is integration test to check if refunds sum is loaded correctly from the database.
     * Refunds are found and the amount is added up.
     */
    public function testGetRefundedSumBySaleId__refundsExist_refundsSumReturned()
    {
        $this->assertSame(9.0, $this->SUT->getRefundedSumBySaleId('testRefundSaleId'));
    }

    /**
     * test `getRefundedSumBySaleId`. This is integration test to check if refunds sum is loaded correctly from the database.
     * Refunds are not found and the amount returned is 0.0
     */
    public function testGetRefundedSumBySaleId__refundsDoNotExist_refundsSumReturned()
    {
        $this->assertSame(0.0, $this->SUT->getRefundedSumBySaleId('testRefundNotExistingSaleId'));
    }
}
