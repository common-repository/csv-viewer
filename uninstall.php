<?php
function cwsUninstall(){
	global $wpdb;
	$table_name = $wpdb->prefix.CSV_TABLENAME;
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name){
		$sql = "DROP TABLE `".$table_name."`";
		$wpdb->query($sql);
	}
	if (is_dir(CSV_UPLOAD_PATH) !== false){
		$rmdir = rmdir(CSV_UPLOAD_PATH);
	}
	if ($rmdir !== false){
		echo __("The plugin has been uninstalled succesfully","csv_trans");
	}
}