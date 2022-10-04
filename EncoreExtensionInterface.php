<?php

namespace HeimrichHannot\EncoreContracts;

interface EncoreExtensionInterface
{
    /**
     * @return string|class-string<\Symfony\Component\HttpKernel\Bundle\Bundle> The bundle class
     */
    public function getBundle(): string;

    /**
     * @return array|EncoreEntry[]
     */
    public function getEntries(): array;
}