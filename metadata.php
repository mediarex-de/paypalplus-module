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
 * @copyright (C) PayPal (Europe) S.à r.l. et Cie, S.C.A. 2015
 */

/**
 * Metadata version
 */
$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = [
    'id'          => 'payppaypalplus',
    'title'       => 'PayPal Plus',
    'description' => [
        'de'      => 'PayPal Plus Bezahlmodul für OXID eShop',
        'en'      => 'PayPal Plus payments module for OXID eShop',
    ],
    'thumbnail'   => 'out/pictures/payppaypalplus.png',
    'version'     => '4.0.0',
    'author'      => 'PayPal (Europe) S.à r.l. et Cie, S.C.A.',
    'url'         => 'https://www.paypal.com',
    'email'       => 'service@paypal.com',
    'extend'      => [

        // Controller
        \OxidEsales\Eshop\Application\Controller\Admin\LanguageMain::class          => \OxidEsales\PayPalPlus\Controller\Admin\LanguageMain::class,
        \OxidEsales\Eshop\Application\Controller\Admin\OrderList::class             => \OxidEsales\PayPalPlus\Controller\Admin\OrderList::class,
        \OxidEsales\Eshop\Application\Controller\Admin\ModuleConfiguration::class   => \OxidEsales\PayPalPlus\Controller\Admin\ModuleConfiguration::class,
        \OxidEsales\Eshop\Application\Controller\BasketController::class            => \OxidEsales\PayPalPlus\Controller\BasketController::class,
        \OxidEsales\Eshop\Application\Controller\OrderController::class             => \OxidEsales\PayPalPlus\Controller\OrderController::class,
        \OxidEsales\Eshop\Application\Controller\PaymentController::class           => \OxidEsales\PayPalPlus\Controller\PaymentController::class,
        \OxidEsales\Eshop\Application\Controller\ThankYouController::class          => \OxidEsales\PayPalPlus\Controller\ThankYouController::class,

        // Core
        \OxidEsales\Eshop\Core\ViewConfig::class                                    => \OxidEsales\PayPalPlus\Core\ViewConfig::class,

        // Model
        \OxidEsales\Eshop\Application\Model\Address::class                          => \OxidEsales\PayPalPlus\Model\Address::class,
        \OxidEsales\Eshop\Application\Model\Basket::class                           => \OxidEsales\PayPalPlus\Model\Basket::class,
        \OxidEsales\Eshop\Application\Model\Order::class                            => \OxidEsales\PayPalPlus\Model\Order::class,
        \OxidEsales\Eshop\Application\Model\PaymentGateway::class                   => \OxidEsales\PayPalPlus\Model\PaymentGateway::class,
        \OxidEsales\Eshop\Application\Model\User::class                             => \OxidEsales\PayPalPlus\Model\User::class,
    ],
    'controllers'       => [

        // Component
        'payppaypalpluswall'                                                => \OxidEsales\PayPalPlus\Component\Widget\WallWidget::class,

        // Controller
        'admin_payppaypalplusordertab'                                      => \OxidEsales\PayPalPlus\Controller\Admin\OrderTab::class,
        'payppaypalpluswebhook'                                             => \OxidEsales\PayPalPlus\Controller\Webhook::class,

        // Core
        'payppaypalplusnoorderexception'                                    => \OxidEsales\PayPalPlus\Core\Exception\NoOrderException::class,
        'payppaypalplusnopaymentfoundexception'                             => \OxidEsales\PayPalPlus\Core\Exception\NoPaymentFoundException::class,
        'payppaypalpluspaymentdatasaveexception'                            => \OxidEsales\PaypalPlus\Core\Exception\PaymentDataSaveException::class,
        'payppaypalplusrefundexception'                                     => \OxidEsales\PayPalPlus\Core\Exception\RefundException::class,
        'payppaypalplusconfig'                                              => \OxidEsales\PayPalPlus\Core\Config::class,
        'payppaypalplusdataaccess'                                          => \OxidEsales\PayPalPlus\Core\DataAccess::class,
        'payppaypalplusdataconverter'                                       => \OxidEsales\PayPalPlus\Core\DataConverter::class,
        'payppaypalpluserrorhandler'                                        => \OxidEsales\PayPalPlus\Core\ErrorHandler::class,
        'payppaypalplusevents'                                              => \OxidEsales\PayPalPlus\Core\PaypalPlusEvents::class,
        'payppaypalplusinvoicepdfarticlesummary'                            => \OxidEsales\PayPalPlus\Core\InvoicePdfArticleSummary::class,
        'payppaypalpluspdfarticlesummarypaymentinstructions'                => \OxidEsales\PayPalPlus\Core\PdfArticleSummaryPaymentInstructions::class,
        'payppaypalpluspaymenthandler'                                      => \OxidEsales\PayPalPlus\Core\PaymentHandler::class,
        'payppaypalpluspdfarticlesummary'                                   => \OxidEsales\PayPalPlus\Core\PdfArticleSummary::class,
        'payppaypalplusrefundhandler'                                       => \OxidEsales\PayPalPlus\Core\RefundHandler::class,
        'payppaypalpluswebprofilehandler'                                   => \OxidEsales\PayPalPlus\Core\WebProfileHandler::class,
        'payppaypalplussdk'                                                 => \OxidEsales\PayPalPlus\Core\Sdk::class,
        'payppaypalplussession'                                             => \OxidEsales\PayPalPlus\Core\Session::class,
        'payppaypalplusshop'                                                => \OxidEsales\PayPalPlus\Core\Shop::class,
        'payppaypalplussupercfg'                                            => \OxidEsales\PayPalPlus\Core\SuperCfg::class,
        'payppaypalplustaxationhandler'                                     => \OxidEsales\PayPalPlus\Core\TaxationHandler::class,
        'payppaypalplusvalidator'                                           => \OxidEsales\PayPalPlus\Core\Validator::class,

        // Model
        'payppaypalplusbasketdata'                                          => \OxidEsales\PayPalPlus\Model\BasketData::class,
        'payppaypalplusbasketitemdata'                                      => \OxidEsales\PayPalPlus\Model\BasketItemData::class,
        'payppaypalplusdataprovider'                                        => \OxidEsales\PayPalPlus\Model\DataProvider::class,
        'payppaypalpluspaymentdata'                                         => \OxidEsales\PayPalPlus\Model\PaymentData::class,
        'payppaypalpluspaymentdataprovider'                                 => \OxidEsales\PayPalPlus\Model\PaymentDataProvider::class,
        'payppaypalplusprofile'                                             => \OxidEsales\PayPalPlus\Model\Profile::class,
        'payppaypalpluspuidata'                                             => \OxidEsales\PayPalPlus\Model\PuiData::class,
        'payppaypalpluspuidataprovider'                                     => \OxidEsales\PayPalPlus\Model\PuiDataProvider::class,
        'payppaypalplusrefunddata'                                          => \OxidEsales\PayPalPlus\Model\RefundData::class,
        'payppaypalplusrefunddatalist'                                      => \OxidEsales\PayPalPlus\Model\RefundDataList::class,
        'payppaypalplusrefunddataprovider'                                  => \OxidEsales\PayPalPlus\Model\RefundDataProvider::class,
        'payppaypalplususerdata'                                            => \OxidEsales\PayPalPlus\Model\UserData::class
    ],
    'templates'   => [
        'payppaypalpluswall.tpl'  => 'payp/paypalplus/views/widgets/payppaypalpluswall.tpl',
        'payppaypalplusorder.tpl' => 'payp/paypalplus/views/admin/tpl/payppaypalplusorder.tpl',
        'page/webhook/response.tpl' => 'payp/paypalplus/views/tpl/page/webhook/response.tpl'
    ],
    'blocks'      => [
        [
            'template' => 'page/checkout/inc/payment_other.tpl',
            'block'    => 'checkout_payment_longdesc',
            'file'     => 'views/blocks/payppaypalplus_payment_description.tpl'],
        [
            'template' => 'page/checkout/thankyou.tpl',
            'block'    => 'checkout_thankyou_info',
            'file'     => 'views/blocks/payppaypalplus_checkout_thankyou_info.tpl'],
        [
            'template' => 'page/checkout/order.tpl',
            'block'    => 'shippingAndPayment',
            'file'     => 'views/blocks/payppaypalplus_order_payment.tpl'],
        [
            'template' => 'email/html/order_cust.tpl',
            'block'    => 'email_html_order_cust_orderemailend',
            'file'     => 'views/blocks/payppaypalplus_email_html_order_cust_orderemailend.tpl'],
        [
            'template' => 'email/plain/order_cust.tpl',
            'block'    => 'email_plain_order_cust_orderemailend',
            'file'     => 'views/blocks/payppaypalplus_email_plain_order_cust_orderemailend.tpl'],
        [
            'template' => 'language_main.tpl',
            'block'    => 'admin_language_main_form',
            'file'     => 'views/blocks/payppaypalplus_admin_language_main_form.tpl'],
        [
            'template' => 'order_list.tpl',
            'block'    => 'admin_order_list_filter',
            'file'     => '/views/blocks/payppaypalplus_admin_order_list_filter_actions.tpl'],
        [
            'template' => 'order_list.tpl',
            'block'    => 'admin_order_list_sorting',
            'file'     => '/views/blocks/payppaypalplus_admin_order_list_sorting_actions.tpl'],
        [
            'template' => 'order_list.tpl',
            'block'    => 'admin_order_list_item',
            'file'     => '/views/blocks/payppaypalplus_admin_order_list_items_actions.tpl'],
        [
            'template' => 'order_list.tpl',
            'block'    => 'admin_order_list_colgroup',
            'file'     => '/views/blocks/payppaypalplus_admin_order_list_colgroup_actions.tpl'],
        [
            'template' => 'module_config.tpl',
            'block'    => 'admin_module_config_form',
            'file'     => '/views/blocks/paypalplus_admin_module_config_form.tpl']
    ],
    'settings'    => [
        [
            'group' => 'paypPayPalPlusApi',
            'name'  => 'paypPayPalPlusClientId',
            'type'  => 'str',
            'value' => ''
        ],
        [
            'group' => 'paypPayPalPlusApi',
            'name'  => 'paypPayPalPlusSecret',
            'type'  => 'str',
            'value' => ''
        ],
        [
            'group' => 'paypPayPalPlusSandbox',
            'name'  => 'paypPayPalPlusSandbox',
            'type'  => 'bool',
            'value' => false
        ],
        [
            'group' => 'paypPayPalPlusSandbox',
            'name'  => 'paypPayPalPlusSandboxClientId',
            'type'  => 'str',
            'value' => ''
        ],
        [
            'group' => 'paypPayPalPlusSandbox',
            'name'  => 'paypPayPalPlusSandboxSecret',
            'type'  => 'str',
            'value' => ''
        ],

        /** Common integration settings **/

        [
            'group' => 'paypPayPalPlusIntegration',
            'name'  => 'paypPayPalPlusExternalMethods',
            'type'  => 'arr',
            'value' => array('oxidinvoice', 'oxidpayadvance', 'oxidcashondel', 'oxempty')
        ],
        [
            'group' => 'paypPayPalPlusIntegration',
            'name'  => 'paypPayPalPlusValidateTemplate',
            'type'  => 'bool',
            'value' => true
        ],

        /** Settings for template integration **/

        [
            'group' => 'paypPayPalPlusTemplateIntegration',
            'name'  => 'paypPayPalPlusNextButtonId',
            'type'  => 'str',
            'value' => 'paymentNextStepBottom'
        ],
        [
            'group' => 'paypPayPalPlusTemplateIntegration',
            'name'  => 'paypPayPalPlusNextLink',
            'type'  => 'str',
            'value' => 'a#orderStep'
        ],
        [
            'group' => 'paypPayPalPlusTemplateIntegration',
            'name'  => 'paypPayPalPlusNextLinkParent',
            'type'  => 'str',
            'value' => 'span'
        ],
        [
            'group' => 'paypPayPalPlusTemplateIntegration',
            'name'  => 'paypPayPalPlusPaymentRadio',
            'type'  => 'str',
            'value' => 'input[name="paymentid"]'
        ],
        [
            'group' => 'paypPayPalPlusTemplateIntegration',
            'name'  => 'paypPayPalPlusListItem',
            'type'  => 'str',
            'value' => 'dl'
        ],
        [
            'group' => 'paypPayPalPlusTemplateIntegration',
            'name'  => 'paypPayPalPlusListItemTitle',
            'type'  => 'str',
            'value' => 'dt'
        ],
        [
            'group' => 'paypPayPalPlusTemplateIntegration',
            'name'  => 'paypPayPalPlusLabelFormat',
            'type'  => 'str',
            'value' => 'label[for="payment_%s"]'
        ],
        [
            'group' => 'paypPayPalPlusTemplateIntegration',
            'name'  => 'paypPayPalPlusLabelChild',
            'type'  => 'str',
            'value' => 'b'
        ],
        [
            'group' => 'paypPayPalPlusTemplateIntegration',
            'name'  => 'paypPayPalPlusDescription',
            'type'  => 'str',
            'value' => 'div.desc'
        ],
        [
            'group' => 'paypPayPalPlusTemplateIntegration',
            'name'  => 'paypPayPalPlusMethodIdPrefix',
            'type'  => 'str',
            'value' => 'payment_'
        ],

        /** Settings for Mobile template integration **/

        [
            'group' => 'paypPayPalPlusMobIntegration',
            'name'  => 'paypPayPalPlusMobNextButtonId',
            'type'  => 'str',
            'value' => 'paymentNextStepBottom'
        ],
        [
            'group' => 'paypPayPalPlusMobIntegration',
            'name'  => 'paypPayPalPlusMobNextLink',
            'type'  => 'str',
            'value' => 'a#orderStep'
        ],
        [
            'group' => 'paypPayPalPlusMobIntegration',
            'name'  => 'paypPayPalPlusMobNextLinkParent',
            'type'  => 'str',
            'value' => 'li'
        ],
        [
            'group' => 'paypPayPalPlusMobIntegration',
            'name'  => 'paypPayPalPlusMobPaymentRadio',
            'type'  => 'str',
            'value' => 'input[name="paymentid"]'
        ],
        [
            'group' => 'paypPayPalPlusMobIntegration',
            'name'  => 'paypPayPalPlusMobListItem',
            'type'  => 'str',
            'value' => '#paymentMethods ul.dropdown-menu li'
        ],
        [
            'group' => 'paypPayPalPlusMobIntegration',
            'name'  => 'paypPayPalPlusMobListItemTitle',
            'type'  => 'str',
            'value' => 'a'
        ],
        [
            'group' => 'paypPayPalPlusMobIntegration',
            'name'  => 'paypPayPalPlusMobLabelFormat',
            'type'  => 'str',
            'value' => 'a[data-selection-id="%s"]'
        ],
        [
            'group' => 'paypPayPalPlusMobIntegration',
            'name'  => 'paypPayPalPlusMobDescription',
            'type'  => 'str',
            'value' => 'div[id="paymentOption_%s"] div.payment-desc'
        ],
        [
            'group' => 'paypPayPalPlusMobIntegration',
            'name'  => 'paypPayPalPlusMobMethodIdPrefix',
            'type'  => 'str',
            'value' => 'payment_'
        ],

        /** Settings for Flow template integration **/

        [
            'group' => 'paypPayPalPlusFlowIntegration',
            'name'  => 'paypPayPalPlusFlowNextButtonId',
            'type'  => 'str',
            'value' => 'paymentNextStepBottom'
        ],
        [
            'group' => 'paypPayPalPlusFlowIntegration',
            'name'  => 'paypPayPalPlusFlowNextLink',
            'type'  => 'str',
            'value' => 'a#orderStep'
        ],
        [
            'group' => 'paypPayPalPlusFlowIntegration',
            'name'  => 'paypPayPalPlusFlowNextLinkParent',
            'type'  => 'str',
            'value' => 'li'
        ],
        [
            'group' => 'paypPayPalPlusFlowIntegration',
            'name'  => 'paypPayPalPlusFlowPaymentRadio',
            'type'  => 'str',
            'value' => 'input[name="paymentid"]'
        ],
        [
            'group' => 'paypPayPalPlusFlowIntegration',
            'name'  => 'paypPayPalPlusFlowListItem',
            'type'  => 'str',
            'value' => '.panel-body .well'
        ],
        [
            'group' => 'paypPayPalPlusFlowIntegration',
            'name'  => 'paypPayPalPlusFlowListItemTitle',
            'type'  => 'str',
            'value' => 'dt'
        ],
        [
            'group' => 'paypPayPalPlusFlowIntegration',
            'name'  => 'paypPayPalPlusFlowLabelFormat',
            'type'  => 'str',
            'value' => 'label[for="payment_%s"]'
        ],
        [
            'group' => 'paypPayPalPlusFlowIntegration',
            'name'  => 'paypPayPalPlusFlowDescription',
            'type'  => 'str',
            'value' => 'div.desc'
        ],
        [
            'group' => 'paypPayPalPlusFlowIntegration',
            'name'  => 'paypPayPalPlusFlowMethodIdPrefix',
            'type'  => 'str',
            'value' => 'payment_'
        ],

        /** Logging debugging and connectivity **/

        [
            'group' => 'paypPayPalPlusOther',
            'name'  => 'paypPayPalPlusLogEnabled',
            'type'  => 'bool',
            'value' => true
        ],
        [
            'group' => 'paypPayPalPlusOther',
            'name'  => 'paypPayPalPlusLogFile',
            'type'  => 'str',
            'value' => 'paypalplus.log'
        ],
        [
            'group'      => 'paypPayPalPlusOther',
            'name'       => 'paypPayPalPlusLogLevel',
            'type'       => 'select',
            'constrains' => 'DEBUG|INFO|WARN|ERROR',
            'value'      => 'INFO'
        ],
        [
            'group'      => 'paypPayPalPlusOther',
            'name'       => 'paypPayPalPlusValidation',
            'type'       => 'select',
            'constrains' => 'log|strict|disabled',
            'value'      => 'log'
        ],
        [
            'group' => 'paypPayPalPlusOther',
            'name'  => 'paypPayPalPlusTimeout',
            'type'  => 'num',
            'value' => 60
        ],
        [
            'group' => 'paypPayPalPlusOther',
            'name'  => 'paypPayPalPlusRetry',
            'type'  => 'num',
            'value' => 1
        ],
        [
            'group' => 'paypPayPalPlusOther',
            'name'  => 'paypPayPalPlusDebug',
            'type'  => 'bool',
            'value' => false
        ],
        [
            'group' => 'paypPayPalPlusOther',
            'name'  => 'paypPayPalPlusSaveToFile',
            'type'  => 'bool',
            'value' => false
        ],

        /** Other settings **/

        [
            'group' => 'paypPayPalPlusPUI',
            'name'  => 'paypPayPalPlusDiscountRefunds',
            'type'  => 'bool',
            'value' => true
        ],
        [
            'group' => 'paypPayPalPlusPUI',
            'name'  => 'paypPayPalPlusRefundOnInvoice',
            'type'  => 'bool',
            'value' => false
        ],
        [
            'group' => 'paypPayPalPlusPUI',
            'name'  => 'paypPayPalPlusShopOwnerStr',
            'type'  => 'str',
            'value' => ''
        ],
        [
            'group' => 'paypPayPalPlusPUI',
            'name'  => 'paypPayPalPlusInvNr',
            'type'  => 'bool',
            'value' => false
        ],

        /** Paypal Payment Experience settings **/

        [
            'group' => 'paypPayPalPlusExperience',
            'name'  => 'paypPayPalPlusExpProfileId',
            'type'  => 'str',
            'value' => ''
        ],
        [
            'group' => 'paypPayPalPlusExperience',
            'name'  => 'paypPayPalPlusExpName',
            'type'  => 'str',
            'value' => ''
        ],
        [
            'group' => 'paypPayPalPlusExperience',
            'name'  => 'paypPayPalPlusExpBrand',
            'type'  => 'str',
            'value' => ''
        ],
        [
            'group' => 'paypPayPalPlusExperience',
            'name'  => 'paypPayPalPlusExpLogo',
            'type'  => 'str',
            'value' => ''
        ],
        [
            'group' => 'paypPayPalPlusExperience',
            'name'  => 'paypPayPalPlusExpLocale',
            'type'  => 'str',
            'value' => ''
        ]
    ],
    'events'      => [
        'onActivate'   => '\OxidEsales\PayPalPlus\Core\Events::onActivate',
        'onDeactivate' => '\OxidEsales\PayPalPlus\Core\Events::onDeactivate'
    ]
];