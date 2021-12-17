<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

final class UserNormalizer implements ContextAwareNormalizerInterface
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
     * @return array<string, array<string, string>>
     */
    public function normalize($user, string $format = null, array $context = []): array
    {

        $data = $this->normalizer->normalize($user, $format, $context);

        $data['_links']['self'] = $this->router->generate('api_users_item_get', [
            'id' => $user->getId(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);
        $data['_links']['post'] = $this->router->generate('api_users_collection_post', [],
        UrlGeneratorInterface::ABSOLUTE_URL);
        $data['_links']['put'] = $this->router->generate('api_users_item_put', [
            'id' => $user->getId(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);
        $data['_links']['delete'] = $this->router->generate('api_users_item_delete', [
            'id' => $user->getId(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        return $data;
    }

    /**
     * @param array<string, mixed> $context
     */
    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof User;
    }
}
