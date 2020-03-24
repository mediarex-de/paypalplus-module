<?php
/**
 * Created by PhpStorm.
 * User: Robert Blank
 * Date: 2015-10-22
 * Time: 16:54
 */
class paypPayPalPlusPdfArticleSummaryPaymentInstructionsTest extends OxidTestCase
{

    /**
     * System Under Test
     *
     * @var $_SUT paypPayPalPlusPdfArticleSummaryPaymentInstructions|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_SUT;

    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->_SUT = $this->getMock('paypPayPalPlusPdfArticleSummaryPaymentInstructions');
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

    /** This test will fail, if you change the formatting options in the _lang.php file */
    public function testGetFormattedDate_returnsInputString_onInvalidEntryFormat()
    {
        $SUT = new paypPayPalPlusPdfArticleSummaryPaymentInstructions();
        $sDate = '2015/01/01';

        $sExpectedDate = '2015/01/01';

        // $SUT->setLang(0);
        $sActualDate = $this->invokeMethod($SUT, '_getFormattedDate', array($sDate));

        $this->assertSame($sExpectedDate, $sActualDate);

        $sExpectedDate = '2015/01/01';
        // $SUT->setLang(1);
        $sActualDate = $this->invokeMethod($SUT, '_getFormattedDate', array($sDate));

        $this->assertSame($sExpectedDate, $sActualDate);
    }

    /** This test will fail, if you change the formatting options in the _lang.php file */
    public function testGetFormattedDate_returnsExpectedString()
    {
        $SUT = new paypPayPalPlusPdfArticleSummaryPaymentInstructions();
        $sDate = '2015-01-01 00:00:00';

        $sExpectedDate = "1.1.2015";
        $SUT->setLang(0);
        $sActualDate = $this->invokeMethod($SUT, '_getFormattedDate', array($sDate));

        $this->assertSame($sExpectedDate, $sActualDate);

        $sExpectedDate = "2015-01-01";
        $SUT->setLang(1);
        $sActualDate = $this->invokeMethod($SUT, '_getFormattedDate', array($sDate));

        $this->assertSame($sExpectedDate, $sActualDate);
    }

    /** This test will fail, if you change the formatting options in the _lang.php file */
    public function testGetFormattedTotal_returnsExpectedString () {

        $SUT = new paypPayPalPlusPdfArticleSummaryPaymentInstructions();
        $fTotal = 1000000;

        $sExpectedTotal = "1 000 000,00";
        $SUT->setLang(0);
        $sActualTotal = $this->invokeMethod($SUT, '_getFormattedTotal', array($fTotal));

        $this->assertSame($sExpectedTotal, $sActualTotal);

        $sExpectedTotal = "1,000,000.00";
        $SUT->setLang(1);
        $sActualTotal = $this->invokeMethod($SUT, '_getFormattedTotal', array($fTotal));

        $this->assertSame($sExpectedTotal, $sActualTotal);
    }

    public function testGetPaymentInstructionsTextLines_returnsAnArray () {
        $aPaymentInstructionsTextLines = $this->invokeMethod (
            $this->_SUT, '_getPaymentInstructionsTextLines',
            array('Legal Notice', 'Term', 'BankName', 'AccountHolder', 'Iban', 'Bic', 'Amount', 'ReferenceNumber')
        );

        $this->assertNotEmpty($aPaymentInstructionsTextLines);
        $this->assertTrue(is_array($aPaymentInstructionsTextLines));
    }

    /**
     * This test will fail, if you change the strings in the _lang.php files
     *
     * @dataProvider dataProviderTranslateString
     *
     * @param $sString
     * @param $iLang
     * @param $sExpectedString
     */
    public function testTranslateString_returnsExpectedString ($sString, $iLang, $sExpectedString) {
        $SUT = new paypPayPalPlusPdfArticleSummaryPaymentInstructions();
        $SUT->setLang($iLang);

        $sActualTextLine = $this->invokeMethod (
            $SUT, '_translateString',
            array($sString)
        );

        $this->assertSame($sExpectedString, $sActualTextLine);
    }

    public function dataProviderTranslateString() {
        return array(
            array('PAYP_PAYPALPLUS_TEST', 0, 'german string with ü'),
            array('PAYP_PAYPALPLUS_TEST', 1, 'english string with ü'),
        );
    }
    /**
     * This test will fail, if you change the strings in the _lang.php files
     *
     * @dataProvider dataProviderGetTextLine
     *
     * @param $sLabel
     * @param $sValue
     * @param $iLang
     * @param $sExpectedString
     */
    public function testGetTextLine_returnsExpectedString ($sLabel, $sValue, $iLang, $sExpectedString) {

        $SUT = new paypPayPalPlusPdfArticleSummaryPaymentInstructions();
        $SUT->setLang($iLang);

        $sActualTextLine = $this->invokeMethod (
            $SUT, '_getTextLine',
            array($sLabel, $sValue)
        );

        $this->assertSame($sExpectedString, $sActualTextLine);
    }

    public function dataProviderGetTextLine() {
        return array(
            array('PAYP_PAYPALPLUS_TEST', 'Hello World!', 0, 'german string with ü: Hello World!'),
            array('PAYP_PAYPALPLUS_TEST', 'Hello World!', 1, 'english string with ü: Hello World!'),
        );
    }
}
