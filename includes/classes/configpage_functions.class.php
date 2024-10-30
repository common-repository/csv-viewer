<?php
class CwsConfigpageFunctions {
	public $csv_template_class;
	
	function __construct() {
		//Verwijzing naar het object om de templates van de divs op te halen
		$this->csv_template_class = new CwsConfigPageTemplates;
	}
	
	function cwsGetPost() {
		global $wpdb;
		
		
		if (!$_POST){
			$table_name = $wpdb->prefix.CSV_TABLENAME;
			if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
				echo '<form method="POST">';
				echo '<input type="submit" class="button-primary" name="csv_install_plugin" value="'.__("Install plugin", "csv_trans").'" />';
				echo '</form>';
			}else{
				//Wanneer er geen post is, alle divs weergeven die in de functie staan
				$this->cwsReturnAllDivs();
				$this->cwsReturnUninstallButton();
			}
		}
		
		if (isset($_POST['csv_install_plugin'])){
			cwsInstall();
			$this->cwsReturnAllDivs();
		}
		
		if (isset($_POST['csv_uninstall'])){
			cwsUninstall();
			unset($_POST);
		}

		if (isset($_POST['csv_viewer_upload_file'])) {
			//Bestand gekozen
			if (!empty($_FILES['upload_csv_file']['tmp_name'])) {
				//Upload class wordt doorgelopen
				$csv_uploader = new CwsCsvUploader($_FILES['upload_csv_file']);
				//Alle divs tonen en een bericht meesturen voor de oorspronkelijke div
				$this->cwsReturnAllDivs('cwsGetDivUpload', $csv_uploader->cwsGetMessage());
			}else{
				//Wanneer er geen bestand gekozen is, alle divs laten zien die in de functie staan
				$this->cwsReturnAllDivs();
			}
		}
		
		if (isset($_POST['csv_viewer_settings_file'])) {
			//Als er een bestand gekozen is
			if (!empty($_POST['select_csv_file'])) {
				//Alle divs tonen, behalve de oorspronkelijke
				$this->cwsReturnAllDivs('cwsGetDivFileList');
				//De class haalt de huidige instellingen op
				$csv_settings = new CwsCsvSettings();
				if (($result = $csv_settings->cwsGetFileProperties($_POST['select_csv_file'])) !== false){
					$this->csv_template_class->cwsGetDivFileSettings($result);
				}else{
					$this->csv_template_class->cwsGetDivFileSettings(false, $csv_settings->message);
				}
				echo $csv_settings->message;
			}else{
				//Wanneer er geen bestand gekozen is, alle divs laten zien die in de functie staan
				$this->cwsReturnAllDivs();
			}
		}
		
		if (isset($_POST['csv_viewer_style_file'])) {
			if (!empty($_POST['select_csv_file'])) {
				//Alle divs tonen, behalve de oorspronkelijke
				$this->cwsReturnAllDivs('cwsGetDivFileList');
				//De class haalt de huidige instellingen op
				$csv_settings = new CwsCsvSettings();
				$this->csv_template_class->cwsGetDivFileStyle($csv_settings->cwsGetFileProperties($_POST['select_csv_file']));
			}else{
				//Wanneer er geen bestand gekozen is, alle divs laten zien die in de functie staan
				$this->cwsReturnAllDivs();
			}
		}
		
		if (isset($_POST['csv_save_settings'])) {
			if ((isset($_POST['csv_name'])) && (isset($_POST['csv_header'])) && (isset($_POST['csv_delimitor']))
		 	&& (isset($_POST['csv_imagecolum'])) && (isset($_POST['csv_extension'])) && (isset($_POST['csv_id']))){
				$csv_settings = new CwsCsvSettings();
				if (($csv_settings->cwsSetCsvId($_POST['csv_id'])) !== false){
					if (($csv_settings->cwsSetCsvName($_POST['csv_name'], $_POST['csv_extension'])) !== false){
						if (($csv_settings->cwsSetCsvHeader($_POST['csv_header'])) !== false){
							if (($csv_settings->cwsSetCsvDelimitor($_POST['csv_delimitor'])) !== false){
								$csv_settings->cwsSetCsvImagecolum($_POST['csv_imagecolum']);
							}
						}
					}
				}				
				$this->cwsReturnAllDivs('cwsGetDivFileList', $csv_settings->cwsGetMessage());
			}
		}
		
		if (isset($_POST['csv_save_style'])){
			$csv_style = new CwsCsvStyle;
			$csv_style->cwsSetStyle($_POST);
			$this->cwsReturnAllDivs('cwsGetDivFileList', $csv_style->cwsGetMessage());
		}
		
		if (isset($_POST['csv_viewer_remove_file'])){
			if (!empty($_POST['select_csv_file'])){
				$csv_viewer = new CwsCsvViewer();
				$csv_viewer->cwsRemoveCsvFile($_POST['select_csv_file']);
				$this->cwsReturnAllDivs('cwsGetDivFileList', $csv_viewer->cwsGetMessage());
			}
		}
		
		if (isset($_POST['csv_viewer_preview'])){
			if (!empty($_POST['select_csv_file'])){
				echo $this->cwsReturnDocument($_POST['select_csv_file']);
			}
		}
		
		if (isset($_POST['csv_viewer_upload_images'])){
			if (!empty($_POST['select_csv_file'])){
				$this->cwsReturnAllDivs();
				$this->csv_template_class->cwsGetDivImageUpload($_POST['select_csv_file']);
			}
		}
		
		if (isset($_POST['csv_send_uploads'])){
			$csv_image_upload = new CwsCsvImageUploader($_POST['csv_filename'], $_FILES['csv_image_uploads']);
			$this->cwsReturnAllDivs();
			$csv_viewer = new CwsCsvViewer();
			$this->csv_template_class->cwsGetDivImageViewer($_POST['csv_filename'], $csv_viewer->cwsGetImages($_POST['csv_filename']));
		}
		
		if (isset($_POST['csv_viewer_images_viewer'])){
			if (!empty($_POST['select_csv_file'])){
				$this->cwsReturnAllDivs();
				$csv_viewer = new CwsCsvViewer();
				$this->csv_template_class->cwsGetDivImageViewer($_POST['select_csv_file'], $csv_viewer->cwsGetImages($_POST['select_csv_file']));
			}
		}
		
		if (isset($_POST['csv_remove_images'])){
			$csv_viewer = new CwsCsvViewer();
			$csv_viewer->cwsRemoveImages($_POST['csv_filename'], $_POST['csv_select_image']);
			$this->cwsReturnAllDivs();
			$this->csv_template_class->cwsGetDivImageViewer($_POST['csv_filename'], $csv_viewer->cwsGetImages($_POST['csv_filename']));
		}
	}
	
	function cwsReturnDocument($filename) {
		$csv_viewer = new CwsCsvViewer();
		$data = $csv_viewer->cwsGetCsvFile($filename);
		return $this->csv_template_class->cwsGetDivDocument($data);
	}
	
	function cwsReturnAllDivs($sourcediv = '', $message = '') {
		echo $this->csv_template_class->cwsGetTitle();
		echo ($sourcediv == 'cwsGetDivUpload')?$this->csv_template_class->cwsGetDivUpload($message):$this->csv_template_class->cwsGetDivUpload();
		echo ($sourcediv == 'cwsGetDivFileList')?$this->csv_template_class->cwsGetDivFileList($message):$this->csv_template_class->cwsGetDivFileList();
		echo $this->csv_template_class->cwsGetEnd();
	}
	
	function cwsReturnUninstallButton(){
		echo $this->csv_template_class->cwsGetUninstall();
	}
}