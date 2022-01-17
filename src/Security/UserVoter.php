<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\CacheableVoterInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class UserVoter implements CacheableVoterInterface
{
    public function vote(TokenInterface $token, mixed $subject, array $attributes): int
    {
        if ($token->getUser() === $subject->getCustomer()) {
            return VoterInterface::ACCESS_GRANTED;
        }

        return VoterInterface::ACCESS_DENIED;
    }
    
    
    public function supportsAttribute(string $attribute): bool
    {
        return true;
    }

    public function supportsType(string $subjectType): bool
    {
        return User::class === $subjectType;
    }
}
