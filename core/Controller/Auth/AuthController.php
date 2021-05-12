<?php

namespace App\Core\Controller\Auth;

use App\Core\Event\Account\PasswordRequestEvent;
use App\Core\Manager\EntityManager;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use ZxcvbnPhp\Zxcvbn;

class AuthController extends AbstractController
{
    protected array $coreParameters;

    public function __construct(ParameterBagInterface $parameters)
    {
        $this->coreParameters = $parameters->get('core');
    }

    /**
     * @Route("/login", name="auth_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('admin_dashboard_index');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@Core/auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'site_name' => $this->coreParameters['site']['name'],
            'site_logo' => $this->coreParameters['site']['logo'],
        ]);
    }

    /**
     * @Route("/resetting/request", name="auth_resetting_request")
     */
    public function requestResetting(Request $request, UserRepository $repository, EventDispatcherInterface $eventDispatcher): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('admin_dashboard_index');
        }

        if ($request->isMethod('POST')) {
            $csrfToken = $request->request->get('_csrf_token');

            if (!$this->isCsrfTokenValid('resetting_request', $csrfToken)) {
                throw $this->createAccessDeniedException();
            }

            $username = trim((string) $request->request->get('username'));

            if (!$username) {
                throw $this->createAccessDeniedException();
            }

            $account = $repository->findOneByEmail($username);

            if ($account) {
                $requestedAt = $account->getPasswordRequestedAt();

                if (null === $requestedAt || $requestedAt->getTimestamp() < (time() - 3600 / 2)) {
                    $eventDispatcher->dispatch(new PasswordRequestEvent($account), PasswordRequestEvent::EVENT);
                }
            }
        }

        return $this->render('@Core/auth/resetting_request.html.twig', [
            'email_sent' => $request->isMethod('POST'),
            'site_name' => $this->coreParameters['site']['name'],
            'site_logo' => $this->coreParameters['site']['logo'],
        ]);
    }

    /**
     * @Route("/resetting/update/{token}", name="auth_resetting_update")
     */
    public function requestUpdate(
        string $token,
        Request $request,
        UserRepository $repository,
        TokenGeneratorInterface $tokenGenerator,
        UserPasswordEncoderInterface $encoder,
        EntityManager $entityManager
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('admin_dashboard_index');
        }

        $account = $repository->findOneByConfirmationToken($token);
        $passwordUpdated = false;
        $expired = true;

        if ($account) {
            $requestedAt = $account->getPasswordRequestedAt();
            $expired = (null === $requestedAt || ($requestedAt->getTimestamp() < (time() - 3600 * 2)));
        }

        if ($request->isMethod('POST') && !$expired) {
            $csrfToken = $request->request->get('_csrf_token');

            if ($this->isCsrfTokenValid('resetting_update', $csrfToken)) {
                $password = $request->request->get('password');
                $password2 = $request->request->get('password2');

                $zxcvbn = new Zxcvbn();
                $strength = $zxcvbn->passwordStrength($password, []);

                if (4 === $strength['score'] && $password === $password2) {
                    $account
                        ->setPassword($encoder->encodePassword(
                            $account,
                            $password
                        ))
                        ->setConfirmationToken($tokenGenerator->generateToken())
                        ->setPasswordRequestedAt(new \DateTime('now'))
                    ;

                    $entityManager->update($account);

                    $passwordUpdated = true;
                }
            }
        }

        return $this->render('@Core/auth/resetting_update.html.twig', [
            'password_updated' => $passwordUpdated,
            'token' => $token,
            'expired' => $expired,
            'site_name' => $this->coreParameters['site']['name'],
            'site_logo' => $this->coreParameters['site']['logo'],
        ]);
    }

    /**
     * @Route("/logout", name="auth_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}
