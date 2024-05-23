<?php

namespace RusaDrako\resource_graber;

use RusaDrako\resource_graber\handler\_fac_handler;
use RusaDrako\resource_graber\upload\curl;

/** Грабер */
class Graber {

	protected $time_limit=600;
	protected $host_settings=[];

	public function setTimeLimit(int $time_sec){
		$this->time_limit=$time_sec;
	}

	public function setHostSettings(array $array){
		$this->host_settings=$array;
	}


	public function execute($url, $folder){
		echo PHP_EOL;
		echo "Обработка ссылки: {$url}" . PHP_EOL;

		$parse = parse_url($url);

		if(!array_key_exists($parse['host'], $this->host_settings)) {
			echo "!!! Настройки для хоста {$parse['host']} отсутствуют" . PHP_EOL;
			return;
		}

		$host_setting = $this->host_settings[$parse['host']];

		# Получаем содержание страницы
		$page = $this->uploadFile($url, $host_setting['page']["curl_set"]?:[]);
		# Получаем список ссылок для загрузки
		$arr_link = $this->getLinkArray($host_setting['handler']?:[], $page->getData());

		echo PHP_EOL;
		echo "Список файлов:" . PHP_EOL;
		var_export($arr_link);
		echo PHP_EOL;

		# Создаём папку назначения
		$this->createFolder($folder);

		$count_file_name=strlen(count($arr_link));

		# Загружаем файлы по списку
		foreach($arr_link as $k=>$v){
			set_time_limit($this->time_limit);
			$link=$v;

			echo " - Загрузка файла: {$link}" . PHP_EOL;
			# Загружаем файл
			$file_data = $this->uploadFile($link, $host_setting['file']?:[]);

			$key=str_pad($k+1, $count_file_name, '0', STR_PAD_LEFT );
			$basename=basename($file_data->getFileName());

			$file_name = $this->getNewFileName($host_setting['safe_file']?:[], $basename, $key);

			$full_file_name = $folder . $file_name;
			echo "\tСохранение файла: {$full_file_name}" . PHP_EOL;
			file_put_contents($full_file_name, $file_data->getData());
			echo "\tРазмер: " . strlen($file_data->getData()) . PHP_EOL;
		}

		echo PHP_EOL;
	}

	/** Загрузка файла */
	public static function uploadFile(string $link, array $addSet=[]) {
		$object_upload=new curl();
		$object_upload->setCurlSet(CURLOPT_URL, $link);
		foreach($addSet as $k=>$v){
			$object_upload->setCurlSet($k, $v);
		}
		$object_upload->grabeData($link);
		return $object_upload;
	}

	/** Загрузка файла */
	public static function createFolder(string $folder, $right=0777) {
		if(!file_exists($folder)){
			echo "Создание папки: {$folder}" . PHP_EOL;
			mkdir($folder, $right, 1);
		}
	}

	/** Обработка содержимого */
	public static function getLinkArray($host_setting, string $data) {
		$object_handler=_fac_handler::getHandler($host_setting);
		$arr_link=$object_handler->getLinkArray($data);
		return $arr_link;
	}

	/** Возвращает имя файла */
	public static function getNewFileName($host_setting, $basename, $key) {
		if(!$host_setting["use_key_for_file_name"]?:false){
			return $basename;
		}
		$extension = pathinfo($basename, PATHINFO_EXTENSION);
		return "{$key}.{$extension}";
	}

/**/
}
