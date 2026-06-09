<?php

namespace HeimrichHannot\EncoreContracts;

use Symfony\Component\HttpKernel\Bundle\Bundle;

interface EncoreExtensionInterface
{
    /**
     * @return string|class-string<Bundle> The bundle class
     */
    public function getBundle(): string;

    /**
     * @return array|EncoreEntry[]
     */
    public function getEntries(): array;
}
