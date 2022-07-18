# Contao Encore Contracts

A set of abstractions needed for [encore bundle](https://github.com/heimrichhannot/contao-encore-bundle) preparation.

## Usage

```php
namespace HeimrichHannot\ExampleBundle\Asset;

use HeimrichHannot\EncoreContracts\EncoreEntry;
use HeimrichHannot\EncoreContracts\EncoreExtensionInterface;
use HeimrichHannot\ExampleBundle\HeimrichHannotExampleBundle;

class EncoreExtension implements EncoreExtensionInterface
{
    public static function getBundle(): string
    {
        // Return the bundle class
        return HeimrichHannotExampleBundle::class;
    }

    public static function getEntries(): array
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

