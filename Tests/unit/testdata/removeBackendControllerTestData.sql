-- PAYP PayPal Plus - Test data removal for: Admin_paypPayPalPlusOrderTabTest

-- Remove test order dummy entry
DELETE FROM `oxorder` WHERE `OXID` = 'payp_pay_pal_plus_test_order_1';
DELETE FROM `oxorder` WHERE `OXID` = 'payp_pay_pal_plus_test_order_2';
DELETE FROM `oxorder` WHERE `OXID` = 'payp_pay_pal_plus_test_order_3';
DELETE FROM `oxorder` WHERE `OXID` = 'payp_pay_pal_plus_test_order_4';
DELETE FROM `oxorder` WHERE `OXID` = 'payp_pay_pal_plus_test_order_5';
DELETE FROM `oxorder` WHERE `OXID` = 'payp_pay_pal_plus_test_order_6';
DELETE FROM `oxorder` WHERE `OXID` = 'payp_pay_pal_plus_test_order_7';

DELETE FROM `payppaypalpluspayment` WHERE `OXID` = 'payp_pay_pal_plus_test_payment_2';
DELETE FROM `payppaypalpluspayment` WHERE `OXID` = 'payp_pay_pal_plus_test_payment_3';
DELETE FROM `payppaypalpluspayment` WHERE `OXID` = 'payp_pay_pal_plus_test_payment_4';
DELETE FROM `payppaypalpluspayment` WHERE `OXID` = 'payp_pay_pal_plus_test_payment_5';
DELETE FROM `payppaypalpluspayment` WHERE `OXID` = 'payp_pay_pal_plus_test_payment_6';
DELETE FROM `payppaypalpluspayment` WHERE `OXID` = 'payp_pay_pal_plus_test_payment_7';

DELETE FROM `payppaypalplusrefund` WHERE `OXID` = 'payp_pay_pal_plus_test_ref3_1';
DELETE FROM `payppaypalplusrefund` WHERE `OXID` = 'payp_pay_pal_plus_test_ref3_2';
DELETE FROM `payppaypalplusrefund` WHERE `OXID` = 'payp_pay_pal_plus_test_ref4_3';
DELETE FROM `payppaypalplusrefund` WHERE `OXID` = 'payp_pay_pal_plus_test_ref4_4';
DELETE FROM `payppaypalplusrefund` WHERE `OXID` = 'payp_pay_pal_plus_test_ref4_5';
DELETE FROM `payppaypalplusrefund` WHERE `OXID` = 'payp_pay_pal_plus_test_ref4_6';
DELETE FROM `payppaypalplusrefund` WHERE `OXID` = 'payp_pay_pal_plus_test_ref4_7';
DELETE FROM `payppaypalplusrefund` WHERE `OXID` = 'payp_pay_pal_plus_test_ref4_8';
DELETE FROM `payppaypalplusrefund` WHERE `OXID` = 'payp_pay_pal_plus_test_ref4_9';
DELETE FROM `payppaypalplusrefund` WHERE `OXID` = 'payp_pay_pal_plus_test_ref4_10';
DELETE FROM `payppaypalplusrefund` WHERE `OXID` = 'payp_pay_pal_plus_test_ref4_11';
DELETE FROM `payppaypalplusrefund` WHERE `OXID` = 'payp_pay_pal_plus_test_ref4_12';
DELETE FROM `payppaypalplusrefund` WHERE `OXID` = 'payp_pay_pal_plus_test_ref4_13';
DELETE FROM `payppaypalplusrefund` WHERE `OXID` = 'payp_pay_pal_plus_test_ref4_14';
DELETE FROM `payppaypalplusrefund` WHERE `OXID` = 'payp_pay_pal_plus_test_ref6_15';
DELETE FROM `payppaypalplusrefund` WHERE `OXID` = 'payp_pay_pal_plus_test_ref7_16';
