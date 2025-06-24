<?php

namespace App\Message;

final readonly class SendBorrowingConfirmationEmail
{
    public function __construct(
        private string             $userEmail,
        private string             $bookTitle,
        private \DateTimeImmutable $borrowingDate,
        private \DateTimeImmutable $dueDate
    ) {}

    public function getUserEmail(): string
    {
        return $this->userEmail;
    }

    public function getBookTitle(): string
    {
        return $this->bookTitle;
    }

    public function getBorrowingDate(): \DateTimeImmutable
    {
        return $this->borrowingDate;
    }

    public function getDueDate(): \DateTimeImmutable
    {
        return $this->dueDate;
    }
}
