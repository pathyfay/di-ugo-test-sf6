<?php
namespace App\ApiResource;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail; // si tu utilises twig for email body; sinon construis le message manuellement
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api', name: 'api_')]
class ResetPasswordApiController extends AbstractController
{
    public function __construct(private ResetPasswordHelperInterface $resetPasswordHelper, private MailerInterface $mailer, private EntityManagerInterface $em) {}

    #[Route('/reset-password', name: 'app_forgot_password_request', methods: ['POST'])]
    public function request(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        if (!$email) {
            return new JsonResponse(['error' => 'email required'], 400);
        }

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        // don't reveal whether user exists
        if (!$user) {
            return new JsonResponse(['status' => 'ok'], 200);
        }

        // create token
        $resetToken = $this->resetPasswordHelper->generateResetToken($user);

        // build reset link (client will open this)
        $resetUrl = $this->generateUrl('api_reset_password_confirm', ['token' => $resetToken->getToken()], UrlGeneratorInterface::ABSOLUTE_URL);

        // send mail (adapt email body as you like)
        $emailMessage = (new TemplatedEmail())
            ->from(new Address('no-reply@yourdomain.tld', 'Your App'))
            ->to($user->getEmail())
            ->subject('Password reset request')
            ->htmlTemplate('emails/reset_password.html.twig') // optional
            ->context([
                'resetUrl' => $resetUrl,
                'tokenLifetime' => $resetToken->getExpiresAt()->getTimestamp(),
            ]);

        $this->mailer->send($emailMessage);

        return new JsonResponse(['status' => 'ok'], 200);
    }

    #[Route('/reset-password/confirm', name: 'api_reset_password_confirm', methods: ['POST'])]
    public function reset(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $token = $data['token'] ?? null;
        $newPassword = $data['password'] ?? null;

        if (!$token || !$newPassword) {
            return new JsonResponse(['error' => 'token and password required'], 400);
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'invalid_or_expired_token'], 400);
        }

        // hash new password
        $passwordHasher = $this->container->get('security.password_hasher'); // or inject UserPasswordHasherInterface
        $user->setPassword($passwordHasher->hashPassword($user, $newPassword));

        $this->em->persist($user);
        $this->em->flush();

        // remove the used reset request
        $this->resetPasswordHelper->removeResetRequest($token);

        return new JsonResponse(['status' => 'password_updated'], 200);
    }
}
