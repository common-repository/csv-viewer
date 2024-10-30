<?php
class CwsCsvImageUploader {
	private $name		= '';
	private $images 	= array();
	private $maxsize	= 0;
	
	function __construct($name, $images){
		$this->name = $name;
		$this->images = $images;
		$this->maxsize = 1000000; //1 MB
		
		$this->cwsCheckAndUploadImages();
	}
	
	function cwsCheckAndUploadImages(){
		//Check if map exists, else create it
		if (empty($this->images))
			return false;
		
		$dir = CSV_UPLOAD_PATH.substr($this->name, 0, -4);
		
		if (is_dir($dir)){
			if (!is_writable($dir)){
				return false;
			}
		}else{
			mkdir($dir);
		}
		
		for ($i=0; $i<count($this->images['tmp_name']); $i++){
			if ($this->images['error'][$i] > 0){
				return false;
			}
			if ($this->images['size'][$i] > $this->maxsize){
				return false;
			}
			
			if (file_exists($dir.DIRECTORY_SEPARATOR.$this->images['name'][$i]))
				return false;
			
			move_uploaded_file($this->images['tmp_name'][$i], $dir.DIRECTORY_SEPARATOR.$this->images['name'][$i]);
		}
		return true;
	}
}