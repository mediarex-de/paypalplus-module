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

namespace OxidProfessionalServices\PayPalPlus\Controller;

/**
 * Class \OxidProfessionalServices\PayPalPlus\Controller\BasketController.
 * Overloads Basket controller.
 *
 * @see \OxidEsales\Eshop\Application\Controller\BasketController
 */
class BasketController extends BasketController_parent
{
    /**
     * Overloaded parent method.
     * Payment is being created each time user opens basket page.
     * Created payment instance and basket hash are saved to session.
     * Always assuming PayPal Plus payment method to be used.
     *
     * @return mixed
     */
    public function render()
    {
        $mReturn = $this->_paypPayPalPlusOxBasket_render_parent();

        $oShop = \OxidEsales\PaypalPlus\Core\Shop::getShop();

        if ($oShop->getValidator()->isPaymentPossible()) {
            $oBasket = $oShop->getBasket();
            $oBasket->setPayment($oShop->getPayPalPlusConfig()->getPayPalPlusMethodId());
            $oBasket->calculateBasket(true);
            $oShop->setBasket($oBasket);

            $oPayPalSession = $oShop->getPayPalPlusSession();

            /** @var \OxidProfessionalServices\PayPalPlus\Core\PaymentHandler $oPayPalPaymentHandler */
            $oPayPalPaymentHandler = $oShop->getFromRegistry(\OxidEsales\PaypalPlus\Core\PaymentHandler::class);
            $oPayPalPaymentHandler->init();
            $oPayPalPaymentHandler->create($oPayPalSession->getApiContext());

            $oPayPalSession->setPayment($oPayPalPaymentHandler->getPayment());
            $oPayPalSession->setBasketStamp($oBasket->getBasketHash());
        }

        return $mReturn;
    }

    /**
     * Parent `render` call. Method required for mocking.
     *
     * @codeCoverageIgnore
     *
     * @return mixed
     */
    protected function _paypPayPalPlusOxBasket_render_parent()
    {
        return parent::render();
    }
}
