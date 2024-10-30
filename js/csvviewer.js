jQuery("a[rel='csv_images']").colorbox({maxWidth: '750'});

jQuery('input[name=csv_uninstall]').click(function(){
	var confirmation = confirm(jQuery('input[name=csv_uninstall_trans]').val());
	if (confirmation == false){
		return false;
	}
});

jQuery('input[name=csv_viewer_remove_file]').click(function(){
	var confirmation2 = confirm(jQuery('input[name=csv_remove_trans]').val()+jQuery('select[name=select_csv_file]').val());
	if (confirmation2 == false){
		return false;
	}
});