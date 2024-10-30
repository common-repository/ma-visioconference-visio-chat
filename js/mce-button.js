/*
Plugin Name: Ma-Visioconference
Plugin URI: http://www.ma-visioconference.com
Description:  Ma-Visioconference for wordpress.
Version: 1.0.0.0
Author: DiVA-Cloud
Author URI: http://www.diva-cloud.com
*/
(function() {
    tinymce.PluginManager.add('ma_visioconference_mce_button', function( editor, url ) {
	    editor.addButton('ma_visioconference_mce_button_link', {
			image: tinymceMaVisioconferenceIconLink,
			title: 'Ma-Visioconference Link',
			cmd: 'ma_visioconference_add_link'
		});
	    editor.addButton('ma_visioconference_mce_button_iframe', {
			image: tinymceMaVisioconferenceIconIFrame,
			title: 'Ma-Visioconference IFrame',
			cmd: 'ma_visioconference_add_iframe'
		});
	    editor.addCommand('ma_visioconference_add_link', function() {
		    var selected_text = editor.selection.getContent();
		    var return_text = '';
		    return_text = '[Ma-Visioconference-Link text="' + selected_text + '"]';
		    editor.execCommand('mceInsertContent', 0, return_text);
		});
	    editor.addCommand('ma_visioconference_add_iframe', function() {
		    var selected_text = editor.selection.getContent();
		    var return_text = '';
		    return_text = selected_text+'[Ma-Visioconference-IFrame border="0" width="100%" height="1200px"]';
		    editor.execCommand('mceInsertContent', 0, return_text);
		});
	});
})();
