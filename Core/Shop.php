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
 * @link      http://www.paypal.com
 * @copyright (C) PayPal (Europe) S.à r.l. et Cie, S.C.A. 2015
 */

namespace OxidEsales\PayPalPlus\Core;

/**
 * Class \OxidEsales\PayPalPlus\Core\Shop
 * OXID eShop wrapper class for shop methods aliases.
 * It also provides getters for most common module core classes from registry.
 */
class Shop extends \OxidEsales\Eshop\Core\Base
{
    /**
     * Get an instance of itself.
     *
     * @return \OxidEsales\PayPalPlus\Core\Shop
     */
    public static function getShop()
    {
        return \OxidEsales\Eshop\Core\Registry::get(__CLASS__);
    }

    /**
     * Get request parameter value.
     *
     * @param string $sKey
     *
     * @return mixed
     */
    public function getRequestParameter($sKey)
    {
        /** @var \OxidEsales\Eshop\Core\Config $oConfig */
        $oConfig = $this->getConfig();

        return $oConfig->getRequestParameter($sKey);
    }

    /**
     * Get OXID eShop configuration parameter value.
     *
     * @param string $sKey
     *
     * @return mixed
     */
    public function getSetting($sKey)
    {
        /** @var \OxidEsales\Eshop\Core\Config $oConfig */
        $oConfig = $this->getConfig();

        return $oConfig->getConfigParam($sKey);
    }

    /**
     * Set OXID eShop session variable.
     *
     * @param string $sKey
     * @param mixed  $mValue
     */
    public function setSessionVariable($sKey, $mValue)
    {
        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable($sKey, $mValue);
    }

    /**
     * Get OXID eShop session variable.
     *
     * @param string $sKey
     *
     * @return mixed
     */
    public function getSessionVariable($sKey)
    {
        return \OxidEsales\Eshop\Core\Registry::getSession()->getVariable($sKey);
    }

    /**
     * Delete OXID eShop session variable.
     *
     * @param string $sKey
     */
    public function deleteSessionVariable($sKey)
    {
        \OxidEsales\Eshop\Core\Registry::getSession()->deleteVariable($sKey);
    }

    /**
     * Get a value of request parameter by key and if it is empty,
     * try to load session parameter value by the same key.
     *
     * @param string $sKey
     *
     * @return mixed
     */
    public function getRequestOrSessionParameter($sKey)
    {
        $mValue = $this->getRequestParameter($sKey);

        if (empty($mValue)) {
            $mValue = $this->getSessionVariable($sKey);
        }

        return $mValue;
    }

    /**
     * Set shop basket to session.
     *
     * @return \OxidEsales\PayPalPlus\Model\Basket|\OxidEsales\Eshop\Application\Model\Basket
     */
    public function setBasket(\OxidEsales\Eshop\Application\Model\Basket $oBasket)
    {
        \OxidEsales\Eshop\Core\Registry::getSession()->setBasket($oBasket);
    }

    /**
     * Get shop basket from session.
     *
     * @return \OxidEsales\PayPalPlus\Model\Basket|\OxidEsales\Eshop\Application\Model\Basket
     */
    public function getBasket()
    {
        return \OxidEsales\Eshop\Core\Registry::getSession()->getBasket();
    }

    /**
     * Get current shop user.
     *
     * @return \OxidEsales\Eshop\Application\Model\User
     */
    public function getUser()
    {
        $oUser = parent::getUser();

        if (!($oUser instanceof \OxidEsales\Eshop\Application\Model\User)) {
            $oUser = $this->getNew(\OxidEsales\Eshop\Application\Model\User::class);
        }

        return $oUser;
    }

    /**
     * An alias for OXID eShop oxNew factory.
     *
     * @param $sClassName
     *
     * @return object
     */
    public function getNew($sClassName)
    {
        return oxNew($sClassName);
    }

