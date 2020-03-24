casper.test.begin('Test PayPalPlus Module is active', 0, function() {
    var oxidModule = require('../modules/oxid');

    oxidModule.start();

    oxidModule.admin.login();
    oxidModule.admin.checkModuleActive('PayPal Plus');
    oxidModule.admin.logout();

    oxidModule.end();
});

