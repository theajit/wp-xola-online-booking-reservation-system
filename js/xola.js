(function() {
	tinymce.create('tinymce.plugins.Xola', {
		init : function(ed, url) {
			ed.addButton('xola', {
				title : 'Xola Buttons',
				image : url+'/xola.png',
				onclick : function() {
				var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
						W = W - 80;
						H = H - 84;
						tb_show( 'Xola Listings', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=test_edit' );
						jQuery('#TB_window').css({'overflow-y':'scroll','overflow-x':'hidden'});
						jQuery('#TB_title').css({'color':'#fff','background':'#000','font-weight':'bold','padding':'10px 0'});
				}
			});
		},
		createControl : function(n, cm) {
			return null;
		},
		getInfo : function() {
			return {
				longname : "Xola Buttons",
				author : 'Xola',
				authorurl : 'http://www.xola.com/',
				infourl : 'http://www.xola.com/',
				version : "2.0"
			};
		}
	});
	tinymce.PluginManager.add('xola', tinymce.plugins.Xola);
	jQuery(function(){
		jQuery('#image_xola_popup').find('li').each(function(){
			jQuery(this).click(function(){
			  var xola_shortcode = '';
			   xola_shortcode += '[xola-button';
				var id = jQuery(this).attr('id');
				xola_shortcode += '  id="' + id + '"';
				xola_shortcode += ']';
				tinyMCE.activeEditor.execCommand('mceInsertContent', 0, xola_shortcode);
				tb_remove();
			});
				});
					});
	

})();