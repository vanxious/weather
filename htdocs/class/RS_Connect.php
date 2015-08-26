<?php

class RS_Connect extends FTPConnect
{

    protected $config = array(
            'host' => 'ftp.tdanix.ru',
            'user' => 'shop2ftp',
            'pass' => '=J3$2q!',
            'defaultDir' => 'ObmenRS',
            'fileMask'   => 'rs_*',
    );

    /**
     *
     * @param string $fileName
     */
    public function putFile($fileName = NULL)
    {
            if ( empty($fileName) || !is_string($fileName) ) {
                throw new Exception('Неверный параметр!');
            }

            $this->fileName = $fileName;

            foreach ($this->getListTT() as $TT) {
                $result = ftp_fput($this->resource, './' . $TT . '/OUT/' . $this->fileName, $this->getFileResources(), FTP_ASCII);
            }

            return $result;
    }


}