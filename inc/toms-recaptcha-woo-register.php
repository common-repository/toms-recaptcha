<?php
/**
 * @shortcode       WooCommerce User Registration Shortcode
 * @shortcode_name  [toms_woo_register_form]
 * @author          Tom Sneddon
 * @compatible      WooCommerce 6.0
 * @version 6.0.0
 * 
 * The Form HTML copy from woocommerce/templates/myaccount/form-login.php Start line: 68.
 */
   
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) && !shortcode_exists('toms_woo_register_form') ){
    
add_shortcode( 'toms_woo_register_form', 'toms_woo_registration_form' );
    
    function toms_woo_registration_form() {
        if ( is_admin() ) return;
        if( is_user_logged_in() )return;

        ob_start();

        wc_print_notices();

    ?>
<!-- Form Style -->
<style>
    .woocommerce-form-register{
        border: 1px solid #d3ced2;
        padding: 20px;
        margin: 2em 0;
        text-align: left;
        border-radius: 5px;
    }
    .woocommerce-form-register .form-row {
        padding: 3px;
        margin: 0 0 6px;
    }
    .woocommerce-form-register .form-row label {
        line-height: 2;
        font-weight: 700;
        font-size: 13.5px;
        font-size: .9rem;
        display: block;
    }
    .woocommerce-form-register .form-row input.input-text{
        box-sizing: border-box;
        width: 100%;
        margin: 0;
        outline: 0;
        line-height: 1;
        border-color: #ddd;
        background: #fff;
        box-shadow: none;
        border-radius: 0;
    }
    .woocommerce-form-register .form-row .required {
        color: red;
        font-weight: 700;
        border: 0;
    }
</style>

<!-- Form HTML Start -->
    <div class="u-column2 col-2">

		<h2><?php esc_html_e( 'Register', 'woocommerce' ); ?></h2>

		<form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >

			<?php do_action( 'woocommerce_register_form_start' ); ?>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
				</p>

			<?php endif; ?>

			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="reg_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
				<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
			</p>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
					<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" />
				</p>

			<?php else : ?>

				<p><?php esc_html_e( 'A link to set a new password will be sent to your email address.', 'woocommerce' ); ?></p>

			<?php endif; ?>

			<?php do_action( 'woocommerce_register_form' ); ?>

			<p class="woocommerce-form-row form-row">
				<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
				<button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Register', 'woocommerce' ); ?></button>
			</p>

			<?php do_action( 'woocommerce_register_form_end' ); ?>

		</form>

	</div>
<!-- Form HTML End -->
    <?php   
    return ob_get_clean();
    }
}