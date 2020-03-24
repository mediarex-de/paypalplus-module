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
 * Class Admin_paypPayPalPlusLanguage_Main.
 *
 * @see Admin_paypPayPalPlusLanguage_Main
 */
class Admin_paypPayPalPlusLanguage_MainTest extends OxidTestCase
{

    /**
     * test `save` method, no edit object id, nothing is done in the extended method
     */
    public function testSave_noEditObjectId_doNothing()
    {
        //set some default test language code value
        $oConfig = oxRegistry::getConfig();
        $aLanguageParams = array('en' => array('payppaypalplus_localecode' => 'def'));
        $oConfig->saveShopConfVar('aarr', 'aLanguageParams', $aLanguageParams);

        $oController = $this->getMock('Admin_paypPayPalPlusLanguage_Main', array('_callParentSave', 'getEditObjectId'));
        $oController->expects($this->once())->method('getEditObjectId')->will($this->returnValue(-1));

        $oController->save();

        $aLanguageParams = $oConfig->getConfigParam('aLanguageParams');
        $this->assertSame('def', $aLanguageParams['en']['payppaypalplus_localecode']);
    }

    /**
     * test `save` method. Empty value is saved too.
     */
    public function testSave_emptyValue_emptyIsSet()
    {
        //set some default test language code value
        $oConfig = oxRegistry::getConfig();
        $aLanguageParams = array('en' => array('payppaypalplus_localecode' => 'def'));
        $oConfig->saveShopConfVar('aarr', 'aLanguageParams', $aLanguageParams);

        $oController = $this->getMock('Admin_paypPayPalPlusLanguage_Main', array('_callParentSave', 'getEditObjectId'));
        $oController->expects($this->once())->method('getEditObjectId')->will($this->returnValue('en'));

        modConfig::setRequestParameter('editval', array('payppaypalplus_localecode' => ''));

        $oController->save();

        $aLanguageParams = $oConfig->getConfigParam('aLanguageParams');

        $this->assertSame('', $aLanguageParams['en']['payppaypalplus_localecode']);
    }

    /**
     * test `save` method. Locale code or any other value is saved. Validation is admin panel user responsibility
     */
    public function testSave_localeCodeIsEntered_localeCodeIsSet()
    {
        //set some default test language code value
        $oConfig = oxRegistry::getConfig();
        $aLanguageParams = array('en' => array('payppaypalplus_localecode' => 'def'));
        $oConfig->saveShopConfVar('aarr', 'aLanguageParams', $aLanguageParams);

        $oController = $this->getMock('Admin_paypPayPalPlusLanguage_Main', array('_callParentSave', 'getEditObjectId'));
        $oController->expects($this->once())->method('getEditObjectId')->will($this->returnValue('en'));

        modConfig::setRequestParameter('editval', array('payppaypalplus_localecode' => 'en_US'));

        $oController->save();

        $aLanguageParams = $oConfig->getConfigParam('aLanguageParams');

        $this->assertSame('en_US', $aLanguageParams['en']['payppaypalplus_localecode']);
    }
}
