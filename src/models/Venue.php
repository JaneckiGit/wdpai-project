<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;

/*
 * Lokal z tabeli venues razem z celami, udogodnieniami i oknami dostępności.
 * Szkic wymaga tylko nazwy, dlatego część pól może być pusta (null), dopóki gospodarz nie wyśle lokalu do weryfikacji.
 */
final class Venue
{
    /**
     * @param string[] $purposes kody celów, np. ['work', 'meetings']
     * @param array<string, ?string> $amenities kod udogodnienia => dopisek gospodarza, np. ['wifi' => 'hasło po akceptacji']
     * @param AvailabilityWindow[] $windows
     */
    public function __construct(
        private ?int $id,
        private int $hostId,
        private string $hostName,
        private string $name,
        private ?string $type,
        private ?string $street,
        private ?string $buildingNumber,
        private ?string $unitNumber,
        private ?string $postalCode,
        private ?string $city,
        private ?string $district,
        private ?float $latitude,
        private ?float $longitude,
        private ?string $description,
        private string $entryMode,
        private ?string $entryInstructions,
        private string $status,
        // Nowy lokal z formularza nie ma jeszcze id ani daty dodania – nadaje je baza przy zapisie.
        private ?DateTimeImmutable $createdAt,
        private array $purposes,
        private array $amenities,
        private array $windows,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
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

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function getBuildingNumber(): ?string
    {
        return $this->buildingNumber;
    }

    public function getUnitNumber(): ?string
    {
        return $this->unitNumber;
    }

    /* Ulica z numerem budynku i lokalu, np. „ul. Szpitalna 9/2”; null, gdy szkic nie ma jeszcze adresu. */
    public function getStreetAddress(): ?string
    {
        if ($this->street === null || $this->buildingNumber === null) {
            return null;
        }

        return $this->street . ' ' . $this->buildingNumber . ($this->unitNumber === null ? '' : '/' . $this->unitNumber);
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getDistrict(): ?string
    {
        return $this->district;
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

    /* Instrukcja wejścia – najemca zobaczy ją dopiero po akceptacji rezerwacji. */
    public function getEntryInstructions(): ?string
    {
        return $this->entryInstructions;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    /** @return string[] */
    public function getPurposes(): array
    {
        return $this->purposes;
    }

    /** @return array<string, ?string> */
    public function getAmenities(): array
    {
        return $this->amenities;
    }

    /** @return AvailabilityWindow[] */
    public function getWindows(): array
    {
        return $this->windows;
    }

    /**
     * Pierwsze okno każdego trybu, np. ['seats' => okno, 'exclusive' => okno] – z tych okien panel rezerwacji
     * pokazuje dni, godziny i cenę. Okna są posortowane w repozytorium: najpierw miejsca do pracy, potem wyłączność.
     *
     * @return array<string, AvailabilityWindow>
     */
    public function getWindowsByMode(): array
    {
        $windows = [];
        foreach ($this->windows as $window) {
            $windows[$window->getMode()] ??= $window;
        }

        return $windows;
    }

    /* Okno w danym trybie, które obejmuje dzień tygodnia i godzinę rozpoczęcia; null, gdy takiego nie ma. */
    public function findWindow(string $mode, int $weekday, int $hour): ?AvailabilityWindow
    {
        foreach ($this->windows as $window) {
            if ($window->getMode() === $mode && $window->isOpenOn($weekday) && $window->includesHour($hour)) {
                return $window;
            }
        }

        return null;
    }

    /* Czy któreś okno w danym trybie obejmuje ten dzień tygodnia. */
    public function isOpenOn(string $mode, int $weekday): bool
    {
        foreach ($this->windows as $window) {
            if ($window->getMode() === $mode && $window->isOpenOn($weekday)) {
                return true;
            }
        }

        return false;
    }
}
