<?php

namespace RusaDrako\mp3_graber\handler;

class _fac_handler {
	/** Подбираем обработчик в зависимости от хоста */
	public static function getHandler(array $hast_setting){
		return new handler($hast_setting);
	}
}
