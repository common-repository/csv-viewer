<?php
class CwsCsvViewer {
	protected $allowed_extensions	= array();
	protected $allowed_delimitors	= array();
	protected $maximum_size			= 0;
	protected $maximum_rows			= 0;
	public $message					= '';
	protected $filedata				= array('filename' => '', 'header' => '', 'delimitor' => '', 'imagecolum' => '', 'headerdata' => array(), 'contentdata' => array());
	
	function __construct() {
		$this->allowed_extensions	= array('csv');
		$this->allowed_delimitors	= array(';');
		$this->maximum_size			= CSV_FILE_MAXIMUM_SIZE;
		$this->maximum_rows			= CSV_FILE_MAXIMUM_ROWS;
		$this->message				= '';
	}
	
	function cwsGetFileProperties($file) {
		global $wpdb;
		//Get data from database
		$row = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix.CSV_TABLENAME."` WHERE `name` = '".$file."'");
		
		if (empty($row)){
			$this->message = __("The settings can not be retrieved", "csv_trans");
			return false;
		}
	
		$settings = array();
		foreach ($row as $key => $field){
			$settings[$key] = $field;
		}
		
		return $settings;
	}
	
	function cwsGetCsvFile($file) {
		if (is_string($file) === false){
			return false;
		}
		
		global $wpdb;
				
		$fields = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix.CSV_TABLENAME."` WHERE `name` = '".$file."'");
		foreach ($fields as $field){
			$this->filedata['filename'] = $file;
			$this->filedata['id'] = $field->id;
			$this->filedata['header'] = $field->header;
			$this->filedata['delimitor'] = $field->delimitor;
			$this->filedata['imagecolum'] = $field->imagecolum;
		}
		
		$handle = fopen(CSV_UPLOAD_PATH.$this->filedata['filename'], 'r');
		if ($this->filedata['header'] == '1'){
			if ($handle !== false){
				$this->filedata['headerdata'] = fgetcsv($handle, 1000, $this->filedata['delimitor']);
			}
		}
		while (($contentdata = fgetcsv($handle, 1000, $this->filedata['delimitor'])) !== false){
			$this->filedata['contentdata'][] = $contentdata;
		}
		fclose($handle);
		return $this->filedata;		
	}
	
	function cwsRemoveCsvFile($file) {
		global $wpdb;
		$selectcss = $wpdb->get_results(
								"SELECT `id`,
									`css_table_font_family`, `css_table_font_size`,
									`css_table_margin`, `css_table_padding`,
									`css_th_background_color`, `css_th_color`,
									`css_tr-even_background_color`, `css_tr-even_color`,
									`css_tr-oneven_background_color`, `css_tr-oneven_color`,
									`css_td_margin`, `css_td_padding`
								FROM `".$wpdb->prefix.CSV_TABLENAME."` WHERE `name` = '".$file."'");
		$cssarray = array();
		foreach ($selectcss[0] as $key => $value){
			$cssarray[$key] = $value;
		}
		
		$styleclass = new CwsCsvStyle();
		$removecss = $styleclass->cwsRemoveStyle($cssarray);
		
		$removerecord = $wpdb->query("DELETE FROM `".$wpdb->prefix.CSV_TABLENAME."` WHERE `name` = '".$file."'");
		
		$imagedir = CSV_UPLOAD_PATH.substr($file, 0, -4);
		
		$dir = CSV_UPLOAD_PATH;
		if (is_dir($dir)) {
		    if ($dh = opendir($dir)) {
		        while (($mapfile = readdir($dh)) !== false) {
					if ($mapfile == $file){
						$removefile = unlink($dir.$file);
					}
				}
			}
		}
		closedir($dh);
		if (is_dir($imagedir)){
			if (is_writable($imagedir)){
				if ((($handle = opendir($imagedir)) !== false))
				while (($contents = readdir($handle)) !== false){
					if($contents != '.' && $contents != '..'){
						$removeimages = unlink($imagedir.DIRECTORY_SEPARATOR.$contents);
					}
				}
				closedir($handle);
				rmdir($imagedir);
			}
		}

		if ($removerecord === false){
			$this->message = __("The file can not be removed from the database", "csv_trans");
			return false;
		}elseif ($removefile === false){
			$this->message = __("The file can not be removed from the uploadfolder", "csv_trans");
			return false;
		}elseif ($removecss === false){
			$this->message = __("The css-file can not be removed from the uploadfolder", "csv_trans");
			return false;			
		}elseif($removeimages === false){
			$this->message = __("The related images can not be removed", "csv_trans");		
		}elseif (($removerecord === true) && ($removefile === true) && ($removecss === true) && ($removeimages === true)){
			$this->message = 'Het bestand '.$file.' is succesvol verwijderd';
			return true;
		}
	}
	
	function cwsGetImages($filename){
		$images = array();
		$dir = CSV_UPLOAD_PATH.substr($filename, 0, -4);
		if ((is_dir($dir)) !== false){ 
			if (($handle = opendir($dir)) !== false){
				$i = 0;
				while (($file = readdir($handle)) !== false){
					if (($file == '.') || ($file == '..')){}else{
						$images[$i] = $file;
						$i++;
					}
				}
			}
			return $images;
		}
	}
	
	function cwsRemoveImages($filename, $images){
		if (empty($images))
			return false;
		
		$dir = CSV_UPLOAD_PATH.substr($filename, 0, -4).DIRECTORY_SEPARATOR;
		foreach ($images as $image){
			unlink($dir.$image);
		}
		if (file_exists($dir.'Thumbs.db')){
			unlink($dir.'Thumbs.db');
		}
		
		return true;
	}
	
	function cwsGetMessage() {
		if (!empty($this->message))
			return $this->message;
	}
}