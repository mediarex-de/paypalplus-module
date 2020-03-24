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

require_once dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))) . DIRECTORY_SEPARATOR . 'bootstrap.php';
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'testDataConfig.php';

//Checking if the test data was already imported by the test user name
if (!\OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getOne("SELECT OXID FROM `oxuser` WHERE OXUSERNAME = '{$sAdminName}'")) {
    createTestUser($sUserName);
    createTestUser($sAdminName, true);
    createTestVoucher($sVoucherSeriesName, $sVoucherNr, $iNumberOfVouchers);
    createTestWrapper($sGiftWrapperId, $sGiftWrapperName, $dGiftWrapperPrice, 'WRAP');
    createTestWrapper($sGiftingCardId, $sGiftingCardName, $dGiftingCardPrice, 'CARD');
    createTestArticles($aArticles);
    createTestPayment($sPaymentId, $sPaymentName, $dPaymentPrice);

    echo 'Test data was successfully imported';
} else {
    echo 'Test data is already imported';
}

function createTestUser($sTestUserName, $blIsAdmin = false)
{
    $oUser = oxNew("oxuser");
    $oUser->oxuser__oxactive = new oxField(1);
    $oUser->oxuser__oxrights = new oxField('user');
    $oUser->oxuser__oxshopid = new oxField(oxRegistry::getConfig()->getBaseShopId());
    $oUser->oxuser__oxusername = new oxField($sTestUserName);
    $oUser->setPassword($sTestUserName);
    $oUser->oxuser__oxfname = new oxField('Name');
    $oUser->oxuser__oxlname = new oxField('Surname');
    $oUser->oxuser__oxstreet = new oxField('Street');
    $oUser->oxuser__oxstreetnr = new oxField('123');
    $oUser->oxuser__oxzip = new oxField('12345');
    $oUser->oxuser__oxcity = new oxField('City');
    $oUser->oxuser__oxcountryid = new oxField(\OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getOne('SELECT OXID FROM `oxcountry` WHERE OXACTIVE = 1 LIMIT 1'));
    $oUser->save();

    //assign user to newcustomer group
    $oRelation = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
    $oRelation->init('oxobject2group');
    $oRelation->oxobject2group__oxshopid = new oxField(oxRegistry::getConfig()->getBaseShopId());
    $oRelation->oxobject2group__oxobjectid = new oxField($oUser->getId());
    if ($blIsAdmin) {
        $oRelation->oxobject2group__oxgroupsid = new oxField('oxidadmin');
    } else {
        $oRelation->oxobject2group__oxgroupsid = new oxField('oxidnewcustomer');
    }
    $oRelation->save();

    if ($blIsAdmin) {
        \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute("UPDATE `oxuser` SET OXRIGHTS = 'malladmin' WHERE OXUSERNAME = '$sTestUserName'");
    }
}

function createTestVoucher($sVoucherSeriesName, $sVoucherNr, $iNumberOfVouchers)
{
    $oVoucherSeries = oxNew("oxvoucherserie");
    $oVoucherSeries->oxvoucherseries__oxshopid = new oxField(oxRegistry::getConfig()->getBaseShopId());
    $oVoucherSeries->oxvoucherseries__oxdiscount = new oxField(1);
    $oVoucherSeries->oxvoucherseries__oxserienr = new oxField($sVoucherSeriesName);
    $oVoucherSeries->oxvoucherseries__oxdiscount = new oxField(1);
    $oVoucherSeries->save();

    for ($iCounter = 0; $iCounter <= $iNumberOfVouchers; $iCounter++) {
        $oNewVoucher = oxNew("oxvoucher");
        $oNewVoucher->oxvouchers__oxvoucherserieid = new oxField($oVoucherSeries->getId());
        $oNewVoucher->oxvouchers__oxvouchernr = new oxField($sVoucherNr);
        $oNewVoucher->save();
    }
}

