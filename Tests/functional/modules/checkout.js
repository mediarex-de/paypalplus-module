exports.goToPaymentPage = function () {
    casper.thenOpen(config.mainUrl + config.checkout.paymentUrl, function () {
        this.test.assertExists('#content', 'Checkout page accessed');
    });

    casper.then(function () {
        this.test.assertExists(config.checkout.shippingFormSelector, 'Shipping methods form exists');

        this.test.assertEval(function (paymentMethodSelector) {
            return __utils__.findAll(paymentMethodSelector).length >= 1;
        }, 'At least one payment method was found', {paymentMethodSelector: config.checkout.paymentMethodSelector});
    });
};


exports.payPalPlusWallIsVisible = function () {
    casper.then(function () {
        this.capture('screenshots/ppp_payments_page.png');
        this.test.assertExists('#payment_payppaypalplus', 'PayPal Plus payment method exists');
        this.click('#payment_payppaypalplus');
        this.capture('screenshots/ppp_payments_wall.png');
        this.test.assertExists('#paypPayPalPlusWall', 'PayPal Plus Wall wrapper exists');
        this.test.assertExists('#paypPayPalPlusWall iframe', 'PayPal Plus Wall frame exists');
    });
};

//TODO: continue to next step action should go into separate function
exports.selectPaymentAndContinue = function (paymentId) {
    casper.then(function () {
        this.test.assertExists('form#payment input[value="' + paymentId + '"]', 'Payment with id ' + paymentId + ' exists');
        this.click('form#payment input[value="' + paymentId + '"]');
        this.echo('Payment with id ' + paymentId + ' was selected');
    });

    casper.then(function () {
        this.click(config.checkout.nextStepButtonSelector);
        this.echo('Continue to next step button was clicked');
    });
};

exports.selectFromPayPalWallPayPalMethodAndContinue = function () {
    casper.withFrame(config.payPalWall.frameNumber, function () {
        this.click('img[alt="PayPal"]');
        this.echo('PayPal payment method was selected from the Wall');
    });

    casper.then(function () {
        this.click(config.checkout.nextStepButtonSelector);
        this.echo('Continue to next step button was clicked');
    });

    casper.waitForUrl(config.payPalSandBox.urlRegExp, function then() {
        casper.test.pass("PayPal sandbox page was loaded");
    }, function timeout() {
        casper.test.fail("PayPal sandbox page was loaded");
    }, config.payPalSandBox.waitForPageLoad);

    casper.then(function () {
        //test if the form exists
        this.test.assertExists(config.payPalSandBox.loginFormSelector, "PayPal Sandbox login form is found");

        //fill in the form
        this.fill(config.payPalSandBox.loginFormSelector, {
            email: config.payPalSandBox.email,
            password: config.payPalSandBox.password
        }, true);
    });

    casper.waitForSelector(config.payPalSandBox.continueButtonSelector, function then() {
        casper.test.pass("PayPal SandBox can continue");
    }, function timeout() {
        casper.test.fail("PayPal SandBox can continue");
    }, config.payPalSandBox.waitForPageLoad);

    casper.then(function () {
        casper.click(config.payPalSandBox.continueButtonSelector);
        this.echo('PayPal SandBox continue button was clicked');
    });

    casper.waitForSelector(config.orderPage.orderNowButtonSelector, function then() {
        casper.test.pass("Returned to OXID and can order");
    }, function timeout() {
        casper.test.fail("Returned to OXID and can order");
    }, config.payPalSandBox.waitForPageLoad);


};

exports.clickOrderNow = function () {
    casper.then(function () {
        this.capture('screenshots/ppp_order_page.png');
        this.test.assertExists(config.orderPage.orderNowButtonSelector, "Order now button exists");
        casper.click(config.orderPage.orderNowButtonSelector);
        this.echo('Order now button was clicked');
    });

    casper.waitForSelector(config.thankYouPage.thankYouDivSelector, function then() {
        casper.test.pass("Thank you page reached");
        this.capture('screenshots/ppp_thank_you_page.png');
    }, function timeout() {
        casper.test.fail("Thank you page NOT reached");
        this.capture('screenshots/ppp_thank_you_page.png');
    }, config.payPalSandBox.waitForPageLoad);
};

function goToBasketPageAndCheckIt() {
    casper.thenOpen(config.mainUrl + config.checkout.basketUrl, function () {
        this.test.assertExists('form[name="basket"]', 'Basket page accessed and not empty');
    });
}

exports.clearAllBasket = function () {
    goToBasketPageAndCheckIt();

    casper.then(function () {
        var basketSelectAllButtonSelector = 'button#basketRemoveAll';
        var basketRemoveButtonSelector = 'button#basketRemove';

        this.test.assertExists(basketSelectAllButtonSelector, 'Basket select all button exists');
        this.test.assertExists(basketRemoveButtonSelector, 'Basket remove button exists');
        this.click(basketSelectAllButtonSelector);
        this.click(basketRemoveButtonSelector);
        this.echo('Select all and remove buttons was clicked');
    });

    casper.then(function () {
        this.test.assertDoesntExist('form[name="basket"]', 'Basket was cleared successfully');
    });
};

exports.addVoucherToBasket = function (voucherNumber) {
    goToBasketPageAndCheckIt()

    casper.then(function () {
        this.test.assertExists(config.checkout.voucherInputSelector, 'Voucher input exists');

        this.fill(config.checkout.voucherFormSelector, {
            voucherNr: voucherNumber
        }, true);
    });

    casper.then(function () {
        this.test.assertDoesntExist(config.checkout.voucherInlineErrorSelector, "Voucher was accepted");
    });
};

//Assuming that wrapper has price
exports.addWrapperToBasket = function (wrapperId) {
    goToBasketPageAndCheckIt();

    casper.then(function () {
        this.test.assertExists('.wrappingTrigger', 'Add wrapper link found');
        this.click('.wrappingTrigger');
    });

    casper.waitForSelector('div.wrapping', function () {
        this.echo('Wrapping popup opened');
        this.test.assertExists('.wrappingData input[value="' + wrapperId + '"]', 'Wrapper with id ' + wrapperId + ' was found');
        this.click('.wrappingData input[value="' + wrapperId + '"]');
        this.fill('div.wrapping form', {}, true);
        this.echo('Wrapping was selected and submitted');
    });

    casper.then(function () {
        this.test.assertExists('#basketWrappingGross', 'Wrapping price was found');
    });
};

//Assuming that gifting card has price
exports.addGiftingCardToBasket = function (giftingCardId) {
    goToBasketPageAndCheckIt();

    casper.then(function () {
        this.test.assertExists('.wrappingTrigger', 'Add wrapper link found');
        this.click('.wrappingTrigger');
    });

    casper.waitForSelector('div.wrapping', function () {
        this.echo('Wrapping popup opened');
        this.test.assertExists('.wrappingCard input[value="' + giftingCardId + '"]', 'Gifting card with id ' + giftingCardId + ' was found');
        this.click('.wrappingCard input[value="' + giftingCardId + '"]');
        this.fill('div.wrapping form', {}, true);
        this.echo('Gifting card was selected and submitted');
    });

    casper.then(function () {
        this.test.assertExists('#basketGiftCardGross', 'Gifting card price was found');
    });
};