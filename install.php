<?php
function cwsInstall(){
	global $wpdb;
	$table_name = $wpdb->prefix.CSV_TABLENAME;
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
		$sql = "CREATE TABLE `".$table_name."` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `name` varchar(255) DEFAULT NULL,
		  `header` int(1) DEFAULT NULL,
		  `delimitor` varchar(255) DEFAULT NULL,
		  `imagecolum` int(11) DEFAULT NULL,
		  `css_table_font_family` varchar(255) DEFAULT NULL,
		  `css_table_font_size` varchar(255) DEFAULT NULL,
		  `css_table_margin` varchar(255) DEFAULT NULL,
		  `css_table_padding` varchar(255) DEFAULT NULL,
		  `css_th_background_color` varchar(255) DEFAULT NULL,
		  `css_th_color` varchar(255) DEFAULT NULL,
		  `css_tr-even_background_color` varchar(255) DEFAULT NULL,
		  `css_tr-even_color` varchar(255) DEFAULT NULL,
		  `css_tr-oneven_background_color` varchar(255) DEFAULT NULL,
		  `css_tr-oneven_color` varchar(255) DEFAULT NULL,
		  `css_td_margin` varchar(255) DEFAULT NULL,
		  `css_td_padding` varchar(255) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
	if (is_dir(CSV_BASEDIR.'wp-content'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.CSV_UPLOAD_DIR) === false){
		if (is_dir(CSV_BASEDIR.'wp-content'.DIRECTORY_SEPARATOR.'uploads') === false){
			mkdir(CSV_BASEDIR.'wp-content'.DIRECTORY_SEPARATOR.'uploads', 0777);
		}
		mkdir(CSV_BASEDIR.'wp-content'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.CSV_UPLOAD_DIR, 0777);
	}
}