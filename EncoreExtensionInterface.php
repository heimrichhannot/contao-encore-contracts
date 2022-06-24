<?php

namespace HeimrichHannot\EncoreContracts;

interface EncoreExtensionInterface
{
    /**
     * @return string The bundle class
     */
    public function getBundle(): string;

    /**
     * @return array|EncoreEntry[]
     */
    public function getEntries(): array;
}