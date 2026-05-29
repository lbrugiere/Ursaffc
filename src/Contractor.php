<?php

namespace Urssaf;

class Contractor
{
    public function __construct(
        private int $id,
        private string $fullName,
        private string $siret,
        private string $activity,
        private string $taxSystem
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getSiret(): string
    {
        return $this->siret;
    }

    public function getActivity(): string
    {
        return $this->activity;
    }

    public function getTaxSystem(): string
    {
        return $this->taxSystem;
    }
}