<?php
class CwsReturnView {
	public $registersheet = '';
	public $enqueuesheet = '';
	public $id = 0;
	
	function cwsGetStyleSheet($id){
		global $wpdb;
		$results = $wpdb->get_results("SELECT `name` FROM `".$wpdb->prefix.CSV_TABLENAME."` WHERE `id` = '".$id."'");
		$cssfile = str_replace('csv', 'css', $results[0]->name);
		$myStyleUrl = get_option('siteurl').'/wp-content/uploads/csv_viewer/'.$cssfile;
		$myStyleFile = CSV_UPLOAD_PATH.$cssfile;
		if ( file_exists($myStyleFile) ) {
			$this->registersheet =	wp_register_style('myStyleSheets', $myStyleUrl);
			$this->enqueuesheet =	wp_enqueue_style('myStyleSheets');
		}
		$this->id = $id;
	}
	
	function cwsReturnSheet() {
		return $this->registersheet;
		return $this->enqueuesheet;
	}
}