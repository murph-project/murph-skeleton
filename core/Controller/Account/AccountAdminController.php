<?php

namespace App\Core\Controller\Account;

use App\Core\Controller\Admin\AdminController;
use App\Core\Manager\EntityManager;
use App\Repository\UserRepository;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface as TotpAuthenticatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use ZxcvbnPhp\Zxcvbn;

/**
 * @Route("/admin/account")
 */
class AccountAdminController extends AdminController
{
    /**
     * @Route("/", name="admin_account")
     */
    public function account(Request $request, TotpAuthenticatorInterface $totpAuthenticatorService): Response
    {
        $account = $this->getUser();

        return $this->render('@Core/account/admin/edit.html.twig', [
            'account' => $account,
        ]);
    }

    /**
     * @Route("/2fa", name="admin_account_2fa")
     */
    public function twoFactorAuthentication(
        Request $request,
        GoogleAuthenticatorInterface $totpAuthenticatorService,
        EntityManager $entityManager
    ): Response {
        if ($request->isMethod('GET')) {
            return $this->redirectToRoute('admin_account');
        }

        $account = $this->getUser();
        $csrfToken = $request->request->get('_csrf_token');
        $enable = (bool) $request->request->get('enable');
        $code = $request->request->get('code', '');
        $secret = $request->request->get('secret', '');
        $qrCodeContent = null;

        if ($this->isCsrfTokenValid('2fa', $csrfToken)) {
            if ($enable && !$account->isTotpAuthenticationEnabled()) {
                if (empty($secret)) {
                    $secret = $totpAuthenticatorService->generateSecret();

                    $account->setTotpSecret($secret);

                    $qrCodeContent = $totpAuthenticatorService->getQRContent($account);
                } else {
                    $account->setTotpSecret($secret);

                    $qrCodeContent = $totpAuthenticatorService->getQRContent($account);

                    if (!$totpAuthenticatorService->checkCode($account, $code)) {
                        $this->addFlash('error', 'The code is not valid.');
                    } else {
                        $this->addFlash('success', 'Double authentication enabled.');

                        $entityManager->update($account);

                        return $this->redirectToRoute('admin_account');
                    }
                }
            }

            if (!$enable && $account->isTotpAuthenticationEnabled()) {
                $account->setTotpSecret(null);

                $entityManager->update($account);

                $this->addFlash('success', 'Double authentication disabled.');

                return $this->redirectToRoute('admin_account');
            }
        }

        return $this->render('@Core/account/admin/edit.html.twig', [
            'account' => $account,
            'twoFaKey' => $secret,
            'twoFaQrCodeContent' => $qrCodeContent,
        ]);
    }

    /**
     * @Route("/password", name="admin_account_password", methods={"POST"})
     */
    public function password(
        Request $request,
        UserRepository $repository,
        TokenGeneratorInterface $tokenGenerator,
        UserPasswordEncoderInterface $encoder,
        EntityManager $entityManager
    ): Response {
        $account = $this->getUser();
        $csrfToken = $request->request->get('_csrf_token');

        if ($this->isCsrfTokenValid('password', $csrfToken)) {
            $password = $request->request->get('password');

            if (!$encoder->isPasswordValid($account, $password)) {
                $this->addFlash('error', 'The form is not valid.');

                return $this->redirectToRoute('admin_account');
            }

            $password1 = $request->request->get('password1');
            $password2 = $request->request->get('password2');

            $zxcvbn = new Zxcvbn();
            $strength = $zxcvbn->passwordStrength($password1, []);

            if (4 === $strength['score'] && $password1 === $password2) {
                $account
                    ->setPassword($encoder->encodePassword($account, $password1))
                    ->setConfirmationToken($tokenGenerator->generateToken())
                ;

                $entityManager->update($account);

                $this->addFlash('success', 'Password updated.');

                return $this->redirectToRoute('admin_account');
            }
        }

        $this->addFlash('error', 'The form is not valid.');

        return $this->redirectToRoute('admin_account');
    }

    /**
     * {@inheritdoc}
     */
    protected function getSection(): string
    {
        return 'account';
    }
}
