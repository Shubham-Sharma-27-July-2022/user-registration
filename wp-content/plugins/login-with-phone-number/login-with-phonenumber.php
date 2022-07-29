<?php
/*
Plugin Name: Login with phone number
Plugin URI: http://idehweb.com/login-with-phone-number
Description: Login with phone number - sending sms - activate user by phone number - limit pages to login - register and login with ajax - modal
Version: 1.3.7
Author: Hamid Alinia - idehweb
Author URI: http://idehweb.com
Text Domain: login-with-phone-number
Domain Path: /languages
*/
require 'gateways/class-lwp-twilio-api.php';
require 'gateways/class-lwp-infobip-api.php';
require 'gateways/class-lwp-zenziva-api.php';
require 'gateways/class-lwp-raygansms-api.php';
require 'gateways/class-lwp-smsbharti-api.php';
require 'gateways/class-lwp-mshastra-api.php';
require 'gateways/class-lwp-taqnyat-api.php';

class idehwebLwp
{
    public $textdomain = 'login-with-phone-number';

    function __construct()
    {
        add_action('init', array(&$this, 'idehweb_lwp_textdomain'));
        add_action('admin_init', array(&$this, 'admin_init'));
        add_action('admin_menu', array(&$this, 'admin_menu'));
        add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));
        add_action('wp_ajax_idehweb_lwp_auth_customer', array(&$this, 'idehweb_lwp_auth_customer'));
        add_action('wp_ajax_idehweb_lwp_auth_customer_with_website', array(&$this, 'idehweb_lwp_auth_customer_with_website'));
        add_action('wp_ajax_idehweb_lwp_activate_customer', array(&$this, 'idehweb_lwp_activate_customer'));
        add_action('wp_ajax_idehweb_lwp_check_credit', array(&$this, 'idehweb_lwp_check_credit'));
        add_action('wp_ajax_idehweb_lwp_get_shop', array(&$this, 'idehweb_lwp_get_shop'));
        add_action('wp_ajax_lwp_ajax_login', array(&$this, 'lwp_ajax_login'));
        add_action('wp_ajax_lwp_update_password_action', array(&$this, 'lwp_update_password_action'));
        add_action('wp_ajax_lwp_enter_password_action', array(&$this, 'lwp_enter_password_action'));
        add_action('wp_ajax_lwp_ajax_login_with_email', array(&$this, 'lwp_ajax_login_with_email'));
        add_action('wp_ajax_lwp_ajax_register', array(&$this, 'lwp_ajax_register'));
        add_action('wp_ajax_lwp_forgot_password', array(&$this, 'lwp_forgot_password'));
        add_action('wp_ajax_lwp_verify_domain', array(&$this, 'lwp_verify_domain'));
        add_action('wp_ajax_nopriv_lwp_verify_domain', array(&$this, 'lwp_verify_domain'));
        add_action('wp_ajax_nopriv_lwp_ajax_login', array(&$this, 'lwp_ajax_login'));
        add_action('wp_ajax_nopriv_lwp_ajax_login_with_email', array(&$this, 'lwp_ajax_login_with_email'));
        add_action('wp_ajax_nopriv_lwp_ajax_register', array(&$this, 'lwp_ajax_register'));
        add_action('wp_ajax_nopriv_lwp_update_password_action', array(&$this, 'lwp_update_password_action'));
        add_action('wp_ajax_nopriv_lwp_enter_password_action', array(&$this, 'lwp_enter_password_action'));
        add_action('wp_ajax_nopriv_lwp_forgot_password', array(&$this, 'lwp_forgot_password'));
        add_action('activated_plugin', array(&$this, 'lwp_activation_redirect'));

        add_action('show_user_profile', array(&$this, 'lwp_add_phonenumber_field'));
        add_action('edit_user_profile', array(&$this, 'lwp_add_phonenumber_field'));

        add_action('personal_options_update', array(&$this, 'lwp_update_phonenumber_field'));
        add_action('edit_user_profile_update', array(&$this, 'lwp_update_phonenumber_field'));

        add_action('wp_head', array(&$this, 'lwp_custom_css'));

//        add_action('admin_bar_menu', array(&$this, 'credit_adminbar'), 100);
//        add_action('login_enqueue_scripts', array(&$this, 'admin_custom_css'));


        add_action('rest_api_init', array(&$this, 'lwp_register_rest_route'));
        add_filter('manage_users_columns', array(&$this, 'lwp_modify_user_table'));
        add_filter('manage_users_custom_column', array(&$this, 'lwp_modify_user_table_row'), 10, 3);
        add_filter('manage_users_sortable_columns', array(&$this, 'lwp_make_registered_column_sortable'));
        add_filter('woocommerce_locate_template', array(&$this, 'lwp_addon_woocommerce_login'), 1, 3);


        add_shortcode('idehweb_lwp', array(&$this, 'shortcode'));
        add_shortcode('idehweb_lwp_metas', array(&$this, 'idehweb_lwp_metas'));

    }

    function lwp_add_phonenumber_field($user)
    {
        $phn = get_the_author_meta('phone_number', $user->ID);
        ?>
        <h3><?php esc_html_e('Personal Information', 'crf'); ?></h3>

        <table class="form-table">
            <tr>
                <th><label for="phone_number"><?php esc_html_e('phone_number', $this->textdomain); ?></label></th>
                <td>
                    <input type="text"

                           step="1"
                           id="phone_number"
                           name="phone_number"
                           value="<?php echo esc_attr($phn); ?>"
                           class="regular-text"
                    />

                </td>
            </tr>
        </table>
        <?php
    }

    function lwp_update_phonenumber_field($user_id)
    {
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }

//        if ( ! empty( $_POST['year_of_birth'] ) && intval( $_POST['year_of_birth'] ) >= 1900 ) {
        update_user_meta($user_id, 'phone_number', $_POST['phone_number']);
