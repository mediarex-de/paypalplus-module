casper.test.begin('Test changing user billing address', 0, function() {
    var oxidModule = require('../modules/oxid');

    oxidModule.start();

    oxidModule.user.login();
    oxidModule.user.changeBillingAddressTo('8f241f11096877ac0.98748826', 'AL', 'TestCity', 'TestStreet', 'TestStreetNr', 'TestPostal');

    oxidModule.end();
});

