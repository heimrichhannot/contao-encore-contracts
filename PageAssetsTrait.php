<?php

namespace HeimrichHannot\EncoreContracts;

use HeimrichHannot\EncoreBundle\Asset\FrontendAsset;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Contracts\Service\ServiceSubscriberTrait;

trait PageAssetsTrait
{
    use ServiceSubscriberTrait {
        ServiceSubscriberTrait::getSubscribedServices as public _ServiceSubscriberTrait_getSubscribedServices;
        ServiceSubscriberTrait::setContainer as public _ServiceSubscriberTrait_setContainer;
    }

    /**
     * Add a page entrypoint.
     *
     * If the encore bundle is installed, add the entrypoint to the current page.
     * Otherwise, registers the fallback assets to the contao global asset array.
     *
     * Fallback asset example:
     * [
     *      'TL_CSS' => ['main-theme' => 'assets/main/dist/main-theme.min.css|static'],
     *      'TL_JAVASCRIPT' => [
     *          'main-theme' => 'assets/main/dist/main-theme.min.js|static',
     *          'some-dependency' => 'assets/some-dependency/some-dependency.min.js|static',
     *      ],
     * ]
     *
     * @param string $name
     * @param array<string, array<string|int, string>> $fallbackAssets An array of global key name
     *              (e.g., TL_CSS, TL_JAVASCRIPT, ...), entry key and entry path.
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     */
    protected function addPageEntrypoint(string $name, array $fallbackAssets = []): void
    {
        if (\class_exists(FrontendAsset::class)
            && isset($this->container)
            && $this->container->has(FrontendAsset::class))
        {
            $this->container->get(FrontendAsset::class)->addActiveEntrypoint($name);
            return;
        }

        if (empty($fallbackAssets))
        {
            return;
        }

        foreach ($fallbackAssets as $globalKey => $assets)
        {
            if (!\in_array($globalKey, EncoreEntry::GLOBAL_KEYS, true)) {
                \trigger_error("Invalid global key for encore entry fallback asset in ".__CLASS__, E_USER_WARNING);
                continue;
            }

            if (!\is_array($assets)) {
                \trigger_error("Invalid fallback entry in " . __CLASS__ . ". Entry must be an array.", E_USER_WARNING);
                continue;
            }

            if (!isset($GLOBALS[$globalKey]) || !\is_array($GLOBALS[$globalKey])) {
                $GLOBALS[$globalKey] = [];
            }

            $glob = &$GLOBALS[$globalKey];

            foreach ($assets as $key => $path)
            {
                if (!\is_string($path)) {
                    trigger_error("Invalid fallback entry in ".__CLASS__.". Path must be a string.", E_USER_WARNING);
                }

                if (\is_string($key) && !\is_numeric($key))
                {
                    $glob[$key] = $path;
                    return;
                }

                $glob[] = $path;
            }
        }
    }

    public static function getSubscribedServices(): array
    {
        $services = self::_ServiceSubscriberTrait_getSubscribedServices();

        if (\class_exists(FrontendAsset::class)) {
            $services[] = '?'.FrontendAsset::class;
        }

        return $services;
    }

    public function setContainer(ContainerInterface $container): ?ContainerInterface
    {
        $ret = $this->_ServiceSubscriberTrait_setContainer($container);

        if ($ret instanceof ContainerInterface)
        {
            return $ret;
        }

        return $this->container = null;
    }
}