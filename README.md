# RusaDrako\\resource_graber

[![Version](http://poser.pugx.org/rusadrako/resource_graber/version)](https://packagist.org/packages/rusadrako/resource_graber)
[![Total Downloads](http://poser.pugx.org/rusadrako/resource_graber/downloads)](https://packagist.org/packages/rusadrako/resource_graber/stats)
[![License](http://poser.pugx.org/rusadrako/resource_graber/license)](./LICENSE)

Автоматическое скачивание ресурсов с указанного сайта.

## Установка (composer)
```sh
composer require 'rusadrako/resource_graber'
```

## Установка (manual)
- Скачать и распоковать библиотеку.
- Добавить в код инструкцию:
```php
require_once('/resource_graber/src/autoload.php')
```

## Массив настроек хоста
Все параметры настроек не обязательны
```php
array(
	"host.ru"=>array(
		# Настройки для загрузки страницы
		"page"=>array(
			# Дополнительные настройки curl
			"curl_set"=>array(CURLOPT_TIMEOUT=>300),
		),
		# Настройки обработчика страницы
		"handler"=>array(
			# Регулярное выражение для получения ссылок на файлы
			"regex"=>'/"url":"([^"]*)"/umi',
			# Функция дополнительной обработки данных полученных регулярным выражением
			"handler"=>function($data){ return array();},
			# Замена символов в URL
			'url_str_replace'=>array('\\'=>''),
		),
		# Настройки для загрузки файлов из списка
		"file"=>array(
			# Дополнительные настройки curl
			"curl_set"=>array(CURLOPT_TIMEOUT=>300),
		),
		# Сохранение загруженных файлов
		"safe_file"=>array(
			# Использовать ключ массива в качестве имени файла
			"use_key_for_file_name"=>true,
		),
	),
);
```

## Пример кода
```php
use RusaDrako\resource_graber\Graber;

# Загрузка библиотеки
require_once('../src/autoload.php');

# Обработчик массива url
$func=function($data){
	$result=[];
	foreach($data as $k=>$v){
		$result[$k]="https://www.google.com/{$v}";
	}
	return $result;
};

# Настройка обработки хоста
$host_settings=array(
	"www.google.com"=>array(
		"handler"=>array(
			"regex"=>'/src="([^"]*)"/umi',
			"handler"=>$func,
		),
		"safe_file"=>array(
			"use_key_for_file_name"=>true,
		),
	),
);

# Формирование объекта
$graber=new Graber();
$graber->setTimeLimit(600);
$graber->setHostSettings($host_settings);

# Выполние загрузки
$graber->execute("https://www.google.com/", "/test/");
```
