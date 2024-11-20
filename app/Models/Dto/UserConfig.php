<?php

namespace App\Models\Dto;

/**
 * UserConfig DTO class
 */
class UserConfig
{
    /**
     * User country ("Russia")
     *
     * @var string|null
     */
    private ?string $country = null;

    /**
     * User region ("Nizhny Novgorod Oblast")
     *
     * @var string|null
     */
    private ?string $region	= null;

    /**
     * User city ("Nizhny Novgorod")
     *
     * @var string|null
     */
    private ?string $city = null;

    /**
     * User postal code ("603033")
     *
     * @var string|null
     */
    private ?string $postal	= null;

    /**
     * User timezone utc ("+03:00")
     *
     * @var string|null
     */
    private ?string $timezone = null;


    public function setCountry(?string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setRegion(?string $region): self
    {
        $this->region = $region;
        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setPostal(?string $postal): self
    {
        $this->postal = $postal;
        return $this;
    }

    public function getPostal(): ?string
    {
        return $this->postal;
    }

    public function setTimezone(?string $timezone): self
    {
        $this->timezone = $timezone;
        return $this;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function toArray(): array
    {
        return [
            'country' => $this->country,
            'region' => $this->region,
            'city' => $this->city,
            'postal'=> $this->postal,
            'timezone' => $this->timezone,
        ];
    }
}
