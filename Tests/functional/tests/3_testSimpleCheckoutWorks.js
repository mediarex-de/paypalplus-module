casper.test.begin('Test simple checkout works', 0, function() {
    var oxidModule = require('../modules/oxid');

    oxidModule.start();

    oxidModule.user.login();
    oxidModule.article.addArticleToBasket('testppp1');
    oxidModule.checkout.goToPaymentPage();
    oxidModule.checkout.selectPaymentAndContinue('testppppayment');
    oxidModule.checkout.clickOrderNow();
    oxidModule.user.logout();

    oxidModule.end();
});

