<?php

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


    /**
     * Это только для тестов!
     * Обучения ради.
     *
     * @param string $key
     * @return string
     */
    private function getProperty( $key )
    {

            $class = new ReflectionClass( __CLASS__ );

            $property = $class->getProperties();

            var_dump($property);

            if ( isset($property[$key]) ) {
                return $class->getPropertyValue($key);
            }

            return 'Ничего не получилось' . "\n";

    }


}