function createTestWrapper($sWrapperId, $sWrapperName, $dWrapperPrice, $sType)
{
    $oWrapping = oxNew("oxwrapping");
    $oWrapping->setLanguage(0);
    $oWrapping->setId($sWrapperId);
    $oWrapping->oxwrapping__oxshopid = new oxField(oxRegistry::getConfig()->getBaseShopId());
    $oWrapping->oxwrapping__oxactive = new oxField(1);
    $oWrapping->oxwrapping__oxtype = new oxField($sType);
    $oWrapping->oxwrapping__oxname = new oxField($sWrapperName);
    $oWrapping->oxwrapping__oxprice = new oxField($dWrapperPrice);
    $oWrapping->save();
}

function createTestArticles($aArticles)
{
    foreach ($aArticles as $aArticle) {
        $oArticle = oxNew( "oxarticle");
        $oArticle->setLanguage(0);
        $oArticle->setId($aArticle['id']);
        $oArticle->oxarticles__oxactive = new oxField(1);
        $oArticle->oxarticles__oxshopid = new oxField(oxRegistry::getConfig()->getBaseShopId());
        $oArticle->oxarticles__oxtitle = new oxField($aArticle['title']);
        $oArticle->oxarticles__oxartnum = new oxField($aArticle['number']);
        $oArticle->oxarticles__oxprice = new oxField($aArticle['price']);
        $oArticle->save();

        //set name for all languages
        /** @var oxLang $oLanguage */
        $oLanguage = oxRegistry::getLang();
        $aLanguages = $oLanguage->getLanguageIds();

        foreach ($aLanguages as $iLanguageId => $sAbbreviation) {
            $oArticle->setLanguage($iLanguageId);
            $oArticle->oxarticles__oxtitle = new oxField($aArticle['title']);
            $oArticle->save();
        }
    }
}

//TODO: make this function nicer with less repeating
function createTestPayment($sPaymentId, $sPaymentName, $dPaymentPrice)
{
    $oPayment = oxNew( "oxpayment" );
    $oPayment->setId($sPaymentId);
    $oPayment->setLanguage(0);
    $oPayment->oxpayments__oxshopid = new oxField(oxRegistry::getConfig()->getBaseShopId());
    $oPayment->oxpayments__oxdesc = new oxField($sPaymentName);
    $oPayment->oxpayments__oxactive = new oxField(1);
    $oPayment->oxpayments__oxaddsumtype = new oxField('abs');
    $oPayment->oxpayments__oxaddsum = new oxField($dPaymentPrice);
    $oPayment->oxpayments__oxtoamount = new oxField(99999999);
    $oPayment->save();

    //assign all countries
    /** @var oxList $oCountries */
    $oCountries = oxNew(\OxidEsales\Eshop\Core\Model\ListModel::class);
    $oCountries->init('oxCountry');
    $oCountries->selectString(
        sprintf(
            "SELECT * FROM `%s` WHERE `OXACTIVE` = 1",
            getViewName('oxcountry')
        )
    );

    if ($oCountries->count()) {
        foreach ($oCountries as $oObject) {
            /** @var oxBase $oRelation */
            $oRelation = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
            $oRelation->init('oxobject2payment');
            $oRelation->oxobject2payment__oxpaymentid = new oxField($sPaymentId);
            $oRelation->oxobject2payment__oxobjectid = new oxField($oObject->getId());
            $oRelation->oxobject2payment__oxtype = new oxField('oxcountry');
            $oRelation->save();
        }
    }

    //assign all shipping methods
    /** @var oxDeliverySetList $oShippingMethods */
    $oShippingMethods = oxNew(\OxidEsales\Eshop\Application\Model\DeliverySetList::class);
    $oShippingMethods->selectString(sprintf("SELECT * FROM `%s` WHERE 1", getViewName('oxdeliveryset')));

    if ($oShippingMethods->count()) {
        foreach ($oShippingMethods as $oObject) {
            /** @var oxBase $oRelation */
            $oRelation = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
            $oRelation->init('oxobject2payment');
            $oRelation->oxobject2payment__oxpaymentid = new oxField($sPaymentId);
            $oRelation->oxobject2payment__oxobjectid = new oxField($oObject->getId());
            $oRelation->oxobject2payment__oxtype = new oxField('oxdelset');
            $oRelation->save();
        }
    }
}
