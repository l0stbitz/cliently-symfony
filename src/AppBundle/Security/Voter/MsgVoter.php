<?php
namespace AppBundle\Security\Voter;

use AppBundle\Entity\Msg;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MsgVoter extends Voter
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

        // only vote on Msg objects inside this voter
        if (!$subject instanceof Msg) {
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

        // you know $subject is a Msg object, thanks to supports
        /**
 * @var Msg $msg 
*/
        $msg = $subject;

        switch ($attribute) {
        case self::VIEW:
            return $this->canView($msg, $user);
        case self::EDIT:
            return $this->canEdit($msg, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Msg $msg, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($msg, $user)) {
            return true;
        }
        return false;
    }

    private function canEdit(Msg $msg, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user->getId() === $msg->getOwnerId();
    }
}