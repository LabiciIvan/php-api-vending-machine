<?php

declare(strict_types=1);

namespace App\Abstracts;

use App\Interfaces\ValidatorInterface;

abstract class AbstractValidator implements ValidatorInterface
{
    protected $availableMethods = [
        'int' => 'isInt',
        'string' => 'isString',
        'array' => 'isArray',
        'required' => 'isRequired',
    ];

    public function validate(array $requiredRules, array $data): array
    {
        $errors = [];

        foreach ($requiredRules as $field => $string) {

            $rules = explode('|', $string);

            $isRequired = in_array('required', $rules);

            foreach ($rules as $rule) {
                if (!$isRequired && !isset($data[$field])) {
                    break;
                }

                if ($isRequired && !isset($data[$field])) {
                    $errors[$field][] = $this->isRequired($field, $data);
                    break;
                }

                $validationMethod = $this->availableMethods[$rule] ?? null;

                /** @var string|null $validationError */
                $validationError = $validationMethod ? $this->$validationMethod($field, $data) : null;

                if ($validationError !== null) {
                    if (!isset($errors[$field])) {
                        $errors[$field] = [];
                    }

                    $errors[$field][] = $validationError;
                }
            }
        }

        return $errors;
    }

    public function isInt(string $field, mixed $data): ?string
    {
        if (is_int($data[$field])) {
            return null;
        }

        return "The '{$field}' value for this field must be an integer";
    }

    public function isString(string $field, mixed $data): ?string
    {
        if (is_string($data[$field])) {
            return null;
        }

        return "The '{$field}' value for this field must be a string";
    }

    public function isArray(string $field, mixed $data): ?string
    {
        if (is_array($data[$field])) {
            return null;
        }

        return "The '{$field}' value for this field must be an array";
    }

    public function isRequired(string $field, array $data): ?string
    {
        if (isset($data[$field])) {
            return null;
        }

        return "The '{$field}' field is required";
    }
}
