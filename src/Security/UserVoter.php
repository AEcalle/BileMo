<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Customer;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{

    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    protected function supports(string $attribute, $subject): bool
    {

        if (!in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $customer = $token->getUser();

        if (!$customer instanceof Customer) {
            return false;
        }

        $user = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($user, $customer);
            case self::EDIT:
                return $this->canEdit($user, $customer);
            case self::DELETE:
                return $this->canDelete($user, $customer);
        }

    }

    private function canView(User $user, Customer $customer): bool
    {
        if ($this->canEdit($user, $customer)) {
            return true;
        }

        return false;
    }

    private function canDelete(User $user, Customer $customer): bool
    {
        if ($this->canEdit($user, $customer)) {
            return true;
        }

        return false;
    }

    private function canEdit(User $user, Customer $customer): bool
    {
        return $customer === $user->getCustomer();
    }
}