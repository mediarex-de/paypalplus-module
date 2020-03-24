var oxidConfigModule = require('../config');

exports.config = oxidConfigModule.config;
config = oxidConfigModule.config;

var userModule = require('./user');
var articleModule = require('./article');
var checkoutModule = require('./checkout');
var adminModule = require('./admin');

exports.user = userModule;
exports.article = articleModule;
exports.checkout = checkoutModule;
exports.admin = adminModule;

exports.start = function() {
    casper.start(oxidConfigModule.config.mainUrl, function() {
        this.test.assertHttpStatus(200, 'Website is accessible, HTTP status is 200');
    });
};

exports.end = function() {
    casper.run(function() {
        this.test.done();
    });
};
