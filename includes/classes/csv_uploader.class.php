<?php
class CwsCsvUploader extends CwsCsvViewer {
	private $upload_completed;
	
	private $csv_file				= '';
	private $csv_name				= '';
	private $csv_type				= '';
	private $csv_tmp_name			= '';
	private $csv_error				= 0;
	private $csv_size				= 0;
	private $csv_extension			= '';
	private $csv_name_without_ext	= '';
	private $number_of_rows			= 0;
	private $style_array			= array();
		
	function __construct($csvfile) {
		//Vul properties
		parent::__construct();
		
		$this->upload_completed		= false;
		$this->csv_file				= $csvfile;
		$this->csv_name				= $csvfile['name'];
		$this->csv_type				= $csvfile['type'];
		$this->csv_tmp_name			= $csvfile['tmp_name'];
		$this->csv_error			= $csvfile['error'];
		$this->csv_size				= $csvfile['size'];
		$this->csv_extension		= end(explode('.', $this->csv_name));
		$this->csv_name_without_ext	= substr($this->csv_name, 0, -4);
		
		$this->cwsCheckExtension();
	}
	
	function cwsCheckExtension() {
		//Check extension
		if (in_array($this->csv_extension, $this->allowed_extensions) === FALSE){
			$this->message = __("You can not upload this type of file", "csv_trans");
			return false;
		}
		$this->cwsCheckSize();
		
		if ($this->upload_completed === true) {
			return true;
		}
	}
	
	function cwsCheckSize() {
		//Check size
		if ($this->csv_size > $this->maximum_size){
			$this->message = __("The maximum allowed filesize is ", "csv_trans").$this->maximum_size.' kb';
			return false;
		}
		$this->cwsCheckIfFileAlreadyExists();
	}

	function cwsCheckIfFileAlreadyExists() {
		$dir = CSV_UPLOAD_PATH;
		if (is_dir($dir)) {
		    if ($dh = opendir($dir)) {
		        while (($file = readdir($dh)) !== false) {
		            if ($this->csv_name == $file){
		            	$this->message = __("This filename exists already", "csv_trans").'. '.__("Change the filename", "csv_trans");
		            	closedir($dh);
		            	return false;
		            }
		        }
		    }
		    closedir($dh);
		}
		$this->cwsUploadFile();	
	}
	
	function cwsUploadFile() {
		if (move_uploaded_file($_FILES['upload_csv_file']['tmp_name'], CSV_UPLOAD_PATH.$this->csv_name) === FALSE){
			$this->message = __("Upload failed for unknown reasons", "csv_trans").'!';
			return false;
		}
		$this->cswCheckNumberOfRows();
	}

	function cswCheckNumberOfRows() {
		$handle = fopen(CSV_UPLOAD_PATH.$this->csv_name, 'r');
		if ($handle !== false){
			//$data = fgetcsv($handle, 1000, ';');
			$rows = 1;
			while(($data = fgetcsv($handle, 1000, ';')) !== false){
				$rows++;
			}
			if ($rows > $this->maximum_rows){
				fclose($handle);
				$this->cwsRemoveUpload();
				return false;
			}
		}
		fclose($handle);
		$this->cwsInsertIntoDatabase();	
		$this->cswCreateStyleSheet();	
		$this->upload_completed = true;
	}
	
	function cwsRemoveUpload() {
		unlink(CSV_UPLOAD_PATH.$this->csv_name);
		$this->message = __("The file has too many rows", "csv_trans");
		return false;
	}
	
	function cwsInsertIntoDatabase() {
		global $wpdb;
		$this->style_array = array('css_table_font_family' => 'Arial', 'css_table_font_size' => '12px', 'css_table_margin' => '5px',
							'css_table_padding' => '5px', 'css_th_background_color' => '#00008B', 'css_th_color' => '#333333',
							'css_tr-even_background_color' => '#AFEEEE', 'css_tr-even_color' => '#333333', 'css_tr-oneven_background_color' => '#E0FFFF',
							'css_tr-oneven_color' => '#333333', 'css_td_margin' => '3px', 'css_td_padding' => '3px');
		$mysql_array = array('name' => $this->csv_name, 'header' => 1, 'delimitor' => ';', 'imagecolum' => 0);
		$array = array_merge($mysql_array, $this->style_array);
		$insert = $wpdb->insert($wpdb->prefix.CSV_TABLENAME, $array);
	}
	
	function cswCreateStyleSheet() {
		global $wpdb;
		$result = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix.CSV_TABLENAME."` WHERE `name` = '".$this->csv_name."'");
		
		$csv_style = new CwsCsvStyle();
		$csv_style->cwsCreateNewStyleFile($this->csv_name, $result[0]->id, $this->style_array);
	}
	
	function __destruct() {
		$this->allowed_extensions	= array();
		$this->maximum_size			= 0;
		$this->maximum_rows			= 0;
		$this->upload_completed		= false;
		$this->csv_file				= '';
		$this->csv_name				= '';
		$this->csv_type				= '';
		$this->csv_tmp_name			= '';
		$this->csv_error			= '';
		$this->csv_size				= '';
		$this->csv_extension		= '';
		unset($_FILES);
	}
}