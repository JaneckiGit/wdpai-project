<?php

declare(strict_types=1);

namespace App\Model;

/*
 * Encja lokalu (Laboratorium 8): wiersz tabeli venues razem z celami, udogodnieniami i oknami dostępności.
 * Szkic wymaga tylko nazwy, dlatego część pól może być pusta (null), dopóki gospodarz nie wyśle lokalu do weryfikacji.
 */
final class Venue
{
    /**
     * @param string[] $purposes kody celów, np. ['work', 'meetings']
     * @param string[] $amenities kody udogodnień, np. ['wifi', 'sockets']
     * @param AvailabilityWindow[] $windows
     */
    public function __construct(
        private int $id,
        private int $hostId,
        private string $hostName,
        private string $name,
        private ?string $type,
        private ?string $address,
        private ?string $district,
        private ?float $latitude,
        private ?float $longitude,
        private ?string $description,
        private string $entryMode,
        private string $status,
        private array $purposes,
        private array $amenities,
        private array $windows,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getHostId(): int
    {
        return $this->hostId;
    }

    public function getHostName(): string
    {
        return $this->hostName;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getDistrict(): ?string
    {
        return $this->district;
    }

    /* Dzielnica i adres do jednej linii, np. „Kazimierz, ul. Estery 8” – dzielnica jest nieobowiązkowa. */
    public function getLocation(): string
    {
        return implode(', ', array_filter([$this->district, $this->address]));
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getEntryMode(): string
    {
        return $this->entryMode;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /** @return string[] */
    public function getPurposes(): array
    {
        return $this->purposes;
    }

    /** @return string[] */
    public function getAmenities(): array
    {
        return $this->amenities;
    }

    /** @return AvailabilityWindow[] */
    public function getWindows(): array
    {
        return $this->windows;
    }

    /* Okno w danym trybie (miejsca albo wyłączność); strona lokalu i rezerwacja zakładają jedno okno na tryb. */
    public function getWindow(string $mode): ?AvailabilityWindow
    {
        foreach ($this->windows as $window) {
            if ($window->getMode() === $mode) {
                return $window;
            }
        }

        return null;
    }
}
