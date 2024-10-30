<?php
class CwsConfigPageTemplates {
	private $csv_uploader_message	= '';
	
	function cwsGetTitle() {
		$div_title = 	'<div class="wrap">';
		$div_title .= 		'<div id="icon-options-general" class="icon32"><br /></div>';
		$div_title .= 		'<h2>'.__("Settings", "csv_trans").' | Csv Viewer</h2>';
				
		echo $div_title;
	}
	
	function cwsGetDivUpload($message = '') {
		$div_upload_files  = '<div class="option_page" id="div_upload_file">';
		$div_upload_files .= 	'<h3>'.__("Upload your CSV-file", "csv_trans").'</h3>';
		$div_upload_files .= 	'<form action="" class="form1" method="POST" enctype="multipart/form-data">';
		$div_upload_files .= 		'<input type="file" name="upload_csv_file" /><br />';
		$div_upload_files .= 		'<input type="submit" class="button-primary" name="csv_viewer_upload_file" value="'.__('Upload').'" />';
		$div_upload_files .= 	'</form>';
		$div_upload_files .=	(!empty($message))?'<span class="errormessage">'.$message.'</span>':'';
		$div_upload_files .= '</div>';
		
		echo $div_upload_files;
	}
	
	function cwsGetDivFileList($message = '') {
		$csv_file_list = new CwsCsvFileList();
		$obj = $csv_file_list->cwsGetFileList();
		if (!$obj){
			return false;
		}
		
		$div_file_list 	= 	'<div class="option_page" id="div_file_list">';
		$div_file_list .= 		'<h3>'.__("Choose your CSV-file", "csv_trans").'</h3>';
		$div_file_list .= 		'<form action="" class="form2" method="POST">';
		$div_file_list .= 			'<select name="select_csv_file">';
		foreach($obj as $file){
			$div_file_list .=			'<option value="'.$file.'">'.$file.'</option>';
		}
		$div_file_list .= 			'</select>';
		$div_file_list .= 			'<input type="submit" class="button-primary" name="csv_viewer_settings_file" value="'.__("Settings", "csv_trans").'" />';
		$div_file_list .= 			'<input type="submit" class="button-primary" name="csv_viewer_style_file" value="'.__("Style", "csv_trans").'" />';
		$div_file_list .= 			'<input type="submit" class="button-primary" name="csv_viewer_preview" value="'.__("Preview", "csv_trans").'" />';
		$div_file_list .=			'<input type="hidden" name="csv_remove_trans" value="'.__('Are you sure to remove ', 'csv_trans').'" />';
		$div_file_list .= 			'<input type="submit" class="button-primary" name="csv_viewer_remove_file" value="'.__("Remove", "csv_trans").'" />';
		$div_file_list .=			'<input type="submit" class="button-primary" name="csv_viewer_upload_images" value="'.__("Upload images", "csv_trans").'" />';
		$div_file_list .=			'<input type="submit" class="button-primary" name="csv_viewer_images_viewer" value="'.__("Show images", "csv_trans").'" />';
		$div_file_list .= 		'</form>';
		$div_file_list .=		(!empty($message))?'<span class="message">'.$message.'</span>':'';
		$div_file_list .= 	'</div>';
		
		echo $div_file_list;
	}

	function cwsGetDivDocument($data) {
		$div_id = 'div_'.$data['id'];
		$name = substr($data['filename'], 0, -4);
		$imgdir = get_option('siteurl').'/wp-content/uploads/'.CSV_UPLOAD_DIR.'/'.$name.'/';
		if ($data === false){
			return false;
		}
		$div_file_preview  =					'<div class="default_csv_document" id="'.$div_id.'">';
		$div_file_preview .=						'<table>';
		if ($data['header'] == 1){
			$div_file_preview .=						'<tr>';
			foreach ($data['headerdata'] as $headerfield){
				$div_file_preview .=						'<th>'.$headerfield.'</th>';
			}
			$div_file_preview .=						'</tr>';
		}
		for ($i=0, $row = count($data['contentdata']); $i<$row; $i++){
			if ($i%2==0){
				$div_file_preview .=						'<tr class="even">';
				for ($j=0, $field=count($data['contentdata'][$i]); $j<$field; $j++){
					if (($j == ($data['imagecolum'] - 1)) && (!empty($data['contentdata'][$i][$j])) && (file_exists(CSV_UPLOAD_PATH.$name.DIRECTORY_SEPARATOR.$data['contentdata'][$i][$j]))){
						$div_file_preview .=					'<td><a href="'.$imgdir.$data['contentdata'][$i][$j].'" rel="csv_images"><img src="'.$imgdir.$data['contentdata'][$i][$j].'" width="50px" /></a></td>';
					}else{
						$div_file_preview .=					'<td>'.$data['contentdata'][$i][$j].'</td>';
					}
				}
				$div_file_preview .=						'</tr>';
			}else{
				$div_file_preview .=						'<tr class="oneven">';
				for ($j=0, $field=count($data['contentdata'][$i]); $j<$field; $j++){
					if (($j == ($data['imagecolum'] - 1)) && (!empty($data['contentdata'][$i][$j])) && (file_exists(CSV_UPLOAD_PATH.$name.DIRECTORY_SEPARATOR.$data['contentdata'][$i][$j]))){
						$div_file_preview .=					'<td><a href="'.$imgdir.$data['contentdata'][$i][$j].'" rel="csv_images"><img src="'.$imgdir.$data['contentdata'][$i][$j].'" width="50px" /></a></td>';
					}else{
						$div_file_preview .=					'<td>'.$data['contentdata'][$i][$j].'</td>';
					}
				}
				$div_file_preview .=						'</tr>';
			}
		}	$div_file_preview .=						'</table>';
		$div_file_preview .=					'</div>';
		
		return $div_file_preview;
	}
	
