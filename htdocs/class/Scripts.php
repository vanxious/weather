<?php

class Scripts
{
        /**
         * Количество городов для которых наступило время обновления данных.
         * @var integer
         */
        private static $countObject;


        /**
         * Получение списка всех населённых пунктов,
         * которые отмечены для получения погоды.
         * @return array
         */
        public static function getListEnableScript()
        {
                $sql = '
                        SELECT
                            id
                        FROM scripts
                        WHERE
                            IsEnable = 1

                    ';
                    //and NOW() > DATE_ADD(LastRun, INTERVAL GREATEST( ROUND(1440/GREATEST(RunPerDay,1)), 1 ) MINUTE)

                $result = DB::getInstance()->query($sql);

                $objects = array();
                foreach ($result as $value) {
                    array_push( $objects, Factory::createRecord((int)$value['id']) );
                }

                self::$countObject = count($objects);

                return $objects;
        }


        /**
         * Возвращает количество городов, обновляющих данные.
         * @return integer
         */
        public static function getConfigCount()
        {
                return self::$countObject;
        }

}