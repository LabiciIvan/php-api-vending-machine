<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Abstracts\AbstractValidator;

class ValidatorTest extends TestCase
{
    public $validateClass;

    public function setUp(): void
    {
        $this->validateClass = new class extends AbstractValidator {};
    }

    public function testValidationFailed(): void
    {
        $requestData = [
            'category'  => 'A',
            'id'        => 1
        ];

        $rules = [
            'category'  => 'string|required',
            'id'        => 'string|required'
        ];

        $validationError = $this->validateClass->validate($rules, $requestData);

        $this->assertTrue($validationError);
    }

    public function testValidationPassed(): void
    {
        $requestData = [
            'id' => 1
        ];

        $rules = [
            'id' => 'int'
        ];

        $validationError = $this->validateClass->validate($rules, $requestData);

        $this->assertFalse($validationError);
    }

    public function testValidationOnMissingFieldsInRequest(): void
    {
        $requestData = [
            'category' => 1
        ];

        $rules = [
            'id' => 'int'
        ];

        $validationError = $this->validateClass->validate($rules, $requestData);

        $this->assertFalse($validationError);
    }

    public function testErrorMessageOnFailedValidation(): void
    {
        $requestData = [
            'id' => '1',
            'price' => 12
        ];

        $rules = [
            'id' => 'int',
            'price' => 'string',
            'category' => 'required'
        ];

        $this->validateClass->validate($rules, $requestData);

        $errorMessage = $this->validateClass->getErrors();

        $errorInt = $errorMessage[0]['id'][0];
        $errorString = $errorMessage[0]['price'][0];
        $errorRequired = $errorMessage[0]['category'][0];

        $this->assertSame('The value for this field must be an integer', $errorInt);
        $this->assertSame('The value for this field must be a string', $errorString);
        $this->assertSame('This field is required', $errorRequired);
    }
}