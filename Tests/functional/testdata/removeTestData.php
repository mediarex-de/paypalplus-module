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

if (\OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getOne("SELECT OXID FROM `oxuser` WHERE OXUSERNAME = '{$sAdminName}'")) {
    removeTestUsers($sUserName, $sAdminName);
    removeTestVoucher($sVoucherSeriesName, $sVoucherNr);
    removeTestGiftWrapper($sGiftWrapperId);
    removeTestGiftingCard($sGiftingCardId);
    removeTestArticles($aArticles);
    removeTestPayment($sPaymentId);
    removeUserOrders($sUserName, $aArticles);

    echo 'Test data was removed successfully';
} else {
    echo 'There is no test data to remove';
}

function removeTestUsers($sTestUserName, $sTestAdminName)
{
    \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute("DELETE FROM `oxuser` WHERE OXUSERNAME IN (?, ?)", array($sTestUserName, $sTestAdminName));
}

function removeTestVoucher($sVoucherSeriesName, $sVoucherNr)
{
    \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute("DELETE FROM `oxvouchers` WHERE OXVOUCHERNR = ?", array($sVoucherNr));
    \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute("DELETE FROM `oxvoucherseries` WHERE OXSERIENR = ?", array($sVoucherSeriesName));
}

function removeTestGiftWrapper($sGiftWrapperId)
{
    \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute("DELETE FROM `oxwrapping` WHERE OXID = ?", array($sGiftWrapperId));
}

function removeTestGiftingCard($sGiftingCardId)
{
    \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute("DELETE FROM `oxwrapping` WHERE OXID = ?", array($sGiftingCardId));
}

function removeTestArticles($aArticles)
{
    foreach ($aArticles as $aArticle) {
        \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute("DELETE FROM `oxarticles` WHERE OXID = ?", array($aArticle['id']));
    }
}

function removeTestPayment($sPaymentId)
{
    \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute("DELETE FROM `oxpayments` WHERE OXID = ?", array($sPaymentId));
    \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute("DELETE FROM `oxobject2payment` WHERE OXPAYMENTID = ?", array($sPaymentId));
}

function removeUserOrders($sTestUserName, $aArticles)
{
    foreach ($aArticles as $aArticle) {
        \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute("DELETE FROM `oxorderarticles` WHERE OXARTID = ?", array($aArticle['id']));
    }

    \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->execute("DELETE FROM `oxorder` WHERE OXBILLEMAIL = ?", array($sTestUserName));
}
