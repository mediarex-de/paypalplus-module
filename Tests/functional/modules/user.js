exports.login = function () {
    casper.then(function () {
        //test if the user login form exists
        this.test.assertExists(config.user.loginFormSelector, "Login form is found");

        //fill in the form
        this.fill(config.user.loginFormSelector, {
            lgn_usr: config.user.username,
            lgn_pwd: config.user.password
        }, true);
    });

    casper.then(function () {
        //test if the user has logged in
        this.test.assertExists(config.user.logoutLinkSelector, "User logged in successfully");
    });
};

exports.logout = function () {
    casper.then(function () {
        this.test.assertExists(config.user.logoutLinkSelector, "User logout link exists");
        this.click(config.user.logoutLinkSelector);
        this.echo('User logout link was clicked');
    });

    casper.then(function () {
        this.test.assertExists(config.user.loginFormSelector, "User was logged out successfully");
    });
};

function isEmpty(str) {
    return (!str || 0 === str.length);
}

function generateFormParameters(formSelector, countryId, stateId, city, street, streetNumber, postalCode) {
    var formParameters = {};

    if (!isEmpty(countryId)) {
        if (casper.exists('select[name="invadr[oxuser__oxcountryid]"] option[value="' + countryId + '"]')) {
            casper.test.pass('Country id ' + countryId + ' exists in selection');
            formParameters["invadr[oxuser__oxcountryid]"] = countryId;
        } else {
            casper.test.fail('Country id ' + countryId + ' exists in selection');
        }
    }

    if (!isEmpty(countryId) && !isEmpty(stateId)) {
        casper.fill(formSelector, {
            "invadr[oxuser__oxcountryid]": countryId
        }, false);
        casper.waitForSelector('select[name="invadr[oxuser__oxstateid]"] option[value="' + stateId + '"]', function then() {
            casper.test.pass('State id ' + stateId + ' exists in selection');
            formParameters["invadr[oxuser__oxstateid]"] = stateId;
        }, function timeout() {
            casper.test.fail('State id ' + stateId + ' exists in selection');
        });
    }

    if (!isEmpty(city)) {
        formParameters["invadr[oxuser__oxcity]"] = city;
    }

    if (!isEmpty(street)) {
        formParameters["invadr[oxuser__oxstreet]"] = street;
    }

    if (!isEmpty(streetNumber)) {
        formParameters["invadr[oxuser__oxstreetnr]"] = streetNumber;
    }

    if (!isEmpty(postalCode)) {
        formParameters["invadr[oxuser__oxzip]"] = postalCode;
    }

    return formParameters;
}

//@todo: check if user logged in
exports.changeBillingAddressTo = function (countryId, stateId, city, street, streetNumber, postalCode) {
    var billingAndShippingFormSelector = 'form[name="order"]';
    var formParameters;

    casper.thenOpen(config.mainUrl + config.user.billingAndShippingUrl, function () {
        this.test.assertExists('#addressSettingsHeader', 'User billing and shipping settings page was accessed');
    });

    casper.then(function () {
        this.test.assertExists(billingAndShippingFormSelector, "User billing and shipping form was found");
        this.click('#userChangeAddress');
        formParameters = generateFormParameters(billingAndShippingFormSelector, countryId, stateId, city, street, streetNumber, postalCode);
    });

    casper.then(function () {
        this.fill(billingAndShippingFormSelector, formParameters, true);
        this.echo('User billing and shipping form was submitted with provided values');
    });
};

