<?php
/**
 * This file is part of OXID eSales PayPal Plus module.
 *
 * OXID eSales PayPal Plus module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales PayPal Plus module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales PayPal Plus module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2015
 */

/**
 * Class paypPayPalPlusErrorHandlerTest
 * Tests for paypPayPalPlusErrorHandler class.
 *
 * @see paypPayPalPlusErrorHandlerTest
 */
class paypPayPalPlusErrorHandlerTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusErrorHandler|PHPUnit_Framework_MockObject_MockObject
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock('paypPayPalPlusErrorHandler', array('__call', '_exitWithError'));
    }


    /**
     * test `getGeneralErrorCode`
     */
    public function testGetGeneralErrorCode()
    {
        $this->assertSame('_PAYP_PAYPALPLUS_ERROR_', $this->SUT->getGeneralErrorCode());
    }


    /**
     * test `getPaymentErrorKey`
     */
    public function testGetPaymentErrorKey()
    {
        $this->assertSame('payerror', $this->SUT->getPaymentErrorKey());
    }


    /**
     * test `setDataValidationNotice`
     */
    public function testSetDataValidationNotice()
    {
        $oException = $this->getMock('oxExceptionToDisplay', array('setMessage'));
        $oException->expects($this->once())->method('setMessage')->with('PAYP_PAYPALPLUS_ERROR_ADDRESS');
        oxTestModules::addModuleObject('oxExceptionToDisplay', $oException);

        $oUtilsView = $this->getMock('oxUtilsView', array('addErrorToDisplay'));
        $oUtilsView->expects($this->once())->method('addErrorToDisplay')->with($oException, false);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\UtilsView::class, $oUtilsView);

        $this->SUT->setDataValidationNotice();
    }


    /**
     * test `setPaymentErrorAndRedirect`
     */
    public function testSetPaymentErrorAndRedirect()
    {
        $oConfig = $this->getMock('oxConfig', array('getShopCurrentUrl'));
        $oConfig->expects($this->once())->method('getShopCurrentUrl')->will(
            $this->returnValue('http://www.example.com/index.php?lang=1')
        );
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

        $oUtils = $this->getMock('oxUtils', array('redirect'));
        $oUtils->expects($this->once())->method('redirect')->with('http://www.example.com/index.php?lang=1&cl=payment');
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Utils::class, $oUtils);

        $this->SUT->setPaymentErrorAndRedirect(3);

        $this->assertSame(3, modSession::getInstance()->getVar('payerror'));
    }


    /**
     * `exitWithError` should exit with general error code, when exception is not instance of PayPalConnectionException.
     */
    public function testParseError_itIsNotPayPalExceptionInstance_exitWithGeneralCode()
    {
        $this->SUT->expects($this->once())->method('_exitWithError')->with($this->SUT->getGeneralErrorCode(), false);

        $this->SUT->parseError(new oxException('Some error'));
    }

    /**
     * `exitWithError` should call a method that would return an error, when second argument is true.
     */
    public function testParseError_secondArgumentIsTrue_callToReturnAnError()
    {
        $this->SUT->expects($this->once())->method('_exitWithError')->with($this->SUT->getGeneralErrorCode(), true);

        $this->SUT->parseError(new oxException('Some error'), true);
    }

    /**
     * `exitWithError` should exit with general error code, when exception could not be parsed for an error.
     */
    public function testParseError_itIsPayPalExceptionInstanceButNotParsed_exitWithGeneralCode()
    {
        $oException = new PayPal\Exception\PayPalConnectionException('', '');
        $oException->setData('{"name":"MALFORMED_REQUEST","message":"Request is malformed."}');

        $this->SUT->expects($this->once())->method('_exitWithError')->with($this->SUT->getGeneralErrorCode(), false);

        $this->SUT->parseError($oException);
    }

    /**
     * `exitWithError` should exit with general error code, when exception has no error message.
     */
    public function testParseError_itIsPayPalExceptionInstanceButMessageIsEmpty_exitWithGeneralCode()
    {
        $oException = new PayPal\Exception\PayPalConnectionException('', '');
        $oException->setData('{"name":"ADDRESS_INVALID","message":""}');

        $this->SUT->expects($this->once())->method('_exitWithError')->with($this->SUT->getGeneralErrorCode(), false);

        $this->SUT->parseError($oException);
    }

    /**
     * `exitWithError` should exit with a message from an exception, when it's PayPal invalid address exception.
     */
    public function testParseError_itIsPayPalAddressValidationExceptions_exitWithTheExceptionErrorMessage()
    {
        $oException = new PayPal\Exception\PayPalConnectionException('', '');
        $oException->setData('{"name":"ADDRESS_INVALID","message":"Address is invalid"}');

        $this->SUT->expects($this->once())->method('_exitWithError')->with('Address is invalid', false);

        $this->SUT->parseError($oException);
    }

    /**
     * `exitWithError` should exit with a message from an exception, when it's PayPal validation exception.
     */
    public function testParseError_itIsPayPalValidationExceptions_exitWithTheExceptionErrorMessage()
    {
        $oException = new PayPal\Exception\PayPalConnectionException('', '');
        $oException->setData('{"name":"VALIDATION_ERROR","message":"Field is invalid"}');

        $this->SUT->expects($this->once())->method('_exitWithError')->with('Field is invalid', false);

        $this->SUT->parseError($oException);
    }

    /**
     * `exitWithError` should exit with a message from an exception and additional error description,
     *  when it's PayPal validation exception containing more error details.
     */
    public function testParseError_itIsPayPalValidationExceptionsWithExtraData_exitWithExtendedErrorMessage()
    {
        $oException = new PayPal\Exception\PayPalConnectionException('', '');
        $oException->setData(
            '{"name":"VALIDATION_ERROR","message":"Field is invalid","details":[{"field":"phone","issue":"BAD phone"}]}'
        );

        $this->SUT->expects($this->once())->method('_exitWithError')->with(
            'Field is invalid: (phone) BAD phone',
            false
        );

        $this->SUT->parseError($oException);
    }
}
