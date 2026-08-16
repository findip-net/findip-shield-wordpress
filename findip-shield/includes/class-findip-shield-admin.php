<?php
/**
 * WordPress administration settings.
 *
 * @package FindIP_Shield
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and renders privacy-safe plugin settings.
 */
final class FindIP_Shield_Admin {
	/**
	 * Register admin hooks.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
	}

	/**
	 * Register the plugin settings.
	 */
	public function register_settings() {
		register_setting(
			'findip_shield',
			FindIP_Shield::OPTION_NAME,
			array(
				'type'              => 'array',
				'default'           => FindIP_Shield::default_settings(),
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
			)
		);
	}

	/**
	 * Sanitize all plugin settings.
	 *
	 * @param mixed $input Submitted settings.
	 * @return array<string, mixed>
	 */
	public function sanitize_settings( $input ) {
		$defaults = FindIP_Shield::default_settings();
		$input    = is_array( $input ) ? $input : array();
		$site_key = isset( $input['site_key'] ) ? sanitize_text_field( $input['site_key'] ) : '';

		if ( '' !== $site_key && ! preg_match( '/^pub_[a-f0-9]+$/', $site_key ) ) {
			add_settings_error(
				FindIP_Shield::OPTION_NAME,
				'invalid_site_key',
				esc_html__( 'The public site key must start with pub_ and contain lowercase hexadecimal characters.', 'findip-shield' )
			);
			$site_key = '';
		}

		$privacy_mode = isset( $input['privacy_mode'] ) ? sanitize_key( $input['privacy_mode'] ) : $defaults['privacy_mode'];
		if ( ! in_array( $privacy_mode, array( 'strict', 'balanced', 'advanced' ), true ) ) {
			$privacy_mode = $defaults['privacy_mode'];
		}

		$no_consent_mode = isset( $input['no_consent_mode'] ) ? sanitize_key( $input['no_consent_mode'] ) : $defaults['no_consent_mode'];
		if ( ! in_array( $no_consent_mode, array( 'strict', 'disabled' ), true ) ) {
			$no_consent_mode = $defaults['no_consent_mode'];
		}

		return array(
			'site_key'          => $site_key,
			'privacy_mode'      => $privacy_mode,
			'auto_track'        => ! empty( $input['auto_track'] ),
			'auto_detect_forms' => ! empty( $input['auto_detect_forms'] ),
			'consent_required'  => ! empty( $input['consent_required'] ),
			'no_consent_mode'   => $no_consent_mode,
			'woocommerce'       => ! empty( $input['woocommerce'] ),
		);
	}

	/**
	 * Register the Settings page.
	 */
	public function add_settings_page() {
		add_options_page(
			esc_html__( 'FindIP Shield', 'findip-shield' ),
			esc_html__( 'FindIP Shield', 'findip-shield' ),
			'manage_options',
			'findip-shield',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Render the Settings page.
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings = FindIP_Shield::settings();
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'FindIP Shield', 'findip-shield' ); ?></h1>
			<p>
				<?php
				echo wp_kses_post(
					sprintf(
						/* translators: %s: FindIP Shield dashboard URL. */
						__( 'Create a site in the <a href="%s" target="_blank" rel="noopener noreferrer">FindIP Shield dashboard</a>, then paste its public key below.', 'findip-shield' ),
						esc_url( 'https://www.findip.net/shield' )
					)
				);
				?>
			</p>
			<form method="post" action="options.php">
				<?php settings_fields( 'findip_shield' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="findip-shield-site-key"><?php echo esc_html__( 'Public site key', 'findip-shield' ); ?></label></th>
						<td>
							<input id="findip-shield-site-key" class="regular-text code" name="<?php echo esc_attr( FindIP_Shield::OPTION_NAME ); ?>[site_key]" value="<?php echo esc_attr( $settings['site_key'] ); ?>" placeholder="pub_0123456789abcdef" pattern="pub_[a-f0-9]+">
							<p class="description"><?php echo esc_html__( 'A public identifier—not the secret server-verification key.', 'findip-shield' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="findip-shield-privacy-mode"><?php echo esc_html__( 'Privacy mode', 'findip-shield' ); ?></label></th>
						<td>
							<select id="findip-shield-privacy-mode" name="<?php echo esc_attr( FindIP_Shield::OPTION_NAME ); ?>[privacy_mode]">
								<?php
								$privacy_modes = array(
									'strict'   => __( 'Strict', 'findip-shield' ),
									'balanced' => __( 'Balanced', 'findip-shield' ),
									'advanced' => __( 'Advanced', 'findip-shield' ),
								);
								foreach ( $privacy_modes as $value => $label ) :
									?>
									<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $settings['privacy_mode'], $value ); ?>><?php echo esc_html( $label ); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo esc_html__( 'Collection', 'findip-shield' ); ?></th>
						<td>
							<label><input type="checkbox" name="<?php echo esc_attr( FindIP_Shield::OPTION_NAME ); ?>[auto_track]" value="1" <?php checked( $settings['auto_track'] ); ?>> <?php echo esc_html__( 'Track page and session events automatically', 'findip-shield' ); ?></label><br>
							<label><input type="checkbox" name="<?php echo esc_attr( FindIP_Shield::OPTION_NAME ); ?>[auto_detect_forms]" value="1" <?php checked( $settings['auto_detect_forms'] ); ?>> <?php echo esc_html__( 'Detect form activity without reading form values', 'findip-shield' ); ?></label><br>
							<label><input type="checkbox" name="<?php echo esc_attr( FindIP_Shield::OPTION_NAME ); ?>[woocommerce]" value="1" <?php checked( $settings['woocommerce'] ); ?>> <?php echo esc_html__( 'Enable WooCommerce checkout events when WooCommerce is active', 'findip-shield' ); ?></label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo esc_html__( 'Consent', 'findip-shield' ); ?></th>
						<td>
							<label><input type="checkbox" name="<?php echo esc_attr( FindIP_Shield::OPTION_NAME ); ?>[consent_required]" value="1" <?php checked( $settings['consent_required'] ); ?>> <?php echo esc_html__( 'Require an explicit consent signal', 'findip-shield' ); ?></label>
							<p class="description"><?php echo esc_html__( 'Dispatch findip:consent with detail.granted set to true or false from your consent manager.', 'findip-shield' ); ?></p>
							<select name="<?php echo esc_attr( FindIP_Shield::OPTION_NAME ); ?>[no_consent_mode]">
								<option value="strict" <?php selected( $settings['no_consent_mode'], 'strict' ); ?>><?php echo esc_html__( 'Before consent: strict mode', 'findip-shield' ); ?></option>
								<option value="disabled" <?php selected( $settings['no_consent_mode'], 'disabled' ); ?>><?php echo esc_html__( 'Before consent: no tracking', 'findip-shield' ); ?></option>
							</select>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
			<p>
				<a href="https://www.findip.net/docs/shield" target="_blank" rel="noopener noreferrer"><?php echo esc_html__( 'Documentation', 'findip-shield' ); ?></a>
				· <a href="mailto:info@findip.net"><?php echo esc_html__( 'Support', 'findip-shield' ); ?></a>
				· <a href="mailto:security@findip.net"><?php echo esc_html__( 'Security', 'findip-shield' ); ?></a>
			</p>
		</div>
		<?php
	}
}
