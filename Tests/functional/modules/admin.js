exports.login = function () {
    casper.thenOpen(config.mainUrl + 'admin', function () {
        this.test.assertExists(config.admin.loginFormSelector, "Admin panel login form is found");
    });

    casper.then(function () {
        this.fill(config.admin.loginFormSelector, {
            user: config.admin.username,
            pwd: config.admin.password
        }, true);
    });

    casper.then(function () {
        this.test.assertExists('frame[name="navigation"]', "Admin user logged in successfully");
    });
};

exports.logout = function () {
    casper.then(function () {
        this.withFrame('header', function () {
            this.test.assertExists('a#logoutlink', "Admin logout link exists");
            this.click('a#logoutlink');
            this.echo('Admin logout link was clicked');
        });
    });

    casper.then(function () {
        this.test.assertExists(config.admin.loginFormSelector, "Admin was logged out successfully");
    });
};

//@todo: a check if in admin panel
exports.checkModuleActive = function (moduleName) {
    casper.then(function () {
        this.withFrame('navigation', function () {
            this.withFrame('adminnav', function () {
                this.test.assertExists('a[href*="cl=module"]', "Module link exists");
                this.click('a[href*="cl=module"]');
                this.echo('Module extensions link was clicked');
            });
        });
    });

    casper.then(function () {
        this.withFrame('basefrm', function () {
            this.withFrame('list', function () {
                this.test.assertSelectorHasText('.listitemfloating a', moduleName, "Module with name '" + moduleName + "' was found");
                this.clickLabel(moduleName, 'a');
                //this.click(".listitemfloating a:contains('" + moduleName + "')");
                this.echo("Module '" + moduleName + "' was selected");
            });
        });
    });

    casper.then(function () {
        this.withFrame('basefrm', function () {
            this.withFrame('edit', function () {
                this.test.assertElementCount('input#module_deactivate', 1, 'Module is active');
            });
        });
    });
};

/**
 * @param {string} paymentName
 * @param {int} price
 */
//@todo: a check if in admin panel
exports.setPaymentSurchargePrice = function (paymentName, price) {
    casper.then(function () {
        casper.withFrame('navigation', function () {
            this.withFrame('adminnav', function () {
                this.test.assertExists('a[href*="cl=admin_payment"]', "Module payments link exist");
                this.click('a[href*="cl=admin_payment"]');
                this.echo('Module payments link was clicked');
            });
        });
    });

    casper.then(function () {
        this.withFrame('basefrm', function () {
            this.withFrame('list', function () {
                this.test.assertSelectorHasText('.listitemfloating a', paymentName, "Payment with name '" + paymentName + "' was found");
                this.clickLabel(paymentName, 'a');
                this.echo("Payment '" + paymentName + "' was selected");
            });
        });
    });

    var editingFormSelector = 'form#myedit';

    casper.then(function () {
        this.withFrame('basefrm', function () {
            this.withFrame('edit', function () {
                this.test.assertExists(editingFormSelector, "Payment editing form was found");

                this.fill(editingFormSelector, {
                    'editval[oxpayments__oxaddsum]': price
                }, false);

                this.click('input[name="save"]');
            });
        });
    });

    casper.then(function () {
        this.withFrame('basefrm', function () {
            this.withFrame('edit', function () {
                this.test.assertEquals(price.toString(), this.getFormValues(editingFormSelector)['editval[oxpayments__oxaddsum]'], 'Payment price was changed to ' + price);
            });
        });
    });
};

exports.openLastUserOrder = function (userName, userLastNames) {
    casper.then(function () {
        this.withFrame('navigation', function () {
            this.withFrame('adminnav', function () {
                this.test.assertExists('a[href*="cl=admin_order"]', "Administer Orders -> Orders link exist");
                this.click('a[href*="cl=admin_order"]');
                this.echo('Administer Orders -> Orders link clicked');
            });
        });
    });

    casper.then(function () {
        this.withFrame('basefrm', function () {
            this.withFrame('list', function () {
                this.test.assertExists("form#search", "Admin panel login form is found");

                this.fill("form#search", {
                    "where[oxorder][oxbillfname]": userName,
                    "where[oxorder][oxbilllname]": userLastNames
                }, true);
            });
        });
    });

    casper.then(function () {
        this.withFrame('basefrm', function () {
            this.withFrame('list', function () {
                this.test.assertExists('a[href*="oxordernr"]', "Sorting by order link exists");
                this.click('a[href*="oxordernr"]');
                this.echo('Sorting by order link clicked');
            });
        });
    });

    casper.then(function () {
        this.withFrame('basefrm', function () {
            this.withFrame('list', function () {
                this.test.assertExists('tr#row\\.1 a', "First order link exists");
                this.click('tr#row\\.1 a');
                this.echo('First order link clicked');
            });
        });
    });
};

exports.selectOrderTab = function (tabName) {
    casper.then(function () {
        this.withFrame('basefrm', function () {
            this.withFrame('list', function () {
                this.test.assertExists('a[href*="' + tabName + '"]', "Tab with name " + tabName + " exists");
                this.click('a[href*="' + tabName + '"]');
                this.echo("Tab with name " + tabName + " clicked");
            });
        });
    });
};

exports.testRefundFormVisible = function (formShouldExist) {
    casper.then(function () {
        this.withFrame('basefrm', function () {
            this.withFrame('edit', function () {
                var formExists = this.exists('form#refund');
                if (formExists && formShouldExist) {
                    this.test.pass("Refund form exists, refund is possible");
                } else if (formExists && !formShouldExist) {
                    this.test.fail("Refund form does not exist, refund is not possible");
                } else if (!formExists && !formShouldExist) {
                    this.test.pass("Refund form does not exist, refund is not possible");
                } else {
                    this.test.fail("Refund form exists, refund is possible");
                }
            });
        });
    });
};

exports.testRefundValue = function (value, errorBoxShouldBeVisible) {
    casper.then(function () {
        this.withFrame('basefrm', function () {
            this.withFrame('edit', function () {
                this.fill('form#refund', {
                    'refundAmount': value
                }, true);
            });
        });
    });

    casper.then(function () {
        this.capture('screenshots/ppp_order_tab.png');
        this.withFrame('basefrm', function () {
            this.withFrame('edit', function () {
                var errorBoxIsVisible = this.exists('.paypPayPalPlusOverviewTable .errorbox');
                if (errorBoxIsVisible && errorBoxShouldBeVisible) {
                    this.test.pass("Refund value '" + value + "' gives error");
                } else if (errorBoxIsVisible && !errorBoxShouldBeVisible) {
                    this.test.fail("Refund value '" + value + "' does not give an error");
                } else if (!errorBoxIsVisible && !errorBoxShouldBeVisible) {
                    this.test.pass("Refund value '" + value + "' does not give an error");
                } else {
                    this.test.fail("Refund value '" + value + "' gives error");
                }
            });
        });
    });
};