<?php
/**
 * Created by PhpStorm.
 * User: dnb751
 * Date: 07.09.2017
 * Time: 16:12
 */

namespace Weather\Test;

use Doctrine\Instantiator\Exception\InvalidArgumentException;
use Weather\Factory;
use Weather\ScriptConfig;

class FactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testCreateRecord()
    {
        //TODO: плохо, зависимость от базы данных.
        $id = 2; //бийск
        $scriptConfigObject = Factory::createRecord($id);

        $this->assertTrue( $scriptConfigObject instanceof ScriptConfig );
    }

    /**
     * @dataProvider dataProviderInvalidParametersForFactory
     * @expectedException InvalidArgumentException
     */
    public function testCreateRecordInvalidArgumets($record_id)
    {
        $scriptConfigObject = Factory::createRecord($record_id);

        $this->assertTrue( $scriptConfigObject instanceof ScriptConfig );
    }

    public function dataProviderInvalidParametersForFactory()
    {
        return [
            [ ['string'] ],
            [ '12' ],
            [ NULL ],
        ];
    }

}
