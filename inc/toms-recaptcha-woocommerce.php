<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) && !class_exists('TomSreCAPTCHAWoocommerce') ){
    class TomSreCAPTCHAWoocommerce {

        public function __construct(){
            add_action( 'TomSreCAPTCHAExtraForms', array($this, 'TomSreCAPTCHAWoocommerceOptions'), 10, 2 );
            add_action( 'TomSreCAPTCHAExtraFormsData', array($this, 'TomSreCAPTCHAWoocommerceOptionsData'), 10, 2);

            if( esc_textarea( get_option('toms_recaptcha_woo_login_form' ) ) == "0" && !empty( esc_textarea($this->TomSreCAPTCHA_woo_current_type_check()) ) ){
                add_action( 'woocommerce_login_form', array($this, 'add_TomSreCAPTCHA_to_woo_login_form'));
                add_filter( "woocommerce_process_login_errors", array($this, 'TomSreCAPTCHA_woo_login_form_verification'));

                //v2 invisible
                add_action( 'woocommerce_login_form_end', array($this, 'TomSreCAPTCHA_woo_login_v2_invisible_after'));
            }
            if( esc_textarea( get_option('toms_recaptcha_woo_register_form', "0") ) == "0" && !empty( esc_textarea($this->TomSreCAPTCHA_woo_current_type_check()) ) ){
                add_action( 'woocommerce_register_form',  array($this, 'add_TomSreCAPTCHA_to_woo_register_form'), 10, 2 );
                add_filter( 'woocommerce_process_registration_errors', array($this, 'TomSreCAPTCHA_woo_register_form_verification'), 10, 3 );

                //v2 invisible
                add_action( 'woocommerce_register_form_end', array($this, 'TomSreCAPTCHA_woo_register_v2_invisible_after'));
            }
            if( esc_textarea( get_option('toms_recaptcha_woo_lostpassword_form', "0") ) == "0" && !empty( esc_textarea($this->TomSreCAPTCHA_woo_current_type_check()) ) ){
                add_action('woocommerce_lostpassword_form', array($this, 'add_TomSreCAPTCHA_to_woocommerce_lostpassword_form'), 10, 2 );
                add_filter( 'allow_password_reset', array($this, 'TomSreCAPTCHA_woo_lostpassword_form_verification'), 10, 3);

                //TomSreCAPTCHA_woo_lostpassword_v2_invisible_after
                add_action('woocommerce_after_lost_password_form', array($this, 'TomSreCAPTCHA_woo_lostpassword_v2_invisible_after'), 10, 2 );
            }
            if( esc_textarea( get_option('toms_recaptcha_woo_checkout_page', "0") ) == "0" && !empty( esc_textarea($this->TomSreCAPTCHA_woo_current_type_check()) ) ){
                //v2 checkbox and v3
                add_action( 'woocommerce_review_order_before_payment', array( $this, 'add_TomSreCAPTCHA_to_woocommerce_checkout' ), 10, 2);
                add_action( 'woocommerce_checkout_process', array( $this, 'TomSreCAPTCHA_woo_checkout_verification' ), 10, 2 );

                //v2 invisible
                add_filter( 'woocommerce_order_button_html', array( $this, 'toms_checkout_v2_invisible_filter' ));
                add_action( 'woocommerce_checkout_before_order_review', array( $this, 'TomSreCAPTCHA_woo_checkout_v2_invisible_into' ), 10, 2);
                add_action( 'woocommerce_after_checkout_form', array( $this, 'TomSreCAPTCHA_woo_checkout_v2_invisible_after' ), 10, 2);
            }
        }

        function TomSreCAPTCHA_woo_current_type_check(){
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
         *  Add Woocommerce Support to TomS reCAPTCHA settings page 
        */
        function TomSreCAPTCHAWoocommerceOptions(){ ?>
            <!--Woocommerce Forms-->
            <div class="toms-recaptcha-wordpress-default-forms"><?php _e('Woocommerce', 'toms-reCAPTCHA'); ?> : </div>
            <div class="toms-recaptcha-form-list">
                <div class="toms-recaptcha-forms-contents">
                    <label class="toms-label">
                        <input type="checkbox" name="toms_recaptcha_woo_login_form" value="0" <?php if( esc_textarea( get_option('toms_recaptcha_woo_login_form') ) == "0" )  echo 'checked="checked"'; ?> />
                        <span class="login-text"><?php _e('Login Form', 'toms-reCAPTCHA'); ?></span>
                    </label>
                    <label class="toms-label">
                        <input type="checkbox" name="toms_recaptcha_woo_register_form" value="0"  <?php if( esc_textarea( get_option('toms_recaptcha_woo_register_form', "0") ) == "0" )  echo 'checked="checked"'; ?> />
                        <span class="register-text"><?php _e('Register Form', 'toms-reCAPTCHA'); ?></span>
                    </label>
                    <label class="toms-label">
                        <input type="checkbox" name="toms_recaptcha_woo_lostpassword_form" value="0"  <?php if( esc_textarea( get_option('toms_recaptcha_woo_lostpassword_form', "0") ) == "0" )  echo 'checked="checked"'; ?> />
                        <span class="lostpassword-text"><?php _e('Lost Password Form', 'toms-reCAPTCHA'); ?></span>
                    </label>
                    <label class="toms-label">
                        <input type="checkbox" name="toms_recaptcha_woo_checkout_page" value="0"  <?php if( esc_textarea( get_option('toms_recaptcha_woo_checkout_page', "0") ) == "0" )  echo 'checked="checked"'; ?> />
                        <span class="checkout-text"><?php _e('Checkout Billing Form', 'toms-reCAPTCHA'); ?></span>
                    </label>
                </div>
            </div>
        <?php }

        /**
         *  Insert Woocommerce options to database
        */
        function TomSreCAPTCHAWoocommerceOptionsData(){
            update_option('toms_recaptcha_woo_login_form', isset($_POST['toms_recaptcha_woo_login_form']) ? sanitize_text_field( $_POST['toms_recaptcha_woo_login_form'] ) : '' );
            update_option('toms_recaptcha_woo_register_form', isset($_POST['toms_recaptcha_woo_register_form']) ? sanitize_text_field( $_POST['toms_recaptcha_woo_register_form'] ) : '' );
            update_option('toms_recaptcha_woo_lostpassword_form', isset($_POST['toms_recaptcha_woo_lostpassword_form']) ? sanitize_text_field( $_POST['toms_recaptcha_woo_lostpassword_form'] ) : '' );
            update_option('toms_recaptcha_woo_checkout_page', isset($_POST['toms_recaptcha_woo_checkout_page']) ? sanitize_text_field( $_POST['toms_recaptcha_woo_checkout_page'] ) : '' );
        }

        /**
         * Woocommerce Login Form
         * 
         * @param woocommerce-form-login  Woocommerce login form class name.
         * 
         * Warnning: Work woocommerce->settings->Advanced->My account page only.
        */
        function add_TomSreCAPTCHA_to_woo_login_form(){
            if( !is_user_logged_in() ){
                $id_class = '.woocommerce-form-login';
                $html = '
                <style>
                    .woocommerce-form-login .woocommerce-form-login-toms-recaptcha{
                        margin: 12px 0 10px 0 !important;
                        box-sizing: border-box;
                    }
                </style>
                ';
                $current_type = $this->TomSreCAPTCHA_woo_current_type_check();
                $TomSreCAPTCHAFrontend = new TomSreCAPTCHAFrontend();
                $TomSreCAPTCHA = new TomSreCAPTCHA();
                $allowed_html = $TomSreCAPTCHA->TomSreCAPTCHA_allow_html();
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
            }
        }
        /** 
         *  After login form | v2 invisible
         * 
         */
        function TomSreCAPTCHA_woo_login_v2_invisible_after(){
            $current_type = $this->TomSreCAPTCHA_woo_current_type_check();
            if( !is_user_logged_in() && $current_type == 'reCAPTCHA_v2_invisible' ){
                $div_id     = "toms-woo-login-form";
                $input_id   = "toms-woo-login-form-g-recaptcha";

                $TomSreCAPTCHAFrontend = new TomSreCAPTCHAFrontend();
                $html = $TomSreCAPTCHAFrontend->TomSreCAPTCHA_woo_v2_invisible_html($div_id, $input_id);
                $TomSreCAPTCHA = new TomSreCAPTCHA();
                $allowed_html = $TomSreCAPTCHA->TomSreCAPTCHA_allow_html();
                echo wp_kses( $html, $allowed_html);

                // Add Call Back function in header tag
                add_action( 'wp_head', 'woo_login_header' );
                function woo_login_header(){
                    $div_id             = "toms-woo-login-form";
                    $input_id           = "toms-woo-login-form-g-recaptcha";
                    $onload_callback    = "tomsWooLoginFormOnload";
                    $render_var         = "tomsWooLoginFormRender";
                    $token_var          = "tomsWooLoginFormToken";
                    $TomSreCAPTCHAFrontend = new TomSreCAPTCHAFrontend();
                    $js = $TomSreCAPTCHAFrontend->TomSreCAPTCHA_woo_v2_invisible_header( $div_id, $input_id, $onload_callback, $render_var, $token_var );
                    wp_print_inline_script_tag( $js,[ 'type' => 'text/javascript' ]);
                }

                //api js
                $onload_callback    = "tomsWooLoginFormOnload";
                $render_var         = "tomsWooLoginFormRender";
                $reset_callback     = "tomsWooLoginFormReset";
                $TomSreCAPTCHAFrontend = new TomSreCAPTCHAFrontend();
                $js_url = $TomSreCAPTCHAFrontend->TomSreCAPTCHA_woo_v2_invisible_api( $onload_callback );
                wp_print_inline_script_tag(null, array(
                    'type'  => 'text/javascript',
                    'async' => true,
                    'src'   => $js_url
                ));
                $js_reset = $TomSreCAPTCHAFrontend->TomSreCAPTCHA_woo_v2_invisible_reset( $reset_callback, $render_var );
                wp_print_inline_script_tag( $js_reset,[ 'type' => 'text/javascript' ]);

                //add reset function to login button
                wp_print_inline_script_tag( 
                    '
                    var woo_login_button = document.getElementsByClassName("woocommerce-form-login__submit")[0]
                    woo_login_button.setAttribute("onclick","tomsWooLoginFormReset()")
                    ',[ 'type' => 'text/javascript' ]);
            }
        }

        /**
         * Woocommerce Login Form reCAPTCHA verification
         * 
         *  reCAPTCHA v3 $_POST['g-recaptcha']
         *  reCAPTCHA v2 $_POST['g-recaptcha-response']
         * 
         * @param $user  if the reCAPTCHA passed, will allow user login, else return ERROR.
         * 
         * Warnning: Work woocommerce->settings->Advanced->My account page only.
         * 
        */
        function TomSreCAPTCHA_woo_login_form_verification($user){
            $TomSreCAPTCHA = new TomSreCAPTCHA();
            if( esc_textarea( get_option('toms_recaptcha_type', 'reCAPTCHA_v3') ) == 'reCAPTCHA_v3'){

                $response  = isset( $_POST['g-recaptcha'] ) && !empty( $_POST['g-recaptcha'] ) ? sanitize_text_field( $_POST['g-recaptcha'] ) : '';
                
                $result = $TomSreCAPTCHA->TomSreCAPTCHA_v3_verification( $response );

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
                
                $result = $TomSreCAPTCHA->TomSreCAPTCHA_v2_checkbox_verification( $response );

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

                $toms_resp  = isset( $_POST['toms-woo-login-form-g-recaptcha'] ) ? sanitize_text_field( $_POST['toms-woo-login-form-g-recaptcha'] ) : '';
                
                $response  = isset( $_POST['g-recaptcha-response'] ) && !empty( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( $_POST['g-recaptcha-response'] ) : '';

                if( !empty( $response ) ){
                    $response =  $response;
                }else{
                    $response =  $toms_resp;
                }
                
                $result = $TomSreCAPTCHA->TomSreCAPTCHA_v2_invisible_verification( $response );

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
            return $user;
        }

        /**
         * Woocommerce Register Form
         * 
         * @param woocommerce-form-register  Woocommerce register form class name.
         * 
         *  Warnning: Work woocommerce->settings->Advanced->My account page only.
         * 
        */
        function add_TomSreCAPTCHA_to_woo_register_form(){
            if( !is_user_logged_in() ){
                $id_class = '.woocommerce-form-register';
                $html = '
                <style>
                    .woocommerce-form-register .woocommerce-form-register-toms-recaptcha{
                        margin: 12px 0 10px 0 !important;
                        box-sizing: border-box;
                    }
                </style>
                ';
                $current_type = $this->TomSreCAPTCHA_woo_current_type_check();
                $TomSreCAPTCHAFrontend = new TomSreCAPTCHAFrontend();
                $TomSreCAPTCHA = new TomSreCAPTCHA();
                $allowed_html = $TomSreCAPTCHA->TomSreCAPTCHA_allow_html();
                if( $current_type == 'reCAPTCHA_v3' ){
                    $html .= $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V3_HTML($id_class);
                    $html .= '<input type="hidden" name="toms_recaptcha_shortcode" value="true" />';
                    echo wp_kses( $html, $allowed_html);
                    wp_print_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V3_load_JS($id_class) );
                    wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V3_render_JS($id_class), [ 'type' => 'text/javascript' ] );
                }

                if( $current_type == 'reCAPTCHA_v2_checkbox'){
                    $html .= $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Checkbox_HTML($id_class);
                    $html .= '<input type="hidden" name="toms_recaptcha_shortcode" value="true" />';
                    echo wp_kses( $html, $allowed_html);
                    wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Checkbox_JS_render($id_class), [ 'type' => 'text/javascript' ] );
                    wp_print_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Checkbox_load_JS($id_class) );
                }

                if( $current_type == 'reCAPTCHA_v2_invisible'){
                    $html .= '<input type="hidden" name="toms_recaptcha_shortcode" value="true" />';
                    echo wp_kses( $html, $allowed_html);
                }
            }
        }
        /** 
         *  After register form | v2 invisible
         * 
         */
        function TomSreCAPTCHA_woo_register_v2_invisible_after(){
            $current_type = $this->TomSreCAPTCHA_woo_current_type_check();
            if( !is_user_logged_in() && $current_type == 'reCAPTCHA_v2_invisible' ){
                $div_id     = "toms-woo-register-form";
                $input_id   = "toms-woo-register-form-g-recaptcha";

                //Output container
                $TomSreCAPTCHAFrontend = new TomSreCAPTCHAFrontend();
                $html = $TomSreCAPTCHAFrontend->TomSreCAPTCHA_woo_v2_invisible_html($div_id, $input_id);
                $TomSreCAPTCHA = new TomSreCAPTCHA();
                $allowed_html = $TomSreCAPTCHA->TomSreCAPTCHA_allow_html();
                echo wp_kses( $html, $allowed_html);

                // Add Call Back function in header tag
                add_action( 'wp_head', 'woo_register_header' );
                function woo_register_header(){
                    $div_id             = "toms-woo-register-form";
                    $input_id           = "toms-woo-register-form-g-recaptcha";
                    $onload_callback    = "tomsWooRegisterFormOnload";
                    $render_var         = "tomsWooRegisterFormRender";
                    $token_var          = "tomsWooRegisterFormToken";
                    $TomSreCAPTCHAFrontend = new TomSreCAPTCHAFrontend();
                    $js = $TomSreCAPTCHAFrontend->TomSreCAPTCHA_woo_v2_invisible_header( $div_id, $input_id, $onload_callback, $render_var, $token_var );
                    wp_print_inline_script_tag( $js,[ 'type' => 'text/javascript' ]);
                }

                //api js
                $onload_callback    = "tomsWooRegisterFormOnload";
                $render_var         = "tomsWooRegisterFormRender";
                $reset_callback     = "tomsWooRegisterFormReset";
                $js_url = $TomSreCAPTCHAFrontend->TomSreCAPTCHA_woo_v2_invisible_api( $onload_callback );
                wp_print_inline_script_tag(null, array(
                    'type'  => 'text/javascript',
                    'async' => true,
                    'src'   => $js_url
                ));
                $js_reset = $TomSreCAPTCHAFrontend->TomSreCAPTCHA_woo_v2_invisible_reset( $reset_callback, $render_var );
                wp_print_inline_script_tag( $js_reset,[ 'type' => 'text/javascript' ]);

                //add reset function to Register button
                wp_print_inline_script_tag( 
                    '
                    var woo_register_button = document.getElementsByClassName("woocommerce-form-register__submit")[0]
                    woo_register_button.setAttribute("onclick","' . esc_textarea( $reset_callback ) . '()")
                    ',[ 'type' => 'text/javascript' ]);
            }
        }
        /**
         * Woocommerce Register Form reCAPTCHA verification
         * 
         *  reCAPTCHA v3 $_POST['g-recaptcha']
         *  reCAPTCHA v2 $_POST['g-recaptcha-response']
         * 
         * @return $errors
         * 
         *  Warnning: Work woocommerce->settings->Advanced->My account page only.
         * 
        */
        function TomSreCAPTCHA_woo_register_form_verification($errors, $username, $email ){
            $TomSreCAPTCHA = new TomSreCAPTCHA();
            if( esc_textarea( get_option('toms_recaptcha_type', 'reCAPTCHA_v3') ) == 'reCAPTCHA_v3'){

                $response  = isset( $_POST['g-recaptcha'] ) && !empty( $_POST['g-recaptcha'] ) ? sanitize_text_field( $_POST['g-recaptcha'] ) : '';
                
                $result = $TomSreCAPTCHA->TomSreCAPTCHA_v3_verification( $response );

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
                
                $result = $TomSreCAPTCHA->TomSreCAPTCHA_v2_checkbox_verification( $response );

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

                $toms_resp  = isset( $_POST['toms-checkout-form-g-recaptcha'] ) ? sanitize_text_field( $_POST['toms-checkout-form-g-recaptcha'] ) : '';
                $response   = isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( $_POST['g-recaptcha-response'] ) : '';

                if( !empty( $response ) ){
                    $response =  $response;
                }else{
                    $response =  $toms_resp;
                }
                $result = $TomSreCAPTCHA->TomSreCAPTCHA_v2_invisible_verification( $response );

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
            return $errors;
        }

        /**
         * Woocommerce Lost Password Form
         * 
         * @param lostpasswordform  Woocommerce Lost Password form class name.
         * 
         * Only verify the data from woocommerce lostpassword page 
        */
        function add_TomSreCAPTCHA_to_woocommerce_lostpassword_form(){
            if( !is_user_logged_in() ){
                $id_class   = '.woocommerce-ResetPassword';
                $input      = 'button';
                $html = '
                    <style>
                        .woocommerce-ResetPassword .woocommerce-ResetPassword-toms-recaptcha{
                            margin: 12px 0 10px 0 !important;
                            box-sizing: border-box;
                        }
                    </style>
                ';
                $current_type = $this->TomSreCAPTCHA_woo_current_type_check();
                $TomSreCAPTCHAFrontend = new TomSreCAPTCHAFrontend();
                $TomSreCAPTCHA = new TomSreCAPTCHA();
                $allowed_html = $TomSreCAPTCHA->TomSreCAPTCHA_allow_html();
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

                    $div_id     = "toms-woo-lostpassword-form";
                    $input_id   = "toms-woo-lostpassword-form-g-recaptcha";
                    //Output container
                    $html .= $TomSreCAPTCHAFrontend->TomSreCAPTCHA_woo_v2_invisible_html($div_id, $input_id);
                    echo wp_kses( $html, $allowed_html);

                    // Add Call Back function in header tag
                    add_action( 'wp_head', 'woo_lostpassword_header' );
                    function woo_lostpassword_header(){
                        $div_id             = "toms-woo-lostpassword-form";
                        $input_id           = "toms-woo-lostpassword-form-g-recaptcha";
                        $onload_callback    = "tomsWooLostpasswordFormOnload";
                        $render_var         = "tomsWooLostpasswordFormRender";
                        $token_var          = "tomsWooLostpasswordFormToken";
                        $TomSreCAPTCHAFrontend = new TomSreCAPTCHAFrontend();
                        $js = $TomSreCAPTCHAFrontend->TomSreCAPTCHA_woo_v2_invisible_header( $div_id, $input_id, $onload_callback, $render_var, $token_var );
                        wp_print_inline_script_tag( $js,[ 'type' => 'text/javascript' ]);
                    }
                }
            }
        }

        function TomSreCAPTCHA_woo_lostpassword_v2_invisible_after(){

            //api js
            $onload_callback    = "tomsWooLostpasswordFormOnload";
            $render_var         = "tomsWooLostpasswordFormRender";
            $reset_callback     = "tomsWooLostpasswordFormReset";
            $TomSreCAPTCHAFrontend = new TomSreCAPTCHAFrontend();
            $js_url = $TomSreCAPTCHAFrontend->TomSreCAPTCHA_woo_v2_invisible_api( $onload_callback );
            wp_print_inline_script_tag(null, array(
                'type'  => 'text/javascript',
                'async' => true,
                'src'   => $js_url
            ));
            //reset function
            $js_reset = $TomSreCAPTCHAFrontend->TomSreCAPTCHA_woo_v2_invisible_reset( $reset_callback, $render_var );
            wp_print_inline_script_tag( $js_reset,[ 'type' => 'text/javascript' ]);

            //add reset function to Register button
            wp_print_inline_script_tag( 
                '
                var woo_register_button = document.getElementsByClassName("woocommerce-Button")[0]
                woo_register_button.setAttribute("onclick","' . esc_textarea( $reset_callback ) . '()")
                ',[ 'type' => 'text/javascript' ]);
        }
        /**
         * Woocommerce Lost Password Form reCAPTCHA verification
         * 
         *  reCAPTCHA v3 $_POST['g-recaptcha']
         *  reCAPTCHA v2 $_POST['g-recaptcha-response']
         * 
         * @param $true
         * 
         *  Warnning:  Verify the data from woocommerce lostpassword page Only.
        */
        function TomSreCAPTCHA_woo_lostpassword_form_verification($true){
            //Get Woocommerce Lostpassword URL
            $woo_account_url            = untrailingslashit(get_permalink( get_option('woocommerce_myaccount_page_id') ));
            $wp_link_type               = empty( esc_textarea( get_option( 'permalink_structure' ) ) ) ? '&' : '/';
            $woo_lostpasswd_endpoint    = esc_textarea(get_option( 'woocommerce_myaccount_lost_password_endpoint' ));
            $woo_lostpasswd_url         = untrailingslashit( $woo_account_url . $wp_link_type . $woo_lostpasswd_endpoint );

            //Get current page URL
            $current_page_url = untrailingslashit( home_url( $_SERVER["REQUEST_URI"]) );

            if( esc_url( $current_page_url ) == esc_url( $woo_lostpasswd_url ) ) {  
                $TomSreCAPTCHA = new TomSreCAPTCHA();
                if( esc_textarea( get_option('toms_recaptcha_type', 'reCAPTCHA_v3') ) == 'reCAPTCHA_v3'){

                    $response  = isset( $_POST['g-recaptcha'] ) && !empty( $_POST['g-recaptcha'] ) ? sanitize_text_field( $_POST['g-recaptcha'] ) : '';
                    
                    $result = $TomSreCAPTCHA->TomSreCAPTCHA_v3_verification( $response );

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
                    
                    $result = $TomSreCAPTCHA->TomSreCAPTCHA_v2_checkbox_verification( $response );

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
                    
                    $toms_resp  = isset( $_POST['toms-woo-lostpassword-form-g-recaptcha'] ) ? sanitize_text_field( $_POST['toms-woo-lostpassword-form-g-recaptcha'] ) : '';
                    $response   = isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( $_POST['g-recaptcha-response'] ) : '';

                    if( !empty( $response ) ){
                        $response =  $response;
                    }else{
                        $response =  $toms_resp;
                    }
                    $result = $TomSreCAPTCHA->TomSreCAPTCHA_v2_invisible_verification( $response );

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
         * Woocommerce checkout page
         * 
         * @param woocommerce-checkout  Woocommerce checkout form class name.
         * 
         * Warnning: Work woocommerce->settings->Advanced->Checkout page only.
         * 
         * Only no login user
        */
        function add_TomSreCAPTCHA_to_woocommerce_checkout(){
            $id_class = '.checkout';
            $html = '';

            if( !is_user_logged_in() ) {
                $current_type = $this->TomSreCAPTCHA_woo_current_type_check();
                $TomSreCAPTCHAFrontend = new TomSreCAPTCHAFrontend();
                $TomSreCAPTCHA = new TomSreCAPTCHA();
                $allowed_html = $TomSreCAPTCHA->TomSreCAPTCHA_allow_html();
                if( $current_type == 'reCAPTCHA_v3' ){
                    $html .= $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V3_HTML($id_class);
                    echo wp_kses( $html, $allowed_html);
                    wp_print_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V3_load_JS($id_class) );
                    wp_print_inline_script_tag(
                        'function getReCaptcha(){' 
                            . $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V3_render_JS($id_class) .
                        '}
                        getReCaptcha();
                        setInterval(function(){getReCaptcha();}, 110000);',
                        [ 'type' => 'text/javascript' ]
                    );
                }

                if( $current_type == 'reCAPTCHA_v2_checkbox'){
                    $html .= $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Checkbox_HTML($id_class);
                    echo wp_kses( $html, $allowed_html);
                    wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Checkbox_JS_render($id_class), [ 'type' => 'text/javascript' ] );
                    wp_print_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Checkbox_load_JS($id_class) );
                }
            }
        }

        /** 
         *  Into checkout place_holder form | v2 invisible
         * 
         */
        function TomSreCAPTCHA_woo_checkout_v2_invisible_into(){
            $current_type = $this->TomSreCAPTCHA_woo_current_type_check();
            if( !is_user_logged_in() && $current_type == 'reCAPTCHA_v2_invisible' ){
                $div_id     = "toms-checkout-form";
                $input_id   = "toms-checkout-form-g-recaptcha";


                $woo_checkout_url   = get_permalink( get_option('woocommerce_checkout_page_id') );
                $current_page_url   = home_url( $_SERVER["REQUEST_URI"]);

                if( esc_url( $current_page_url ) == esc_url( $woo_checkout_url ) ) {
                    $current_type = $this->TomSreCAPTCHA_woo_current_type_check();
                    $TomSreCAPTCHAFrontend = new TomSreCAPTCHAFrontend();
                    $TomSreCAPTCHA = new TomSreCAPTCHA();
                    $allowed_html = $TomSreCAPTCHA->TomSreCAPTCHA_allow_html();

                    if( $current_type == 'reCAPTCHA_v2_invisible'){
                        $html = $TomSreCAPTCHAFrontend->TomSreCAPTCHA_woo_v2_invisible_html($div_id, $input_id);
                        echo wp_kses( $html, $allowed_html);
                    }
                }
            }
        }

        /** 
         *  After checkout form | v2 invisible
         * 
         */
        function TomSreCAPTCHA_woo_checkout_v2_invisible_after(){
            $current_type = $this->TomSreCAPTCHA_woo_current_type_check();
            if( !is_user_logged_in() && $current_type == 'reCAPTCHA_v2_invisible' ){
                // Add Call Back function in header tag
                add_action( 'wp_head', 'checkout_header' );
                function checkout_header(){
                    $div_id             = "toms-checkout-form";
                    $input_id           = "toms-checkout-form-g-recaptcha";
                    $onload_callback    = "tomsCheckoutFormOnload";
                    $render_var         = "tomsCheckoutFormRender";
                    $token_var          = "tomsCheckoutFormToken";
                    $TomSreCAPTCHAFrontend = new TomSreCAPTCHAFrontend();
                    $js = $TomSreCAPTCHAFrontend->TomSreCAPTCHA_woo_v2_invisible_header( $div_id, $input_id, $onload_callback, $render_var, $token_var );
                    wp_print_inline_script_tag( $js,[ 'type' => 'text/javascript' ]);
                }

                //api js
                $onload_callback    = "tomsCheckoutFormOnload";
                $render_var         = "tomsCheckoutFormRender";
                $reset_callback     = "tomsCheckoutFormReset";
                $TomSreCAPTCHAFrontend = new TomSreCAPTCHAFrontend();
                $js_url = $TomSreCAPTCHAFrontend->TomSreCAPTCHA_woo_v2_invisible_api( $onload_callback );
                wp_print_inline_script_tag(null, array(
                    'type'  => 'text/javascript',
                    'async' => true,
                    'src'   => $js_url
                ));
                $js_reset = $TomSreCAPTCHAFrontend->TomSreCAPTCHA_woo_v2_invisible_reset( $reset_callback, $render_var );
                wp_print_inline_script_tag( $js_reset,[ 'type' => 'text/javascript' ]);
            }
        }

        /**
         *  Add Reset reCaptcha event on place_holder button  | v2 invisible
         *  Php add attributes for html
        */
        function toms_checkout_v2_invisible_filter( $contents ){
            $current_type = $this->TomSreCAPTCHA_woo_current_type_check();
            if( !is_user_logged_in() && $current_type == 'reCAPTCHA_v2_invisible' ){
                $reset_callback     = "tomsCheckoutFormReset";
                $button_contents    = $contents;
                $TomSreCAPTCHAFrontend = new TomSreCAPTCHAFrontend();
                $newHtml = $TomSreCAPTCHAFrontend->TomSreCAPTCHA_woo_v2_invisible_button_filter( $button_contents, $reset_callback );
                return $newHtml;
            }
            return $contents;
        }

         /**
         * Woocommerce checkout page verification
         * 
         *  reCAPTCHA v3 $_POST['g-recaptcha']
         *  reCAPTCHA v2 $_POST['g-recaptcha-response']
         * 
         * @return $errors
         * 
         * Warnning: Work woocommerce->settings->Advanced->Checkout page only.
         * 
         * Only no login user
        */
        function TomSreCAPTCHA_woo_checkout_verification(){
            if( !is_user_logged_in() ){
                $TomSreCAPTCHA = new TomSreCAPTCHA();
                if( esc_textarea( get_option('toms_recaptcha_type', 'reCAPTCHA_v3') ) == 'reCAPTCHA_v3'){

                    $response  = isset( $_POST['g-recaptcha'] ) && !empty( $_POST['g-recaptcha'] ) ? sanitize_text_field( $_POST['g-recaptcha'] ) : '';
                    
                    $result = $TomSreCAPTCHA->TomSreCAPTCHA_v3_verification( $response );

                    if( isset($result['success']) && $result['success'] == "1" &&
                        isset($result['action']) && $result['action'] == "TomSreCAPTCHAv3" &&
                        isset($result['score']) && $result['score'] >= 0.5
                    ){
                        //not thing to do;
                    }else{
                        $error = isset($result['error-codes']) ? $result['error-codes'] : '';
                        if( is_array( $error ) ){
                            foreach( $error as $value ){
                                $error = $value;
                            }
                        }
                        wc_add_notice( sprintf( __('<strong>ERROR</strong>: Captcha verification failed, please try again!!! <br>Error Codes: %s ', 'toms-recaptcha'), $error), 'error' );
                    }
                }

                if( esc_textarea( get_option('toms_recaptcha_type') ) == 'reCAPTCHA_v2_checkbox' ){
                    
                    $response  = isset( $_POST['g-recaptcha-response'] ) && !empty( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( $_POST['g-recaptcha-response'] ) : '';
                    
                    $result = $TomSreCAPTCHA->TomSreCAPTCHA_v2_checkbox_verification( $response );

                    if( isset($result['success']) && $result['success'] == "1" ){
                        //not thing to do;
                    }else{
                        $error = isset($result['error-codes']) ? $result['error-codes'] : '';
                        if( is_array( $error ) ){
                            foreach( $error as $value ){
                                $error = $value;
                            }
                        }
                        wc_add_notice( sprintf( __('<strong>ERROR</strong>: Captcha verification failed, please try again!!! <br>Error Codes: %s ', 'toms-recaptcha'), $error), 'error' );
                    }

                }

                if( esc_textarea( get_option('toms_recaptcha_type') ) == 'reCAPTCHA_v2_invisible'){

                    $toms_resp  = isset( $_POST['toms-checkout-form-g-recaptcha'] ) ? sanitize_text_field( $_POST['toms-checkout-form-g-recaptcha'] ) : '';
                    $response   = isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( $_POST['g-recaptcha-response'] ) : '';

                    if( !empty( $response ) ){
                        $response =  $response;
                    }else{
                        $response =  $toms_resp;
                    }
                    
                    $result = $TomSreCAPTCHA->TomSreCAPTCHA_v2_invisible_verification( $response );

                    if( isset($result['success']) && $result['success'] == "1" ){
                        //not thing to do;
                    }else{
                        $error = isset($result['error-codes']) ? $result['error-codes'] : '';
                        if( is_array( $error ) ){
                            foreach( $error as $value ){
                                $error = $value;
                            }
                        }
                        wc_add_notice( sprintf( __('<strong>ERROR</strong>: Captcha verification failed, please try again!!! <br>Error Codes: %s ', 'toms-recaptcha'), $error), 'error' );
                    }
                }
            }
        }
    }
    $TomSreCAPTCHAWoocommerce = new TomSreCAPTCHAWoocommerce();
}