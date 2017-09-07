<?php

namespace Weather\Test;
use PHPUnit\Framework\TestCase;
use Weather\Wunderground;
use Weather\Factory;
use Weather\Config;


class WundergroundTest extends TestCase
{
    /**
     * Полный путь к json файлу с данными.
     * @var sting
     */
    protected static $fixturesJsonFile;

    protected static $fixturesEmptyJsonFile;

    /**
     * Полный путь к json файлу в рабочей директории.
     * @var string
     */
    protected static $jsonFileInXmlDir;

    /**
     * Имя json файла.
     * @var string
     */
    protected static $jsonFileName = 'Wunderground_biysk.json';

    /**
     * Пустой json файл.
     * @var string
     */
    protected static $jsonEmptyFileName = 'Wunderground_biysk_empty.json';

    public static function setUpBeforeClass()
    {
        self::$fixturesJsonFile      = __DIR__ . '/fixtures/' . self::$jsonFileName;
        self::$fixturesEmptyJsonFile = __DIR__ . '/fixtures/' . self::$jsonEmptyFileName;
        self::$jsonFileInXmlDir = Config::getInstance()->getXMLDir() . self::$jsonFileName;
    }

    protected function copyFileFromFixturesToXmlDir($file)
    {
        return copy($file, self::$jsonFileInXmlDir);
    }

    protected function deleteFileFromXmlDir()
    {
        return unlink(self::$jsonFileInXmlDir);
    }

    public function testParseCorrectFileWithoutDB()
    {
        $this->copyFileFromFixturesToXmlDir(self::$fixturesJsonFile);

        $scriptConfig = Factory::createRecord(2); //Бийск

        $wunderground = $this->getMockBuilder('Weather\Wunderground')
                             ->setConstructorArgs([$scriptConfig])
                             ->setMethods(['loadFileJsonFromWeb', 'updateData', 'insertData'])
                             ->getMock();

        $wunderground->expects($this->once())
                     ->method('loadFileJsonFromWeb');

        $wunderground->expects($this->once())
                     ->method('updateData');

        $wunderground->expects($this->once())
                     ->method('insertData');

        $wunderground->run();

        $this->expectOutputString(date('M d H:i:s') .  ' Загружен файл ' . self::$jsonFileInXmlDir . "\n"
                                . date('M d H:i:s') .  ' Бийск, затронуто 0 строк.'. "\n\n");

        $this->deleteFileFromXmlDir();
    }

    public function testParseEmptyFileWithoutDB()
    {
        $this->copyFileFromFixturesToXmlDir(self::$fixturesEmptyJsonFile);

        $scriptConfig = Factory::createRecord(2); //Бийск

        $wunderground = $this->getMockBuilder('Weather\Wunderground')
                             ->setConstructorArgs([$scriptConfig])
                             ->setMethods(['loadFileJsonFromWeb', 'updateData', 'insertData'])
                             ->getMock();

        $this->expectException('Exception');

        $this->expectOutputString(
            date('M d H:i:s') . ' Загружен файл ' . self::$jsonFileInXmlDir . "\n"
        .   date('M d H:i:s') . ' Невозможно считать данные из файла. Файл пуст "/home/dnb751/public_html/weather/htdocs/xml/Wunderground_biysk.json".'."\n");

        $wunderground->run();

        $this->deleteFileFromXmlDir();
    }

    public function testDeleteXMLFile()
    {
        $this->copyFileFromFixturesToXmlDir(self::$fixturesJsonFile);

        $scriptConfig = Factory::createRecord(2); //Бийск

        $wunderground = $this->getMockBuilder('Weather\Wunderground')
                             ->setConstructorArgs([$scriptConfig])
                             ->setMethods(null) //все методы имитирующие
                             ->getMock();

        $resultDeleteXMLFile = $wunderground->deleteXMLFile();

        $this->assertTrue($resultDeleteXMLFile);
    }

}
