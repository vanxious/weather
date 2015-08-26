<?php
/**
 * Получение погоды из нескольких мест.
 * Величие скрипта не поддаётся описанию!
 *
 * @date 2015-02
 * @author Бражников Дмитрий
 * @version 0.5
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'autoload.php';

/**
 * Удаляем файл weather.err
 * Если фатальная ошибка сохранилась, то она будет опять записана в файл.
 * Если же ошибка исправилась, то гуд, никого будить не нужно.
 */
Log::deleteErrorFile();

//TODO сделать единую точку входа с оборачиванием её в try-catch
foreach (Scripts::getListEnableScript() as $script) {
    try {

        //bool class_exists ( string $class_name [, bool $autoload = true ] )
        if ( !class_exists($script->ClassName, TRUE) ) {
            Log::add(date('M d H:i:s') . 'ОШИБКА. Класс ' . $script->ClassName . ' не объявлен!');
            continue;
        }

        $className = $script->ClassName;
        $object = new $className($script);

        if ( !($object instanceof WeatherService) ) {
            Log::add(date('M d H:i:s') . 'ОШИБКА. Класс ' . $script->ClassName . ' не реализует необходимый интерфейс!');
            continue;
        }

        $object->run();
        unset($object);

        //Share::sendFileToShare($script);
        //$script->updateTimeRun();

    } catch (Exception $e) {
        Log::add(date('M d H:i:s') . ' ' . $script->CityName . ' ' . $e->getMessage() . "\n", $e->getCode());
    }
}//foreach

if (Scripts::getConfigCount() === 0) {
    Log::add(date('M d H:i:s') . " Запуск обновления погодных данных. Нечего обновлять. Выход. \n");
}
