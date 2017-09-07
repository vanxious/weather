<?php
/**
 * Получение погоды из нескольких мест.
 * Величие скрипта не поддаётся описанию!
 *
 * @date 2015-02
 * @author Бражников Дмитрий
 * @version 0.6
 */

require_once 'autoload.php';

use Weather\Log;
use Weather\Scripts;
use Weather\WeatherService;
use Weather\Config;

if ( Config::DebugMode() ) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

/**
 * Удаляем файл weather.err
 * Если фатальная ошибка сохранилась, то она будет опять записана в файл.
 * Если же ошибка исправилась, то гуд, никого будить не нужно.
 */
Log::deleteErrorFile();

//TODO сделать единую точку входа с оборачиванием её в try-catch
$scriptObjects = Scripts::getListEnableScript();
foreach ($scriptObjects as $script) {
    try {
        //bool class_exists ( string $class_name [, bool $autoload = true ] )
        if ( !class_exists('Weather\\' . $script->ClassName, TRUE) ) {
            Log::message('ОШИБКА. Класс ' . $script->ClassName . ' не объявлен!');
            continue;
        }

        $className = 'Weather\\' . $script->ClassName;
        $object = new $className($script);

        if ( !($object instanceof WeatherService) ) {
            Log::message('ОШИБКА. Класс ' . $script->ClassName . ' не реализует необходимый интерфейс!');
            continue;
        }

        $object->run();
        unset($object);

        //создание файлов происходит перед их отправкой на шары
        //Share::sendFileToShare($script);
        //TODO: перенести отправку на шару в объект $script
        $script->updateTimeRun();

    } catch (Exception $e) {
        Log::message($script->CityName . ' ' . $e->getMessage() . "\n");
    }
}//foreach

if (count($scriptObjects) === 0) {
    Log::message("Запуск обновления погодных данных. Нечего обновлять. Выход. \n");
}
