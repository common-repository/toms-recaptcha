<?php
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('TomSreCAPTCHAFrontend') ){

    class TomSreCAPTCHAFrontend {

        /**
         * TomS reCAPTCHA v3 Frontend HTML
         * 
         * @param $form     The Form id or class name
         */
        function TomSreCAPTCHA_V3_HTML($form){
            $reCAPTCHA_form     = $form;
            $v3_site_key        = esc_textarea( get_option('toms_recaptcha_v3_site_key') );
            $verify_api         = esc_textarea( get_option('toms_recaptcha_verify_api', "0") ) == "0" ? 'google.com' : 'recaptcha.net';
            $lang               = !empty( esc_textarea( get_option('toms_recaptcha_language') ) ) ? '&hl=' . esc_textarea( get_option('toms_recaptcha_language') ) : '';

            if( preg_match('/^\./', $reCAPTCHA_form) != 0 ){ //check the name of form id or form class
                $element            = 'getElementsByClassName';
                $number             = '[0]';
                $sign               = '.';
                $id_class           = 'class';   
                $suffix             = '-toms-recaptcha';
                $reCAPTCHA_form_name  = ltrim($reCAPTCHA_form, '.'); //Delete the first Ending mark'.'
            }else{
                $element            = 'getElementById';
                $number             = '';
                $sign               = '#';
                $id_class           = 'id';
                $suffix             = '-toms-recaptcha';
                $reCAPTCHA_form_name  = $reCAPTCHA_form;
            }

            $html = '';
            ob_start(); ?>

                <input type="hidden" <?php echo esc_textarea( $id_class ).'="'.esc_textarea( $reCAPTCHA_form_name.$suffix ).'"'; ?> name="g-recaptcha" />

            <?php $html = ob_get_contents();
                ob_end_clean();
                return $html;
        }

        /**
         * TomS reCAPTCHA v3 Frontend HTML
         * 
         * @param $form     The Form id or class name
         */
        function TomSreCAPTCHA_V3_load_JS($form){
            $reCAPTCHA_form     = $form;
            $v3_site_key        = esc_textarea( get_option('toms_recaptcha_v3_site_key') );
            $verify_api         = esc_textarea( get_option('toms_recaptcha_verify_api', "0") ) == "0" ? 'google.com' : 'recaptcha.net';
            $lang               = !empty( esc_textarea( get_option('toms_recaptcha_language') ) ) ? '&hl=' . esc_textarea( get_option('toms_recaptcha_language') ) : '';

            if( preg_match('/^\./', $reCAPTCHA_form) != 0 ){ //check the name of form id or form class
                $element            = 'getElementsByClassName';
                $number             = '[0]';
                $sign               = '.';
                $id_class           = 'class';   
                $suffix             = '-toms-recaptcha';
                $reCAPTCHA_form_name  = ltrim($reCAPTCHA_form, '.'); //Delete the first Ending mark'.'
            }else{
                $element            = 'getElementById';
                $number             = '';
                $sign               = '#';
                $id_class           = 'id';
                $suffix             = '-toms-recaptcha';
                $reCAPTCHA_form_name  = $reCAPTCHA_form;
            }

            //wp_get_inline_script_tag() wordpress 5.7 only
            $html = [
                        'src'   => 'https://www.' . esc_textarea( $verify_api ) . '/recaptcha/api.js?render=' . esc_textarea( $v3_site_key . $lang )
                    ];
            return $html;
        }

        /**
         * TomS reCAPTCHA v3 Frontend HTML
         * 
         * @param $form     The Form id or class name
         */
        function TomSreCAPTCHA_V3_render_JS($form){
            $reCAPTCHA_form     = $form;
            $v3_site_key        = esc_textarea( get_option('toms_recaptcha_v3_site_key') );
            $verify_api         = esc_textarea( get_option('toms_recaptcha_verify_api', "0") ) == "0" ? 'google.com' : 'recaptcha.net';
            $lang               = !empty( esc_textarea( get_option('toms_recaptcha_language') ) ) ? '&hl=' . esc_textarea( get_option('toms_recaptcha_language') ) : '';

            if( preg_match('/^\./', $reCAPTCHA_form) != 0 ){ //check the name of form id or form class
                $element            = 'getElementsByClassName';
                $number             = '[0]';
                $sign               = '.';
                $id_class           = 'class';   
                $suffix             = '-toms-recaptcha';
                $reCAPTCHA_form_name  = ltrim($reCAPTCHA_form, '.'); //Delete the first Ending mark'.'
            }else{
                $element            = 'getElementById';
                $number             = '';
                $sign               = '#';
                $id_class           = 'id';
                $suffix             = '-toms-recaptcha';
                $reCAPTCHA_form_name  = $reCAPTCHA_form;
            }
            $string = str_replace('-', '', $reCAPTCHA_form_name);

            $html = '';
            ob_start(); ?>

                grecaptcha.ready(function() {
                    grecaptcha.execute(
                        '<?php echo esc_textarea( $v3_site_key ); ?>',
                        {
                            action: 'TomSreCAPTCHAv3'
                        }
                    ).then(function(token) {
                        //console.log(token);
                        var TomSreCAPTCHA_v3_tocken = token;
                        document.<?php echo esc_textarea( $element ); ?>('<?php echo esc_textarea( $reCAPTCHA_form_name.$suffix ); ?>')<?php echo esc_textarea( $number ); ?>.value=TomSreCAPTCHA_v3_tocken;
                    });
                });
                
            <?php $html = ob_get_contents();
                ob_end_clean();
                return $html ;
        }

        /**
         * TomS reCAPTCHA v2 CheckBox Frontend HTML
         * 
         * @param $form     The Form id or class name
         */
        function TomSreCAPTCHA_V2_Checkbox_HTML($form){
            $reCAPTCHA_form         = $form;
            $v2_checkbox_site_key   = esc_textarea( get_option('toms_recaptcha_v2_checkbox_site_key') );
            $verify_api             = esc_textarea( get_option('toms_recaptcha_verify_api', "0") ) == "0" ? 'google.com' : 'recaptcha.net';
            $theme                  = esc_textarea( get_option('toms_recaptcha_v2_theme', "0") ) == "0" ? 'light' : 'dark';
            $lang                   = !empty( esc_textarea( get_option('toms_recaptcha_language') ) ) ? '&hl=' . esc_textarea( get_option('toms_recaptcha_language') ) : '';

            if( preg_match('/^\./', $reCAPTCHA_form) != 0 ){ //check the name of form id or form class
                $sign               = '.';
                $id_class           = 'class';
                $suffix             = '-toms-recaptcha';
                $reCAPTCHA_form_name  = ltrim($reCAPTCHA_form, '.'); //Delete the first Ending mark'.'
            }else{
                $sign               = '#';
                $id_class           = 'id';
                $suffix             = '-toms-recaptcha';
                $reCAPTCHA_form_name  = $reCAPTCHA_form;
            }
            $html = '';
            ob_start(); ?>

                <style>
                    <?php echo '#'.esc_textarea( $reCAPTCHA_form_name.$suffix ); ?> {
                        transform:scale(0.895);
                        transform-origin:0 0;
                        margin: 0 6px 16px 0;
                    }
                </style>
                
                <div id="<?php echo esc_textarea( $reCAPTCHA_form_name.$suffix ); ?>" class="<?php echo esc_textarea( $reCAPTCHA_form_name.$suffix ); ?>"></div>

            <?php $html = ob_get_contents();
                ob_end_clean();
                return $html ;
        }
        /**
         * TomS reCAPTCHA v2 CheckBox Frontend Load JS
         * 
         * @param $form     The Form id or class name
         */
        function TomSreCAPTCHA_V2_Checkbox_load_JS($form){
            $reCAPTCHA_form         = $form;
            $verify_api             = esc_textarea( get_option('toms_recaptcha_verify_api', "0") ) == "0" ? 'google.com' : 'recaptcha.net';
            $lang                   = !empty( esc_textarea( get_option('toms_recaptcha_language') ) ) ? '&hl=' . esc_textarea( get_option('toms_recaptcha_language') ) : '';

            if( preg_match('/^\./', $reCAPTCHA_form) != 0 ){ //check the name of form id or form class
                $sign               = '.';
                $id_class           = 'class';
                $suffix             = '-toms-recaptcha';
                $reCAPTCHA_form_name  = ltrim($reCAPTCHA_form, '.'); //Delete the first Ending mark'.'
            }else{
                $sign               = '#';
                $id_class           = 'id';
                $suffix             = '-toms-recaptcha';
                $reCAPTCHA_form_name  = $reCAPTCHA_form;
            }
            $string = str_replace('-', '', $reCAPTCHA_form_name);
            //wp_get_inline_script_tag() wordpress 5.7 only
            $html = [
                        'src'   => 'https://www.' . esc_textarea( $verify_api ) . '/recaptcha/api.js?onload=TomSreCAPTCHA' . esc_textarea( $string ) . 'Callback&render=explicit' . esc_textarea( $lang ),
                        'async' => true,
                        'defer' => true
                    ];
            return $html ;
        }
        /**
         * TomS reCAPTCHA v2 CheckBox Frontend JS render
         * 
         * @param $form     The Form id or class name
         */
        function TomSreCAPTCHA_V2_Checkbox_JS_render($form){
            $reCAPTCHA_form         = $form;
            $v2_checkbox_site_key   = esc_textarea( get_option('toms_recaptcha_v2_checkbox_site_key') );
            $theme                  = esc_textarea( get_option('toms_recaptcha_v2_theme', "0") ) == "0" ? 'light' : 'dark';

            if( preg_match('/^\./', $reCAPTCHA_form) != 0 ){ //check the name of form id or form class
                $sign               = '.';
                $id_class           = 'class';
                $suffix             = '-toms-recaptcha';
                $reCAPTCHA_form_name  = ltrim($reCAPTCHA_form, '.'); //Delete the first Ending mark'.'
            }else{
                $sign               = '#';
                $id_class           = 'id';
                $suffix             = '-toms-recaptcha';
                $reCAPTCHA_form_name  = $reCAPTCHA_form;
            }
            $string = str_replace('-', '', $reCAPTCHA_form_name);
            $html = '';
            ob_start(); ?>

                var TomSreCAPTCHA<?php echo esc_js( $string );?>Callback = function() {
                    grecaptcha.render('<?php echo esc_textarea( $reCAPTCHA_form_name.$suffix ); ?>', {
                        'sitekey' : '<?php echo esc_textarea( $v2_checkbox_site_key ); ?>',
                        'theme':'<?php echo esc_textarea( $theme ); ?>'
                    });
                };
            
            <?php $html = ob_get_contents();
            ob_end_clean();
            return $html;
        }

        /**
         * TomS reCAPTCHA v2 Invisible Frontend Load JS
         * 
         * @param $form     The Form id or class name
         */
        function TomSreCAPTCHA_V2_Invisible_load_JS($form){
            $reCAPTCHA_form         = $form;
            $v2_invisible_site_key  = esc_textarea( get_option('toms_recaptcha_v2_invisible_site_key') );
            $verify_api             = esc_textarea( get_option('toms_recaptcha_verify_api', "0") ) == "0" ? 'google.com' : 'recaptcha.net';
            $lang                   = !empty( esc_textarea( get_option('toms_recaptcha_language') ) ) ? '?hl=' . esc_textarea( get_option('toms_recaptcha_language') ) : '';
            $badge                  = esc_textarea( get_option('toms_recaptcha_invisible_badge', "0") ) == "0" ? 'bottomright' : 'bottomleft';

            if( preg_match('/^\./', $reCAPTCHA_form) != 0 ){ //check the name of form id or form class
                $element            = 'getElementsByClassName';
                $number             = '[0]';
                $sign               = '.';
                $id_class           = 'class';   
                $suffix             = '-toms-recaptcha';
                $reCAPTCHA_form_name  = ltrim($reCAPTCHA_form, '.'); //Delete the first Ending mark'.'
            }else{
                $element            = 'getElementById';
                $number             = '';
                $sign               = '#';
                $id_class           = 'id';
                $suffix             = '-toms-recaptcha';
                $reCAPTCHA_form_name  = $reCAPTCHA_form;
            }
            $string = str_replace('-', '', $reCAPTCHA_form_name);

            $html = [
                        'src'   => 'https://www.' . esc_textarea( $verify_api ) . '/recaptcha/api.js' . esc_textarea( $lang ),
                        'async' => true,
                        'defer' => true
                    ];
            return $html ;
        }
        /**
         * TomS reCAPTCHA v2 Invisible Frontend Onsubmit JS
         * 
         * @param $form     The Form id or class name
         */
        function TomSreCAPTCHA_V2_Invisible_onsubmit_JS($form){
            $reCAPTCHA_form         = $form;
            $v2_invisible_site_key  = esc_textarea( get_option('toms_recaptcha_v2_invisible_site_key') );
            $verify_api             = esc_textarea( get_option('toms_recaptcha_verify_api', "0") ) == "0" ? 'google.com' : 'recaptcha.net';
            $lang                   = !empty( esc_textarea( get_option('toms_recaptcha_language') ) ) ? '?hl=' . esc_textarea( get_option('toms_recaptcha_language') ) : '';
            $badge                  = esc_textarea( get_option('toms_recaptcha_invisible_badge', "0") ) == "0" ? 'bottomright' : 'bottomleft';

            if( preg_match('/^\./', $reCAPTCHA_form) != 0 ){ //check the name of form id or form class
                $element            = 'getElementsByClassName';
                $number             = '[0]';
                $sign               = '.';
                $id_class           = 'class';   
                $suffix             = '-toms-recaptcha';
                $reCAPTCHA_form_name  = ltrim($reCAPTCHA_form, '.'); //Delete the first Ending mark'.'
            }else{
                $element            = 'getElementById';
                $number             = '';
                $sign               = '#';
                $id_class           = 'id';
                $suffix             = '-toms-recaptcha';
                $reCAPTCHA_form_name  = $reCAPTCHA_form;
            }
            $string = str_replace('-', '', $reCAPTCHA_form_name);

            $html = '';
            ob_start(); ?>

                function Invisible<?php echo esc_textarea( $string ); ?>OnSubmit(token) {
                    document.<?php echo esc_textarea( $element ); ?>("<?php echo esc_textarea( $reCAPTCHA_form_name ); ?>")<?php echo esc_textarea( $number ); ?>.submit();
                }
            
            <?php $html = ob_get_contents();
            ob_end_clean();
            return $html;
        }
        /**
         * TomS reCAPTCHA v2 Invisible Frontend render JS
         * 
         * @param $form     The Form id or class name
         */
        function TomSreCAPTCHA_V2_Invisible_render_JS($form){
            $reCAPTCHA_form         = $form;
            $v2_invisible_site_key  = esc_textarea( get_option('toms_recaptcha_v2_invisible_site_key') );
            $verify_api             = esc_textarea( get_option('toms_recaptcha_verify_api', "0") ) == "0" ? 'google.com' : 'recaptcha.net';
            $lang                   = !empty( esc_textarea( get_option('toms_recaptcha_language') ) ) ? '?hl=' . esc_textarea( get_option('toms_recaptcha_language') ) : '';
            $badge                  = esc_textarea( get_option('toms_recaptcha_invisible_badge', "0") ) == "0" ? 'bottomright' : 'bottomleft';

            if( preg_match('/^\./', $reCAPTCHA_form) != 0 ){ //check the name of form id or form class
                $element            = 'getElementsByClassName';
                $number             = '[0]';
                $sign               = '.';
                $id_class           = 'class';   
                $suffix             = '-toms-recaptcha';
                $reCAPTCHA_form_name  = ltrim($reCAPTCHA_form, '.'); //Delete the first Ending mark'.'
            }else{
                $element            = 'getElementById';
                $number             = '';
                $sign               = '#';
                $id_class           = 'id';
                $suffix             = '-toms-recaptcha';
                $reCAPTCHA_form_name  = $reCAPTCHA_form;
            }
            $string = str_replace('-', '', $reCAPTCHA_form_name);

            $html = '';
            ob_start(); ?>

                var TomSInputs = document.querySelectorAll("<?php echo esc_textarea( $sign.$reCAPTCHA_form_name ); ?> input");
                
                for( var TomS = 0; TomS < TomSInputs.length; TomS++ ) {
                    //Remove items with value submit to resolve conflict
                    if( TomSInputs[TomS].name.toLowerCase() == 'submit' ) {
                        TomSInputs[TomS].removeAttribute('name');
                    }
                    if( TomSInputs[TomS].id.toLowerCase() == 'submit' ) {
                        TomSInputs[TomS].removeAttribute('id');
                    }
                    if( TomSInputs[TomS].classList.contains('submit') ) {
                        TomSInputs[TomS].classList.remove('submit');
                    }
                    //Add reCaptcha Invisible items to comment form submit button
                    if( TomSInputs[TomS].type.toLowerCase() == 'submit' ) {
                        TomSInputs[TomS].classList.add('g-recaptcha');
                        TomSInputs[TomS].setAttribute('data-sitekey', '<?php echo esc_textarea( $v2_invisible_site_key ); ?>');
                        TomSInputs[TomS].setAttribute('data-badge', '<?php echo esc_textarea( $badge ); ?>');
                        TomSInputs[TomS].setAttribute('data-callback', 'Invisible<?php echo esc_textarea( $string ); ?>OnSubmit');
                    }
                }
            
            <?php $html = ob_get_contents();
            ob_end_clean();
            return $html;
        }

        //Woocommerce start
        /** 
         *  Into checkout place_holder form | v2 invisible
         * 
         */
        function TomSreCAPTCHA_woo_v2_invisible_html($div_id, $input_id){
            $html = '<div id="' . esc_textarea( $div_id ) . '" name="' . esc_textarea( $div_id ) . '" /></div>';
            $html .= '<input name="' . esc_textarea( $input_id ) . '" type="hidden" id="' . esc_textarea( $input_id ) . '" class="' . esc_textarea( $input_id ) . '" />';
            return $html;
        }

        // Add Call Back function in header tag
        function TomSreCAPTCHA_woo_v2_invisible_header( $div_id, $input_id, $onload_callback, $render_var, $token_var ){
            $v2_invisible_site_key  = esc_textarea( get_option('toms_recaptcha_v2_invisible_site_key') );
            $badge                  = esc_textarea( get_option('toms_recaptcha_invisible_badge', "0") ) == "0" ? 'bottomright' : 'bottomleft';

            ob_start(); ?>
                function <?php echo esc_textarea( $onload_callback ); ?>() {
                    <?php echo esc_textarea( $render_var ); ?> = grecaptcha.render(
                        "<?php echo esc_textarea( $div_id ); ?>",{
                            "sitekey": "<?php echo esc_textarea( $v2_invisible_site_key ); ?>",
                            "badge": "<?php echo esc_textarea( $badge ); ?>",
                            "size": "invisible"
                        }
                    )
                    grecaptcha.execute(<?php echo esc_textarea( $render_var ); ?>)
                    
                    setTimeout(function(){ 
                        var <?php echo esc_textarea( $token_var ); ?> = grecaptcha.getResponse(<?php echo esc_textarea( $render_var ); ?>)
                        document.getElementById("<?php echo esc_textarea( $input_id ); ?>").setAttribute("value", <?php echo esc_textarea( $token_var ); ?>)
                    }, 1500);

                    setInterval(function(){
                        if( grecaptcha.getResponse(<?php echo esc_textarea( $render_var ); ?>) ){
                            var <?php echo esc_textarea( $token_var ); ?> = grecaptcha.getResponse(<?php echo esc_textarea( $render_var ); ?>)
                            document.getElementById("<?php echo esc_textarea( $input_id ); ?>").setAttribute("value", <?php echo esc_textarea( $token_var ); ?>)
                        }
                    }, 1500);

                }
                <?php $js = ob_get_clean();
                return $js;
        }
        
        //Google API js
        function TomSreCAPTCHA_woo_v2_invisible_api( $onload_callback ){
            //Google reCAPTCHA API
            $verify_api     = esc_textarea( get_option('toms_recaptcha_verify_api', "0") ) == "0" ? 'google.com' : 'recaptcha.net';
            $lang           = !empty( esc_textarea( get_option('toms_recaptcha_language') ) ) ? '&hl=' . esc_textarea( get_option('toms_recaptcha_language') ) : '';
            $api_url = 'https://' . esc_textarea( $verify_api ) . '/recaptcha/api.js?onload=' . esc_textarea( $onload_callback ) . '&render=explicit' . esc_textarea( $lang );
            return $api_url;
        }

        /** 
         *  After checkout form | v2 invisible
         * 
         */
        function TomSreCAPTCHA_woo_v2_invisible_reset( $reset_callback, $render_var ){
            //Click Place holder button js function
            ob_start(); ?>
                function <?php echo esc_textarea( $reset_callback ); ?>(){
                    setTimeout(function(){
                        grecaptcha.reset(<?php echo esc_textarea( $render_var ); ?>)
                        grecaptcha.execute(<?php echo esc_textarea( $render_var ); ?>)
                    }, 3000);
                    grecaptcha.execute(<?php echo esc_textarea( $render_var ); ?>)
                }
            <?php $js = ob_get_clean();
            return $js;
        }

        /**
         *  Add Reset reCaptcha event on place_holder button  | v2 invisible
         *  Php add attributes for html
        */
        function TomSreCAPTCHA_woo_v2_invisible_button_filter( $button_contents, $reset_callback ){
            $charset = mb_internal_encoding();
            $dom        = new DOMDocument();
            @$dom->loadHTML('<?xml encoding="' . esc_textarea( $charset ) . '">' . $button_contents);
            $x = new DOMXPath($dom);
            foreach($x->query("//button") as $node){  
                $node->setAttribute( "onclick", esc_textarea( $reset_callback ) . '()' );
            }
            $newHtml = $dom->saveHtml();
            return $newHtml;
        }
        //Woocommerce end

    }
}