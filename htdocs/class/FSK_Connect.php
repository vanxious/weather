<?php

class FSK_Connect extends FTPConnect
{

    protected $config = array(
            'host' => '192.168.101.29',
            'user' => 'fskftp',
            'pass' => '78HbD2qz',
            'defaultDir' => 'ObmenFSK',
            'fileMask'   => 'fs_*',
    );

    /**
     *
     * @param string $fileName
     */
    public function putFile($fileName = NULL)
    {
            $this->$fileName = $fileName;

            foreach ($this->getListTT() as $TT) {
                $result = ftp_fput($this->resource, './' . $TT . '/OUT/' . $this->$fileName, $this->getFileResources(), FTP_ASCII);
            }

            return $result;
    }

}
