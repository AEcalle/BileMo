<?php

declare(strict_types=1);

namespace App\Serializer;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

final class PrefixNameConverter implements NameConverterInterface
{
    public function normalize(string $propertyName): string
    {
        if ($propertyName === 'links' || $propertyName === 'embedded'){
            return '_'.$propertyName;
        }
        return $propertyName;
    }

    public function denormalize(string $propertyName): string
    {
        return '_' === substr($propertyName, 0, 1) ? substr($propertyName, 1) : $propertyName;
    }
}
