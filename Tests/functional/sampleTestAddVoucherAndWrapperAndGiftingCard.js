casper.test.begin('Test adding voucher, wrapper and gifting card', 0, function() {
    var oxidModule = require('../modules/oxid');

    oxidModule.start();

    oxidModule.article.addArticleToBasket('testppp1');
    oxidModule.checkout.addVoucherToBasket('testPPPModuleVoucher');
    oxidModule.checkout.addWrapperToBasket('testPPPModuleGiftWrapper');
    oxidModule.checkout.addGiftingCardToBasket('testPPPModuleGiftingCard');

    oxidModule.end();
});

