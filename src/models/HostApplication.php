<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;

/* Encja wniosku o rolę gospodarza z formularza „Zostań gospodarzem”; e-mail pochodzi z konta (JOIN users). */
final class HostApplication
{
    public function __construct(
        private int $id,
        private int $userId,
        private string $email,
        private string $firstName,
        private string $lastName,
        private string $phone,
        private string $companyName,
        private string $nip,
        private string $venueName,
        private string $venueType,
        private string $address,
        private string $message,
        private string $status,
        private DateTimeImmutable $createdAt,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    /* NIP w bazie to 10 cyfr, na ekranie w grupach 3-3-2-2, np. 676-248-10-35. */
    public function getFormattedNip(): string
    {
        return implode('-', [substr($this->nip, 0, 3), substr($this->nip, 3, 3), substr($this->nip, 6, 2), substr($this->nip, 8, 2)]);
    }

    public function getVenueName(): string
    {
        return $this->venueName;
    }

    public function getVenueType(): string
    {
        return $this->venueType;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /* Pełne godziny od wysłania – panel wyróżnia wnioski czekające co najmniej dobę. */
    public function getHoursWaiting(): int
    {
        return intdiv(time() - $this->createdAt->getTimestamp(), 3600);
    }
}
