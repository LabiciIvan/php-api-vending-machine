<?php

namespace App\Requests;

use App\VM;
use App\Request;

class CategoryRequest extends Request {

    public function __construct()
    {
        parent::__construct();
    }

    public function validateShow(): void
    {
        VM::validateURLParameters($this->params, 1, [CATEGORY]);
    }

    public function validateCreate(): void
    {
        if ($this->body === null) {
            VM::sendResponse(VM::toJson(['error' => 'Data sent is not in the right format']), 400);
        }

        $validationError = VM::validateRequestData(
            [
                CATEGORY => 'string'
            ],
            $this->body
        );

        if ($validationError) {
            VM::sendResponse(VM::toJson(['error' => $validationError]), 400);
        }
    }

    public function validateUpdate(): void
    {
        if ($this->body === null) {
            VM::sendResponse(VM::toJson(['error' => 'Data sent is not in the right format']), 400);
        }

        $validationError = VM::validateRequestData(
            [
                CATEGORY        => 'string',
                NEW_CATEGORY    => 'string',
            ],
            $this->body
        );

        if ($validationError) {
            VM::sendResponse(VM::toJson(['error' => $validationError]), 400);
        }
    }

    public function validateDelete(): void
    {
        if ($this->body === null) {
            VM::sendResponse(VM::toJson(['error' => 'Data sent is not in the right format']), 400);
        }

        $validationError = VM::validateRequestData(
            [
                CATEGORY => 'string'
            ],
            $this->body
        );

        if ($validationError) {
            VM::sendResponse(VM::toJson(['error' => $validationError]), 400);
        }
    }
}