<?php
/**
 * TODO: Рефакторинг. Всё плохо сделано!
 * TODO: Есть неочевидные зависимости.
 */

namespace Weather;


class Scripts
{
    /**
     * Получение списка всех населённых пунктов,
     * которые отмечены для получения погоды.
     * @return array
     */
    public static function getListEnableScript()
    {
        $addWhere = '';
        if ( !Config::DebugMode() ) {
            $addWhere = ' and NOW() > DATE_ADD(LastRun, INTERVAL GREATEST( ROUND(1440/GREATEST(RunPerDay,1)), 1 ) MINUTE)';
        }

        $sql = '
            SELECT
                id
            FROM scripts
            WHERE
                IsEnable = 1
                ' . $addWhere . '                    
        ';

        $result = DB::getInstance()->query($sql);

        $objects = array();
        foreach ($result as $value) {
            array_push( $objects, Factory::createRecord((int)$value['id']) );
        }

        return $objects;
    }
}