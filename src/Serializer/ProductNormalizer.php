<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Product;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

final class ProductNormalizer implements ContextAwareNormalizerInterface
{
    private UrlGeneratorInterface $router;
    private ObjectNormalizer $normalizer;

    public function __construct(UrlGeneratorInterface $router, ObjectNormalizer $normalizer)
    {
        $this->router = $router;
        $this->normalizer = $normalizer;
    }

    /**
     * @param array<string, mixed> $context
     * @return array<string, string>
     */
    public function normalize($product, string $format = null, array $context = []): array
    {

        $data = $this->normalizer->normalize($product, $format, $context);

        $data['_links']['self'] = $this->router->generate('api_products_item_get', [
            'id' => $product->getId(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        return $data;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Product;
    }
}