	function cwsGetDivFileSettings($settings, $message = '') {
		$allowed_delimitors = array(';');
		
		if ($settings['header'] == 1){
			$header0 = '';
			$header1 = 'checked="checked"';
		}elseif ($settings['header'] == 0){
			$header1 = '';
			$header0 = 'checked="checked"';
		}
		
		$filename = explode('.', $settings['name']);
		$name = $filename[0];
		$ext = $filename[1];
		
		$div_file_settings =	'<div class="option_page" id="div_file_settings">';
		$div_file_settings .=		'<h3>'.__("Change the settings of ", "csv_trans").$settings['name'].'</h3>';
		$div_file_settings .=		'<form action="" method="POST">';
		$div_file_settings .=			'<ul>';
		$div_file_settings .=				'<li>'.__("Id", "csv_trans").': '.$settings['id'].'</li>';
		$div_file_settings .=				'<li>'.__("Name", "csv_trans").': <input type="text" name="csv_name" value="'.$name.'" /> .'.$ext.'</li>';
		$div_file_settings .=				'<li>'.__("Header included", "csv_trans").': ';
		$div_file_settings .=					'<input type="radio" name="csv_header" value="1" '.$header1.'" /> '.__("Yes", "csv_trans").' ';
		$div_file_settings .=					'<input type="radio" name="csv_header" value="0" '.$header0.'" /> '.__("No", "csv_trans").' ';
		$div_file_settings .=				'</li>';
		$div_file_settings .=				'<li>';
		$div_file_settings .=					''.__("Delimitor", "csv_trans").': <select name="csv_delimitor">';
		foreach($allowed_delimitors as $delimitor){
			$div_file_settings .=					'<option value="'.$delimitor.'">'.$delimitor.'</option>';
		}
		$div_file_settings .=					'</select>';
		$div_file_settings .=				'</li>';
		$div_file_settings .=				'<li>'.__("Imagecolum", "csv_trans").': <input type="text" name="csv_imagecolum" value='.$settings['imagecolum'].' /></li>';
		$div_file_settings .=				'<input type="hidden" name="csv_extension" value="'.$ext.'" />';
		$div_file_settings .=				'<input type="hidden" name="csv_id" value="'.$settings['id'].'" />';
		$div_file_settings .=				'<input type="submit" class="button-primary" name="csv_save_settings" value="'.__("Save", "csv_trans").'" />';
		$div_file_settings .=			'</ul>';
		$div_file_settings .=		'</form>';
		$div_file_settings .=	'</div>';
		
		echo $div_file_settings;
	}
	
