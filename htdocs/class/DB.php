<?php

class DB
{
        //Пользуясь случаем передаю Сергею привет! ;)
        private $host = 'serv-db.tdanix.ru';
        private $user = 'weather';
        private $pass = 'forecast';
        private $db   = 'weather';

//        private $host = '127.0.0.1';
//        private $user = 'weather';
//        private $pass = 'ghbdtn';
//        private $db   = 'weather';


        private $connect;
        protected static $_instance;

        private function __construct()
        {
                $this->connect = mysql_pconnect($this->host, $this->user, $this->pass);

                //if (!$this->connect) {
                //    throw new Exception('Невозможно установить соединения с БД.', 100);
                //}

                mysql_select_db($this->db);
                //mysql_query("set character_set_client = utf8;");
                //mysql_query("set character_set_results = utf8;");
                mysql_set_charset('utf8');
        }

        private function __clone()
        {
        }

        final public function __destruct()
        {
                self::$_instance = null;
        }

        public static function getInstance() // получить экземпляр данного класса
        {
                if ( empty(self::$_instance) ) { // если экземпляр данного класса  не создан
                    self::$_instance = new self;  // создаем экземпляр данного класса
                }
                return self::$_instance; // возвращаем экземпляр данного класса
        }


        /**
         * Выполнение запросов вида insert, update.
         *
         * @param string $sql
         */
        public function execute($sql)
        {
                $object = self::$_instance;

                if (isset($object->connect)) {
                    $result = @mysql_query($sql, $object->connect);
                    if ($result === FALSE) {
                        throw new Exception('Невозможно выполнить запрос: "'.$sql.'"', 101);
                    }
                    return mysql_affected_rows($object->connect);
                }

                return FALSE;
        }

        /**
         * Выполнение запросов на выборку.
         *
         * @param string $sql
         * @return array результат запроса
         */
        public function query($sql)
        {
                $object = self::$_instance;
                $rows = array();

                if (isset($object->connect)) {
                    $resut = @mysql_query($sql, $object->connect);

                    if ($resut === FALSE) {
                        throw new Exception('Невозможно выполнить запрос: "'.$sql.'"', 102);
                    }

                    while ($row = mysql_fetch_array($resut, MYSQL_ASSOC)) {
                        $rows[] = $row;
                    }

                    return $rows;
                }//if

                return FALSE;
        }


}
