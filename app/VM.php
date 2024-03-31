<?php

namespace App;

use JsonException;

class VM {

    /**
    * @return array{endpoint:string,params:array,method:string}
    */
    public static function processURL(): array
    {
        $request = parse_url($_SERVER['REQUEST_URI']);
   
        $params = [];
   
        if (isset($request['query'])) {
            parse_str($request['query'], $params);
        }
   
        return [
            'endpoint' => str_replace('/', '', $request['path']), 
            'params' => $params,
            'method' => $_SERVER['REQUEST_METHOD'], 
        ];
    }

    public static function readProducts(string $path): ?array
    {
        $productsRead = @file_get_contents($path);

        if ($productsRead === false) {
            return null;
        }

        return VM::fromJson($productsRead);
    }

    public static function logErrors(string $customMessage, string $errorMessage, int $line): void
    {
        error_log(
            date('Y-m-d H:i:s') . " - Line: {$line} = {$customMessage} {$errorMessage} " . PHP_EOL,
            3,
            'errors.txt'
        );
    }

    public static function toJson(array $data): ?string
    {
        try {
            $jsonEncoded = json_encode($data, JSON_THROW_ON_ERROR, 512);
        } catch (JsonException $e) {
            VM::logErrors('Error encoding json', $e->getMessage(), __LINE__);
            return null;
        }

        return $jsonEncoded;
    }

    public static function fromJson(string $data): ?array
    {
        try {
            $jsonDecoded = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            VM::logErrors('Error decoding json', $e->getMessage(), __LINE__);
            return null;
        }

        return $jsonDecoded;
    }

    public static function sendResponse(string $message, int $statusCode): void
    {
        http_response_code($statusCode);

        echo $message;

        exit;
    }

    public static function saveProducts(array $data, $path): bool
    {
        if (!file_exists($path)) {
            return false;
        }

        $encodedData = VM::toJson($data);

        $isSaved = file_put_contents($path, $encodedData);

        return $isSaved !== false;
    }

        /**
     * @param array $params An array with params
     * 
     * @param array $paramsCount Allowed number of params in the url
     * 
     * @param array $paramsNames An array with required params names
     */
    public static function validateURLParameters(array $params, int $paramsCount, array $paramsNames): void
    {
        if (count($params) !== $paramsCount) {
            VM::sendResponse(VM::toJson(['error' => 'The URL format is incorrect']), 400);
        }

        if ($paramsCount > 0) {
            foreach ($paramsNames as $requiredParamsName) {
                if (!isset($params[$requiredParamsName])) {
                    VM::sendResponse(VM::toJson(['error' => 'Required URL params are not correct']), 400);
                }
            }
        }
    }

    public static function validateRequestData(array $mandatoryFields, ?array $requestData = null): ?string
    {
        if ($requestData === null) {
            return 'Sent data is not correct';
        }

        $missingFields = array_keys(array_diff_key($mandatoryFields, $requestData));

        if ($missingFields) {
            return 'Missing fields: ' . implode(', ', $missingFields);
        }

        $typeErrors = VM::validateType($mandatoryFields, $requestData);

        if($typeErrors) {
            return implode(', ', $typeErrors);
        }

        return null;
    }

    public static function validateType(array $rules, array $requestData, bool $optional = false): array
    {
        $errors = [];

        foreach ($rules as $key => $value) {
            switch ($value) {
                case 'int':
                    if ($optional && !isset($requestData[$key])) {
                        break;
                    }

                    if (!is_int($requestData[$key])) {
                        $errors[] = "Key {$key} must be {$value}";
                    }
                    break;
                case 'string':
                    if ($optional && !isset($requestData[$key])) {
                        break;
                    }

                    if (!is_string($requestData[$key])) {
                        $errors[] = "Key {$key} must be {$value}";
                    }
                    break;
                default:
                    $errors[] = "Can not check {$key} type";
                    break;
            }
        }

        return $errors;
    }

    public static function addToList(array &$products, array $requestData, ?array $oldProduct = null, bool $newId = false): void
    {
        $fields = [ID, NAME, PRICE, QUANTITY];

        foreach ($fields as $property) {
            $updatedProduct[$property] = $requestData[$property] ?? $oldProduct[$property] ?? null;
        }

        $category = $requestData[CATEGORY];

        if (!isset($products[$category])) {
            $products[$category] = [];
        }

        if ($newId) {
            $updatedProduct[ID] = VM::generateId($products[$category], ID);
            $products[$category][] = $updatedProduct;
        } else {
            foreach($products[$category] as &$product) {
                if ($product[ID] === $updatedProduct[ID]) {
                    $product = $updatedProduct;
                }
            }
        }
    }

    public static function generateId(array $categoryOfProducts, string $idKeyName): int
    {
        $id = 0;

        foreach ($categoryOfProducts as $product) {
            if ($product[$idKeyName] >= $id) {
                $id = $product[$idKeyName] + 1;
            }
        }

        return $id;
    }

    public static function printReceipt(string $name,string $price, string $paid, string $change): array
    {
        return [
            'name' => $name,
            'price' => $price,
            'paid' => number_format($paid, 2, '.'),
            'change' => number_format($change, 2, '.')
        ];
    }

    public static function validateProducts(array $data, array $dataKeysAndTypes): ?array
    {
        $errors = [];

        foreach ($data as $category => $products) {
            if (!is_string($category)) {
                $errors[] = "Category {$category} must be type of string";
            }

            if (!is_array($products)) {
                $errors[] = "Items of category {$category} must be an array";
            }

            if (is_array($products) && !empty($products)) {
                foreach ($products as $key => $product) {

                    $missing = array_diff_key($dataKeysAndTypes, $product);
                    $missingKeys = array_keys($missing);

                    $unwantedKeys = array_diff_key($product, $dataKeysAndTypes);

                    if ($missing) {
                        $errors[] = sprintf(
                            'Keys: %s are missing on element %s from category %s',
                            implode(', ', $missingKeys),
                            $key,
                            $category
                        );
                    } else {
                        $typeErrors = VM::validateType($dataKeysAndTypes, $product);

                        if ($typeErrors) {
                            $errors[] = sprintf(
                                'Keys at element %s from category %s must be: %s', 
                                $key,
                                $category,
                                implode(', ', $typeErrors)
                            );
                        }
                    }

                    if ($unwantedKeys) {
                        $errors[] = sprintf(
                            'Keys: %s are not accepted on element %s from category %s',
                            implode(', ', array_flip($unwantedKeys)),
                            $key,
                            $category
                        );
                    }
                }
            }
        }

        return $errors ? $errors : null;
    }
}