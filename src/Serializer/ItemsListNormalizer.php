<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\ItemsList;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

final class ItemsListNormalizer implements ContextAwareNormalizerInterface
{
    private ObjectNormalizer $normalizer;

    public function __construct(ObjectNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * @param array<string, mixed> $context
     * @return array<string, array<string, string>>
     */
    public function normalize($itemsList, string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($itemsList, $format, $context);

        $data['_links'] = $data['links'];
        unset($data['links']);

        $data['_embedded'] = $data['embedded'];
        unset($data['embedded']);

        for ($i = 0; $i < count($data['_embedded']['items']); $i++) {
            unset($data['_embedded']['items'][$i]['customer']);
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof ItemsList;
    }
}