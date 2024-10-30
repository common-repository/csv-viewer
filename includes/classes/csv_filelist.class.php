<?php
class CwsCsvFileList extends CwsCsvViewer {
	private $filelist = array();
	
	function __construct() {
		parent::__construct();
	}
	
	function cwsGetFileList() {
		global $wpdb;
		
		$fields = $wpdb->get_results("SELECT `name` FROM `".$wpdb->prefix.CSV_TABLENAME."`");
		if (empty($fields)){
			return false;
		}
		foreach ($fields as $filename){
			$this->filelist[] = $filename->name;
		}
		return $this->filelist;
	}
}