<?php

declare(strict_types=1);

namespace App\Validation;

use App\Abstracts\AbstractValidator;

class ValidateProduct extends AbstractValidator
{
    private const ID = 'id';

    private const CATEGORY_ID = 'category_id';

    private const PRICE = 'price';

    private const NAME = 'name';

    private const QUANTITY = 'quantity';


    public $rulesCreate = [
        self::CATEGORY_ID => 'int|required',
        self::PRICE => 'string|required',
        self::NAME => 'string|required',
        self::QUANTITY => 'int|required',
    ];

    public $rulesUpdate = [
        self::ID => 'int|required',
        self::CATEGORY_ID => 'int|required',
        self::NAME => 'string|required',
        self::PRICE => 'string|required',
        self::QUANTITY => 'int|required'
    ];

    public $rulesPatch = [
        self::ID => 'int|required', 
        self::CATEGORY_ID => 'int|required',
        self::NAME => 'string',
        self::PRICE => 'string',
        self::QUANTITY => 'int',
    ];
}
