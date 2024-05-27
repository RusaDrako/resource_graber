<pre><?php

use RusaDrako\resource_graber\Graber;

# Загрузка библиотеки
require_once('../src/autoload.php');

# Обработчик массива url
$func=function($data){
	$result=array();
	foreach($data as $k=>$v){
		$result[$k]="https://www.google.com/{$v}";
	}
	return $result;
};

# Настройка обработки хоста
$host_settings=array(
	"www.google.com" => array(
		"handler" => array(
			"regex" => '/src="([^"]*)"/umi',
			"handler" => $func,
		),
		"safe_file" => array(
			"use_key_for_file_name" => true,
		),
	),
);

# Формирование объекта
$graber=new Graber();
$graber->setTimeLimit(600);
$graber->setHostSettings($host_settings);

# Выполние загрузки
$graber->execute("https://www.google.com/", "/test/");