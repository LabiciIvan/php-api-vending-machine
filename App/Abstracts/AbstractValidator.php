<?php

declare(strict_types=1);

namespace App\Abstracts;

use App\Interfaces\Validation\ValidatorInterface;

abstract class AbstractValidator implements ValidatorInterface
{
    public array $errors = [];

    protected $availableMethods = [
        'int'       => 'isInt',
        'string'    => 'isString',
        'array'     => 'isArray',
        'required'  => 'isRequired',
    ];

    public function validate(array $requiredRules, array $data): bool
    {
        $errors = [];

        foreach ($requiredRules as $field => $string) {

            $rules = explode('|', $string);

            $isRequired = in_array('required', $rules) ? true : false;

            foreach ($rules as $rule) {

                if (!$isRequired && !isset($data[$field])) {
                    break;
                }

                if ($isRequired && !isset($data[$field])) {
                    $errors[$field][] = $this->isRequired($field, $data);
                    break;
                }

                $validationMethod = $this->availableMethods[$rule] ?? false;

                if ($validationMethod) {

                    $validationError = $this->$validationMethod($field, $data);

                    if ($validationError !== null) {

                        if (!isset($errors[$field])) {
                            $errors[$field] = [];
                        }

                        $errors[$field][] = $validationError;
                    }
                }
            }
        }

        $this->errors[] = $errors;

        return $errors ? true : false;
    }

    public function isInt(string $field, mixed $data): ?string
    {
        if (is_int($data[$field])) {
            return null;
        }

        return 'The value for this field must be an integer';
    }

    public function isString(string $field, mixed $data): ?string
    {
        if (is_string($data[$field])) {
            return null;
        }

        return 'The value for this field must be a string';
    }

    public function isArray(string $field, mixed $data): ?string
    {
        if (is_string($data[$field])) {
            return null;
        }

        return 'The value for this field must be an array';
        return is_array($data);
    }

    public function isRequired(string $field, array $data): ?string
    {
        if (isset($data[$field])) {
            return null;
        }

        return 'This field is required';
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}