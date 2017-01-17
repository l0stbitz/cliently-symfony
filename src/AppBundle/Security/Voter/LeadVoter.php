<?php
namespace AppBundle\Security\Voter;

use AppBundle\Entity\Lead;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class LeadVoter extends Voter
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

        // only vote on Lead objects inside this voter
        if (!$subject instanceof Lead) {
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

        // you know $subject is a Lead object, thanks to supports
        /**
 * @var Lead $lead 
*/
        $lead = $subject;

        switch ($attribute) {
        case self::VIEW:
            return $this->canView($lead, $user);
        case self::EDIT:
            return $this->canEdit($lead, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Lead $lead, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($lead, $user)) {
            return true;
        }
        return false;
    }

    private function canEdit(Lead $lead, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $lead->getOwner();
    }
}