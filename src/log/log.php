<?php

namespace RusaDrako\resource_graber\log;

class log {

	/** Объект модели */
	protected static $_object = null;

	/** Объект модели */
	protected $is_use = false;

	/** Загрузка класса */
	function __construct () {}

	/** Вызов объекта класса
	 * @return object Объект модели
	 */
	public static function call() {
		if(null === self::$_object) {
			self::$_object = new static();
		}
		return self::$_object;
	}

	/**  */
	public function isUse($data){
		$this->is_use = $data;
	}

	/**  */
	public function addLog($data){
		if(!$this->is_use) { return; }
		echo date('Y-m-d H:i:s') . ' ' . $data . PHP_EOL;
	}

	/**  */
	public function addData($data){
		if(!$this->is_use) { return; }
		var_export($data);
	}

}
