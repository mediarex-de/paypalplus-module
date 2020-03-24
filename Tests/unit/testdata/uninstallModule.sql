-- Delete module entry from `oxpayments` table
DELETE FROM `oxpayments` WHERE `OXID` = 'payppaypalplus';

-- Unassign the PayPal Plus from all countries and shipping methods.
DELETE FROM `oxobject2payment` WHERE `OXPAYMENTID` = 'payppaypalplus';

-- Unassign the PayPal Plus from all user groups.
DELETE FROM `oxobject2group` WHERE `OXOBJECTID` = 'payppaypalplus';

-- Delete additional module table
DROP TABLE `payppaypalpluspayment`;

-- Delete additional module table
DROP TABLE `payppaypalplusrefund`;

-- Delete additional module table
DROP TABLE `payppaypalpluspui`;