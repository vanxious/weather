<?php
/**
 * Логирование работы скрипта.
 *
 * @date 2017-09
 * @author Бражников Дмитрий.
 */

namespace Weather;

class MessageCenter {

    private $fileError;

    private $fileLog;

    private $debugMode;

    public function __construct()
    {
        $this->fileError = dirname(__DIR__) . '/log/weather.err';
        $this->fileLog   = dirname(__DIR__) . '/log/weather.log';
        $appConfig       = include_once(dirname(__DIR__) . '/config/app_config.php');
        $this->debugMode = $appConfig['debugMode'];
    }

    public function message($message, $isError = false)
    {
        if (!is_string($message)) {
            throw new \InvalidArgumentException('Передан параметр неподдерживаего типа. Ожидалось "string" пришло "' . gettype($message) . '".' );
        }

        if (!is_bool($isError)) {
            throw new \InvalidArgumentException('Передан параметр неподдерживаего типа. Ожидалось "bool" пришло "' . gettype($isError) . '".' );
        }

        $this->printMessageToConsole($message);
        $this->writeMessageToFile($message, $isError);
        $this->addMessageToDB($message);
    }

    protected function printMessageToConsole($message)
    {
        echo self::getCurrentDateWithFormat() , ' ', $message, "\n";
    }

    public static function getCurrentDateWithFormat()
    {
        return date('M d H:i:s');
    }

    protected function writeMessageToFile($message, $isError = false)
    {
        $filePath = ($isError) ? $this->fileError : $this->fileLog;

        if ( $this->checkAndCreateFileByPath($filePath) ) {
            return $this->writeToFile($filePath, $message);
        }

        return false;
    }

    protected function checkAndCreateFileByPath($path)
    {
        $dir = dirname($path);
        if ( !is_dir($dir) ) {
            mkdir($dir);
        }

        if (!file_exists($path)) {
            $file = @fopen($path, 'w');

            if ($file === false) {
                return false;
            } else {
                @fclose($file);
            }
        }

        return true;
    }

    protected function writeToFile($file, $message)
    {
        return @file_put_contents($file, $message . "\n", FILE_APPEND);
    }

    protected function addMessageToDB($message)
    {
        $sql = 'INSERT INTO log(Message) VALUES ("'.addslashes($message).'")';
        return DB::getInstance()->execute($sql);
    }

    public function deleteErrorFile()
    {
        if (file_exists($this->fileError)) {
            return @unlink($this->fileError);
        }

        return true;
    }

    public function getErrorFile()
    {
        return $this->fileError;
    }

    public function getLogFile()
    {
        return $this->fileLog;
    }

}