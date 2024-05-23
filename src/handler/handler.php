<?php

namespace RusaDrako\resource_graber\handler;

class handler implements _inf_handler {

	protected $regex=null;
	protected $handler=null;
	protected $url_str_replace=null;

	public function __construct($set){
		$this->regex=$set['regex']?:null;
		$this->handler=$set['handler']?:null;
		$this->url_str_replace=$set['url_str_replace']?:null;
	}

	/**  */
	public function getLinkArray($html){
		$arr_result=[];

		$result=null;

		$result=$this->regex ? $this->useRegex($html) : $html;
//var_dump($html);
//exit;
		if(!$result){return $arr_result;}

		$func=$this->handler;

		$result=$func && is_callable($func) ? $func($result) : $result;

		if($this->url_str_replace){
			foreach($result as $v){
				$arr_result[]=str_replace(array_keys($this->url_str_replace), $this->url_str_replace, $v);
			}
		} else {
			$arr_result = $result;
		}

		return $arr_result;
	}

	/** Возвращает результат поиска по регулярному выражению */
	protected function useRegex($data){
		if(!preg_match_all($this->regex, $data, $arr_result)){
			return null;
		}
		return $arr_result[1];
	}

}
