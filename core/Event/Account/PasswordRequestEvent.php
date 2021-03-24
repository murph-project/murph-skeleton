<?php

namespace App\Core\Event\Account;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * class PasswordRequestEvent.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class PasswordRequestEvent extends Event
{
    const EVENT = 'account_event.password_request';

    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): USer
    {
        return $this->user;
    }
}
