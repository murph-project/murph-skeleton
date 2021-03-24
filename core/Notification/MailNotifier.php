<?php

namespace App\Core\Notification;

use Swift_Attachment;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment as TwigEnvironment;

/**
 * class MailNotifier.
 *
 * @author Simon Vieille <simon@deblan.fr>
 */
class MailNotifier
{
    /**
     * @var Swift_Mailer
     */
    protected $mailer;

    /**
     * @var array
     */
    protected $attachments = [];

    /**
     * @var array
     */
    protected $recipients = [];

    /**
     * @var array
     */
    protected $bccRecipients = [];

    /**
     * @var string
     */
    protected $subject;

    /**
     * @var string
     */
    protected $from;

    /**
     * @var string
     */
    protected $replyTo;

    /**
     * Constructor.
     *
     * @param BasicNotifier $basicNotifier
     * @param Swift_Mailer  $mail
     */
    public function __construct(TwigEnvironment $twig, Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @return EmailNotifier
     */
    public function setMailer(Swift_Mailer $mailer): self
    {
        $this->mailer = $mailer;

        return $this;
    }

    public function getMailer(): Swift_Mailer
    {
        return $this->mailer;
    }

    /**
     * @return EmailNotifier
     */
    public function setRecipients(array $recipients): self
    {
        $this->recipients = $recipients;

        return $this;
    }

    public function getRecipients(): array
    {
        return $this->recipients;
    }

    /**
     * @return EmailNotifier
     */
    public function setBccRecipients(array $bccRecipients): self
    {
        $this->bccRecipients = $bccRecipients;

        return $this;
    }

    public function getBccRecipients(): array
    {
        return $this->bccRecipients;
    }

    /**
     * @param string $subject
     *
     * @return EmailNotifier
     */
    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param mixed $from
     *
     * @return EmailNotifier
     */
    public function setFrom($from): self
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFrom(): ?string
    {
        return $this->from;
    }

    /**
     * Set the value of "replyTo".
     *
     * @param string $replyTo
     *
     * @return EmailNotifier
     */
    public function setReplyTo($replyTo): self
    {
        $this->replyTo = $replyTo;

        return $this;
    }

    /*
     * Get the value of "replyTo".
     *
     * @return string
     */
    public function getReplyTo(): ?string
    {
        return $this->replyTo;
    }

    /**
     * @return EmailNotifier
     */
    public function setAttachments(array $attachments): self
    {
        $this->attachments = $attachments;

        return $this;
    }

    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * @return EmailNotifier
     */
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

    /**
     * @return EmailNotifier
     */
    public function addRecipients(array $emails, bool $isBcc = false): self
    {
        foreach ($emails as $email) {
            $this->addRecipient($email, $isBcc);
        }

        return $this;
    }

    /**
     * @return EmailNotifier
     */
    public function addRecipientByAccount(Account $account, bool $isBcc = false): self
    {
        return $this->addRecipient($account->getEmail(), $isBcc);
    }

    /**
     * @param mixed $accounts
     *
     * @return EmailNotifier
     */
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

    /**
     * @return EmailNotifier
     */
    public function addAttachment(string $attachment): self
    {
        if (!in_array($attachment, $this->attachments)) {
            $this->attachments[] = $attachment;
        }

        return $this;
    }

    /**
     * @return EmailNotifier
     */
    public function addAttachments(array $attachments): self
    {
        foreach ($attachments as $attachment) {
            $this->addAttachment($attachment);
        }

        return $this;
    }

    /**
     * @return EmailNotifier
     */
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

    /**
     * @return EmailNotifier
     */
    public function notify(string $template, array $data = [], string $type = 'text/html'): self
    {
        $message = $this->createMessage(
            $this->twig->render(
                $template,
                $data
            ),
            $type
        );

        $this->mailer->send($message);

        return $this;
    }

    protected function createMessage(string $body, string $type = 'text/html'): Swift_Message
    {
        $message = new Swift_Message();

        if ($this->getSubject()) {
            $message->setSubject($this->getSubject());
        }

        if ($this->getFrom()) {
            $message->setFrom($this->getFrom());
        }

        if ($this->getReplyTo()) {
            $message->setReplyTo($this->getReplyTo());
        }

        if (count($this->getRecipients()) > 0) {
            $message->setTo($this->getRecipients());
        }

        if (count($this->getBccRecipients()) > 0) {
            $message->setBcc($this->getBccRecipients());
        }

        foreach ($this->getAttachments() as $attachment) {
            if (is_object($attachment) && $attachment instanceof Swift_Attachment) {
                $message->attach($attachment);
            } elseif (is_string($attachment) && file_exists($attachment) && is_readable($attachment) && !is_dir($attachment)) {
                $message->attach(Swift_Attachment::fromPath($attachment));
            }
        }

        $message->setBody($body, $type);

        return $message;
    }
}
