<?php

abstract class FTPConnect extends CConnect
{

    /**
     *
     * @var array
     */
    protected $config;

    protected $resource;

    /**
     *
     * @var string
     */
    protected $fileName;

    public function __construct()
    {
            $this->resource = @ftp_connect($this->config['host']);

            if ( !$this->resource ) {
                throw new Exception("Невозможно установить соединения с ftp сервером {$this->config['host']}.", 100);
            }

            $login_result = @ftp_login($this->resource, $this->config['user'], $this->config['pass']);

            if ( !$login_result ) {
                throw new Exception("Не удалось произвести вход под именем {$this->config['user']}", 100);
            }

    }

    /**
     *
     * @return array
     */
    protected function getListTT()
    {
            $listTT = ftp_nlist($this->resource, $this->config['fileMask']);

            return $listTT;
    }


    /**
     *
     * @return resource
     */
    protected function getFileResources()
    {
            $resource = NULL;
            $fileName = dirname(__DIR__) . '/files/' . $this->fileName;
            if ( file_exists($fileName) && is_readable($fileName) ) {
                $resource = @fopen($fileName, 'r');
            } else {
                throw new Exception('Невозможно открыть файл "' . $fileName . '". Он не существует либо нет прав для чтения.');
            }

            if ( !$resource ) {
                throw new Exception('Невозможно открыть файл "' . $fileName . '". Неизвестная ошибка.');
            }

            return $resource;
    }

}