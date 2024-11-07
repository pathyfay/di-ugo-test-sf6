<?php

namespace App\Traits;

use Symfony\Component\PropertyAccess\PropertyAccess;

Trait UtilsTrait
{
    function convertArrayObjectToArray(array $arrayEntity, array $properties = []): array
    {
        $datas = [];
        foreach ($arrayEntity as $entity) {
            $datas[] = $this->convertObjectToArray($entity, $properties);
        }

        return $datas;
    }
    function convertObjectToArray(object $entity, array $properties = []): array
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $data = [];

        if (empty($properties)) {
            $reflectionClass = new \ReflectionClass($entity);
            $properties = array_map(fn($prop) => $prop->getName(), $reflectionClass->getProperties());
        }

        foreach ($properties as $property) {
            try {
                $data[$property] = $propertyAccessor->getValue($entity, $property);
            } catch (\Exception $e) {
                $data[$property] = null;
            }
        }

        return $data;
    }
}