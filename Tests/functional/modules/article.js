exports.addArticleToBasket = function(articleNumber) {
    casper.then(function() {
        //test if search form exists
        this.test.assertExists(config.search.formSelector, "Search form is found");

        //fill in the form and submit
        this.fill(config.search.formSelector, {
            searchparam: articleNumber
        }, true);
    });

    casper.then(function() {
        //test if article was found, and the count of articles found is 1
        this.test.assertElementCount(config.search.toBasketButtonSelector, 1, 'Single article was found');

        this.click(config.search.toBasketButtonSelector);
        this.echo('Add to basket button clicked for article ' + articleNumber);
    });

    casper.waitForSelector('#newItemMsg', function then() {
        casper.test.pass("Article was added");
    }, function timeout() {
        casper.test.fail("Article was added");
    }, 5000);
};