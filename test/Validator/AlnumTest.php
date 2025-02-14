<?php

namespace LaminasTest\I18n\Validator;

use Laminas\I18n\Validator\Alnum as AlnumValidator;
use PHPUnit\Framework\TestCase;

/**
 * @group      Laminas_Validator
 */
class AlnumTest extends TestCase
{
    /**
     * @var AlnumValidator
     */
    protected $validator;

    /**
     * Creates a new Alnum object for each test method
     *
     * @return void
     */
    protected function setUp(): void
    {
        if (! extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $this->validator = new AlnumValidator();
    }

    /**
     * Ensures that the validator follows expected behavior for basic input values
     *
     * @return void
     */
    public function testExpectedResultsWithBasicInputValues()
    {
        $valuesExpected = [
            'abc123'  => true,
            'abc 123' => false,
            'abcxyz'  => true,
            'AZ@#4.3' => false,
            'aBc123'  => true,
            ''        => false,
            ' '       => false,
            "\n"      => false,
            'foobar1' => true
        ];
        foreach ($valuesExpected as $input => $result) {
            $this->assertEquals($result, $this->validator->isValid($input));
        }
    }

    /**
     * Ensures that getMessages() returns expected initial value
     *
     * @return void
     */
    public function testMessagesEmptyInitially()
    {
        $this->assertEquals([], $this->validator->getMessages());
    }

    /**
     * Ensures that the allowWhiteSpace option works as expected
     *
     * @return void
     */
    public function testOptionToAllowWhiteSpaceWithBasicInputValues()
    {
        $this->validator->setAllowWhiteSpace(true);

        $valuesExpected = [
            'abc123'  => true,
            'abc 123' => true,
            'abcxyz'  => true,
            'AZ@#4.3' => false,
            'aBc123'  => true,
            ''        => false,
            ' '       => true,
            "\n"      => true,
            " \t "    => true,
            'foobar1' => true
            ];
        foreach ($valuesExpected as $input => $result) {
            $this->assertEquals(
                $result,
                $this->validator->isValid($input),
                "Expected '$input' to be considered " . ($result ? '' : 'in') . 'valid'
            );
        }
    }

    /**
     * @return void
     */
    public function testEmptyStringValueResultsInProperValidationFailureMessages()
    {
        $this->assertFalse($this->validator->isValid(''));

        $messages = $this->validator->getMessages();
        $arrayExpected = [
            AlnumValidator::STRING_EMPTY => 'The input is an empty string'
        ];
        $this->assertThat($messages, $this->identicalTo($arrayExpected));
    }

    /**
     * @return void
     */
    public function testInvalidValueResultsInProperValidationFailureMessages()
    {
        $this->assertFalse($this->validator->isValid('#'));
        $messages = $this->validator->getMessages();
        $arrayExpected = [
            AlnumValidator::NOT_ALNUM => 'The input contains characters which are non alphabetic and no digits'
        ];
        $this->assertThat($messages, $this->identicalTo($arrayExpected));
    }

    /**
     * @Laminas-4352
     */
    public function testNonStringValidation()
    {
        $this->assertFalse($this->validator->isValid([1 => 1]));
    }

    /**
     * @Laminas-7475
     */
    public function testIntegerValidation()
    {
        $this->assertTrue($this->validator->isValid(1));
    }

    public function testEqualsMessageTemplates()
    {
        $validator = $this->validator;

        $this->assertSame($validator->getOption('messageTemplates'), $validator->getMessageTemplates());
    }
}
