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
 * Class paypPayPalPlusBasketItemDataTest
 * Tests for paypPayPalPlusBasketItemData model.
 *
 * @see paypPayPalPlusBasketItemData
 */
class paypPayPalPlusBasketItemDataTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusBasketItemData
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $oShop = $this->getMock('paypPayPalPlusShop', array('getBasket'));

        $this->SUT = $this->getMock('paypPayPalPlusBasketItemData', array('getShop'));
        $this->SUT->expects($this->any())->method('getShop')->will($this->returnValue($oShop));
    }


    /**
     * test `setBasketItem` a setter for _oBasketItem
     */
    public function testSetBasketItem()
    {
        $oBasketItem = new oxBasketItem();

        $this->SUT->setBasketItem($oBasketItem);

        $this->assertSame($oBasketItem, $this->SUT->getBasketItem());
    }

    /**
     * test `getBasketItem` when _oBasketItem is not initiated
     */
    public function testGetBasketItem()
    {
        $this->assertEquals(null, $this->SUT->getBasketItem());
    }


    /**
     * @param string $sTestConditions
     * @param array  $aBasketItemMockData
     * @param array  $aExpectedReturn
     *
     * @dataProvider basketItemDataProvider
     */
    public function testGetData($sTestConditions, array $aBasketItemMockData, array $aExpectedReturn)
    {
        $oBasket = $this->getMock('oxBasket', array('__construct', '__call', 'getBasketCurrency'));
        $oBasket->expects($this->once())->method('getBasketCurrency')->will(
            $this->returnValue((object) array('name' => 'EUR'))
        );

        $oShop = $this->getMock('paypPayPalPlusShop', array('getBasket'));
        $oShop->expects($this->any())->method('getBasket')->will($this->returnValue($oBasket));

        $this->SUT = $this->getMock('paypPayPalPlusBasketItemData', array('getShop'));
        $this->SUT->expects($this->any())->method('getShop')->will($this->returnValue($oShop));

        $oBasketItem = $this->getMock(
            'oxBasketItem',
            array('__construct', '__call', 'getArticle', 'getUnitPrice', 'getAmount')
        );
        $oBasketItem->expects($this->any())->method('getArticle')->will(
            $this->returnValue($aBasketItemMockData['getArticle'])
        );
        $oBasketItem->expects($this->any())->method('getUnitPrice')->will(
            $this->returnValue($aBasketItemMockData['getUnitPrice'])
        );
        $oBasketItem->expects($this->once())->method('getAmount')->will(
            $this->returnValue($aBasketItemMockData['getAmount'])
        );
        $this->SUT->setBasketItem($oBasketItem);

        $this->assertEquals($aExpectedReturn, $this->SUT->getData(), $sTestConditions);
    }

    /**
     * Data provider for testing `getData`
     *
     * @return array
     */
    public function basketItemDataProvider()
    {
        return array(
            array(
                'Basket item when shop is in netto mode',
                array(
                    'getArticle'   => $this->_getArticleMock('Test article 1', 'testarticle1'),
                    'getUnitPrice' => $this->_getPriceObject(1, 10, 19),
                    'getAmount'    => 1,
                ),
                array(
                    'Name'     => 'Test article 1',
                    'Currency' => 'EUR',
                    'Price'    => '10.00',
                    'Quantity' => '1',
                    'Tax'      => '1.90',
                    'Sku'      => 'testarticle1',
                ),
            ),

            array(
                'Basket item with no cost',
                array(
                    'getArticle'   => $this->_getArticleMock('Test article 2', 'testarticle2'),
                    'getUnitPrice' => $this->_getPriceObject(1, 0, 19),
                    'getAmount'    => 1,
                ),
                array(
                    'Name'     => 'Test article 2',
                    'Currency' => 'EUR',
                    'Price'    => '0.00',
                    'Quantity' => '1',
                    'Tax'      => '0.00',
                    'Sku'      => 'testarticle2',
                ),
            ),

            array(
                'Basket item when shop is in brutto mode',
                array(
                    'getArticle'   => $this->_getArticleMock('Test article 3', 'testarticle3'),
                    'getUnitPrice' => $this->_getPriceObject(2, 10, 19, false),
                    'getAmount'    => 2,
                ),
                array(
                    'Name'     => 'Test article 3',
                    'Currency' => 'EUR',
                    'Price'    => '16.81',
                    'Quantity' => '2',
                    'Tax'      => '3.19',
                    'Sku'      => 'testarticle3',
                ),
            ),

            array(
                'Basket item when shop is in netto mode, testing edges',
                array(
                    'getArticle'   => $this->_getArticleMock(
                        'Test article with very very very very very very very very very' .
                        ' very very very very very very very very very very very long title',
                        'testarticleveryveryveryveryveryveryveryveryverylongsku'
                    ),
                    'getUnitPrice' => $this->_getPriceObject(1, 10, 19),
                    'getAmount'    => 1,
                ),
                array(
                    'Name'     => 'Test article with very very very very very very very very very' .
                                  ' very very very very very very very very very very very long titl',
                    'Currency' => 'EUR',
                    'Price'    => '10.00',
                    'Quantity' => '1',
                    'Tax'      => '1.90',
                    'Sku'      => 'testarticleveryveryveryveryveryveryveryveryverylon',
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


    /**
     * `__call` should parse getters and return data value where it matches data provider fields.
     */
    public function testMagicCall()
    {
        $oBasket = $this->getMock('oxBasket', array('__call', 'getBasketCurrency'));
        $oBasket->expects($this->once())->method('getBasketCurrency')->will(
            $this->returnValue((object) array('name' => 'EUR'))
        );

        $oShop = $this->getMock('paypPayPalPlusShop', array('getBasket'));
        $oShop->expects($this->any())->method('getBasket')->will($this->returnValue($oBasket));

        $this->SUT = $this->getMock('paypPayPalPlusBasketItemData', array('getShop'));
        $this->SUT->expects($this->any())->method('getShop')->will($this->returnValue($oShop));

        $oBasketItem = $this->getMock(
            'oxBasketItem',
            array('__construct', '__call', 'getArticle', 'getUnitPrice', 'getAmount')
        );
        $oBasketItem->expects($this->any())->method('getArticle')->will(
            $this->returnValue($this->_getArticleMock('Test article 3', 'testarticle3'))
        );
        $oBasketItem->expects($this->any())->method('getUnitPrice')->will(
            $this->returnValue($this->_getPriceObject(2, 10, 19, false))
        );
        $oBasketItem->expects($this->once())->method('getAmount')->will(
            $this->returnValue(2)
        );
        $this->SUT->setBasketItem($oBasketItem);

        $this->assertNull($this->SUT->getSomething());
        $this->assertNull($this->SUT->setName());
        $this->assertNull($this->SUT->getNewName());
        $this->assertNull($this->SUT->newName());
        $this->assertNull($this->SUT->Name());

        $this->assertSame('Test article 3', $this->SUT->getName());
        $this->assertSame('EUR', $this->SUT->getCurrency());
        $this->assertSame('16.81', $this->SUT->getPrice());
        $this->assertSame('2', $this->SUT->getQuantity());
        $this->assertSame('3.19', $this->SUT->getTax());
        $this->assertSame('testarticle3', $this->SUT->getSku());
    }


    /**
     * Get article mock with title and artNum set
     *
     * @param string $sTitle
     * @param string $sArtNum
     *
     * @return PHPUnit_Framework_MockObject_MockObject|oxArticle
     */
    protected function _getArticleMock($sTitle, $sArtNum)
    {
        $oArticle = $this->getMock('oxArticle', array('__construct'));
        $oArticle->oxarticles__oxtitle = new oxField($sTitle);
        $oArticle->oxarticles__oxartnum = new oxField($sArtNum);

        return $oArticle;
    }

    /**
     * Get price object with price and vat percentage set
     *
     * @param double  $dQuantity
     * @param double  $dPrice
     * @param double  $dVat
     * @param boolean $blIsNettoMode
     *
     * @return PHPUnit_Framework_MockObject_MockObject|oxPrice
     */
    protected function _getPriceObject($dQuantity, $dPrice, $dVat, $blIsNettoMode = true)
    {
        $oPrice = new oxPrice();
        if($blIsNettoMode) {
            $oPrice->setNettoPriceMode();
        } else {
            $oPrice->setBruttoPriceMode();
        }

        $oPrice->setPrice($dQuantity * $dPrice, $dVat);

        return $oPrice;
    }
}
