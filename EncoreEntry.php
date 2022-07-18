<?php

namespace HeimrichHannot\EncoreContracts;

class EncoreEntry
{
    const GLOBAL_KEYS = ['TL_JAVASCRIPT', 'TL_JQUERY', 'TL_USER_CSS', 'TL_CSS'];
    private string $name;
    private string $path;
    private bool  $requiresCss       = false;
    private bool  $isHeadScript      = false;
    private array $replaceGlobelKeys = [];

    /**
     * @param string $name
     * @param string $path
     */
    public function __construct(string $name, string $path)
    {
        $this->name = $name;
        $this->path = $path;
    }

    public static function create(string $name, string $path): EncoreEntry
    {
        return (new self($name, $path));
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return EncoreEntry
     */
    public function setName(string $name): EncoreEntry
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return EncoreEntry
     */
    public function setPath(string $path): EncoreEntry
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return bool
     */
    public function getRequiresCss(): bool
    {
        return $this->requiresCss;
    }

    /**
     * @param bool $requiresCss
     * @return EncoreEntry
     */
    public function setRequiresCss(bool $requiresCss): EncoreEntry
    {
        $this->requiresCss = $requiresCss;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsHeadScript(): bool
    {
        return $this->isHeadScript;
    }

    /**
     * @param bool $head
     * @return EncoreEntry
     */
    public function setIsHeadScript(bool $head): EncoreEntry
    {
        $this->isHeadScript = $head;
        return $this;
    }

    public function addEntryToRemoveFromGlobals(string $globalKey, string $entryName): EncoreEntry
    {
        if (!in_array($globalKey, self::GLOBAL_KEYS)) {
            trigger_error(
                "Possible invalid global keys $globalKey used. Supported keys are " . implode(", ", self::GLOBAL_KEYS) . ".",
                E_USER_WARNING
            );
        }

        if (!isset($this->replaceGlobelKeys[$globalKey])) {
            $this->replaceGlobelKeys[$globalKey] = [];
        }

        if (!in_array($entryName, $this->replaceGlobelKeys[$globalKey])) {
            $this->replaceGlobelKeys[$globalKey][] = $entryName;
        }

        return $this;
    }

    public function addJsEntryToRemoveFromGlobals(string $key): EncoreEntry
    {
        $this->addEntryToRemoveFromGlobals('TL_JAVASCRIPT', $key);
        return $this;
    }

    public function addJqueryEntryToRemoveFromGlobals(string $key): EncoreEntry
    {
        $this->addEntryToRemoveFromGlobals('TL_JQUERY', $key);
        return $this;
    }

    public function addCssEntryToRemoveFromGlobals(string $key): EncoreEntry
    {
        $this->addEntryToRemoveFromGlobals('TL_CSS', $key);
        $this->addEntryToRemoveFromGlobals('TL_USER_CSS', $key);
        return $this;
    }
}