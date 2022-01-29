<?php

namespace App\Core\Notification;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment as TwigEnvironment;

/**
 * class MailNotifier.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class MailNotifier
{
    protected MailerInterface $mailer;
    protected array $attachments = [];
    protected array $recipients = [];
    protected array $bccRecipients = [];
    protected ?string $subject = null;
    protected ?string $from = null;
    protected ?string $replyTo = null;

    public function __construct(TwigEnvironment $twig, MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function setMailer(Swift_Mailer $mailer): self
    {
        $this->mailer = $mailer;

        return $this;
    }

    public function getMailer(): Swift_Mailer
    {
        return $this->mailer;
    }

    public function setRecipients(array $recipients): self
    {
        $this->recipients = $recipients;

        return $this;
    }

    public function getRecipients(): array
    {
        return $this->recipients;
    }

    public function setBccRecipients(array $bccRecipients): self
    {
        $this->bccRecipients = $bccRecipients;

        return $this;
    }

    public function getBccRecipients(): array
    {
        return $this->bccRecipients;
    }

    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setFrom($from): self
    {
        $this->from = $from;

        return $this;
    }

    public function getFrom(): ?string
    {
        return $this->from;
    }

    public function setReplyTo($replyTo): self
    {
        $this->replyTo = $replyTo;

        return $this;
    }

    public function getReplyTo(): ?string
    {
        return $this->replyTo;
    }

    public function setAttachments(array $attachments): self
    {
        $this->attachments = $attachments;

        return $this;
    }

    public function getAttachments(): array
    {
        return $this->attachments;
    }

    public function addRecipient(string $email, bool $isBcc = false): self
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(sprintf('Invalid email "%s".', $email));
        }

        if ($isBcc) {
            if (!in_array($email, $this->bccRecipients)) {
                $this->bccRecipients[] = $email;
            }
        } else {
            if (!in_array($email, $this->recipients)) {
                $this->recipients[] = $email;
            }
        }

        return $this;
    }

    public function addRecipients(array $emails, bool $isBcc = false): self
    {
        foreach ($emails as $email) {
            $this->addRecipient($email, $isBcc);
        }

        return $this;
    }

    public function addRecipientByAccount(Account $account, bool $isBcc = false): self
    {
        return $this->addRecipient($account->getEmail(), $isBcc);
    }

    public function addRecipientsByAccounts($accounts, bool $isBcc = false)
    {
        if (!is_array($accounts)) {
            throw new InvalidArgumentException('The "accounts" parameter must be an array or an instance of ObjectCollection');
        }

        foreach ($accounts as $account) {
            $this->addRecipientByAccount($account, $isBcc);
        }

        return $this;
    }

    public function addAttachment(string $attachment): self
    {
        if (!in_array($attachment, $this->attachments)) {
            $this->attachments[] = $attachment;
        }

        return $this;
    }

    public function addAttachments(array $attachments): self
    {
        foreach ($attachments as $attachment) {
            $this->addAttachment($attachment);
        }

        return $this;
    }

    public function init(): self
    {
        $this
            ->setSubject(null)
            ->setRecipients([])
            ->setBccRecipients([])
            ->setAttachments([])
        ;

        return $this;
    }

    public function notify(string $template, array $data = [], string $type = 'text/html'): self
    {
        $message = $this->createMessage();
        $message->context($data);

        if (in_array($type, ['text/plain', 'text'])) {
            $message->textTemplate($template);
        } else {
            $message->htmlTemplate($template);
        }

        $this->mailer->send($message);

        return $this;
    }

    protected function createMessage(): TemplatedEmail
    {
        $message = new TemplatedEmail();

        if ($this->getSubject()) {
            $message->subject($this->getSubject());
        }

        if ($this->getFrom()) {
            $message->from($this->getFrom());
        }

        if ($this->getReplyTo()) {
            $message->replyTo($this->getReplyTo());
        }

        if (count($this->getRecipients()) > 0) {
            $message->to(...$this->getRecipients());
        }

        if (count($this->getBccRecipients()) > 0) {
            $message->bcc(...$this->getBccRecipients());
        }

        foreach ($this->getAttachments() as $attachment) {
            $message->attachFromPath($attachment);
        }

        return $message;
    }
}
