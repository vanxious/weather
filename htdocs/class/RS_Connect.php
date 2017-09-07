<?php

namespace Weather;

class RS_Connect extends FTPConnect
{

    protected $config = array(
            'host' => 'ftp.tdanix.ru',
            'user' => 'shop2ftp',
            'pass' => '=J3$2q!',
            'defaultDir' => 'ObmenRS',
            'fileMask'   => 'rs_*',
    );

}