<?php


namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Bundle\SecurityBundle\Security;

class UserVoter extends Voter
{
    const EDIT = 'EDIT';
    const DELETE = 'DELETE';

    public function __construct(private Security $security)
    {
    }

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [self::EDIT, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var User $targetUser */
        $targetUser = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($targetUser, $user);
            case self::DELETE:
                return $this->canDelete($targetUser, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canEdit(User $targetUser, User $user): bool
    {
        // Админ может редактировать любого
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // Пользователь может редактировать только себя
        return $user->getId() === $targetUser->getId();
    }

    private function canDelete(User $targetUser, User $user): bool
    {
        // Админ может удалять любого
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // Пользователь может удалять только себя
        return $user->getId() === $targetUser->getId();
    }
}