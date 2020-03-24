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
 * Class paypPayPalPlusOxBasketTest
 * Tests for core class paypPayPalPlusOxBasket.
 *
 * @see paypPayPalPlusOxBasket
 */
class paypPayPalPlusOxBasketTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusOxBasket
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock(
            'paypPayPalPlusOxBasket',
            array('__call', '_paypPayPalPlusOxBasket_afterUpdate_parent')
        );
    }


    /**
     * test `getShop`. Always returns an instance of paypPayPalPlusShop
     */
    public function testGetShop()
    {
        $this->SUT = $this->getMock('paypPayPalPlusOxBasket', array('__call'));

        $this->assertInstanceOf('paypPayPalPlusShop', $this->SUT->getShop());
    }


    /**
     * test `getPaymentIdValue` checking default value
     */
    public function testGetPaymentIdValue_noValueSet_returnNull()
    {
        $this->assertNull($this->SUT->getPaymentIdValue());
    }


    /**
     * test `getBasketHash`, using data provider to test different conditions
     *
     * @param string $sTestCondition
     * @param array  $aBasketItemsData
     * @param double $dPaymentCost
     * @param string $sExpectedReturn
     *
     * @dataProvider basketHashDataProvider
     */
    public function testGetBasketHash($sTestCondition, array $aBasketItemsData, $dPaymentCost, $sExpectedReturn)
    {
        $this->SUT = $this->getMock('paypPayPalPlusOxBasket', array('getContents', 'getPaymentCost'));

        $aItems = array();

        foreach ($aBasketItemsData as $aBasketItemData) {
            $oBasketItem = $this->getMock('oxBasketItem', array('getProductId', 'getAmount'));
            $oBasketItem->expects($this->once())->method('getProductId')->will($this->returnValue($aBasketItemData['productId']));
            $oBasketItem->expects($this->once())->method('getAmount')->will($this->returnValue($aBasketItemData['amount']));

            $aItems[] = $oBasketItem;
        }

        $oPrice = $this->getMock('oxPrice', array('getPrice'));
        $oPrice->expects($this->once())->method('getPrice')->will($this->returnValue($dPaymentCost));

        $this->SUT->expects($this->once())->method('getPaymentCost')->will($this->returnValue($oPrice));
        $this->SUT->expects($this->once())->method('getContents')->will($this->returnValue($aItems));

        $this->assertSame($sExpectedReturn, $this->SUT->getBasketHash(), $sTestCondition);
    }

    /**
     * Data provider for testing `getBasketHash`
     *
     * @return array
     */
    public function basketHashDataProvider()
    {
        return array(
            array(
                'Basket has 3 items',
                array(
                    array(
                        'productId' => 'productId1',
                        'amount'    => 1,
                    ),
                    array(
                        'productId' => 'productId2',
                        'amount'    => 2,
                    ),
                    array(
                        'productId' => 'productId3',
                        'amount'    => 3,
                    ),
                ),
                15.5,
                md5('productId1-1;productId2-2;productId3-3;15.5'),
            ),
            array(
                'Basket has no items',
                array(),
                0,
                md5('0'),
            ),
            array(
                'Basket has one item',
                array(
                    array(
                        'productId' => 'productId1',
                        'amount'    => 11,
                    ),
                ),
                5,
                md5('productId1-11;5'),
            ),
        );
    }


    /**
     * test `afterUpdate` no approved payments, nothing is done.
     */
    public function testAfterUpdate_approvedPaymentEmpty_nothingDone()
    {
        $oPayPalSession = $this->getMock('getPayPalPlusSession', array('getApprovedPayment'));
        $oPayPalSession->expects($this->once())->method('getApprovedPayment')->will($this->returnValue(null));

        $oShop = $this->getMock('paypPayPalPlusShop', array('getPayPalPlusSession'));
        $oShop->expects($this->once())->method('getPayPalPlusSession')->will($this->returnValue($oPayPalSession));

        $this->SUT = $this->getMock('paypPayPalPlusOxBasket', array('getShop', '_paypPayPalPlusOxBasket_afterUpdate_parent', '_resetApprovedPayment'));
        $this->SUT->expects($this->once())->method('getShop')->will($this->returnValue($oShop));
        $this->SUT->expects($this->once())->method('_paypPayPalPlusOxBasket_afterUpdate_parent');
        $this->SUT->expects($this->never())->method('_resetApprovedPayment');

        $this->SUT->afterUpdate();
    }

    /**
     * test `afterUpdate` approved payment exists, but it is not an instance of PayPal Apy Payment. Nothing is done.
     */
    public function testAfterUpdate_approvedPaymentNotAnInstanceOfPayPalPaymentApi_nothingDone()
    {
        $oPayPalSession = $this->getMock('getPayPalPlusSession', array('getApprovedPayment'));
        $oPayPalSession->expects($this->once())->method('getApprovedPayment')->will($this->returnValue($this));

        $oShop = $this->getMock('paypPayPalPlusShop', array('getPayPalPlusSession'));
        $oShop->expects($this->once())->method('getPayPalPlusSession')->will($this->returnValue($oPayPalSession));

        $this->SUT = $this->getMock('paypPayPalPlusOxBasket', array('getShop', '_paypPayPalPlusOxBasket_afterUpdate_parent', '_resetApprovedPayment'));
        $this->SUT->expects($this->once())->method('getShop')->will($this->returnValue($oShop));
        $this->SUT->expects($this->once())->method('_paypPayPalPlusOxBasket_afterUpdate_parent');
        $this->SUT->expects($this->never())->method('_resetApprovedPayment');

        $this->SUT->afterUpdate();
    }

    /**
     * test `afterUpdate` approved payment exists and it is an instance of PayPal Apy Payment.
     * Current basket hash does not match the approved basket hash. Approved payment is unset.
     */
    public function testAfterUpdate_basketHashesNotEqual_unsettingApprovedPayment()
    {
        $oPayPalPayment = new PayPal\Api\Payment();

        $oPayPalSession = $this->getMock('getPayPalPlusSession', array('getApprovedPayment', 'getBasketStamp', 'unsetApprovedPayment'));
        $oPayPalSession->expects($this->once())->method('getApprovedPayment')->will($this->returnValue($oPayPalPayment));
        $oPayPalSession->expects($this->once())->method('getBasketStamp')->will($this->returnValue('someBasketHash1'));
        $oPayPalSession->expects($this->once())->method('unsetApprovedPayment');

        $oShop = $this->getMock('paypPayPalPlusShop', array('getPayPalPlusSession'));
        $oShop->expects($this->any())->method('getPayPalPlusSession')->will($this->returnValue($oPayPalSession));

        $this->SUT = $this->getMock('paypPayPalPlusOxBasket', array('getShop', '_paypPayPalPlusOxBasket_afterUpdate_parent', 'getBasketHash'));
        $this->SUT->expects($this->any())->method('getShop')->will($this->returnValue($oShop));
        $this->SUT->expects($this->once())->method('getBasketHash')->will($this->returnValue('someBasketHash2'));
        $this->SUT->expects($this->once())->method('_paypPayPalPlusOxBasket_afterUpdate_parent');

        $this->SUT->afterUpdate();
    }

    /**
     * test `afterUpdate` approved payment exists and it is an instance of PayPal Apy Payment.
     * Current basket hash does match the approved basket hash, but current basket price does not match
     * approved payment price. Approved payment is unset.
     */
    public function testAfterUpdate_basketHashesAreEqual_approvedAndBasketPricesAreNotEqual_unsettingApprovedPayment()
    {
        $oAmount = new PayPal\Api\Amount();
        $oAmount->setTotal('15.15');

        $oTransaction = new PayPal\Api\Transaction();
        $oTransaction->setAmount($oAmount);

        $oPayPalPayment = new PayPal\Api\Payment();
        $oPayPalPayment->setTransactions(array($oTransaction));

        $oPayPalSession = $this->getMock('getPayPalPlusSession', array('getApprovedPayment', 'getBasketStamp', 'unsetApprovedPayment'));
        $oPayPalSession->expects($this->once())->method('getApprovedPayment')->will($this->returnValue($oPayPalPayment));
        $oPayPalSession->expects($this->once())->method('getBasketStamp')->will($this->returnValue('someBasketHash1'));
        $oPayPalSession->expects($this->once())->method('unsetApprovedPayment');

        $oShop = $this->getMock('paypPayPalPlusShop', array('getPayPalPlusSession'));
        $oShop->expects($this->any())->method('getPayPalPlusSession')->will($this->returnValue($oPayPalSession));

        $this->SUT = $this->getMock('paypPayPalPlusOxBasket', array('getShop', '_paypPayPalPlusOxBasket_afterUpdate_parent', 'getBasketHash', 'getPrice'));
        $this->SUT->expects($this->any())->method('getShop')->will($this->returnValue($oShop));
        $this->SUT->expects($this->once())->method('getBasketHash')->will($this->returnValue('someBasketHash1'));
        $this->SUT->expects($this->once())->method('_paypPayPalPlusOxBasket_afterUpdate_parent');

        $this->SUT->afterUpdate();
    }

    /**
     * test `afterUpdate` approved payment exists and it is an instance of PayPal Apy Payment.
     * Current basket hash does match the approved basket hash and current basket price does match
     * the approved payment price. Payment is not unset.
     */
    public function testAfterUpdate_basketHashesAreEqual_approvedAndBasketPricesAreEqual_paymentApproved()
    {
        $oAmount = new PayPal\Api\Amount();
        $oAmount->setTotal('15.15');

        $oTransaction = new PayPal\Api\Transaction();
        $oTransaction->setAmount($oAmount);

        $oPayPalPayment = new PayPal\Api\Payment();
        $oPayPalPayment->setTransactions(array($oTransaction));

        $oPayPalSession = $this->getMock('getPayPalPlusSession', array('getApprovedPayment', 'getBasketStamp', 'unsetApprovedPayment'));
        $oPayPalSession->expects($this->once())->method('getApprovedPayment')->will($this->returnValue($oPayPalPayment));
        $oPayPalSession->expects($this->once())->method('getBasketStamp')->will($this->returnValue('someBasketHash1'));
        $oPayPalSession->expects($this->never())->method('unsetApprovedPayment');

        $oShop = $this->getMock('paypPayPalPlusShop', array('getPayPalPlusSession'));
        $oShop->expects($this->any())->method('getPayPalPlusSession')->will($this->returnValue($oPayPalSession));

        $oPrice = $this->getMock('oxPrice', array('getPrice'));
        $oPrice->expects($this->once())->method('getPrice')->will($this->returnValue('15.15'));

        $this->SUT = $this->getMock('paypPayPalPlusOxBasket', array('getShop', '_paypPayPalPlusOxBasket_afterUpdate_parent', 'getBasketHash', 'getPrice'));
        $this->SUT->expects($this->any())->method('getShop')->will($this->returnValue($oShop));
        $this->SUT->expects($this->once())->method('getBasketHash')->will($this->returnValue('someBasketHash1'));
        $this->SUT->expects($this->once())->method('getPrice')->will($this->returnValue($oPrice));
        $this->SUT->expects($this->once())->method('_paypPayPalPlusOxBasket_afterUpdate_parent');

        $this->SUT->afterUpdate();
    }


    /**
     * test `getPaymentCost`. Testing if getCosts is called with the right string.
     */
    public function testGetPaymentCost()
    {
        $this->SUT = $this->getMock('paypPayPalPlusOxBasket', array('getCosts'));
        $this->SUT->expects($this->once())->method('getCosts')->with('oxpayment');

        $this->SUT->getPaymentCost();
    }


    /**
     * test `getTotalDiscountSum`. Using data provider to test various scenarios.
     *
     * @param string $sTestConditions
     * @param double $dTotalDiscount
     * @param double $dVoucherDiscount
     * @param double $dExpectedResult
     *
     * @dataProvider totalDiscountDataProvider
     */
    public function testGetTotalDiscountSum($sTestConditions, $dTotalDiscount, $dVoucherDiscount, $dExpectedResult)
    {
        $oDiscountPrice = $this->getMock('paypPayPalPlusOxBasket', array('getPrice'));
        $oDiscountPrice->expects($this->once())->method('getPrice')->will($this->returnValue($dTotalDiscount));

        $oVoucherPrice = $this->getMock('paypPayPalPlusOxBasket', array('getPrice'));
        $oVoucherPrice->expects($this->once())->method('getPrice')->will($this->returnValue($dVoucherDiscount));

        $this->SUT = $this->getMock('paypPayPalPlusOxBasket', array('getTotalDiscount', 'getVoucherDiscount'));
        $this->SUT->expects($this->once())->method('getTotalDiscount')->will($this->returnValue($oDiscountPrice));
        $this->SUT->expects($this->once())->method('getVoucherDiscount')->will($this->returnValue($oVoucherPrice));

        $this->assertSame($dExpectedResult, $this->SUT->getTotalDiscountSum(), $sTestConditions);
    }

    /**
     * Data provider for testing `getTotalDiscountSum`
     *
     * @return array
     */
    public function totalDiscountDataProvider()
    {
        return array(
            array(
                'No discounts',
                0,
                0,
                0,
            ),
            array(
                'Only total discount',
                15.5,
                0,
                15.5,
            ),
            array(
                'Only voucher discount',
                0,
                16.6,
                16.6,
            ),
            array(
                'Both discounts',
                3.4,
                16.6,
                20.0,
            ),
        );
    }
}
