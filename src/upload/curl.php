<?php

namespace RusaDrako\resource_graber\upload;

use RusaDrako\resource_graber\log\log;

class curl extends _abs_upload {

	/** @var array Настройка curl */
	protected $curl_set=[
		CURLOPT_RETURNTRANSFER   => TRUE,
		CURLOPT_TIMEOUT          => 1200,
	];

	/** Задаёт настройку для запроса curl */
	public function setCurlSet($name, $value){
		$this->curl_set[$name]=$value;
	}

	/** Выполняет получение данных по ссылке */
	public function grabeData(){
		if (!array_key_exists(CURLOPT_USERAGENT, $this->curl_set)) {
			$this->setCurlSet(CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		}
		# Запускай curl
		$curl = curl_init();
		# Запоминаем имя файла
		$url=$this->curl_set[CURLOPT_URL];
		$this->file_name=basename($url);
		# Выполняем настройки
		curl_setopt_array($curl, $this->curl_set);
		# Выполняем curl
		$result = curl_exec($curl);
		# Если curl выдал ошибку
		if ($result === false) {
			# Выводим сообщение
			log::call()->addLog("\tОшибка curl: " . curl_error($curl));
			# Закрываем соединение
			curl_close($curl);
			return null;
		}
		$code=curl_getinfo($curl, CURLINFO_HTTP_CODE);
		# Если происходит перенаправление
		if ($code==302) {
			$new_url=curl_getinfo($curl, CURLINFO_REDIRECT_URL);
			$this->curl_set[CURLOPT_URL]=$new_url;
			log::call()->addLog("\tПеренаправление: {$new_url}");
			$result = $this->grabeData($new_url)->getData();
		}
		echo PHP_EOL;
		# Закрываем соединение
		curl_close($curl);

		$this->setData($result);
		return $this;
	}

}
