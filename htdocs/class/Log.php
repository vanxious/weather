<?php
/**
 * Логирование работы скрипта.
 *
 * @date 2017-09
 * @author Бражников Дмитрий.
 */

namespace Weather;

class Log {

    private static $object = null;

    protected static function returnObject()
    {
        if (self::$object === null) {
            return self::$object = new MessageCenter();
        }

        return self::$object;
    }

    public static function message($message, $isError = false)
    {
        $obj = self::returnObject();
        $obj->message($message, $isError);

    }

    public static function deleteErrorFile()
    {
        $obj = self::returnObject();
        $obj->deleteErrorFile();
    }

}