<?php

//Путь к Вашему xml файлу
$xml_file="http://meteoinfo.ru/xml3/export/pay/220/220.xml";
$main_dir="/usr/local/lib/weather";

//Подключение к БД, укажите адрес сервера, имя пользователя, пароль и название БД.

$host='192.168.101.2';
$user='weather';
//$pasd=<hide>;
$db='weather';


mysql_pconnect($host,$user,$pasd) or die ('Нет подключения к базе!!!');
mysql_select_db($db) or die ('Ошибка при подключении!!!');
mysql_query("set character_set_client = cp1251;");
mysql_query("set character_set_results = cp1251;");

//Копирование  xml-файла к себе на хостинг

function http_get($url)
{
 $buffer = "";

   $url_stuff = parse_url($url);
   $port = isset($url_stuff['port']) ? $url_stuff['port'] : 80;

   $fp = fsockopen($url_stuff['host'], $port);

   $query  = 'GET ' . $url_stuff['path'] . " HTTP/1.0\n";
   $query .= 'Host: ' . $url_stuff['host'];
   $query .= "\n\n";

   fwrite($fp, $query);

   while ($tmp = fread($fp, 1024))
   {
       $buffer .= $tmp;
   }
   preg_match('/Content-Length: ([0-9]+)/', $buffer, $parts);
   return substr($buffer, - $parts[1]);
}

$fp = fopen ($main_dir."/forecast.xml", "w+");
fwrite ($fp, http_get($xml_file));
fclose ($fp);

//Парсинг xml-файла

$xml = simplexml_load_file($main_dir."/forecast.xml");
foreach ($xml->town as $town) {
    $name=iconv("utf-8","windows-1251",$town['name']);
    $ind=$town['id'];
    foreach ($town->date as $date) {
        $day=$date['day'];
        $tday=$date->tday;
	$tnight=$date->tnight;
	$prec=$date->prec;
	$prec_prob=$date->prec_prob;
	$wind_dir=$date->wind_dir;
	$windspeed=$date->windspeed;
	$pday=$date->pday;
	$pnight=$date->pnight;
	mysql_query("delete from forecast where Ind = '".$ind."' and dat = '".$day."'");
	$res=mysql_query("insert into forecast (Ind,Station,Country,Region,dat,tday,tnight,prec,prec_prob,wind_dir,wind_speed,weather_conditions,pday,pnight)
	                  values  (".$ind.",\"".$name."\",\"".$xml->country."\",\"".$xml->region."\",\"".$day."\",".$tday.",".$tnight.","
	                  .$prec.",".$prec_prob.",".$wind_dir.",".$windspeed.",\"".$xml->weather_conditions."\",".$pday.",".$pnight.")");
    }

    $cities = array(
	0 => array(
	    'index' => '29947',
	    'fname' => 'biysk.txt'
	),
	1 => array(
	    'index' => '29838',
	    'fname' => 'barnaul.txt'
	),
	2 => array(
	    'index' => '36052',
	    'fname' => 'g-alaysk.txt'
	)
    );
}

    // put info into file
    foreach ($cities as $k => $v) {
	$f = fopen($main_dir."/".$v['fname'],"w");
//	$res=mysql_query("select date_format(dat,'%d.%m.%y') as fcdate,tday from forecast where (dat between current_timestamp() - interval 1 day and current_timestamp() + interval 11 day) and (ind=".$v['index'].") order by dat asc");
	$res=mysql_query("select date_format(dat,'%d.%m.%y') as fcdate,tday from forecast where ind=".$v['index']." order by dat asc");
	while ($r = mysql_fetch_assoc($res)) fputs($f,"".$r['fcdate']."~".$r['tday']."~\r\n");
	fclose($f);
    }

    foreach ($cities as $k => $v) exec("cd /usr/local/lib/weather && /usr/bin/smbclient //SERV-TERM/Prognoz 111 -c 'put ".$v['fname']."' -I serv-term -U weather -W TDANIX > /dev/null 2>&1");

    /**
     * Добавление файликов на шару ФСК
     *
     */
    $resultCliArray = array();
    exec("smbclient //ftp/fsk -U weather% -c 'cd ./ObmenFSK; ls fs_*' 2>&1", $resultCliArray);
    $listTT = array();

    for ($i = 2; $i<count($resultCliArray)-2; $i++) { //то что от 2 до -2 это правильно! Лишние строки после выполнения команды smbclient
	$listTT[] = trim(substr($resultCliArray[$i], 0, 20));
    }

    foreach ($listTT as $TT) {
	foreach ($cities as $k => $v) {
	    exec("cd /usr/local/lib/weather && /usr/bin/smbclient //ftp/fsk -U weather% -W TDANIX -c 'cd ./ObmenFSK/" . $TT . "/OUT; put ".$v['fname']."' > /dev/null 2>&1");
	}
    }

?>


