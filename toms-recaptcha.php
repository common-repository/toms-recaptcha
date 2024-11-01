<?php
/**
 * Plugin Name:       TomS reCAPTCHA
 * Description:       Integrated Google ReCaptcha for WordPress.Protect the login, register, lostpassword and comment forms. Support Woocommerce, Ultimate Member and more popular forms.
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           1.2.0
 * Author:            Tom Sneddon
 * Author URI:        https://toms-caprice.org
 * Plugin URI:        https://wordpress.org/plugins/toms-recaptcha
 * Update URI:        https://wordpress.org/plugins/toms-recaptcha
 * License:           GPLv3 or later
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.htmlnditional-logic
 * Text Domain:       toms-recaptcha
 * Domain Path:		  /languages
 */

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once( plugin_dir_path( __FILE__ ) . 'vendor/toms-caprice.php');

if( !class_exists('TomSreCAPTCHA') ){

    class TomSreCAPTCHA {
    
        public function __construct(){
           
           add_action( 'init', array($this, 'TomSreCAPTCHAInit'), 10, 2);
           add_action( 'admin_menu', array($this, 'add_TomSreCAPTCHA_menu_to_TomS'), 10, 2);

            if( esc_textarea( get_option('toms_recaptcha_login_form') ) == "0" && !empty( esc_textarea($this->TomSreCAPTCHA_Current_Type()) ) ){
                if( esc_textarea( $this->TomSreCAPTCHA_Current_Type() ) == 'reCAPTCHA_v2_invisible' ){
                    $login_action = 'login_footer';
                }else{
                    $login_action = 'login_form';
                }
                add_action( $login_action, array($this, 'add_TomSreCAPTCHA_to_login_form'), 10, 2);
                add_filter( 'wp_authenticate_user',  array($this, 'TomSreCAPTCHA_login_form_verification'), 10, 3);
            }
            if( esc_textarea( get_option('toms_recaptcha_register_form', "0") ) == "0" && !empty( esc_textarea($this->TomSreCAPTCHA_Current_Type()) ) ){
                if( esc_textarea( $this->TomSreCAPTCHA_Current_Type() ) == 'reCAPTCHA_v2_invisible' ){
                    $register_action = 'login_footer';
                }else{
                    $register_action = 'register_form'; 
                }
                add_action( $register_action, array($this, 'add_TomSreCAPTCHA_to_register_form'), 10, 2);
                add_filter( 'registration_errors',  array($this, 'TomSreCAPTCHA_register_form_verification'), 10, 3);
            }
            if( esc_textarea( get_option('toms_recaptcha_lostpassword_form', "0") )  == "0" && !empty( esc_textarea($this->TomSreCAPTCHA_Current_Type()) ) ){
                if( esc_textarea( $this->TomSreCAPTCHA_Current_Type() ) == 'reCAPTCHA_v2_invisible' ){
                    $lostpassword_action = 'login_footer';
                }else{
                    $lostpassword_action = 'lostpassword_form';
                }
                add_action( $lostpassword_action, array($this, 'add_TomSreCAPTCHA_to_lostpassword_form'), 10, 2);
                add_filter( 'allow_password_reset', array($this, 'TomSreCAPTCHA_lostpassword_form_verification'), 10, 3);
            }
            if( esc_textarea( get_option('toms_recaptcha_comment_form', "0") ) == "0" && !empty( esc_textarea($this->TomSreCAPTCHA_Current_Type()) ) ){
                if( esc_textarea( $this->TomSreCAPTCHA_Current_Type() ) == 'reCAPTCHA_v2_invisible' ){
                    $comment_form = 'comment_form_after';
                }else{
                    $comment_form = 'comment_form';
                }
                add_action( $comment_form, array($this, 'add_TomSreCAPTCHA_to_comment_form'), 10, 2);
                add_filter( 'preprocess_comment', array($this, 'TomSreCAPTCHA_comment_form_verification'), 10, 3);
            }
           
           //add settings button to Installed plugin page
           add_filter('plugin_action_links', array($this, 'plugin_page_setting_button'), 10, 3);
        }

        public function add_TomSreCAPTCHA_menu_to_TomS(){
            add_submenu_page( "toms-wp", __('TomS reCAPTCHA Settings', 'toms-recaptcha'), '<span class="toms-menu-item"><span class="toms-recaptcha"></span><span class="toms-menu-text">'. __('TomS reCAPTCHA', 'toms-recaptcha').'</span></span>', 'manage_options', 'toms-recaptcha-settings', array($this, 'TomSreCAPTCHASettings'), );
            add_action( "admin_enqueue_scripts", array($this, 'TomSreCAPTCHA_global_load_style') );
            add_action( "toms-wp_page_toms-recaptcha-settings", array($this, 'TomSreCAPTCHA_load_style') );
        }
    
        //TomS reCAPTCHA backend Global style
        public function TomSreCAPTCHA_global_load_style() {
            //wp_enqueue_style( 'TomSreCAPTCHAGlobalStyle', plugin_dir_url( __FILE__ ) . 'inc/assets/css/toms-recaptcha-admin.css' );
        }
        //TomS reCAPTCHA backend setting page style
        public function TomSreCAPTCHA_load_style() {
            wp_enqueue_style( 'TomSreCAPTCHAStyle', plugin_dir_url( __FILE__ ) . 'inc/assets/css/toms-recaptcha.css' );
        }
        //TomS reCAPTCHA Languages
        public function TomSreCAPTCHAInit(){
            load_plugin_textdomain( 'toms-recaptcha', false, plugin_dir_path( __FILE__ ) . '/languages' );
        }

        /**
         * Submit data to database.
        */
        private function TomSreCAPTCHAHandleForm(){
            //check nonce
            if( wp_verify_nonce( $_POST['toms_recaptcha_nonce'], 'save_toms_recaptcha_nonce' ) AND current_user_can( 'manage_options' ) ) {
                //update_option() insert data to database.
                update_option('toms_recaptcha_v3_site_key', sanitize_text_field( $_POST['toms_recaptcha_v3_site_key'] ) );
                update_option('toms_recaptcha_v3_secret_key', sanitize_text_field( $_POST['toms_recaptcha_v3_secret_key'] ) );
                update_option('toms_recaptcha_v2_checkbox_site_key', sanitize_text_field( $_POST['toms_recaptcha_v2_checkbox_site_key'] ) );
                update_option('toms_recaptcha_v2_checkbox_secret_key', sanitize_text_field( $_POST['toms_recaptcha_v2_checkbox_secret_key'] ) );
                update_option('toms_recaptcha_v2_invisible_site_key', sanitize_text_field( $_POST['toms_recaptcha_v2_invisible_site_key'] ) );
                update_option('toms_recaptcha_v2_invisible_secret_key', sanitize_text_field( $_POST['toms_recaptcha_v2_invisible_secret_key'] ) );

                update_option('toms_recaptcha_type', sanitize_text_field( $_POST['toms_recaptcha_type'] ) );

                update_option('toms_recaptcha_login_form', isset($_POST['toms_recaptcha_login_form']) ? sanitize_text_field( $_POST['toms_recaptcha_login_form'] ) : '' );
                update_option('toms_recaptcha_register_form', isset($_POST['toms_recaptcha_register_form']) ? sanitize_text_field( $_POST['toms_recaptcha_register_form'] ) : '' );
                update_option('toms_recaptcha_lostpassword_form', isset($_POST['toms_recaptcha_lostpassword_form']) ? sanitize_text_field( $_POST['toms_recaptcha_lostpassword_form'] ) : '' );
                update_option('toms_recaptcha_comment_form', isset($_POST['toms_recaptcha_comment_form']) ? sanitize_text_field( $_POST['toms_recaptcha_comment_form'] ) : '' );

                update_option('toms_recaptcha_verify_api', sanitize_text_field( $_POST['toms_recaptcha_verify_api'] ) );

                update_option('toms_recaptcha_invisible_badge', sanitize_text_field( $_POST['toms_recaptcha_invisible_badge'] ) );

                update_option('toms_recaptcha_v2_theme', sanitize_text_field( $_POST['toms_recaptcha_v2_theme'] ) );

                update_option('toms_recaptcha_language', sanitize_text_field( $_POST['toms_recaptcha_language'] ) );

                // Create an action for extra form data
                $extra_forms = do_action( 'TomSreCAPTCHAExtraFormsData');
                
                update_option('toms_recaptcha_clear_data', isset($_POST['toms_recaptcha_clear_data']) ? sanitize_text_field( $_POST['toms_recaptcha_clear_data'] ) : '' );
                   
            ?>
                <div class="updated notice notice-success settings-error is-dismissible">
                    <p><strong><?php _e('Settings saved.', 'toms-recaptcha'); ?></strong></p>
                </div>
            <?php } else { ?>
                <div class="error notice notice-success settings-error is-dismissible">
                    <p><strong><?php _e('ERROR : Settings save failed.', 'toms-recaptcha'); ?></strong></p>
                    <p class="description"><?php _e('Sorry, you don\'t have permission to perform this action.', 'toms-recaptcha'); ?> </p>
                </div>
            <?php }
        }
        
        //TomS reCAPTCHA setting page contents
        public function TomSreCAPTCHASettings() { ?>
            <div class="wrap">
                <h1>
                    <span class="toms-recaptcha-heading">
                        <span class="toms-recaptcha-icon">
                            <img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'vendor/assets/img/toms-recaptcha.png'); ?>" width="35" />
                        </span>
                        <span class="toms-heading-text"><?php _e('TomS reCAPTCHA', 'toms-recaptcha'); ?></span>
                    </span>
                </h1>
                <?php if( !wp_is_mobile() ) : ?>
                <p class="description"><a href="https://developers.google.com/recaptcha" target="_blank" ><strong><?php _e('Google reCAPTCHA', 'toms-recaptcha'); ?></strong></a> <?php _e(' uses an advanced risk analysis engine and adaptive challenges to keep malicious software from engaging in abusive activities on your website. Meanwhile, legitimate users will be able to login, make purchases, view pages, or create accounts and fake users will be blocked.', 'toms-recaptcha'); ?></p>
                <?php endif; ?>
    
                <?php if( isset($_POST['justsubmitted']) && $_POST['justsubmitted'] == "true") $this->TomSreCAPTCHAHandleForm(); ?>
                <form method="post" class="toms-recaptcha-form">
                    <input type="hidden" name="justsubmitted" value="true" />
                    <?php if ( function_exists('wp_nonce_field') ){ wp_nonce_field('save_toms_recaptcha_nonce', 'toms_recaptcha_nonce'); }  //create a nonce to confirm the user submit from current page. ?>
    
                    <!--Site KEYS and Secret KEYS-->
                    <div class="toms-recaptcha-keys">
                        <p class="toms-recaptcha-keys-title"><?php _e('Google reCAPTCHA keys', 'toms-recaptcha'); ?></p>
                        <div class="description"><?php _e('To get the reCAPTCHA <strong>Site KEY</strong> and <strong>Secret KEY</strong>, click ', 'toms-recaptcha'); ?> <a href="https://www.google.com/recaptcha/admin/create" target="_blank"> <?php _e('here', 'toms-recaptcha'); ?></a>.</div>
                        
                        <!--V3 Site KEY-->
                        <label for="toms_recaptcha_settings"><strong><?php _e('reCAPTCHA', 'toms-recaptcha');?> <span style="color: #0ea105; font-size: 18px;">v3</span> <?php _e('Site KEY ', 'toms-recaptcha'); ?></strong>:</label>
                        <input type="text" name="toms_recaptcha_v3_site_key" id="toms_recaptcha_v3_site_key" value="<?php echo esc_textarea( get_option('toms_recaptcha_v3_site_key') );  ?>" />
                        <!--V3 Secret Key-->
                        <label for="toms_recaptcha_settings"><strong><?php _e('reCAPTCHA', 'toms-recaptcha');?>  <span style="color: #0ea105; font-size: 18px;">v3</span> <?php _e('Secret KEY ', 'toms-recaptcha'); ?></strong>:</label>
                        <input type="password" name="toms_recaptcha_v3_secret_key" id="toms_recaptcha_v3_secret_key" value="<?php echo esc_textarea( get_option('toms_recaptcha_v3_secret_key') ); ?>" onfocus="this.type='text'" onblur="this.type='password'" />
                        
                        <!--V2 Checkbox Site KEY-->
                        <label for="toms_recaptcha_settings"><strong><?php _e('reCAPTCHA', 'toms-recaptcha');?> <span style="color: red; font-size: 18px;">v2</span> <?php _e('Site KEY', 'toms-recaptcha'); ?></strong> (<span style="color: red;"><?php _e('Checkbox', 'toms-recaptcha'); ?></span>) :</label>
                        <input type="text" name="toms_recaptcha_v2_checkbox_site_key" id="toms_recaptcha_v2_checkbox_site_key" value="<?php echo esc_textarea( get_option('toms_recaptcha_v2_checkbox_site_key') );  ?>" />
                        <!--V2 Checkbox Secret Key-->
                        <label for="toms_recaptcha_settings"><strong><?php _e('reCAPTCHA', 'toms-recaptcha');?>  <span style="color: red; font-size: 18px;">v2</span> <?php _e('Secret KEY', 'toms-recaptcha'); ?></strong> (<span style="color: red;"><?php _e('Checkbox', 'toms-recaptcha'); ?></span>) :</label>
                        <input type="password" name="toms_recaptcha_v2_checkbox_secret_key" id="toms_recaptcha_v2_checkbox_secret_key" value="<?php echo esc_textarea( get_option('toms_recaptcha_v2_checkbox_secret_key') );  ?>" onfocus="this.type='text'" onblur="this.type='password'" />

                        <!--V2 Invisible Site KEY-->
                        <label for="toms_recaptcha_settings"><strong><?php _e('reCAPTCHA', 'toms-recaptcha');?> <span style="color: #1a73e8; font-size: 18px;">v2</span> <?php _e('Site KEY', 'toms-recaptcha'); ?></strong> (<span style="color: #1a73e8;"><?php _e('Invisible', 'toms-recaptcha'); ?></span>) :</label>
                        <input type="text" name="toms_recaptcha_v2_invisible_site_key" id="toms_recaptcha_v2_invisible_site_key" value="<?php echo esc_textarea( get_option('toms_recaptcha_v2_invisible_site_key') );  ?>" />
                        <!--V2 Invisible  Secret Key-->
                        <label for="toms_recaptcha_settings"><strong><?php _e('reCAPTCHA', 'toms-recaptcha');?>  <span style="color: #1a73e8; font-size: 18px;">v2</span> <?php _e('Secret KEY', 'toms-recaptcha'); ?></strong> (<span style="color: #1a73e8;"><?php _e('Invisible', 'toms-recaptcha'); ?></span>) :</label>
                        <input type="password" name="toms_recaptcha_v2_invisible_secret_key" id="toms_recaptcha_v2_invisible_secret_key" value="<?php echo esc_textarea( get_option('toms_recaptcha_v2_invisible_secret_key') );  ?>" onfocus="this.type='text'" onblur="this.type='password'" />
                    </div>
                    
                    <!--reCAPTCHA Type-->
                    <div class="toms-recaptcha-type">
                        <p class="toms-recaptcha-type-title"><?php _e('reCAPTCHA Type', 'toms-recaptcha'); ?></p>
                        <div class="toms-recaptcha-type-items">
                            <?php $toms_recaptcha_type = !empty( esc_textarea( get_option('toms_recaptcha_type') ) ) ? esc_textarea( get_option('toms_recaptcha_type', "reCAPTCHA_v3") ) : "reCAPTCHA_v3" ;?>
                            <label class="toms-label">
                                <input type="radio" name="toms_recaptcha_type" value="reCAPTCHA_v3" <?php if( $toms_recaptcha_type == 'reCAPTCHA_v3' || empty($toms_recaptcha_type) ) echo 'checked="checked"'; ?> />
                                <span class="reCAPTCHA-v3-text"><?php _e('reCAPTCHA v3', 'toms-recaptcha'); ?></span>
                                <div class="toms-recaptcha-type-img"><img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'inc/assets/img/reCAPTCHAv3.png' )?>" width="" height="60px" /></div>
                            </label>
                            <label class="toms-label">
                                <input type="radio" name="toms_recaptcha_type" value="reCAPTCHA_v2_checkbox" <?php if( $toms_recaptcha_type == 'reCAPTCHA_v2_checkbox') echo 'checked="checked"'; ?> />
                                <span class="reCAPTCHA-v2-checkbox-text"><?php _e('reCAPTCHA v2 (Checkbox)', 'toms-recaptcha'); ?></span>
                                <div class="toms-recaptcha-type-img"><img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'inc/assets/img/checkbox.gif' )?>" width="" height="60px" /></div>
                            </label>
                            <label class="toms-label">
                                <input type="radio" name="toms_recaptcha_type" value="reCAPTCHA_v2_invisible" <?php if( $toms_recaptcha_type == 'reCAPTCHA_v2_invisible') echo 'checked="checked"'; ?> />
                                <span class="reCAPTCHA-v2-invisible-text"><?php _e('reCAPTCHA v2 (Invisible)', 'toms-recaptcha'); ?></span>
                                <div class="toms-recaptcha-type-img"><img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'inc/assets/img/invisible_badge.png' )?>" width="" height="60px" /></div>
                            </label>
                        </div>
                    </div>

                    <!--Support Verification Form Lists-->
                    <div class="toms-recaptcha-support-forms">
                        <p class="toms-recaptcha-support-forms-title"><?php _e('Support Forms', 'toms-recaptcha'); ?></p>
                        <!--Wordpress Default Forms-->
                        <div class="toms-recaptcha-wordpress-default-forms"><?php _e('WordPress Default', 'toms-recaptcha'); ?> : </div>
                        <div class="toms-recaptcha-form-list">
                            <div class="toms-recaptcha-forms-contents">
                                <label class="toms-label">
                                    <input type="checkbox" name="toms_recaptcha_login_form" value="0" <?php if( esc_textarea( get_option('toms_recaptcha_login_form') ) == "0" )  echo 'checked="checked"'; ?> />
                                    <span class="login-text"><?php _e('Login Form', 'toms-recaptcha'); ?></span>
                                </label>
                                <label class="toms-label">
                                    <input type="checkbox" name="toms_recaptcha_register_form" value="0"  <?php if( esc_textarea( get_option('toms_recaptcha_register_form', '0') ) == "0" )  echo 'checked="checked"'; ?> />
                                    <span class="register-text"><?php _e('Register Form', 'toms-recaptcha'); ?></span>
                                </label>
                                <label class="toms-label">
                                    <input type="checkbox" name="toms_recaptcha_lostpassword_form" value="0"  <?php if( esc_textarea( get_option('toms_recaptcha_lostpassword_form', '0') ) == "0" )  echo 'checked="checked"'; ?> />
                                    <span class="lostpassword-text"><?php _e('Lost Password Form', 'toms-recaptcha'); ?></span>
                                </label>
                                <label class="toms-label">
                                    <input type="checkbox" name="toms_recaptcha_comment_form" value="0"  <?php if( esc_textarea( get_option('toms_recaptcha_comment_form', '0') ) == "0" )  echo 'checked="checked"'; ?> />
                                    <span class="comment-text"><?php _e('Comment Form', 'toms-recaptcha'); ?></span>
                                </label>
                            </div>
                        </div>
                        <?php
                            //Create an action for extra forms
                            $extra_forms = do_action( 'TomSreCAPTCHAExtraForms');
                            ?>
                    </div>

                    <!--reCAPTCHA Optional Settings-->
                    <div class="toms-recaptcha-options">
                        <p class="toms-recaptcha-options-title"><?php _e('Option Settings', 'toms-recaptcha'); ?></p>
                        <div class="toms-recaptcha-options-items">
                            <div class="toms-verify-api">
                                <label class="toms-verify-api-text"><?php _e('Verify API', 'toms-recaptcha'); ?> : </label>
                                <div class="toms-label">
                                    <?php $toms_recaptcha_verify_api = !empty( esc_textarea( get_option('toms_recaptcha_verify_api') ) ) ? esc_textarea( get_option('toms_recaptcha_verify_api') ) : "0" ;?>
                                    <label class="toms-true-label">
                                        <input type="radio" name="toms_recaptcha_verify_api" value="0" <?php if( $toms_recaptcha_verify_api == "0" || empty($toms_recaptcha_verify_api) ) echo 'checked="checked"'; ?> />
                                        <span class="Google-com-text"><?php _e('Google.com', 'toms-recaptcha'); ?></span>
                                    </label>
                                    <label class="toms-false-label">
                                        <input type="radio" name="toms_recaptcha_verify_api" value="1" <?php if( $toms_recaptcha_verify_api == "1" ) echo 'checked="checked"'; ?> />
                                        <span class="Recaptcha-net-text"><?php _e('Recaptcha.net', 'toms-recaptcha'); ?></span>
                                    </label>
                                </div>
                            </div>

                            <div class="toms-button-style">
                                <span class="toms-button-style-text"><?php _e('Theme', 'toms-recaptcha'); ?></span><span> ( <?php _e('reCAPTCHA v2 Checkbox', 'toms-recaptcha'); ?> )</span> : 
                                <div class="toms-button-style-label">
                                    <?php $toms_recaptcha_v2_theme = !empty( esc_textarea( get_option('toms_recaptcha_v2_theme') ) ) ? esc_textarea( get_option('toms_recaptcha_v2_theme') ) : "0" ;?>
                                    <label class="toms-label">
                                        <input type="radio" name="toms_recaptcha_v2_theme" value="0" <?php if( $toms_recaptcha_v2_theme == "0" || empty( $toms_recaptcha_v2_theme ) ) echo 'checked="checked"'; ?> />
                                        <span class="light-text"><?php _e('Light', 'toms-recaptcha'); ?></span>
                                        <div class="toms-recaptcha-type-img"><img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'inc/assets/img/light.png' )?>" width="200px" height="48px" /></div>
                                    </label>
                                    <label class="toms-label">
                                        <input type="radio" name="toms_recaptcha_v2_theme" value="1" <?php if( $toms_recaptcha_v2_theme == "1" ) echo 'checked="checked"'; ?> />
                                        <span class="dark-text"><?php _e('Dark', 'toms-recaptcha'); ?></span>
                                        <div class="toms-recaptcha-type-img"><img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'inc/assets/img/dark.png' )?>" width="200px" height="48px" /></div>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="toms-badge">
                                <span class="toms-badge-title"><?php _e('Badge', 'toms-recaptcha'); ?></span> <span>( <?php _e('reCAPTCHA v2 Invisible', 'toms-recaptcha'); ?> )</span> : 
                                <?php $toms_recaptcha_invisible_badge = !empty( esc_textarea( get_option('toms_recaptcha_invisible_badge') ) ) ? esc_textarea( get_option('toms_recaptcha_invisible_badge', "0") ) : "0" ;?>
                                <div class="toms-badge-label">
                                    <label class="toms-label">
                                        <input type="radio" name="toms_recaptcha_invisible_badge" value="0" <?php if( $toms_recaptcha_invisible_badge == "0" || empty($toms_recaptcha_invisible_badge) ) echo 'checked="checked"'; ?> />
                                        <span class="true-text"><?php _e('Bottom Right', 'toms-recaptcha'); ?></span>
                                    </label>
                                    <label class="toms-label">
                                        <input type="radio" name="toms_recaptcha_invisible_badge" value="1" <?php if( $toms_recaptcha_invisible_badge == "1" ) echo 'checked="checked"'; ?> />
                                        <span class="false-text"><?php _e('Bottom Left', 'toms-recaptcha'); ?></span>
                                    </label>
                                </div>
                            </div>

                            <?php
                                //TomS reCaptcha Languages
                                $TOMS_Languages_JSON    = file_get_contents(plugin_dir_url( __FILE__ ) . 'inc/language.json');
                                $TOMS_Languages         = json_decode($TOMS_Languages_JSON, true);
                            ?>
                            <div class="toms-languages">
                                <label class="toms-label"><?php _e('Language', 'toms-recaptcha'); ?> : </label>
                                <select name="toms_recaptcha_language">
                                <?php foreach($TOMS_Languages as $language => $value ){ ?>
                                    <option value="<?php echo esc_attr( $value );?>" <?php selected( esc_textarea( get_option('toms_recaptcha_language') ), esc_attr( $value ) ); ?> ><?php echo esc_textarea( $language ); ?></option>
                                <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!--Clear all data option-->
                    <div class="toms-recaptcha-clear-data">
                        <div class="toms-recaptcha-clear-data-contents">
                            <div class="toms-label">
                                <input type="checkbox" name="toms_recaptcha_clear_data" value="0"  <?php if( esc_textarea( get_option('toms_recaptcha_clear_data') ) == "0" )  echo 'checked="checked"'; ?> />
                                <span class="delete-text"><?php _e('Delete all the configuration Data!', 'toms-recaptcha'); ?></span>
                                <div class="delete-warning-text"><span class="delete-warning-title"><?php _e('Warning: ', 'toms-recaptcha'); ?></span> <?php _e('Please check this option carefully, it will delete all data saved on this page when the plugin is deleted .', 'toms-recaptcha'); ?></div>
                            </div>
                        </div>
                    </div>

                    <!--Submit Button-->
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes', 'toms-recaptcha'); ?>" />
                </form>
            </div>
        <?php }

        /**
         * TomSreCAPTCHA Current Type check
         * 
         * @return string   Current Type
        */
        function TomSreCAPTCHA_Current_Type(){
            if( !empty( esc_textarea( get_option( 'toms_recaptcha_v3_site_key') ) ) &&
                !empty( esc_textarea( get_option( 'toms_recaptcha_v3_secret_key') ) ) && 
                esc_textarea( get_option('toms_recaptcha_type', 'reCAPTCHA_v3') ) == 'reCAPTCHA_v3'
            ){
                return 'reCAPTCHA_v3';
            }

            if( !empty( esc_textarea( get_option( 'toms_recaptcha_v2_checkbox_site_key') ) ) &&
                !empty( esc_textarea( get_option( 'toms_recaptcha_v2_checkbox_secret_key') ) ) && 
                esc_textarea( get_option('toms_recaptcha_type') ) == 'reCAPTCHA_v2_checkbox'
            ){
                return 'reCAPTCHA_v2_checkbox';
            }

            if( !empty( esc_textarea( get_option( 'toms_recaptcha_v2_invisible_site_key') ) ) &&
                !empty( esc_textarea( get_option( 'toms_recaptcha_v2_invisible_secret_key') ) ) && 
                esc_textarea( get_option('toms_recaptcha_type') ) == 'reCAPTCHA_v2_invisible'
            ){
                return 'reCAPTCHA_v2_invisible';
            }

            return '';
        }

        /**
         *  TomS reCAPTCHA Allowed HTML
         * @return array
        */
        function TomSreCAPTCHA_allow_html(){
            return $allowed_html =[
                'style' => [
                    'id'        => [],
                    'class'     => [],
                    'name'      => [],
                    '@media'    => [],
                    'max-width' => []
                ],
                'div' => [
                    'class' => [],
                    'id'    => [],
                    'name'  => [],
                    'data-sitekey' => [],
                    'data-badge' => [],
                    'data-callback' => [],
                    'data-size' => []
                ],
                'span' => [
                    'class' => [],
                    'id'    => [],
                    'name'  => []
                ],
                'img'   => [
                    'title' => [],
                    'src' => [],
                    'alt' => []
                ],
                'input' => [
                    'id'    => [],
                    'class' => [],
                    'type'  => [],
                    'name'  => [],
                    'value' => [],
                    'data-key' => [],
                    'data-sitekey' => [],
                    'data-badge' => [],
                    'data-callback' => []
                ],
                'button' => [
                    'class' => [],
                    'data-sitekey' => [],
                    'data-badge' => [],
                    'data-callback' => []
                ],
                'script' => [
                    'async' => [],
                    'src' => []
                ]
            ];
        }
        /**
         *  TomS reCAPTCHA Allowed protocols
         * @return array
        */
        function TomSreCAPTCHA_allow_protocols(){
            return $protocols = array( 'data', 'http', 'https' );
        }

        /**
         * Login Form
         * 
         * @param loginform  Wordpress login form id.
         * 
        */
        public function add_TomSreCAPTCHA_to_login_form(){
            if( $GLOBALS['pagenow'] === 'wp-login.php' ) {
                $id_class = 'loginform';
                $html = '';
                $current_type = $this->TomSreCAPTCHA_Current_Type();
                $TomSreCAPTCHAFrontend = new TomSreCAPTCHAFrontend();
                $allowed_html = $this->TomSreCAPTCHA_allow_html();
                if( $current_type == 'reCAPTCHA_v3' ){
                    $html .= $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V3_HTML($id_class);
                    echo wp_kses( $html, $allowed_html);
                    wp_print_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V3_load_JS($id_class) );
                    wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V3_render_JS($id_class), [ 'type' => 'text/javascript' ] );
                }

                if( $current_type == 'reCAPTCHA_v2_checkbox'){
                    $html .= $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Checkbox_HTML($id_class);
                    echo wp_kses( $html, $allowed_html);
                    wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Checkbox_JS_render($id_class), [ 'type' => 'text/javascript' ] );
                    wp_print_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Checkbox_load_JS($id_class) );
                }

                if( $current_type == 'reCAPTCHA_v2_invisible'){
                    wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Invisible_onsubmit_JS($id_class), [ 'type' => 'text/javascript' ] );
                    wp_print_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Invisible_load_JS($id_class) );
                    wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Invisible_render_JS($id_class), [ 'type' => 'text/javascript' ] );
                }
            }
        }

         /**
         * Login Form reCAPTCHA verification
         * 
         *  reCAPTCHA v3 $_POST['g-recaptcha']
         *  reCAPTCHA v2 $_POST['g-recaptcha-response']
         * 
         * @return $user  Wordpress login form user data. if the reCAPTCHA passed, will allow user login, else return ERROR.
         * 
        */
        public function TomSreCAPTCHA_login_form_verification($user){
            if( $GLOBALS['pagenow'] === 'wp-login.php' ) {
                
                if( esc_textarea( get_option('toms_recaptcha_type', 'reCAPTCHA_v3') ) == 'reCAPTCHA_v3'){

                    $response  = isset( $_POST['g-recaptcha'] ) && !empty( $_POST['g-recaptcha'] ) ? sanitize_text_field( $_POST['g-recaptcha'] ) : '';
                    
                    $result = $this->TomSreCAPTCHA_v3_verification( $response );

                    if( isset($result['success']) && $result['success'] == "1" &&
                        isset($result['action']) && $result['action'] == "TomSreCAPTCHAv3" &&
                        isset($result['score']) && $result['score'] >= 0.5
                    ){
                        return $user;
                    }else{
                        $error = isset($result['error-codes']) ? $result['error-codes'] : '';
                        if( is_array( $error ) ){
                            foreach( $error as $value ){
                                $error = $value;
                            }
                        }
                        return new WP_Error("Captcha Invalid", sprintf( __('<strong>ERROR</strong>: Captcha verification failed, please try again!!! <br>Error Codes: %s ', 'toms-recaptcha'), $error) );
                    }
                }

                if( esc_textarea( get_option('toms_recaptcha_type') ) == 'reCAPTCHA_v2_checkbox' ){
                    
                    $response  = isset( $_POST['g-recaptcha-response'] ) && !empty( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( $_POST['g-recaptcha-response'] ) : '';
                    
                    $result = $this->TomSreCAPTCHA_v2_checkbox_verification( $response );

                    if( isset($result['success']) && $result['success'] == "1" ){
                        return $user;
                    }else{
                        $error = isset($result['error-codes']) ? $result['error-codes'] : '';
                        if( is_array( $error ) ){
                            foreach( $error as $value ){
                                $error = $value;
                            }
                        }
                        return new WP_Error("Captcha Invalid", sprintf( __('<strong>ERROR</strong>: Captcha verification failed, please try again!!! <br>Error Codes: %s ', 'toms-recaptcha'), $error) );
                    }

                }

                if( esc_textarea( get_option('toms_recaptcha_type') ) == 'reCAPTCHA_v2_invisible'){
                    
                    $response  = isset( $_POST['g-recaptcha-response'] ) && !empty( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( $_POST['g-recaptcha-response'] ) : '';
                    
                    $result = $this->TomSreCAPTCHA_v2_invisible_verification( $response );

                    if( isset($result['success']) && $result['success'] == "1" ){
                        return $user;
                    }else{
                        $error = isset($result['error-codes']) ? $result['error-codes'] : '';
                        if( is_array( $error ) ){
                            foreach( $error as $value ){
                                $error = $value;
                            }
                        }
                        return new WP_Error("Captcha Invalid", sprintf( __('<strong>ERROR</strong>: Captcha verification failed, please try again!!! <br>Error Codes: %s ', 'toms-recaptcha'), $error) );
                    }

                }

            }else{
                return $user;
            }
            
        }

        /**
         * Register Form
         * 
         * @param registerform  Wordpress register form id.
         * 
        */
        public function add_TomSreCAPTCHA_to_register_form(){
            if( $GLOBALS['pagenow'] === 'wp-login.php' && !empty($_REQUEST['action']) && $_REQUEST['action'] === 'register' ) {
                $id_class = 'registerform';
                $html = '';
                $current_type = $this->TomSreCAPTCHA_Current_Type();
                $TomSreCAPTCHAFrontend = new TomSreCAPTCHAFrontend();
                $allowed_html = $this->TomSreCAPTCHA_allow_html();
                if( $current_type == 'reCAPTCHA_v3' ){
                    $html .= $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V3_HTML($id_class);
                    echo wp_kses( $html, $allowed_html);
                    wp_print_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V3_load_JS($id_class) );
                    wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V3_render_JS($id_class), [ 'type' => 'text/javascript' ] );
                }

                if( $current_type == 'reCAPTCHA_v2_checkbox'){
                    $html .= $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Checkbox_HTML($id_class);
                    echo wp_kses( $html, $allowed_html);
                    wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Checkbox_JS_render($id_class), [ 'type' => 'text/javascript' ] );
                    wp_print_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Checkbox_load_JS($id_class) );
                }

                if( $current_type == 'reCAPTCHA_v2_invisible'){
                    wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Invisible_onsubmit_JS($id_class), [ 'type' => 'text/javascript' ] );
                    wp_print_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Invisible_load_JS($id_class) );
                    wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Invisible_render_JS($id_class), [ 'type' => 'text/javascript' ] );
                }
            }
        }
        
         /**
         * Register Form reCAPTCHA verification
         * 
         *  reCAPTCHA v3 $_POST['g-recaptcha']
         *  reCAPTCHA v2 $_POST['g-recaptcha-response']
         * 
         * @return $errors  Wordpress login form user data. if the reCAPTCHA passed, will allow user register, else return ERROR.
         *                 Always need to return $errors even the reCAPTCHA verify passed or not.
         * 
        */
        public function TomSreCAPTCHA_register_form_verification($errors, $sanitized_user_login, $user_email){
            if( $GLOBALS['pagenow'] === 'wp-login.php' && !empty($_REQUEST['action']) && $_REQUEST['action'] === 'register' ) {

                if( esc_textarea( get_option('toms_recaptcha_type', 'reCAPTCHA_v3') ) == 'reCAPTCHA_v3'){

                    $response  = isset( $_POST['g-recaptcha'] ) && !empty( $_POST['g-recaptcha'] ) ? sanitize_text_field( $_POST['g-recaptcha'] ) : '';
                    
                    $result = $this->TomSreCAPTCHA_v3_verification( $response );

                    if( isset($result['success']) && $result['success'] == "1" &&
                        isset($result['action']) && $result['action'] == "TomSreCAPTCHAv3" &&
                        isset($result['score']) && $result['score'] >= 0.5
                    ){
                        return $errors;
                    }else{
                        $error = isset($result['error-codes']) ? $result['error-codes'] : '';
                        if( is_array( $error ) ){
                            foreach( $error as $value ){
                                $error = $value;
                            }
                        }
                        return new WP_Error("Captcha Invalid", sprintf( __('<strong>ERROR</strong>: Captcha verification failed, please try again!!! <br>Error Codes: %s ', 'toms-recaptcha'), $error) );
                    }
                }

                if( esc_textarea( get_option('toms_recaptcha_type') ) == 'reCAPTCHA_v2_checkbox' ){
                    
                    $response  = isset( $_POST['g-recaptcha-response'] ) && !empty( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( $_POST['g-recaptcha-response'] ) : '';
                    
                    $result = $this->TomSreCAPTCHA_v2_checkbox_verification( $response );

                    if( isset($result['success']) && $result['success'] == "1" ){
                        return $errors;
                    }else{
                        $error = isset($result['error-codes']) ? $result['error-codes'] : '';
                        if( is_array( $error ) ){
                            foreach( $error as $value ){
                                $error = $value;
                            }
                        }
                        return new WP_Error("Captcha Invalid", sprintf( __('<strong>ERROR</strong>: Captcha verification failed, please try again!!! <br>Error Codes: %s ', 'toms-recaptcha'), $error) );
                    }

                }

                if( esc_textarea( get_option('toms_recaptcha_type') ) == 'reCAPTCHA_v2_invisible'){
                    
                    $response  = isset( $_POST['g-recaptcha-response'] ) && !empty( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( $_POST['g-recaptcha-response'] ) : '';
                    
                    $result = $this->TomSreCAPTCHA_v2_invisible_verification( $response );

                    if( isset($result['success']) && $result['success'] == "1" ){
                        return $errors;
                    }else{
                        $error = isset($result['error-codes']) ? $result['error-codes'] : '';
                        if( is_array( $error ) ){
                            foreach( $error as $value ){
                                $error = $value;
                            }
                        }
                        return new WP_Error("Captcha Invalid", sprintf( __('<strong>ERROR</strong>: Captcha verification failed, please try again!!! <br>Error Codes: %s ', 'toms-recaptcha'), $error) );
                    }

                }
            }
            return $errors;
            
        }

        /**
         * Lost Password Form
         * 
         * @param lostpasswordform  Wordpress Lost Password form id.
         * 
        */
        public function add_TomSreCAPTCHA_to_lostpassword_form(){
            if( $GLOBALS['pagenow'] === 'wp-login.php' && !empty($_REQUEST['action']) && $_REQUEST['action'] === 'lostpassword' ) {
                $id_class = 'lostpasswordform';
                $html = '';
                $current_type = $this->TomSreCAPTCHA_Current_Type();
                $TomSreCAPTCHAFrontend = new TomSreCAPTCHAFrontend();
                $allowed_html = $this->TomSreCAPTCHA_allow_html();
                if( $current_type == 'reCAPTCHA_v3' ){
                    $html .= $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V3_HTML($id_class);
                    echo wp_kses( $html, $allowed_html);
                    wp_print_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V3_load_JS($id_class) );
                    wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V3_render_JS($id_class), [ 'type' => 'text/javascript' ] );
                }

                if( $current_type == 'reCAPTCHA_v2_checkbox'){
                    $html .= $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Checkbox_HTML($id_class);
                    echo wp_kses( $html, $allowed_html);
                    wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Checkbox_JS_render($id_class), [ 'type' => 'text/javascript' ] );
                    wp_print_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Checkbox_load_JS($id_class) );
                }

                if( $current_type == 'reCAPTCHA_v2_invisible'){
                    wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Invisible_onsubmit_JS($id_class), [ 'type' => 'text/javascript' ] );
                    wp_print_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Invisible_load_JS($id_class) );
                    wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Invisible_render_JS($id_class), [ 'type' => 'text/javascript' ] );
                }
            }
        }

        /**
         * Lost Password Form reCAPTCHA verification
         * 
         *  reCAPTCHA v3 $_POST['g-recaptcha']
         *  reCAPTCHA v2 $_POST['g-recaptcha-response']
         * 
         * @return $true  Default is true. if the reCAPTCHA passed, return $true, else return ERROR.
         * 
        */
        public function TomSreCAPTCHA_lostpassword_form_verification($true){
            if( $GLOBALS['pagenow'] === 'wp-login.php' && !empty($_REQUEST['action']) && $_REQUEST['action'] === 'lostpassword' ) {

                if( esc_textarea( get_option('toms_recaptcha_type', 'reCAPTCHA_v3') ) == 'reCAPTCHA_v3'){

                    $response  = isset( $_POST['g-recaptcha'] ) && !empty( $_POST['g-recaptcha'] ) ? sanitize_text_field( $_POST['g-recaptcha'] ) : '';
                    
                    $result = $this->TomSreCAPTCHA_v3_verification( $response );

                    if( isset($result['success']) && $result['success'] == "1" &&
                        isset($result['action']) && $result['action'] == "TomSreCAPTCHAv3" &&
                        isset($result['score']) && $result['score'] >= 0.5
                    ){
                        return $true;
                    }else{
                        $error = isset($result['error-codes']) ? $result['error-codes'] : '';
                        if( is_array( $error ) ){
                            foreach( $error as $value ){
                                $error = $value;
                            }
                        }
                        return new WP_Error("Captcha Invalid", sprintf( __('<strong>ERROR</strong>: Captcha verification failed, please try again!!! <br>Error Codes: %s ', 'toms-recaptcha'), $error) );
                    }
                }

                if( esc_textarea( get_option('toms_recaptcha_type') ) == 'reCAPTCHA_v2_checkbox' ){
                    
                    $response  = isset( $_POST['g-recaptcha-response'] ) && !empty( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( $_POST['g-recaptcha-response'] ) : '';
                    
                    $result = $this->TomSreCAPTCHA_v2_checkbox_verification( $response );

                    if( isset($result['success']) && $result['success'] == "1" ){
                        return $true;
                    }else{
                        $error = isset($result['error-codes']) ? $result['error-codes'] : '';
                        if( is_array( $error ) ){
                            foreach( $error as $value ){
                                $error = $value;
                            }
                        }
                        return new WP_Error("Captcha Invalid", sprintf( __('<strong>ERROR</strong>: Captcha verification failed, please try again!!! <br>Error Codes: %s ', 'toms-recaptcha'), $error) );
                    }

                }

                if( esc_textarea( get_option('toms_recaptcha_type') ) == 'reCAPTCHA_v2_invisible'){
                    
                    $response  = isset( $_POST['g-recaptcha-response'] ) && !empty( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( $_POST['g-recaptcha-response'] ) : '';
                    
                    $result = $this->TomSreCAPTCHA_v2_invisible_verification( $response );

                    if( isset($result['success']) && $result['success'] == "1" ){
                        return $true;
                    }else{
                        $error = isset($result['error-codes']) ? $result['error-codes'] : '';
                        if( is_array( $error ) ){
                            foreach( $error as $value ){
                                $error = $value;
                            }
                        }
                        return new WP_Error("Captcha Invalid", sprintf( __('<strong>ERROR</strong>: Captcha verification failed, please try again!!! <br>Error Codes: %s ', 'toms-recaptcha'), $error) );
                    }

                }
            }

            return $true;
            
        }

        /**
         * Comment Form
         * 
         * @param comment-form  Wordpress comment form classname.
         * 
        */
        public function add_TomSreCAPTCHA_to_comment_form(){
            $id_class = '.comment-form';
            $html = '';
            $current_type = $this->TomSreCAPTCHA_Current_Type();
            $TomSreCAPTCHAFrontend = new TomSreCAPTCHAFrontend();
            $allowed_html = $this->TomSreCAPTCHA_allow_html();
            if( $current_type == 'reCAPTCHA_v3' ){
                $html .= $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V3_HTML($id_class);
                echo wp_kses( $html, $allowed_html);
                wp_print_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V3_load_JS($id_class) );
                wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V3_render_JS($id_class), [ 'type' => 'text/javascript' ] );
            }

            if( $current_type == 'reCAPTCHA_v2_checkbox'){
                $html .= $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Checkbox_HTML($id_class);
                echo wp_kses( $html, $allowed_html);
                wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Checkbox_JS_render($id_class), [ 'type' => 'text/javascript' ] );
                wp_print_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Checkbox_load_JS($id_class) );
            }

            if( $current_type == 'reCAPTCHA_v2_invisible'){
                wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Invisible_onsubmit_JS($id_class), [ 'type' => 'text/javascript' ] );
                wp_print_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Invisible_load_JS($id_class) );
                wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Invisible_render_JS($id_class), [ 'type' => 'text/javascript' ] );
            }

            if( $current_type == 'reCAPTCHA_v2_checkbox'){
                if( preg_match('/^\./', $id_class ) != 0 ){ //check the name of form id or form class
                    $element            = 'getElementsByClassName';
                    $number             = '[0]';
                    $sign               = '.';
                    $id_class_type      = 'class';   
                    $suffix             = '-toms-recaptcha';
                    $reCAPTCHA_form_name  = ltrim($id_class, '.'); //Delete the first Ending mark'.'
                }else{
                    $element            = 'getElementById';
                    $number             = '';
                    $sign               = '#';
                    $id_class_type      = 'id';
                    $suffix             = '-toms-recaptcha';
                    $reCAPTCHA_form_name  = $id_class;
                }

                $html_js = '';
                ob_start(); ?>
                        //add reCAPTCHA before the comment submit button.
                        var commentFormBtn  = document.<?php echo esc_textarea($element); ?>('<?php echo esc_textarea($reCAPTCHA_form_name.$suffix); ?>')<?php echo esc_textarea( $number ); ?>;
                        var commentForm     = document.getElementsByClassName('comment-form')[0];
                        commentForm.insertBefore(commentFormBtn, document.getElementsByClassName('form-submit')[0]);
               <?php $html_js .= ob_get_contents();
                ob_end_clean();
                wp_print_inline_script_tag( $html_js, [ 'type' => 'text/javascript' ] );
            }
        }

         /**
         * Comment Form reCAPTCHA verification
         * 
         *  reCAPTCHA v3 $_POST['g-recaptcha']
         *  reCAPTCHA v2 $_POST['g-recaptcha-response']
         * 
         * @return $user  Wordpress login form user data. if the reCAPTCHA passed, will allow user login, else return ERROR.
         * 
        */
        public function TomSreCAPTCHA_comment_form_verification($commentdata){

            if( esc_textarea( get_option('toms_recaptcha_type', 'reCAPTCHA_v3') ) == 'reCAPTCHA_v3'){

                $response  = isset( $_POST['g-recaptcha'] ) && !empty( $_POST['g-recaptcha'] ) ? sanitize_text_field( $_POST['g-recaptcha'] ) : '';
                
                $result = $this->TomSreCAPTCHA_v3_verification( $response );

                if( isset($result['success']) && $result['success'] == "1" &&
                    isset($result['action']) && $result['action'] == "TomSreCAPTCHAv3" &&
                    isset($result['score']) && $result['score'] >= 0.5
                ){
                    return $commentdata;
                }else{
                    $error = isset($result['error-codes']) ? $result['error-codes'] : '';
                    if( is_array( $error ) ){
                        foreach( $error as $value ){
                            $error = $value;
                        }
                    }
                    wp_die( sprintf( __("<strong>ERROR</strong>: Challenge failed!!! Bots are not allowed to submit comments, Please try again!!! <br>Error Codes: %s ", 'toms-recaptcha'), $error) .'<br/><br/> <a href="javascript:history.back()">« '.__('Back', 'toms-recaptcha').'</a>');
                }
            }

            if( esc_textarea( get_option('toms_recaptcha_type') ) == 'reCAPTCHA_v2_checkbox' ){
                
                $response  = isset( $_POST['g-recaptcha-response'] ) && !empty( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( $_POST['g-recaptcha-response'] ) : '';
                
                $result = $this->TomSreCAPTCHA_v2_checkbox_verification( $response );

                if( isset($result['success']) && $result['success'] == "1" ){
                    return $commentdata;
                }else{
                    $error = isset($result['error-codes']) ? $result['error-codes'] : '';
                    if( is_array( $error ) ){
                        foreach( $error as $value ){
                            $error = $value;
                        }
                    }
                    wp_die( sprintf( __("<strong>ERROR</strong>: Challenge failed!!! Bots are not allowed to submit comments, Please try again!!! <br>Error Codes: %s ", 'toms-recaptcha'), $error) .'<br/><br/> <a href="javascript:history.back()">« '.__('Back', 'toms-recaptcha').'</a>');
                }

            }

            if( esc_textarea( get_option('toms_recaptcha_type') ) == 'reCAPTCHA_v2_invisible'){

                $response  = isset( $_POST['g-recaptcha-response'] ) && !empty( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( $_POST['g-recaptcha-response'] ) : '';
                
                $result = $this->TomSreCAPTCHA_v2_invisible_verification( $response );

                if( isset($result['success']) && $result['success'] == "1" ){
                    return $commentdata;
                }else{
                    $error = isset($result['error-codes']) ? $result['error-codes'] : '';
                    if( is_array( $error ) ){
                        foreach( $error as $value ){
                            $error = $value;
                        }
                    }
                    wp_die( sprintf( __("<strong>ERROR</strong>: Challenge failed!!! Bots are not allowed to submit comments, Please try again!!! <br>Error Codes: %s ", 'toms-recaptcha'), $error) .'<br/><br/> <a href="javascript:history.back()">« '.__('Back', 'toms-recaptcha').'</a>');
                }
            }

            return $commentdata;
        }

        /**
         * reCAPTCHA v3 verification
         * 
         * Call this function need a arg: $_POST['g-recaptcha']
         * 
         * @param $response   reCAPTCHA token $_POST['g-recaptcha']
         * 
         * @return Array    Array of verify result.
         * 
         */
        public function TomSreCAPTCHA_v3_verification( $response ) {

            $v3_secretkey   = esc_textarea( get_option('toms_recaptcha_v3_secret_key') );

            $verify_api     = esc_textarea( get_option('toms_recaptcha_verify_api', "0") ) == "0" ? 'google.com' : 'recaptcha.net';

            $remoteip       = esc_textarea( $_SERVER['REMOTE_ADDR'] );

            $g_recaptcha    = !empty($response) ? esc_textarea( $response ) : '';

            $verify_url     = 'https://www.' . esc_textarea( $verify_api ) . '/recaptcha/api/siteverify';

            $args_array = $verify_url . '?secret=' . $v3_secretkey . '&response=' . $g_recaptcha . '&remoteip=' . $remoteip;

            $response_json = wp_remote_post( $args_array );
            
            $obj_json = wp_remote_retrieve_body( $response_json );
            
            return $result = json_decode($obj_json, true);
        }

        /**
         * reCAPTCHA v2 Checkbox verification
         * 
         * Call this function need a arg: $_POST['g-recaptcha-response']
         * 
         * @param $response   reCAPTCHA token $_POST['g-recaptcha-response']
         * 
         * @return Array    Array of verify result.
         * 
         */
        public function TomSreCAPTCHA_v2_checkbox_verification( $response ) {

            $v2_checkbox_secretkey   = esc_textarea( get_option('toms_recaptcha_v2_checkbox_secret_key') );

            $verify_api     = esc_textarea( get_option('toms_recaptcha_verify_api', "0") ) == "0" ? 'google.com' : 'recaptcha.net';

            $remoteip       = esc_textarea( $_SERVER['REMOTE_ADDR'] );

            $g_recaptcha    = !empty($response) ? esc_textarea( $response ) : '';

            $verify_url     = 'https://www.' . esc_textarea( $verify_api ) . '/recaptcha/api/siteverify';
            
            $args_array = $verify_url . '?secret=' . $v2_checkbox_secretkey . '&response=' . $g_recaptcha . '&remoteip=' . $remoteip;
    
            $response_json = wp_remote_post( $args_array );
            
            $obj_json = wp_remote_retrieve_body( $response_json );
            
            return $result = json_decode($obj_json, true);
        }

        /**
         * reCAPTCHA v2 Invisible verification
         * 
         * Call this function need a arg: $_POST['g-recaptcha-response']
         * 
         * @param $response   reCAPTCHA token $_POST['g-recaptcha-response']
         * 
         * @return Array    Array of verify result.
         * 
         */
        public function TomSreCAPTCHA_v2_invisible_verification( $response ) {

            $v2_invisible_secretkey   = esc_textarea( get_option('toms_recaptcha_v2_invisible_secret_key') );

            $verify_api     = esc_textarea( get_option('toms_recaptcha_verify_api', "0") ) == "0" ? 'google.com' : 'recaptcha.net';

            $remoteip       = esc_textarea( $_SERVER['REMOTE_ADDR'] );

            $g_recaptcha    = !empty($response) ? esc_textarea( $response ) : '';

            $verify_url     = 'https://www.' . esc_textarea( $verify_api ) . '/recaptcha/api/siteverify';

            $args_array = $verify_url . '?secret=' . $v2_invisible_secretkey . '&response=' . $g_recaptcha . '&remoteip=' . $remoteip;

            $response_json = wp_remote_post( $args_array );
            
            $obj_json = wp_remote_retrieve_body( $response_json );
            
            return $result = json_decode($obj_json, true);
        }

        /**
         * Add settings link to plugin actions
         *
         * @param  array  $plugin_actions
         * @param  string $plugin_file
         * @since  1.0
         * @return array
         */
        public function plugin_page_setting_button( $plugin_actions, $plugin_file ){
 
            if ( 'toms-recaptcha/toms-recaptcha.php' === $plugin_file ) {
                $plugin_actions[] = sprintf( __( '<a href="%s">Settings</a>', 'toms-recaptcha' ), esc_url( admin_url( 'admin.php?page=toms-recaptcha-settings' ) ) );
            }
            return $plugin_actions;
        }
    }
    
    $TomSreCAPTCHA = new TomSreCAPTCHA();
    
    //Include TomS Plugins main php file. glob() make the path as array.
    $toms_include_files_array = glob( plugin_dir_path( __FILE__ ) . "inc/*.php" );
    foreach ( $toms_include_files_array as $file ) {
        include_once $file;
    }
}
