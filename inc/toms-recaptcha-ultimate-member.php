<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(in_array('ultimate-member/ultimate-member.php', apply_filters('active_plugins', get_option('active_plugins'))) && !class_exists('TomSreCAPTCHA_UM') ){
   class TomSreCAPTCHA_UM {
       public function __construct() {
            add_action( 'TomSreCAPTCHAExtraForms', array($this, 'TomSreCAPTCHAUMOptions'), 11, 2 );
            add_action( 'TomSreCAPTCHAExtraFormsData', array($this, 'TomSreCAPTCHAUMOptionsData'), 11, 2);
            
            if( esc_textarea( get_option('toms_recaptcha_um_login_form' ) ) == "0" && !empty( esc_textarea($this->TomSreCAPTCHA_um_current_type_check()) ) ){
                if( esc_textarea( $this->TomSreCAPTCHA_um_current_type_check() ) == 'reCAPTCHA_v2_invisible' ){
                    $um_login_form = 'um_after_form';
                }else{
                    $um_login_form = 'um_after_form_fields';
                }
                add_action( $um_login_form, array($this, 'toms_recaptcha_um_login_form'), 10, 1);
                add_action( 'um_submit_form_errors_hook_login', array($this, 'toms_recaptcha_um_login_verification'), 10, 1 );
            }
            if( esc_textarea( get_option('toms_recaptcha_um_register_form', "0") ) == "0" && !empty( esc_textarea($this->TomSreCAPTCHA_um_current_type_check()) ) ){
                if( esc_textarea( $this->TomSreCAPTCHA_um_current_type_check() ) == 'reCAPTCHA_v2_invisible' ){
                    $um_register_form = 'um_after_form';
                }else{
                    $um_register_form = 'um_after_form_fields';
                }
                add_action( $um_register_form, array($this, 'toms_recaptcha_um_register_form'), 10, 1);
                add_action( 'um_submit_form_errors_hook__registration', array($this, 'toms_recaptcha_um_register_verification' ), 10, 1);
            }
            if( esc_textarea( get_option('toms_recaptcha_um_lostpassword_form', "0") ) == "0" && !empty( esc_textarea($this->TomSreCAPTCHA_um_current_type_check()) ) ){
                if( esc_textarea( $this->TomSreCAPTCHA_um_current_type_check() ) == 'reCAPTCHA_v2_invisible' ){
                    $um_lostpassword_form = 'um_after_form_fields';
                }else{
                    $um_lostpassword_form = 'um_after_password_reset_fields';
                }
                add_action( $um_lostpassword_form,  array($this, 'toms_recaptcha_um_lostpassword_form'), 10, 1);
                add_action( 'um_reset_password_errors_hook', array($this, 'toms_recaptcha_um_lostpassword_verification' ), 10, 1);
            }
        }

        function TomSreCAPTCHA_um_current_type_check(){
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
         *  Add Ultimate Member Support to TomS reCAPTCHA settings page 
        */
        function TomSreCAPTCHAUMOptions(){ ?>
            <!--Ultimate Member Forms-->
            <div class="toms-recaptcha-wordpress-default-forms"><?php _e('Ultimate Member', 'toms-recaptcha'); ?> : </div>
            <div class="toms-recaptcha-form-list">
                <div class="toms-recaptcha-forms-contents">
                    <label class="toms-label">
                        <input type="checkbox" name="toms_recaptcha_um_login_form" value="0" <?php if( esc_textarea( get_option('toms_recaptcha_um_login_form') ) == "0" )  echo 'checked="checked"'; ?> />
                        <span class="login-text"><?php _e('Login Form', 'toms-recaptcha'); ?></span>
                    </label>
                    <label class="toms-label">
                        <input type="checkbox" name="toms_recaptcha_um_register_form" value="0"  <?php if( esc_textarea( get_option('toms_recaptcha_um_register_form', "0") ) == "0" )  echo 'checked="checked"'; ?> />
                        <span class="register-text"><?php _e('Register Form', 'toms-recaptcha'); ?></span>
                    </label>
                    <label class="toms-label">
                        <input type="checkbox" name="toms_recaptcha_um_lostpassword_form" value="0"  <?php if( esc_textarea( get_option('toms_recaptcha_um_lostpassword_form', "0") ) == "0" )  echo 'checked="checked"'; ?> />
                        <span class="lostpassword-text"><?php _e('Lost Password Form', 'toms-recaptcha'); ?></span>
                    </label>
                    <span class="toms-label">
                        <span class="comment-text"></span>
                    </span>
                </div>
            </div>
        <?php }

        /**
         *  Insert Ultimate Member options to database
        */
        function TomSreCAPTCHAUMOptionsData(){
            update_option('toms_recaptcha_um_login_form', isset($_POST['toms_recaptcha_um_login_form']) ? sanitize_text_field( $_POST['toms_recaptcha_um_login_form'] ) : '' );
            update_option('toms_recaptcha_um_register_form', isset($_POST['toms_recaptcha_um_register_form']) ? sanitize_text_field( $_POST['toms_recaptcha_um_register_form'] ) : '' );
            update_option('toms_recaptcha_um_lostpassword_form', isset($_POST['toms_recaptcha_um_lostpassword_form']) ? sanitize_text_field( $_POST['toms_recaptcha_um_lostpassword_form'] ) : '' );
        }

       /**
        * Add TomS reCAPTCHA to Ultimate Member Login Form
        *
        * @param um-login   Ultimate Member Login Form id or class name.
        * 
        */
       function toms_recaptcha_um_login_form($args){
            $mode = $args['mode'];
            if( $mode == 'login' ){
                $id_class = '.um-login';
                $html = '';
                $current_type             = $this->TomSreCAPTCHA_um_current_type_check();
                $TomSreCAPTCHAFrontend    = new TomSreCAPTCHAFrontend();
                $TomSreCAPTCHA            = new TomSreCAPTCHA();
                $allowed_html             = $TomSreCAPTCHA->TomSreCAPTCHA_allow_html();
                if( $current_type == 'reCAPTCHA_v3' ){
                    $html .= $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V3_HTML($id_class);
                    $html .= '<input type="hidden" name="toms_recaptcha_um_error" id="toms_recaptcha_um_error" value="true" data-key="toms_recaptcha_um_error" />';
                    echo wp_kses( $html, $allowed_html);
                    wp_print_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V3_load_JS($id_class) );
                    wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V3_render_JS($id_class), [ 'type' => 'text/javascript' ] );
                }

                if( $current_type == 'reCAPTCHA_v2_checkbox'){
                    $html .= $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Checkbox_HTML($id_class);
                    $html .= '<input type="hidden" name="toms_recaptcha_um_error" id="toms_recaptcha_um_error" value="true" data-key="toms_recaptcha_um_error" />';
                    echo wp_kses( $html, $allowed_html);
                    wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Checkbox_JS_render($id_class), [ 'type' => 'text/javascript' ] );
                    wp_print_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Checkbox_load_JS($id_class) );
                }

                if( $current_type == 'reCAPTCHA_v2_invisible'){
                    wp_print_inline_script_tag( '
                        var TomSForm = document.querySelectorAll(".um-login form");
                        TomSForm[0].classList.add("um-login-toms-recaptcha-invisible");
                    ', [ 'type' => 'text/javascript' ] );
                    $html .= '<input type="hidden" name="toms_recaptcha_um_error" id="toms_recaptcha_um_error" value="true" data-key="toms_recaptcha_um_error" />';
                    echo wp_kses( $html, $allowed_html);
                    wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Invisible_onsubmit_JS('.um-login-toms-recaptcha-invisible'), [ 'type' => 'text/javascript' ] );
                    wp_print_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Invisible_load_JS('.um-login-toms-recaptcha-invisible') );
                    wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Invisible_render_JS('.um-login-toms-recaptcha-invisible'), [ 'type' => 'text/javascript' ] );
                }

                if ( UM()->fields()->is_error( 'toms_recaptcha_um_error' ) ) {
                    echo UM()->fields()->field_error( UM()->fields()->show_error( 'toms_recaptcha_um_error' ) );
                }
                
            }
       }

       /**
        * Ultimate Member Login Form reCAPTCHA verification
        * 
        */
       function toms_recaptcha_um_login_verification($args){
            $mode = $args['mode'];
            if( $mode == 'login'){
                
                $TomSreCAPTCHA = new TomSreCAPTCHA();

                if( esc_textarea( get_option('toms_recaptcha_type', 'reCAPTCHA_v3') ) == 'reCAPTCHA_v3'){

                    $response  = isset( $_POST['g-recaptcha'] ) && !empty( $_POST['g-recaptcha'] ) ? sanitize_text_field( $_POST['g-recaptcha'] ) : '';
                    
                    $result = $TomSreCAPTCHA->TomSreCAPTCHA_v3_verification( $response );

                    if( isset($result['success']) && $result['success'] == "1" &&
                        isset($result['action']) && $result['action'] == "TomSreCAPTCHAv3" &&
                        isset($result['score']) && $result['score'] >= 0.5
                    ){
                         // nothing to do
                    }else{
                        $error = isset($result['error-codes']) ? $result['error-codes'] : '';
                        if( is_array( $error ) ){
                            foreach( $error as $value ){
                                $error = $value;
                            }
                        }
                        //create an error
                        UM()->form()->add_error( 'toms_recaptcha_um_error', sprintf( __('ERROR:  Captcha verification failed, please try again!!! Error Codes: %s ', 'toms-recaptcha'), $error) );
                    }
                }

                if( esc_textarea( get_option('toms_recaptcha_type') ) == 'reCAPTCHA_v2_checkbox' ){
                    
                    $response  = isset( $_POST['g-recaptcha-response'] ) && !empty( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( $_POST['g-recaptcha-response'] ) : '';
                    
                    $result = $TomSreCAPTCHA->TomSreCAPTCHA_v2_checkbox_verification( $response );

                    if( isset($result['success']) && $result['success'] == "1" ){
                        // nothing to do
                    }else{
                        $error = isset($result['error-codes']) ? $result['error-codes'] : '';
                        if( is_array( $error ) ){
                            foreach( $error as $value ){
                                $error = $value;
                            }
                        }
                        //create an error
                        UM()->form()->add_error( 'toms_recaptcha_um_error', sprintf( __('ERROR:  Captcha verification failed, please try again!!! Error Codes: %s ', 'toms-recaptcha'), $error) );
                    }

                }

                if( esc_textarea( get_option('toms_recaptcha_type') ) == 'reCAPTCHA_v2_invisible'){
                    
                    $response  = isset( $_POST['g-recaptcha-response'] ) && !empty( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( $_POST['g-recaptcha-response'] ) : '';

                    $result = $TomSreCAPTCHA->TomSreCAPTCHA_v2_invisible_verification( $response );
                    
                    if( isset($result['success']) && $result['success'] == "1" ){
                        // nothing to do
                    }else{
                        $error = isset($result['error-codes']) ? $result['error-codes'] : '';
                        if( is_array( $error ) ){
                            foreach( $error as $value ){
                                $error = $value;
                            }                        
                        }
                        //create an error
                        UM()->form()->add_error( 'toms_recaptcha_um_error', sprintf( __('ERROR:  Captcha verification failed, please try again!!! Error Codes: %s ', 'toms-recaptcha'), $error) );
                   }

                }
            }
        }
        /**
        * Add TomS reCAPTCHA to Ultimate Member Register Form
        *
        * @param um-register   Ultimate Member Register Form id or class name.
        * 
        */
        function toms_recaptcha_um_register_form($args){
            $mode = $args['mode'];

            if( $mode == 'register' ){
                $id_class = '.um-register';
                $html = '';
                $current_type             = $this->TomSreCAPTCHA_um_current_type_check();
                $TomSreCAPTCHAFrontend    = new TomSreCAPTCHAFrontend();
                $TomSreCAPTCHA            = new TomSreCAPTCHA();
                $allowed_html             = $TomSreCAPTCHA->TomSreCAPTCHA_allow_html();
                if( $current_type == 'reCAPTCHA_v3' ){
                    $html .= $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V3_HTML($id_class);
                    $html .= '<input type="hidden" name="toms_recaptcha_um_error" id="toms_recaptcha_um_error" value="true" data-key="toms_recaptcha_um_error" />';
                    echo wp_kses( $html, $allowed_html);
                    wp_print_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V3_load_JS($id_class) );
                    wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V3_render_JS($id_class), [ 'type' => 'text/javascript' ] );
                }

                if( $current_type == 'reCAPTCHA_v2_checkbox'){
                    $html .= $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Checkbox_HTML($id_class);
                    $html .= '<input type="hidden" name="toms_recaptcha_um_error" id="toms_recaptcha_um_error" value="true" data-key="toms_recaptcha_um_error" />';
                    echo wp_kses( $html, $allowed_html);
                    wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Checkbox_JS_render($id_class), [ 'type' => 'text/javascript' ] );
                    wp_print_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Checkbox_load_JS($id_class) );
                }

                if( $current_type == 'reCAPTCHA_v2_invisible'){
                    wp_print_inline_script_tag( '
                        var TomSForm = document.querySelectorAll(".um-register form");
                        TomSForm[0].classList.add("um-register-toms-recaptcha-visible");
                    ', [ 'type' => 'text/javascript' ] );
                    $html .= '<input type="hidden" name="toms_recaptcha_um_error" id="toms_recaptcha_um_error" value="true" data-key="toms_recaptcha_um_error" />';
                    echo wp_kses( $html, $allowed_html);
                    wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Invisible_onsubmit_JS('.um-register-toms-recaptcha-visible'), [ 'type' => 'text/javascript' ] );
                    wp_print_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Invisible_load_JS('.um-register-toms-recaptcha-visible') );
                    wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Invisible_render_JS('.um-register-toms-recaptcha-visible'), [ 'type' => 'text/javascript' ] );
                }
                
                //Display error
                if ( UM()->fields()->is_error( 'toms_recaptcha_um_error' ) ) {
                    echo UM()->fields()->field_error( UM()->fields()->show_error( 'toms_recaptcha_um_error' ) );
                }
            }
        }
        /**
        * Ultimate Member Register Form reCAPTCHA verification
        * 
        */
       function toms_recaptcha_um_register_verification($args){
            $mode = $args['mode'];
            if( $mode == 'register'){
                $TomSreCAPTCHA = new TomSreCAPTCHA();

                if( esc_textarea( get_option('toms_recaptcha_type', 'reCAPTCHA_v3') ) == 'reCAPTCHA_v3'){

                    $response  = isset( $_POST['g-recaptcha'] ) && !empty( $_POST['g-recaptcha'] ) ? sanitize_text_field( $_POST['g-recaptcha'] ) : '';
                    
                    $result = $TomSreCAPTCHA->TomSreCAPTCHA_v3_verification( $response );

                    if( isset($result['success']) && $result['success'] == "1" &&
                        isset($result['action']) && $result['action'] == "TomSreCAPTCHAv3" &&
                        isset($result['score']) && $result['score'] >= 0.5
                    ){
                         // nothing to do
                    }else{
                        $error = isset($result['error-codes']) ? $result['error-codes'] : '';
                        if( is_array( $error ) ){
                            foreach( $error as $value ){
                                $error = $value;
                            }
                        }
                        //create an error
                        UM()->form()->add_error( 'toms_recaptcha_um_error', sprintf( __('ERROR:  Captcha verification failed, please try again!!! Error Codes: %s ', 'toms-recaptcha'), $error) );
                    }
                }

                if( esc_textarea( get_option('toms_recaptcha_type') ) == 'reCAPTCHA_v2_checkbox' ){
                    
                    $response  = isset( $_POST['g-recaptcha-response'] ) && !empty( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( $_POST['g-recaptcha-response'] ) : '';
                    
                    $result = $TomSreCAPTCHA->TomSreCAPTCHA_v2_checkbox_verification( $response );

                    if( isset($result['success']) && $result['success'] == "1" ){
                        // nothing to do
                    }else{
                        $error = isset($result['error-codes']) ? $result['error-codes'] : '';
                        if( is_array( $error ) ){
                            foreach( $error as $value ){
                                $error = $value;
                            }
                        }
                        //create an error
                        UM()->form()->add_error( 'toms_recaptcha_um_error', sprintf( __('ERROR:  Captcha verification failed, please try again!!! Error Codes: %s ', 'toms-recaptcha'), $error) );
                    }

                }

                if( esc_textarea( get_option('toms_recaptcha_type') ) == 'reCAPTCHA_v2_invisible'){
                    
                    $response  = isset( $_POST['g-recaptcha-response'] ) && !empty( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( $_POST['g-recaptcha-response'] ) : '';
                    
                    $result = $TomSreCAPTCHA->TomSreCAPTCHA_v2_invisible_verification( $response );

                    if( isset($result['success']) && $result['success'] == "1" ){
                        // nothing to do
                    }else{
                        $error = isset($result['error-codes']) ? $result['error-codes'] : '';
                        if( is_array( $error ) ){
                            foreach( $error as $value ){
                                $error = $value;
                            }                        
                        }
                        //create an error
                        UM()->form()->add_error( 'toms_recaptcha_um_error', sprintf( __('ERROR:  Captcha verification failed, please try again!!! Error Codes: %s ', 'toms-recaptcha'), $error) );
                    }

                }
            }
        }
        /**
        * Add TomS reCAPTCHA to Ultimate Member Lostpassword Form
        *
        * @param um-password   Ultimate Member Lostpassword Form id or class name.
        * 
        */
        function toms_recaptcha_um_lostpassword_form($args){
            $mode = $args['mode'];

            if( $mode == 'password' ){
                $id_class = '.um-password';
                $html = '
                <style>
                    .um-password .um-password-toms-recaptcha{
                        margin: 15px 0 0px 0 !important;
                    }
                </style>
                ';
                $current_type             = $this->TomSreCAPTCHA_um_current_type_check();
                $TomSreCAPTCHAFrontend    = new TomSreCAPTCHAFrontend();
                $TomSreCAPTCHA            = new TomSreCAPTCHA();
                $allowed_html             = $TomSreCAPTCHA->TomSreCAPTCHA_allow_html();
                if( $current_type == 'reCAPTCHA_v3' ){
                    $html .= $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V3_HTML($id_class);
                    $html .= '<input type="hidden" name="toms_recaptcha_um_error" id="toms_recaptcha_um_error" value="true" data-key="toms_recaptcha_um_error" />';
                    echo wp_kses( $html, $allowed_html);
                    wp_print_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V3_load_JS($id_class) );
                    wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V3_render_JS($id_class), [ 'type' => 'text/javascript' ] );
                }

                if( $current_type == 'reCAPTCHA_v2_checkbox'){
                    $html .= $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Checkbox_HTML($id_class);
                    $html .= '<input type="hidden" name="toms_recaptcha_um_error" id="toms_recaptcha_um_error" value="true" data-key="toms_recaptcha_um_error" />';
                    echo wp_kses( $html, $allowed_html);
                    wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Checkbox_JS_render($id_class), [ 'type' => 'text/javascript' ] );
                    wp_print_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Checkbox_load_JS($id_class) );
                }

                if( $current_type == 'reCAPTCHA_v2_invisible'){
                    wp_print_inline_script_tag( '
                        var TomSForm = document.querySelectorAll(".um-password form");
                        TomSForm[0].classList.add("um-password-toms-recaptcha-visible");
                    ', [ 'type' => 'text/javascript' ] );
                    $html .= '<input type="hidden" name="toms_recaptcha_um_error" id="toms_recaptcha_um_error" value="true" data-key="toms_recaptcha_um_error" />';
                    echo wp_kses( $html, $allowed_html);
                    wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Invisible_onsubmit_JS('.um-password-toms-recaptcha-visible'), [ 'type' => 'text/javascript' ] );
                    wp_print_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Invisible_load_JS('.um-password-toms-recaptcha-visible') );
                    wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Invisible_render_JS('.um-password-toms-recaptcha-visible'), [ 'type' => 'text/javascript' ] );
                }
                
                //Display error
                if ( UM()->fields()->is_error( 'toms_recaptcha_um_error' ) ) {
                    echo UM()->fields()->field_error( UM()->fields()->show_error( 'toms_recaptcha_um_error' ) );
                }
            }
        }
        /**
        * Ultimate Member Lostpassword Form reCAPTCHA verification
        * 
        */
        function toms_recaptcha_um_lostpassword_verification($args){
            $mode = $args['mode'];
            if( $mode == 'password'){
                $TomSreCAPTCHA = new TomSreCAPTCHA();

                if( esc_textarea( get_option('toms_recaptcha_type', 'reCAPTCHA_v3') ) == 'reCAPTCHA_v3'){

                    $response  = isset( $_POST['g-recaptcha'] ) && !empty( $_POST['g-recaptcha'] ) ? sanitize_text_field( $_POST['g-recaptcha'] ) : '';
                    
                    $result = $TomSreCAPTCHA->TomSreCAPTCHA_v3_verification( $response );

                    if( isset($result['success']) && $result['success'] == "1" &&
                        isset($result['action']) && $result['action'] == "TomSreCAPTCHAv3" &&
                        isset($result['score']) && $result['score'] >= 0.5
                    ){
                         // nothing to do
                    }else{
                        $error = isset($result['error-codes']) ? $result['error-codes'] : '';
                        if( is_array( $error ) ){
                            foreach( $error as $value ){
                                $error = $value;
                            }
                        }
                        //create an error
                        UM()->form()->add_error( 'toms_recaptcha_um_error', sprintf( __('ERROR:  Captcha verification failed, please try again!!! Error Codes: %s ', 'toms-recaptcha'), $error) );
                    }
                }

                if( esc_textarea( get_option('toms_recaptcha_type') ) == 'reCAPTCHA_v2_checkbox' ){
                    
                    $response  = isset( $_POST['g-recaptcha-response'] ) && !empty( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( $_POST['g-recaptcha-response'] ) : '';
                    
                    $result = $TomSreCAPTCHA->TomSreCAPTCHA_v2_checkbox_verification( $response );

                    if( isset($result['success']) && $result['success'] == "1" ){
                        // nothing to do
                    }else{
                        $error = isset($result['error-codes']) ? $result['error-codes'] : '';
                        if( is_array( $error ) ){
                            foreach( $error as $value ){
                                $error = $value;
                            }
                        }
                        //create an error
                        UM()->form()->add_error( 'toms_recaptcha_um_error', sprintf( __('ERROR:  Captcha verification failed, please try again!!! Error Codes: %s ', 'toms-recaptcha'), $error) );
                    }

                }

                if( esc_textarea( get_option('toms_recaptcha_type') ) == 'reCAPTCHA_v2_invisible'){
                    
                    $response  = isset( $_POST['g-recaptcha-response'] ) && !empty( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( $_POST['g-recaptcha-response'] ) : '';
                    
                    $result = $TomSreCAPTCHA->TomSreCAPTCHA_v2_invisible_verification( $response );

                    if( isset($result['success']) && $result['success'] == "1" ){
                        // nothing to do
                    }else{
                        $error = isset($result['error-codes']) ? $result['error-codes'] : '';
                        if( is_array( $error ) ){
                            foreach( $error as $value ){
                                $error = $value;
                            }                        }
                            //create an error
                            UM()->form()->add_error( 'toms_recaptcha_um_error', sprintf( __('ERROR:  Captcha verification failed, please try again!!! Error Codes: %s ', 'toms-recaptcha'), $error) );
                    }

                }
            }
        }
    }
    $TomSreCAPTCHA_UM = new TomSreCAPTCHA_UM();
}