<?php
namespace AppBundle\Security\Voter;

use AppBundle\Entity\Account;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AccountVoter extends Voter
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

        // only vote on Account objects inside this voter
        if (!$subject instanceof Account) {
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

        // you know $subject is a Account object, thanks to supports
        /**
 * @var Account $account 
*/
        $account = $subject;

        switch ($attribute) {
        case self::VIEW:
            return $this->canView($account, $user);
        case self::EDIT:
            return $this->canEdit($account, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Account $account, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($account, $user)) {
            return true;
        }

        return false;
    }

    private function canEdit(Account $account, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $account->getOwner();
    }
}