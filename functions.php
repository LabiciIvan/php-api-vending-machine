<?php

function logErrors(string $customMessage, string $errorMessage, int $line): void
{
    error_log(
        date('Y-m-d H:i:s') . " - Line: {$line} = {$customMessage} {$errorMessage} " . PHP_EOL,
        3,
        'errors.txt'
    );
}

function toJson(array $data): ?string
{
    try {
        $jsonEncoded = json_encode($data, JSON_THROW_ON_ERROR, 512);
    } catch (JsonException $e) {
        logErrors('Error encoding json', $e->getMessage(), __LINE__);
        return null;
    }

    return $jsonEncoded;
}

function fromJson(string $data): ?array
{
    try {
        $jsonDecoded = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
        logErrors('Error decoding json', $e->getMessage(), __LINE__);
        return null;
    }

    return $jsonDecoded;
}

function sendResponse(string $message, int $statusCode): void
{
    http_response_code($statusCode);

    echo $message;

    exit;
}

/**
 * @return array{endpoint:string,params:array,method:string}
 */
function processURL(): array
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

function readProducts(string $path): ?array
{
    $productsRead = @file_get_contents($path);

    if ($productsRead === false) {
        return null;
    }

    return fromJson($productsRead);
}

function extractProduct(array $products, string $category, ?int $id = null): ?array
{
    if (!isset($products[$category])) {
        return null;
    }

    if ($id !== null) {
        foreach ($products[$category] as $product) {
            if ($product['id'] == $id) {
                return $product;
            }
        }

        return null;
    }

    return $products[$category];
}

function addToList(array &$products, array $requestData, ?array $oldProduct = null, bool $newId = false): void
{
    $fields = [ID, NAME, PRICE, QUANTITY];

    foreach ($fields as $property) {
        $updatedProduct[$property] = (
            isset($requestData[$property]) ? $requestData[$property] :
            (isset($oldProduct[$property]) ? $oldProduct[$property] : null)
        );
    }

    if (!isset($products[$requestData[CATEGORY]])) {
        $products[$requestData[CATEGORY]] = [];
    }

    if ($newId) {
        $updatedProduct[ID] = generateId($products[$requestData[CATEGORY]], ID);
        $products[$requestData[CATEGORY]][] = $updatedProduct;
    } else {
        foreach($products[$requestData[CATEGORY]] as &$product) {
            if ($product[ID] === $updatedProduct[ID]) {
                $product = $updatedProduct;
            }
        }
    }
}

function generateId(array $categoryOfProducts, string $idKeyName): int
{
    $id = 0;

    foreach ($categoryOfProducts as $product) {
        if ($product[$idKeyName] >= $id) {
            $id = $product[$idKeyName] + 1;
        }
    }

    return $id;
}

function validateRequestData(array $mandatoryFields, ?array $requestData = null): void
{
    if ($requestData === null) {
        sendResponse(toJson(['error' => 'Sent data is not correct']), 414);
    }

    $missingFields = array_keys(array_diff_key($mandatoryFields, $requestData));

    if ($missingFields) {
        sendResponse(toJson(['error' => 'Missing fields: ' . implode(', ', $missingFields)]), 400);
    }

    $typeErrors = validateType($mandatoryFields, $requestData);

    if($typeErrors) {
        sendResponse(toJson(['error' => implode(', ', $typeErrors)]), 500);
    }
}

function validateType(array $rules, array $requestData, bool $optional = false): ?array
{
    $errors = [];

    foreach ($rules as $key => $value) {
        switch ($value) {
            case 'int':
                if ($optional && isset($requestData[$key]) && !is_int($requestData[$key])) {
                    $errors[] = "Key {$key} must be {$value}";
                } elseif (!$optional && !is_int($requestData[$key])) {
                    $errors[] = "Key {$key} must be {$value}";
                }
                break;
            case 'string':
                if ($optional && isset($requestData[$key]) && !is_string($requestData[$key])) {
                    $errors[] = "Key {$key} must be {$value}";
                } elseif (!$optional && !is_string($requestData[$key])) {
                    $errors[] = "Key {$key} must be {$value}";
                }
                break;
            default:
                $errors[] = "Can not check {$key} type";
                break;
        }
    }

    return $errors ? $errors : null;
}

/**
 * @param array $params An array with params
 * 
 * @param array $paramsCount Allowed number of params in the url
 * 
 * @param array $paramsNames An array with required params names
 */
function validateURLParameters(array $params, int $paramsCount, array $paramsNames): void
{
    if (count($params) !== $paramsCount) {
        sendResponse(toJson(['error' => 'The URL format is incorrect']), 400);
    }

    if ($paramsCount > 0) {
        foreach ($paramsNames as $requiredParamsName) {
            if (!isset($params[$requiredParamsName])) {
                sendResponse(toJson(['error' => 'Required URL params are not correct']), 400);
            }
        }
    }
}

function validateProducts(array $data, array $dataKeysAndTypes): ?array
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
                    $typeErrors = validateType($dataKeysAndTypes, $product);

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

function saveProducts(array $data, $path): bool
{
    if (!file_exists($path)) {
        return false;
    }

    $encodedData = toJson($data);

    $isSaved = file_put_contents($path, $encodedData);

    return $isSaved !== false;
}

function printReceipt(string $name,string $price, string $paid, string $change): array
{
    return [
        'name' => $name,
        'price' => $price,
        'paid' => number_format($paid, 2, '.'),
        'change' => number_format($change, 2, '.')
    ];
}