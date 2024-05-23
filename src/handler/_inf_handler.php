<?php

namespace RusaDrako\resource_graber\handler;

interface _inf_handler {
	/** Получает список файлов для загрузки из кода страницы */
	public function getLinkArray($data);
}
