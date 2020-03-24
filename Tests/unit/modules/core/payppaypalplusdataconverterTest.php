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
 * Class paypPayPalPlusDataConverterTest
 * Unit tests for paypPayPalPlusDataConverter class.
 *
 * @see paypPayPalPlusDataConverter
 */
class paypPayPalPlusDataConverterTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusDataConverter
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock('paypPayPalPlusDataConverter', array('__call'));
    }


    /**
     * @dataProvider stringDataProvider
     */
    public function testString($sTestingCondition, $mValueToConvert, $iMaxLength, $sExpectedResult)
    {
        $this->assertSame($sExpectedResult, $this->SUT->string($mValueToConvert, $iMaxLength), $sTestingCondition);
    }

    /**
     * Data provider for testing `string`
     *
     * @return array
     */
    public function stringDataProvider()
    {
        return array(
            array('Testing value: null', null, 100, ''),
            array('Testing value: true', true, 100, '1'),
            array('Testing value: false', false, 100, ''),
            array('Testing value: 0', 0, 100, '0'),
            array("Testing value: '0'", '0', 100, '0'),
            array("Testing negative value integer", -5, 100, '-5'),
            array("Testing integer", 123, 100, '123'),
            array("Testing float number", 1.1234, 100, '1.1234'),
            array("Testing negative float number", -14.1234, 100, '-14.1234'),
            array("Testing negative float passed as string", '-14.14', 100, '-14.14'),
            array("Testing integer passed as string", '6', 100, '6'),
            array("Testing empty array", array(), 100, ''),
            array("Testing array with integer", array(1), 100, ''),
            array("Testing object", (object) array(''), 100, ''),
            array("Testing not trimmed string", ' string ', 100, 'string'),
            array("Testing string with whitespace", "\tstring \0", 100, 'string'),
            array("Testing max length", "String ", 1, 'S'),
            array("Testing max length", "String ", 0, ''),
            array("Testing max length with longer strings", " String should be trimmed, removed white space and shortened by this \t", 59, 'String should be trimmed, removed white space and shortened'),
        );
    }

    public function testString_shopIsNotInUtfMode_castEncodingAndConverValue()
    {
        modConfig::getInstance()->setConfigParam('iUtfMode', '0');

        $this->assertSame('ÄÜÖß / äöüß', $this->SUT->string(iconv('UTF-8', 'ISO-8859-15', ' ÄÜÖß / äöüß')));
    }


    /**
     * @dataProvider numberDataProvider
     */
    public function testNumber($sTestingCondition, $mValueToConvert, $sExpectedResult)
    {
        $this->assertSame($sExpectedResult, $this->SUT->number($mValueToConvert), $sTestingCondition);
    }

    /**
     * Data provider for testing `number`
     *
     * @return array
     */
    public function numberDataProvider()
    {
        return array(
            array('Testing value: null', null, '0'),
            array('Testing value: true', true, '1'),
            array('Testing value: false', false, '0'),
            array('Testing value: 0', 0, '0'),
            array("Testing value: '0'", '0', '0'),
            array("Testing negative value integer", -5, '-5'),
            array("Testing integer", 123, '123'),
            array("Testing float number", 1.1234, '1.1234'),
            array("Testing negative float number", -14.1234, '-14.1234'),
            array("Testing negative float passed as string", '-14.14', '-14.14'),
            array("Testing integer passed as string", '6', '6'),
            array("Testing empty array", array(), '0'),
            array("Testing array with integer", array(1), '0'),
            array("Testing object", (object) array(''), '0'),
            array("Testing not trimmed string", ' string ', '0'),
        );
    }


    /**
     * @dataProvider priceDataProvider
     */
    public function testPrice($sTestingCondition, $mValueToConvert, $sExpectedResult)
    {
        $this->assertSame($sExpectedResult, $this->SUT->price($mValueToConvert), $sTestingCondition);
    }

    /**
     * Data provider for testing `price`
     *
     * @return array
     */
    public function priceDataProvider()
    {
        return array(
            array('Testing value: null', null, '0.00'),
            array('Testing value: true', true, '1.00'),
            array('Testing value: false', false, '0.00'),
            array('Testing value: 0', 0, '0.00'),
            array("Testing value: '0'", '0', '0.00'),
            array("Testing negative value integer", -5, '-5.00'),
            array("Testing integer", 123, '123.00'),
            array("Testing float number", 1.1234, '1.12'),
            array("Testing negative float number", -14.1234, '-14.12'),
            array("Testing negative float passed as string", '-14.14', '-14.14'),
            array("Testing integer passed as string", '6', '6.00'),
            array("Testing empty array", array(), '0.00'),
            array("Testing array with integer", array(1), '0.00'),
            array("Testing object", (object) array(''), '0.00'),
            array("Testing not trimmed string", ' string ', '0.00'),
            array("Testing price rounding when .**5", '1.9050 ', '1.91'),
            array("Testing price rounding when -.**5", '-1.9050 ', '-1.91'),
            array("Testing price rounding when .**49", '1.9549 ', '1.95'),
            array("Testing price rounding when -.**49", '-1.9549 ', '-1.95'),
        );
    }


    /**
     * @dataProvider dateDataProvider
     */
    public function testDate($sTestingCondition, $mValueToConvert, $sExpectedResult)
    {
        $this->assertSame($sExpectedResult, $this->SUT->date($mValueToConvert), $sTestingCondition);
    }

    public function dateDataProvider()
    {
        return array(
            array('Null should not be converted to date', null, ''),
            array('True should not be converted to date', true, ''),
            array('False should not be converted to date', false, ''),
            array('Zero is treated as empty value', 0, ''),
            array('Zero as string is treated as empty value', '0', ''),
            array('Text value are treated as empty', 'Some text', ''),
            array('ISO date is parsed as it is', '1990-02-03 15:14:13', '1990-02-03 15:14:13'),
            //todo (nice2have): The following line causes segmentation fault during coverage run.
            //                  Uncomment this test and add more, once date parsing is refactored.
            //array('Other date formats are converted to ISO format', 'July 1 2010 12:12:12', '2010-07-01 12:12:12'),
        );
    }
}
