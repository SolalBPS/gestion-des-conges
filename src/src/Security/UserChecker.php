<?php


namespace App\Security;

use App\Entity\Salarie;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{

    public function checkPreAuth(UserInterface $salarie)
    {
        if (!$salarie instanceof Salarie) {
            return;
        }
    }

    public function checkPostAuth(UserInterface $salarie)
    {
        if (!$salarie instanceof Salarie) {
            return;
        }
    }
}
