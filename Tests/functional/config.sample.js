//this is configured to work with default shop template and module test data for functional tests imported
var config = {
    mainUrl: "http://vm/", //should end with / because later urls are added to this
    admin: {
        username: "admin@testpppmodule.de",
        password: "admin@testpppmodule.de",
        loginFormSelector: "form#login"
    },
    user: {
        username: "user@testpppmodule.de",
        password: "user@testpppmodule.de",
        loginFormSelector: "form#login",
        logoutLinkSelector: "a#logoutLink",
        billingAndShippingUrl: "en/my-address/"
    },
    checkout: {
        paymentUrl: "index.php?cl=payment&lang=1",
        basketUrl: "en/cart/",
        shippingFormSelector: 'form#shipping',
        paymentMethodSelector: 'form#payment input[type="radio"]',
        nextStepButtonSelector: '#payment #paymentNextStepBottom',
        voucherFormSelector: 'form[name="voucher"]',
        voucherInputSelector: '#coupon input[name="voucherNr"]',
        voucherInlineErrorSelector: '#coupon .inlineError'
    },
    orderPage: {
        orderNowButtonSelector: 'form#orderConfirmAgbBottom button[type="submit"]'
    },
    thankYouPage: {
        thankYouDivSelector: '#thankyouPage'
    },
    payPalWall: {
        frameNumber: 0,
        paymentMethodRowSelector: '#paymentMethodContainer .paymentMethodRow'
    },
    payPalSandBox: {
        urlRegExp: /sandbox.paypal/,
        waitForPageLoad: 20000,
        email: "__SANDOX_EMAIL__",
        password: "__SANDBOX_PASSWORD__",
        loginFormSelector: 'form#loginForm',
        continueButtonSelector: '#confirmButtonTop'
    },
    search: {
        formSelector: 'form[name="search"]',
        toBasketButtonSelector: 'ul#searchList li.productData form button[type="submit"]'
    }
};

exports.config = config;