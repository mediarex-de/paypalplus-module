casper.test.begin('Test simple PayPal checkout', 0, function() {
    var oxidModule = require('../modules/oxid');

    oxidModule.start();

    oxidModule.user.login();
    oxidModule.article.addArticleToBasket('testppp1');
    oxidModule.checkout.goToPaymentPage();
    oxidModule.checkout.payPalPlusWallIsVisible();
    oxidModule.checkout.selectFromPayPalWallPayPalMethodAndContinue();
    oxidModule.checkout.clickOrderNow();
    oxidModule.user.logout();

    oxidModule.end();
});

