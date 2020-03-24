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
 * Class paypPayPalPlusModuleTest
 * Tests for paypPayPalPlusModule PayPal Plus module model.
 *
 * @see paypPayPalPlusModule
 */
class paypPayPalPlusModuleTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusModule
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock('paypPayPalPlusModule', array('__call'));
    }


    public function testConstructor()
    {
        $this->assertSame('payppaypalplus', $this->SUT->getId());
        $this->assertSame('PayPal Plus', $this->SUT->getTitle());
    }


    public function testOnActivate()
    {
        $oEvent = $this->getMock('paypPayPalPlusEvents', array('__call', 'activate', 'deactivate'));
        $oEvent->expects($this->once())->method('activate');
        $oEvent->expects($this->never())->method('deactivate');

        \OxidEsales\Eshop\Core\Registry::set(\OxidProfessionalServices\PayPalPlus\Core\PaypalPlusEvents::class, $oEvent);

        $this->SUT->onActivate();
    }


    public function testOnDeactivate()
    {
        $oEvent = $this->getMock('paypPayPalPlusEvents', array('__call', 'activate', 'deactivate'));
        $oEvent->expects($this->never())->method('activate');
        $oEvent->expects($this->once())->method('deactivate');

        \OxidEsales\Eshop\Core\Registry::set(\OxidProfessionalServices\PayPalPlus\Core\PaypalPlusEvents::class, $oEvent);

        $this->SUT->onDeactivate();
    }
}
