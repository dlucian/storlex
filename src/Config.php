<?php

namespace App;

/**
 * Configuration class, handling application configuration.
 */
class Config
{
    /** @var string The local configuration file */
    protected $filename;

    /**
     * Constructor.
     */
    public function __construct(string $iniFile = null)
    {
        $this->filename = $iniFile ?? __DIR__ . '/../config.ini';
    }

    /**
     * Load the configuration file and apply it into
     * the $environment variable.
     *
     * @param array<string,string> $environment Environment to update
     * @return void
     */
    public function load(array &$environment): void
    {
        if (!file_exists($this->filename)) {
            throw new \RuntimeException(
                "Configuration file '{$this->filename}' does not exist."
            );
        }
        $ini = parse_ini_file($this->filename);
        if (is_array($ini)) {
            $this->insertEnvironment($environment, $ini);
        }
    }

    /**
     * Insert the configuration into the environment.
     *
     * @param array<string,string> $target Environment to update
     * @param array<string,string> $source Configuration to insert
     * @return void
     */
    protected function insertEnvironment(array &$target, array $source): void
    {
        foreach ($source as $param => $value) {
            if (!isset($target[$param])) {
                $target[$param] = $value;
            }
        }
    }
}
