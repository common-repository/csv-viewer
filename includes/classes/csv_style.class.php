<?php
class CwsCsvStyle extends CwsCsvViewer {
	private $style_changed;
	
	function __construct() {
		parent::__construct();
		$this->style_changed = false;
	}
	
	function cwsSetStyle($values){
		global $wpdb;
		
		$results = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix.CSV_TABLENAME."` WHERE `id` = '".$values['csv_id']."' ");
		
		$oldstyle = array();
		$style = array();
		$query = 'UPDATE `'.$wpdb->prefix.CSV_TABLENAME.'` SET ';
		foreach ($results[0] as $key => $value) {
			if (array_key_exists($key, $values)) {
				$oldstyle[$key] = $value;
				$style[$key] = $values[$key];
				$query .= " `".$key."` = '".$values[$key]."',";
			}
		}
		
		$query = substr($query, 0, -1);
		$query .= ' WHERE `id` = '.$values['csv_id'];		

		if (($update = $wpdb->query("$query")) === false){
			$this->message = __("The data can not be imported in the database", "csv_trans");
			return false;
		}
		
		$this->cwsCreateNewStyleFile($results[0]->name, $results[0]->id, $style, $oldstyle);
		if ($this->style_changed === true){
			$this->message = __("The css-file has been changed succesfully", "csv_trans");
			return true;
		}
		return false;
	}
	
	function cwsCreateNewStyleFile($name, $id, $style, $oldstyle = ''){
		$name = substr($name, 0, -4);
		$css = array();
		$csscontent = '';
		foreach ($style as $key => $value){
			//Get tags
			//Voorbeeld: $key = css_table_font_family, $value = Arial
			$str = strstr($key, '_');					//str = _table_font_family
			$str1 = substr($str, 1, 100);				//str1 = table_font_family
			$str2 = explode("_", $str1);				//str2 = array(table, font, family);
			$str2 = $str2[0];							//str2 = table;
			$str3 = str_replace('-', '.', $str2);		//str3 = table, bij tr-even, str3 = tr.even
			//Get styles
			$style = strstr($str1, '_');				//style = _font_family
			$style1 = substr($style, 1, 100); 			//style1 = font_family
			$style2 = str_replace('_', '-', $style1);	//style2 = font-family
			$css[$str3][$style2] = $value;				//css = $css[table][font-family] = Arial
		}
		foreach ($css as $key => $value){
			//key = table; value = array(font-family: Arial)
			if ($key == 'th'){$key = 'tr th';}
			if ($key == 'td'){$key = 'tr td';}
			$csscontent .= '#div_'.$id.' '.$key.'{';		//Csscontent = #div_1 table{
			foreach ($value as $styletype => $waarde){
				//Styletype = font-family, Waarde = Arial
				$csscontent .= $styletype.': '.$waarde.'; ';//Csscontent .= font-family: Arial; 
			}
			$csscontent .= '} ';	//Csscontent .= } 
		}
		//Csscontent = #div_1 table{font-family: Arial;} 
		
		if (!empty($oldstyle)){
			$oldcss = array();
			$oldcsscontent = '';
			foreach ($oldstyle as $key => $value){
				//Get tags
				$str = strstr($key, '_');
				$str1 = substr($str, 1, 100);
				$str2 = explode("_", $str1);
				$str2 = $str2[0];
				$str3 = str_replace('-', '.', $str2);
				//Get styles
				$style = strstr($str1, '_');
				$style1 = substr($style, 1, 100);
				$style2 = str_replace('_', '-', $style1);
				$oldcss[$str3][$style2] = $value;
			}
			foreach ($oldcss as $key => $value){
				if ($key == 'th'){$key = 'tr th';}
				if ($key == 'td'){$key = 'tr td';}
				$oldcsscontent .= '#div_'.$id.' '.$key.'{';
				foreach ($value as $styletype => $waarde){
					$oldcsscontent .= $styletype.': '.$waarde.'; ';
				}
				$oldcsscontent .= '} ';
			}
		}
		
		$dir = CSV_PLUGIN_BASEDIR.'css'.DIRECTORY_SEPARATOR;
		$cssfile = $dir.'style.css';
		if (($handle = fopen($cssfile, 'r')) !== false){
			$fread = fread($handle, 100000);
			if (!empty($oldstyle)){
				$change = str_replace($oldcsscontent, $csscontent, $fread);
			}else{
				$change = $fread;
				$change .= $csscontent;
			}
		}
		fclose($handle);
		if (($handle2 = fopen($cssfile, 'w')) !== false){
			$fwrite = fwrite($handle2, $change);
			if ($fwrite === false){
				$this->message = __("The css-file can not be changed", "csv_trans");
				return false;
			}else{
				$this->message = __("The css-file has been changed succesfully", "csv_trans");
				$this->style_changed = true;
			}
		}
		fclose($handle2);
				
		return false;
		return true;
	}
	
	function cwsRemoveStyle($data){
		$id = $data['id'];
		unset($data['id']);
		
		$name = substr($name, 0, -4);
		$css = array();
		$csscontent = '';
		foreach ($data as $key => $value){
			//Get tags
			$str = strstr($key, '_');
			$str1 = substr($str, 1, 100);
			$str2 = explode("_", $str1);
			$str2 = $str2[0];
			$str3 = str_replace('-', '.', $str2);
			//Get styles
			$style = strstr($str1, '_');
			$style1 = substr($style, 1, 100);
			$style2 = str_replace('_', '-', $style1);
			$css[$str3][$style2] = $value;
		}
		foreach ($css as $key => $value){
			if ($key == 'th'){$key = 'tr th';}
			if ($key == 'td'){$key = 'tr td';}
			$csscontent .= '#div_'.$id.' '.$key.'{';
			foreach ($value as $styletype => $waarde){
				$csscontent .= $styletype.': '.$waarde.'; ';
			}
			$csscontent .= '} ';
		}
		
		$dir = CSV_PLUGIN_BASEDIR.'css'.DIRECTORY_SEPARATOR;
		$cssfile = $dir.'style.css';
		if (($handle = fopen($cssfile, 'r')) !== false){
			$fread = fread($handle, filesize($cssfile));
			$change = str_replace($csscontent, '', $fread);
		}
		fclose($handle);
		if (($handle2 = fopen($cssfile, 'w')) !== false){
			$fwrite = fwrite($handle2, $change);
			if ($fwrite === false){
				$this->message = __("The css-file can not be changed", "csv_trans");
				return false;
			}else{
				$this->message = __("The css-file has been changed succesfully", "csv_trans");
				$this->style_changed = true;
			}
		}
		fclose($handle2);
	}
}