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
 * Class paypPayPalPlusSuperCfgTest
 * Tests for paypPayPalPlusSuperCfg extended base class.
 *
 * @see paypPayPalPlusSuperCfg
 */
class paypPayPalPlusSuperCfgTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusSuperCfg
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock('paypPayPalPlusSuperCfg', array('__call'));
    }


    public function testGetShop()
    {
        $this->assertInstanceOf('paypPayPalPlusShop', $this->SUT->getShop());
    }


    public function testGetSdk()
    {
        $this->assertInstanceOf('paypPayPalPlusSdk', $this->SUT->getSdk());
    }


    public function testGetNew()
    {
        $this->assertInstanceOf('stdClass', $this->SUT->getNew('stdClass'));
    }


    public function testGetFromRegistry()
    {
        $this->assertInstanceOf('oxSession', $this->SUT->getFromRegistry('oxSession'));
    }


    /**
     * @dataProvider prefixAndMethodNameDataProvider
     */
    public function testParseCallFor($sCondition, $sPrefix, $sMethodName, $mExpectedReturn)
    {
        $this->assertSame($mExpectedReturn, $this->SUT->parseCallFor($sPrefix, $sMethodName), $sCondition);
    }

    public function prefixAndMethodNameDataProvider()
    {
        return array(
            array('Empty prefix', '', 'someMethod', null),
            array('Empty method name', 'get', '', null),
            array('Prefix is longer than method name', 'getValue', 'get', null),
            array('Prefix is same as method name', 'get', 'get', null),
            array('Prefix is not found in the method', 'get', 'setValue', null),
            array('Prefix is not found in the beginning of the method name', 'get', 'justForget', null),
            array('Prefix is found in method name', 'get', 'getSomeValue', 'SomeValue'),
            array('Prefix is found and is long one', 'getItemValue', 'getItemValueOne', 'One'),
        );
    }
}
