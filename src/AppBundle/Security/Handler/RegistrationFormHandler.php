<?php
// src/AppBundle/Form/Handler/RegistrationFormHandler.php

namespace AppBundle\Security\Handler;

use FOS\UserBundle\Form\Handler\RegistrationFormHandler as BaseHandler;

class RegistrationFormHandler extends BaseHandler
{
    public function process($confirmation = false)
    {
        $user = $this->userManager->createUser();
        $this->form->setData($user);
        echo $usr->getId();exit;
        if ('POST' == $this->request->getMethod()) {
            $this->form->bind($this->request);
            if ($this->form->isValid()) {

                // do your custom logic here

                return true;
            }
        }

        return false;
    }
}