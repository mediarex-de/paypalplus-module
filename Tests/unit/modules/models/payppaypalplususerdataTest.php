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
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2015
 */

/**
 * Class paypPayPalPlusUserDataTest
 * Tests for paypPayPalPlusUserData model.
 *
 * @see paypPayPalPlusUserData
 */
class paypPayPalPlusUserDataTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusUserData
     */
    protected $SUT;

    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $oShop = $this->getMock('paypPayPalPlusShop', array('getBasket'));

        $this->SUT = $this->getMock('paypPayPalPlusUserData', array('getSessionVariable'));
        $this->SUT->expects($this->any())->method('getShop')->will($this->returnValue($oShop));
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
     * test `__call` in the abstract class
     */
    public function testMagicCall()
    {
        $oUser = $this->_getUserMock(
            array(
                'oxfname'     => 'John',
                'oxlname'     => 'Smith',
                'oxaddinfo'   => 'Billing addinfo',
                'oxcity'      => 'Billing city',
                'oxzip'       => '123',
                'oxfon'       => '123',
                'oxstreet'    => 'Billing street',
                'oxstreetnr'  => '123',
                'countryCode' => 'DE',
                'stateCode'   => '',
            ),
            array(
                'oxfname'     => 'Johnny',
                'oxlname'     => 'Brown',
                'oxaddinfo'   => 'Shipping addinfo',
                'oxcity'      => 'Shipping city',
                'oxzip'       => '321',
                'oxfon'       => '321',
                'oxstreet'    => 'Shipping street',
                'oxstreetnr'  => '321',
                'countryCode' => 'DE',
                'stateCode'   => '',
            )
        );

        $oShop = $this->getMock('paypPayPalPlusShop', array('getSessionVariable', 'getUser'));
        $oShop->expects($this->once())->method('getSessionVariable')->with('blshowshipaddress')->will(
            $this->returnValue(true)
        );
        $oShop->expects($this->any())->method('getUser')->will($this->returnValue($oUser));

        $oCountry                         = $this->getMock('oxCountry', array('__construct', 'load'));
        $oCountry->oxcountry__oxisoalpha2 = new oxField('DE');
        oxTestModules::addModuleObject('oxCountry', $oCountry);

        $oState                        = $this->getMock('oxState', array('__construct', 'load'));
        $oState->oxstates__oxisoalpha2 = new oxField('');
        oxTestModules::addModuleObject('oxState', $oState);

        $this->SUT = $this->getMock('paypPayPalPlusUserData', array('getShop'));
        $this->SUT->expects($this->any())->method('getShop')->will($this->returnValue($oShop));

        $this->assertSame(null, $this->SUT->setSomething());

        $this->assertSame(
            array(
                'RecipientName' => 'John Smith',
                'Line1'         => 'Billing street 123',
                'Line2'         => 'Billing addinfo',
                'City'          => 'Billing city',
                'CountryCode'   => 'DE',
                'State'         => '',
                'PostalCode'    => '123',
                'Phone'         => '',
            ),
            $this->SUT->getBillingAddress()
        );
        $this->assertSame(
            array(
                'RecipientName' => 'Johnny Brown',
                'Line1'         => 'Shipping street 321',
                'Line2'         => 'Shipping addinfo',
                'City'          => 'Shipping city',
                'CountryCode'   => 'DE',
                'State'         => '',
                'PostalCode'    => '321',
                'Phone'         => '',
            ),
            $this->SUT->getShippingAddress()
        );
        $this->assertSame(null, $this->SUT->getNoSuchArray());

        $this->assertSame('John Smith', $this->SUT->getBillingAddressValueRecipientName());
        $this->assertSame('Billing street 123', $this->SUT->getBillingAddressValueLine1());
        $this->assertSame('Billing addinfo', $this->SUT->getBillingAddressValueLine2());
        $this->assertSame('Billing city', $this->SUT->getBillingAddressValueCity());
        $this->assertSame('DE', $this->SUT->getBillingAddressValueCountryCode());
        $this->assertSame('', $this->SUT->getBillingAddressValueState());
        $this->assertSame('123', $this->SUT->getBillingAddressValuePostalCode());
        $this->assertSame('', $this->SUT->getBillingAddressValuePhone());
        $this->assertSame(null, $this->SUT->getBillingAddressValueNoSuchField());

        $this->assertSame('Johnny Brown', $this->SUT->getShippingAddressValueRecipientName());
        $this->assertSame('Shipping street 321', $this->SUT->getShippingAddressValueLine1());
        $this->assertSame('Shipping addinfo', $this->SUT->getShippingAddressValueLine2());
        $this->assertSame('Shipping city', $this->SUT->getShippingAddressValueCity());
        $this->assertSame('DE', $this->SUT->getShippingAddressValueCountryCode());
        $this->assertSame('', $this->SUT->getShippingAddressValueState());
        $this->assertSame('321', $this->SUT->getShippingAddressValuePostalCode());
        $this->assertSame('', $this->SUT->getShippingAddressValuePhone());
        $this->assertSame(null, $this->SUT->getShippingAddressValueNoFieldExists());
    }

    /**
     * @param string  $sTestConditions
     * @param boolean $blSeparateShippingAddress
     * @param array   $aBillingData
     * @param array   $aShippingData
     * @param array   $aExpectedReturn
     *
     * @dataProvider UserDataDataProviderForGetData
     */
    public function testGetData(
        $sTestConditions,
        $blSeparateShippingAddress,
        array $aBillingData,
        array $aShippingData,
        array $aExpectedReturn
    ) {
        $oUser = $this->_getUserMock($aBillingData, $aShippingData);

        $oShop = $this->getMock('paypPayPalPlusShop', array('getSessionVariable'));
        $oShop->expects($this->any())->method('getSessionVariable')->will(
            $this->returnValue($blSeparateShippingAddress)
        );

        $oCountry                          = $this->getMock('oxCountry', array('__construct', 'load'));
        $oCountry->oxcountry__oxisoalpha2  = new oxField($aBillingData['countryCode']);
        $oCountry2                         = $this->getMock('oxCountry', array('__construct', 'load'));
        $oCountry2->oxcountry__oxisoalpha2 = new oxField($aShippingData['countryCode']);
        $oState                            = $this->getMock('oxState', array('__construct', 'load'));
        $oState->oxstates__oxisoalpha2     = new oxField($aBillingData['stateCode']);
        $oState2                           = $this->getMock('oxState', array('__construct', 'load'));
        $oState2->oxstates__oxisoalpha2    = new oxField($aShippingData['stateCode']);

        $this->SUT = $this->getMock(
            'paypPayPalPlusUserData',
            array('__construct', '__call', 'getShop', '_getSourceObject')
        );
        $this->SUT->expects($this->any())->method('getShop')->will($this->returnValue($oShop));
        $this->SUT->expects($this->any())->method('_getSourceObject')->will(
            $this->onConsecutiveCalls($oUser, $oCountry, $oState, $oCountry2, $oState2)
        );

        $this->assertEquals($aExpectedReturn, $this->SUT->getData(), $sTestConditions);
    }

    /**
     * Data provider for testing `getData`
     *
     * @return array
     */
    public function UserDataDataProviderForGetData()
    {
        return array(
            array(
                'Billing and shipping are the same',
                false,
                array(
                    'oxfname'     => 'John',
                    'oxlname'     => 'Smith',
                    'oxaddinfo'   => 'Billing addinfo',
                    'oxcity'      => 'Billing city',
                    'oxzip'       => '123',
                    'oxfon'       => '',
                    'oxstreet'    => 'Billing street',
                    'oxstreetnr'  => '123',
                    'countryCode' => 'DE',
                    'stateCode'   => '',
                ),
                array(
                    'oxfname'     => '',
                    'oxlname'     => '',
                    'oxaddinfo'   => '',
                    'oxcity'      => '',
                    'oxzip'       => '',
                    'oxfon'       => '',
                    'oxstreet'    => '',
                    'oxstreetnr'  => '',
                    'countryCode' => '',
                    'stateCode'   => '',
                ),
                array(
                    'BillingAddress'  => array(
                        'RecipientName' => 'John Smith',
                        'Line1'         => 'Billing street 123',
                        'Line2'         => 'Billing addinfo',
                        'City'          => 'Billing city',
                        'CountryCode'   => 'DE',
                        'State'         => '',
                        'PostalCode'    => '123',
                        'Phone'         => '',
                    ),
                    'ShippingAddress' => array(
                        'RecipientName' => 'John Smith',
                        'Line1'         => 'Billing street 123',
                        'Line2'         => 'Billing addinfo',
                        'City'          => 'Billing city',
                        'CountryCode'   => 'DE',
                        'State'         => '',
                        'PostalCode'    => '123',
                        'Phone'         => '',
                    ),
                    'FirstName'       => 'John',
                    'LastName'        => 'Smith',
                    'Email'           => 'john@smith.com'
                ),
            ),

            array(
                'Billing and shipping are separated',
                true,
                array(
                    'oxfname'     => 'John',
                    'oxlname'     => 'Smith',
                    'oxaddinfo'   => 'Billing addinfo',
                    'oxcity'      => 'Billing city',
                    'oxzip'       => '123',
                    'oxfon'       => '',
                    'oxstreet'    => 'Billing street',
                    'oxstreetnr'  => '123',
                    'countryCode' => 'DE',
                    'stateCode'   => '',
                ),
                array(
                    'oxfname'     => 'Johnny',
                    'oxlname'     => 'Brown',
                    'oxaddinfo'   => 'Shipping addinfo',
                    'oxcity'      => 'Shipping city',
                    'oxzip'       => '321',
                    'oxfon'       => '',
                    'oxstreet'    => 'Shipping street',
                    'oxstreetnr'  => '321',
                    'countryCode' => 'LT',
                    'stateCode'   => '',
                ),
                array(
                    'BillingAddress'  => array(
                        'RecipientName' => 'John Smith',
                        'Line1'         => 'Billing street 123',
                        'Line2'         => 'Billing addinfo',
                        'City'          => 'Billing city',
                        'CountryCode'   => 'DE',
                        'State'         => '',
                        'PostalCode'    => '123',
                        'Phone'         => '',
                    ),
                    'ShippingAddress' => array(
                        'RecipientName' => 'Johnny Brown',
                        'Line1'         => 'Shipping street 321',
                        'Line2'         => 'Shipping addinfo',
                        'City'          => 'Shipping city',
                        'CountryCode'   => 'LT',
                        'State'         => '',
                        'PostalCode'    => '321',
                        'Phone'         => '',
                    ),
                    'FirstName'       => 'John',
                    'LastName'        => 'Smith',
                    'Email'           => 'john@smith.com'
                ),
            ),

            array(
                'Billing and shipping same, testing string edges',
                false,
                array(
                    'oxfname'     => 'John Eleven Twelve Thirteen Fourteen Fifteen',
                    'oxlname'     => 'Smith One Two Tree Five Seven Eight Nine Ten',
                    'oxaddinfo'   => 'Billing addinfo',
                    'oxcity'      => 'Billing very very very very very very city',
                    'oxzip'       => '123456789123456789123',
                    'oxfon'       => '',
                    'oxstreet'    => 'Billing street',
                    'oxstreetnr'  => '123',
                    'countryCode' => 'DEE',
                    'stateCode'   => '',
                ),
                array(
                    'oxfname'     => '',
                    'oxlname'     => '',
                    'oxaddinfo'   => '',
                    'oxcity'      => '',
                    'oxzip'       => '',
                    'oxfon'       => '',
                    'oxstreet'    => '',
                    'oxstreetnr'  => '',
                    'countryCode' => '',
                    'stateCode'   => '',
                ),
                array(
                    'BillingAddress'  => array(
                        'RecipientName' => 'John Eleven Twelve Thirteen Fourteen Fifteen Smith',
                        'Line1'         => 'Billing street 123',
                        'Line2'         => 'Billing addinfo',
                        'City'          => 'Billing very very very very very very ci',
                        'CountryCode'   => 'DE',
                        'State'         => '',
                        'PostalCode'    => '12345678912345678912',
                        'Phone'         => '',
                    ),
                    'ShippingAddress' => array(
                        'RecipientName' => 'John Eleven Twelve Thirteen Fourteen Fifteen Smith',
                        'Line1'         => 'Billing street 123',
                        'Line2'         => 'Billing addinfo',
                        'City'          => 'Billing very very very very very very ci',
                        'CountryCode'   => 'DE',
                        'State'         => '',
                        'PostalCode'    => '12345678912345678912',
                        'Phone'         => '',
                    ),
                    'FirstName'       => 'John Eleven Twelve Thirteen Fourteen Fifteen',
                    'LastName'        => 'Smith One Two Tree Five Seven Eight Nine Ten',
                    'Email'           => 'john@smith.com'
                ),
            ),
        );
    }

    /**
     * Get user mock with fields and address object set by billing and shipping data
     *
     * @param $aBillingData
     * @param $aShippingData
     *
     * @return mixed
     */
    protected function _getUserMock($aBillingData, $aShippingData)
    {
        $oAddress                        = $this->getMock('oxAddress', array('__construct', '__call'));
        $oAddress->oxaddress__oxfname    = new oxField($aShippingData['oxfname']);
        $oAddress->oxaddress__oxlname    = new oxField($aShippingData['oxlname']);
        $oAddress->oxaddress__oxstreet   = new oxField($aShippingData['oxstreet']);
        $oAddress->oxaddress__oxstreetnr = new oxField($aShippingData['oxstreetnr']);
        $oAddress->oxaddress__oxcity     = new oxField($aShippingData['oxcity']);
        $oAddress->oxaddress__oxaddinfo  = new oxField($aShippingData['oxaddinfo']);
        $oAddress->oxaddress__oxzip      = new oxField($aShippingData['oxzip']);
        $oAddress->oxaddress__oxfon      = new oxField($aShippingData['oxfon']);

        $oUser = $this->getMock('oxUser', array('getSelectedAddress'));
        $oUser->expects($this->any())->method('getSelectedAddress')->will($this->returnValue($oAddress));
        $oUser->oxuser__oxusername = new oxField('john@smith.com');
        $oUser->oxuser__oxfname    = new oxField($aBillingData['oxfname']);
        $oUser->oxuser__oxlname    = new oxField($aBillingData['oxlname']);
        $oUser->oxuser__oxaddinfo  = new oxField($aBillingData['oxaddinfo']);
        $oUser->oxuser__oxcity     = new oxField($aBillingData['oxcity']);
        $oUser->oxuser__oxzip      = new oxField($aBillingData['oxzip']);
        $oUser->oxuser__oxfon      = new oxField($aBillingData['oxfon']);
        $oUser->oxuser__oxstreet   = new oxField($aBillingData['oxstreet']);
        $oUser->oxuser__oxstreetnr = new oxField($aBillingData['oxstreetnr']);

        return $oUser;
    }
}