//        }
    }

    function lwp_activation_redirect($plugin)
    {
        if ($plugin == plugin_basename(__FILE__)) {
            exit(wp_redirect(admin_url('admin.php?page=idehweb-lwp')));
        }
    }

    function idehweb_lwp_textdomain()
    {
        $idehweb_lwp_lang_dir = dirname(plugin_basename(__FILE__)) . '/languages/';
        $idehweb_lwp_lang_dir = apply_filters('idehweb_lwp_languages_directory', $idehweb_lwp_lang_dir);

        load_plugin_textdomain($this->textdomain, false, $idehweb_lwp_lang_dir);


    }

    function admin_init()
    {
        $options = get_option('idehweb_lwp_settings');
//        print_r($options);
        $style_options = get_option('idehweb_lwp_settings_styles');
//        print_r($style_options);

        if (!isset($options['idehweb_token'])) $options['idehweb_token'] = '';
        if (!isset($style_options['idehweb_styles_status'])) $style_options['idehweb_styles_status'] = '1';

        register_setting('idehweb-lwp', 'idehweb_lwp_settings', array(&$this, 'settings_validate'));
        register_setting('idehweb-lwp-styles', 'idehweb_lwp_settings_styles', array(&$this, 'settings_validate'));
        register_setting('idehweb-lwp-localization', 'idehweb_lwp_settings_localization', array(&$this, 'settings_validate'));

        add_settings_section('idehweb-lwp-styles', '', array(&$this, 'section_intro'), 'idehweb-lwp-styles');
        add_settings_section('idehweb-lwp-localization', '', array(&$this, 'section_intro'), 'idehweb-lwp-localization');
        add_settings_field('idehweb_styles_status', __('Enable custom styles', $this->textdomain), array(&$this, 'setting_idehweb_style_enable_custom_style'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);

        if ($style_options['idehweb_styles_status']) {
//            add_settings_field('idehweb_styles_title1', 'tyuiuy', array(&$this, 'section_title'), 'idehweb-lwp-styles');
            add_settings_field('idehweb_styles_title', __('Primary button', $this->textdomain), array(&$this, 'section_title'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_button_background', __('button background color', $this->textdomain), array(&$this, 'setting_idehweb_style_button_background_color'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_button_border_color', __('button border color', $this->textdomain), array(&$this, 'setting_idehweb_style_button_border_color'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_button_border_radius', __('button border radius', $this->textdomain), array(&$this, 'setting_idehweb_style_button_border_radius'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_button_border_width', __('button border width', $this->textdomain), array(&$this, 'setting_idehweb_style_button_border_width'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_button_text_color', __('button text color', $this->textdomain), array(&$this, 'setting_idehweb_style_button_text_color'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);

//            add_settings_section('idehweb_styles_title2', '', array(&$this, 'section_title'), 'idehweb-lwp-styles');
            add_settings_field('idehweb_styles_title2', __('Secondary button', $this->textdomain), array(&$this, 'section_title'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);

            add_settings_field('idehweb_styles_button_background2', __('secondary button background color', $this->textdomain), array(&$this, 'setting_idehweb_style_button_background_color2'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_button_border_color2', __('secondary button border color', $this->textdomain), array(&$this, 'setting_idehweb_style_button_border_color2'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_button_border_radius2', __('secondary button border radius', $this->textdomain), array(&$this, 'setting_idehweb_style_button_border_radius2'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_button_border_width2', __('secondary button border width', $this->textdomain), array(&$this, 'setting_idehweb_style_button_border_width2'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_button_text_color2', __('secondary button text color', $this->textdomain), array(&$this, 'setting_idehweb_style_button_text_color2'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);


            add_settings_field('idehweb_styles_title3', __('Inputs', $this->textdomain), array(&$this, 'section_title'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);

            add_settings_field('idehweb_styles_input_background', __('input background color', $this->textdomain), array(&$this, 'setting_idehweb_style_input_background_color'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_input_border_color', __('input border color', $this->textdomain), array(&$this, 'setting_idehweb_style_input_border_color'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_input_border_radius', __('input border radius', $this->textdomain), array(&$this, 'setting_idehweb_style_input_border_radius'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_input_border_width', __('input border width', $this->textdomain), array(&$this, 'setting_idehweb_style_input_border_width'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_input_text_color', __('input text color', $this->textdomain), array(&$this, 'setting_idehweb_style_input_text_color'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_input_placeholder_color', __('input placeholder color', $this->textdomain), array(&$this, 'setting_idehweb_style_input_placeholder_color'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);

            add_settings_field('idehweb_styles_title4', __('Box', $this->textdomain), array(&$this, 'section_title'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_box_background_color', __('box background color', $this->textdomain), array(&$this, 'setting_idehweb_style_box_background_color'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_position_form', __('Enable fix position', $this->textdomain), array(&$this, 'idehweb_position_form'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel']);


            add_settings_field('idehweb_styles_title5', __('Labels', $this->textdomain), array(&$this, 'section_title'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_labels_text_color', __('label text color', $this->textdomain), array(&$this, 'setting_idehweb_style_labels_text_color'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_labels_font_size', __('label font size', $this->textdomain), array(&$this, 'setting_idehweb_style_labels_font_size'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);


            add_settings_field('idehweb_styles_title6', __('Titles', $this->textdomain), array(&$this, 'section_title'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_title_color', __('title color', $this->textdomain), array(&$this, 'setting_idehweb_style_title_color'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);
            add_settings_field('idehweb_styles_title_font_size', __('title font size', $this->textdomain), array(&$this, 'setting_idehweb_style_title_font_size'), 'idehweb-lwp-styles', 'idehweb-lwp-styles', ['label_for' => '', 'class' => 'ilwplabel']);


        }

        add_settings_section('idehweb-lwp', '', array(&$this, 'section_intro'), 'idehweb-lwp');

        add_settings_field('idehweb_sms_login', __('Enable phone number login', $this->textdomain), array(&$this, 'setting_idehweb_sms_login'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel']);

        $ghgfd = '';
        if ($options['idehweb_token']) {
            $ghgfd = ' none';
        }
//        add_settings_field('idehweb_phone_number_ccode', __('Enter your Country Code', $this->textdomain), array(&$this, 'setting_idehweb_phone_number'), 'idehweb-lwp', 'idehweb-lwp', ['class' => 'ilwplabel lwp_phone_number_label related_to_login' . $ghgfd]);
//        add_settings_field('idehweb_phone_number', __('Enter your phone number', $this->textdomain), array(&$this, 'setting_idehweb_phone_number'), 'idehweb-lwp', 'idehweb-lwp', ['class' => 'ilwplabel lwp_phone_number_label related_to_login' . $ghgfd]);
        add_settings_field('idehweb_website_url', __('Enter your website url', $this->textdomain), array(&$this, 'setting_idehweb_website_url'), 'idehweb-lwp', 'idehweb-lwp', ['class' => 'ilwplabel lwp_website_label related_to_login' . $ghgfd]);
//        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        add_settings_field('idehweb_token', __('Enter api key', $this->textdomain), array(&$this, 'setting_idehweb_token'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel alwaysDisplayNone']);
        add_settings_field('idehweb_country_codes', __('Country code accepted in front', $this->textdomain), array(&$this, 'setting_country_code'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_login']);

        if ($options['idehweb_token']) {

            add_settings_field('idehweb_sms_shop', __('Buy credit here', $this->textdomain), array(&$this, 'setting_buy_credit'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_login rltll']);
        }
        add_settings_field('idehweb_use_custom_gateway', __('use custom sms gateway', $this->textdomain), array(&$this, 'setting_use_custom_gateway'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_login']);
        add_settings_field('idehweb_default_gateways', __('sms default gateway', $this->textdomain), array(&$this, 'setting_default_gateways'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_defaultgateway']);


        add_settings_field('idehweb_twilio_account_sid', __('Twilio account SID', $this->textdomain), array(&$this, 'setting_twilio_account_sid'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_twilio']);
        add_settings_field('idehweb_twilio_auth_token', __('Twilio auth token', $this->textdomain), array(&$this, 'setting_twilio_auth_token'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_twilio']);
        add_settings_field('idehweb_twilio_phone_number', __('Twilio phone number', $this->textdomain), array(&$this, 'setting_twilio_phone_number'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_twilio']);


        add_settings_field('idehweb_zenziva_userkey', __('Zenziva user key', $this->textdomain), array(&$this, 'setting_zenziva_user_key'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_zenziva']);
        add_settings_field('idehweb_zenziva_passkey', __('Zenziva pass key', $this->textdomain), array(&$this, 'setting_zenziva_pass_key'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_zenziva']);

        add_settings_field('idehweb_infobip_user', __('Infobip user', $this->textdomain), array(&$this, 'setting_infobip_user'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_infobip']);
        add_settings_field('idehweb_infobip_password', __('Infobip password', $this->textdomain), array(&$this, 'setting_infobip_password'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_infobip']);
        add_settings_field('idehweb_infobip_sender', __('Infobip sender', $this->textdomain), array(&$this, 'setting_infobip_sender'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_infobip']);


        add_settings_field('idehweb_firebase_api', __('Firebase api', $this->textdomain), array(&$this, 'setting_firebase_api'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_firebase']);
        add_settings_field('idehweb_firebase_config', __('Firebase config', $this->textdomain), array(&$this, 'setting_firebase_config'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_firebase']);


//        add_settings_field('idehweb_raygansms_username', __('Raygansms username', $this->textdomain), array(&$this, 'setting_raygansms_username'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_raygansms']);
//        add_settings_field('idehweb_raygansms_password', __('Raygansms password', $this->textdomain), array(&$this, 'setting_raygansms_password'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_raygansms']);
//        add_settings_field('idehweb_raygansms_phonenumber', __('Raygansms phone number', $this->textdomain), array(&$this, 'setting_raygansms_phonenumber'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_raygansms']);

        add_settings_field('idehweb_smsbharti_api_key', __('Smsbharti api key', $this->textdomain), array(&$this, 'setting_smsbharti_api_key'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_smsbharti']);
        add_settings_field('idehweb_smsbharti_from', __('smsbharti from', $this->textdomain), array(&$this, 'setting_smsbharti_from'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_smsbharti']);
        add_settings_field('idehweb_smsbharti_template_id', __('smsbharti template id', $this->textdomain), array(&$this, 'setting_smsbharti_template_id'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_smsbharti']);
        add_settings_field('idehweb_smsbharti_routeid', __('smsbharti route id', $this->textdomain), array(&$this, 'setting_smsbharti_routeid'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_smsbharti']);


        add_settings_field('idehweb_mshastra_user', __('mshastra user', $this->textdomain), array(&$this, 'setting_mshastra_user'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_mshastra']);
        add_settings_field('idehweb_mshastra_pwd', __('mshastra pwd', $this->textdomain), array(&$this, 'setting_mshastra_pwd'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_mshastra']);
        add_settings_field('idehweb_mshastra_senderid', __('mshastra senderid', $this->textdomain), array(&$this, 'setting_mshastra_senderid'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_mshastra']);


        add_settings_field('idehweb_taqnyat_sendernumber', __('taqnyat sender number', $this->textdomain), array(&$this, 'setting_taqnyat_sender_number'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_taqnyat']);
        add_settings_field('idehweb_taqnyat_api_key', __('taqnyat api key', $this->textdomain), array(&$this, 'setting_taqnyat_api_key'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_taqnyat']);


//        add_settings_field('idehweb_custom_gateway_url', __('sms gateway url', $this->textdomain), array(&$this, 'setting_custom_gateway_url'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_customgateway']);
//        add_settings_field('idehweb_custom_gateway_username', __('sms gateway username', $this->textdomain), array(&$this, 'setting_custom_gateway_username'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_customgateway']);
//        add_settings_field('idehweb_custom_gateway_password', __('sms gateway password', $this->textdomain), array(&$this, 'setting_custom_gateway_password'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_customgateway']);
//        add_settings_field('idehweb_custom_gateway_password', __('sms gateway url', $this->textdomain), array(&$this, 'setting_custom_gateway_url'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_customgateway']);

        //        $display = 'inherit';

//            $display = 'none';

        add_settings_field('idehweb_lwp_space', __('', $this->textdomain), array(&$this, 'setting_idehweb_lwp_space'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel idehweb_lwp_mgt100']);
        add_settings_field('idehweb_email_login', __('Enable email login', $this->textdomain), array(&$this, 'setting_idehweb_email_login'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel']);
        add_settings_field('idehweb_lwp_space2', __('', $this->textdomain), array(&$this, 'setting_idehweb_lwp_space'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel idehweb_lwp_mgt100']);

        add_settings_field('idehweb_user_registration', __('Enable user registration', $this->textdomain), array(&$this, 'setting_idehweb_user_registration'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel']);
        add_settings_field('idehweb_password_login', __('Enable password login', $this->textdomain), array(&$this, 'setting_idehweb_password_login'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel']);
        add_settings_field('idehweb_redirect_url', __('Enter redirect url', $this->textdomain), array(&$this, 'setting_idehweb_url_redirect'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel']);
        add_settings_field('idehweb_use_phone_number_for_username', __('use phone number for username', $this->textdomain), array(&$this, 'idehweb_use_phone_number_for_username'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel']);
        add_settings_field('idehweb_default_username', __('Default username', $this->textdomain), array(&$this, 'setting_default_username'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_upnfu']);
        add_settings_field('idehweb_default_nickname', __('Default nickname', $this->textdomain), array(&$this, 'setting_default_nickname'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_upnfu']);
        add_settings_field('idehweb_enable_timer_on_sending_sms', __('Enable timer', $this->textdomain), array(&$this, 'idehweb_enable_timer_on_sending_sms'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel ']);
        add_settings_field('idehweb_timer_count', __('Timer count', $this->textdomain), array(&$this, 'setting_timer_count'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel related_to_entimer']);
        add_settings_field('idehweb_enable_accept_terms_and_condition', __('Enable accept term & conditions', $this->textdomain), array(&$this, 'idehweb_enable_accept_term_and_conditions'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel ']);
        add_settings_field('idehweb_term_and_conditions_text', __('Text of term & conditions part', $this->textdomain), array(&$this, 'setting_term_and_conditions_text'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel ']);


        add_settings_field('idehweb_lwp_space3', __('', $this->textdomain), array(&$this, 'setting_idehweb_lwp_space'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel idehweb_lwp_mgt100']);
        add_settings_field('instructions', __('Shortcode and Template Tag', $this->textdomain), array(&$this, 'setting_instructions'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel']);
        add_settings_field('idehweb_online_support', __('Enable online support', $this->textdomain), array(&$this, 'idehweb_online_support'), 'idehweb-lwp', 'idehweb-lwp', ['label_for' => '', 'class' => 'ilwplabel']);


        add_settings_field('idehweb_localization_status', __('Enable localization', $this->textdomain), array(&$this, 'setting_idehweb_localization_enable_custom_localization'), 'idehweb-lwp-localization', 'idehweb-lwp-localization', ['label_for' => '', 'class' => 'ilwplabel']);
        add_settings_field('idehweb_localization_title_of_login_form', __('Title of login form (with phone number)', $this->textdomain), array(&$this, 'setting_idehweb_localization_of_login_form'), 'idehweb-lwp-localization', 'idehweb-lwp-localization', ['label_for' => '', 'class' => 'ilwplabel']);
        add_settings_field('idehweb_localization_title_of_login_form1', __('Title of login form (with email)', $this->textdomain), array(&$this, 'setting_idehweb_localization_of_login_form_email'), 'idehweb-lwp-localization', 'idehweb-lwp-localization', ['label_for' => '', 'class' => 'ilwplabel']);
        add_settings_field('idehweb_localization_placeholder_of_phonenumber_field', __('Placeholder of phone number field', $this->textdomain), array(&$this, 'setting_idehweb_localization_placeholder_of_phonenumber_field'), 'idehweb-lwp-localization', 'idehweb-lwp-localization', ['label_for' => '', 'class' => 'ilwplabel']);

        //        }
//        add_settings_section('idehweb-lwp', '', array(&$this, 'section_intro'), 'idehweb-lwp');

    }

    function admin_menu()
    {

        $icon_url = 'dashicons-smartphone';
        $page_hook = add_menu_page(
            __('login setting', $this->textdomain),
            __('login setting', $this->textdomain),
            'manage_options',
            'idehweb-lwp',
            array(&$this, 'settings_page'),
            $icon_url
        );
        add_submenu_page('idehweb-lwp', __('Style settings', $this->textdomain), __('Style Settings', $this->textdomain), 'manage_options', 'idehweb-lwp-styles', array(&$this, 'style_settings_page'));
        add_submenu_page('idehweb-lwp', __('Text & localization', $this->textdomain), __('Text & localization', $this->textdomain), 'manage_options', 'idehweb-lwp-localization', array(&$this, 'localization_settings_page'));

        add_action('admin_print_styles-' . $page_hook, array(&$this, 'admin_custom_css'));
        wp_enqueue_script('idehweb-lwp-admin-select2-js', plugins_url('/scripts/select2.full.min.js', __FILE__), array('jquery'), true, true);
        wp_enqueue_script('idehweb-lwp-admin-chat-js', plugins_url('/scripts/chat.js', __FILE__), array('jquery'), true, true);

    }

    function admin_custom_css()
    {
        wp_enqueue_style('idehweb-lwp-admin', plugins_url('/styles/lwp-admin.css', __FILE__));
        wp_enqueue_style('idehweb-lwp-admin-select2-style', plugins_url('/styles/select2.min.css', __FILE__));


    }

    function settings_page()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        if (!isset($options['idehweb_token'])) $options['idehweb_token'] = '';
        if (!isset($options['idehweb_online_support'])) $options['idehweb_online_support'] = '1';


        ?>
        <div class="wrap">
            <div class="lwp-wrap-left">


                <div id="icon-themes" class="icon32"></div>
                <h2><?php _e('idehwebLwp Settings', $this->textdomain); ?></h2>
                <?php if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {

                    ?>
                    <div id="setting-error-settings_updated" class="updated settings-error">
                        <p><strong><?php _e('Settings saved.', $this->textdomain); ?></strong></p>
                    </div>
                <?php } ?>
                <form action="options.php" method="post" id="iuytfrdghj">
                    <?php settings_fields('idehweb-lwp'); ?>
                    <?php do_settings_sections('idehweb-lwp'); ?>

                    <p class="submit">
                        <span id="wkdugchgwfchevg3r4r"></span>
                    </p>
                    <p class="submit">
                        <span id="oihdfvygehv"></span>
                    </p>
                    <p class="submit">
                        <?php

                        //                    if (!$options['idehweb_phone_number']){
                        //
                        ?>

                        <?php

                        //                    }else{
                        ?>
                        <input type="submit" class="button-primary"
                               value="<?php _e('Save Changes', $this->textdomain); ?>"/></p>
                    <!--            --><?php //}
                    ?>
                    <?php
                    if (empty($options['idehweb_token'])) {
                        ?>
                        <!--                    <div class="lwploadr">-->
                        <!--                        <div class="lwpmainloader">Loading...</div>-->
                        <!--                        <div class="lwpmaintextloader">configuring...</div>-->
                        <!--                    </div>-->

                    <?php } ?>
                </form>
            </div>
            <div class="lwp-wrap-right">
                <a href="https://idehweb.com/product/login-with-phone-number-in-wordpress/" target="_blank">
                    <img src="<?php echo plugins_url('/images/login-with-phone-number-wordpress-buy-pro-version.png', __FILE__) ?>"/>
                </a>
            </div>

            <?php
            if ($options['idehweb_online_support'] == '1') {
                ?>
                <script type="text/javascript">window.makecrispactivate=1;</script>
            <?php } ?>

            <script>
                <?php

                ?>
                jQuery(function ($) {
                    var idehweb_country_codes = $("#idehweb_country_codes");
                    var idehweb_phone_number_ccodeG = '1';
                    $(window).load(function () {

                        $('.loiuyt').click();
                        $('.refreshShop').click();
                        $("#idehweb_phone_number_ccode").select2();
                        idehweb_country_codes.select2();

                        <?php
                        if (empty($options['idehweb_token'])) {
                        ?>
                        $('.authwithwebsite').click();
                        <?php } ?>
                        // $("#idehweb_country_codes").chosen();
                        // if ($('#idehweb_phone_number_ccode').is(':visible'))
                        //     $("#idehweb_phone_number_ccode").chosen();

                    });

                    var edf = $('#idehweb_lwp_settings_idehweb_sms_login');
                    var edf2 = $('#idehweb_lwp_settings_use_phone_number_for_username');
                    var edf3 = $('#idehweb_lwp_settings_use_custom_gateway');
                    var edf4 = $('#idehweb_default_gateways');
                    var edf5 = $('#idehweb_lwp_settings_enable_timer_on_sending_sms');
                    var idehweb_body = $('body');
                    var related_to_login = $('.related_to_login');
                    var related_to_upnfu = $('.related_to_upnfu');
                    var related_to_entimer = $('.related_to_entimer');
                    var related_to_defaultgateway = $('.related_to_defaultgateway');
                    var related_to_customgateway = $('.related_to_customgateway');
                    var related_to_twilio = $('.related_to_twilio');
                    var related_to_zenziva = $('.related_to_zenziva');
                    var related_to_infobip = $('.related_to_infobip');
                    var related_to_raygansms = $('.related_to_raygansms');
                    var related_to_smsbharti = $('.related_to_smsbharti');
                    var related_to_mshastra = $('.related_to_mshastra');
                    var related_to_taqnyat = $('.related_to_taqnyat');
                    var related_to_firebase = $('.related_to_firebase');

                    if (edf.is(':checked')) {
                        related_to_login.css('display', 'table-row');
                        // $("#idehweb_phone_number_ccode").chosen();


                    } else {

                        related_to_login.css('display', 'none');
                    }


                    if (edf2.is(':checked')) {
                        // console.log('is checked!');
                        // $("#idehweb_phone_number_ccode").chosen();
                        related_to_upnfu.css('display', 'none');


                    } else {
                        // console.log('is not checked!');
                        related_to_upnfu.css('display', 'table-row');

                    }
                    if (edf5.is(':checked')) {
                        // console.log('is checked!');
                        // $("#idehweb_phone_number_ccode").chosen();

                        related_to_entimer.css('display', 'table-row');

                    } else {
                        // console.log('is not checked!');
                        related_to_entimer.css('display', 'none');

                    }

                    if (edf3.is(':checked')) {
                        // console.log('is checked!');
                        // $("#idehweb_phone_number_ccode").chosen();
                        related_to_defaultgateway.css('display', 'table-row');
                        $('.rltll').css('display', 'none');


                    } else {
                        // console.log('is not checked!');
                        related_to_defaultgateway.css('display', 'none');


                    }
                    if (edf4.val() == 'twilio' && edf3.is(':checked')) {
                        // console.log('is checked!');
                        // $("#idehweb_phone_number_ccode").chosen();
                        related_to_twilio.css('display', 'table-row');


                    } else {
                        // console.log('is not checked!');
                        related_to_twilio.css('display', 'none');


                    }
                    if (edf4.val() == 'custom' && edf3.is(':checked')) {
                        // console.log('is checked!');
                        // $("#idehweb_phone_number_ccode").chosen();
                        related_to_customgateway.css('display', 'table-row');


                    } else {
                        // console.log('is not checked!');
                        related_to_customgateway.css('display', 'none');


                    }
                    if (edf4.val() == 'zenziva' && edf3.is(':checked')) {
                        // console.log('is checked!');
                        // $("#idehweb_phone_number_ccode").chosen();
                        related_to_zenziva.css('display', 'table-row');


                    } else {
                        // console.log('is not checked!');
                        related_to_zenziva.css('display', 'none');


                    }
                    if (edf4.val() == 'firebase' && edf3.is(':checked')) {
                        // console.log('is checked!');
                        // $("#idehweb_phone_number_ccode").chosen();
                        related_to_firebase.css('display', 'table-row');


                    } else {
                        // console.log('is not checked!');
                        related_to_firebase.css('display', 'none');


                    }
                    if (edf4.val() == 'infobip' && edf3.is(':checked')) {
                        // console.log('is checked!');
                        // $("#idehweb_phone_number_ccode").chosen();
                        related_to_infobip.css('display', 'table-row');


                    } else {
                        // console.log('is not checked!');
                        related_to_infobip.css('display', 'none');


                    }
                    if (edf4.val() == 'raygansms' && edf3.is(':checked')) {
                        // console.log('is checked!');
                        // $("#idehweb_phone_number_ccode").chosen();
                        related_to_raygansms.css('display', 'table-row');


                    } else {
                        // console.log('is not checked!');
                        related_to_raygansms.css('display', 'none');


                    }
                    if (edf4.val() == 'smsbharti' && edf3.is(':checked')) {
                        // console.log('is checked!');
                        // $("#idehweb_phone_number_ccode").chosen();
                        related_to_smsbharti.css('display', 'table-row');


                    } else {
                        // console.log('is not checked!');
                        related_to_smsbharti.css('display', 'none');


                    }
                    if (edf4.val() == 'mshastra' && edf3.is(':checked')) {
                        // console.log('is checked!');
                        // $("#idehweb_phone_number_ccode").chosen();
                        related_to_mshastra.css('display', 'table-row');


                    } else {
                        // console.log('is not checked!');
                        related_to_mshastra.css('display', 'none');


                    }
                    if (edf4.val() == 'taqnyat' && edf3.is(':checked')) {
                        // console.log('is checked!');
                        // $("#idehweb_phone_number_ccode").chosen();
                        related_to_taqnyat.css('display', 'table-row');


                    } else {
                        // console.log('is not checked!');
                        related_to_taqnyat.css('display', 'none');


                    }
                    $('#idehweb_lwp_settings_idehweb_sms_login').change(
                        function () {
                            if (this.checked && this.value == '1') {
                                // console.log('change is checked!');

                                related_to_login.css('display', 'table-row');
                                // $("#idehweb_phone_number_ccode").chosen();

                            } else {
                                // console.log('change is not checked!');

                                related_to_login.css('display', 'none');
                            }
                        });
                    $('#idehweb_lwp_settings_use_phone_number_for_username').change(
                        function () {
                            if (this.checked && this.value == '1') {
                                // console.log('change is checked!');

                                // $("#idehweb_phone_number_ccode").chosen();
                                related_to_upnfu.css('display', 'none');

                            } else {
                                // console.log('change is not checked!');
                                related_to_upnfu.css('display', 'table-row');

                            }
                        });
                    $('#idehweb_lwp_settings_use_custom_gateway').change(
                        function () {
                            $('#idehweb_default_gateways').trigger('change');
                            if (this.checked && this.value == '1') {
                                // console.log('change is checked!');

                                // $("#idehweb_phone_number_ccode").chosen();
                                related_to_defaultgateway.css('display', 'table-row');
                                $('.rltll').css('display', 'none');

                            } else {
                                // console.log('change is not checked!');
                                $('.rltll').css('display', 'table-row');

                                related_to_defaultgateway.css('display', 'none');

                            }
                        });

                    $('#idehweb_lwp_settings_enable_timer_on_sending_sms').change(
                        function () {
                            if (this.checked && this.value == '1') {
                                // console.log('change is checked!');

                                // $("#idehweb_phone_number_ccode").chosen();
                                related_to_entimer.css('display', 'table-row');

                            } else {
                                // console.log('change is not checked!');
                                related_to_entimer.css('display', 'none');

                            }
                        });
                    //
                    $('#idehweb_default_gateways').on('change', function (e) {
                        // console.log('event fired');
                        if (this.value == "custom" && edf3.is(':checked')) {

                            related_to_customgateway.css('display', 'table-row');
                            related_to_twilio.css('display', 'none');
                            related_to_zenziva.css('display', 'none');
                            related_to_firebase.css('display', 'none');
                            related_to_infobip.css('display', 'none');
                            related_to_raygansms.css('display', 'none');
                            related_to_smsbharti.css('display', 'none');
                            related_to_mshastra.css('display', 'none');
                            related_to_taqnyat.css('display', 'none');


                        } else if (this.value == "twilio" && edf3.is(':checked')) {
                            related_to_customgateway.css('display', 'none');
                            related_to_twilio.css('display', 'table-row');
                            related_to_zenziva.css('display', 'none');
                            related_to_firebase.css('display', 'none');
                            related_to_infobip.css('display', 'none');
                            related_to_raygansms.css('display', 'none');
                            related_to_smsbharti.css('display', 'none');
                            related_to_mshastra.css('display', 'none');
                            related_to_taqnyat.css('display', 'none');


                        } else if (this.value == "zenziva" && edf3.is(':checked')) {
                            related_to_customgateway.css('display', 'none');
                            related_to_twilio.css('display', 'none');
                            related_to_zenziva.css('display', 'table-row');
                            related_to_firebase.css('display', 'none');
                            related_to_infobip.css('display', 'none');
                            related_to_raygansms.css('display', 'none');
                            related_to_smsbharti.css('display', 'none');
                            related_to_mshastra.css('display', 'none');
                            related_to_taqnyat.css('display', 'none');


                        } else if (this.value == "firebase" && edf3.is(':checked')) {
                            related_to_customgateway.css('display', 'none');
                            related_to_twilio.css('display', 'none');
                            related_to_zenziva.css('display', 'none');
                            related_to_firebase.css('display', 'table-row');
                            related_to_infobip.css('display', 'none');
                            related_to_raygansms.css('display', 'none');
                            related_to_smsbharti.css('display', 'none');
                            related_to_mshastra.css('display', 'none');
                            related_to_taqnyat.css('display', 'none');


                        } else if (this.value == "infobip" && edf3.is(':checked')) {
                            related_to_customgateway.css('display', 'none');
                            related_to_twilio.css('display', 'none');
                            related_to_zenziva.css('display', 'none');
                            related_to_firebase.css('display', 'none');
                            related_to_infobip.css('display', 'table-row');
                            related_to_raygansms.css('display', 'none');
                            related_to_smsbharti.css('display', 'none');
                            related_to_mshastra.css('display', 'none');
                            related_to_taqnyat.css('display', 'none');


                        } else if (this.value == "raygansms" && edf3.is(':checked')) {
                            related_to_customgateway.css('display', 'none');
                            related_to_twilio.css('display', 'none');
                            related_to_zenziva.css('display', 'none');
                            related_to_firebase.css('display', 'none');
                            related_to_infobip.css('display', 'none');
                            related_to_raygansms.css('display', 'table-row');
                            related_to_smsbharti.css('display', 'none');
                            related_to_mshastra.css('display', 'none');
                            related_to_taqnyat.css('display', 'none');


                        } else if (this.value == "smsbharti" && edf3.is(':checked')) {
                            related_to_customgateway.css('display', 'none');
                            related_to_twilio.css('display', 'none');
                            related_to_zenziva.css('display', 'none');
                            related_to_firebase.css('display', 'none');
                            related_to_infobip.css('display', 'none');
                            related_to_raygansms.css('display', 'none');
                            related_to_smsbharti.css('display', 'table-row');
                            related_to_mshastra.css('display', 'none');
                            related_to_taqnyat.css('display', 'none');


                        } else if (this.value == "mshastra" && edf3.is(':checked')) {
                            related_to_customgateway.css('display', 'none');
                            related_to_twilio.css('display', 'none');
                            related_to_zenziva.css('display', 'none');
                            related_to_firebase.css('display', 'none');
                            related_to_infobip.css('display', 'none');
                            related_to_raygansms.css('display', 'none');
                            related_to_smsbharti.css('display', 'none');
                            related_to_mshastra.css('display', 'table-row');
                            related_to_taqnyat.css('display', 'none');


                        } else if (this.value == "taqnyat" && edf3.is(':checked')) {
                            related_to_customgateway.css('display', 'none');
                            related_to_twilio.css('display', 'none');
                            related_to_zenziva.css('display', 'none');
                            related_to_firebase.css('display', 'none');
                            related_to_infobip.css('display', 'none');
                            related_to_raygansms.css('display', 'none');
                            related_to_smsbharti.css('display', 'none');
                            related_to_mshastra.css('display', 'none');
                            related_to_taqnyat.css('display', 'table-row');


                        } else {

                            related_to_customgateway.css('display', 'none');
                            related_to_twilio.css('display', 'none');
                            related_to_zenziva.css('display', 'none');
                            related_to_firebase.css('display', 'none');
                            related_to_infobip.css('display', 'none');
                            related_to_raygansms.css('display', 'none');
                            related_to_smsbharti.css('display', 'none');
                            related_to_mshastra.css('display', 'none');
                            related_to_taqnyat.css('display', 'none');


                        }
                    });
                    idehweb_body.on('click', '.loiuyt',
                        function () {

                            $.ajax({
                                type: "GET",
                                url: ajaxurl,
                                data: {action: 'idehweb_lwp_check_credit'}
                            }).done(function (msg) {
                                var arr = JSON.parse(msg);
                                // console.log(arr);
                                $('.creditor .cp').html('<?php _e('Your Credit:', $this->textdomain) ?>' + ' ' + arr['credit'])


                            });

                        });
                    idehweb_body.on('click', '.refreshShop',
                        function () {
                            var lwp_token = $('#lwp_token').val();
                            if (lwp_token) {
                                $.ajax({
                                    type: "GET",
                                    url: ajaxurl,
                                    data: {action: 'idehweb_lwp_get_shop'}
                                }).done(function (msg) {
                                    if (msg) {
                                        var arr = JSON.parse(msg);
                                        if (arr && arr.products) {
                                            $('.chargeAccount').empty();
                                            for (var j = 0; j < arr.products.length; j++) {
                                                $('.chargeAccount').append('<div class="col-lg-2 col-md-4 col-sm-6">' +
                                                    '<div class="lwp-produ-wrap">' +
                                                    '<div class="lwp-shop-title">' +
                                                    arr.products[j].title + ' ' +
                                                    '</div>' +
                                                    '<div class="lwp-shop-price">' +
                                                    arr.products[j].price +
                                                    '</div>' +
                                                    '<div class="lwp-shop-buy">' +
                                                    '<a target="_blank" href="' + arr.products[j].buy + lwp_token + '/' + arr.products[j].ID + '">' + '<?php _e("Buy", $this->textdomain); ?>' + '</a>' +
                                                    '</div>' +
                                                    '</div>' +
                                                    '</div>'
                                                )

                                            }
                                        }
                                    }

                                });
                            }

                        });
                    idehweb_body.on('click', '.auth',
                        function () {
                            var lwp_phone_number = $('#lwp_phone_number').val();
                            var idehweb_phone_number_ccode = $('#idehweb_phone_number_ccode').val();
                            idehweb_phone_number_ccodeG = idehweb_phone_number_ccode;
                            // alert(idehweb_phone_number_ccode);
                            // return;
                            if (lwp_phone_number) {
                                lwp_phone_number = lwp_phone_number.replace(/^0+/, '');
                                $('.lwp_phone_number_label th').html('enter code messaged to you!');
                                $('#lwp_phone_number').css('display', 'none');
                                $('#lwp_secod').css('display', 'inherit');
                                $('.i34').css('display', 'inline-block');
                                $('.i35').css('display', 'none');
                                $('.idehweb_phone_number_ccode_wrap').css('display', 'none');
                                // $('#lwp_secod').html('enter code messaged to you!');
                                lwp_phone_number = idehweb_phone_number_ccode + lwp_phone_number;
                                $.ajax({
                                    type: "GET",
                                    url: ajaxurl,
                                    data: {
                                        action: 'idehweb_lwp_auth_customer',
                                        phone_number: lwp_phone_number,
                                        country_code: idehweb_phone_number_ccode
                                    }
                                }).done(function (msg) {
                                    if (msg) {
                                        var arr = JSON.parse(msg);
                                        // console.log(arr);
                                    }
                                    // $('form#iuytfrdghj').submit();

                                });

                            }
                        });

                    idehweb_body.on('click', '.authwithwebsite',
                        function () {
                            var lwp_token = $('#lwp_token').val();
                            // if(!lwp_token) {
                            var lwp_website_url = $('#lwp_website_url').val();
                            if (lwp_website_url) {
                                // lwp_phone_number = lwp_phone_number.replace(/^0+/, '');
                                // $('.lwp_phone_number_label th').html('enter code messaged to you!');
                                // $('#lwp_phone_number').css('display', 'none');
                                // $('#lwp_secod').css('display', 'inherit');
                                // $('.i34').css('display', 'inline-block');
                                // $('.i35').css('display', 'none');
                                // $('.idehweb_phone_number_ccode_wrap').css('display', 'none');
                                // $('#lwp_secod').html('enter code messaged to you!');
                                // lwp_phone_number = idehweb_phone_number_ccode + lwp_phone_number;
                                $('.lwp_website_label').fadeOut();

                                setTimeout(() => {
                                    $('.lwploadr').fadeOut();

                                }, 2000)
                                $.ajax({
                                    type: "GET",
                                    url: ajaxurl,
                                    data: {
                                        action: 'idehweb_lwp_auth_customer_with_website',
                                        url: lwp_website_url
                                    }
                                }).done(function (msg) {
                                    if (msg) {
                                        var arr = JSON.parse(msg);
                                        // console.log(arr);
                                        if (arr && arr['success']) {
                                            if (arr['token']) {
                                                $('#lwp_token').val(arr['token']);
                                                setTimeout(() => {
                                                    $('form#iuytfrdghj').submit();

                                                }, 500)
                                            }
                                        } else {
                                            if (arr['err'] && arr['err']['response'] && arr['err']['response']['request'] && arr['err']['response']['request']['uri'] && arr['err']['response']['request']['uri']['host'] === 'localhost') {
                                                $('.lwpmaintextloader').html('authentication on localhost not accepted. please use with your domain!');

                                            }

                                        }
                                    }

                                    // $('form#iuytfrdghj').submit();

                                });
                                // .((e)=>{
                                //     console.log('e',e);
                                // });

                            }
                            // }
                        });
                    idehweb_body.on('click', '.lwpchangePhoneNumber',
                        function (e) {
                            e.preventDefault();
                            $('.lwp_phone_number_label').removeClass('none');
                            $('#lwp_phone_number').focus();
                            // $("#idehweb_phone_number_ccode").chosen();

                        });
                    idehweb_body.on('click', '.lwp_more_help', function () {
                        createTutorial();
                    });
                    idehweb_body.on('click', '.lwp_close , .lwp_button', function (e) {
                        e.preventDefault();
                        $('.lwp_modal').remove();
                        $('.lwp_modal_overlay').remove();
                        localStorage.setItem('ldwtutshow', 1);
                    });
                    idehweb_body.on('click', '.activate',
                        function () {

                            var lwp_phone_number = $('#lwp_phone_number').val();
                            var lwp_secod = $('#lwp_secod').val();
                            var idehweb_phone_number_ccode = $('#idehweb_phone_number_ccode').val();

                            if (lwp_phone_number && lwp_secod && idehweb_phone_number_ccode) {
                                lwp_phone_number = lwp_phone_number.replace(/^0+/, '');
                                lwp_phone_number = idehweb_phone_number_ccode + lwp_phone_number;
                                $.ajax({
                                    type: "GET",
                                    url: ajaxurl,
                                    data: {
                                        action: 'idehweb_lwp_activate_customer', phone_number: lwp_phone_number,
                                        secod: lwp_secod
                                    }
                                }).done(function (msg) {
                                    if (msg) {
                                        var arr = JSON.parse(msg);
                                        // console.log(arr);
                                        if (arr['token']) {
                                            $('#lwp_token').val(arr['token']);
                                            //
                                            // idehweb_country_codes.val([idehweb_phone_number_ccodeG]); // Select the option with a value of '1'
                                            // idehweb_country_codes.trigger('change');

                                            // $('#idehweb_country_codes').val(arr['token']);
                                            setTimeout(() => {
                                                $('form#iuytfrdghj').submit();

                                            }, 500)
                                        }
                                    }
                                });

                            }
                        });
                    var ldwtutshow = localStorage.getItem('ldwtutshow');
                    if (ldwtutshow === null) {
                        // localStorage.setItem('ldwtutshow', 1);
                        // Show popup here
                        // $('#myModal').modal('show');
                        // console.log('set here');
                        createTutorial();
                    }

                    function createTutorial() {
                        var wrap = $('.wrap');
                        wrap.prepend('<div class="lwp_modal_overlay"></div>')
                            .prepend('<div class="lwp_modal">' +
                                '<div class="lwp_modal_header">' +
                                '<div class="lwp_l"></div>' +
                                '<div class="lwp_r"><button class="lwp_close">x</button></div>' +
                                '</div>' +
                                '<div class="lwp_modal_body">' +
                                '<ul>' +
                                '<li>' + '<?php _e("1. create a page and name it login or register or what ever", $this->textdomain) ?>' + '</li>' +
                                '<li>' + '<?php _e("2. copy this shortcode <code>[idehweb_lwp]</code> and paste in the page you created at step 1", $this->textdomain) ?>' + '</li>' +
                                '<li>' + '<?php _e("3. now, that is your login page. check your login page with other device or browser that you are not logged in!", $this->textdomain) ?>' +
                                '</li>' +
                                '<li>' +
                                '<?php _e("for more information visit: ", $this->textdomain) ?>' + '<a target="_blank" href="https://idehweb.com/product/login-with-phone-number-in-wordpress/?lang=en">Idehweb</a>' +
                                '</li>' +
                                '</ul>' +
                                '</div>' +
                                '<div class="lwp_modal_footer">' +
                                '<button class="lwp_button"><?php _e("got it ", $this->textdomain) ?></button>' +
                                '</div>' +
                                '</div>');

                    }
                });
            </script>
        </div>
        <?php
    }

    function lwp_custom_css()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_status'])) $options['idehweb_styles_status'] = '1';

        //first button
        if (!isset($options['idehweb_styles_button_background'])) $options['idehweb_styles_button_background'] = '#009b9a';
        if (!isset($options['idehweb_styles_button_border_color'])) $options['idehweb_styles_button_border_color'] = '#009b9a';
        if (!isset($options['idehweb_styles_button_text_color'])) $options['idehweb_styles_button_text_color'] = '#ffffff';
        if (!isset($options['idehweb_styles_button_border_radius'])) $options['idehweb_styles_button_border_radius'] = 'inherit';
        if (!isset($options['idehweb_styles_button_border_width'])) $options['idehweb_styles_button_border_width'] = 'inherit';

        //secondary button
        if (!isset($options['idehweb_styles_button_background2'])) $options['idehweb_styles_button_background2'] = '#009b9a';
        if (!isset($options['idehweb_styles_button_border_color2'])) $options['idehweb_styles_button_border_color2'] = '#009b9a';
        if (!isset($options['idehweb_styles_button_text_color2'])) $options['idehweb_styles_button_text_color2'] = '#ffffff';
        if (!isset($options['idehweb_styles_button_border_radius2'])) $options['idehweb_styles_button_border_radius2'] = 'inherit';
        if (!isset($options['idehweb_styles_button_border_width2'])) $options['idehweb_styles_button_border_width2'] = 'inherit';

        //input
        if (!isset($options['idehweb_styles_input_background'])) $options['idehweb_styles_input_background'] = 'inherit';
        if (!isset($options['idehweb_styles_input_border_color'])) $options['idehweb_styles_input_border_color'] = '#009b9a';
        if (!isset($options['idehweb_styles_input_text_color'])) $options['idehweb_styles_input_text_color'] = '#000000';
        if (!isset($options['idehweb_styles_input_placeholder_color'])) $options['idehweb_styles_input_placeholder_color'] = '#000000';
        if (!isset($options['idehweb_styles_input_border_radius'])) $options['idehweb_styles_input_border_radius'] = 'inherit';
        if (!isset($options['idehweb_styles_input_border_width'])) $options['idehweb_styles_input_border_width'] = '1px';

        //box
        if (!isset($options['idehweb_styles_box_background_color'])) $options['idehweb_styles_box_background_color'] = '#ffffff';

        //Labels
        if (!isset($options['idehweb_styles_labels_text_color'])) $options['idehweb_styles_labels_text_color'] = '#000000';
        if (!isset($options['idehweb_styles_labels_font_size'])) $options['idehweb_styles_labels_font_size'] = 'inherit';

        //title
        if (!isset($options['idehweb_styles_title_color'])) $options['idehweb_styles_title_color'] = '#000000';
        if (!isset($options['idehweb_styles_title_font_size'])) $options['idehweb_styles_title_font_size'] = 'inherit';

        $lwp_custom_css = ' .submit_button { background-color: ' . $options['idehweb_styles_button_background'] . ' !important; border-color: ' . $options['idehweb_styles_button_border_color'] . ' !important; color: ' . $options['idehweb_styles_button_text_color'] . ' !important;border-radius: ' . $options['idehweb_styles_button_border_radius'] . ' !important;border-width: ' . $options['idehweb_styles_button_border_width'] . ' !important; }
        .secondaryccolor { background-color: ' . $options['idehweb_styles_button_background2'] . ' !important; border-color: ' . $options['idehweb_styles_button_border_color2'] . ' !important; color: ' . $options['idehweb_styles_button_text_color2'] . ' !important;border-radius: ' . $options['idehweb_styles_button_border_radius2'] . ' !important;border-width: ' . $options['idehweb_styles_button_border_width2'] . ' !important; }
        .the_lwp_input { background-color: ' . $options['idehweb_styles_input_background'] . ' !important; border-color: ' . $options['idehweb_styles_input_border_color'] . ' !important; color: ' . $options['idehweb_styles_input_text_color'] . ' !important;border-radius: ' . $options['idehweb_styles_input_border_radius'] . ' !important;border-width: ' . $options['idehweb_styles_input_border_width'] . ' !important; }
        .the_lwp_input::placeholder{ color: ' . $options['idehweb_styles_input_placeholder_color'] . ' !important; }
        .lwp_forms_login form.ajax-auth{ background-color: ' . $options['idehweb_styles_box_background_color'] . ' !important; }
        .lwp_labels{ color: ' . $options['idehweb_styles_labels_text_color'] . ' !important;font-size: ' . $options['idehweb_styles_labels_font_size'] . ' !important; }
        .lh1{ color: ' . $options['idehweb_styles_title_color'] . ' !important;font-size: ' . $options['idehweb_styles_title_font_size'] . ' !important; }
        ';
        ?>

        <?php
        if ($options['idehweb_styles_status']) {
            ?>
            <style type="text/css"><?php echo $lwp_custom_css; ?></style>
            <?php
        }
    }

    function style_settings_page()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        if (!isset($options['idehweb_token'])) $options['idehweb_token'] = '';
        if (!isset($options['idehweb_online_support'])) $options['idehweb_online_support'] = '1';


        ?>
        <div class="wrap">
            <div id="icon-themes" class="icon32"></div>
            <h2><?php _e('Style settings', $this->textdomain); ?></h2>
            <?php if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {

                ?>
                <div id="setting-error-settings_updated" class="updated settings-error">
                    <p><strong><?php _e('Settings saved.', $this->textdomain); ?></strong></p>
                </div>
            <?php } ?>
            <form action="options.php" method="post" id="iuytfrdghj">
                <?php settings_fields('idehweb-lwp-styles'); ?>
                <?php do_settings_sections('idehweb-lwp-styles'); ?>

                <p class="submit">
                    <span id="wkdugchgwfchevg3r4r"></span>
                </p>
                <p class="submit">
                    <span id="oihdfvygehv"></span>
                </p>
                <p class="submit">

                    <input type="submit" class="button-primary"
                           value="<?php _e('Save Changes', $this->textdomain); ?>"/></p>

            </form>


        </div>
        <?php
    }

    function localization_settings_page()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        if (!isset($options['idehweb_token'])) $options['idehweb_token'] = '';
        if (!isset($options['idehweb_online_support'])) $options['idehweb_online_support'] = '1';


        ?>
        <div class="wrap">
            <div id="icon-themes" class="icon32"></div>
            <h2><?php _e('Localization settings', $this->textdomain); ?></h2>
            <?php if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {

                ?>
                <div id="setting-error-settings_updated" class="updated settings-error">
                    <p><strong><?php _e('Settings saved.', $this->textdomain); ?></strong></p>
                </div>
            <?php } ?>
            <form action="options.php" method="post" id="iuytfrdghj">
                <?php settings_fields('idehweb-lwp-localization'); ?>
                <?php do_settings_sections('idehweb-lwp-localization'); ?>

                <p class="submit">
                    <span id="wkdugchgwfchevg3r4r"></span>
                </p>
                <p class="submit">
                    <span id="oihdfvygehv"></span>
                </p>
                <p class="submit">

                    <input type="submit" class="button-primary"
                           value="<?php _e('Save Changes', $this->textdomain); ?>"/></p>

            </form>


        </div>
        <?php
    }

    function section_intro()
    {
        ?>

        <?php

    }

    function section_title()
    {
        ?>
        <!--        jhgjk-->

        <?php

    }

    function setting_idehweb_lwp_space()
    {
        echo '<div class="idehweb_lwp_mgt50"></div>';
    }

    function setting_idehweb_email_login()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_email_login'])) $options['idehweb_email_login'] = '1';
        $display = 'inherit';
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        if (!$options['idehweb_phone_number']) {
            $display = 'none';
        }
        echo '<input  type="hidden" name="idehweb_lwp_settings[idehweb_email_login]" value="0" />
		<label><input type="checkbox" name="idehweb_lwp_settings[idehweb_email_login]" value="1"' . (($options['idehweb_email_login']) ? ' checked="checked"' : '') . ' />' . __('I want user login with email', $this->textdomain) . '</label>';

    }

    function setting_idehweb_style_enable_custom_style()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_status'])) $options['idehweb_styles_status'] = '1';
        else $options['idehweb_styles_status'] = sanitize_text_field($options['idehweb_styles_status']);

        echo '<input  type="hidden" name="idehweb_lwp_settings_styles[idehweb_styles_status]" value="0" />
		<label><input type="checkbox" id="idehweb_lwp_settings_idehweb_styles_status" name="idehweb_lwp_settings_styles[idehweb_styles_status]" value="1"' . (($options['idehweb_styles_status']) ? ' checked="checked"' : '') . ' />' . __('enable custom styles', $this->textdomain) . '</label>';

    }


    function setting_idehweb_style_button_background_color()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_button_background'])) $options['idehweb_styles_button_background'] = '#009b9a';
        else $options['idehweb_styles_button_background'] = sanitize_text_field($options['idehweb_styles_button_background']);


        echo '<input type="color" name="idehweb_lwp_settings_styles[idehweb_styles_button_background]" class="regular-text" value="' . esc_attr($options['idehweb_styles_button_background']) . '" />
		<p class="description">' . __('button background color', $this->textdomain) . '</p>';
    }

    function setting_idehweb_style_button_border_color()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_button_border_color'])) $options['idehweb_styles_button_border_color'] = '#009b9a';
        else $options['idehweb_styles_button_border_color'] = sanitize_text_field($options['idehweb_styles_button_border_color']);

        echo '<input type="color" name="idehweb_lwp_settings_styles[idehweb_styles_button_border_color]" class="regular-text" value="' . esc_attr($options['idehweb_styles_button_border_color']) . '" />
		<p class="description">' . __('button border color', $this->textdomain) . '</p>';
    }

    function setting_idehweb_style_button_border_radius()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_button_border_radius'])) $options['idehweb_styles_button_border_radius'] = 'inherit';
        else $options['idehweb_styles_button_border_radius'] = sanitize_text_field($options['idehweb_styles_button_border_radius']);

        echo '<input type="text" name="idehweb_lwp_settings_styles[idehweb_styles_button_border_radius]" class="regular-text" value="' . esc_attr($options['idehweb_styles_button_border_radius']) . '" />
		<p class="description">' . __('0px 0px 0px 0px', $this->textdomain) . '</p>';
    }

    function setting_idehweb_style_button_border_width()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_button_border_width'])) $options['idehweb_styles_button_border_width'] = 'inherit';
        else $options['idehweb_styles_button_border_width'] = sanitize_text_field($options['idehweb_styles_button_border_width']);

        echo '<input type="text" name="idehweb_lwp_settings_styles[idehweb_styles_button_border_width]" class="regular-text" value="' . esc_attr($options['idehweb_styles_button_border_width']) . '" />
		<p class="description">' . __('0px 0px 0px 0px', $this->textdomain) . '</p>';
    }

    function setting_idehweb_style_button_text_color()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_button_text_color'])) $options['idehweb_styles_button_text_color'] = '#ffffff';
        else $options['idehweb_styles_button_text_color'] = sanitize_text_field($options['idehweb_styles_button_text_color']);

        echo '<input type="color" name="idehweb_lwp_settings_styles[idehweb_styles_button_text_color]" class="regular-text" value="' . esc_attr($options['idehweb_styles_button_text_color']) . '" />
		<p class="description">' . __('button text color', $this->textdomain) . '</p>';
    }


    function setting_idehweb_style_button_background_color2()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_button_background2'])) $options['idehweb_styles_button_background2'] = '#009b9a';
        else $options['idehweb_styles_button_background2'] = sanitize_text_field($options['idehweb_styles_button_background2']);

        echo '<input type="color" name="idehweb_lwp_settings_styles[idehweb_styles_button_background2]" class="regular-text" value="' . esc_attr($options['idehweb_styles_button_background2']) . '" />
		<p class="description">' . __('secondary button background color', $this->textdomain) . '</p>';
    }

    function setting_idehweb_style_button_border_color2()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_button_border_color2'])) $options['idehweb_styles_button_border_color2'] = '#009b9a';
        else $options['idehweb_styles_button_border_color2'] = sanitize_text_field($options['idehweb_styles_button_border_color2']);

        echo '<input type="color" name="idehweb_lwp_settings_styles[idehweb_styles_button_border_color2]" class="regular-text" value="' . esc_attr($options['idehweb_styles_button_border_color2']) . '" />
		<p class="description">' . __('secondary button border color', $this->textdomain) . '</p>';
    }

    function setting_idehweb_style_button_border_radius2()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_button_border_radius2'])) $options['idehweb_styles_button_border_radius2'] = 'inherit';
        else $options['idehweb_styles_button_border_radius2'] = sanitize_text_field($options['idehweb_styles_button_border_radius2']);

        echo '<input type="text" name="idehweb_lwp_settings_styles[idehweb_styles_button_border_radius2]" class="regular-text" value="' . esc_attr($options['idehweb_styles_button_border_radius2']) . '" />
		<p class="description">' . __('0px 0px 0px 0px', $this->textdomain) . '</p>';
    }

    function setting_idehweb_style_button_border_width2()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_button_border_width2'])) $options['idehweb_styles_button_border_width2'] = 'inherit';
        else $options['idehweb_styles_button_border_width2'] = sanitize_text_field($options['idehweb_styles_button_border_width2']);
        echo '<input type="text" name="idehweb_lwp_settings_styles[idehweb_styles_button_border_width2]" class="regular-text" value="' . esc_attr($options['idehweb_styles_button_border_width2']) . '" />
		<p class="description">' . __('0px 0px 0px 0px', $this->textdomain) . '</p>';
    }

    function setting_idehweb_style_button_text_color2()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_button_text_color2'])) $options['idehweb_styles_button_text_color2'] = '#ffffff';
        else $options['idehweb_styles_button_text_color2'] = sanitize_text_field($options['idehweb_styles_button_text_color2']);
        echo '<input type="color" name="idehweb_lwp_settings_styles[idehweb_styles_button_text_color2]" class="regular-text" value="' . esc_attr($options['idehweb_styles_button_text_color2']) . '" />
		<p class="description">' . __('secondary button text color', $this->textdomain) . '</p>';
    }


    function setting_idehweb_style_input_background_color()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_input_background'])) $options['idehweb_styles_input_background'] = '#009b9a';
        else $options['idehweb_styles_input_background'] = sanitize_text_field($options['idehweb_styles_input_background']);
        echo '<input type="color" name="idehweb_lwp_settings_styles[idehweb_styles_input_background]" class="regular-text" value="' . esc_attr($options['idehweb_styles_input_background']) . '" />
		<p class="description">' . __('input background color', $this->textdomain) . '</p>';
    }

    function setting_idehweb_style_input_border_color()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_input_border_color'])) $options['idehweb_styles_input_border_color'] = '#009b9a';
        else $options['idehweb_styles_input_border_color'] = sanitize_text_field($options['idehweb_styles_input_border_color']);

        echo '<input type="color" name="idehweb_lwp_settings_styles[idehweb_styles_input_border_color]" class="regular-text" value="' . esc_attr($options['idehweb_styles_input_border_color']) . '" />
		<p class="description">' . __('input border color', $this->textdomain) . '</p>';
    }

    function setting_idehweb_style_input_border_radius()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_input_border_radius'])) $options['idehweb_styles_input_border_radius'] = 'inherit';
        else $options['idehweb_styles_input_border_radius'] = sanitize_text_field($options['idehweb_styles_input_border_radius']);
        echo '<input type="text" name="idehweb_lwp_settings_styles[idehweb_styles_input_border_radius]" class="regular-text" value="' . esc_attr($options['idehweb_styles_input_border_radius']) . '" />
		<p class="description">' . __('0px 0px 0px 0px', $this->textdomain) . '</p>';
    }

    function setting_idehweb_style_input_border_width()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_input_border_width'])) $options['idehweb_styles_input_border_width'] = '1px';
        else $options['idehweb_styles_input_border_width'] = sanitize_text_field($options['idehweb_styles_input_border_width']);

        echo '<input type="text" name="idehweb_lwp_settings_styles[idehweb_styles_input_border_width]" class="regular-text" value="' . esc_attr($options['idehweb_styles_input_border_width']) . '" />
		<p class="description">' . __('0px 0px 0px 0px', $this->textdomain) . '</p>';
    }

    function setting_idehweb_style_input_text_color()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_input_text_color'])) $options['idehweb_styles_input_text_color'] = '#000000';
        echo '<input type="color" name="idehweb_lwp_settings_styles[idehweb_styles_input_text_color]" class="regular-text" value="' . esc_attr($options['idehweb_styles_input_text_color']) . '" />
		<p class="description">' . __('input text color', $this->textdomain) . '</p>';
    }

    function setting_idehweb_style_input_placeholder_color()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_input_placeholder_color'])) $options['idehweb_styles_input_placeholder_color'] = '#000000';
        echo '<input type="color" name="idehweb_lwp_settings_styles[idehweb_styles_input_placeholder_color]" class="regular-text" value="' . esc_attr($options['idehweb_styles_input_placeholder_color']) . '" />
		<p class="description">' . __('input placeholder color', $this->textdomain) . '</p>';
    }

    function setting_idehweb_style_box_background_color()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_box_background_color'])) $options['idehweb_styles_box_background_color'] = '#ffffff';
        else $options['idehweb_styles_box_background_color'] = sanitize_text_field($options['idehweb_styles_box_background_color']);
        echo '<input type="color" name="idehweb_lwp_settings_styles[idehweb_styles_box_background_color]" class="regular-text" value="' . esc_attr($options['idehweb_styles_box_background_color']) . '" />
		<p class="description">' . __('box background color', $this->textdomain) . '</p>';
    }

    function setting_idehweb_style_labels_font_size()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_labels_font_size'])) $options['idehweb_styles_labels_font_size'] = 'inherit';
        else $options['idehweb_styles_labels_font_size'] = sanitize_text_field($options['idehweb_styles_labels_font_size']);

        echo '<input type="text" name="idehweb_lwp_settings_styles[idehweb_styles_labels_font_size]" class="regular-text" value="' . esc_attr($options['idehweb_styles_labels_font_size']) . '" />
		<p class="description">' . __('13px', $this->textdomain) . '</p>';
    }

    function setting_idehweb_style_labels_text_color()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_labels_text_color'])) $options['idehweb_styles_labels_text_color'] = '#000000';
        else $options['idehweb_styles_labels_text_color'] = sanitize_text_field($options['idehweb_styles_labels_text_color']);

        echo '<input type="color" name="idehweb_lwp_settings_styles[idehweb_styles_labels_text_color]" class="regular-text" value="' . esc_attr($options['idehweb_styles_labels_text_color']) . '" />
		<p class="description">' . __('label text color', $this->textdomain) . '</p>';
    }

    function setting_idehweb_style_title_color()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_title_color'])) $options['idehweb_styles_title_color'] = '#000000';
        else $options['idehweb_styles_title_color'] = sanitize_text_field($options['idehweb_styles_title_color']);
        echo '<input type="color" name="idehweb_lwp_settings_styles[idehweb_styles_title_color]" class="regular-text" value="' . esc_attr($options['idehweb_styles_title_color']) . '" />
		<p class="description">' . __('label text color', $this->textdomain) . '</p>';
    }

    function setting_idehweb_style_title_font_size()
    {
        $options = get_option('idehweb_lwp_settings_styles');
        if (!isset($options['idehweb_styles_title_font_size'])) $options['idehweb_styles_title_font_size'] = 'inherit';
        else $options['idehweb_styles_title_font_size'] = sanitize_text_field($options['idehweb_styles_title_font_size']);
        echo '<input type="text" name="idehweb_lwp_settings_styles[idehweb_styles_title_font_size]" class="regular-text" value="' . esc_attr($options['idehweb_styles_title_font_size']) . '" />
		<p class="description">' . __('20px', $this->textdomain) . '</p>';
    }

    function setting_idehweb_localization_enable_custom_localization()
    {
        $options = get_option('idehweb_lwp_settings_localization');
        if (!isset($options['idehweb_localization_status'])) $options['idehweb_localization_status'] = '0';
        echo '<input  type="hidden" name="idehweb_lwp_settings_localization[idehweb_localization_status]" value="0" />
		<label><input type="checkbox" id="idehweb_lwp_settings_localization_status" name="idehweb_lwp_settings_localization[idehweb_localization_status]" value="1"' . (($options['idehweb_localization_status']) ? ' checked="checked"' : '') . ' />' . __('enable localization', $this->textdomain) . '</label>';

    }

    function setting_idehweb_localization_of_login_form()
    {
        $options = get_option('idehweb_lwp_settings_localization');
        if (!isset($options['idehweb_localization_title_of_login_form'])) $options['idehweb_localization_title_of_login_form'] = 'Login / register';
        else $options['idehweb_localization_title_of_login_form'] = sanitize_text_field($options['idehweb_localization_title_of_login_form']);


        echo '<input type="text" name="idehweb_lwp_settings_localization[idehweb_localization_title_of_login_form]" class="regular-text" value="' . esc_attr($options['idehweb_localization_title_of_login_form']) . '" />
		<p class="description">' . __('Login / register', $this->textdomain) . '</p>';
    }

    function setting_idehweb_localization_of_login_form_email()
    {
        $options = get_option('idehweb_lwp_settings_localization');
        if (!isset($options['idehweb_localization_title_of_login_form_email'])) $options['idehweb_localization_title_of_login_form_email'] = 'Login / register';
        else $options['idehweb_localization_title_of_login_form_email'] = sanitize_text_field($options['idehweb_localization_title_of_login_form_email']);


        echo '<input type="text" name="idehweb_lwp_settings_localization[idehweb_localization_title_of_login_form_email]" class="regular-text" value="' . esc_attr($options['idehweb_localization_title_of_login_form_email']) . '" />
		<p class="description">' . __('Login / register', $this->textdomain) . '</p>';
    }

    function setting_idehweb_localization_placeholder_of_phonenumber_field()
    {
        $options = get_option('idehweb_lwp_settings_localization');
        if (!isset($options['idehweb_localization_placeholder_of_phonenumber_field'])) $options['idehweb_localization_placeholder_of_phonenumber_field'] = '9*********';
        else $options['idehweb_localization_placeholder_of_phonenumber_field'] = sanitize_text_field($options['idehweb_localization_placeholder_of_phonenumber_field']);

        echo '<input type="text" name="idehweb_lwp_settings_localization[idehweb_localization_placeholder_of_phonenumber_field]" class="regular-text" value="' . esc_attr($options['idehweb_localization_placeholder_of_phonenumber_field']) . '" />
		<p class="description">' . __('9*********', $this->textdomain) . '</p>';
    }

    function setting_idehweb_sms_login()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_sms_login'])) $options['idehweb_sms_login'] = '1';
        $display = 'inherit';
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        if (!$options['idehweb_phone_number']) {
            $display = 'none';
        }
        echo '<input  type="hidden" name="idehweb_lwp_settings[idehweb_sms_login]" value="0" />
		<label><input type="checkbox" id="idehweb_lwp_settings_idehweb_sms_login" name="idehweb_lwp_settings[idehweb_sms_login]" value="1"' . (($options['idehweb_sms_login']) ? ' checked="checked"' : '') . ' />' . __('I want user login with phone number', $this->textdomain) . '</label>';

    }

    function setting_idehweb_user_registration()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_user_registration'])) $options['idehweb_user_registration'] = '1';

        echo '<input type="hidden" name="idehweb_lwp_settings[idehweb_user_registration]" value="0" />
		<label><input type="checkbox" name="idehweb_lwp_settings[idehweb_user_registration]" value="1"' . (($options['idehweb_user_registration']) ? ' checked="checked"' : '') . ' />' . __('I want to enable registration', $this->textdomain) . '</label>';

    }

    function setting_idehweb_password_login()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_password_login'])) $options['idehweb_password_login'] = '1';
        $display = 'inherit';
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        if (!$options['idehweb_phone_number']) {
            $display = 'none';
        }
        echo '<input type="hidden" name="idehweb_lwp_settings[idehweb_password_login]" value="0" />
		<label><input type="checkbox" name="idehweb_lwp_settings[idehweb_password_login]" value="1"' . (($options['idehweb_password_login']) ? ' checked="checked"' : '') . ' />' . __('I want user login with password too', $this->textdomain) . '</label>';

    }

    function idehweb_position_form()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_position_form'])) $options['idehweb_position_form'] = '0';

        echo '<input type="hidden" name="idehweb_lwp_settings[idehweb_position_form]" value="0" />
		<label><input type="checkbox" name="idehweb_lwp_settings[idehweb_position_form]" value="1"' . (($options['idehweb_position_form']) ? ' checked="checked"' : '') . ' />' . __('I want form shows on page in fix position', $this->textdomain) . '</label>';

    }

    function idehweb_online_support()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_online_support'])) $options['idehweb_online_support'] = '1';

        echo '<input type="hidden" name="idehweb_lwp_settings[idehweb_online_support]" value="0" />
		<label><input type="checkbox" name="idehweb_lwp_settings[idehweb_online_support]" value="1"' . (($options['idehweb_online_support']) ? ' checked="checked"' : '') . ' />' . __('I want online support be active', $this->textdomain) . '</label>';

    }

    function setting_use_custom_gateway()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_use_custom_gateway'])) $options['idehweb_use_custom_gateway'] = '1';

        echo '<input type="hidden" name="idehweb_lwp_settings[idehweb_use_custom_gateway]" value="0" />
		<label><input type="checkbox" id="idehweb_lwp_settings_use_custom_gateway" name="idehweb_lwp_settings[idehweb_use_custom_gateway]" value="1"' . (($options['idehweb_use_custom_gateway']) ? ' checked="checked"' : '') . ' />' . __('I want to use custom gateways', $this->textdomain) . '</label>';

    }

    function setting_default_gateways()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_default_gateways'])) $options['idehweb_default_gateways'] = 'firebase';
        $gateways = [
            ["value" => "twilio", "label" => "Twilio (International)"],
            ["value" => "zenziva", "label" => "Zenziva (Indonesia)"],
            ["value" => "infobip", "label" => "Infobip (Portugal , Brazil ...)"],
            ["value" => "firebase", "label" => "Firebase (Google)"],
//            ["value" => "raygansms", "label" => "Raygansms.com (Iran)"],
//            ["value" => "smsbharti", "label" => "smsbharti.com (India)"],
            ["value" => "mshastra", "label" => "mshastra.com (Saudi Arabia)"],
            ["value" => "taqnyat", "label" => "taqnyat.sa (Saudi Arabia)"]
        ];
//        ["value"=>"custom","label"=>"custom gateway setting"]
//        ["value"=>"saudibulksms","label"=>"Saudi Bulk SMS (STC) (saudi arabia)"],
//        print_r($options['idehweb_country_codes']);

        ?>
        <select name="idehweb_lwp_settings[idehweb_default_gateways]" id="idehweb_default_gateways">
            <?php
            foreach ($gateways as $gateway) {
                $rr = false;
//                if(is_array($options['idehweb_default_gateways']))
                if (($gateway["value"] == $options['idehweb_default_gateways'])) {
                    $rr = true;
                }
                echo '<option value="' . $gateway["value"] . '" ' . ($rr ? ' selected="selected"' : '') . '>' . $gateway['label'] . '</option>';
            }
            ?>
        </select>
        <?php

    }

    function setting_twilio_account_sid()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_twilio_account_sid'])) $options['idehweb_twilio_account_sid'] = '';

        echo '<input type="text" name="idehweb_lwp_settings[idehweb_twilio_account_sid]" class="regular-text" value="' . esc_attr($options['idehweb_twilio_account_sid']) . '" />
		<p class="description">' . __('enter your Twilio account SID', $this->textdomain) . '</p>';
    }

    function setting_twilio_auth_token()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_twilio_auth_token'])) $options['idehweb_twilio_auth_token'] = '';

        echo '<input type="text" name="idehweb_lwp_settings[idehweb_twilio_auth_token]" class="regular-text" value="' . esc_attr($options['idehweb_twilio_auth_token']) . '" />
		<p class="description">' . __('enter your Twilio auth token', $this->textdomain) . '</p>';
    }

    function setting_twilio_phone_number()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_twilio_phone_number'])) $options['idehweb_twilio_phone_number'] = '';

        echo '<input type="text" name="idehweb_lwp_settings[idehweb_twilio_phone_number]" class="regular-text" value="' . esc_attr($options['idehweb_twilio_phone_number']) . '" />
		<p class="description">' . __('enter your Twilio phone number', $this->textdomain) . '</p>';
    }

    function setting_zenziva_user_key()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_zenziva_user_key'])) $options['idehweb_zenziva_user_key'] = '';

        echo '<input type="text" name="idehweb_lwp_settings[idehweb_zenziva_user_key]" class="regular-text" value="' . esc_attr($options['idehweb_zenziva_user_key']) . '" />
		<p class="description">' . __('enter your Zenziva user key', $this->textdomain) . '</p>';
    }

    function setting_zenziva_pass_key()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_zenziva_pass_key'])) $options['idehweb_zenziva_pass_key'] = '';

        echo '<input type="text" name="idehweb_lwp_settings[idehweb_zenziva_pass_key]" class="regular-text" value="' . esc_attr($options['idehweb_zenziva_pass_key']) . '" />
		<p class="description">' . __('enter your Zenziva pass key', $this->textdomain) . '</p>';
    }

    function setting_infobip_user()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_infobip_user'])) $options['idehweb_infobip_user'] = '';

        echo '<input type="text" name="idehweb_lwp_settings[idehweb_infobip_user]" class="regular-text" value="' . esc_attr($options['idehweb_infobip_user']) . '" />
		<p class="description">' . __('enter your Infobip pass key', $this->textdomain) . '</p>';
    }


    function setting_infobip_password()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_infobip_password'])) $options['idehweb_infobip_password'] = '';

        echo '<input type="text" name="idehweb_lwp_settings[idehweb_infobip_password]" class="regular-text" value="' . esc_attr($options['idehweb_infobip_password']) . '" />
		<p class="description">' . __('enter your Infobip pass key', $this->textdomain) . '</p>';
    }

    function setting_infobip_sender()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_infobip_sender'])) $options['idehweb_infobip_sender'] = '';

        echo '<input type="text" name="idehweb_lwp_settings[idehweb_infobip_sender]" class="regular-text" value="' . esc_attr($options['idehweb_infobip_sender']) . '" />
		<p class="description">' . __('enter your Infobip sender', $this->textdomain) . '</p>';
    }


    function setting_firebase_api()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_firebase_api'])) $options['idehweb_firebase_api'] = '';

        echo '<input type="text" name="idehweb_lwp_settings[idehweb_firebase_api]" class="regular-text" value="' . esc_attr($options['idehweb_firebase_api']) . '" />
		<p class="description">' . __('enter Firebase api', $this->textdomain) . ' - <a  href="https://idehweb.com/support/login-with-phone-number-wordpress/send-10000-sms-free-with-firebase-in-plugin-login-with-phone-number-wordpress/" target="_blank">' . __('Firebase config help - documentation', $this->textdomain) . '</a></p>';
    }

    function setting_firebase_config()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_firebase_config'])) $options['idehweb_firebase_config'] = '';
        else $options['idehweb_firebase_config'] = sanitize_textarea_field($options['idehweb_firebase_config']);

        echo '<textarea name="idehweb_lwp_settings[idehweb_firebase_config]" class="regular-text">' . esc_attr($options['idehweb_firebase_config']) . '</textarea>
		<p class="description">' . __('enter Firebase config', $this->textdomain) . '</p>';
    }

    function setting_raygansms_username()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_raygansms_username'])) $options['idehweb_raygansms_username'] = '';

        echo '<input type="text" name="idehweb_lwp_settings[idehweb_raygansms_username]" class="regular-text" value="' . esc_attr($options['idehweb_raygansms_username']) . '" />
		<p class="description">' . __('enter your Raygansms username', $this->textdomain) . '</p>';
    }

    function setting_raygansms_password()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_raygansms_password'])) $options['idehweb_raygansms_password'] = '';

        echo '<input type="text" name="idehweb_lwp_settings[idehweb_raygansms_password]" class="regular-text" value="' . esc_attr($options['idehweb_raygansms_password']) . '" />
		<p class="description">' . __('enter your Raygansms password', $this->textdomain) . '</p>';
    }

    function setting_raygansms_phonenumber()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_raygansms_phonenumber'])) $options['idehweb_raygansms_phonenumber'] = '';

        echo '<input type="text" name="idehweb_lwp_settings[idehweb_raygansms_phonenumber]" class="regular-text" value="' . esc_attr($options['idehweb_raygansms_phonenumber']) . '" />
		<p class="description">' . __('enter your Raygansms phone number', $this->textdomain) . '</p>';
    }


    function setting_smsbharti_api_key()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_smsbharti_api_key'])) $options['idehweb_smsbharti_api_key'] = '';

        echo '<input type="text" name="idehweb_lwp_settings[idehweb_smsbharti_api_key]" class="regular-text" value="' . esc_attr($options['idehweb_smsbharti_api_key']) . '" />
		<p class="description">' . __('enter your smsbharti api key', $this->textdomain) . '</p>';
    }

    function setting_smsbharti_from()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_smsbharti_from'])) $options['idehweb_smsbharti_from'] = '';

        echo '<input type="text" name="idehweb_lwp_settings[idehweb_smsbharti_from]" class="regular-text" value="' . esc_attr($options['idehweb_smsbharti_from']) . '" />
		<p class="description">' . __('enter your smsbharti from', $this->textdomain) . '</p>';
    }

    function setting_smsbharti_template_id()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_smsbharti_template_id'])) $options['idehweb_smsbharti_template_id'] = '';

        echo '<input type="text" name="idehweb_lwp_settings[idehweb_smsbharti_template_id]" class="regular-text" value="' . esc_attr($options['idehweb_smsbharti_template_id']) . '" />
		<p class="description">' . __('enter your smsbharti template id', $this->textdomain) . '</p>';
    }

    function setting_smsbharti_routeid()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_smsbharti_routeid'])) $options['idehweb_smsbharti_routeid'] = '';

        echo '<input type="text" name="idehweb_lwp_settings[idehweb_smsbharti_routeid]" class="regular-text" value="' . esc_attr($options['idehweb_smsbharti_routeid']) . '" />
		<p class="description">' . __('enter your smsbharti route id', $this->textdomain) . '</p>';
    }


    function setting_mshastra_user()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_mshastra_user'])) $options['idehweb_mshastra_user'] = '';

        echo '<input type="text" name="idehweb_lwp_settings[idehweb_mshastra_user]" class="regular-text" value="' . esc_attr($options['idehweb_mshastra_user']) . '" />
		<p class="description">' . __('enter your mshastra username', $this->textdomain) . '</p>';
    }

    function setting_mshastra_pwd()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_mshastra_pwd'])) $options['idehweb_mshastra_pwd'] = '';

        echo '<input type="text" name="idehweb_lwp_settings[idehweb_mshastra_pwd]" class="regular-text" value="' . esc_attr($options['idehweb_mshastra_pwd']) . '" />
		<p class="description">' . __('enter your mshastra password', $this->textdomain) . '</p>';
    }

    function setting_mshastra_senderid()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_mshastra_senderid'])) $options['idehweb_mshastra_senderid'] = '';

        echo '<input type="text" name="idehweb_lwp_settings[idehweb_mshastra_senderid]" class="regular-text" value="' . esc_attr($options['idehweb_mshastra_senderid']) . '" />
		<p class="description">' . __('enter your mshastra sender ID', $this->textdomain) . '</p>';
    }
//    function setting_custom_gateway_url()
//    {
//
//        $options = get_option('idehweb_lwp_settings');
//        if (!isset($options['idehweb_custom_gateway_url'])) $options['idehweb_custom_gateway_url'] = '';
//
//        echo '<input type="text" name="idehweb_lwp_settings[idehweb_custom_gateway_url]" class="regular-text" value="' . esc_attr($options['idehweb_custom_gateway_url']) . '" />
//		<p class="description">' . __('enter your sms gateway url', $this->textdomain) . '</p>';
//    }
//    function setting_custom_gateway_username()
//    {
//
//        $options = get_option('idehweb_lwp_settings');
//        if (!isset($options['idehweb_custom_gateway_username'])) $options['idehweb_custom_gateway_username'] = '';
//
//        echo '<input type="text" name="idehweb_lwp_settings[idehweb_custom_gateway_username]" class="regular-text" value="' . esc_attr($options['idehweb_custom_gateway_username']) . '" />
//		<p class="description">' . __('enter your sms gateway username', $this->textdomain) . '</p>';
//    }
//
//    function setting_custom_gateway_password()
//    {
//
//        $options = get_option('idehweb_lwp_settings');
//        if (!isset($options['idehweb_custom_gateway_password'])) $options['idehweb_custom_gateway_password'] = '';
//
//        echo '<input type="text" name="idehweb_lwp_settings[idehweb_custom_gateway_password]" class="regular-text" value="' . esc_attr($options['idehweb_custom_gateway_password']) . '" />
//		<p class="description">' . __('enter your sms gateway password', $this->textdomain) . '</p>';
//    }


    function setting_taqnyat_sender_number()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_taqnyat_sendernumber'])) $options['idehweb_taqnyat_sendernumber'] = '';

        echo '<input type="text" name="idehweb_lwp_settings[idehweb_taqnyat_sendernumber]" class="regular-text" value="' . esc_attr($options['idehweb_taqnyat_sendernumber']) . '" />
		<p class="description">' . __('enter your taqnyat sender number', $this->textdomain) . '</p>';
    }

    function setting_taqnyat_api_key()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_taqnyat_api_key'])) $options['idehweb_taqnyat_api_key'] = '';

        echo '<input type="text" name="idehweb_lwp_settings[idehweb_taqnyat_api_key]" class="regular-text" value="' . esc_attr($options['idehweb_taqnyat_api_key']) . '" />
		<p class="description">' . __('enter your taqnyat api key', $this->textdomain) . '</p>';
    }

    function idehweb_use_phone_number_for_username()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_use_phone_number_for_username'])) $options['idehweb_use_phone_number_for_username'] = '0';

        echo '<input type="hidden" name="idehweb_lwp_settings[idehweb_use_phone_number_for_username]" value="0" />
		<label><input type="checkbox" id="idehweb_lwp_settings_use_phone_number_for_username" name="idehweb_lwp_settings[idehweb_use_phone_number_for_username]" value="1"' . (($options['idehweb_use_phone_number_for_username']) ? ' checked="checked"' : '') . ' />' . __('I want to set phone number as username and nickname', $this->textdomain) . '</label>';

    }

    function idehweb_enable_timer_on_sending_sms()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_enable_timer_on_sending_sms'])) $options['idehweb_enable_timer_on_sending_sms'] = '1';

        echo '<input type="hidden" name="idehweb_lwp_settings[idehweb_enable_timer_on_sending_sms]" value="0" />
		<label><input type="checkbox" id="idehweb_lwp_settings_enable_timer_on_sending_sms" name="idehweb_lwp_settings[idehweb_enable_timer_on_sending_sms]" value="1"' . (($options['idehweb_enable_timer_on_sending_sms']) ? ' checked="checked"' : '') . ' />' . __('I want to enable timer after user entered phone number and clicked on submit', $this->textdomain) . '</label>';

    }


    function setting_timer_count()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_timer_count'])) $options['idehweb_timer_count'] = '60';


        echo '<input id="lwp_timer_count" type="text" name="idehweb_lwp_settings[idehweb_timer_count]" class="regular-text" value="' . esc_attr($options['idehweb_timer_count']) . '" />
		<p class="description">' . __('Timer count', $this->textdomain) . '</p>';

    }

    function idehweb_enable_accept_term_and_conditions()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_enable_accept_terms_and_condition'])) $options['idehweb_enable_accept_terms_and_condition'] = '1';

        echo '<input type="hidden" name="idehweb_lwp_settings[idehweb_enable_accept_terms_and_condition]" value="0" />
		<label><input type="checkbox" id="idehweb_enable_accept_terms_and_condition" name="idehweb_lwp_settings[idehweb_enable_accept_terms_and_condition]" value="1"' . (($options['idehweb_enable_accept_terms_and_condition']) ? ' checked="checked"' : '') . ' />' . __('I want to show some terms & conditions for user to accept it, when he/she wants to register ', $this->textdomain) . '</label>';

    }

    function setting_term_and_conditions_text()
    {

        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_term_and_conditions_text'])) $options['idehweb_term_and_conditions_text'] = 'By submitting, you agree to the <a href="#">Terms and Privacy Policy</a>';
        else $options['idehweb_term_and_conditions_text'] = ($options['idehweb_term_and_conditions_text']);
        echo '<textarea name="idehweb_lwp_settings[idehweb_term_and_conditions_text]" class="regular-text">' . esc_attr($options['idehweb_term_and_conditions_text']) . '</textarea>
		<p class="description">' . __('enter term and condition accepting text', $this->textdomain) . '</p>';
    }

    function credit_adminbar()
    {
        global $wp_admin_bar, $melipayamak;
        if (!is_super_admin() || !is_admin_bar_showing())
            return;

        $credit = '0';
//        $wp_admin_bar -> add_menu(array('id' => 'melipayamak', 'title' => __('sms credit: ',$this->textdomain).'<span class="lwpcreditupdate">'.$credit.'</span>', 'href' => get_bloginfo('url') . '/wp-admin/admin.php?page=melipayamak'));

//        $wp_admin_bar->add_menu(array('id' => 'lwpcreditbar', 'title' => '<span class="ab-icon dashicons dashicons-smartphone"></span><span class="lwpcreditupdate">' . $credit . '</span>', 'href' => get_bloginfo('url') . '/wp-admin/admin.php?page=idehweb-lwp'));
        ?>
        <!--        <script>-->
        <!---->
        <!--            jQuery(function ($) {-->
        <!--                $(window).load(function () {-->
        <!--                    $.ajax({-->
        <!--                        type: "GET",-->
        <!--                        url: ajaxurl,-->
        <!--                        data: {action: 'idehweb_lwp_check_credit'}-->
        <!--                    }).done(function (msg) {-->
        <!--                        var arr = JSON.parse(msg);-->
        <!--                        console.log(arr);-->
        <!--                        $('.lwpcreditupdate').html(arr['credit'])-->
        <!---->
        <!---->
        <!--                    });-->
        <!--                });-->
        <!--            });-->
        <!--        </script>-->
        <?php
        //        $balance = $melipayamak -> credit;
//        if ($balance && $melipayamak -> is_ready) {
//            $balance = number_format($balance);
//            $wp_admin_bar -> add_menu(array('parent' => 'melipayamak', 'title' => 'موجودی حساب: ' . $balance . ' پیامک', 'href' => get_bloginfo('url') . '/wp-admin/admin.php?page=melipayamak_setting'));
//        }
//        $t = 'اعضای خبرنامه: ' . number_format(intval($melipayamak -> count)) . ' نفر';
//        $wp_admin_bar -> add_menu(array('parent' => 'melipayamak', 'title' => $t, 'href' => get_bloginfo('url') . '/wp-admin/admin.php?page=melipayamak_phonebook'));
//        $wp_admin_bar -> add_menu(array('parent' => 'melipayamak', 'title' => 'مشاهده پیام ها', 'href' => get_bloginfo('url') . '/wp-admin/admin.php?page=melipayamak_smessages'));
//        $wp_admin_bar -> add_menu(array('parent' => 'melipayamak', 'title' => 'ملی پیامک', 'href' => 'http://melipayamak.com'));
    }

    function setting_idehweb_phone_number()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        if (!isset($options['idehweb_phone_number_ccode'])) $options['idehweb_phone_number_ccode'] = '';
        ?>
        <div class="idehweb_phone_number_ccode_wrap">
            <select name="idehweb_lwp_settings[idehweb_phone_number_ccode]" id="idehweb_phone_number_ccode"
                    data-placeholder="<?php _e('Choose a country...', $this->textdomain); ?>">
                <?php
                $country_codes = $this->get_country_code_options();

                foreach ($country_codes as $country) {
                    echo '<option value="' . $country["value"] . '" ' . (($options['idehweb_phone_number_ccode'] == $country["value"]) ? ' selected="selected"' : '') . ' >+' . $country['value'] . ' - ' . $country["code"] . '</option>';
                }
                ?>
            </select>
            <?php
            echo '<input placeholder="Ex: 9120539945" type="text" name="idehweb_lwp_settings[idehweb_phone_number]" id="lwp_phone_number" class="regular-text" value="' . esc_attr($options['idehweb_phone_number']) . '" />';
            ?>
        </div>
        <?php
        echo '<input type="text" name="idehweb_lwp_settings[idehweb_secod]" id="lwp_secod" class="regular-text" style="display:none" value="" placeholder="_ _ _ _ _ _" />';
        ?>
        <button type="button" class="button-primary auth i35"
                value="<?php _e('Authenticate', $this->textdomain); ?>"><?php _e('activate sms login', $this->textdomain); ?></button>
        <button type="button" class="button-primary activate i34" style="display: none"
                value="<?php _e('Activate', $this->textdomain); ?>"><?php _e('activate account', $this->textdomain); ?></button>

        <?php
    }

    function setting_idehweb_website_url()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_website_url'])) $options['idehweb_website_url'] = $this->settings_get_site_url();
        ?>
        <div class="idehweb_website_url_wrap">
            <?php
            echo '<input placeholder="Ex: example.com" type="text" name="idehweb_lwp_settings[idehweb_website_url]" id="lwp_website_url" class="regular-text" value="' . esc_attr($options['idehweb_website_url']) . '" />';
            ?>
        </div>

        <button type="button" class="button-primary authwithwebsite i35"
                value="<?php _e('Authenticate', $this->textdomain); ?>"><?php _e('activate sms login', $this->textdomain); ?></button>

        <?php
    }

    function setting_idehweb_token()
    {
        $options = get_option('idehweb_lwp_settings');
        $display = 'inherit';
        if (!isset($options['idehweb_token'])) $options['idehweb_token'] = '';
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        if (!$options['idehweb_phone_number']) {
            $display = 'none';
        }
        echo '<input id="lwp_token" type="text" name="idehweb_lwp_settings[idehweb_token]" class="regular-text" value="' . esc_attr($options['idehweb_token']) . '" />
		<p class="description">' . __('enter api key', $this->textdomain) . '</p>';

    }

    function settings_get_site_url()
    {
        $url = get_site_url();
        $disallowed = array('http://', 'https://', 'https://www.', 'http://www.', 'www.');
        foreach ($disallowed as $d) {
            if (strpos($url, $d) === 0) {
                return str_replace($d, '', $url);
            }
        }
        return $url;

    }

    function setting_idehweb_url_redirect()
    {
        $options = get_option('idehweb_lwp_settings');
        $display = 'inherit';
        if (!isset($options['idehweb_redirect_url'])) $options['idehweb_redirect_url'] = '';
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        if (!$options['idehweb_phone_number']) {
            $display = 'none';
        }
        echo '<input id="lwp_token" type="text" name="idehweb_lwp_settings[idehweb_redirect_url]" class="regular-text" value="' . esc_attr($options['idehweb_redirect_url']) . '" />
		<p class="description">' . __('enter redirect url', $this->textdomain) . '</p>';

    }

    function get_country_code_options()
    {

        $json_countries = '[["Afghanistan (‫افغانستان‬‎)", "af", "93"], ["Albania (Shqipëri)", "al", "355"], ["Algeria (‫الجزائر‬‎)", "dz", "213"], ["American Samoa", "as", "1684"], ["Andorra", "ad", "376"], ["Angola", "ao", "244"], ["Anguilla", "ai", "1264"], ["Antigua and Barbuda", "ag", "1268"], ["Argentina", "ar", "54"], ["Armenia (Հայաստան)", "am", "374"], ["Aruba", "aw", "297"], ["Australia", "au", "61", 0], ["Austria (Österreich)", "at", "43"], ["Azerbaijan (Azərbaycan)", "az", "994"], ["Bahamas", "bs", "1242"], ["Bahrain (‫البحرين‬‎)", "bh", "973"], ["Bangladesh (বাংলাদেশ)", "bd", "880"], ["Barbados", "bb", "1246"], ["Belarus (Беларусь)", "by", "375"], ["Belgium (België)", "be", "32"], ["Belize", "bz", "501"], ["Benin (Bénin)", "bj", "229"], ["Bermuda", "bm", "1441"], ["Bhutan (འབྲུག)", "bt", "975"], ["Bolivia", "bo", "591"], ["Bosnia and Herzegovina (Босна и Херцеговина)", "ba", "387"], ["Botswana", "bw", "267"], ["Brazil (Brasil)", "br", "55"], ["British Indian Ocean Territory", "io", "246"], ["British Virgin Islands", "vg", "1284"], ["Brunei", "bn", "673"], ["Bulgaria (България)", "bg", "359"], ["Burkina Faso", "bf", "226"], ["Burundi (Uburundi)", "bi", "257"], ["Cambodia (កម្ពុជា)", "kh", "855"], ["Cameroon (Cameroun)", "cm", "237"], ["Canada", "ca", "1", 1, ["204", "226", "236", "249", "250", "289", "306", "343", "365", "387", "403", "416", "418", "431", "437", "438", "450", "506", "514", "519", "548", "579", "581", "587", "604", "613", "639", "647", "672", "705", "709", "742", "778", "780", "782", "807", "819", "825", "867", "873", "902", "905"]], ["Cape Verde (Kabu Verdi)", "cv", "238"], ["Caribbean Netherlands", "bq", "599", 1], ["Cayman Islands", "ky", "1345"], ["Central African Republic (République centrafricaine)", "cf", "236"], ["Chad (Tchad)", "td", "235"], ["Chile", "cl", "56"], ["China (中国)", "cn", "86"], ["Christmas Island", "cx", "61", 2], ["Cocos (Keeling) Islands", "cc", "61", 1], ["Colombia", "co", "57"], ["Comoros (‫جزر القمر‬‎)", "km", "269"], ["Congo (DRC) (Jamhuri ya Kidemokrasia ya Kongo)", "cd", "243"], ["Congo (Republic) (Congo-Brazzaville)", "cg", "242"], ["Cook Islands", "ck", "682"], ["Costa Rica", "cr", "506"], ["Côte d’Ivoire", "ci", "225"], ["Croatia (Hrvatska)", "hr", "385"], ["Cuba", "cu", "53"], ["Curaçao", "cw", "599", 0], ["Cyprus (Κύπρος)", "cy", "357"], ["Czech Republic (Česká republika)", "cz", "420"], ["Denmark (Danmark)", "dk", "45"], ["Djibouti", "dj", "253"], ["Dominica", "dm", "1767"], ["Dominican Republic (República Dominicana)", "do", "1", 2, ["809", "829", "849"]], ["Ecuador", "ec", "593"], ["Egypt (‫مصر‬‎)", "eg", "20"], ["El Salvador", "sv", "503"], ["Equatorial Guinea (Guinea Ecuatorial)", "gq", "240"], ["Eritrea", "er", "291"], ["Estonia (Eesti)", "ee", "372"], ["Ethiopia", "et", "251"], ["Falkland Islands (Islas Malvinas)", "fk", "500"], ["Faroe Islands (Føroyar)", "fo", "298"], ["Fiji", "fj", "679"], ["Finland (Suomi)", "fi", "358", 0], ["France", "fr", "33"], ["French Guiana (Guyane française)", "gf", "594"], ["French Polynesia (Polynésie française)", "pf", "689"], ["Gabon", "ga", "241"], ["Gambia", "gm", "220"], ["Georgia (საქართველო)", "ge", "995"], ["Germany (Deutschland)", "de", "49"], ["Ghana (Gaana)", "gh", "233"], ["Gibraltar", "gi", "350"], ["Greece (Ελλάδα)", "gr", "30"], ["Greenland (Kalaallit Nunaat)", "gl", "299"], ["Grenada", "gd", "1473"], ["Guadeloupe", "gp", "590", 0], ["Guam", "gu", "1671"], ["Guatemala", "gt", "502"], ["Guernsey", "gg", "44", 1], ["Guinea (Guinée)", "gn", "224"], ["Guinea-Bissau (Guiné Bissau)", "gw", "245"], ["Guyana", "gy", "592"], ["Haiti", "ht", "509"], ["Honduras", "hn", "504"], ["Hong Kong (香港)", "hk", "852"], ["Hungary (Magyarország)", "hu", "36"], ["Iceland (Ísland)", "is", "354"], ["India (भारत)", "in", "91"], ["Indonesia", "id", "62"], ["Iran (‫ایران‬‎)", "ir", "98"], ["Iraq (‫العراق‬‎)", "iq", "964"], ["Ireland", "ie", "353"], ["Isle of Man", "im", "44", 2], ["Israel (‫ישראל‬‎)", "il", "972"], ["Italy (Italia)", "it", "39", 0], ["Jamaica", "jm", "1", 4, ["876", "658"]], ["Japan (日本)", "jp", "81"], ["Jersey", "je", "44", 3], ["Jordan (‫الأردن‬‎)", "jo", "962"], ["Kazakhstan (Казахстан)", "kz", "7", 1], ["Kenya", "ke", "254"], ["Kiribati", "ki", "686"], ["Kosovo", "xk", "383"], ["Kuwait (‫الكويت‬‎)", "kw", "965"], ["Kyrgyzstan (Кыргызстан)", "kg", "996"], ["Laos (ລາວ)", "la", "856"], ["Latvia (Latvija)", "lv", "371"], ["Lebanon (‫لبنان‬‎)", "lb", "961"], ["Lesotho", "ls", "266"], ["Liberia", "lr", "231"], ["Libya (‫ليبيا‬‎)", "ly", "218"], ["Liechtenstein", "li", "423"], ["Lithuania (Lietuva)", "lt", "370"], ["Luxembourg", "lu", "352"], ["Macau (澳門)", "mo", "853"], ["Macedonia (FYROM) (Македонија)", "mk", "389"], ["Madagascar (Madagasikara)", "mg", "261"], ["Malawi", "mw", "265"], ["Malaysia", "my", "60"], ["Maldives", "mv", "960"], ["Mali", "ml", "223"], ["Malta", "mt", "356"], ["Marshall Islands", "mh", "692"], ["Martinique", "mq", "596"], ["Mauritania (‫موريتانيا‬‎)", "mr", "222"], ["Mauritius (Moris)", "mu", "230"], ["Mayotte", "yt", "262", 1], ["Mexico (México)", "mx", "52"], ["Micronesia", "fm", "691"], ["Moldova (Republica Moldova)", "md", "373"], ["Monaco", "mc", "377"], ["Mongolia (Монгол)", "mn", "976"], ["Montenegro (Crna Gora)", "me", "382"], ["Montserrat", "ms", "1664"], ["Morocco (‫المغرب‬‎)", "ma", "212", 0], ["Mozambique (Moçambique)", "mz", "258"], ["Myanmar (Burma) (မြန်မာ)", "mm", "95"], ["Namibia (Namibië)", "na", "264"], ["Nauru", "nr", "674"], ["Nepal (नेपाल)", "np", "977"], ["Netherlands (Nederland)", "nl", "31"], ["New Caledonia (Nouvelle-Calédonie)", "nc", "687"], ["New Zealand", "nz", "64"], ["Nicaragua", "ni", "505"], ["Niger (Nijar)", "ne", "227"], ["Nigeria", "ng", "234"], ["Niue", "nu", "683"], ["Norfolk Island", "nf", "672"], ["North Korea (조선 민주주의 인민 공화국)", "kp", "850"], ["Northern Mariana Islands", "mp", "1670"], ["Norway (Norge)", "no", "47", 0], ["Oman (‫عُمان‬‎)", "om", "968"], ["Pakistan (‫پاکستان‬‎)", "pk", "92"], ["Palau", "pw", "680"], ["Palestine (‫فلسطين‬‎)", "ps", "970"], ["Panama (Panamá)", "pa", "507"], ["Papua New Guinea", "pg", "675"], ["Paraguay", "py", "595"], ["Peru (Perú)", "pe", "51"], ["Philippines", "ph", "63"], ["Poland (Polska)", "pl", "48"], ["Portugal", "pt", "351"], ["Puerto Rico", "pr", "1", 3, ["787", "939"]], ["Qatar (‫قطر‬‎)", "qa", "974"], ["Réunion (La Réunion)", "re", "262", 0], ["Romania (România)", "ro", "40"], ["Russia (Россия)", "ru", "7", 0], ["Rwanda", "rw", "250"], ["Saint Barthélemy", "bl", "590", 1], ["Saint Helena", "sh", "290"], ["Saint Kitts and Nevis", "kn", "1869"], ["Saint Lucia", "lc", "1758"], ["Saint Martin (Saint-Martin (partie française))", "mf", "590", 2], ["Saint Pierre and Miquelon (Saint-Pierre-et-Miquelon)", "pm", "508"], ["Saint Vincent and the Grenadines", "vc", "1784"], ["Samoa", "ws", "685"], ["San Marino", "sm", "378"], ["São Tomé and Príncipe (São Tomé e Príncipe)", "st", "239"], ["Saudi Arabia (‫المملكة العربية السعودية‬‎)", "sa", "966"], ["Senegal (Sénégal)", "sn", "221"], ["Serbia (Србија)", "rs", "381"], ["Seychelles", "sc", "248"], ["Sierra Leone", "sl", "232"], ["Singapore", "sg", "65"], ["Sint Maarten", "sx", "1721"], ["Slovakia (Slovensko)", "sk", "421"], ["Slovenia (Slovenija)", "si", "386"], ["Solomon Islands", "sb", "677"], ["Somalia (Soomaaliya)", "so", "252"], ["South Africa", "za", "27"], ["South Korea (대한민국)", "kr", "82"], ["South Sudan (‫جنوب السودان‬‎)", "ss", "211"], ["Spain (España)", "es", "34"], ["Sri Lanka (ශ්‍රී ලංකාව)", "lk", "94"], ["Sudan (‫السودان‬‎)", "sd", "249"], ["Suriname", "sr", "597"], ["Svalbard and Jan Mayen", "sj", "47", 1], ["Swaziland", "sz", "268"], ["Sweden (Sverige)", "se", "46"], ["Switzerland (Schweiz)", "ch", "41"], ["Syria (‫سوريا‬‎)", "sy", "963"], ["Taiwan (台灣)", "tw", "886"], ["Tajikistan", "tj", "992"], ["Tanzania", "tz", "255"], ["Thailand (ไทย)", "th", "66"], ["Timor-Leste", "tl", "670"], ["Togo", "tg", "228"], ["Tokelau", "tk", "690"], ["Tonga", "to", "676"], ["Trinidad and Tobago", "tt", "1868"], ["Tunisia (‫تونس‬‎)", "tn", "216"], ["Turkey (Türkiye)", "tr", "90"], ["Turkmenistan", "tm", "993"], ["Turks and Caicos Islands", "tc", "1649"], ["Tuvalu", "tv", "688"], ["U.S. Virgin Islands", "vi", "1340"], ["Uganda", "ug", "256"], ["Ukraine (Україна)", "ua", "380"], ["United Arab Emirates (‫الإمارات العربية المتحدة‬‎)", "ae", "971"], ["United Kingdom", "gb", "44", 0], ["United States", "us", "1", 0], ["Uruguay", "uy", "598"], ["Uzbekistan (Oʻzbekiston)", "uz", "998"], ["Vanuatu", "vu", "678"], ["Vatican City (Città del Vaticano)", "va", "39", 1], ["Venezuela", "ve", "58"], ["Vietnam (Việt Nam)", "vn", "84"], ["Wallis and Futuna (Wallis-et-Futuna)", "wf", "681"], ["Western Sahara (‫الصحراء الغربية‬‎)", "eh", "212", 1], ["Yemen (‫اليمن‬‎)", "ye", "967"], ["Zambia", "zm", "260"], ["Zimbabwe", "zw", "263"], ["Åland Islands", "ax", "358", 1]]';
        $countries = json_decode($json_countries);
        $retrun_array = array();

        foreach ($countries as $country) {
            $option = array(
                'label' => $country[0] . ' [+' . $country[2] . ']',
                'value' => $country[2],
                'code' => $country[1],
                'is_placeholder' => false,
            );
            array_push($retrun_array, $option);
        }

        return $retrun_array;
    }

    function setting_instructions()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        $display = 'inherit';
        if (!$options['idehweb_phone_number']) {
            $display = 'none';
        }
        echo '<div> <p>' . __('make a page and name it login, put the shortcode inside it, now you have a login page!', $this->textdomain) . '</p>
		<p><code>[idehweb_lwp]</code></p>';
        echo '<div> <p>' . __('For showing metas of user for example in profile page, like: showing phone number, username, email, nicename', $this->textdomain) . '</p>
		<p><code>[idehweb_lwp_metas nicename="false" username="false" phone_number="true" email="false"]</code></p>
		<p><a href="https://idehweb.com/product/login-with-phone-number-in-wordpress/" target="_blank" class="lwp_more_help">' . __('Need more help?', $this->textdomain) . '</a></p>
		</div>';
    }

    function setting_country_code()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_country_codes'])) $options['idehweb_country_codes'] = ["93"];
        $country_codes = $this->get_country_code_options();
//        print_r($options['idehweb_country_codes']);

        ?>
        <select name="idehweb_lwp_settings[idehweb_country_codes][]" id="idehweb_country_codes" multiple>
            <?php
            foreach ($country_codes as $country) {
                $rr = in_array($country["value"], $options['idehweb_country_codes']);
                echo '<option value="' . $country["value"] . '" ' . ($rr ? ' selected="selected"' : '') . '>' . $country['label'] . '</option>';
            }
            ?>
        </select>
        <?php

    }

    function setting_default_username()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_default_username'])) $options['idehweb_default_username'] = 'user';

//        print_r($options['idehweb_country_codes']);

        echo '<input id="lwp_default_username" type="text" name="idehweb_lwp_settings[idehweb_default_username]" class="regular-text" value="' . esc_attr($options['idehweb_default_username']) . '" />
		<p class="description">' . __('Default username', $this->textdomain) . '</p>';

    }

    function setting_default_nickname()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_default_nickname'])) $options['idehweb_default_nickname'] = 'user';

//        print_r($options['idehweb_country_codes']);

        echo '<input id="lwp_default_nickname" type="text" name="idehweb_lwp_settings[idehweb_default_nickname]" class="regular-text" value="' . esc_attr($options['idehweb_default_nickname']) . '" />
		<p class="description">' . __('Default nickname', $this->textdomain) . '</p>';

    }


    function setting_buy_credit()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        if (!isset($options['idehweb_website_url'])) $options['idehweb_website_url'] = '';
        if (!isset($options['idehweb_phone_number_ccode'])) $options['idehweb_phone_number_ccode'] = '';
        $display = 'inherit';
        if (!$options['idehweb_phone_number']) {
            $display = 'none';
        }
        ?>

        <div class="creditor">
            <button type="button" class="button-primary loiuyt"
                    value="<?php _e('Check credit', $this->textdomain); ?>"><?php _e('Check credit', $this->textdomain); ?></button>
            <span class="cp"></span>

            <button type="button" class="button-primary refreshShop"
                    value="<?php _e('Refresh', $this->textdomain); ?>"><?php _e('Refresh', $this->textdomain); ?></button>
            <span class="df">
                <?php echo $options['idehweb_website_url']; ?>
                <!--                <a href="#" class="lwpchangePhoneNumber">-->
                <?php //_e('change', $this->textdomain);
                //
                ?>
                <!--            </a>-->
            </span>
        </div>


        <div class="chargeAccount">

        </div>
        <?php
    }

    function settings_validate($input)
    {

        return $input;
    }

    function removePhpComments($str, $preserveWhiteSpace = true)
    {
        $commentTokens = [
            \T_COMMENT,
            \T_DOC_COMMENT,
        ];
        $tokens = token_get_all($str);


        if (true === $preserveWhiteSpace) {
            $lines = explode(PHP_EOL, $str);
        }


        $s = '';
        foreach ($tokens as $token) {
            if (is_array($token)) {
                if (in_array($token[0], $commentTokens)) {
                    if (true === $preserveWhiteSpace) {
                        $comment = $token[1];
                        $lineNb = $token[2];
                        $firstLine = $lines[$lineNb - 1];
                        $p = explode(PHP_EOL, $comment);
                        $nbLineComments = count($p);
                        if ($nbLineComments < 1) {
                            $nbLineComments = 1;
                        }
                        $firstCommentLine = array_shift($p);

                        $isStandAlone = (trim($firstLine) === trim($firstCommentLine));

                        if (false === $isStandAlone) {
                            if (2 === $nbLineComments) {
                                $s .= PHP_EOL;
                            }

                            continue; // just remove inline comments
                        }

                        // stand alone case
                        $s .= str_repeat(PHP_EOL, $nbLineComments - 1);
                    }
                    continue;
                }
                $token = $token[1];
            }

            $s .= $token;
        }
        return $s;
    }

    function enqueue_scripts()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_redirect_url'])) $options['idehweb_redirect_url'] = home_url();
        if (!isset($options['idehweb_default_gateways'])) $options['idehweb_default_gateways'] = 'firebase';
        if (!isset($options['idehweb_use_custom_gateway'])) $options['idehweb_use_custom_gateway'] = '1';
        if (!isset($options['idehweb_firebase_api'])) $options['idehweb_firebase_api'] = '';
        if (!isset($options['idehweb_firebase_config'])) $options['idehweb_firebase_config'] = '';
        if (!isset($options['idehweb_enable_timer_on_sending_sms'])) $options['idehweb_enable_timer_on_sending_sms'] = '1';
        if (!isset($options['idehweb_timer_count'])) $options['idehweb_timer_count'] = '60';
//        if (!isset($options['idehweb_default_gateways'])) $options['idehweb_default_gateways'] = '';
        $localize = array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'redirecturl' => $options['idehweb_redirect_url'],
            'UserId' => 0,
            'loadingmessage' => __('please wait...', $this->textdomain),
            'timer' => $options['idehweb_enable_timer_on_sending_sms'],
            'timer_count' => $options['idehweb_timer_count'],
        );

        wp_enqueue_style('idehweb-lwp', plugins_url('/styles/login-with-phonenumber.css', __FILE__));

        wp_enqueue_script('idehweb-lwp-validate-script', plugins_url('/scripts/jquery.validate.js', __FILE__), array('jquery'));


        wp_enqueue_script('idehweb-lwp', plugins_url('/scripts/login-with-phonenumber.js', __FILE__), array('jquery'));


        if ($options['idehweb_use_custom_gateway'] == '1' && $options['idehweb_default_gateways'] === 'firebase') {
            wp_enqueue_script('lwp-firebase', 'https://www.gstatic.com/firebasejs/7.21.0/firebase-app.js', array(), false, true);
            wp_enqueue_script('lwp-firebase-auth', 'https://www.gstatic.com/firebasejs/7.21.0/firebase-auth.js', array(), false, true);
            wp_enqueue_script('lwp-firebase-sender', plugins_url('/scripts/firebase-sender.js', __FILE__), array('jquery'));
//            wp_add_inline_script('idehweb-lwp', '' . htmlspecialchars_decode($options['idehweb_firebase_config']));
//            wp_add_inline_script('idehweb-lwp', 'lwp_localize.firebase.config = ' . htmlspecialchars_decode($options['idehweb_firebase_config']));
//            $localize['firebase_config'] = $this->removePhpComments($options['idehweb_firebase_config']);


            $localize['firebase_api'] = $options['idehweb_firebase_api'];
        }
        wp_localize_script('idehweb-lwp', 'idehweb_lwp', $localize);
        if ($options['idehweb_use_custom_gateway'] == '1' && $options['idehweb_default_gateways'] === 'firebase') {

            wp_add_inline_script('idehweb-lwp', '' . htmlspecialchars_decode($options['idehweb_firebase_config']));
        }

    }

    function idehweb_lwp_metas($vals)
    {

        $atts = shortcode_atts(array(
            'email' => false,
            'phone_number' => true,
            'username' => false,
            'nicename' => false

        ), $vals);
        ob_start();
        $user = wp_get_current_user();
        if (!isset($atts['username'])) $atts['username'] = false;
        if (!isset($atts['nicename'])) $atts['nicename'] = false;
        if (!isset($atts['email'])) $atts['email'] = false;
        if (!isset($atts['phone_number'])) $atts['phone_number'] = true;
        if ($atts['username'] == 'true') {
            echo '<div class="lwp user_login">' . $user->user_login . '</div>';
        }
        if ($atts['nicename'] == 'true') {
            echo '<div class="lwp user_nicename">' . $user->user_nicename . '</div>';

        }
        if ($atts['email'] == 'true') {
            echo '<div class="lwp user_email">' . $user->user_email . '</div>';

        }
        if ($atts['phone_number'] == 'true') {
            echo '<div class="lwp user_email">' . get_user_meta($user->ID, 'phone_number', true) . '</div>';
        }
        return ob_get_clean();
    }

    function shortcode($atts)
    {

        extract(shortcode_atts(array(
            'redirect_url' => ''
        ), $atts));
        ob_start();
        $options = get_option('idehweb_lwp_settings');
        $localizationـoptions = get_option('idehweb_lwp_settings_localization');
        if (!isset($options['idehweb_sms_login'])) $options['idehweb_sms_login'] = '1';
        if (!isset($options['idehweb_enable_accept_terms_and_condition'])) $options['idehweb_enable_accept_terms_and_condition'] = '1';
        if (!isset($options['idehweb_term_and_conditions_text'])) $options['idehweb_term_and_conditions_text'] = '';
        if (!isset($options['idehweb_email_login'])) $options['idehweb_email_login'] = '1';
        if (!isset($options['idehweb_password_login'])) $options['idehweb_password_login'] = '1';
        if (!isset($options['idehweb_redirect_url'])) $options['idehweb_redirect_url'] = '';
        if (!isset($options['idehweb_country_codes'])) $options['idehweb_country_codes'] = [];
        if (!isset($options['idehweb_position_form'])) $options['idehweb_position_form'] = '0';
        if (!isset($localizationـoptions['idehweb_localization_placeholder_of_phonenumber_field'])) $localizationـoptions['idehweb_localization_placeholder_of_phonenumber_field'] = '';
        if (!isset($localizationـoptions['idehweb_localization_title_of_login_form'])) $localizationـoptions['idehweb_localization_title_of_login_form'] = '';
        if (!isset($localizationـoptions['idehweb_localization_title_of_login_form_email'])) $localizationـoptions['idehweb_localization_title_of_login_form_email'] = '';

        $class = '';
        if ($options['idehweb_position_form'] == '1') {
            $class = 'lw-sticky';
        }
        $is_user_logged_in = is_user_logged_in();
        if (!$is_user_logged_in) {
            ?>
            <a id="show_login" class="show_login"
               style="display: none"
               data-sticky="<?php echo esc_attr($options['idehweb_position_form']); ?>"><?php echo __('login', $this->textdomain); ?></a>
            <div class="lwp_forms_login <?php echo esc_attr($class); ?>">
                <?php
                if ($options['idehweb_sms_login']) {
                    if ($options['idehweb_email_login'] && $options['idehweb_sms_login']) {
                        $cclass = 'display:none';
                    } else if (!$options['idehweb_email_login'] && $options['idehweb_sms_login']) {
                        $cclass = 'display:block';
                    }
                    ?>
                    <form id="lwp_login" class="ajax-auth" action="login" style="<?php echo $cclass; ?>" method="post">

                        <div class="lh1"><?php echo isset($localizationـoptions['idehweb_localization_status']) ? esc_html($localizationـoptions['idehweb_localization_title_of_login_form']) : (__('Login / register', $this->textdomain)); ?></div>
                        <p class="status"></p>
                        <?php wp_nonce_field('ajax-login-nonce', 'security'); ?>
                        <div class="lwp-form-box">
                            <label class="lwp_labels"
                                   for="lwp_username"><?php echo __('Phone number', $this->textdomain); ?></label>
                            <?php
                            //                    $country_codes = $this->get_country_code_options();
                            ?>
                            <div class="lwp-form-box-bottom">
                                <div class="lwp_country_codes_wrap">
                                    <select id="lwp_country_codes">
                                        <?php
                                        foreach ($options['idehweb_country_codes'] as $country) {
//                            $rr=in_array($country["value"],$options['idehweb_country_codes']);
                                            echo '<option value="' . $country . '">+' . $country . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <input type="number" class="required lwp_username the_lwp_input" name="lwp_username"
                                       placeholder="<?php echo ($localizationـoptions['idehweb_localization_placeholder_of_phonenumber_field']) ? sanitize_text_field($localizationـoptions['idehweb_localization_placeholder_of_phonenumber_field']) : (__('9*********', $this->textdomain)); ?>">
                            </div>
                        </div>
                        <?php if ($options['idehweb_enable_accept_terms_and_condition'] == '1') { ?>
                            <div class="accept_terms_and_conditions">
                                <input class="required lwp_check_box" type="checkbox" name="lwp_accept_terms"
                                       checked="checked">
                                <span class="accept_terms_and_conditions_text"><?php echo($options['idehweb_term_and_conditions_text']); ?></span>
                            </div>
                        <?php } ?>
                        <button class="submit_button auth_phoneNumber" type="submit">
                            <?php echo __('Submit', $this->textdomain); ?>
                        </button>
                        <?php
                        if ($options['idehweb_email_login']) {
                            ?>
                            <button class="submit_button auth_with_email secondaryccolor" type="button">
                                <?php echo __('Login with email', $this->textdomain); ?>
                            </button>
                        <?php } ?>
                        <a class="close" href="">(x)</a>
                    </form>
                <?php } ?>
                <?php
                if ($options['idehweb_email_login']) {
//                    if($options['idehweb_email_login'] && $options['idehweb_sms_login']){
                    $ecclass = 'display:block';
//                    }
                    ?>
                    <form id="lwp_login_email" class="ajax-auth" action="loginemail" style="<?php echo $ecclass; ?>"
                          method="post">

                        <div class="lh1"><?php echo isset($localizationـoptions['idehweb_localization_status']) ? esc_html($localizationـoptions['idehweb_localization_title_of_login_form_email']) : (__('Login / register', $this->textdomain)); ?></div>
                        <p class="status"></p>
                        <?php wp_nonce_field('ajax-login-nonce', 'security'); ?>
                        <label class="lwp_labels"
                               for="lwp_email"><?php echo __('Your email:', $this->textdomain); ?></label>
                        <input type="email" class="required lwp_email the_lwp_input" name="lwp_email"
                               placeholder="<?php echo __('Please enter your email', $this->textdomain); ?>">
                        <?php if ($options['idehweb_enable_accept_terms_and_condition'] == '1') { ?>
                            <div class="accept_terms_and_conditions">
                                <input class="required lwp_check_box lwp_accept_terms_email" type="checkbox"
                                       name="lwp_accept_terms_email" checked="checked">
                                <span class="accept_terms_and_conditions_text"><?php echo($options['idehweb_term_and_conditions_text']); ?></span>
                            </div>
                        <?php } ?>
                        <button class="submit_button auth_email" type="submit">
                            <?php echo __('Submit', $this->textdomain); ?>
                        </button>
                        <?php
                        if ($options['idehweb_sms_login']) {
                            ?>
                            <button class="submit_button auth_with_phoneNumber secondaryccolor" type="button">
                                <?php echo __('Login with phone number', $this->textdomain); ?>
                            </button>
                        <?php } ?>
                        <a class="close" href="">(x)</a>
                    </form>
                <?php } ?>

                <form id="lwp_activate" class="ajax-auth" action="activate" method="post">
                    <div class="lh1"><?php echo __('Activation', $this->textdomain); ?></div>
                    <p class="status"></p>
                    <?php wp_nonce_field('ajax-login-nonce', 'security'); ?>
                    <div class="lwp_top_activation">
                        <div class="lwp_timer"></div>


                    </div>
                    <label class="lwp_labels"
                           for="lwp_scode"><?php echo __('Security code', $this->textdomain); ?></label>
                    <input type="text" class="required lwp_scode" name="lwp_scode" placeholder="ـ ـ ـ ـ ـ ـ">

                    <button class="submit_button auth_secCode">
                        <?php echo __('Activate', $this->textdomain); ?>
                    </button>
                    <button class="submit_button lwp_didnt_r_c lwp_disable" type="button">
                        <?php echo __('Send code again', $this->textdomain); ?>
                    </button>
                    <hr class="lwp_line"/>
                    <div class="lwp_bottom_activation">

                        <a class="lwp_change_pn" href="#">
                            <?php echo __('Change phone number?', $this->textdomain); ?>
                        </a>
                        <a class="lwp_change_el" href="#">
                            <?php echo __('Change email?', $this->textdomain); ?>
                        </a>
                    </div>


                    <a class="close" href="">(x)</a>
                </form>

                <?php
                if ($options['idehweb_password_login']) {
                    ?>
                    <form id="lwp_update_password" class="ajax-auth" action="update_password" method="post">

                        <div class="lh1"><?php echo __('Update password', $this->textdomain); ?></div>
                        <p class="status"></p>
                        <?php wp_nonce_field('ajax-login-nonce', 'security'); ?>
                        <label class="lwp_labels"
                               for="lwp_email"><?php echo __('Enter new password:', $this->textdomain); ?></label>
                        <input type="password" class="required lwp_up_password" name="lwp_up_password"
                               placeholder="<?php echo __('Please choose a password', $this->textdomain); ?>">

                        <button class="submit_button auth_email" type="submit">
                            <?php echo __('Update', $this->textdomain); ?>
                        </button>
                        <a class="close" href="">(x)</a>
                    </form>
                    <form id="lwp_enter_password" class="ajax-auth" action="enter_password" method="post">

                        <div class="lh1"><?php echo __('Enter password', $this->textdomain); ?></div>
                        <p class="status"></p>
                        <?php wp_nonce_field('ajax-login-nonce', 'security'); ?>
                        <label class="lwp_labels"
                               for="lwp_email"><?php echo __('Your password:', $this->textdomain); ?></label>
                        <input type="password" class="required lwp_auth_password" name="lwp_auth_password"
                               placeholder="<?php echo __('Please enter your password', $this->textdomain); ?>">

                        <button class="submit_button login_with_pass" type="submit">
                            <?php echo __('Login', $this->textdomain); ?>
                        </button>
                        <button class="submit_button forgot_password" type="button">
                            <?php echo __('Forgot password', $this->textdomain); ?>
                        </button>
                        <hr class="lwp_line"/>
                        <div class="lwp_bottom_activation">

                            <a class="lwp_change_pn" href="#">
                                <?php echo __('Change phone number?', $this->textdomain); ?>
                            </a>
                            <a class="lwp_change_el" href="#">
                                <?php echo __('Change email?', $this->textdomain); ?>
                            </a>
                        </div>

                        <!--                    --><?php
                        //                    if ($options['idehweb_sms_login']) {
                        //                        ?>
                        <!--                        <button class="submit_button auth_with_phoneNumber" type="button">-->
                        <!--                            --><?php //echo __('Login with phone number', $this->textdomain); ?>
                        <!--                        </button>-->
                        <!--                    --><?php //} ?>
                        <a class="close" href="">(x)</a>
                    </form>
                <?php } ?>
            </div>
            <?php
        } else {
            if ($options['idehweb_redirect_url'])
                wp_redirect($options['idehweb_redirect_url']);
        }
        return ob_get_clean();
    }

    function phone_number_exist($phone_number)
    {
        $args = array(
            'meta_query' => array(
                array(
                    'key' => 'phone_number',
                    'value' => $phone_number,
                    'compare' => '='
                )
            )
        );

        $member_arr = get_users($args);
        if ($member_arr && $member_arr[0])
            return $member_arr[0]->ID;
        else
            return 0;

    }

    function lwp_ajax_login()
    {
        $usesrname = sanitize_text_field($_GET['username']);
        $options = get_option('idehweb_lwp_settings');

        if (preg_replace('/^(\-){0,1}[0-9]+(\.[0-9]+){0,1}/', '', $usesrname) == "") {
            $phone_number = ltrim($usesrname, '0');
            $phone_number = substr($phone_number, 0, 15);
//echo $phone_number;
//die();
            if (strlen($phone_number) < 10) {
                echo json_encode([
                    'success' => false,
                    'phone_number' => $phone_number,
                    'message' => __('phone number is wrong!', $this->textdomain)
                ]);
                die();
            }
            $username_exists = $this->phone_number_exist($phone_number);
//            $registration = get_site_option('registration');
            if (!isset($options['idehweb_user_registration'])) $options['idehweb_user_registration'] = '1';
            $registration = $options['idehweb_user_registration'];
            $is_multisite = is_multisite();
            if ($is_multisite) {
                if ($registration == '0' && !$username_exists) {
                    echo json_encode([
                        'success' => false,
                        'phone_number' => $usesrname,
                        'registeration' => $registration,
                        'is_multisite' => $is_multisite,
                        'username_exists' => $username_exists,
                        'message' => __('users can not register!', $this->textdomain)
                    ]);
                    die();
                }
            } else {
                if ($registration == '0') {
//                if (!$username_exists) {
                    echo json_encode([
                        'success' => false,
                        'phone_number' => $usesrname,
                        'registeration' => $registration,
                        'is_multisite' => $is_multisite,
                        'username_exists' => $username_exists,
                        'message' => __('users can not register!', $this->textdomain)
                    ]);
                    die();
//                }
                }
            }
            $userRegisteredNow = false;
            if (!$username_exists) {
                $info = array();
                $info['user_login'] = $this->generate_username($phone_number);
                $info['user_nicename'] = $info['nickname'] = $info['display_name'] = $this->generate_nickname();
                $info['user_url'] = sanitize_text_field($_GET['website']);
                $user_register = wp_insert_user($info);
                if (is_wp_error($user_register)) {
                    $error = $user_register->get_error_codes();

                    if (in_array('empty_user_login', $error)) {
                        echo json_encode([
                            'success' => false,
                            'phone_number' => $phone_number,
                            'message' => __($user_register->get_error_message('empty_user_login'))
                        ]);
                        die();
                    } elseif (in_array('existing_user_login', $error)) {
                        echo json_encode([
                            'success' => false,
                            'phone_number' => $phone_number,
                            'message' => __('This username is already registered.', $this->textdomain)
                        ]);
                        die();
                    } elseif (in_array('existing_user_email', $error)) {
                        echo json_encode([
                            'success' => false,
                            'phone_number' => $phone_number,
                            'message' => __('This email address is already registered.', $this->textdomain)
                        ]);
                        die();
                    }
                    die();
                } else {
                    add_user_meta($user_register, 'phone_number', sanitize_user($phone_number));
                    update_user_meta($user_register, '_billing_phone', sanitize_user($phone_number));
                    update_user_meta($user_register, 'billing_phone', sanitize_user($phone_number));
//                    update_user_meta($user_register, '_shipping_phone', sanitize_user($phone_number));
//                    update_user_meta($user_register, 'shipping_phone', sanitize_user($phone_number));
                    $userRegisteredNow = true;
                    add_user_meta($user_register, 'updatedPass', 0);
                    $username_exists = $user_register;

                }


            }
            $showPass = false;
            $log = '';


//            $options = get_option('idehweb_lwp_settings');
            if (!isset($options['idehweb_password_login'])) $options['idehweb_password_login'] = '1';
            $options['idehweb_password_login'] = (bool)(int)$options['idehweb_password_login'];
            if (!$options['idehweb_password_login']) {
                $log = $this->lwp_generate_token($username_exists, $phone_number);

            } else {
                if (!$userRegisteredNow) {
                    $showPass = true;
                } else {
                    $log = $this->lwp_generate_token($username_exists, $phone_number);
                }
            }
            echo json_encode([
                'success' => true,
                'ID' => $username_exists,
                'phone_number' => $phone_number,
                'showPass' => $showPass,
//                '$userRegisteredNow' => $userRegisteredNow,
//                '$userRegisteredNow1' => $options['idehweb_password_login'],
                'authWithPass' => (bool)(int)$options['idehweb_password_login'],
                'message' => __('Sms sent successfully!', $this->textdomain),
                'log' => $log
            ]);
            die();

        } else {
            echo json_encode([
                'success' => false,
                'phone_number' => $usesrname,
                'message' => __('phone number is wrong!', $this->textdomain)
            ]);
            die();
        }
    }

    function lwp_verify_domain()
    {

        echo json_encode([
            'success' => true
        ]);
        die();
    }

    function lwp_forgot_password()
    {
        $log = '';
        if ($_GET['email'] != '' && $_GET['ID']) {
            $log = $this->lwp_generate_token($_GET['ID'], $_GET['email'], true);

        }
        if ($_GET['phone_number'] != '' && $_GET['ID'] != '') {
            $log = $this->lwp_generate_token($_GET['ID'], $_GET['phone_number']);

//
        }
        update_user_meta($_GET['ID'], 'updatedPass', '0');


//        $options = get_option('idehweb_lwp_settings');
//        if (!isset($options['idehweb_smsbharti_api_key'])) $options['idehweb_smsbharti_api_key'] = '';
//        if (!isset($options['idehweb_smsbharti_from'])) $options['idehweb_smsbharti_from'] = '';
//        if (!isset($options['idehweb_smsbharti_template_id'])) $options['idehweb_smsbharti_template_id'] = '';
//        if (!isset($options['idehweb_smsbharti_routeid'])) $options['idehweb_smsbharti_routeid'] = '';
//        $api_key = $options['idehweb_smsbharti_api_key'];
//        $from = $options['idehweb_smsbharti_user_key'];
//        $template_id = $options['idehweb_smsbharti_template_id'];
//        $routeid = $options['idehweb_smsbharti_routeid'];
//        $phone = ltrim($_GET['phone_number'], '0');
//        $phone = substr($phone, 0, 12);
//        $phone = substr($phone, 2, 10);
//        $text='received?';
//        $url = "http://webmsg.smsbharti.com/app/smsapi/index.php?key=".$api_key."&campaign=0&routeid=".$routeid."&type=text&contacts=".$phone."&senderid=".$from."&msg=".$text."&template_id=".$template_id;
////        $auth = base64_encode( $this->sid . ':' . $this->token );
////        return ['ytr'=>$url];
//        $response = wp_safe_remote_get(
//            $url,
//            array(
//                'method' => 'GET',
//                'timeout' => 60,
//                'redirection' => 5,
////				'httpversion' => '1.1',
//                'blocking' => true,
//                'headers' => array(),
////                'headers' => [
////                    'Authorization' => "Basic $auth"
////                ],
//                'body' => array(),
//                'cookies' => array(),
//            )
//        );

        echo json_encode([
            'success' => true,
            'ID' => $_GET['ID'],
            'log' => $log,
//            '$url' => $url,
            'message' => __('Update password', $this->textdomain)
        ]);
//
        die();
//        }
    }

    function lwp_enter_password_action()
    {


        if ($_GET['email'] != '') {
            $user = get_user_by('email', $_GET['email']);

        }
        if ($_GET['ID'] != '') {
            $user = get_user_by('ID', $_GET['ID']);

        }
        $creds = array(
            'user_login' => $user->user_login,
            'user_password' => $_GET['password'],
            'remember' => true
        );

        $user = wp_signon($creds, false);

        if (is_wp_error($user)) {
            echo json_encode([
                'success' => false,
                'ID' => $user->ID,
                'err' => $user->get_error_message(),
                'message' => __('Password is incorrect!', $this->textdomain)
            ]);
            die();
        } else {

            echo json_encode([
                'success' => true,
                'ID' => $user->ID,
                'message' => __('Redirecting...', $this->textdomain)
            ]);

            die();
        }
    }

    function lwp_update_password_action()
    {
        $user = wp_get_current_user();
        if ($user) {
            wp_clear_auth_cookie();
            wp_update_user([
                'ID' => $user->ID,
                'user_pass' => $_GET['password']
            ]);
            update_user_meta($user->ID, 'updatedPass', 1);
            wp_set_current_user($user->ID); // Set the current user detail
            wp_set_auth_cookie($user->ID); // Set auth details in cookie
            echo json_encode([
                'success' => true,
                'message' => __('Password set successfully! redirecting...', $this->textdomain)
            ]);

            die();
        } else {

            echo json_encode([
                'success' => false,
                'message' => __('User not found', $this->textdomain)
            ]);

            die();
        }
    }

    function lwp_ajax_login_with_email()

    {
        $email = sanitize_text_field($_GET['email']);
        $userRegisteredNow = false;

        $options = get_option('idehweb_lwp_settings');

        if (!isset($options['idehweb_user_registration'])) $options['idehweb_user_registration'] = '1';
        $registration = $options['idehweb_user_registration'];


        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_exists = email_exists($email);
            if (!$email_exists) {
                if ($registration == '0') {
                    echo json_encode([
                        'success' => false,
                        'email' => $email,
                        'registeration' => $registration,
                        'email_exists' => $email_exists,
                        'message' => __('users can not register!', $this->textdomain)
                    ]);
                    die();
                }
                $info = array();
                $info['user_email'] = sanitize_user($email);
                $info['user_nicename'] = $info['nickname'] = $info['display_name'] = $this->generate_nickname();
                $info['user_url'] = sanitize_text_field($_GET['website']);
                $info['user_login'] = $this->generate_username($email);
                $user_register = wp_insert_user($info);
                if (is_wp_error($user_register)) {
                    $error = $user_register->get_error_codes();

                    echo json_encode([
                        'success' => false,
                        'email' => $email,
                        '$email_exists' => $email_exists,
                        '$error' => $error,
                        'message' => __('This email address is already registered.', $this->textdomain)
                    ]);

                    die();
                } else {
                    $userRegisteredNow = true;
                    add_user_meta($user_register, 'updatedPass', 0);
                    $email_exists = $user_register;
                }


            }
//            $user = get_user_by('ID', $email_exists);
//            $password = $user->data->user_pass;
            $log = '';
            $showPass = false;
            if (!$userRegisteredNow) {
                $showPass = true;
            } else {
                $log = $this->lwp_generate_token($email_exists, $email, true);
            }
//            $options = get_option('idehweb_lwp_settings');
            if (!isset($options['idehweb_password_login'])) $options['idehweb_password_login'] = '1';
            $options['idehweb_password_login'] = (bool)(int)$options['idehweb_password_login'];
            if (!$options['idehweb_password_login']) {
                $log = $this->lwp_generate_token($email_exists, $email, true);


            }
            echo json_encode([
                'success' => true,
                'ID' => $email_exists,
                'log' => $log,
//                '$user' => $user,
                'showPass' => $showPass,
                'authWithPass' => (bool)(int)$options['idehweb_password_login'],

                'email' => $email,
                'message' => __('Email sent successfully!', $this->textdomain)
            ]);
            die();

        } else {
            echo json_encode([
                'success' => false,
                'email' => $email,
                'message' => __('email is wrong!', $this->textdomain)
            ]);
            die();
        }
    }

    function lwp_rest_api_stn_auth_customer($data)
    {

        if (preg_replace('/^(\-){0,1}[0-9]+(\.[0-9]+){0,1}/', '', $data['accode']) == "") {
            $accode = ltrim($data['accode'], '0');
            $accode = substr($accode, 0, 15);
            return [

                'success' => true
            ];
        } else {
            return [
                'success' => false
            ];
        }


    }

    function lwp_register_rest_route()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_token'])) $options['idehweb_token'] = '';

//        if (empty($options['idehweb_token'])) {

        register_rest_route('authorizelwp', '/(?P<accode>[a-zA-Z0-9_-]+)', array(
            'methods' => 'GET',
            'callback' => array(&$this, 'lwp_rest_api_stn_auth_customer'),
            'permission_callback' => '__return_true'
        ));

//        }
    }


    function lwp_generate_token($user_id, $contact, $send_email = false)
    {
        $six_digit_random_number = mt_rand(100000, 999999);
        update_user_meta($user_id, 'activation_code', $six_digit_random_number);
        if ($send_email) {
            $wp_mail = wp_mail($contact, 'activation code', __('your activation code: ', $this->textdomain) . $six_digit_random_number);
            return $wp_mail;
        } else {
            return $this->send_sms($contact, $six_digit_random_number);
        }
    }

    function generate_username($defU = '')
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_default_username'])) $options['idehweb_default_username'] = 'user';
        if (!isset($options['idehweb_use_phone_number_for_username'])) $options['idehweb_use_phone_number_for_username'] = '0';
        if ($options['idehweb_use_phone_number_for_username'] == '0') {
            $ulogin = $options['idehweb_default_username'];

        } else {
            $ulogin = $defU;
        }

        // make user_login unique so WP will not return error
        $check = username_exists($ulogin);
        if (!empty($check)) {
            $suffix = 2;
            while (!empty($check)) {
                $alt_ulogin = $ulogin . '-' . $suffix;
                $check = username_exists($alt_ulogin);
                $suffix++;
            }
            $ulogin = $alt_ulogin;
        }

        return $ulogin;
    }

    function generate_nickname()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_default_nickname'])) $options['idehweb_default_nickname'] = 'user';


        return $options['idehweb_default_nickname'];
    }

    function send_sms($phone_number, $code)
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_use_custom_gateway'])) $options['idehweb_use_custom_gateway'] = '1';
        if (!isset($options['idehweb_default_gateways'])) $options['idehweb_default_gateways'] = 'firebase';
        if ($options['idehweb_use_custom_gateway'] == '1') {
            if ($options['idehweb_default_gateways'] == 'zenziva') {
                $zenziva = new LWP_Zenziva_Api();
                return $zenziva->lwp_send_sms($phone_number, $code);
            } else if ($options['idehweb_default_gateways'] == 'infobip') {
                $infobip = new LWP_Infobip_Api();
                return $infobip->lwp_send_sms($phone_number, $code);
            } else if ($options['idehweb_default_gateways'] == 'raygansms') {
                $raygansms = new LWP_Raygansms_Api();
                return $raygansms->lwp_send_sms($phone_number, $code);
            } else if ($options['idehweb_default_gateways'] == 'smsbharti') {
                $smsbharti = new LWP_Smsbharti_Api();
                return $smsbharti->lwp_send_sms($phone_number, $code);
            } else if ($options['idehweb_default_gateways'] == 'twilio') {
                $twilio = new LWP_Twilio_Api();
                return $twilio->lwp_send_sms($phone_number, $code);
            } else if ($options['idehweb_default_gateways'] == 'mshastra') {
                $mshastra = new LWP_Mshastra_Api();
                return $mshastra->lwp_send_sms($phone_number, $code);
            } else if ($options['idehweb_default_gateways'] == 'taqnyat') {
                $taqnyat = new LWP_Taqnyat_Api();
                return $taqnyat->lwp_send_sms($phone_number, $code);
            } else {
                return true;
            }
        } else {
//        $smsUrl = "https://zoomiroom.com/customer/sms/" . $options['idehweb_token'] . "/" . $phone_number . "/" . $code;
            $response = wp_safe_remote_post("https://zoomiroom.com/customer/sms/", [
                'timeout' => 60,
                'redirection' => 1,
                'blocking' => true,
                'headers' => array('Content-Type' => 'application/json',
                    'token' => $options['idehweb_token']),
                'body' => wp_json_encode([
                    'phoneNumber' => $phone_number,
                    'message' => $code
                ])
            ]);
            $body = wp_remote_retrieve_body($response);
            return $body;
        }
//        $response = wp_remote_get($smsUrl);
//        wp_remote_retrieve_body($response);

    }

    function lwp_ajax_register()
    {
        $options = get_option('idehweb_lwp_settings');
        if (!isset($options['idehweb_default_gateways'])) $options['idehweb_default_gateways'] = 'firebase';
        if (!isset($options['idehweb_use_custom_gateway'])) $options['idehweb_use_custom_gateway'] = '1';

        if (isset($_GET['phone_number'])) {
            $phoneNumber = sanitize_text_field($_GET['phone_number']);
            if (preg_replace('/^(\-){0,1}[0-9]+(\.[0-9]+){0,1}/', '', $phoneNumber) == "") {
                $phone_number = ltrim($phoneNumber, '0');
                $phone_number = substr($phone_number, 0, 15);

                if ($phone_number < 10) {
                    echo json_encode([
                        'success' => false,
                        'phone_number' => $phone_number,
                        'message' => __('phone number is wrong!', $this->textdomain)
                    ]);
                    die();
                }
            }
            $username_exists = $this->phone_number_exist($phone_number);
        } else if (isset($_GET['email'])) {
            $username_exists = email_exists($_GET['email']);
        } else {
            echo json_encode([
                'success' => false,
                'message' => __('phone number is wrong!', $this->textdomain)
            ]);
            die();
        }
        if ($username_exists) {
            $activation_code = get_user_meta($username_exists, 'activation_code', true);
            $secod = sanitize_text_field($_GET['secod']);
            $verificationId = sanitize_text_field($_GET['verificationId']);
            if ($options['idehweb_use_custom_gateway'] == '1' && $options['idehweb_default_gateways'] == 'firebase' && isset($_GET['phone_number'])) {
                $response = $this->idehweb_lwp_activate_through_firebase($verificationId, $secod);
                if ($response->error && $response->error->code == 400) {
                    echo json_encode([
                        'success' => false,
                        'phone_number' => $phone_number,
                        'firebase' => $response->error,
                        'message' => __('entered code is wrong!', $this->textdomain)
                    ]);
                    die();
                } else {
//                if($response=='true') {
                    $user = get_user_by('ID', $username_exists);
                    if (!is_wp_error($user)) {
                        wp_clear_auth_cookie();
                        wp_set_current_user($user->ID); // Set the current user detail
                        wp_set_auth_cookie($user->ID); // Set auth details in cookie
                        update_user_meta($username_exists, 'activation_code', '');
                        if (!isset($options['idehweb_password_login'])) $options['idehweb_password_login'] = '1';
                        $options['idehweb_password_login'] = (bool)(int)$options['idehweb_password_login'];
                        $updatedPass = (bool)(int)get_user_meta($username_exists, 'updatedPass', true);

                        echo json_encode(array('success' => true, 'firebase' => $response, 'loggedin' => true, 'message' => __('loading...', $this->textdomain), 'updatedPass' => $updatedPass, 'authWithPass' => $options['idehweb_password_login']));

                    } else {
                        echo json_encode(array('success' => false, 'loggedin' => false, 'message' => __('wrong', $this->textdomain)));

                    }

                    die();
                }
            } else {
                if ($activation_code == $secod) {
                    // First get the user details
                    $user = get_user_by('ID', $username_exists);

                    if (!is_wp_error($user)) {
                        wp_clear_auth_cookie();
                        wp_set_current_user($user->ID); // Set the current user detail
                        wp_set_auth_cookie($user->ID); // Set auth details in cookie
                        update_user_meta($username_exists, 'activation_code', '');
                        if (!isset($options['idehweb_password_login'])) $options['idehweb_password_login'] = '1';
                        $options['idehweb_password_login'] = (bool)(int)$options['idehweb_password_login'];
                        $updatedPass = (bool)(int)get_user_meta($username_exists, 'updatedPass', true);

                        echo json_encode(array('success' => true, 'loggedin' => true, 'message' => __('loading...', $this->textdomain), 'updatedPass' => $updatedPass, 'authWithPass' => $options['idehweb_password_login']));

                    } else {
                        echo json_encode(array('success' => false, 'loggedin' => false, 'message' => __('wrong', $this->textdomain)));

                    }

                    die();

                } else {
                    echo json_encode([
                        'success' => false,
                        'phone_number' => $phone_number,
                        'message' => __('entered code is wrong!', $this->textdomain)
                    ]);
                    die();

                }
            }
        } else {

            echo json_encode([
                'success' => false,
                'phone_number' => $phone_number,
                'message' => __('user does not exist!', $this->textdomain)
            ]);
            die();

        }

//        echo json_encode([
//            'success' => false,
//            'phone_number' => $phoneNumber,
//            'message' => __('phone number is not correct!', $this->textdomain)
//        ]);
//        die();

    }

    function auth_user_login($user_login, $password, $login)
    {
        $info = array();
        $info['user_login'] = $user_login;
        $info['user_password'] = $password;
        $info['remember'] = true;

        // From false to '' since v 4.9
        $user_signon = wp_signon($info, '');
        if (is_wp_error($user_signon)) {
            echo json_encode(array('loggedin' => false, 'message' => __('Wrong username or password.', $this->textdomain)));
        } else {
            wp_set_current_user($user_signon->ID);
            echo json_encode(array('loggedin' => true, 'message' => __($login . ' successful, redirecting...', $this->textdomain)));
        }

        die();
    }

    function idehweb_lwp_auth_customer()
    {
        $options = get_option('idehweb_lwp_settings');

        if (!isset($options['idehweb_phone_number'])) $options['idehweb_phone_number'] = '';
        $phone_number = sanitize_text_field($_GET['phone_number']);
        $country_code = sanitize_text_field($_GET['country_code']);
        $url = get_site_url();
        $response = wp_safe_remote_post("https://zoomiroom.com/customer/customer/authcustomerforsms", [
            'timeout' => 60,
            'redirection' => 1,
            'blocking' => true,
            'headers' => array('Content-Type' => 'application/json'),
            'body' => wp_json_encode([
                'phoneNumber' => $phone_number,
                'countryCode' => $country_code,
                'websiteUrl' => $url
            ])
        ]);
        $body = wp_remote_retrieve_body($response);
        echo $body;
        die();
    }

    function idehweb_lwp_auth_customer_with_website()
    {
//        $options = get_option('idehweb_lwp_settings');

//        if (!isset($options['idehweb_website_url'])) $options['idehweb_website_url'] = $this->settings_get_site_url();
        $url = sanitize_text_field($_GET['url']);

        $response = wp_safe_remote_post("https://zoomiroom.com/customer/customer/authcustomerwithdomain", [
            'timeout' => 60,
            'redirection' => 1,
            'blocking' => true,
            'headers' => array('Content-Type' => 'application/json'),
            'body' => wp_json_encode([
                'websiteUrl' => $url,
                'restUrl' => get_rest_url(null, 'authorizelwp')
            ])
        ]);
        $body = wp_remote_retrieve_body($response);
        echo $body;
        die();
    }

    function idehweb_lwp_activate_through_firebase($sessionInfo, $code)
    {
        $options = get_option('idehweb_lwp_settings');

        if (!isset($options['idehweb_firebase_api'])) $options['idehweb_firebase_api'] = '';

        $response = wp_safe_remote_post("https://www.googleapis.com/identitytoolkit/v3/relyingparty/verifyPhoneNumber?key=" . $options['idehweb_firebase_api'], [
            'timeout' => 60,
            'redirection' => 4,
            'blocking' => true,
            'headers' => array('Content-Type' => 'application/json'),
            'body' => wp_json_encode([
                'code' => $code,
                'sessionInfo' => $sessionInfo
            ])
        ]);
        $body = wp_remote_retrieve_body($response);
        return json_decode($body);
    }

    function idehweb_lwp_check_credit()
    {
        $options = get_option('idehweb_lwp_settings');

        if (!isset($options['idehweb_token'])) $options['idehweb_token'] = '';
        $idehweb_token = $options['idehweb_token'];
//        $url = "https://idehweb.com/wp-json/check-credit/$idehweb_token";
//        $response = wp_remote_get($url);

        $response = wp_safe_remote_post("https://zoomiroom.com/customer/customer/checkCredit", [
            'timeout' => 60,
            'redirection' => 1,
            'blocking' => true,
            'headers' => array('Content-Type' => 'application/json',
                'token' => $idehweb_token)
        ]);
        $body = wp_remote_retrieve_body($response);

        echo $body;


        die();
    }

    function idehweb_lwp_get_shop()
    {
//        $url = "https://idehweb.com/wp-json/all-products/0";
//        $response = wp_remote_get($url);
        $lan = get_bloginfo("language");
        $response = wp_safe_remote_post("https://zoomiroom.com/customer/post/smsproducts", [
            'timeout' => 60,
            'redirection' => 1,
            'blocking' => true,
            'headers' => array('Content-Type' => 'application/json',
                'lan' => $lan)
        ]);
        $body = wp_remote_retrieve_body($response);

//        $body = wp_remote_retrieve_body($response);


        echo $body;


        die();
    }

    function idehweb_lwp_activate_customer()
    {
        $phone_number = sanitize_text_field($_GET['phone_number']);
        $secod = sanitize_text_field($_GET['secod']);

        $response = wp_safe_remote_post("https://zoomiroom.com/customer/customer/activateCustomer", [
            'timeout' => 60,
            'redirection' => 1,
            'blocking' => true,
            'headers' => array('Content-Type' => 'application/json'),
            'body' => wp_json_encode([
                'phoneNumber' => $phone_number,
                'activationCode' => $secod
            ])
        ]);
        $body = wp_remote_retrieve_body($response);

        echo $body;


        die();
    }

    function lwp_modify_user_table($column)
    {
        $column['phone_number'] = __('Phone number', $this->textdomain);
        $column['activation_code'] = __('Activation code', $this->textdomain);
        $column['registered_date'] = __('Registered date', $this->textdomain);

        return $column;
    }


    function lwp_modify_user_table_row($val, $column_name, $user_id)
    {
        $udata = get_userdata($user_id);
        switch ($column_name) {
            case 'phone_number' :
                return get_the_author_meta('phone_number', $user_id);
            case 'activation_code' :
                return get_the_author_meta('activation_code', $user_id);
            case 'registered_date' :
                return $udata->user_registered;
            default:
        }
        return $val;
    }

    function lwp_addon_woocommerce_login($template, $template_name, $template_path)
    {
        global $woocommerce;
        $_template = $template;
        if (!$template_path) $template_path = $woocommerce->template_url;
        $plugin_path = untrailingslashit(plugin_dir_path(__FILE__)) . '/template/woocommerce/';
        // Look within passed path within the theme - this is priority
        $template = locate_template(array($template_path . $template_name, $template_name));
        if (!$template && file_exists($plugin_path . $template_name)) $template = $plugin_path . $template_name;
        if (!$template) $template = $_template;
        return $template;
    }


    function lwp_make_registered_column_sortable($columns)
    {
        return wp_parse_args(array('registered_date' => 'registered'), $columns);
    }


}

global $idehweb_lwp;
$idehweb_lwp = new idehwebLwp();

/**
 * Template Tag
 */
function idehweb_lwp()
{

}



