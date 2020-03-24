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
 * Class paypPayPalPlusDataAccessTest
 * Tests for paypPayPalPlusDataAccess model.
 *
 * @see paypPayPalPlusDataAccess
 */
class paypPayPalPlusDataAccessTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusDataAccess
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock('paypPayPalPlusDataAccess', array('__call'));
    }


    /**
     * @dataProvider arrayAndKeyDataProvider
     */
    public function testGetArrayValue($sTestingCondition, $mKey, array $aData, $mExpectedReturn)
    {
        $this->assertEquals($mExpectedReturn, $this->SUT->getArrayValue($mKey, $aData), $sTestingCondition);
    }

    public function arrayAndKeyDataProvider()
    {
        return array(
            array('Key not present in array', 'b', array('a' => 'value'), null),
            array('Array is empty', 'a', array(), null),
            array('Key is an integer', 10, array(10 => 'value'), 'value'),
            array('Key is a string', '10 ', array(10 => 'value', '10 ' => 'Special VAL'), 'Special VAL'),
            array('Value is scalar', 'key', array('key' => 888.8), 888.8),
            array('Value is array', 'key', array('key' => array('key' => 'value')), array('key' => 'value')),
            array('Value is object', 'key', array('key' => new stdClass()), new stdClass()),
            array('Value is boolean', 'key', array('key' => false), false),
            array('Value is null', 'key', array('key' => null), null),
            array('Value is empty string', 'key', array('key' => ''), ''),
        );
    }


    /**
     * @dataProvider objectOrArrayAndInvokeArgumentsDataProvider
     */
    public function testInvokeGet($sTestingCondition, $mObject, array $aArguments, $mExpectedReturn)
    {
        $mReturn = call_user_func_array(
            array($this->SUT, 'invokeGet'),
            array_merge(array($mObject), $aArguments)
        );

        $this->assertEquals($mExpectedReturn, $mReturn, $sTestingCondition);
    }

    public function objectOrArrayAndInvokeArgumentsDataProvider()
    {
        return array(
            array('Null as object', null, array('value:[]'), null),
            array('Scalar as object', 'value', array('value:[]'), null),
            array('No invoke arguments', array('value'), array(), array('value')),

            // Object method access (no arguments)
            array('Object method access with no arguments', new invokeTestClass, array(), new invokeTestClass),
            array('Object method access with a modifier', new invokeTestClass, array('getString:$'), null),
            array('Object method access with proper argument', new invokeTestClass, array('getString'), 'x'),
            array(
                'Deeper level of objects access chain',
                new invokeTestClass,
                array('getSelf', 'getSelf', 'getArray'),
                array(0 => 'VAL', 1 => (object) array('key' => 'value'), 2 => array('field' => 1))
            ),

            // Array access
            array('Array as object and no modifier', array('value'), array('value'), null),
            array('Array as object and empty modifier', array('value'), array('value:'), null),
            array('Array as object and invalid modifier', array('value'), array('value:$'), null),
            array('Array as object and no key found', array('value'), array('value:[]'), null),
            array('Array as object and key is integer', array('value'), array('0:[]'), 'value'),
            array('Array as object and key is string', array('key' => 'value'), array('key:[]'), 'value'),
            array(
                'Multidimensional array access',
                array('key' => array('sub-key' => array(1))),
                array('key:[]', 'sub-key:[]', '0:[]'),
                1
            ),

            // Property access
            array('Object property access and no modifier', (object) array('key' => 'value'), array('key'), null),
            array('Object property access and empty modifier', (object) array('key' => 'value'), array('key:'), null),
            array('Object property access and invalid modifier', (object) array('key' => 'value'), array('key:[]'), null),
            array('Object property not present', (object) array('key' => 'value'), array('other:$'), null),
            array('Object is invalid for property', new stdClass(), array('key:$'), null),
            array('Object property is invalid', new stdClass(), array("key or test() + " . PHP_EOL . ":$"), null),
            array('Object property is valid', (object) array('key' => 'value'), array('key:$'), 'value'),
            array(
                'A chain of objects with properties',
                (object) array('key' => (object) array('sub-key' => (object) array('lastKey' => array(1)))),
                array('key:$', 'sub-key:$', 'lastKey:$'),
                array(1)
            ),

            // Object method access with arguments used
            array(
                'Object method access with argument and no modifier',
                new invokeTestClass,
                array('getString'),
                'x'
            ),
            array(
                'Object method access with argument and empty modifier',
                new invokeTestClass,
                array('getString:'),
                'x'
            ),
            array(
                'Object method access with argument and invalid modifier',
                new invokeTestClass,
                array('getByArgument:[]'),
                null
            ),
            array(
                'Object method access with a valid argument modifier',
                new invokeTestClass,
                array('getString:argument'),
                'ARGUMENT'
            ),
            array(
                'Objects chain methods access with a valid argument modifiers',
                new invokeTestClass,
                array('getByArgument:self', 'getByArgument:self', 'getString:argument'),
                'ARGUMENT'
            ),

            // Special and mixed data access cases
            array(
                'Access chain: object method, object property',
                new invokeTestClass,
                array('getSelf', 'sProperty:$'),
                'STR'
            ),
            array(
                'Access chain: object method, object property, array key',
                new invokeTestClass,
                array('getSelf', 'aProperty:$', 'x:[]'),
                'y'
            ),
            array(
                'Access chain: object method with argument, object method, array key, object property',
                new invokeTestClass,
                array('getByArgument:self', 'getArray', '1:[]', 'key:$'),
                'value'
            ),
            array(
                'Long mixed chain',
                array(0 => (object) array('dataObject' => new invokeTestClass)),
                array('0:[]', 'dataObject:$', 'getSelf', 'getByArgument:self', 'iProperty:$'),
                0
            )
        );
    }


    /**
     * test `transfuse` with data provider. Testing conditions are defined in the data provider.
     *
     * @param string $sTestingCondition
     * @param        paypPayPalPlusBasketData|paypPayPalPlusBasketItemData|
     * paypPayPalPlusUserData|paypPayPalPlusPaymentDataProvider|paypPayPalPlusRefundDataProvider $mSource
     * @param string $sTestName
     * @param array  $aArguments
     * @param string $sPrefix
     *
     * @dataProvider transfuseDataProvider
     */
    public function testTransfuse($sTestingCondition, $mSource, $sTestName, array $aArguments, $sPrefix)
    {
        $mTarget = $this->_getTargetObjectByTestName($sTestName);
        $mExpectedReturn = $this->_getExpectedReturnByTestName($sTestName);

        $this->SUT->transfuse($mSource, $mTarget, $aArguments, $sPrefix);

        $this->assertEquals($mExpectedReturn, $mTarget, $sTestingCondition);
    }

    /**
     * Data provider for testing `transfuse`
     *
     * @return array
     */
    public function transfuseDataProvider()
    {
        $oBasketData = $this->_getBasketDataMock();
        $oBasketItemData = $this->_getBasketItemDataMock();
        $oUserData = $this->_getUserDataMock();
        $oPaymentDataProvider = $this->_getPaymentDataProviderMock();
        $oRefundDataProvider = $this->_getRefundDataProviderMock();

        return array(
            array(
                'Testing transfusing from basket data to PayPal Item',
                $oBasketData,
                'test1',
                $oBasketData->getFields('Details'),
                'DetailsValue',
            ),
            array(
                'Testing transfusing from basket data to PayPal Amount',
                $oBasketData,
                'test2',
                $oBasketData->getFields('Amount'),
                'AmountValue',
            ),
            array(
                'Testing transfusing from basket item data to PayPal Item',
                $oBasketItemData,
                'test3',
                $oBasketItemData->getFields(),
                '',
            ),
            array(
                'Testing transfusing from user data to PayPal Address',
                $oUserData,
                'test4',
                $oUserData->getFields('Address'),
                'ShippingAddressValue',
            ),
            array(
                'Testing transfusing from payment data provider to payment data',
                $oPaymentDataProvider,
                'test5',
                $oPaymentDataProvider->getFields(),
                ''
            ),
            array(
                'Testing transfusing from refund data provider to refund data',
                $oRefundDataProvider,
                'test6',
                $oRefundDataProvider->getFields(),
                ''
            ),
            array(
                'Testing bad target instance, returns same empty object',
                $oRefundDataProvider,
                'test7',
                $oRefundDataProvider->getFields(),
                ''
            )
        );
    }

    /**
     * Tests the function for getting the delivery date of a basket.
     *
     * @dataProvider deliveryDateProvider
     */
    public function testGetBasketDeliveryDate($items, $interval, $expect)
    {
        $articles = array();
        foreach ($items as $item) {
            $article = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
            $article->oxarticles__oxmaxdeltime = new oxField($item[0], oxField::T_RAW);
            $article->oxarticles__oxdeltimeunit = new oxField($item[1]);
            $article->oxarticles__oxdelivery = new oxField($item[2], oxField::T_RAW);
            $articles[] = $article;
        }

        $basket = $this->getMock('oxBasket');
        $basket->expects($this->once())
            ->method('getBasketArticles')
            ->will(
                $this->returnValue($articles)
            );
        oxRegistry::getSession()->setBasket($basket);

        if ($interval) {
            $date = new DateTime();
            $date->add(new DateInterval($expect));
            $deliveryDate = $date->format('Y-m-d');
        } else {
            $deliveryDate = $expect;
        }

        $this->assertEquals($deliveryDate, $this->SUT->getBasketDeliveryDate());
    }

    /**
     * Data provider for testing testGetBasketDeliveryDate.
     *
     * @return array
     */
    public function deliveryDateProvider()
    {
        return array(
            array(
                'items' => array(
                    array(3, 'DAY', '0000-00-00')
                ),
                'interval' => true,
                'expect' => 'P3D'
            ),
            array(
                'items' => array(
                    array(4, 'WEEK', '0000-00-00'),
                ),
                'interval' => true,
                'expect' => 'P4W'
            ),
            array(
                'items' => array(
                    array(3, 'DAY', '2042-01-01'),
                ),
                'interval' => false,
                'expect' => '2042-01-01'
            ),
            array(
                'items' => array(
                    array(3, 'DAY', '0000-00-00'),
                    array(4, 'WEEK', '0000-00-00'),
                ),
                'interval' => true,
                'expect' => 'P4W'
            ),
            array(
                'items' => array(
                    array(3, 'DAY', '2042-01-01'),
                    array(3, 'DAY', '0000-00-00'),
                ),
                'interval' => false,
                'expect' => '2042-01-01'
            ),
        );
    }

    /**
     * Return target object by test case name.
     * NOTE: Used like thins instead of in data provider, because data provider can not find PayPal Api classes
     *
     * @param $sTestName
     *
     * @return paypPayPalPlusPaymentData|paypPayPalPlusRefundData|oxSuperCfg|\PayPal\Api\Address|\PayPal\Api\Amount|\PayPal\Api\Item
     */
    protected function _getTargetObjectByTestName($sTestName)
    {
        switch ($sTestName) {
            case 'test1':
                return new PayPal\Api\Item();
            case 'test2':
                return new PayPal\Api\Amount();
            case 'test3':
                return new PayPal\Api\Item();
            case 'test4':
                return new PayPal\Api\Address();
            case 'test5':
                return oxNew(\OxidEsales\PayPalPlus\Model\PaymentData::class);
            case 'test6':
                return oxNew(\OxidEsales\PayPalPlus\Model\RefundData::class);
            default:
                return oxNew(\OxidEsales\Eshop\Core\Base::class);
        }
    }

    /**
     * Return expected transfused object by test case name.
     * NOTE: Used like thins instead of in data provider, because data provider can not find PayPal Api classes
     *
     * @param $sTestName
     *
     * @return PayPal\Api\Item|PayPal\Api\Amount|PayPal\Api\Address|paypPayPalPlusPaymentData|
     * paypPayPalPlusRefundData|oxSuperCfg
     */
    protected function _getExpectedReturnByTestName($sTestName)
    {
        switch ($sTestName) {
            case 'test1':
                return $this->_getTransfusedExpectedItemFromBasket();
            case 'test2':
                return $this->_getTransfusedExpectedAmountFromBasket();
            case 'test3':
                return $this->_getTransfusedExpectedItemFromBasketItem();
            case 'test4':
                return $this->_getTransfusedExpectedAddressFromUserData();
            case 'test5':
                return $this->_getTransfusedExpectedPaymentDataFromPaymentDataProvider();
            case 'test6':
                return $this->_getTransfusedExpectedRefundDataFromRefundDataProvider();
            default:
                return oxNew(\OxidEsales\Eshop\Core\Base::class);
        }
    }

    /**
     * Basket data mock
     *
     * @return paypPayPalPlusBasketData
     */
    protected function _getBasketDataMock()
    {
        $aData = array(
            'Amount'   => array(
                'Total'    => 15.5,
                'Currency' => 'EUR',
            ),
            'Details'  => array(
                'Subtotal'         => 0.0,
                'Tax'              => 8.0,
                'HandlingFee'      => 5.0,
                'Insurance'        => 1.0,
                'Shipping'         => 2.0,
                'ShippingDiscount' => -1.0,
            ),
            'ItemList' => array(),
        );

        $oBasketData = $this->getMock('paypPayPalPlusBasketData', array('getData'));
        $oBasketData->expects($this->once())->method('getData')->will($this->returnValue($aData));

        return $oBasketData;
    }

    /**
     * Returns PayPal Plus Api expected Item object transfused from basket data
     *
     * @return \PayPal\Api\Item
     */
    protected function _getTransfusedExpectedItemFromBasket()
    {
        $oItem = new PayPal\Api\Item();
        $oItem->setTax(8.0);

        return $oItem;
    }

    /**
     * Returns PayPal Plus Api expected Amount object transfused from basket data
     *
     * @return \PayPal\Api\Amount
     */
    protected function _getTransfusedExpectedAmountFromBasket()
    {
        $oAmount = new PayPal\Api\Amount();
        $oAmount->setTotal(15.50);
        $oAmount->setCurrency('EUR');

        return $oAmount;
    }

    /**
     * Basket item data mock
     *
     * @return paypPayPalPlusBasketItemData
     */
    protected function _getBasketItemDataMock()
    {
        $aData = array(
            'Name'     => 'test article',
            'Currency' => 'LTU',
            'Price'    => 16.5,
            'Quantity' => 1,
            'Tax'      => 9.0,
            'Sku'      => 'testArtNum',
        );

        $oBasketData = $this->getMock('paypPayPalPlusBasketItemData', array('getData'));
        $oBasketData->expects($this->any())->method('getData')->will($this->returnValue($aData));

        return $oBasketData;
    }

    /**
     * Returns PayPal Plus Api expected Item object transfused from basket item data
     *
     * @return \PayPal\Api\Item
     */
    protected function _getTransfusedExpectedItemFromBasketItem()
    {
        $oItem = new PayPal\Api\Item();
        $oItem->setTax(9.0);
        $oItem->setName('test article');
        $oItem->setCurrency('LTU');
        $oItem->setPrice(16.5);
        $oItem->setQuantity(1);
        $oItem->setSku('testArtNum');

        return $oItem;
    }

    /**
     * User data mock
     *
     * @return paypPayPalPlusUserData
     */
    protected function _getUserDataMock()
    {
        $aData = array(
            'BillingAddress'  => array(
                'RecipientName' => 'Name Surname',
                'Line1'         => 'testStreet 5',
                'Line2'         => 'test additional info',
                'City'          => 'testCity',
                'CountryCode'   => 'testCountryCode',
                'State'         => 'testState',
                'PostalCode'    => 'testPostalCode',
                'Phone'         => 'testPhone',
            ),
            'ShippingAddress' => array(
                'RecipientName' => 'Name Surname',
                'Line1'         => 'testShippingStreet 5',
                'Line2'         => 'test additional info',
                'City'          => 'testShippingCity',
                'CountryCode'   => 'testShippingCountryCode',
                'State'         => 'testShippingState',
                'PostalCode'    => 'testShippingPostalCode',
                'Phone'         => 'testShippingPhone',
            ),
        );

        $oUserData = $this->getMock('paypPayPalPlusUserData', array('getData'));
        $oUserData->expects($this->any())->method('getData')->will($this->returnValue($aData));

        return $oUserData;
    }

    /**
     * Returns PayPal Plus Api expected Address object transfused from user data
     *
     * @return \PayPal\Api\Address
     */
    protected function _getTransfusedExpectedAddressFromUserData()
    {
        $oAddress = new PayPal\Api\Address();
        $oAddress->setLine1('testShippingStreet 5');
        $oAddress->setLine2('test additional info');
        $oAddress->setCity('testShippingCity');
        $oAddress->setCountryCode('testShippingCountryCode');
        $oAddress->setState('testShippingState');
        $oAddress->setPostalCode('testShippingPostalCode');
        $oAddress->setPhone('testShippingPhone');

        return $oAddress;
    }

    /**
     * Payment data provider mock
     *
     * @return paypPayPalPlusPaymentDataProvider
     */
    protected function _getPaymentDataProviderMock()
    {
        $aData = array(
            'OrderId'       => 'testOrderId',
            'SaleId'        => 'testSaleId',
            'PaymentObject' => null,
            'DateCreated'   => '2011-11-11 11:11:11',
            'Total'         => 20.21,
            'Currency'      => 'EUR',
            'PaymentId'     => 'testPaymentId',
            'Status'        => 'testStatus',
        );

        $oPayPalPlusPaymentDataProvider = $this->getMock('paypPayPalPlusPaymentDataProvider', array('getData'));
        $oPayPalPlusPaymentDataProvider->expects($this->any())->method('getData')->will($this->returnValue($aData));

        return $oPayPalPlusPaymentDataProvider;
    }

    /**
     * Returns expected paypPayPalPlusPaymentData object transfused from payment data provider
     *
     * @return paypPayPalPlusPaymentData
     */
    protected function _getTransfusedExpectedPaymentDataFromPaymentDataProvider()
    {
        $oPaymentData = oxNew(\OxidEsales\PayPalPlus\Model\PaymentData::class);
        $oPaymentData->payppaypalpluspayment__oxorderid = new oxField('testOrderId');
        $oPaymentData->payppaypalpluspayment__oxsaleid = new oxField('testSaleId');
        $oPaymentData->payppaypalpluspayment__oxpaymentid = new oxField('testPaymentId');
        $oPaymentData->payppaypalpluspayment__oxstatus = new oxField('testStatus');
        $oPaymentData->payppaypalpluspayment__oxdatecreated = new oxField('2011-11-11 11:11:11');
        $oPaymentData->payppaypalpluspayment__oxtotal = new oxField(20.21);
        $oPaymentData->payppaypalpluspayment__oxcurrency = new oxField('EUR');
        $oPaymentData->payppaypalpluspayment__oxpaymentobject = false;

        return $oPaymentData;
    }

    /**
     * Refund data provider mock
     *
     * @return paypPayPalPlusRefundDataProvider
     */
    protected function _getRefundDataProviderMock()
    {
        $aData = array(
            'SaleId'       => 'testSaleId',
            'RefundId'     => 'testRefundId',
            'Status'       => 'testStatus',
            'DateCreated'  => '2011-11-11 11:11:11',
            'Total'        => 23.23,
            'Currency'     => 'EUR',
            'RefundObject' => null,
        );

        $oPayPalPlusRefundDataProvider = $this->getMock('paypPayPalPlusRefundDataProvider', array('getData'));
        $oPayPalPlusRefundDataProvider->expects($this->any())->method('getData')->will($this->returnValue($aData));

        return $oPayPalPlusRefundDataProvider;
    }

    /**
     * Returns expected paypPayPalPlusRefundData object transfused from refund data provider
     *
     * @return paypPayPalPlusRefundData
     */
    protected function _getTransfusedExpectedRefundDataFromRefundDataProvider()
    {
        $oRefundData = oxNew(\OxidEsales\PayPalPlus\Model\RefundData::class);
        $oRefundData->payppaypalplusrefund__oxsaleid = new oxField('testSaleId');
        $oRefundData->payppaypalplusrefund__oxrefundid = new oxField('testRefundId');
        $oRefundData->payppaypalplusrefund__oxstatus = new oxField('testStatus');
        $oRefundData->payppaypalplusrefund__oxdatecreated = new oxField('2011-11-11 11:11:11');
        $oRefundData->payppaypalplusrefund__oxtotal = new oxField(23.23);
        $oRefundData->payppaypalplusrefund__oxcurrency = new oxField('EUR');

        return $oRefundData;
    }
}
