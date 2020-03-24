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

namespace OxidProfessionalServices\PayPalPlus\Model;

/**
 * Class \OxidProfessionalServices\PayPalPlus\Model\RefundData.
 * PayPal Plus refund data model.
 */
class RefundData extends \OxidEsales\Eshop\Core\Model\BaseModel
{
    /**
     * Class constructor, initiates parent constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->init('payppaypalplusrefund');
    }


    /**
     * Set PayPal Plus related Payment model sale (transaction) ID.
     *
     * @param string $sSaleId
     */
    public function setSaleId($sSaleId)
    {
        $this->payppaypalplusrefund__oxsaleid = new \OxidEsales\Eshop\Core\Field($sSaleId);
    }

    /**
     * Get PayPal Plus related Payment  model sale (transaction) ID.
     *
     * @return string
     */
    public function getSaleId()
    {
        return $this->payppaypalplusrefund__oxsaleid->value;
    }

    /**
     * Set PayPal Plus Refund model ID.
     *
     * @param string $sRefundId
     */
    public function setRefundId($sRefundId)
    {
        $this->payppaypalplusrefund__oxrefundid = new \OxidEsales\Eshop\Core\Field($sRefundId);
    }

    /**
     * Get PayPal Plus Refund model ID.
     *
     * @return string
     */
    public function getRefundId()
    {
        return $this->payppaypalplusrefund__oxrefundid->value;
    }

    /**
     * Set PayPal Plus Refund model status.
     *
     * @param string $sStatus
     */
    public function setStatus($sStatus)
    {
        $this->payppaypalplusrefund__oxstatus = new \OxidEsales\Eshop\Core\Field($sStatus);
    }

    /**
     * Get PayPal Plus Refund model status.
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->payppaypalplusrefund__oxstatus->value;
    }

    /**
     * Set PayPal Plus Refund action date and time.
     *
     * @param string $sDateCreated
     */
    public function setDateCreated($sDateCreated)
    {
        $this->payppaypalplusrefund__oxdatecreated = new \OxidEsales\Eshop\Core\Field($sDateCreated);
    }

    /**
     * Get PayPal Plus Refund action date and time.
     *
     * @return string
     */
    public function getDateCreated()
    {
        return $this->payppaypalplusrefund__oxdatecreated->value;
    }

    /**
     * Set PayPal Plus Refund model total (refunded) amount.
     *
     * @param double $dTotal
     */
    public function setTotal($dTotal)
    {
        $this->payppaypalplusrefund__oxtotal = new \OxidEsales\Eshop\Core\Field((double) $dTotal);
    }

    /**
     * Set PayPal Plus Refund model total (refunded) amount.
     *
     * @return double
     */
    public function getTotal()
    {
        return $this->payppaypalplusrefund__oxtotal->value;
    }

    /**
     * Set PayPal Plus Refund currency code related to the total amount.
     *
     * @param string $sCurrency
     */
    public function setCurrency($sCurrency)
    {
        $this->payppaypalplusrefund__oxcurrency = new \OxidEsales\Eshop\Core\Field($sCurrency);
    }

    /**
     * Get PayPal Plus Refund currency code related to the total amount.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->payppaypalplusrefund__oxcurrency->value;
    }

    /**
     * Set PayPal Plus Refund object serialized.
     *
     * @param \PayPal\Api\Refund $oRefund
     */
    public function setRefundObject(PayPal\Api\Refund $oRefund)
    {
        $this->payppaypalplusrefund__oxrefundobject = new \OxidEsales\Eshop\Core\Field(serialize($oRefund), \OxidEsales\Eshop\Core\Field::T_RAW);
    }

    /**
     * Get PayPal Plus Refund object un-serialized.
     *
     * @return bool|\PayPal\Api\Refund
     */
    public function getRefundObject()
    {
        $oRefundObject = null;
        if ($this->payppaypalplusrefund__oxrefundobject instanceof \OxidEsales\Eshop\Core\Field) {
            $oRefundObject = unserialize(htmlspecialchars_decode($this->payppaypalplusrefund__oxrefundobject->value));
        }

        return $oRefundObject;
    }

    /**
     * Load an instance by refund ID.
     *
     * @param string $sRefundId
     *
     * @return bool
     */
    public function loadByRefundId($sRefundId)
    {
        $sSelect = sprintf(
            "SELECT * FROM `%s` WHERE `OXREFUNDID` = %s",
            $this->getCoreTableName(),
            \OxidProfessionalServices\PayPalPlus\Core\Shop::getShop()->getDb()->quote($sRefundId)
        );

        return $this->assignRecord($sSelect);
    }

    /**
     * Delete all PayPal Plus refunds by sale ID.
     * Used when deleting the payment model entry.
     *
     * @param string $sSaleId
     *
     * @return bool
     */
    public function deleteBySaleId($sSaleId)
    {
        $oDb = \OxidProfessionalServices\PayPalPlus\Core\Shop::getShop()->getDb();

        $sDeleteQuery = sprintf(
            "DELETE FROM `%s` WHERE `OXSALEID` = %s",
            $this->getCoreTableName(),
            $oDb->quote($sSaleId)
        );

        $oDb->execute($sDeleteQuery);

        return (bool) $oDb->affected_Rows();
    }
}
