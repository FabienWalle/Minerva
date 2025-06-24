<?php

namespace App\MessageHandler;

use App\Message\SendBorrowingConfirmationEmail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

#[AsMessageHandler]
final readonly class SendBorrowingConfirmationEmailHandler
{
    public function __construct(
        private MailerInterface $mailer,
        private EntityManagerInterface $entityManager,
    ) {}

    /**
     * @throws TransportExceptionInterface
     */
    public function __invoke(SendBorrowingConfirmationEmail $message): void
    {
        $email = (new Email())
            ->from('admin@minerva.com')
            ->to($message->getUserEmail())
            ->subject('Confirmation emprunt : ' . $message->getBookTitle())
            ->html($this->buildBorrowingEmailBody(
                $message->getBookTitle(),
                $message->getBorrowingDate(),
                $message->getDueDate()
            ));

        $this->mailer->send($email);
        $this->entityManager->clear();
    }

    private function buildBorrowingEmailBody(string $bookTitle, \DateTimeInterface $borrowingDate, \DateTimeInterface $dueDate): string
    {
        return sprintf(
            <<<HTML
                <div>
                    <h1>Confirmation d'emprunt</h1>
                    <p>Bonjour,</p>
                    <p>Vous avez emprunté le livre <strong>%s</strong>.</p>
                    <p>Date d'emprunt : %s</p>
                    <p>Date de retour prévue : %s</p>
                    <p>Bonne lecture ! 📚</p>
                </div>
            HTML,
            htmlspecialchars($bookTitle, ENT_QUOTES),
            $borrowingDate->format('d/m/Y'),
            $dueDate->format('d/m/Y')
        );
    }
}
