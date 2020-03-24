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

//test users
$sUserName = 'user@testpppmodule.de';
$sAdminName = 'admin@testpppmodule.de';

//test voucher
$sVoucherSeriesName = 'Test ppp  module voucher';
$sVoucherNr = 'testPPPModuleVoucher';
$iNumberOfVouchers = 3;

//test gift wrapper
$sGiftWrapperId = 'testPPPModuleGiftWrapper';
$sGiftWrapperName = 'test PPP Module Gift Wrapper';
$dGiftWrapperPrice = 2.5;

//test gifting card
$sGiftingCardId = 'testPPPModuleGiftingCard';
$sGiftingCardName = 'test PPP Module Gifting Card';
$dGiftingCardPrice = 3.5;

//test articles
$aArticles = array(
    array(
        'id'     => 'testppp1',
        'title'  => 'Test PPP article 1',
        'number' => 'testppp1',
        'price'  => '15',
    ),
    array(
        'id'     => 'testppp2',
        'title'  => 'Test PPP article 2',
        'number' => 'testppp2',
        'price'  => '30',
    ),
);

//test payment method
$sPaymentId = 'testppppayment';
$sPaymentName = 'Test PPP Module Payment';
$dPaymentPrice = 3;
