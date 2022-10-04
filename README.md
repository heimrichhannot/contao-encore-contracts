# Contao Encore Contracts

A set of abstractions needed for [encore bundle](https://github.com/heimrichhannot/contao-encore-bundle) preparation.

## Usage

### Register entrypoints

```php
namespace HeimrichHannot\ExampleBundle\Asset;

use HeimrichHannot\EncoreContracts\EncoreEntry;
use HeimrichHannot\EncoreContracts\EncoreExtensionInterface;
use HeimrichHannot\ExampleBundle\HeimrichHannotExampleBundle;

class EncoreExtension implements EncoreExtensionInterface
{
    public function getBundle(): string
    {
        // Return the bundle class
        return HeimrichHannotExampleBundle::class;
    }

    public function getEntries(): array
    {
        // Return the bundle entries
        return [
            EncoreEntry::create('main-theme', 'assets/main/js/main-theme.js')
                ->setRequiresCss(true)
                ->setIsHeadScript(false),
            EncoreEntry::create('one-pager', 'assets/one-pager/js/one-pager.js')
                ->setRequiresCss(true),
            EncoreEntry::create('custom-head-js', 'assets/main/js/head.js')
                ->setIsHeadScript(true)
                // Define entries that will be removed from the global asset array
                ->addJsEntryToRemoveFromGlobals('colorbox')
                ->addCssEntryToRemoveFromGlobals('css-to-replace'),
        ];
    }
}
```

### Add entrypoints for current page

Make your class implement `ServiceSubscriberInterface` and use `PageAssetsTrait`.
Afterwards just call `$this->addPageEntrypoint(string $name, array $fallbackAssets = [])`.

```php
use HeimrichHannot\EncoreContracts\PageAssetsTrait;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class FrontendController implements ServiceSubscriberInterface
{
    use PageAssetsTrait;
    
    public function __invoke()
    {
        $this->addPageEntrypoint(
            // Encore entry point name
            'contao-example-bundle', 
             // Optional: define fallback assets if encore bundle is not installed
             // They are registered to global contao asset array, so you don't need to do it somewhere else
            [
                'TL_CSS' => ['main-theme' => 'assets/main/dist/main-theme.min.css|static'],
                'TL_JAVASCRIPT' => [
                    'main-theme' => 'assets/main/dist/main-theme.min.js|static',
                    'some-dependency' => 'assets/some-dependency/some-dependency.min.js|static',
                ],
            ]
        );
    }
}
```
