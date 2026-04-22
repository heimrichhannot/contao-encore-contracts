<?php

namespace HeimrichHannot\EncoreContracts;

use Contao\System;
use HeimrichHannot\EncoreBundle\Asset\FrontendAsset;
use Psr\Container\ContainerInterface;

trait PageAssetsTrait
{
    use AddPageEntrypointTrait;

    protected function getFrontendAsset(): FrontendAsset|null
    {
        if (!\class_exists(FrontendAsset::class)) {
            return null;
        }

        if (isset($this->container) && $this->container instanceof ContainerInterface && $this->container->has(FrontendAsset::class)) {
            return $this->container->get(FrontendAsset::class);
        }

        if (class_exists(System::class) && System::getContainer()->has(FrontendAsset::class)) {
            return System::getContainer()->get(FrontendAsset::class);

        }

        return null;
    }
}