    /**
     * An alias for OXID eShop oxRegistry objects getter.
     *
     * @param $sClassName
     *
     * @return object
     */
    public function getFromRegistry($sClassName)
    {
        return \OxidEsales\Eshop\Core\Registry::get($sClassName);
    }

    /**
     * Get OXID eShop database connector.
     *
     * @return oxLegacyDb
     */
    public function getDb()
    {
        return \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);
    }

    /**
     * Get OXID eShop string helper.
     *
     * @return \OxidEsales\Eshop\Core\StrRegular|\OxidEsales\Eshop\Core\StrMb
     */
    public function getStr()
    {
        return \OxidEsales\Eshop\Core\Str::getStr();
    }

    /**
     * Get OXID eShop config instance.
     *
     * @return \OxidEsales\Eshop\Core\Config
     */
    public function getConfig()
    {
        return parent::getConfig();
    }

    /**
     * Get OXID eShop utils instance.
     *
     * @return \OxidEsales\Eshop\Core\Utils
     */
    public function getUtils()
    {
        return \OxidEsales\Eshop\Core\Registry::getUtils();
    }

    /**
     * Get OXID eShop oxLang instance.
     *
     * @return \OxidEsales\Eshop\Core\Language
     */
    public function getLang()
    {
        return \OxidEsales\Eshop\Core\Registry::getLang();
    }

    /**
     * Translate language code.
     *
     * @param string $sCode
     * @param bool   $blAdminMode
     *
     * @return string
     */
    public function translate($sCode, $blAdminMode = false)
    {
        return \OxidEsales\Eshop\Core\Registry::getLang()->translateString($sCode, \OxidEsales\Eshop\Core\Registry::getLang()->getTplLanguage(), $blAdminMode);
    }


    /* ---------------------------- *
     * Recently used module classes *
     * ---------------------------- */

    /**
     * Get loaded PayPal Plus module data class.
     *
     * @return \OxidEsales\PayPalPlus\Core\Events
     */
    public function getPayPalPlusModule()
    {
        return $this->getFromRegistry(\OxidEsales\PayPalPlus\Core\Events::class);
    }

    /**
     * Get PayPal Plus configuration instance.
     *
     * @return \OxidEsales\PayPalPlus\Core\Config
     */
    public function getPayPalPlusConfig()
    {
        return $this->getFromRegistry(\OxidEsales\PayPalPlus\Core\Config::class);
    }

    /**
     * Get initialized PayPal Plus session instance.
     *
     * @return \OxidEsales\PayPalPlus\Core\Session::class
     */
    public function getPayPalPlusSession()
    {
        /** @var \OxidEsales\PayPalPlus\Core\Session $oPayPalSession */
        $oPayPalSession = $this->getFromRegistry(\OxidEsales\PayPalPlus\Core\Session::class);
        $oPayPalSession->init();

        return $oPayPalSession;
    }

    /**
     * Get data access helper instance.
     *
     * @return \OxidEsales\PayPalPlus\Core\DataAccess
     */
    public function getDataAccess()
    {
        return $this->getFromRegistry(\OxidEsales\PayPalPlus\Core\DataAccess::class);
    }

    /**
     * Get data casting and formatting helper instance.
     *
     * @return \OxidEsales\PayPalPlus\Core\DataConverter
     */
    public function getConverter()
    {
        return $this->getFromRegistry(\OxidEsales\PayPalPlus\Core\DataConverter::class);
    }

    /**
     * Get module, payment method and session data validator instance.
     *
     * @return \OxidEsales\PayPalPlus\Core\Validator
     */
    public function getValidator()
    {
        return $this->getFromRegistry(\OxidEsales\PayPalPlus\Core\Validator::class);
    }

    /**
     * Get error handler instance.
     *
     * @return \OxidEsales\PayPalPlus\Core\ErrorHandler
     */
    public function getErrorHandler()
    {
        return $this->getFromRegistry(\OxidEsales\PayPalPlus\Core\ErrorHandler::class);
    }
}
