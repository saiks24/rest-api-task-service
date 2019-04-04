<?php

namespace Saiks24\App;


class Config
{
    /** @var array */
    private $configContent;

    /**
     * Config constructor.
     *
     * @param $pathToConfig
     */
    public function __construct($pathToConfig)
    {
        $configContent = $this->getConfigContent($pathToConfig);
        $this->configContent = $configContent;
    }

    /** Return array with all config content
     * @param string $pathToConfig
     *
     * @return mixed
     */
    private function getConfigContent(string $pathToConfig)
    {
        $configContent = include($pathToConfig);
        return $configContent;
    }

    /** Get param from config by name
     * @param string $param
     *
     * @return mixed|null
     */
    public function configGetValue(string $param)
    {
        return $this->configContent[$param] ?? null;
    }

}