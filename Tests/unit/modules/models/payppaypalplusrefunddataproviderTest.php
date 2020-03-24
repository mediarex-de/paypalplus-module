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
 * Class paypPayPalPlusRefundDataProviderTest
 * Integration tests for paypPayPalPlusRefundDataProvider model.
 *
 * @see paypPayPalPlusRefundDataProvider
 */
class paypPayPalPlusRefundDataProviderTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusRefundDataProvider
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = new paypPayPalPlusRefundDataProvider();
    }


    public function testGetDataUtils()
    {
        $this->assertInstanceOf('paypPayPalPlusDataAccess', $this->SUT->getDataUtils());
    }


    public function testGetConverter()
    {
        $this->assertInstanceOf('paypPayPalPlusDataConverter', $this->SUT->getConverter());
    }


    public function testGetFields()
    {
        $this->assertSame(
            array('SaleId', 'RefundId', 'Status', 'DateCreated', 'Total', 'Currency', 'RefundObject'),
            $this->SUT->getFields()
        );
    }


    public function testGetData_dataProviderNotInitializedWithPayPalApiRefundObject_returnAnArrayWithEmptyValues()
    {
        $this->assertSame(
            array(
                'SaleId'       => '',
                'RefundId'     => '',
                'Status'       => '',
                'DateCreated'  => '',
                'Total'        => '0.00',
                'Currency'     => '',
                'RefundObject' => null,
            ),
            $this->SUT->getData()
        );
    }

    public function testGetData_dataProviderIsInitializedWithPayPalApiRefundObject_returnAnArrayWithMappedValues()
    {
        $oAmount = new PayPal\Api\Amount();
        $oAmount->setTotal(99.99999999);
        $oAmount->setCurrency('EUR');

        $oRefund = new PayPal\Api\Refund();
        $oRefund->setSaleId('sale-ID');
        $oRefund->setId('refund-ID');
        $oRefund->setState('completed');
        $oRefund->setCreateTime('2015-01-28 18:05:01');
        $oRefund->setAmount($oAmount);

        $this->SUT->init($oRefund);
        $this->assertSame(
            array(
                'SaleId'       => 'sale-ID',
                'RefundId'     => 'refund-ID',
                'Status'       => 'completed',
                'DateCreated'  => '2015-01-28 18:05:01',
                'Total'        => '100.00',
                'Currency'     => 'EUR',
                'RefundObject' => $oRefund,
            ),
            $this->SUT->getData()
        );
    }


    public function testMagicGetters()
    {
        $oAmount = new PayPal\Api\Amount();
        $oAmount->setTotal(50);
        $oAmount->setCurrency('USD');

        $oRefund = new PayPal\Api\Refund();
        $oRefund->setSaleId('sale-ID-123');
        $oRefund->setId('refund-ID-123');
        $oRefund->setState('completed');
        $oRefund->setCreateTime('2015-01-28 18:05:02');
        $oRefund->setAmount($oAmount);

        $this->SUT->init($oRefund);

        $this->assertNull($this->SUT->faultyCall());
        $this->assertNull($this->SUT->getNotExistingField());

        $this->assertSame('sale-ID-123', $this->SUT->getSaleId());
        $this->assertSame('refund-ID-123', $this->SUT->getRefundId());
        $this->assertSame('completed', $this->SUT->getStatus());
        $this->assertSame('2015-01-28 18:05:02', $this->SUT->getDateCreated());
        $this->assertSame('50.00', $this->SUT->getTotal());
        $this->assertSame('USD', $this->SUT->getCurrency());
        $this->assertSame($oRefund, $this->SUT->getRefundObject());
    }
}
