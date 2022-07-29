<?php

namespace OTP\Helper;

use OTP\Traits\Instance;
if(! defined( 'ABSPATH' )) exit;

class MoOffer
{

use Instance;

	public static function showOfferPricing($priceDiv,$newPriceArray,$festivalName){
		$style = MoOffer::provideStyle();
		$script = MoOffer::provideScript($priceDiv,$newPriceArray,$festivalName);
				
				return $style . $script;
	}

	public static function provideLoaderContent($festivalName){
		$loaderHtml = '<div id="mo_loader_div"><div class="mo_loading"></div><div class="content"><label id="mo_offer_label" style="min-width:max-content">'.$festivalName.' </label>&nbsp;<img id="mo_offer_icon" alt="O" src="'.MOV_ICON_GIF.'"><label id="mo_offer_label">ffers</label>&nbsp;<label id="mo_offer_label">Loading&#8230;</label></div></div>';
		return $loaderHtml;
	}

	public static function provideScript($priceDiv,$newPriceArray){
		$scriptStartTag = '<script>';
		$loaderScript = 'document.onreadystatechange = function() { 
				    		if (document.readyState !== "complete") { 
				        		document.querySelector("#mo_loader_div").style.visibility = "visible"; 
				    		} else { 
				    			setTimeout(function(){
				    				document.querySelector("#mo_loader_div").style.display = "none";
				    				document.getElementById("mo_otp_plans_pricing_table").scrollIntoView()
				    		},2000)
				    		} 
						};';
		$priceSlashingScript = 'jQuery(document).ready(function () {
								var index = 0;
								var newPriceArray = '. json_encode($newPriceArray) .';
								jQuery("'.$priceDiv.'").each(function(){
								var price = jQuery(this).text();
								// jQuery(this).empty();
								// jQuery("<b>"+price+"&nbsp;</b>").insertAfter(this);
								jQuery(this).append("&nbsp;<a title=\"Upcoming Price\"><b style=\"color:#505050;font-size:35px\" class=\"mo_strikethrough\" id=\"mo_new_price\">"+ newPriceArray[index++]+"</b></a>");
								});
						}); ';

		$scriptEndTag = '</script>';
		
				return $scriptStartTag . $priceSlashingScript . $scriptEndTag;

		}

	public static function provideStyle(){
			$style = '<style>
					.mo_strikethrough{position:relative}.mo_strikethrough:before{position:absolute;content:"";left:0;top:50%;right:0;border-top:5px solid;border-color:inherit;color:#505050;-webkit-transform:rotate(-5deg);-moz-transform:rotate(-5deg);-ms-transform:rotate(-5deg);-o-transform:rotate(-5deg);transform:rotate(-15deg)}.mo-pricing-slashed{text-decoration:line-through;color:#000}#mo_offer_label{font-size:21px}#mo_offer_icon{height:100%;width:100%}.mo_loading{position:fixed;z-index:999;height:2em;width:2em;overflow:show;margin:auto;}.content{display:inline-flex;position:fixed;z-index:999;height:2em;width:2em;overflow:show;margin:auto;top:0;left:0;bottom:0;right:0}.mo_loading:before{content:"";display:block;position:fixed;top:0;left:inherit;width:100%;height:100%;background:radial-gradient(rgba(20,20,20,.8),rgba(0,0,0,.8));background:-webkit-radial-gradient(rgb(255 253 253 / 1000%),rgb(236 236 236 / 100%))}
					</style>';
			return $style;
		}

}