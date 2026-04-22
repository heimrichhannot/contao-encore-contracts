<?php

namespace HeimrichHannot\EncoreContracts;

use Contao\System;
use HeimrichHannot\EncoreBundle\Asset\FrontendAsset;
use Psr\Container\ContainerInterface;

trait PageAssetsTrait
{
    use AddPageEntrypointTrait;

    protected function getFrontendAsset(): mixed
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

    /**
     * Implement the ServiceSubscriberInterface method to not break existing usages.
     */
    public static function getSubscribedServices(): array
    {
        $services = \method_exists(\get_parent_class(self::class) ?: '', __FUNCTION__)
            ? parent::getSubscribedServices()
            : [];

        if (\class_exists(FrontendAsset::class)) {
            $services[] = '?'.FrontendAsset::class;
        }

        return $services;
    }
}