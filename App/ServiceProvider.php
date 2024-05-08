<?php

declare(strict_types=1);

namespace App;

use ReflectionClass;

class ServiceProvider extends ReflectionClass
{
    private string $classMethod;

    private array $dependencies = [];

    public function __construct(string $className, string $classMethod)
    {
        $this->classMethod = $classMethod;
        parent::__construct($className);
    }

    public function has(): ?array
    {
        try {
            $method =  $this->getMethod($this->classMethod);
        } catch (\ReflectionException $e) {
            return null;
        }

        $methodParameters = $method->getParameters();

        foreach ($methodParameters as $parameter) {
            $parameterType = $parameter->getType();

            $parameterClass = $parameterType->getName();

            if (class_exists($parameterClass)) {
                $this->dependencies[] = $parameterClass;
            }
        }

        return $this->dependencies;
    }
}
