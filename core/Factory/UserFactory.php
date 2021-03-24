<?php

namespace App\Core\Factory;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

/**
 * class UserFactory.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class UserFactory
{
    protected TokenGeneratorInterface $tokenGenerator;
    protected UserPasswordEncoderInterface $encoder;

    public function __construct(TokenGeneratorInterface $tokenGenerator, UserPasswordEncoderInterface $encoder)
    {
        $this->tokenGenerator = $tokenGenerator;
        $this->encoder = $encoder;
    }

    public function create(): User
    {
        $entity = new User();

        $entity->setPassword($this->encoder->encodePassword(
            $entity,
            $this->tokenGenerator->generateToken()
        ));

        return $entity;
    }
}
