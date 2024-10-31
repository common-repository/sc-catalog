jQuery(document).ready(function() {
	var cat_image_choose = false;
	default_send_to_editor = window.send_to_editor;
	
	jQuery('#upload_image_button').click(function() {
		cat_image_choose = true;
		tb_show('','media-upload.php?type=image&TB_iframe=true');
		return false;
	});
	// send url back to plugin editor
	window.send_to_editor = function(html) {
		if (cat_image_choose) {
			var imgurl = jQuery('img',html).attr('src');
			jQuery('#upload_image').val(imgurl);
			jQuery('#item_image').attr('src', imgurl);
			tb_remove();
			cat_image_choose = false;
		} else {
			default_send_to_editor(html);
		}
	}
});
