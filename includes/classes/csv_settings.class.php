<?php
class CwsCsvSettings extends CwsCsvViewer {
	private $id 				= 0;
	private $filename 			= '';
	private $current_filename 	= '';
	private $header				= '';
	private $delimitor			= '';
	private $imagecolum			= '';
	
	function __construct(){
		parent::__construct();
	}
	
	function cwsSetCsvId($id){
		$this->id = $id;
	}
	
	function cwsSetCsvName($name, $ext){
		global $wpdb;
		
		if (empty($name) !== FALSE){
			$this->message = __("The filename is not entered", "csv_trans");
			return false;
		}
		
		if (in_array($ext, $this->allowed_extensions) === FALSE){
			$this->message = __("This filetype is not allowed", "csv_trans");
			return false;
		}
		
		$this->filename = $name.'.'.$ext;
		
		$row = $wpdb->get_results("SELECT `id`, `name` FROM `".$wpdb->prefix.CSV_TABLENAME."`");
		$oldname = $row[0]->name;
		foreach ($row as $fields){
			if (($fields->name == $this->filename) && ($fields->id !== $this->id)){
				$this->message = __("This filename exists already", "csv_trans");
				return false;
			}
			if (($fields->name == $this->filename) && ($fields->id == $this->id)){
				return true;
			}
			if ($fields->id == $this->id){
				$this->current_filename = $fields->name;
			}
		}
		
		if (is_dir(CSV_UPLOAD_PATH)) {
			if ($dh = opendir(CSV_UPLOAD_PATH)) {
				while (($mapfile = readdir($dh)) !== false) {
					if ($mapfile == $this->filename){
						echo __("This filename exists already", "csv_trans");;
						return false;
					}
				}
			}
			closedir($dh);
		}
		
		if (is_dir(CSV_UPLOAD_PATH.substr($oldname, 0, -4))){
			rename(CSV_UPLOAD_PATH.substr($oldname, 0, -4), CSV_UPLOAD_PATH.substr($this->filename, 0, -4));
		}
		
		$name = substr($this->filename, 0, -4);
		$currentname = substr($this->current_filename, 0, -4);
		$rename = rename(CSV_UPLOAD_PATH.$this->current_filename, CSV_UPLOAD_PATH.$this->filename);
		$wpdb->query("UPDATE `".$wpdb->prefix.CSV_TABLENAME."` SET `name` = '".$this->filename."' WHERE `id` = '".$this->id."' ");
	}
	
	function cwsSetCsvHeader($header){
		global $wpdb;
		$this->header = $header;
		
		if (($this->header == '0') || ($this->header == '1')){
			$wpdb->query("UPDATE `".$wpdb->prefix.CSV_TABLENAME."` SET `header` = '".$this->header."' WHERE `id` = '".$this->id."' ");
		}
	}
	
	function cwsSetCsvDelimitor($delimitor){
		global $wpdb;
		$this->delimitor = $delimitor;
		
		if (in_array($this->delimitor, $this->allowed_delimitors)){
			$wpdb->query("UPDATE `".$wpdb->prefix.CSV_TABLENAME."` SET `delimitor` = '".$this->delimitor."' WHERE `id` = '".$this->id."' ");
		}
	}
	
	function cwsSetCsvImagecolum($imagecolum){
		global $wpdb;
		$this->imagecolum = $imagecolum;
		
		if (is_numeric($this->imagecolum) == false){
			$this->message = __("The imagecolum must be numeric", "csv_trans");
			return false;
		}
		
		$handle = fopen(CSV_UPLOAD_PATH.$this->filename, 'r');
		if ($handle !== false){
			$colums = count(fgetcsv($handle, 1000, $this->delimitor));
			if ($this->imagecolum > $colums){
				$this->message = __("The value of imagecolum is too high", "csv_trans");
				return false;
			}
		}
		fclose($handle);
		
		$wpdb->query("UPDATE `".$wpdb->prefix.CSV_TABLENAME."` SET `imagecolum` = '".$this->imagecolum."' WHERE `id` = '".$this->id."' ");
		
		$this->message = __("The settings are changed of ", "csv_trans").$this->filename;
		return true;
	}
}