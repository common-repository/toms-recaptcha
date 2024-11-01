<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( in_array('contact-form-block/contact-form-block.php', apply_filters('active_plugins', get_option('active_plugins'))) && !class_exists('TomSreCAPTCHACFB') ){
    class TomSreCAPTCHACFB {

        public function __construct(){
            add_action( 'TomSreCAPTCHAExtraForms', array($this, 'TomSreCAPTCHACFBOptions'), 10, 2 );
            add_action( 'TomSreCAPTCHAExtraFormsData', array($this, 'TomSreCAPTCHACFBOptionsData'), 10, 2);

            if( esc_textarea( get_option('toms_recaptcha_woo_login_form' ) ) == "0" && !empty( esc_textarea($this->TomSreCAPTCHA_cfb_current_type_check()) ) ){
                add_filter( 'mcfb_form_after_message', array($this, 'toms_recaptcha_for_cfb'), 4, 10 );
                add_filter( 'mcfb_validate', array($this, 'toms_recaptcha_for_cfb_check_validate'), 2, 10 );

                //v2 invisible
                add_filter( 'mcfb_form_after_message', array($this, 'toms_recaptcha_for_cfb_after_form'), 4, 10 );
            }
        }

        function TomSreCAPTCHA_cfb_current_type_check(){
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
         *  Add Contact Form Block Support to TomS reCAPTCHA settings page 
        */
        function TomSreCAPTCHACFBOptions(){ ?>
            <!--Contact Form Block-->
            <div class="toms-recaptcha-wordpress-default-forms"><?php _e('Contact Form Block', 'toms-reCAPTCHA'); ?> : </div>
            <div class="toms-recaptcha-form-list">
                <div class="toms-recaptcha-forms-contents">
                    <label class="toms-label">
                        <input type="checkbox" name="toms_recaptcha_cfb" value="0" <?php if( esc_textarea( get_option('toms_recaptcha_cfb') ) == "0" )  echo 'checked="checked"'; ?> />
                        <span class="login-text"><?php _e('Contact Form Block', 'toms-reCAPTCHA'); ?></span>
                    </label>
                </div>
            </div>
        <?php }

        /**
         *  Insert options to database
        */
        function TomSreCAPTCHACFBOptionsData(){
            update_option('toms_recaptcha_cfb', isset($_POST['toms_recaptcha_cfb']) ? sanitize_text_field( $_POST['toms_recaptcha_cfb'] ) : '' );
        }


        // Add the human check field after the e-mail field
        function toms_recaptcha_for_cfb( $html, $atts, $css, $reply ) {
            if ( $reply && $reply->success )
                return $html;

            if( !is_user_logged_in() ){
                $id_class = '.meow-contact-form';

                $current_type = $this->TomSreCAPTCHA_cfb_current_type_check();
                $TomSreCAPTCHAFrontend = new TomSreCAPTCHAFrontend();
                $TomSreCAPTCHA = new TomSreCAPTCHA();
                $allowed_html = $TomSreCAPTCHA->TomSreCAPTCHA_allow_html();
                if( $current_type == 'reCAPTCHA_v3' ){
                    $html .= "<div class='{$css['group']}'>";
                    $html .= $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V3_HTML($id_class);
                    $html .= wp_print_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V3_load_JS($id_class) );
                    $html .= wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V3_render_JS($id_class), [ 'type' => 'text/javascript' ] );
                    $html .= "</div>";
                    return $html;
                }

                if( $current_type == 'reCAPTCHA_v2_checkbox'){
                    $html .= '<style>
                                div#meow-contact-form-toms-recaptcha{
                                    max-width: 248px;
                                }
                                @media (max-width: 330px) {
                                    div#meow-contact-form-toms-recaptcha{
                                        transform:scale(0.85);
                                    }
                                }
                            </style>';
                    $html .= "<div class='{$css['group']}'>";
                    $html .= $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Checkbox_HTML($id_class);
                    $html .= wp_print_inline_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Checkbox_JS_render($id_class), [ 'type' => 'text/javascript' ] );
                    $html .= wp_print_script_tag( $TomSreCAPTCHAFrontend->TomSreCAPTCHA_V2_Checkbox_load_JS($id_class) );
                    $html .= "</div>";
                    return $html;
                }
            }
        }
        /** 
         *  After form | v2 invisible
         * 
         */
        function toms_recaptcha_for_cfb_after_form( $html, $atts, $css, $reply ){
            if ( $reply && $reply->success )
                return $html;

            $current_type = $this->TomSreCAPTCHA_cfb_current_type_check();
            if( !is_user_logged_in() && $current_type == 'reCAPTCHA_v2_invisible' ){
                $div_id     = "toms-cfb";
                $input_id   = "toms-cfb-g-recaptcha";

                $TomSreCAPTCHAFrontend = new TomSreCAPTCHAFrontend();
                $html .= $TomSreCAPTCHAFrontend->TomSreCAPTCHA_woo_v2_invisible_html($div_id, $input_id);
                $TomSreCAPTCHA = new TomSreCAPTCHA();
                $allowed_html = $TomSreCAPTCHA->TomSreCAPTCHA_allow_html();

                // Add Call Back function in header tag
                add_action( 'wp_head', 'cfb_header' );
                function cfb_header(){
                    $div_id             = "toms-cfb";
                    $input_id           = "toms-cfb-g-recaptcha";
                    $onload_callback    = "tomscfbFormOnload";
                    $render_var         = "tomscfbFormRender";
                    $token_var          = "tomscfbFormToken";
                    $TomSreCAPTCHAFrontend = new TomSreCAPTCHAFrontend();
                    $js = $TomSreCAPTCHAFrontend->TomSreCAPTCHA_woo_v2_invisible_header( $div_id, $input_id, $onload_callback, $render_var, $token_var );
                    wp_print_inline_script_tag( $js,[ 'type' => 'text/javascript' ]);
                }

                //api js
                $onload_callback    = "tomscfbFormOnload";
                $render_var         = "tomscfbFormRender";
                $reset_callback     = "tomscfbFormReset";
                $TomSreCAPTCHAFrontend = new TomSreCAPTCHAFrontend();
                $js_url = $TomSreCAPTCHAFrontend->TomSreCAPTCHA_woo_v2_invisible_api( $onload_callback );
                $html .= wp_print_inline_script_tag(null, array(
                    'type'  => 'text/javascript',
                    'async' => true,
                    'src'   => $js_url
                ));

                //add reset function to footer section
                add_action( 'wp_footer', 'cfb_footer' );
                function cfb_footer(){
                    wp_print_inline_script_tag( 
                        '
                        var cfb_button = document.getElementsByClassName("meow-contact-form__group-button")[0]
                        cfb_button.setAttribute("onclick","tomscfbFormReset()")
                        ',[ 'type' => 'text/javascript' ]);

                    $TomSreCAPTCHAFrontend = new TomSreCAPTCHAFrontend();
                    $js_reset = $TomSreCAPTCHAFrontend->TomSreCAPTCHA_woo_v2_invisible_reset( 'tomscfbFormReset', $render_var );
                    wp_print_inline_script_tag( $js_reset,[ 'type' => 'text/javascript' ]);
                }
            }
            return $html;
        }

        // Check if the field was properly filled in
        function toms_recaptcha_for_cfb_check_validate( $error, $form ) {

            if( !is_user_logged_in() ){

                $TomSreCAPTCHA = new TomSreCAPTCHA();
                if( esc_textarea( get_option('toms_recaptcha_type', 'reCAPTCHA_v3') ) == 'reCAPTCHA_v3'){

                    $response  = isset( $_POST['g-recaptcha'] ) && !empty( $_POST['g-recaptcha'] ) ? sanitize_text_field( $_POST['g-recaptcha'] ) : '';
                    
                    $result = $TomSreCAPTCHA->TomSreCAPTCHA_v3_verification( $response );

                    if( isset($result['success']) && $result['success'] == "1" &&
                        isset($result['action']) && $result['action'] == "TomSreCAPTCHAv3" &&
                        isset($result['score']) && $result['score'] >= 0.5
                    ){
                        return null;
                    }else{
                        $error = isset($result['error-codes']) ? $result['error-codes'] : '';
                        if( is_array( $error ) ){
                            foreach( $error as $value ){
                                $error = $value;
                            }
                        }
                        return sprintf( __('<strong>ERROR</strong>: Captcha verification failed, please try again!!! <br>Error Codes: %s ', 'toms-recaptcha'), $error);
                    }
                }

                if( esc_textarea( get_option('toms_recaptcha_type') ) == 'reCAPTCHA_v2_checkbox' ){
                    
                    $response  = isset( $_POST['g-recaptcha-response'] ) && !empty( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( $_POST['g-recaptcha-response'] ) : '';
                    
                    $result = $TomSreCAPTCHA->TomSreCAPTCHA_v2_checkbox_verification( $response );

                    if( isset($result['success']) && $result['success'] == "1" ){
                        return null;
                    }else{
                        $error = isset($result['error-codes']) ? $result['error-codes'] : '';
                        if( is_array( $error ) ){
                            foreach( $error as $value ){
                                $error = $value;
                            }
                        }
                        return sprintf( __('<strong>ERROR</strong>: Captcha verification failed, please try again!!! <br>Error Codes: %s ', 'toms-recaptcha'), $error);
                    }

                }

                if( esc_textarea( get_option('toms_recaptcha_type') ) == 'reCAPTCHA_v2_invisible'){

                    $toms_resp  = isset( $_POST['toms-cfb-g-recaptcha'] ) ? sanitize_text_field( $_POST['toms-cfb-g-recaptcha'] ) : '';
                    
                    $response  = isset( $_POST['g-recaptcha-response'] ) && !empty( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( $_POST['g-recaptcha-response'] ) : '';

                    if( !empty( $response ) ){
                        $response =  $response;
                    }else{
                        $response =  $toms_resp;
                    }
                    
                    $result = $TomSreCAPTCHA->TomSreCAPTCHA_v2_invisible_verification( $response );

                    if( isset($result['success']) && $result['success'] == "1" ){
                        return null;
                    }else{
                        $error = isset($result['error-codes']) ? $result['error-codes'] : '';
                        if( is_array( $error ) ){
                            foreach( $error as $value ){
                                $error = $value;
                            }
                        }
                        return sprintf( __('<strong>ERROR</strong>: Captcha verification failed, please try again!!! <br>Error Codes: %s ', 'toms-recaptcha'), $error);
                    }
                }
            }
            return null;
        }
    }
    $TomSreCAPTCHACFB = new TomSreCAPTCHACFB();
}