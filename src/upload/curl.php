<?php

namespace RusaDrako\resource_graber\upload;

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
			echo "\tОшибка curl: " . curl_error($curl) . PHP_EOL;
			# Закрываем соединение
			curl_close($curl);
			return null;
		}
		$code=curl_getinfo($curl, CURLINFO_HTTP_CODE);
		# Если происходит перенаправление
		if ($code==302) {
			$new_url=curl_getinfo($curl, CURLINFO_REDIRECT_URL);
			$this->curl_set[CURLOPT_URL]=$new_url;
			echo "\tПеренаправление: {$new_url}" . PHP_EOL;
			$result = $this->grabeData($new_url)->getData();
		}
		# Закрываем соединение
		curl_close($curl);
		$this->setData($result);
		return $this;
	}

}
