<?php
/**
 * This Software is the property of OXID eSales and is protected
 * by copyright law - it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * @category      module
 * @package       paypalplus
 * @author        OXID Professional services
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2015
 */

/**
 * Class paypPayPalPlusPuiDataTest
 * Tests for paypPayPalPlusPuiData model.
 *
 * @see paypPayPalPlusPuiData
 */
class paypPayPalPlusPuiDataTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusPuiData
     */
    protected $SUT;

    /**
     * @inheritDoc
     *
     * Set SUT state before test.
     * Import data to test loading methods
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock('paypPayPalPlusPuiData', array('__call', '_paypPayPalPlusPuiData_save_parent'));
    }

    public function testSetPaymentId()
    {
        $sExpectedValue = 'ABC';
        $this->SUT->setPaymentId($sExpectedValue);
        $this->assertEquals($sExpectedValue, $this->SUT->getPaymentId());
    }

    public function testSetReferenceNumber()
    {
        $sExpectedValue = 'ABC';
        $this->SUT->setReferenceNumber($sExpectedValue);
        $this->assertEquals($sExpectedValue, $this->SUT->getReferenceNumber());
    }

    public function testSetDueDate()
    {
        $sExpectedValue = '2015-01-01 00:00:00';
        $this->SUT->setDueDate($sExpectedValue);
        $this->assertEquals($sExpectedValue, $this->SUT->getDueDate());
    }

    public function testSetTotal()
    {
        $fExpectedValue = 100.00;
        $this->SUT->setTotal($fExpectedValue);
        $this->assertEquals($fExpectedValue, $this->SUT->getTotal());
    }

    public function testSetCurrency()
    {
        $sExpectedValue = 'EUR';
        $this->SUT->setCurrency($sExpectedValue);
        $this->assertEquals($sExpectedValue, $this->SUT->getCurrency());
    }

    public function testSetBankName()
    {
        $sExpectedValue = 'ABC';
        $this->SUT->setBankName($sExpectedValue);
        $this->assertEquals($sExpectedValue, $this->SUT->getBankName());
    }

    public function testSetAccountHolder()
    {
        $sExpectedValue = 'ABC';
        $this->SUT->setAccountHolder($sExpectedValue);
        $this->assertEquals($sExpectedValue, $this->SUT->getAccountHolder());
    }

    public function testSetIban()
    {
        $sExpectedValue = 'ABC';
        $this->SUT->setIban($sExpectedValue);
        $this->assertEquals($sExpectedValue, $this->SUT->getIban());
    }

    public function testSetBic()
    {
        $sExpectedValue = 'ABC';
        $this->SUT->setBic($sExpectedValue);
        $this->assertEquals($sExpectedValue, $this->SUT->getBic());
    }

    public function testSetPuiObject()
    {
        $sJSON = '{"reference_number":"8SW465758P871352J","instruction_type":"PAY_UPON_INVOICE","recipient_banking_instruction":{"bank_name":"Deutsche Bank","account_holder_name":"PayPal Europe","international_bank_account_number":"DE17120700888000722240","bank_identifier_code":"DEUTDEDBPAL"},"amount":{"value":"6.04","currency":"EUR"},"payment_due_date":"2015-11-07","links":[{"href":"https://api.paypal.com/v1/payments/payment/PAY-4V3628380W961235AKYLDTSY/payment-instruction","rel":"self","method":"GET"}]}';
        $oExpectedObject = new PayPal\Api\PaymentInstruction();
        $oExpectedObject->fromJson($sJSON);
        $this->SUT->setPuiObject($oExpectedObject);
        $this->assertEquals($oExpectedObject, $this->SUT->getPuiObject());
    }

    public function testSave_callsParentSave_onValidData()
    {
        $blExpectedResult = true;

        $SUT = $this->getMock('paypPayPalPlusPuiData', array('__call', '_validateData', '_paypPayPalPlusPuiData_save_parent'));
        $SUT->expects($this->once())->method('_validateData')->will($this->returnValue(true));
        $SUT->expects($this->once())->method('_paypPayPalPlusPuiData_save_parent')->will($this->returnValue(true));

        $blActualResult = $SUT->save();

        $this->assertEquals($blExpectedResult, $blActualResult);
    }

    /**
     * @dataProvider invalidDataProvider
     *
     * @param $sFieldsName
     * @param $sFieldValidatorCallback
     * @param $mFieldValue
     * @param $sExpectedExceptionMethod
     */
    public function testSave_throwsExpectedException_onInValidData($sFieldsName, $sFieldValidatorCallback, $mFieldValue, $sExpectedExceptionMethod)
    {
        $this->setExpectedException('\InvalidArgumentException');

        $SUT = $this->getMock('paypPayPalPlusPuiData', array('__call', 'getRequiredFields'));

        $SUT->{"set$sFieldsName"}($mFieldValue);
        $SUT
            ->expects($this->once())
            ->method('getRequiredFields')
            ->will($this->returnValue(array(strtolower($sFieldsName) => array($sFieldValidatorCallback))));

        $SUT->save();
    }

    /**
     * @dataProvider invalidDataProvider
     *
     * @param $sFieldsName
     * @param $sFieldValidatorCallback
     * @param $mFieldValue
     * @param $sExpectedExceptionMethod
     */
    public function testSave_callsExpectedException_onInValidData($sFieldsName, $sFieldValidatorCallback, $mFieldValue, $sExpectedExceptionMethod)
    {
        $this->setExpectedException('\InvalidArgumentException');

        $SUT = $this->getMock('paypPayPalPlusPuiData', array('__call', 'getRequiredFields', $sExpectedExceptionMethod));

        $SUT->{"set$sFieldsName"}($mFieldValue);
        $SUT
            ->expects($this->once())
            ->method('getRequiredFields')
            ->will($this->returnValue(array(strtolower($sFieldsName) => array($sFieldValidatorCallback))));

        $SUT
            ->expects($this->once())
            ->method($sExpectedExceptionMethod)
            ->will($this->throwException(new \InvalidArgumentException()));

        $SUT->save();
    }

    public function invalidDataProvider()
    {
        $oADayAgoDate = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));
        $oADayAgoDate->sub(new DateInterval('PT24H'));
        $sDateInThePast = $oADayAgoDate->format('Y-m-d H:i:s');

        return array(
            array(
                'PaymentId',
                '_validateNotEmptyString',
                array(),
                '_throwEmptyStringException'
            ),
            array(
                'PaymentId',
                '_validateNotEmptyString',
                '',
                '_throwEmptyStringException'
            ),
            array(
                'Iban',
                '_validateIBAN',
                array(),
                '_throwInvalidIbanException'
            ),
            array(
                'Iban',
                '_validateIBAN',
                '',
                '_throwInvalidIbanException'
            ),
            array(
                'Iban',
                '_validateIBAN',
                null,
                '_throwInvalidIbanException'
            ),
            array(
                'Iban',
                '_validateIBAN',
                'aa2345678901234567',
                '_throwInvalidIbanException'
            ),
            array(
                'Bic',
                '_validateBic',
                array(),
                '_throwInvalidBicException'
            ),
            array(
                'Bic',
                '_validateBic',
                '',
                '_throwInvalidBicException'
            ),
            array(
                'Bic',
                '_validateBic',
                null,
                '_throwInvalidBicException'
            ),
            array(
                'Bic',
                '_validateBic',
                'Something funny is not a valid BIC',
                '_throwInvalidBicException'
            ),
            array(
                'DueDate',
                '_validateFutureDate',
                date('Y-m-d'), // Wrong format
                '_throwNoFutureDateException'
            ),
            array(
                'DueDate',
                '_validateFutureDate',
                $sDateInThePast, // More than 12 hours ago
                '_throwNoFutureDateException'
            ),
            array(
                'Total',
                '_validateNotEmptyFloat',
                array(),
                '_throwEmptyFloatException'
            ),
            array(
                'Total',
                '_validateNotEmptyFloat',
                'Something funny',
                '_throwEmptyFloatException'
            ),
            array(
                'Total',
                '_validateNotEmptyFloat',
                null,
                '_throwEmptyFloatException'
            ),
            array(
                'Total',
                '_validateNotEmptyFloat',
                0,
                '_throwEmptyFloatException'
            ),
            array(
                'Currency',
                '_validateCurrency',
                '',
                '_throwInvalidCurrencyException'
            ),
            array(
                'Currency',
                '_validateCurrency',
                new StdClass(),
                '_throwInvalidCurrencyException'
            ),
            array(
                'Currency',
                '_validateCurrency',
                null,
                '_throwInvalidCurrencyException'
            ),
            array(
                'Currency',
                '_validateCurrency',
                'EURO',
                '_throwInvalidCurrencyException'
            ),
            array(
                'PuiObject',
                '_validateValidJson',
                new \PayPal\Api\PaymentInstruction(),
                '_throwNotValidJsonException'
            ),
        );
    }

    /**
     * @dataProvider validDataProvider
     *
     * @param $sFieldsName
     * @param $sFieldValidatorCallback
     * @param $mFieldValue
     * @param $sExpectedExceptionMethod
     */
    public function testSave_throwsNoException_onValidData($sFieldsName, $sFieldValidatorCallback, $mFieldValue, $sExpectedExceptionMethod)
    {
        $SUT = $this->getMock('paypPayPalPlusPuiData', array('__call', 'getRequiredFields', $sExpectedExceptionMethod));

        $SUT->{"set$sFieldsName"}($mFieldValue);
        $SUT
            ->expects($this->once())
            ->method('getRequiredFields')
            ->will($this->returnValue(array(strtolower($sFieldsName) => array($sFieldValidatorCallback))));

        $SUT
            ->expects($this->never())
            ->method($sExpectedExceptionMethod);

        $SUT->save();
    }

    public function validDataProvider()
    {
        $oADayAgoDate = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));
        $oADayAgoDate->add(new DateInterval('PT24H'));
        $sDateInTheFuture = $oADayAgoDate->format('Y-m-d H:i:s');

        $oValidPaymentInstruction = new \PayPal\Api\PaymentInstruction();
        $oValidPaymentInstruction->fromJson($this->_getValidPaymentInstructionJSON());

        $aData = array(
            array(
                'PaymentId',
                '_validateNotEmptyString',
                'Something funny',
                '_throwEmptyStringException'
            ),
            array(
                'DueDate',
                '_validateFutureDate',
                $sDateInTheFuture,
                '_throwNoFutureDateException'
            ),
            array(
                'Total',
                '_validateNotEmptyFloat',
                100.00,
                '_throwEmptyFloatException'
            ),
            array(
                'Currency',
                '_validateCurrency',
                'EUR',
                '_throwInvalidCurrencyException'
            ),
            array(
                'PuiObject',
                '_validateValidJson',
                $oValidPaymentInstruction,
                '_throwNotValidJsonException'
            ),
        );
        foreach ($this->_getValidIban() as $sIBAN) {
            $aData[] = array(
                'Iban',
                '_validateIBAN',
                $sIBAN,
                '_throwInvalidIbanException'
            );
        }

        foreach ($this->_getValidBic() as $sBic) {
            $aData[] = array(
                'Bic',
                '_validateBic',
                $sBic,
                '_throwInvalidBicException'
            );
        }

        return $aData;
    }

    public function testSave_calledExpectedException_onInValidValidatorCallback()
    {
        $this->setExpectedException('\InvalidArgumentException');

        $sFieldValidatorCallback = 'nonExistentValidator';
        $SUT = $this->getMock('paypPayPalPlusPuiData', array('__call', 'getRequiredFields', '_throwValidatorCouldNotBeCalledException'));

        $SUT->setPaymentId('Some Value');
        $SUT
            ->expects($this->once())
            ->method('getRequiredFields')
            ->will($this->returnValue(array(strtolower('PaymentId') => array($sFieldValidatorCallback))));

        $SUT
            ->expects($this->once())
            ->method('_throwValidatorCouldNotBeCalledException')
            ->will($this->throwException(new \InvalidArgumentException()));

        $SUT->save();
    }

    public function testSave_throwsExpectedException_onInValidValidatorCallback()
    {
        $this->setExpectedException('\InvalidArgumentException');

        $sFieldValidatorCallback = 'nonExistentValidator';
        $SUT = $this->getMock('paypPayPalPlusPuiData', array('__call', 'getRequiredFields'));

        $SUT->setPaymentId('Some Value');
        $SUT
            ->expects($this->once())
            ->method('getRequiredFields')
            ->will($this->returnValue(array(strtolower('PaymentId') => array($sFieldValidatorCallback))));

        $SUT->save();
    }

    public function testLoadByPaymentId_loadsPaymentPuiData()
    {
        $sExpectedPaymentId = 'testPaymentId';

        $this->_removeTestData();
        $this->_addTestData();

        $this->SUT->loadByPaymentId($sExpectedPaymentId);
        $sActualPaymentId = $this->SUT->getPaymentId();

        $this->assertEquals($sExpectedPaymentId, $sActualPaymentId);

        $this->_removeTestData();
    }

    public function testLoadByReferenceNumber_loadsPaymentPuiData()
    {
        $sExpectedReferenceNumber = 'Referencenumber';

        $this->_removeTestData();
        $this->_addTestData();

        $this->SUT->loadByReferenceNumber($sExpectedReferenceNumber);
        $sActualReferenceNumber = $this->SUT->getReferenceNumber();

        $this->assertEquals($sExpectedReferenceNumber, $sActualReferenceNumber);

        $this->_removeTestData();
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * test `_loadBy` when wrong field name is passed. Returning false.
     */
    public function testLoadBy_wrongLoadByFieldName_returnFalse()
    {
        $this->assertFalse($this->invokeMethod($this->SUT, '_loadBy', array('someRandomFieldName', 'someFieldValue')));
    }

    protected function _getValidPaymentInstructionJSON()
    {
        $sJson = <<<EOT
            {
              "reference_number": "72F41598J9209010U",
              "instruction_type": "PAY_UPON_INVOICE",
              "recipient_banking_instruction": {
                "bank_name": "Deutsche Bank",
                "account_holder_name": "PayPal Europe",
                "international_bank_account_number": "DE07120700888000002001",
                "bank_identifier_code": "DEUTDEDBPAL"
              },
              "amount": {
                "value": "21.51",
                "currency": "EUR"
              },
              "note": "This is a mock response!",
              "payment_due_date": "2014-08-26",
              "links": [
                {
                  "href": "https://api.paypal.com/v1/payments/payment/PAY-5YK922393D847794YKER7MUI/payment-instruction",
                  "rel": "self",
                  "method": "GET"
                }
              ]
            }
EOT;

        return $sJson;
    }

    protected function _getValidIban()
    {
        $aValidIbanData = array(
            'AA120011123Z5678',
            'AL47212110090000000235698741',
            'AD1200012030200359100100',
            'AT611904300234573201',
            'AX2112345600000785',
            'AZ21NABZ00000000137010001944',
            'BH67BMAG00001299123456',
            'BE68539007547034',
            'BA391290079401028494',
            'BR2300360305000010009795493P1',
            'BG80BNBG96611020345678',
            'CR9120200102628406621',
            'HR1210010051863000160',
            'CY17002001280000001200527600',
            'CZ6508000000192000145399',
            'DK5000400440116243',
            'FO2000400440116243',
            'GL2000400440116243',
            'DO28BAGR00000001212453611324',
            'EE382200221020145685',
            'FI2112345600000785',
            'FR1420041010050500013M02606',
            'BL9820041010050500013M02606',
            'GF4120041010050500013M02606',
            'GP1120041010050500013M02606',
            'MF9820041010050500013M02606',
            'MQ5120041010050500013M02606',
            'RE4220041010050500013M02606',
            'PF5720041010050500013M02606',
            'TF2120041010050500013M02606',
            'YT3120041010050500013M02606',
            'NC8420041010050500013M02606',
            'PM3620041010050500013M02606',
            'WF9120041010050500013M02606',
            'GE29NB0000000101904917',
            'DE89370400440532013000',
            'GI75NWBK000000007099453',
            'GR1601101250000000012300695',
            'GT82TRAJ01020000001210029690',
            'HU42117730161111101800000000',
            'IS140159260076545510730339',
            'IE29AIBK93115212345678',
            'IL620108000000099999999',
            'IT60X0542811101000000123456',
            'JO94CBJO0010000000000131000302',
            'KW81CBKU0000000000001234560101',
            'LV80BANK0000435195001',
            'LB62099900000001001901229114',
            'LI21088100002324013AA',
            'LT121000011101001000',
            'LU280019400644750000',
            'MK07250120000058984',
            'MT84MALT011000012345MTLCAST001S',
            'MR1300020001010000123456753',
            'MU17BOMM0101101030300200000MUR',
            'MD24AG000225100013104168',
            'MC5811222000010123456789030',
            'ME25505000012345678951',
            'NL91ABNA0417164300',
            'NO9386011117947',
            'PK36SCBL0000001123456702',
            'PL61109010140000071219812874',
            'PS92PALS000000000400123456702',
            'PT50000201231234567890154',
            'QA58DOHB00001234567890ABCDEFG',
            'RO49AAAA1B31007593840000',
            'SM86U0322509800000000270100',
            'SA0380000000608010167519',
            'RS35260005601001611379',
            'SK3112000000198742637541',
            'SI56191000000123438',
            'ES9121000418450200051332',
            'SE4550000000058398257466',
            'CH9300762011623852957',
            'TN5910006035183598478831',
            'TR330006100519786457841326',
            'AE070331234567890123456',
            'GB29NWBK60161331926819',
            'VG96VPVG0000012345678901',
        );

        return $aValidIbanData;
    }

    protected function _getValidBic()
    {
        $aValidBicData = array(
            'ALFHAFKA803',
            'IIIGGB22',
            'PSPBFIHH',
            'HANDFIHH',
        );

        return $aValidBicData;
    }

    protected function _removeTestData()
    {
        importTestdataFile("removePaymentPuiData.sql");
    }

    protected function _addTestData()
    {
        importTestdataFile("addPaymentPuiData.sql");
    }

}
