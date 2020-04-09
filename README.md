# PayPal Plus payments module for OXID eShop

PayPal Plus module for OXID eShop.

## Installation
Use Composer to add the module to your project
```bash
composer require oxid-professional-services/paypalplus-module
```

* Activate PayPal Plus module in administration back end: under _Extensions -> Modules -> PayPal Plus_, tab _Overview_ press _Activate_ button
* Enter PayPal API Client ID and Secret key and adjust other settings in _Extensions -> Modules -> PayPal Plus_, tab _Settings_
* Optionally configure eShop shipping methods and shipping cost rules

## Versions
* Module v3.0.*
* PayPal-PHP-SDK v1.13.0

## Supported OXID eShop version
* 6.0.*
* 6.1.*
* 6.2.*

## For developers
* PayPal plus payment acts as payment container. See `views/blocks/oxpspaypalplus_payment_select_payment.tpl`. At the moment we exclude even "empty/free" payment.
* It is very important to validate html elements/hooks on template. Otherwise payment fails to render.
Validation is done on `out/src/js/oxpspaypalpluswall.js:validateDomElements`. Some tags must be parent to the other.
* Error variable placeholder `%s` is meant for use in both, PHP and JS.
* There are multiple validations on order, JS, payment levels. As a result some errors are logged, some are meant for display and some are both logged and displayed on the front end.
* PayPalPlus payment handler validates phone number to meet E.123 standard. See `\oxpsPayPalPlusPaymentHandler::validateUserData()`. And it is active when attempting to pay by PayPal Plus methods.
* JS payment validation extracts labels and description from html, because this is only way to take changes made by other modules.
* Payments not registered on PayPal Plus should be visible on payment step. Because there are payments developed by partners.
* After developing run PHP and JS test. See `modules/oxps/paypalplus/tests/unit` and `modules/oxps/paypalplus/tests/functional/README.md`.
* It is better to have PayPal test accounts (private and business) registered in Germany. E.g. accounts with currency USD ends up "pending" payments on admin panel. As a result refund functionality can not be used.
* Credit cards for testing purposes `https://www.paypalobjects.com/en_US/vhelp/paypalmanager_help/credit_card_numbers.html`

## Link
https://www.paypal.com

## Mail
service@paypal.com

## Extend
 * language_main
    -- save
 * order_list
    -- render
    -- _prepareWhereQuery
    -- _buildSelectString
    -- _prepareOrderByQuery
 * basket
    -- render
 * order
    -- init
 * payment
    -- render
    -- validatePayment
    -- getPaymentErrorText
 * oxviewconfig
 * oxAddress
    -- save
 * oxBasket
    -- afterUpdate
    -- getPaymentCost
    -- getTotalDiscountSum
    -- getTsProductId
 * oxOrder
    -- save
    -- delete
 * oxPaymentGateway
    -- executePayment
 * oxUser
     -- save
 * thankyou
    -- init
    -- render
