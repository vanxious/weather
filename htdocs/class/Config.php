<?php

namespace Weather;

class Config
{

    private static $_instance;

    /**
     *
     * @var string
     */
    private $FileDir;

    /**
     *
     * @var string
     */
    private $XMLDir;


    private function __construct()
    {
        $this->FileDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR;
        $this->XMLDir  = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'xml'   . DIRECTORY_SEPARATOR;
    }


    public static function getInstance()
    {
        if ( empty(self::$_instance) ) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public function getFileDir()
    {
        return $this->FileDir;
    }

    public function getXMLDir()
    {
        return $this->XMLDir;
    }

    public static function DebugMode()
    {
        $configAppParams = require dirname(__DIR__) . '/config/app_config.php';

        return $configAppParams['debugMode'];
    }

}

