<?php
namespace AppBundle\Security\Voter;

use AppBundle\Entity\AccountMember;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AccountMemberVoter extends Voter
{
    // these strings are just invented: you can use anything
    const VIEW = 'view';
    const EDIT = 'edit';

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::VIEW, self::EDIT))) {
            return false;
        }

        // only vote on AccountMember objects inside this voter
        if (!$subject instanceof AccountMember) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a AccountMember object, thanks to supports
        /**
 * @var AccountMember $accountMember 
*/
        $accountMember = $subject;

        switch ($attribute) {
        case self::VIEW:
            return $this->canView($accountMember, $user);
        case self::EDIT:
            return $this->canEdit($accountMember, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(AccountMember $accountMember, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($accountMember, $user)) {
            return true;
        }

        return false;
    }

    private function canEdit(AccountMember $accountMember, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $accountMember->getOwner();
    }
}