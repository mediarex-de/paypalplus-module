casper.test.begin('Test PayPal refunding max times allowed', 0, function() {
    var oxidModule = require('../modules/oxid');

    oxidModule.start();

    //NOTE: assuming that this test is running after test number 4,
    // because we need a PayPal payment to be the last one

    oxidModule.admin.login();

    oxidModule.admin.openLastUserOrder('Name', 'Surname');
    oxidModule.admin.selectOrderTab('admin_payppaypalplusordertab');

    oxidModule.admin.testRefundFormVisible(true);

    oxidModule.admin.testRefundValue('', true);
    oxidModule.admin.testRefundValue('-1', true);
    oxidModule.admin.testRefundValue('asd', true);
    oxidModule.admin.testRefundValue('0', true);
    oxidModule.admin.testRefundValue('0,0001', true);
    oxidModule.admin.testRefundValue('~', true);
    oxidModule.admin.testRefundValue('-0.1', true);
    oxidModule.admin.testRefundValue('0.004', true);

    //10 acceptable refunds as 10 is the limit
    oxidModule.admin.testRefundValue('0.006', false);
    oxidModule.admin.testRefundValue('0.1', false);
    oxidModule.admin.testRefundValue('0,1', false);
    oxidModule.admin.testRefundValue('0,1', false);
    oxidModule.admin.testRefundValue('0,1', false);
    oxidModule.admin.testRefundValue('0,1', false);
    oxidModule.admin.testRefundValue('0,1', false);
    oxidModule.admin.testRefundValue('0,1', false);
    oxidModule.admin.testRefundValue('0,1', false);
    oxidModule.admin.testRefundValue('0,1', false);

    oxidModule.admin.testRefundFormVisible(false);

    oxidModule.admin.logout();

    oxidModule.end();
});