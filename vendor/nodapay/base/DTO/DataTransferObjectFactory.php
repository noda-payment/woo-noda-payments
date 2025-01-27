<?php

declare(strict_types=1);

namespace NodaPay\Base\DTO;

class DataTransferObjectFactory
{
    /**
     * @param string $type
     * @param array $data
     * @return DynamicDataObject
     */
    public static function create(string $type, array $data = []): DynamicDataObject
    {
        return new $type($data);
    }
}