	function cwsGetDivFileStyle($style) {
		$div_file_style =	'<div class="option_page" id="div_file_style">';
		$div_file_style .=		'<h3>'.__("Change the style of ", "csv_trans").$style['name'].'</h3>';
		$div_file_style .=		'<form action="" method="POST">';
		$div_file_style .=			'<ul>';
		$div_file_style .=				'<li><strong>'.__("Table", "csv_trans").'</strong</li>';
		$div_file_style .=				'<li>'.__("Fonttype", "csv_trans").': <input type="text" name="css_table_font_family" value="'.$style['css_table_font_family'].'" /></li>';
		$div_file_style .=				'<li>'.__("Fontsize", "csv_trans").': <input type="text" name="css_table_font_size" value="'.$style['css_table_font_size'].'" /></li>';
		$div_file_style .=				'<li>'.__("Margin", "csv_trans").': <input type="text" name="css_table_margin" value="'.$style['css_table_margin'].'" /></li>';
		$div_file_style .=				'<li>'.__("Padding", "csv_trans").': <input type="text" name="css_table_padding" value="'.$style['css_table_padding'].'" /></li>';
		$div_file_style .=				'<li><strong>'.__("Tableheader", "csv_trans").'</strong</li>';
		$div_file_style .=				'<li>'.__("Backgroundcolor", "csv_trans").': <input type="text" name="css_th_background_color" value="'.$style['css_th_background_color'].'" /></li>';
		$div_file_style .=				'<li>'.__("Fontcolor", "csv_trans").': <input type="text" name="css_th_color" value="'.$style['css_th_color'].'" /></li>';
		$div_file_style .=				'<li><strong>'.__("Even rows", "csv_trans").'</strong</li>';
		$div_file_style .=				'<li>'.__("Backgroundcolor", "csv_trans").': <input type="text" name="css_tr-even_background_color" value="'.$style['css_tr-even_background_color'].'" /></li>';
		$div_file_style .=				'<li>'.__("Fontcolor", "csv_trans").': <input type="text" name="css_tr-even_color" value="'.$style['css_tr-even_color'].'" /></li>';
		$div_file_style .=				'<li><strong>'.__("Odd rows", "csv_trans").'</strong</li>';
		$div_file_style .=				'<li>'.__("Backgroundcolor", "csv_trans").': <input type="text" name="css_tr-oneven_background_color" value="'.$style['css_tr-oneven_background_color'].'" /></li>';
		$div_file_style .=				'<li>'.__("Fontcolor", "csv_trans").': <input type="text" name="css_tr-oneven_color" value="'.$style['css_tr-oneven_color'].'" /></li>';
		$div_file_style .=				'<li><strong>'.__("Fields", "csv_trans").'</strong</li>';
		$div_file_style .=				'<li>'.__("Margin", "csv_trans").': <input type="text" name="css_td_margin" value="'.$style['css_td_margin'].'" /></li>';
		$div_file_style .=				'<li>'.__("Padding", "csv_trans").': <input type="text" name="css_td_padding" value="'.$style['css_td_padding'].'" /></li>';
		$div_file_style .=				'<input type="hidden" name="csv_id" value="'.$style['id'].'" />';
		$div_file_style .=				'<input type="submit" class="button-primary" name="csv_save_style" value="'.__("Save", "csv_trans").'" />';
		$div_file_style .=			'</ul>';
		$div_file_style .=		'</form>';
		$div_file_style .=	'</div>';
		
		echo $div_file_style;
	}
	
	function cwsGetDivImageUpload($name) {
		$div_image_upload =		'<div class="option_page" id="div_image_upload">';
		$div_image_upload .=		'<h3>'.__("Upload your images for ", "csv_trans").$name.'</h3>';
		$div_image_upload .=		'<form action="" method="POST" enctype="multipart/form-data">';
		$div_image_upload .=			'<input type="file" name="csv_image_uploads[]" class="multi" accept="gif|jpg|png" />';
		$div_image_upload .=			'<input type="hidden" name="csv_filename" value="'.$name.'" />';
		$div_image_upload .=			'<input type="submit" class="button-primary" name="csv_send_uploads" value="'.__("Upload images", "csv_trans").'" />';
		$div_image_upload .=		'</form>';
		$div_image_upload .=	'</div>';
		
		echo $div_image_upload;
	}
	
	function cwsGetDivImageViewer($name, $images) {
		$dir = get_option('siteurl').'/wp-content/uploads/'.CSV_UPLOAD_DIR.'/'.substr($name, 0, -4).'/';
		$div_image_viewer =		'<div class="option_page" id="div_image_viewer">';
		$div_image_viewer .=		'<h3>'.__("Images of ", "csv_trans").$name.'</h3>';
		if (!empty($images)){
			$div_image_viewer .=	'<form action="" method="POST">';
			$div_image_viewer .=		'<ul>';
			foreach ($images as $image){
				if ($image !== 'Thumbs.db'){
					$div_image_viewer .=	'<li><input type="checkbox" name="csv_select_image[]" value="'.$image.'" /> <span class="imagename">'.$image.'</span><center><a href="'.$dir.$image.'" rel="csv_images"><img src="'.$dir.$image.'" width="50px" /></a></center></li>';
				}
			}
			$div_image_viewer .=		'</ul>';
			$div_image_viewer .=		'<input type="hidden" name="csv_filename" value="'.$name.'" />';
			$div_image_viewer .=		'<input type="submit" class="button-primary" name="csv_remove_images" value="'.__("Remove selection", "csv_trans").'" />';
			$div_image_viewer .=	'</form';
		}else{
			$div_image_viewer .=	__("No images found", "csv_trans");
		}
		$div_image_viewer .=	'</div>';
		
		echo $div_image_viewer;
	}
	
	function cwsGetUninstall(){
		echo '<br /><br /><br /><br />';
		echo '<form method="POST">';
		echo 	'<input type="hidden" name="csv_uninstall_trans" value="'.__('Are you sure to remove the plugin? At first you have to remove all the CSV-files and images', 'csv_trans').'" />';
		echo 	'<input type="submit" name="csv_uninstall" value="'.__("Remove plugindata", "csv_trans").'" />';
		echo '</form>';
	}
	
	function cwsGetEnd() {
		$div_end = '</div>';
		
		echo $div_end;
	}
}