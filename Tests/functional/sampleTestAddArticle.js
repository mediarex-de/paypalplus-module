casper.test.begin('Add article', 0, function() {
    var oxidModule = require('../modules/oxid');

    oxidModule.start();

    oxidModule.article.addArticleToBasket('testppp1');

    oxidModule.end();
});