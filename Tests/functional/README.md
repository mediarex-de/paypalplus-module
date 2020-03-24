# Functional tests of PayPal Plus payments module for OXID eShop 

---

## Requirements
* PhantomJS 1.8.2 and greater, but not 2.0.0 (casperjs requirement). Installation instructions can be found [here](http://phantomjs.org/download.html). On linux use binary file Compilation might continue for >2h without success. 
 * Also see [this page](https://gist.github.com/julionc/7476620) for PhantomJS installation details
* Python 2.6 or greater for **casperjs** 
 * [Windows releases](https://www.python.org/downloads/windows/)
 * Ubuntu platform should already have Python installed, you can check it with `python --version`
 * [Python for unix platforms](https://docs.python.org/2/using/unix.html)
* CasperJs. Installation instructions can be found [here](http://docs.casperjs.org/en/latest/installation.html)


## Running the tests
* first you will have to create `config.js` out of `config.sample.js` and make the adjustments if needed
* Before running the tests add the test data by running php script `payp/paypalplus/tests/functional/testdata/addTestData.php`
* To run this module tests it is recommended to be in `payp/paypalplus/tests/functional` folder and then use command `casperjs --ssl-protocol=any test tests/`
* After finishing a test run remove test data by running php script `payp/paypalplus/tests/functional/testdata/removeTestData.php`
* Test after login to PayPal might fail - run this step manually and then run rest of the tests.

## Using pre-defined module
* There are several predefined modules for oxid which give more functionality for tests, they are all assigned to oxidModule.
* Additional modules with functions for oxid testing are in `payp/paypalplus/tests/functional/modules` organized by their task.
* It is recommended to include oxid.js module `var oxidModule = require('../modules/oxid');` when writing the tests in tests folder
* With oxid.js module enabled you can use `oxidModule.start();` to initiate config and get access to all other oxid functions. This also initiates the beginning syntax of tests
* If needed you can access config values in `oxidModule.config`
* Finishing the test requires specific syntax which can be added by `oxidModule.end();`
* To understand this completely please read some sample tests and casperjs quick start documentation [here](http://docs.casperjs.org/en/latest/quickstart.html)