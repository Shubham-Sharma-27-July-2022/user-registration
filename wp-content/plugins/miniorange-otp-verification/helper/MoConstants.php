<?php

namespace OTP\Helper;

if(! defined( 'ABSPATH' )) exit;


class MoConstants
{
	const COUNTRY_BLOCKED_ERROR = "COUNTRY_BLOCKED_ERROR";
    const HOSTNAME				= MOV_HOST;
    const DEFAULT_CUSTOMER_KEY 	= MOV_DEFAULT_CUSTOMERKEY;
    const DEFAULT_API_KEY 		= MOV_DEFAULT_APIKEY;
    const PCODE 				= "UHJlbWl1bSBQbGFuIC0gV1AgT1RQIFZFUklGSUNBVElPTg==";
	const BCODE 				= "RG8gaXQgWW91cnNlbGYgUGxhbiAtIFdQIE9UUCBWRVJJRklDQVRJT04=";
	const CCODE					= "bWluaU9yYW5nZSBTTVMvU01UUCBHYXRld2F5IC0gV1AgT1RQIFZFUklGSUNBVElPTg==";
	const NCODE                 = "d3Bfb3RwX3ZlcmlmaWNhdGlvbl9iYXNpY19wbGFu";
	const AACODE				= "Q3VzdG9tIEdhdGV3YXkgd2l0aCBBZGRPbnMtIFdQIE9UUCBWZXJpZmljYXRpb24=";
	const AACODE2				= "d3BfZW1haWxfdmVyaWZpY2F0aW9uX2ludHJhbmV0X2Jhc2ljX3BsYW4=";
	const AACODE3				= "WW91ciBHYXRld2F5IC0gV1AgT1RQIFZlcmlmaWNhdGlvbg==";
	const TACODE     			= "d3BfZW1haWxfdmVyaWZpY2F0aW9uX2ludHJhbmV0X3R3aWxpb19iYXNpY19wbGFu";
	const TACODE2				= "d3BfZW1haWxfdmVyaWZpY2F0aW9uX2ludHJhbmV0X3R3aWxpbw==";
	const TACODE3 				= "VHdpbGlvIEdhdGV3YXkgLSBXUCBPVFAgVmVyaWZpY2F0aW9u";
	const NACODE				= "Q3VzdG9tIEdhdGV3YXkgd2l0aG91dCBBZGRPbnMgLSBXUCBPVFAgVmVyaWZpY2F0aW9u";
	const NACODE2				= "d3BfZW1haWxfdmVyaWZpY2F0aW9uX2ludHJhbmV0X3N0YW5kYXJkX3BsYW4=";
	const ECODE 				= "d3BfZW1haWxfdmVyaWZpY2F0aW9uX2ludHJhbmV0X2VudGVycHJpc2VfcGxhbg==";
	const ECODE2                = "RW50ZXJwcmlzZSBHYXRld2F5IC0gV1AgT1RQIFZlcmlmaWNhdGlvbg==";
	const FROM_EMAIL			= "no-reply@xecurify.com";
	const SUPPORT_EMAIL 		= "info@xecurify.com";
	const FEEDBACK_EMAIL 		= "otpsupport@xecurify.com";
	const HEADER_CONTENT_TYPE	= "Content-Type: text/html";
	const SUCCESS				= "SUCCESS";
	const ERROR				    = "ERROR";
	const FAILURE				= "FAILURE";
	const AREA_OF_INTEREST		= "WP OTP Verification Plugin";
	const PLUGIN_TYPE            = MOV_TYPE;
	const PATTERN_PHONE			= '/^[\+]\d{1,4}\d{7,12}$|^[\+]\d{1,4}[\s]\d{7,12}$/';
	const PATTERN_COUNTRY_CODE  = '/^[\+]\d{1,4}.*/';
	const PATTERN_SPACES_HYPEN 	= '/([\(\) \-]+)/';
	const ERROR_JSON_TYPE 		= 'error';
	const SUCCESS_JSON_TYPE 	= 'success';
	const EMAIL_TRANS_REMAINING = 10;
	const PHONE_TRANS_REMAINING = 10;
	const USERPRO_VER_FIELD_META= "verification_form";
	const BUSINESS_FREE_TRIAL 	= "https://www.miniorange.com/businessfreetrial";

        const FAQ_URL               = 'https://faq.miniorange.com/kb/otp-verification/';
    const FAQ_BASE_URL          = 'https://faq.miniorange.com/knowledgebase/';
    const VIEW_TRANSACTIONS     = '/moas/viewtransactions';                     
    const FAQ_PAY_URL           = 'https://faq.miniorange.com/knowledgebase/how-to-make-payment-for-the-otp-verification-plugin';
    const CUSTOM_MESSAGE_ADDON_SUCCESS = "MO_ADDON_MESSAGE_CUSTOM_MESSAGE_SUCCESS";
    const CUSTOM_MESSAGE_ADDON_ERROR = "MO_ADDON_MESSAGE_CUSTOM_MESSAGE_ERROR";
    const MOCOUNTRY  = "India";
}