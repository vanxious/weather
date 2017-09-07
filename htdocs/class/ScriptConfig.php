<?php
/**
 *
 * @author Бражников Дмитрий <brazhnikov_dn@anixtd.ru>
 */

namespace Weather;

class ScriptConfig
{
    /**
     *
     * @var integer
     */
    private $record_id;

    /**
     *
     * @param integer $id
     * @throws Exception
     */
    public function __construct($id = NULL)
    {
        if ( !is_int($id) ) {
            throw new \InvalidArgumentException('Недопустимый тип параметра.' . __FILE__ . ':' . __METHOD__ . ':' . __LINE__);
        }
        $this->record_id = $id;
    }

    /**
     *
     * @return void
     */
    public function updateTimeRun()
    {
        if ( empty($this->record_id) ) {
            throw new \InvalidArgumentException('Не определён идентификатор записи скрипта. ' . __FILE__ . ':' . __METHOD__ . ':' . __LINE__ );
        }

        $query = 'UPDATE scripts
                  SET LastRun = NOW(),
                  NextRun = date_ADD(NOW(), INTERVAL GREATEST( ROUND(1440/GREATEST(RunPerDay,1)), 1 ) MINUTE)
                  WHERE id = ' . $this->record_id;

        DB::getInstance()->execute($query);
    }

    /**
     *
     * @return integer
     */
    public function getRecordId()
    {
        return $this->record_id;
    }


}