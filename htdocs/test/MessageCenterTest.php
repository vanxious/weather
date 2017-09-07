<?php
/**
 * Created by PhpStorm.
 * User: dnb751
 * Date: 05.09.2017
 * Time: 15:53
 */

namespace Weather\Test;

use Weather\MessageCenter;

class MessageCenterTest extends \PHPUnit_Framework_TestCase
{

    private function deleteLogDir(MessageCenter $messageCenter)
    {
        $dirLog = dirname($messageCenter->getLogFile());
        $logFile = $messageCenter->getLogFile();
        $errFile = $messageCenter->getErrorFile();

        if (file_exists($logFile)) {
            unlink($logFile);
        }

        if (file_exists($errFile)) {
            unlink($errFile);
        }

        if (is_dir($dirLog)) {
            rmdir($dirLog);
        }
    }

    public function testPrintMessageToConsole()
    {
        $messageCenter = $this->getMockBuilder('Weather\MessageCenter')
                              ->setMethods(['writeMessageToFile', 'addMessageToDB'])
                              ->getMock();

        $messageCenter->expects($this->once())
                      ->method('writeMessageToFile');

        $messageCenter->expects($this->once())
                      ->method('addMessageToDB');

        $string = 'Тестовое сообщение!';
        $messageCenter->message($string);

        $this->expectOutputString(MessageCenter::getCurrentDateWithFormat() . ' ' . $string . "\n");
    }

    public function testWriteStringToLogFile()
    {
        $messageCenter = $this->getMockBuilder('Weather\MessageCenter')
                              ->setMethods(['printMessageToConsole', 'addMessageToDB'])
                              ->getMock();

        $messageCenter->expects($this->once())
                      ->method('printMessageToConsole');

        $messageCenter->expects($this->once())
                      ->method('addMessageToDB');

        $this->deleteLogDir($messageCenter);

        $this->assertFalse( file_exists($messageCenter->getLogFile()) );

        $expectedString = 'Тестовое сообщение!';
        $messageCenter->message($expectedString);

        $this->assertTrue( file_exists($messageCenter->getLogFile()) );

        $actualString = array_pop( file($messageCenter->getLogFile(), FILE_IGNORE_NEW_LINES) );

        $this->assertEquals($expectedString, $actualString);

    }

    public function testWriteStringToErrFile()
    {
        $messageCenter = $this->getMockBuilder('Weather\MessageCenter')
                              ->setMethods(['printMessageToConsole', 'addMessageToDB'])
                              ->getMock();

        $messageCenter->expects($this->once())
                      ->method('printMessageToConsole');

        $messageCenter->expects($this->once())
                      ->method('addMessageToDB');

        $this->deleteLogDir($messageCenter);

        $this->assertFalse( file_exists($messageCenter->getErrorFile()) );

        $expectedString = 'Тестовое сообщение!';
        $messageCenter->message($expectedString, true);

        $this->assertTrue( file_exists($messageCenter->getErrorFile()) );

        $actualString = array_pop( file($messageCenter->getErrorFile(), FILE_IGNORE_NEW_LINES) );

        $this->assertEquals($expectedString, $actualString);

    }

    public function testWriteStringToLogFileWithErrCheck()
    {
        $messageCenter = $this->getMockBuilder('Weather\MessageCenter')
                            ->setMethods(['printMessageToConsole', 'addMessageToDB', 'checkAndCreateFileByPath'])
                            ->getMock();

        $messageCenter->expects($this->once())
                      ->method('printMessageToConsole');

        $messageCenter->expects($this->once())
                      ->method('addMessageToDB');

        $messageCenter->expects($this->once())
                      ->method('checkAndCreateFileByPath')
                      ->will($this->returnValue(false));

        $expectedString = 'Тестовое сообщение!';
        $messageCenter->message($expectedString);
    }

    /**
     * @dataProvider invalidArgumentForMessageMethod
     */
    public function testInvalidArgument($message, $isError)
    {
        $messageCenter = new MessageCenter();

        $this->expectException('InvalidArgumentException');

        $messageCenter->message($message, $isError);

    }

    public function invalidArgumentForMessageMethod()
    {
        return [
            ['Ошибка!', 'false'],
            [['Ошибка'], false],
            [false, 'false'],
        ];
    }

    public function testDeleteErrorFile()
    {
        $messageCenter = $this->getMockBuilder('Weather\MessageCenter')
                              ->setMethods(['printMessageToConsole', 'addMessageToDB'])
                              ->getMock();

        $messageCenter->expects($this->once())
                      ->method('printMessageToConsole');

        $messageCenter->expects($this->once())
                      ->method('addMessageToDB');

        $this->deleteLogDir($messageCenter);

        $this->assertFalse(file_exists($messageCenter->getErrorFile()));

        $messageCenter->message('Файл должен быть удалён.', true);

        $this->assertTrue(file_exists($messageCenter->getErrorFile()));

        $this->assertTrue($messageCenter->deleteErrorFile());
    }


}
