<?php

namespace RusaDrako\mp3_graber\upload;

abstract class _abs_upload implements _inf_upload {
	/**  */
	protected $data;
	protected $file_name;

	/**  */
	public function setData($data){
		$this->data = $data;
		return $this;
	}

	/**  */
	public function getData(){
		return $this->data;
	}

	/**  */
	public function getFileName(){
		return $this->file_name;
	}

	/**  */
	abstract public function grabeData();
}
