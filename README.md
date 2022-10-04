# Contao Encore Contracts

This package contains abstractions to add loose [Encore bundle](https://github.com/heimrichhannot/contao-encore-bundle) support.

## Usage

### Register entrypoints

To register encore entrypoints create an EncoreExtension class implementing `EncoreExtensionInterface`.

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

To add entrypoints (must be registered beforehand) from your code, you can use the `PageAssetsTrait`. 
It checks if encore bundle is installed and add the entry, if this is the case. 
Otherwise, it adds the fallback assets to the contao global asset array.

Make your class implement `ServiceSubscriberInterface` and use `PageAssetsTrait` (it already implements the needed methods for the ServiceSubscriberInterface).
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
             // Optional: define fallback assets to use if encore bundle is not installed
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
