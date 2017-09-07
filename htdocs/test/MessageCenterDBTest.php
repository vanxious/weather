<?php
/**
 * Created by PhpStorm.
 * User: dnb751
 * Date: 07.09.2017
 * Time: 10:45
 */

namespace Weather\Test;
use Weather\MessageCenter;

class MessageCenterDBTest extends \PHPUnit_Extensions_Database_TestCase
{
    // only instantiate pdo once for test clean-up/fixture load
    static private $pdo = null;

    // only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test
    private $conn = null;

    /**
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    public function getConnection()
    {
        if ($this->conn === null) {
            if (self::$pdo === null) {
                //TODO: брать данные из файла конфигурации.
                $dsn = 'mysql:host=localhost;dbname=weather';
                $username = 'weather';
                $password = 'ghbdtn';
                self::$pdo = new \PDO($dsn, $username, $password, [\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]);
            }

            $this->conn = $this->createDefaultDBConnection(self::$pdo, 'weather');
        }

        return $this->conn;
    }

    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet()
    {
        //делает truncate таблиц!!!!
        return $this->createFlatXMLDataSet(dirname(__FILE__) . '/fixtures/Log_table_init.xml');
    }

    public function testWriteLogMessageToDBCount()
    {
        $messageCenter = $this->getMockBuilder('Weather\MessageCenter')
            ->setMethods(['printMessageToConsole', 'writeMessageToFile'])
            ->getMock();

        $messageCenter->expects($this->once())
            ->method('printMessageToConsole');

        $messageCenter->expects($this->once())
            ->method('writeMessageToFile');

        $this->assertEquals(2, $this->getConnection()->getRowCount('log'), "Сообщения в таблице `weather`.`log` до добавления сообщения.");

        $messageCenter->message('Тестовое сообщение для записи в базу данных.');

        //2 уже сообщения загружено из фиксутыр Log_table_init.xml
        $this->assertEquals(3, $this->getConnection()->getRowCount('log'), "Сообщение не добавлено в таблицу `weather`.`log`.");
    }

    public function testWriteLogMessageToDBResultCompare()
    {
        $messageCenter = $this->getMockBuilder('Weather\MessageCenter')
            ->setMethods(['printMessageToConsole', 'writeMessageToFile'])
            ->getMock();

        $messageCenter->expects($this->once())
            ->method('printMessageToConsole');

        $messageCenter->expects($this->once())
            ->method('writeMessageToFile');

        $messageCenter->message('Тестовое сообщение для записи в базу данных.');

        //сравнение результата
        $queryTable = $this->getConnection()->createQueryTable(
            'log', 'SELECT `Message`, `ErrorCode` FROM `log`'
        );

        $expectedTable = $this->createFlatXmlDataSet(dirname(__FILE__) . "/fixtures/Log_table_expect_after_add_message.xml")
            ->getTable("log");

        $this->assertTablesEqual($expectedTable, $queryTable);
    }

}
