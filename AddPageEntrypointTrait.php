<?php

namespace HeimrichHannot\EncoreContracts;

use HeimrichHannot\EncoreBundle\Asset\FrontendAsset;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

trait AddPageEntrypointTrait
{
    /**
     * @return FrontendAsset|null The FrontendAssetService
     */
    abstract protected function getFrontendAsset(): mixed;

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
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function addPageEntrypoint(string $name, array $fallbackAssets = []): void
    {
        $frontendAsset = $this->getFrontendAsset();
        if ($frontendAsset instanceof FrontendAsset)
        {
            $frontendAsset->addActiveEntrypoint($name);
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
}