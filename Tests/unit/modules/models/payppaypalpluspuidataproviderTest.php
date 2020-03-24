<?php
/**
 * Created by PhpStorm.
 * User: Robert Blank
 * Date: 2015-10-08
 * Time: 16:12
 */
class payppaypalpluspuidataproviderTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var paypPayPalPlusPuiDataProvider
     */
    protected $SUT;


    /**
     * @inheritDoc
     *
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = new paypPayPalPlusPuiDataProvider();
    }

    public function testGetDataUtils()
    {
        $this->assertInstanceOf('paypPayPalPlusDataAccess', $this->SUT->getDataUtils());
    }

    public function testGetConverter()
    {
        $this->assertInstanceOf('paypPayPalPlusDataConverter', $this->SUT->getConverter());
    }

    public function testGetFields()
    {
        $this->assertSame(
            array('PaymentId', 'ReferenceNumber', 'DueDate', 'Total', 'Currency', 'BankName', 'AccountHolder', 'Iban', 'Bic', 'PuiObject'),
            $this->SUT->getFields()
        );
    }

    public function _testGetData_dataProviderNotInitializedWithPayPalApiRefundObject_returnAnArrayWithEmptyValues()
    {
        $this->assertSame(
            array(
                'PaymentInstructionInstructionType' => '',
                'PaymentId'                         => '',
                'ReferenceNumber'                   => '',
                'DueDate'                           => '',
                'Total'                             => '0.00',
                'Currency'                          => '',
                'BankName'                          => '',
                'AccountHolder'                     => '',
                'Iban'                              => '',
                'Bic'                               => '',
                'PuiObject'                         => null,
            ),
            $this->SUT->getData()
        );
    }


    /**
     * test `getData`, scenarios are defined in the data provider
     *
     * @param string $sTestCondition
     * @param string $sOrderId
     * @param array  $aPaymentData
     * @param array  $aExpectedReturn
     *
     * @dataProvider paymentDataProvider
     */
    public function testGetData($sTestCondition, $sOrderId, array $aPaymentData, array $aExpectedReturn)
    {
        if ($sOrderId && $aPaymentData) {
            $oOrder = $this->getMock('oxOrder', array('__construct', '__call', 'getId'));
            $oPayment = $this->_getPaymentObject($aPaymentData);
            $aExpectedReturn['PuiObject'] = $oPayment->getPaymentInstruction();

            $this->SUT->init($oOrder, $oPayment);
        } else {
            $aExpectedReturn['PuiObject'] = null;
        }

        $this->assertEquals(
            $aExpectedReturn,
            $this->SUT->getData(),
            $sTestCondition
        );
    }

    /**
     * Data provider for testing `getData`
     *
     * @return array
     */
    public function paymentDataProvider()
    {
        return array(
            array(
                'oxOrder and PayPal Payment objects are not initialized',
                '',
                array(),
                array(
                    'PaymentInstructionInstructionType' => '',
                    'PaymentId'                         => '',
                    'ReferenceNumber'                   => '',
                    'DueDate'                           => '',
                    'Total'                             => '0.00',
                    'Currency'                          => '',
                    'BankName'                          => '',
                    'AccountHolder'                     => '',
                    'Iban'                              => '',
                    'Bic'                               => '',
                    'PuiObject'                         => null,
                ),
            ),
            array(
                'oxOrder and PayPal Payment objects are initialized',
                'testOrderId',
                array(
                    'InstructionType'                => 'PAY_UPON_INVOICE',
                    'ReferenceNumber'                => 'ABC',
                    'PaymentDueDate'                 => '2011-11-25 11:11:00',
                    'BankName'                       => 'TEST Bank',
                    'BankIdentifierCode'             => 'BIC',
                    'InternationalBankAccountNumber' => 'IBAN',
                    'AccountHolderName'              => 'John Doe',
                    'saleId'                         => 'testSaleId',
                    'dateCreated'                    => '2011-11-11 11:11:00',
                    'total'                          => '11.11',
                    'currency'                       => 'EUR',
                    'paymentId'                      => 'testPaymentId',
                    'status'                         => 'created',
                ),
                array(
                    'PaymentInstructionInstructionType' => 'PAY_UPON_INVOICE',
                    'PaymentId'                         => 'testPaymentId',
                    'ReferenceNumber'                   => 'ABC',
                    'DueDate'                           => '2011-11-25 11:11:00',
                    'Total'                             => '11.11',
                    'Currency'                          => 'EUR',
                    'BankName'                          => 'TEST Bank',
                    'AccountHolder'                     => 'John Doe',
                    'Iban'                              => 'IBAN',
                    'Bic'                               => 'BIC',
                ),
            ),
        );
    }


    /**
     * `__call` should parse getters and return data value where it matches data provider fields.
     */
    public function testMagicCall()
    {
        $aPaymentData = $this->paymentDataProvider();

        $oOrder = $this->getMock('oxOrder', array('__construct', '__call'));
        $oPayment = $this->_getPaymentObject($aPaymentData[1][2]);

        $this->SUT->init($oOrder, $oPayment);

        $this->assertNull($this->SUT->someNoneExistentProperty());

        $this->assertSame('PAY_UPON_INVOICE', $this->SUT->getPaymentInstructionInstructionType());
        $this->assertSame('testPaymentId', $this->SUT->getPaymentId());
        $this->assertSame('ABC', $this->SUT->getReferenceNumber());
        $this->assertSame('2011-11-25 11:11:00', $this->SUT->getDueDate());
        $this->assertSame('11.11', $this->SUT->getTotal());
        $this->assertSame('EUR', $this->SUT->getCurrency());
        $this->assertSame('TEST Bank', $this->SUT->getBankName());
        $this->assertSame('John Doe', $this->SUT->getAccountHolder());
        $this->assertSame('IBAN', $this->SUT->getIban());
        $this->assertSame('BIC', $this->SUT->getBic());
    }

    /**
     * Form a PayPal Api Payment object from the data array
     *
     * @param array $aPaymentData
     *
     * @return \PayPal\Api\Payment
     */
    protected function _getPaymentObject($aPaymentData)
    {
        $oSale = new PayPal\Api\Sale();
        $oSale->setId($aPaymentData['saleId']);
        $oSale->setState($aPaymentData['status']);

        $oRelatedResource = new PayPal\Api\RelatedResources();
        $oRelatedResource->setSale($oSale);

        $oAmount = new PayPal\Api\Amount();
        $oAmount->setTotal($aPaymentData['total']);
        $oAmount->setCurrency($aPaymentData['currency']);

        $oTransaction = new PayPal\Api\Transaction();
        $oTransaction->setRelatedResources(array($oRelatedResource));
        $oTransaction->setAmount($oAmount);

        $oRecipientBankingInstruction = new PayPal\Api\RecipientBankingInstruction();
        $oRecipientBankingInstruction->setBankName($aPaymentData['BankName']);
        $oRecipientBankingInstruction->setBankIdentifierCode($aPaymentData['BankIdentifierCode']);
        $oRecipientBankingInstruction->setInternationalBankAccountNumber($aPaymentData['InternationalBankAccountNumber']);
        $oRecipientBankingInstruction->setAccountHolderName($aPaymentData['AccountHolderName']);

        $oCurrency = new PayPal\Api\Currency();
        $oCurrency->setValue($aPaymentData['total']);
        $oCurrency->setCurrency($aPaymentData['currency']);

        $oPaymentInstruction = new PayPal\Api\PaymentInstruction();
        $oPaymentInstruction->setInstructionType($aPaymentData['InstructionType']);
        $oPaymentInstruction->setReferenceNumber($aPaymentData['ReferenceNumber']);
        $oPaymentInstruction->setPaymentDueDate($aPaymentData['PaymentDueDate']);
        $oPaymentInstruction->setAmount($oCurrency);
        $oPaymentInstruction->setRecipientBankingInstruction($oRecipientBankingInstruction);


        $oPayment = new PayPal\Api\Payment();
        $oPayment->setTransactions(array($oTransaction));
        $oPayment->setCreateTime($aPaymentData['dateCreated']);
        $oPayment->setId($aPaymentData['paymentId']);
        $oPayment->setPaymentInstruction($oPaymentInstruction);

        return $oPayment;
    }
}
