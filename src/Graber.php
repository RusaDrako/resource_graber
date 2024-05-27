<?php

namespace RusaDrako\resource_graber;

use RusaDrako\resource_graber\handler\_fac_handler;
use RusaDrako\resource_graber\log\log;
use RusaDrako\resource_graber\upload\curl;

/** Грабер */
class Graber {

	protected $time_limit=600;
	protected $host_settings=[];

	/** Задаёт режим логирования */
	public function setLog(bool $value){
		log::call()->isUse($value);
	}

	/** Задаёт значение set_time_limit для одной итерации скачивания файлов */
	public function setTimeLimit(int $time_sec){
		$this->time_limit=$time_sec;
	}

	/** Задаёт настройки хостов, для обработки ссылок */
	public function setHostSettings(array $value){
		$this->host_settings=$value;
	}

	/** Выполняет полный цикл получения файлов со страницы */
	public function execute($url, $folder, $position=0){
		echo "Обработка ссылки: {$url}" . PHP_EOL;

		$parse = parse_url($url);

		if(!array_key_exists($parse['host'], $this->host_settings)) {
			log::call()->addLog("!!! Настройки для хоста {$parse['host']} отсутствуют");
			return;
		}

		$host_setting = $this->host_settings[$parse['host']];

		# Получаем содержание страницы
		$page = $this->uploadFile($url, $host_setting['page']["curl_set"]?:[]);
		# Получаем список ссылок для загрузки
		$arr_link = $this->getLinkArray($host_setting['handler']?:[], $page->getData());

		if(!$arr_link){
			log::call()->addLog("Список файлов пуст.");
			return;
		}

		echo "Список файлов:" . PHP_EOL;
		log::call()->addData($arr_link);
		echo PHP_EOL;

		# Создаём папку назначения
		$this->createFolder($folder);

		$count_file_name=strlen(count($arr_link));

		# Загружаем файлы по списку
		foreach($arr_link as $k=>$v){

			set_time_limit($this->time_limit);
			$link=$v;

			$key=str_pad($k+1, $count_file_name, '0', STR_PAD_LEFT );

			log::call()->addLog("{$key} - Загрузка файла: {$link}");

			if(is_array($position)){
				if(!in_array(($k+1), $position)) {
					log::call()->addLog("\tПропуск загрузки");
					continue;
				}
			} else {
				if(($k+1)<$position) {
					log::call()->addLog("\tПропуск загрузки");
					continue;
				}
			}

			# Загружаем файл
			$file_data = $this->uploadFile($link, $host_setting['file']?:[]);

			$basename=basename($file_data->getFileName());

			$file_name = $this->getNewFileName($host_setting['safe_file']?:[], $basename, $key);

			$full_file_name = $folder . $file_name;
			log::call()->addLog("\tСохранение файла: {$full_file_name}");
			file_put_contents($full_file_name, $file_data->getData());
			log::call()->addLog("\tРазмер: " . strlen($file_data->getData()));
		}
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
			log::call()->addLog("Создание папки: {$folder}");
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
