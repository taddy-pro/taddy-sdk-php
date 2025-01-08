<?php

namespace Taddy\Sdk\Dto;

use DateTimeImmutable;
use DateTimeInterface;
use Throwable;

final class User extends AbstractDto {

    public int $id;

    public ?string $firstName = null;

    public ?string $lastName = null;

    public ?string $username = null;

    public ?bool $premium = null;

    public ?Gender $gender = null;

    public ?DateTimeInterface $birthDate = null;

    public ?string $ip = null;

    public ?string $userAgent = null;

    public ?Country $country = null;

    public ?Language $language = null;

    public ?string $source = null;

    public function __construct(int $id) {
        $this->id = $id;
    }

    public static function factory(int $id): self {
        return new self($id);
    }

    public function withFirstName(?string $firstName): User {
        $this->firstName = $firstName;
        return $this;
    }

    public function withLastName(?string $lastName): User {
        $this->lastName = $lastName;
        return $this;
    }

    public function withUsername(?string $username): User {
        $this->username = $username;
        return $this;
    }

    public function withPremium(?bool $premium): User {
        $this->premium = $premium;
        return $this;
    }

    public function withGender(Gender|string|null $gender): User {
        if (is_string($gender)) {
            $gender = strtolower($gender);
            if (in_array($gender, ['m', 'man'])) $gender = 'male';
            elseif (in_array($gender, ['w', 'f', 'woman'])) $gender = 'female';
            $gender = Gender::tryFrom($gender);
        }
        $this->gender = $gender ?? Gender::Unknown;
        return $this;
    }

    public function withBirthDate(DateTimeInterface|string|null $birthDate): User {
        if (is_string($birthDate)) {
            try {
                $birthDate = new DateTimeImmutable($birthDate);
            } catch (Throwable) {
                $birthDate = null;
            }
        }
        $this->birthDate = $birthDate;
        return $this;
    }

    public function withIp(?string $ip): User {
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            $this->ip = $ip;
        }
        return $this;
    }

    public function withUserAgent(?string $userAgent): User {
        $this->userAgent = $userAgent;
        return $this;
    }

    public function withCountry(Country|string|null $country): User {
        if (is_string($country)) {
            $country = Country::tryFrom(strtoupper($country));
        }
        $this->country = $country;
        return $this;
    }

    public function withLanguage(Language|string|null $language): User {
        if (is_string($language)) {
            $language = Language::tryFrom(strtolower($language));
        }
        $this->language = $language;
        return $this;
    }

    public function withSource(?string $source): User {
        $this->source = $source;
        return $this;
    }

}