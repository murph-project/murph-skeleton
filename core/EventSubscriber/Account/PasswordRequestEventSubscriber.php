<?php

namespace App\Core\EventSubscriber\Account;

use App\Core\Event\Account\PasswordRequestEvent;
use App\Core\Manager\EntityManager;
use App\Core\Notification\MailNotifier;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * class EventListener.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class PasswordRequestEventSubscriber implements EventSubscriberInterface
{
    protected MailNotifier $notifier;
    protected UrlGeneratorInterface $urlGenerator;
    protected EntityManager $entityManager;
    protected TokenGeneratorInterface $tokenGenerator;
    protected TranslatorInterface $translator;

    public function __construct(
        MailNotifier $notifier,
        UrlGeneratorInterface $urlGenerator,
        EntityManager $entityManager,
        TokenGeneratorInterface $tokenGenerator,
        TranslatorInterface $translator
    ) {
        $this->notifier = $notifier;
        $this->urlGenerator = $urlGenerator;
        $this->entityManager = $entityManager;
        $this->tokenGenerator = $tokenGenerator;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents()
    {
        return [
            PasswordRequestEvent::EVENT => 'onRequest',
        ];
    }

    public function onRequest(PasswordRequestEvent $event)
    {
        $user = $event->getUser();
        $user->setConfirmationToken($this->tokenGenerator->generateToken());
        $user->setPasswordRequestedAt(new \DateTime('now'));

        $this->entityManager->update($user);

        $this->notifier
            ->setSubject($this->translator->trans('Mot de passe perdu'))
            ->addRecipient($user->getEmail())
            ->notify('@Core/mail/account/resetting_request.html.twig', [
                'reseting_update_link' => $this->urlGenerator->generate(
                    'auth_resetting_update',
                    [
                        'token' => $user->getConfirmationToken(),
                    ],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
            ])
        ;
    }
}
