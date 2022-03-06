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
class UserFactory implements FactoryInterface
{
    protected TokenGeneratorInterface $tokenGenerator;
    protected UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function create(?string $email = null, ?string $password = null): User
    {
        $entity = new User();

        if (null !== $email) {
            $entity->setEmail($email);
        }

        if (null !== $password) {
            $entity->setPassword($this->encoder->encodePassword($entity, $password));
        }

        return $entity;
    }
}
