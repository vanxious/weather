<?php

namespace Weather;

abstract class WeatherService
{
    /**
     * Идентификатор населённого пункта в формате,
     * подходящем для конкретного сервиса погоды.
     *
     * @var mixed
     */
    public $cityId;

    public $connectionString;

    public $scriptId;

    public $className;

    public $cityName;

    public $fileName;

    /**
     * Текущий файл xml с погодой.
     *
     * @var object
     */
    public $xml;

    /**
     * Массив данных с данными по населённому пункту
     * готовые к вставке в БД.
     *
     * @var array
     */
    public $weatherData = array();

    public function __construct(ScriptConfig $scriptProperties)
    {
        $this->cityId   = $scriptProperties->CityIdentity;
        $this->cityName = $scriptProperties->CityName;
        $this->scriptId = $scriptProperties->id;
        $this->fileName = $scriptProperties->FileName;
        $this->className = $scriptProperties->ClassName;
    }


    /**
     * Запуск получения и записи погоды в БД.
     * Необходимо обязательно переопределить в потомке!
     */
    public abstract function run();


    /**
     * Получение файла XML с удалённого сервера.
     * @return void
     */
    public function getXMLFile()
    {
        $dir = $this->_getXmlDir();

        if (empty($this->fileName)) {
            throw new Exception ('Не указано имя файла.', 200);
        }

        list($fileName, $ras) = explode('.', $this->fileName);
        $fileName = $this->className . '_' . $fileName;

        $executeString = 'wget -q -O ' . $dir . $fileName . '.xml ' . $this->connectionString;

        try {
            @exec($executeString);
        } catch (Exception $e) {
            //вызов этого события крааайне маловероятен
            throw new Exception('Невозможно подключиться к удалённом серверу. ' . $e->getMessage());
        }

        try {
            $this->xml = @simplexml_load_file($dir . $fileName . '.xml');
        } catch (Exception $e) {
            throw new Exception('Невозможно считать данные из файла. ' . $e->getMessage(), 201);
        }

        if ($this->xml === FALSE) {
            throw new Exception('Невозможно получить файл xml.', 202);
        }

        Log::message('Загружен файл ' . $dir . $fileName . '.xml');
    }

    /**
     * Возвращает директорию, содержащую xml файлы,
     * в случае отсутствия директории создаёт её.
     *
     * @return string
     * @throws Exception
     */
    private function _getXmlDir()
    {
        $dir = Config::getInstance()->getXMLDir();

        if ( !is_dir($dir) ) {
            Debug::Message('Создание директории ' . $dir);

            $resmk = mkdir($dir);

            if ($resmk === FALSE) {
                throw new Exception('Невозможно создать директорию ' . $dir);
            }
        }

        return $dir;
    }


    /**
     * Обновление данных по датам, прогноз у которых обновился.
     * @return int количество обновлённых строк.
     */
    public function updateData()
    {
        $sql = '';
        $updateRows = 0;
        foreach ($this->weatherData as $date => $val) {
            $set = array();
            foreach ($val as $key => $value) {
                if ($value === (int)$value) {
                    $set[] = $key . ' = ' . $value;
                } else {
                    $set[] = $key . ' = \''.$value.'\'';
                }
            }

            $sql = '
                            select count(*) as count from forecast
                            where Station = \''.$this->cityName.'\' and dat = \''.$date.'\'
                        ';

            $result = DB::getInstance()->query($sql); //ошибка обрабатывается в классе DB

            if ($result[0]['count'] > 0) {
                $sql = '
                                    update forecast
                                    set ' . implode(', ', $set) . '
                                    where Station = \''.$this->cityName.'\' and dat = \''.$date.'\'
                                ';

                $result = DB::getInstance()->execute($sql);

                $updateRows = $updateRows + (int)$result;

                unset($this->weatherData[$date]);
            }

        }//foreach

        return $updateRows;
    }


    /**
     * Вставка данных по числам, по которым отсутствуют данные о прогнозе погоды.
     * @return int количество вставленных строк.
     */
    public function insertData()
    {
        $sql = '';
        $insertRows = 0;
        if (count($this->weatherData) == 0) {
            return;
        }

        foreach ($this->weatherData as $date => $val) {
            $field = array();
            $set   = array();

            foreach ($val as $key => $value) {
                $field[] = $key;

                if ($value === (int)$value) {
                    $set[] = $value;
                } else {
                    $set[] = '\''.$value.'\'';
                }
            }

            $sql = '
                            insert into forecast(Station, '. implode(', ', $field) .')
                            values (\''.$this->cityName.'\', '.  implode(', ', $set) . ')
                        ';

            $result = DB::getInstance()->execute($sql);

            $insertRows = $insertRows + (int)$result;

        }//foreach

        return $insertRows;
    }


    /**
     * Удаление xml файла
     * @return void
     */
    public function deleteXMLFile()
    {
        $dir = Config::getInstance()->getXMLDir();

        list($fileName, $ras) = explode('.', $this->fileName);
        $fileName = $dir . $this->className . '_' . $fileName . '.xml';
        $res = @unlink($fileName);
        if ($res === FALSE) {
            Log::message(date('M d H:i:s') . ' ' . $this->cityName . ' Невозможно удалить файл XML. Возможно, будет использоваться старый.', 203);
        }
    }


}