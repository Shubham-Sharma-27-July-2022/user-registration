/* OTP login */
jQuery(document).ready(function(){
	    jQuery(".otpl-tab").hide();
		jQuery("#div-otpl-general").show();
	    jQuery(".otpl-tab-links").click(function(){
		var divid=jQuery(this).attr("id");
		jQuery(".otpl-tab-links").removeClass("active");
		jQuery(".otpl-tab").hide();
		jQuery("#"+divid).addClass("active");
		jQuery("#div-"+divid).fadeIn();
		});
});
