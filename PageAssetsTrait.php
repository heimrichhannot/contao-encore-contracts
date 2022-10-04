<?php

namespace HeimrichHannot\EncoreContracts;

use HeimrichHannot\EncoreBundle\Asset\FrontendAsset;
use Psr\Container\ContainerInterface;

trait PageAssetsTrait
{
    /** @var ContainerInterface */
    protected $container;

    /**
     * Add a page entrypoint
     * This method checks, if encore bundle is installed and add the entrypoint for current page, if it is.
     * Otherwise,it registers the fallback assets to the contao global asset array.
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
     * @param array $fallbackAssets An array of global key name (e.g. TL_CSS, TL_JAVASCRIPT,...), entry key and entry path.
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function addPageEntrypoint(string $name, array $fallbackAssets = []): void
    {
        if (class_exists(FrontendAsset::class) && $this->container && $this->container->has(FrontendAsset::class)) {
            $this->container->get(FrontendAsset::class)->addActiveEntrypoint($name);
            return;
        }
        if (!empty($fallbackAssets)) {
            foreach ($fallbackAssets as $globalKey => $assets) {
                if (!in_array($globalKey, EncoreEntry::GLOBAL_KEYS)) {
                    trigger_error("Invalid global key for encore entry fallback asset in ".__CLASS__, E_USER_WARNING);
                    continue;
                }
                if (!is_array($assets)) {
                    trigger_error("Invalid fallback entry in ".__CLASS__, E_USER_WARNING.". Entry must be an array.");
                }
                foreach ($assets as $key => $path) {
                    if (!is_string($path)) {
                        trigger_error("Invalid fallback entry in ".__CLASS__.". Path must be a string.", E_USER_WARNING);
                    }
                    if (is_string($key) && !is_numeric($key)) {
                        $GLOBALS[$globalKey][$key] = $path;
                    } else {
                        $GLOBALS[$globalKey][] = $path;
                    }
                }
            }
        }
    }

    public static function getSubscribedServices()
    {
        $services = [];

        if (method_exists(get_parent_class(self::class) ?: '', __FUNCTION__)) {
            $services = parent::getSubscribedServices();
        }
        
        if (class_exists(FrontendAsset::class)) {
            $services[] = '?'.FrontendAsset::class;
        }

        return $services;
    }

    /**
     * @required
     *
     * @return ContainerInterface|null
     */
    public function setContainer(ContainerInterface $container): ?ContainerInterface
    {
        $this->container = $container;

        if (method_exists(get_parent_class(self::class) ?: '', __FUNCTION__)) {
            return parent::setContainer($container);
        }

        return null;
    }
}