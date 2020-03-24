casper.test.begin('User login', 0, function() {
    var oxidModule = require('../modules/oxid');

    oxidModule.start();

    oxidModule.user.login(oxidModule.config);

    oxidModule.end();
});