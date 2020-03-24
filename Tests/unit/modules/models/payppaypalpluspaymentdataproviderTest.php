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
 * Class paypPayPalPlusPaymentDataProviderTest
 * Tests for paypPayPalPlusPaymentDataProvider model.
 *
 * @see paypPayPalPlusPaymentDataProvider
 */
class paypPayPalPlusPaymentDataProviderTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusPaymentDataProvider
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

        $this->SUT = new paypPayPalPlusPaymentDataProvider();
    }


    /**
     * test `getData`, scenarios are defined in the data provider
     *
     * @param string $sTestCondition
     * @param string $sOrderId
     * @param array  $aPaymentData
     * @param array  $aExpectedReturn
     *
     * @dataProvider paymentDataProvider
     */
    public function testGetData($sTestCondition, $sOrderId, array $aPaymentData, array $aExpectedReturn)
    {
        if ($sOrderId && $aPaymentData) {
            $oOrder = $this->getMock('oxOrder', array('__construct', '__call', 'getId'));
            $oOrder->expects($this->once())->method('getId')->will($this->returnValue($sOrderId));

            $oPayment = $this->_getPaymentObject($aPaymentData);
            $aExpectedReturn['PaymentObject'] = $oPayment;

            $this->SUT->init($oOrder, $oPayment);
        } else {
            $aExpectedReturn['PaymentObject'] = null;
        }

        $this->assertEquals(
            $aExpectedReturn,
            $this->SUT->getData(),
            $sTestCondition
        );
    }

    /**
     * Data provider for testing `getData`
     *
     * @return array
     */
    public function paymentDataProvider()
    {
        return array(
            array(
                'oxOrder and PayPal Payment objects are not initialized',
                '',
                array(),
                array(
                    'PaymentObject' => null,
                    'OrderId'       => '',
                    'SaleId'        => '',
                    'DateCreated'   => '',
                    'Total'         => '0.00',
                    'Currency'      => '',
                    'PaymentId'     => '',
                    'Status'        => '',
                ),
            ),

            array(
                'oxOrder and PayPal Payment objects are initialized',
                'testOrderId',
                array(
                    'saleId'      => 'testSaleId',
                    'dateCreated' => '2011-11-11 11:11:00',
                    'total'       => '11.11',
                    'currency'    => 'LTU',
                    'paymentId'   => 'testPaymentId',
                    'status'      => 'created',
                ),
                array(
                    'OrderId'     => 'testOrderId',
                    'SaleId'      => 'testSaleId',
                    'DateCreated' => '2011-11-11 11:11:00',
                    'Total'       => '11.11',
                    'Currency'    => 'LTU',
                    'PaymentId'   => 'testPaymentId',
                    'Status'      => 'created',
                ),
            ),
        );
    }

    /**
     * test `getDataUtils` in the abstract class
     */
    public function testGetDataUtils()
    {
        $this->assertInstanceOf('paypPayPalPlusDataAccess', $this->SUT->getDataUtils());
    }


    /**
     * test `getConverter` in the abstract class
     */
    public function testGetConverter()
    {
        $this->assertInstanceOf('paypPayPalPlusDataConverter', $this->SUT->getConverter());
    }


    /**
     * test `getFields` in the abstract class
     */
    public function testGetFields()
    {
        $this->assertSame(
            array('OrderId', 'SaleId', 'PaymentObject', 'DateCreated', 'Total', 'Currency', 'PaymentId', 'Status'),
            $this->SUT->getFields()
        );
    }


    /**
     * `__call` should parse getters and return data value where it matches data provider fields.
     */
    public function testMagicCall()
    {
        $oOrder = $this->getMock('oxOrder', array('__construct', '__call', 'getId'));
        $oOrder->expects($this->once())->method('getId')->will($this->returnValue('#ox_order_id-123'));

        $oSale = new PayPal\Api\Sale();
        $oSale->setId('#PP-SALE-ID-123');
        $oSale->setState('completed');

        $oRelatedResource = new PayPal\Api\RelatedResources();
        $oRelatedResource->setSale($oSale);

        $oAmount = new PayPal\Api\Amount();
        $oAmount->setTotal('100.00');
        $oAmount->setCurrency('USD');

        $oTransaction = new PayPal\Api\Transaction();
        $oTransaction->setRelatedResources(array($oRelatedResource));
        $oTransaction->setAmount($oAmount);

        $oPayment = new PayPal\Api\Payment();
        $oPayment->setTransactions(array($oTransaction));
        $oPayment->setCreateTime('2015-02-09 17:28:00');
        $oPayment->setId('PAY-123');

        $this->SUT->init($oOrder, $oPayment);

        $this->assertNull($this->SUT->getMeOrderId());
        $this->assertNull($this->SUT->getNewOrderId());
        $this->assertNull($this->SUT->newOrderId());
        $this->assertNull($this->SUT->OrderId());

        $this->assertSame('#ox_order_id-123', $this->SUT->getOrderId());
        $this->assertSame('#PP-SALE-ID-123', $this->SUT->getSaleId());
        $this->assertInstanceOf('PayPal\Api\Payment', $this->SUT->getPaymentObject());
        $this->assertSame('2015-02-09 17:28:00', $this->SUT->getDateCreated());
        $this->assertSame('100.00', $this->SUT->getTotal());
        $this->assertSame('USD', $this->SUT->getCurrency());
        $this->assertSame('PAY-123', $this->SUT->getPaymentId());
        $this->assertSame('completed', $this->SUT->getStatus());
    }


    /**
     * Form a PayPal Api Payment object from the data array
     *
     * @param array $aPaymentData
     *
     * @return \PayPal\Api\Payment
     */
    protected function _getPaymentObject(array $aPaymentData)
    {
        $oSale = new PayPal\Api\Sale();
        $oSale->setId($aPaymentData['saleId']);
        $oSale->setState($aPaymentData['status']);

        $oRelatedResource = new PayPal\Api\RelatedResources();
        $oRelatedResource->setSale($oSale);

        $oAmount = new PayPal\Api\Amount();
        $oAmount->setTotal($aPaymentData['total']);
        $oAmount->setCurrency($aPaymentData['currency']);

        $oTransaction = new PayPal\Api\Transaction();
        $oTransaction->setRelatedResources(array($oRelatedResource));
        $oTransaction->setAmount($oAmount);

        $oPayment = new PayPal\Api\Payment();
        $oPayment->setTransactions(array($oTransaction));
        $oPayment->setCreateTime($aPaymentData['dateCreated']);
        $oPayment->setId($aPaymentData['paymentId']);

        return $oPayment;
    }
}
