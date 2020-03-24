casper.test.begin('Test PayPal Plus Wall is accessible', 0, function() {
    var oxidModule = require('../modules/oxid');

    oxidModule.start();

    oxidModule.user.login();
    oxidModule.article.addArticleToBasket('testppp1');
    oxidModule.checkout.goToPaymentPage();
    oxidModule.checkout.payPalPlusWallIsVisible();
    oxidModule.user.logout();

    oxidModule.end();
});

