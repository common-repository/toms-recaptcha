<?php
/**
 * Text Domain:       toms-caprice
 * Domain Path:		  /languages
 * 
*/

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('TomSCaprice') ){

    class TomSCaprice {

        public function __construct() {
            add_action( 'init', array($this, 'TomSCapriceInit') );
            add_action( 'admin_menu', array($this, 'TomSCapriceAdminMenu') );
        }

        public function TomSCapriceInit(){
            //Get current language code
            $lang_code = get_locale();

            /**
             *  TomSLanguageCode filter
             *  usage:
             *       add_filter( 'TomSLanguageCode', 'your_language_function' );
             *       function your_language_function( $lang ){
             *           $myarray = array(
             *               //example: English(UK)
             *               'en_GB' => 'file-path-of-your-language.mo'
             *           );
             *          $newarray = array_merge( $lang, $myarray );
             *          
             *          return $newarray;
             *        }
            */
            $toms_lang_code = array( 'zh_CN' => plugin_dir_path( __FILE__ ) . 'languages/toms-caprice-zh_CN.mo' );
            $toms_lang_code = apply_filters( 'TomSLanguageCode', (array)$toms_lang_code );

            foreach( $toms_lang_code as $language_code => $lang_mo_path ){
                switch ( $lang_code ) {
                    case $language_code:
                        load_textdomain( 'toms-caprice', $lang_mo_path );
                        break;
                }
            }
        }
        
        public function TomSCapriceAdminMenu() {
            if( 'toplevel_page_toms-wp' != get_plugin_page_hook('toms-wp', '') ){

                //$TomSIcon =  plugin_dir_url( __FILE__ ) . 'assets/img/toms-50.png';
                $TomSIcon =  'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjxzdmcKICAgeG1sbnM6ZGM9Imh0dHA6Ly9wdXJsLm9yZy9kYy9lbGVtZW50cy8xLjEvIgogICB4bWxuczpjYz0iaHR0cDovL2NyZWF0aXZlY29tbW9ucy5vcmcvbnMjIgogICB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiCiAgIHhtbG5zOnN2Zz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciCiAgIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIKICAgeG1sbnM6c29kaXBvZGk9Imh0dHA6Ly9zb2RpcG9kaS5zb3VyY2Vmb3JnZS5uZXQvRFREL3NvZGlwb2RpLTAuZHRkIgogICB4bWxuczppbmtzY2FwZT0iaHR0cDovL3d3dy5pbmtzY2FwZS5vcmcvbmFtZXNwYWNlcy9pbmtzY2FwZSIKICAgdD0iMTY2MDU4MTE1NzQ2NyIKICAgY2xhc3M9Imljb24iCiAgIHZpZXdCb3g9IjAgMCAyMjM3IDEwMjQiCiAgIHZlcnNpb249IjEuMSIKICAgcC1pZD0iMTUzMiIKICAgd2lkdGg9IjYxMS42Nzk2ODc1IgogICBoZWlnaHQ9IjI4MCIKICAgaWQ9InN2ZzIwIgogICBzb2RpcG9kaTpkb2NuYW1lPSJUb21TICgyKS5zdmciCiAgIGlua3NjYXBlOnZlcnNpb249IjAuOTIuNSAoMjA2MGVjMWY5ZiwgMjAyMC0wNC0wOCkiPgogIDxtZXRhZGF0YQogICAgIGlkPSJtZXRhZGF0YTI0Ij4KICAgIDxyZGY6UkRGPgogICAgICA8Y2M6V29yawogICAgICAgICByZGY6YWJvdXQ9IiI+CiAgICAgICAgPGRjOmZvcm1hdD5pbWFnZS9zdmcreG1sPC9kYzpmb3JtYXQ+CiAgICAgICAgPGRjOnR5cGUKICAgICAgICAgICByZGY6cmVzb3VyY2U9Imh0dHA6Ly9wdXJsLm9yZy9kYy9kY21pdHlwZS9TdGlsbEltYWdlIiAvPgogICAgICA8L2NjOldvcms+CiAgICA8L3JkZjpSREY+CiAgPC9tZXRhZGF0YT4KICA8c29kaXBvZGk6bmFtZWR2aWV3CiAgICAgcGFnZWNvbG9yPSIjZmZmZmZmIgogICAgIGJvcmRlcmNvbG9yPSIjNjY2NjY2IgogICAgIGJvcmRlcm9wYWNpdHk9IjEiCiAgICAgb2JqZWN0dG9sZXJhbmNlPSIxMCIKICAgICBncmlkdG9sZXJhbmNlPSIxMCIKICAgICBndWlkZXRvbGVyYW5jZT0iMTAiCiAgICAgaW5rc2NhcGU6cGFnZW9wYWNpdHk9IjAiCiAgICAgaW5rc2NhcGU6cGFnZXNoYWRvdz0iMiIKICAgICBpbmtzY2FwZTp3aW5kb3ctd2lkdGg9IjI1NjAiCiAgICAgaW5rc2NhcGU6d2luZG93LWhlaWdodD0iMTM0OSIKICAgICBpZD0ibmFtZWR2aWV3MjIiCiAgICAgc2hvd2dyaWQ9ImZhbHNlIgogICAgIGlua3NjYXBlOnpvb209IjAuNjczNTU1MTQiCiAgICAgaW5rc2NhcGU6Y3g9Ii0xNDYuOTgxMjgiCiAgICAgaW5rc2NhcGU6Y3k9IjE0MCIKICAgICBpbmtzY2FwZTp3aW5kb3cteD0iMCIKICAgICBpbmtzY2FwZTp3aW5kb3cteT0iMCIKICAgICBpbmtzY2FwZTp3aW5kb3ctbWF4aW1pemVkPSIxIgogICAgIGlua3NjYXBlOmN1cnJlbnQtbGF5ZXI9InN2ZzIwIiAvPgogIDxkZWZzCiAgICAgaWQ9ImRlZnM0Ij4KICAgIDxzdHlsZQogICAgICAgdHlwZT0idGV4dC9jc3MiCiAgICAgICBpZD0ic3R5bGUyIj5AZm9udC1mYWNlIHsgZm9udC1mYW1pbHk6IGZlZWRiYWNrLWljb25mb250OyBzcmM6IHVybCgmcXVvdDsvL2F0LmFsaWNkbi5jb20vdC9mb250XzEwMzExNThfdTY5dzh5aHhkdS53b2ZmMj90PTE2MzAwMzM3NTk5NDQmcXVvdDspIGZvcm1hdCgmcXVvdDt3b2ZmMiZxdW90OyksIHVybCgmcXVvdDsvL2F0LmFsaWNkbi5jb20vdC9mb250XzEwMzExNThfdTY5dzh5aHhkdS53b2ZmP3Q9MTYzMDAzMzc1OTk0NCZxdW90OykgZm9ybWF0KCZxdW90O3dvZmYmcXVvdDspLCB1cmwoJnF1b3Q7Ly9hdC5hbGljZG4uY29tL3QvZm9udF8xMDMxMTU4X3U2OXc4eWh4ZHUudHRmP3Q9MTYzMDAzMzc1OTk0NCZxdW90OykgZm9ybWF0KCZxdW90O3RydWV0eXBlJnF1b3Q7KTsgfQo8L3N0eWxlPgogIDwvZGVmcz4KICA8cGF0aAogICAgIHN0eWxlPSJmaWxsOiMyYzJjMmM7c3Ryb2tlLXdpZHRoOjEiCiAgICAgZD0ibSA5NC4zMDc2ODEsNDEuOTM0NjY0IGMgLTI3LjEzNDMzMiwxZS02IC00MC43LDEzLjU2NTY2NyAtNDAuNyw0MC42OTk5OTkgdiA2OS4zNTcxNDcgYyAwLDI3LjEzNDMzIDEzLjU2NTY2OCw0MC43IDQwLjcsNDAuNyBIIDg2NS40NzE5NiBjIDI3LjEzNDM2LDAgNDAuNzA3MTUsLTEzLjU2NTY3IDQwLjcwNzE1LC00MC43IFYgODIuNjM0NjYzIGMgMCwtMjcuMTM0MzMyIC0xMy41NzI3OSwtNDAuNjk5OTk5IC00MC43MDcxNSwtNDAuNjk5OTk5IHogTSAyMDAyLjM0MzQsOTYuMTM0NjY2IGMgLTQ5Ljc0NywwLjMyODA0MiAtMTAxLjk3MywyMS42OTg2NjQgLTE1My4zNDI4LDUwLjg1MDAwNCAtNjUuNzU0LDM3LjMxNDE0IC0xMzAuMTAyNiw4Ny4zNjc3NyAtMTYyLjI3ODYsMTM3LjkwNzE0IC0zMi4xNzY0LDUwLjUzOTgxIC0zMi4xNzkzLDEwMS41NTU4NyAtMTMuMDI4NiwxNTQuOTI4NTYgMTkuMTUwOCw1My4zNzI3MSA1Ny40NDc4LDEwOS4wOTk1MiAxMDIuMTkyOSwxMzMuOTcxNDYgNDQuNzQ1NCwyNC44NzE5IDk1Ljk0MTEsMTguODkzOSAxMjYuNzcxNCwzLjI5Mjg2IDMwLjgyOTksLTE1LjYwMTA4IDQxLjI5NzUsLTQwLjgyNjM0IDIyLjU1NzEsLTY4LjI0Mjg4IC0xOC43NDAzLC0yNy40MTU2NiAtNjYuNjk3NSwtNTcuMDIxNDcgLTg5Ljk0MjgsLTg5LjU1NzE0IC0yMy4yNDQ1LC0zMi41MzU2NiAtMjEuNzY2NywtNjguMTgyNyA3LjM3ODYsLTEwNC42NzE0MyAyOS4xNDUyLC0zNi40ODg3MyA4NS45NiwtNzMuOTA0ODcgMTQxLjQsLTgwLjE3MTQzIDU1LjQzOTksLTYuMjY2NTYgMTEwLjgzNTgsMTkuMjMxIDE0OS40Mjg1LDIzLjcxNDI5IDM4LjU5MjgsNC40ODM3MiA2MS4wNTA5LC0xMS43MzU3MiA0NC4yOTI5LC00OC4xMjE0MyAtMTYuNzU4LC0zNi4zODUyOSAtNzIuNzMzMywtOTIuOTM5OTIgLTEzNC4zLC0xMDguOTM1NzIgLTEzLjQ2NzgsLTMuNDk5MDc5IC0yNy4xOTk0LC01LjA1NjEzNyAtNDEuMTI4NiwtNC45NjQyODQgeiBNIDI0NS4xMDA1NCwyMTQuMjcwMzggQSA1NjEuOTUzOCw1NjEuOTUzOCAwIDAgMCAxMTUuODI5MTEsNTY0LjkyMDM3IGggLTAuMDg1NyB2IDM0My43Nzg2IGMgMCwwLjg1MTM4IDAuNjkzODYsMS41NDI4NCAxLjU3ODU3LDEuNTQyODQgaCAyNjQuNzU3MTQgYSAxLjU3NzI0NSwxLjU3NzI0NSAwIDAgMCAxLjU3ODU4LC0xLjU3MTQgdiAtNDMuMzAwMDIgYSAzNzcuODUyNDIsMzc3Ljg1MjQyIDAgMCAxIC0xMzYuOTE0MywtMjkwLjQyODU3IDM3Ny44NTI0MiwzNzcuODUyNDIgMCAwIDEgMy4zOTI4NSwtNDcuNTQyODYgMzc3Ljg1MjQyLDM3Ny44NTI0MiAwIDAgMSAyLjEwNzE1LC0xMi42Nzg1OCAzNzcuODUyNDIsMzc3Ljg1MjQyIDAgMCAxIDcuNTUsLTM1LjY2NDI4IDM3Ny44NTI0MiwzNzcuODUyNDIgMCAwIDEgMy4xNzE0MywtMTEuNDY0MyAzNzcuODUyNDIsMzc3Ljg1MjQyIDAgMCAxIDE1LjIyODU3LC00Mi4zMDcxNCAzNzcuODUyNDIsMzc3Ljg1MjQyIDAgMCAxIDAuNjg1NzEsLTEuNzQyODUgMzc3Ljg1MjQyLDM3Ny44NTI0MiAwIDAgMSAyMy44NTcxNSwtNDUuNTE0MjggdiAtMC4xIEEgMzc3Ljg1MjQyLDM3Ny44NTI0MiAwIDAgMSA1MTIuNzY0ODEsMjE0LjI3MDM4IFogbSA5MjUuNzcxNDYsNzAuMTQyODYgLTgwLjM4NTcsMTIwLjM1MDAyIGEgMzcyLjU1MzIyLDM3Mi41NTMyMiAwIDAgMSA0Mi4xMTQyLDE3MS4xOTI4MyAzNzIuNTUzMjIsMzcyLjU1MzIyIDAgMCAxIC04MS4wNSwyMzEuOTI4NTggdiA4NS4wMTQzMSBhIDEuNTY2ODU3LDEuNTY2ODU3IDAgMCAwIDEuNTc4NiwxLjU4NTcgaCAxMS43IGEgMzY4LjY0ODY0LDI5My4xMjA5MiAwIDAgMCAxNjkuMDcxNCwtMTEyLjU2NDMgViA0MzcuNDk4OTcgbCA2NS40MDcyLC0xNTMuMDg1NzMgeiBtIDIxMC40NzE0LDAgLTY1LjQwNzEsMTUzLjA4NTczIGggNTkuNDE0MyB2IDAuMTIxNDEgaCAxNDEuODIxNCB2IC0wLjE1NzE0IGggODQuNTU3MSBjIC0xOC4zMDQ1LC01Mi41MTM1MiAtMTcuOTI4LC0xMDIuNzczNzEgMTMuNzcxNSwtMTUyLjU2NDI5IDAuMTAyNSwtMC4xNjE0NCAwLjIzODksLTAuMzIzODMgMC4zNDI4LC0wLjQ4NTcxIHogbSAtMzk0LjY0Mjg2LDQ2LjM1IC0yNTcuMzcxNDMsNjQuMjc4NTYgLTI1Ny40MTQyOSw2NC4yNzg1NyAwLjgxNDMsMC45NTAwMiAtMjcuOTIxNDUsNi44NTAwMSBhIDY3LjI2NjY1Niw2Ny4yNjY2NTYgMCAwIDAgLTM4Ljc0Mjg1LDEwNi45MzU3IGwgMjU0Ljk1LDI5NS43Nzg1NiBhIDY3LjI2NjY1Niw2Ny4yNjY2NTYgMCAwIDAgOTEuNDcxNDMsNS41MjE0NCA2Ny4yNjY2NTYsNjcuMjY2NjU2IDAgMCAwIDIwLjA4NTczLC0yNy45ODU3IGwgMTAuODcxNDQsLTI2LjYzNTc0IDAuMjQyODMsMC4yODU3NCAxMDEuNTA3MTUsLTI0NS4xIHogbSAtMTI4LjY3ODU4LDExMS4zNTcxNSAtNDMuNzk5OTksMTA1LjgxNDI3IC00My43NzE0MywxMDUuNzM1NzIgLTAuMTQ5OTgsLTAuMTc4NTggLTQuNjg1NzEsMTEuNTI4NTkgYSAyOS4wMjE0ODgsMjkuMDIxNDg4IDAgMCAxIC04LjY0Mjg5LDEyLjA4NTczIGwgLTAuMDIxNCwtMC4wMjg2IGEgMjkuMDIxNDg4LDI5LjAyMTQ4OCAwIDAgMSAtMzkuNDc4NiwtMi4zOTI4MyBMIDYwNy41MDA1NCw1NDcuMTA2MSBhIDI5LjAyMTQ4OCwyOS4wMjE0ODggMCAwIDEgMTYuNjY0MjgsLTQ2LjE5Mjg2IGwgMTIuMDg1NzMsLTIuOTM1NyAtMC4zMjE0MywtMC4zNzE0NiAxMTEuMDM1NzIsLTI3LjY5OTk2IHogTSAyMDE5Ljg2NDgsNDg1Ljc1NjEgYyAtMTguMTM3LC0wLjM5MDQgLTI4LjU5MywxNy41ODA3MyAtMjkuODIxNCw0NS40Nzg1OCAtMS43MDg0LDM4LjgxNDc2IDE0LjY1MDMsOTcuNTYxNTMgLTAuMzUsMTUxLjI5OTk5IC05LjM5OTQsMzMuNjc0NDYgLTMwLjk1NTUsNjUuOTM1OTEgLTU1Ljc1LDkwLjk4NTczIGwgMTM5LjksMzAuOTQyODMgYyAzNy4wNzk2LC01MS40MDQxNCA2Mi4xMTAxLC0xMDUuMDUwNTkgNTYuOTU3MiwtMTU5LjM5Mjg0IC02LjAwNTIsLTYzLjMyNzAyIC01Mi45NDc4LC0xMjcuNTg0NDEgLTg2LjIwNzIsLTE0OS45MTQzIC05LjM1NCwtNi4yNzk1MyAtMTcuNjMxNCwtOS4yNDY3NiAtMjQuNzI4NiwtOS4zOTk5OSB6IG0gLTU3My4zMjE0LDYuODc4NTcgYyAtNDcuNDY0OCwwIC03MS4yLDIzLjczNTE5IC03MS4yLDcxLjIgdiAxMDYuNjY0MzEgYyAwLDQ3LjQ2NDc3IDIzLjczNTIsNzEuMiA3MS4yLDcxLjE5OTk2IDQ3LjQ2NDgsMCA3MS4yLC0yMy43MzUxOSA3MS4yLC03MS4xOTk5NiBWIDU2My44MzQ2NyBjIDAsLTQ3LjQ2NDgxIC0yMy43MzUyLC03MS4yIC03MS4yLC03MS4yIHogbSAyMzAuNTg1NywxOTAuNDkyODYgYyAtMTMuODI5LDAuNjkzMzkgLTI3LjIzNTksOS41NzM0MSAtMzguNjU3MSwyNS4zMDAwMSAtMjAuMzAzOCwyNy45NTgwMSAtMzQuMzQyOCw3Ny41NDg3NiAtMTYuOSwxMjUuNjc4NTUgMTcuNDQyMyw0OC4xMzA2NyA2Ni4zNzg3LDk0LjgwMTAxIDExNi4wMjg2LDEyMi4xOTI4NiA0OS42NDk0LDI3LjM5MTQ1IDEwMC4wMDk1LDM1LjQ5NjU2IDE1NS4wMjE0LDExLjc2NDMgNDMuMjk3MiwtMTguNjc4OTMgODkuNDQzMSwtNTcuMTA1MTIgMTMwLjIsLTEwMS4zNzE0MyBsIC0xOTcuNDUsLTQzLjY2NDI3IGMgLTE1LjIwOTgsLTIuNzc3OTMgLTI5LjIwNDYsLTEwLjUxODYxIC00MS44MTQzLC0yMi45OTI4NiAtMjguNDI2OCwtMjguMTIxNjQgLTUwLjAzMzQsLTgwLjE3NTYyIC03NC4xMjE0LC0xMDMuMDM1NzMgLTEwLjUzODcsLTEwLjAwMTQ4IC0yMS41NTEzLC0xNC40MTExOSAtMzIuMzA3MiwtMTMuODcxNDMgeiIKICAgICBpZD0icGF0aDYiCiAgICAgaW5rc2NhcGU6Y29ubmVjdG9yLWN1cnZhdHVyZT0iMCIgLz4KPC9zdmc+';
                //Add menu to admin
                $TomSWP = add_menu_page( 
                            __('TomS Plugins For Wordpress', 'toms-caprice'),
                            '<span class="toms-brand-logo">&nbsp;&nbsp;<span>' . __('TomS WP', 'toms-caprice') . '</span>',
                            'manage_options',
                            'toms-wp',
                            array($this, 'TomSWordpress'),
                            $TomSIcon,
                            null );
                
                add_submenu_page(
                    'toms-wp',
                    __('Dashboard', 'toms-caprice'),
                    __('Dashboard', 'toms-caprice'),
                    'manage_options',
                    'toms-wp',
                    array($this, 'TomSWordpress'),
                    null );
                    
                //Add Admin global style
                add_action( "admin_enqueue_scripts", array($this, 'TomSWP_global_load_style') );
                //Add TomS WP style
                add_action( "load-{$TomSWP}", array($this, 'TomSWP_load_style') );
                //Add TomS admin footer
                add_action( 'admin_footer_text', array($this, 'TomSWP_admin_footer_text') );
            }
        }

        //TomS Admin global style
        public function TomSWP_global_load_style() {
            //wp_enqueue_style( 'TomSWPGlobalStyle', plugin_dir_url( __FILE__ ) . 'assets/css/admin.css' );
            ?>
            <style>
                .folded #adminmenu #toplevel_page_toms-wp div.wp-menu-image.svg{
                    background-size: 28px auto !important;
                    margin-left: 0 !important;
                }
                #adminmenu #toplevel_page_toms-wp div.wp-menu-image.svg{
                    background-size: 36px auto;
                    margin-left: 12px;
                }
                @media screen and (min-width: 783px){
                    .auto-fold  #adminmenu #toplevel_page_toms-wp div.wp-menu-image.svg{
                        background-size: 28px auto;
                        margin-left: 0;
                    }
                }
                @media screen and (max-width: 782px){
                    #adminmenu #toplevel_page_toms-wp div.wp-menu-image.svg{
                        width: 40px;
                        margin-left: 5px;
                        padding: 3px 0;
                    }
                    .toms-brand-logo{
                        padding-left: 5px;
                    }
                }
            </style>
            <?php
        }
        
        //TomS WP style
        public function TomSWP_load_style() {
            wp_enqueue_style( 'TomSWPStyle', plugin_dir_url( __FILE__ ) . 'assets/css/tomswp.css' );
        }
        //TomS admin footer
        public function TomSWP_admin_footer_text($text) {
            if ( is_admin() && isset($_GET['page']) && strpos($_GET['page'], 'toms-') !== false){
                return sprintf( __( '<i>Thank you for using <a href="%s" target="_blank"><strong>TomS WordPress Plugin</strong></a></i>', 'toms-caprice' ) , 'https://toms-caprice.org' );
            }
            return $text;
        }

        //TomS WP Page contents
        public function TomSWordpress() {
            $TOMSLOGO = plugin_dir_url( __FILE__ ) . 'assets/img/TomS-68.png';
            $TOMSURL = 'https://toms-caprice.org/';
            $TOMSWPURL = 'https://wordpress.org/plugins/';
            
            $TOMS_OBJECT    = file_get_contents(plugin_dir_url( __FILE__ ) . 'toms-caprice.json');
            $TOMS_JSON      = json_decode($TOMS_OBJECT, true);

            //将Json数据存为新数组 $toms_json_key[]
            $toms_json_key = (array)[];
            foreach( $TOMS_JSON as $key => $name ){
                $toms_json_key[] = $key;
            }
        
            ?>
            
            <div id="toms-wp-dashboard" class="toms-wp-dashboard">
                <div class="toms-header">
                    <span class="toms-logo" ><img src="<?php echo esc_attr( $TOMSLOGO ); ?>" /></span>
                    <h1 class="toms-header-text"><?php _e('TomS DashBoard', 'toms-caprice'); ?></h1>
                </div>

                <?php if( !wp_is_mobile() ) : ?>
                <h2><?php _e('Welcome to TomS Caprice !');?></h2>
                <p class="description"><?php _e('Thank you for choosing TomS Caprice plugins. We provide easy, useful and secure plugins for you.', 'toms-caprice'); ?></p>
                <?php endif;
                    //get all plugins
                    $all_plugins = get_plugins(''); ?>

                    <div class="current-installed">
                    <h3><?php _e('Current Installed', 'toms-caprice'); ?></h3>
                    <div class="toms-current-activated">
                <?php

                    //$installed = (array)[[]];

                    foreach ( $all_plugins as $key => $value ) {
                        //Get plugin slug
                        $slug = dirname($key);

                        //Installed plugins
                        if( isset( $value['TextDomain'] ) && in_array( $value['TextDomain'], $toms_json_key ) ){ 
                            $slug           = esc_attr( $value['TextDomain'] );
                            $name           = isset( $value['Name'] ) ? esc_textarea( $value['Name'] ) : '';
                            $description    = isset( $value['Description'] ) ? esc_textarea( $value['Description'] ) : '';
                            $version        = isset( $value['Version'] ) ? esc_textarea( $value['Version'] ) : '';
                            $type           = isset( $TOMS_JSON[$slug]['Type'] ) ? esc_textarea( $TOMS_JSON[$slug]['Type'] ) : '';
                            $status         = isset( $TOMS_JSON[$slug]['Status'] ) ? esc_textarea( $TOMS_JSON[$slug]['Status'] ) : '';

                            if( $status == 'published' ){
                                $TOMS_URL = $TOMSWPURL;
                            }else{
                                $TOMS_URL = $TOMSURL;
                            }
                            
                            //Save installed TomS plugins in array 保存已安装的插件名称和版本号到二维数组
                            $installed[]        = $slug;
                            $installed[$slug][] = $version;

                            ?>
                                <div class="toms-items toms-wp-dashboard-<?php echo esc_attr( $slug ); ?>">
                                    <div class="toms-item-contents">
                                        <div class="<?php echo esc_attr( $slug ); ?> toms-plugins-logo">
                                            <?php echo '<img src="' . plugin_dir_url( __FILE__ ) . 'assets/img/' . esc_attr( $slug ) . '.png" />' ?>
                                        </div>
                                        <div class="<?php echo esc_attr( $slug ); ?>-details toms-plugins-details" >
                                            <div class="<?php echo  esc_attr( $slug ); ?>-text toms-plugins-text">
                                                <?php echo esc_html( $name ); ?>
                                                <?php echo $type == 'block' ? ' <span style="color: #ff0000;">[<span style="color: #006600;">Block</span>]</span>' : ''; ?>
                                                <?php echo $type == 'add-on' ? ' <span style="color: #ff0000;">[<span style="color: #9c27b0;">Add-on</span>]</span>' : ''; ?>
                                            </div>
                                            <div class="<?php echo esc_attr( $slug ); ?>-description toms-plugins-description"><?php esc_html_e( $description ); ?></div>
                                        </div>
                                    </div>
                                    <div class="toms-item-button">
                                        <div class="<?php echo esc_attr( $slug ); ?>-version toms-plugins-version"><?php echo __('Version:', 'toms-caprice') .' '. esc_html( $version ); ?></div>
                                        
                                        <?php if( is_plugin_active( $key ) == true ) { 
                                                 if( $type != 'block' && $type != 'add-on'){ ?>
                                                 <div>
                                                    <a class="<?php echo  esc_attr( $slug ); ?>-config toms-plugins-config button activate-now button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page='.$slug.'-settings' ) ); ?>" >
                                                        <?php _e('Settings', 'toms-caprice'); ?>
                                                    </a>
                                                    <a href="<?php echo esc_attr( $TOMS_URL.$slug )?>" class="update-now button toms-active button-hidden" target="_blank">
                                                        <?php _e('View Details', 'toms-caprice'); ?>
                                                    </a>
                                                 </div>
                                            <?php }else{ ?>
                                                    <a href="<?php echo esc_attr( $TOMS_URL.$slug )?>" class="update-now button" target="_blank">
                                                        <?php _e('View Details', 'toms-caprice'); ?>
                                                    </a>
                                            <?php } ?>
                                        <?php }else{ ?>
                                             <div>
                                                <a class="<?php echo  esc_attr( $slug ); ?>-update toms-plugins-update button activate-now button-primary" href="<?php echo wp_nonce_url( add_query_arg( array( 'action' => 'activate', 'plugin' => $key ), admin_url( 'plugins.php' ) ), 'activate-plugin_'.$key ); ?>" >
                                                    <?php _e('Activate', 'toms-caprice'); ?>
                                                </a>
                                                <a href="<?php echo esc_attr( $TOMS_URL.$slug )?>" class="update-now button toms-active button-hidden" target="_blank">
                                                    <?php _e('View Details', 'toms-caprice'); ?>
                                                </a>
                                             </div>
                                        <?php } ?>
                                    </div>
                                </div>

                        <?php }
                    } ?>
                    </div>
                </div>
                <div class="our-more-plugins">
                <h3><?php _e('Our Popular Plugins', 'toms-caprice'); ?></h3>
                <div class="toms-more-plugins">
                <?php
                    foreach ( $TOMS_JSON as $key => $value ) {
                        //plugin name
                        $slug           = $key;
                        $name           = isset( $value['Name'] ) ? esc_textarea( $value['Name'] ) : '';
                        $authorname     = isset( $value['AuthorName'] ) ? esc_textarea( $value['AuthorName'] ) : '';
                        $description    = isset( $value['Description'] ) ? esc_textarea( $value['Description'] ) : '';
                        $version        = isset( $value['Version'] ) ? esc_textarea( $value['Version'] ) : '';
                        $type           = isset( $value['Type'] ) ? esc_textarea( $value['Type'] ) : '';
                        $status         = isset( $value['Status'] ) ? esc_textarea( $value['Status'] ) : '';

                        if( $status == 'published' ){
                            $TOMS_URL = $TOMSWPURL;
                        }else{
                            $TOMS_URL = $TOMSURL;
                        }
                        //Our more plugins
                        if( $authorname == 'Tom Sneddon' ){
                            ?>
                                <div class="toms-items toms-wp-dashboard-<?php echo esc_attr( $slug ); ?>">
                                    <div class="toms-item-contents">
                                        <div class="<?php echo esc_attr( $slug ); ?> toms-plugins-logo">
                                            <?php echo '<img src="' . plugin_dir_url( __FILE__ ) . 'assets/img/' . esc_attr( $slug ) . '.png" />' ?>
                                        </div>
                                        <div class="<?php echo esc_attr( $slug ); ?>-details toms-plugins-details" >
                                            <div class="<?php echo  esc_attr( $slug ); ?>-text toms-plugins-text">
                                                <?php echo esc_html( $name ); ?>
                                                <?php echo $type == 'block' ? ' <span style="color: #ff0000;">[<span style="color: #006600;">Block</span>]</span>' : ''; ?>
                                                <?php echo $type == 'add-on' ? ' <span style="color: #ff0000;">[<span style="color: #9c27b0;">Add-on</span>]</span>' : ''; ?>
                                            </div>
                                            <div class="<?php echo esc_attr( $slug ); ?>-description toms-plugins-description"><?php esc_html_e( $description ); ?></div>
                                        </div>
                                    </div>
                                    <div class="toms-item-button <?php echo empty( $version ) ? 'toms-version-empty' : ''; ?>">
                                        <?php if( in_array( $slug, $installed ) ){ ?>
                                            <div class="<?php echo esc_attr( $slug ); ?>-version toms-plugins-version"><?php echo __('Version:', 'toms-caprice') . ' '. esc_html( $installed[$slug][0] ); ?></div>
                                            <?php if( $TOMS_JSON[$slug]['Status'] == 'extented' ){ ?>
                                                <a class="update-now button" target="_blank" disabled>
                                                    <?php _e('Installed', 'toms-caprice'); ?>
                                                </a>
                                            <?php }elseif( $TOMS_JSON[$slug]['Status'] == 'comming-soon' ){ ?>
                                                <a class="update-now button" target="_blank" disabled>
                                                    <?php _e('Installed', 'toms-caprice'); ?>
                                                </a>
                                            <?php }else{ ?>
                                                <a class="update-now button" target="_blank" disabled>
                                                    <?php _e('Installed', 'toms-caprice'); ?>
                                                </a>
                                            <?php } ?>
                                        <?php }else{ ?>
                                            <div class="<?php echo esc_attr( $slug ); ?>-version toms-plugins-version"><?php echo __('Status:', 'toms-caprice') . '&nbsp;&nbsp;' . '<a style="color: #795548;" href="'. esc_url( $TOMS_URL.$slug ).'" >' . __( 'Not install', 'toms-caprice') . '</a>'; ?></div>
                                        
                                        <?php if( $TOMS_JSON[$slug]['Status'] == 'extented' ){ ?>
                                            <a href="<?php echo esc_url( $TOMS_URL.$slug ); ?>" class="update-now button" target="_blank">
                                                <?php _e('View Details', 'toms-caprice'); ?>
                                            </a>
                                        <?php }elseif( $TOMS_JSON[$slug]['Status'] == 'comming-soon' ){ ?>
                                            <a href="<?php echo esc_url( $TOMS_URL.$slug ); ?>" class="update-now button" target="_blank">
                                                <?php _e('Comming Soon', 'toms-caprice'); ?>
                                            </a>
                                        <?php }else{ ?>
                                            <a href="<?php echo esc_url( $TOMS_URL.$slug ); ?>" class="update-now button" target="_blank">
                                                <?php _e('View Details', 'toms-caprice'); ?>
                                            </a>
                                        <?php } ?>
                                        <?php } ?>
                                    </div>
                                </div>
                        <?php }
                    } ?>
                    </div>
                </div>
            </div>

        </div>
        <?php }

    }

    $TomSCaprice = new TomSCaprice();
}