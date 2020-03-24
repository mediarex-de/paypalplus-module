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
 * Class paypPayPalPlusTaxationHandlerTest
 * Tests for paypPayPalPlusTaxationHandler helper class.
 *
 * @see paypPayPalPlusTaxationHandler
 */
class paypPayPalPlusTaxationHandlerTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusTaxationHandler
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock('paypPayPalPlusTaxationHandler', array('__call'));
    }


    public function testIsB2cShop_showNetPriceOptionIsOff_returnTrue()
    {
        modConfig::getInstance()->setConfigParam('blShowNetPrice', '');

        $this->assertTrue($this->SUT->isB2cShop());
    }

    public function testIsB2cShop_showNetPriceOptionIsOn_returnFalse()
    {
        modConfig::getInstance()->setConfigParam('blShowNetPrice', '1');

        $this->assertFalse($this->SUT->isB2cShop());
    }


    public function testGetConverter()
    {
        $this->assertInstanceOf('paypPayPalPlusDataConverter', $this->SUT->getConverter());
    }


    public function testAdjustedTaxation_netModeIsOn_doesNotChangePriceAndTax()
    {
        modConfig::getInstance()->setConfigParam('blShowNetPrice', '1');

        $oModel = new PayPal\Api\Item();
        $oModel->setPrice('10.00');
        $oModel->setTax('1.90');

        $this->SUT->adjustedTaxation($oModel);

        $this->assertSame('10.00', $oModel->getPrice());
        $this->assertSame('1.90', $oModel->getTax());
    }

    public function testAdjustedTaxation_netModeIsOffButNonPriceModelUsed_doesNotChangeTheModel()
    {
        modConfig::getInstance()->setConfigParam('blShowNetPrice', '0');

        $oModel = new PayPal\Api\Address();
        $oModel->setPhone('10.00');

        $oOriginalModel = clone $oModel;

        $this->SUT->adjustedTaxation($oModel, 'Phone');

        $this->assertSame('10.00', $oModel->getPhone());
        $this->assertEquals($oOriginalModel, $oModel);
    }

    public function testAdjustedTaxation_netModeIsOffItemModelUser_addTaxToPriceAndUnsetTax()
    {
        modConfig::getInstance()->setConfigParam('blShowNetPrice', '');

        $oModel = new PayPal\Api\Item();
        $oModel->setPrice('10.00');
        $oModel->setTax('1.90');

        $this->SUT->adjustedTaxation($oModel);

        $this->assertSame('11.90', $oModel->getPrice());
        $this->assertSame('0.00', $oModel->getTax());
    }

    public function testAdjustedTaxation_netModeIsOffDetailsModelUserButWrongPriceField_doesNotChangeTheModel()
    {
        modConfig::getInstance()->setConfigParam('blShowNetPrice', '');

        $oModel = new PayPal\Api\Details();
        $oModel->setSubtotal('100.00');
        $oModel->setTax('19.00');

        $this->SUT->adjustedTaxation($oModel);

        $this->assertSame('100.00', $oModel->getSubtotal());
        $this->assertSame('19.00', $oModel->getTax());
    }

    public function testAdjustedTaxation_netModeIsOffDetailsModelUserWithProperPriceField_addTaxToPriceAndUnsetTax()
    {
        modConfig::getInstance()->setConfigParam('blShowNetPrice', '');

        $oModel = new PayPal\Api\Details();
        $oModel->setSubtotal('100.00');
        $oModel->setTax('19.00');

        $this->SUT->adjustedTaxation($oModel, 'Subtotal');

        $this->assertSame('119.00', $oModel->getSubtotal());
        $this->assertSame('0.00', $oModel->getTax());
    }

    public function testAdjustedTaxation_taxWasZero_modelPriceAndTaxDoesNotChange()
    {
        modConfig::getInstance()->setConfigParam('blShowNetPrice', '');

        $oModel = new PayPal\Api\Details();
        $oModel->setSubtotal('150.00');
        $oModel->setTax('0.00');

        $this->SUT->adjustedTaxation($oModel, 'Subtotal');

        $this->assertSame('150.00', $oModel->getSubtotal());
        $this->assertSame('0.00', $oModel->getTax());
    }

    public function testAdjustedTaxation_priceWasZero_modelPriceAndTaxDoesNotChange()
    {
        modConfig::getInstance()->setConfigParam('blShowNetPrice', '');

        $oModel = new PayPal\Api\Item();
        $oModel->setPrice('0.00');
        $oModel->setTax('0.00');

        $this->SUT->adjustedTaxation($oModel, 'Price');

        $this->assertSame('0.00', $oModel->getPrice());
        $this->assertSame('0.00', $oModel->getTax());
    }
}
