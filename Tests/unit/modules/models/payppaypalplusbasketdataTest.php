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
 * Class paypPayPalPlusBasketDataTest
 * Tests for paypPayPalPlusBasketData model.
 *
 * @see paypPayPalPlusBasketData
 */
class paypPayPalPlusBasketDataTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusBasketData
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $oShop = $this->getMock('paypPayPalPlusShop', array('getBasket'));

        $this->SUT = $this->getMock('paypPayPalPlusBasketData', array('getShop'));
        $this->SUT->expects($this->any())->method('getShop')->will($this->returnValue($oShop));
    }


    /**
     * Test `getData` with data provider
     *
     * @param string $sTestConditions
     * @param array  $aBasketMockData
     * @param array  $aExpectedReturn
     *
     * @dataProvider basketDataProvider
     */
    public function testGetData($sTestConditions, array $aBasketMockData, array $aExpectedReturn)
    {
        $oBasket = $this->getMock(
            'oxBasket',
            array(
                '__construct', '__call',
                'getPrice', 'getBasketCurrency', 'getCosts', 'getTotalDiscountSum', 'getContents'
            )
        );
        $oBasket->expects($this->once())->method('getPrice')->will(
            $this->returnValue($aBasketMockData['getPrice'])
        );
        $oBasket->expects($this->once())->method('getBasketCurrency')->will(
            $this->returnValue($aBasketMockData['getBasketCurrency'])
        );
        $oBasket->expects($this->at(2))->method('getCosts')->with('oxpayment')->will(
            $this->returnValue($aBasketMockData['getCosts:oxpayment'])
        );
        $oBasket->expects($this->at(3))->method('getCosts')->with('oxgiftcard')->will(
            $this->returnValue($aBasketMockData['getCosts:oxgiftcard'])
        );
        $oBasket->expects($this->at(4))->method('getCosts')->with('oxwrapping')->will(
            $this->returnValue($aBasketMockData['getCosts:oxwrapping'])
        );
        $oBasket->expects($this->at(5))->method('getCosts')->with('oxtsprotection')->will(
            $this->returnValue($aBasketMockData['getCosts:oxtsprotection'])
        );
        $oBasket->expects($this->at(6))->method('getCosts')->with('oxdelivery')->will(
            $this->returnValue($aBasketMockData['getCosts:oxdelivery'])
        );
        $oBasket->expects($this->once())->method('getTotalDiscountSum')->will(
            $this->returnValue($aBasketMockData['getTotalDiscountSum'])
        );
        $oBasket->expects($this->once())->method('getContents')->will(
            $this->returnValue($aBasketMockData['getContents'])
        );

        $oShop = $this->getMock('paypPayPalPlusShop', array('getBasket'));
        $oShop->expects($this->once())->method('getBasket')->will($this->returnValue($oBasket));

        $this->SUT = $this->getMock('paypPayPalPlusBasketData', array('getShop'));
        $this->SUT->expects($this->any())->method('getShop')->will($this->returnValue($oShop));

        oxTestModules::addModuleObject('paypPayPalPlusBasketItemData', $this->_getBasketItemDataMock(99.99));

        $this->assertEquals($aExpectedReturn, $this->SUT->getData(), $sTestConditions);
    }

    /**
     * Data provider for testing `getData`
     *
     * @return array
     */
    public function basketDataProvider()
    {
        return array(
            array(
                'Basket is empty',
                array(
                    'getPrice'                => new oxPrice,
                    'getBasketCurrency'       => (object) array('name' => 'EUR'),
                    'getCosts:oxpayment'      => null,
                    'getCosts:oxgiftcard'     => null,
                    'getCosts:oxwrapping'     => null,
                    'getCosts:oxtsprotection' => null,
                    'getCosts:oxdelivery'     => null,
                    'getTotalDiscountSum'     => 0.0,
                    'getContents'             => array(),
                ),
                array(
                    'Amount'   => array(
                        'Total'    => '0.00',
                        'Currency' => 'EUR',
                    ),
                    'Details'  => array(
                        'Subtotal'         => '0.00',
                        'Tax'              => '0.00',
                        'HandlingFee'      => '0.00',
                        'Insurance'        => '0.00',
                        'Shipping'         => '0.00',
                        'ShippingDiscount' => '0.00',
                    ),
                    'ItemList' => array(),
                ),
            ),

            array(
                'No items in basket',
                array(
                    'getPrice'                => new oxPrice(10.0),
                    'getBasketCurrency'       => (object) array('name' => 'EUR'),
                    'getCosts:oxpayment'      => null,
                    'getCosts:oxgiftcard'     => null,
                    'getCosts:oxwrapping'     => null,
                    'getCosts:oxtsprotection' => null,
                    'getCosts:oxdelivery'     => new oxPrice(10.0),
                    'getTotalDiscountSum'     => 0.0,
                    'getContents'             => array(),
                ),
                array(
                    'Amount'   => array(
                        'Total'    => '10.00',
                        'Currency' => 'EUR',
                    ),
                    'Details'  => array(
                        'Subtotal'         => '0.00',
                        'Tax'              => '0.00',
                        'HandlingFee'      => '0.00',
                        'Insurance'        => '0.00',
                        'Shipping'         => '10.00',
                        'ShippingDiscount' => '0.00',
                    ),
                    'ItemList' => array(),
                ),
            ),

            array(
                'A basket with one item',
                array(
                    'getPrice'                => new oxPrice(128.99),
                    'getBasketCurrency'       => (object) array('name' => 'EUR'),
                    'getCosts:oxpayment'      => null,
                    'getCosts:oxgiftcard'     => null,
                    'getCosts:oxwrapping'     => null,
                    'getCosts:oxtsprotection' => null,
                    'getCosts:oxdelivery'     => new oxPrice(10.0),
                    'getTotalDiscountSum'     => 0.0,
                    'getContents'             => array(
                        $this->getMock('oxBasketItem', array('__construct', '__call')),
                    ),
                ),
                array(
                    'Amount'   => array(
                        'Total'    => '128.99',
                        'Currency' => 'EUR',
                    ),
                    'Details'  => array(
                        'Subtotal'         => '99.99',
                        'Tax'              => '19.00',
                        'HandlingFee'      => '0.00',
                        'Insurance'        => '0.00',
                        'Shipping'         => '10.00',
                        'ShippingDiscount' => '0.00',
                    ),
                    'ItemList' => array(
                        $this->_getBasketItemDataMock(99.99),
                    ),
                ),
            ),


            array(
                'A basket with one item and all fees',
                array(
                    'getPrice'                => new oxPrice(165.22),
                    'getBasketCurrency'       => (object) array('name' => 'EUR'),
                    'getCosts:oxpayment'      => new oxPrice(1.23),
                    'getCosts:oxgiftcard'     => new oxPrice(5.0),
                    'getCosts:oxwrapping'     => new oxPrice(10.0),
                    'getCosts:oxtsprotection' => new oxPrice(20.0),
                    'getCosts:oxdelivery'     => new oxPrice(10.0),
                    'getTotalDiscountSum'     => 0.0,
                    'getContents'             => array(
                        $this->getMock('oxBasketItem', array('__construct', '__call')),
                    ),
                ),
                array(
                    'Amount'   => array(
                        'Total'    => '165.22',
                        'Currency' => 'EUR',
                    ),
                    'Details'  => array(
                        'Subtotal'         => '99.99',
                        'Tax'              => '19.00',
                        'HandlingFee'      => '16.23',
                        'Insurance'        => '20.00',
                        'Shipping'         => '10.00',
                        'ShippingDiscount' => '0.00',
                    ),
                    'ItemList' => array(
                        $this->_getBasketItemDataMock(99.99),
                    ),
                ),
            ),

            array(
                'A basket with one item, all fees and discount vouchers',
                array(
                    'getPrice'                => new oxPrice(145.54),
                    'getBasketCurrency'       => (object) array('name' => 'EUR'),
                    'getCosts:oxpayment'      => new oxPrice(1.23),
                    'getCosts:oxgiftcard'     => new oxPrice(5.0),
                    'getCosts:oxwrapping'     => new oxPrice(10.0),
                    'getCosts:oxtsprotection' => new oxPrice(20.0),
                    'getCosts:oxdelivery'     => new oxPrice(10.0),
                    'getTotalDiscountSum'     => 19.68,
                    'getContents'             => array(
                        $this->getMock('oxBasketItem', array('__construct', '__call')),
                    ),
                ),
                array(
                    'Amount'   => array(
                        'Total'    => '145.54',
                        'Currency' => 'EUR',
                    ),
                    'Details'  => array(
                        'Subtotal'         => '99.99',
                        'Tax'              => '19.00',
                        'HandlingFee'      => '16.23',
                        'Insurance'        => '20.00',
                        'Shipping'         => '10.00',
                        'ShippingDiscount' => '-19.68',
                    ),
                    'ItemList' => array(
                        $this->_getBasketItemDataMock(99.99),
                    ),
                ),
            ),
        );
    }


    /**
     * test `getDataUtils` in the abstract class
     */
    public function testGetDataUtils()
    {
        $this->assertTrue($this->SUT->getDataUtils() instanceof paypPayPalPlusDataAccess);
    }


    /**
     * test `getConverter` in the abstract class
     */
    public function testGetConverter()
    {
        $this->assertTrue($this->SUT->getConverter() instanceof paypPayPalPlusDataConverter);
    }


    public function testGetFields_noArgument_returnAllBasketRelatedSdkFields()
    {
        $mReturn = $this->SUT->getFields();

        $this->assertInternalType('array', $mReturn);

        $this->assertArrayHasKey('Amount', $mReturn);
        $this->assertArrayHasKey('Details', $mReturn);
        $this->assertArrayHasKey('ItemList', $mReturn);

        $this->assertInternalType('array', $mReturn['Amount']);
        $this->assertSame(array('Total', 'Currency'), $mReturn['Amount']);

        $this->assertInternalType('array', $mReturn['Details']);
        $this->assertSame(
            array('Subtotal', 'Tax', 'HandlingFee', 'Insurance', 'Shipping', 'ShippingDiscount'),
            $mReturn['Details']
        );

        $this->assertInternalType('array', $mReturn['ItemList']);
        $this->assertSame(array(), $mReturn['ItemList']);
    }


    /**
     * Test `getFields` with data provider
     *
     * @param mixed $mArgument
     * @param mixed $mExpectedReturn
     *
     * @dataProvider fieldDataProvider
     */
    public function testGetFields($mArgument, $mExpectedReturn)
    {
        $this->assertSame($mExpectedReturn, $this->SUT->getFields($mArgument));
    }

    /**
     * Data provider for testing `getFields`
     *
     * @return array
     */
    public function fieldDataProvider()
    {
        return array(
            array(' ', null),
            array(true, null),
            array('Data', null),
            array('Total', null),
            array('amount', null),
            array(1, null),
            array('Amount', array('Total', 'Currency')),
            array('Details', array('Subtotal', 'Tax', 'HandlingFee', 'Insurance', 'Shipping', 'ShippingDiscount')),
            array('ItemList', array()),
        );
    }


    /**
     * `__call` should parse getters and return data value where it matches data provider fields.
     */
    public function testMagicCall()
    {
        $oBasket = $this->getMock(
            'oxBasket',
            array(
                '__construct', '__call',
                'getPrice', 'getBasketCurrency', 'getCosts', 'getTotalDiscountSum', 'getContents'
            )
        );
        $oBasket->expects($this->once())->method('getPrice')->will(
            $this->returnValue(new oxPrice(145.54))
        );
        $oBasket->expects($this->once())->method('getBasketCurrency')->will(
            $this->returnValue((object) array('name' => 'EUR'))
        );
        $oBasket->expects($this->at(2))->method('getCosts')->with('oxpayment')->will(
            $this->returnValue(new oxPrice(1.23))
        );
        $oBasket->expects($this->at(3))->method('getCosts')->with('oxgiftcard')->will(
            $this->returnValue(new oxPrice(5.0))
        );
        $oBasket->expects($this->at(4))->method('getCosts')->with('oxwrapping')->will(
            $this->returnValue(new oxPrice(10.0))
        );
        $oBasket->expects($this->at(5))->method('getCosts')->with('oxtsprotection')->will(
            $this->returnValue(new oxPrice(20.0))
        );
        $oBasket->expects($this->at(6))->method('getCosts')->with('oxdelivery')->will(
            $this->returnValue(new oxPrice(10.0))
        );
        $oBasket->expects($this->once())->method('getTotalDiscountSum')->will(
            $this->returnValue(19.68)
        );
        $oBasket->expects($this->once())->method('getContents')->will(
            $this->returnValue(array($this->getMock('oxBasketItem', array('__construct', '__call'))))
        );

        $oShop = $this->getMock('paypPayPalPlusShop', array('getBasket'));
        $oShop->expects($this->once())->method('getBasket')->will($this->returnValue($oBasket));

        $this->SUT = $this->getMock('paypPayPalPlusBasketData', array('getShop'));
        $this->SUT->expects($this->any())->method('getShop')->will($this->returnValue($oShop));

        oxTestModules::addModuleObject('paypPayPalPlusBasketItemData', $this->_getBasketItemDataMock(99.99));

        $this->assertNull($this->SUT->newAmount());
        $this->assertNull($this->SUT->goGetAmount());
        $this->assertNull($this->SUT->Amount());
        $this->assertNull($this->SUT->getSomeAmount());
        $this->assertNull($this->SUT->getValueTotal());
        $this->assertNull($this->SUT->getAmountValuePrice());

        $this->assertSame(array('Total' => '145.54', 'Currency' => 'EUR'), $this->SUT->getAmount());
        $this->assertSame('145.54', $this->SUT->getAmountValueTotal());
        $this->assertSame('EUR', $this->SUT->getAmountValueCurrency());

        $this->assertSame(
            array(
                'Subtotal'         => '99.99',
                'Tax'              => '19.00',
                'HandlingFee'      => '16.23',
                'Insurance'        => '20.00',
                'Shipping'         => '10.00',
                'ShippingDiscount' => '-19.68',
            ),
            $this->SUT->getDetails()
        );
        $this->assertSame('99.99', $this->SUT->getDetailsValueSubtotal());
        $this->assertSame('19.00', $this->SUT->getDetailsValueTax());
        $this->assertSame('16.23', $this->SUT->getDetailsValueHandlingFee());
        $this->assertSame('20.00', $this->SUT->getDetailsValueInsurance());
        $this->assertSame('10.00', $this->SUT->getDetailsValueShipping());
        $this->assertSame('-19.68', $this->SUT->getDetailsValueShippingDiscount());

        $this->assertInternalType('array', $this->SUT->getItemList());
    }


    /**
     * Get basket item data provider mock for predefined price and quantity.
     *
     * @param double     $dUnitPrice
     * @param int|double $dQuantity
     *
     * @return PHPUnit_Framework_MockObject_MockObject|paypPayPalPlusBasketItemData
     */
    protected function _getBasketItemDataMock($dUnitPrice, $dQuantity = 1)
    {
        $oItemData = $this->getMock(
            'paypPayPalPlusBasketItemData',
            array('__construct', '__call', 'setBasketItem', 'getPrice', 'getTax', 'getQuantity')
        );
        $oItemData->expects($this->any())->method('setBasketItem')->with($this->isType('object'));
        $oItemData->expects($this->any())->method('getPrice')->will($this->returnValue((double) $dUnitPrice));
        $oItemData->expects($this->any())->method('getTax')->will(
            $this->returnValue(round((double) $dUnitPrice * 0.19, 2))
        );
        $oItemData->expects($this->any())->method('getQuantity')->will($this->returnValue($dQuantity));

        return $oItemData;
    }
}
