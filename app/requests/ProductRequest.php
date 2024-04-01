<?php

namespace App\Requests;

use App\VM;
use App\Request;

class ProductRequest extends Request {

    public function validateShow(): void
    {
        VM::validateURLParameters($this->params, 2, [CATEGORY, ID]);
    }

    public function validateCreate(): void
    {
        $validationError = VM::validateRequestData(
            [
                CATEGORY    => 'string',
                PRICE       => 'string',
                NAME        => 'string',
                QUANTITY    => 'int'
            ],
            $this->body
        );

        if ($validationError) {
            VM::sendResponse(VM::toJson(['errors' => $validationError]), 400);
        }
    }

    public function validateUpdate(): void
    {
        $validationError = VM::validateRequestData(
            [
                ID          => 'int',
                CATEGORY    => 'string',
                NAME        => 'string',
                PRICE       => 'string',
                QUANTITY    => 'int'
            ],
            $this->body
        );

        if ($validationError) {
            VM::sendResponse(VM::toJson(['errors' => $validationError]), 400);
        }
    }

    public function validatePatch(): void
    {
        $validationError = VM::validateRequestData(
            [
                ID          => 'int', 
                CATEGORY    => 'string'
            ], 
            $this->body
        );

        if ($validationError) {
            VM::sendResponse(VM::toJson(['errors' => $validationError]), 400);
        }

        $allowedToPatch = [NAME => 'string', PRICE => 'string', QUANTITY => 'int'];

        $typeErrors = VM::validateType($allowedToPatch, $this->body, true);

        if ($typeErrors) {
            VM::sendResponse(VM::toJson(['error' => $typeErrors]), 400);
        }
    }

    public function validateDelete(): void
    {
        VM::validateURLParameters($this->params, 2, [CATEGORY, ID]);
    }

    public function validatePay(): void
    {
        VM::validateRequestData(
            [
                ID              => 'int',
                CATEGORY        => 'string',
                PAYMENT_AMOUNT  => 'string'
            ],
            $this->body
        );
    }
}