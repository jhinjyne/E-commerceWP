<?php

namespace WPDesk\FlexibleShippingUps;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use UpsFreeVendor\Octolize\Onboarding\Field\Html;
use UpsFreeVendor\Octolize\Onboarding\OnboardingAjax;
use UpsFreeVendor\Octolize\Onboarding\OnboardingButton;
use UpsFreeVendor\Octolize\Onboarding\OnboardingDeactivationData;
use UpsFreeVendor\Octolize\Onboarding\OnboardingOption;
use UpsFreeVendor\Octolize\Onboarding\OnboardingShouldShowGetParametersStrategy;
use UpsFreeVendor\Octolize\Onboarding\OnboardingShouldShowNeverStrategy;
use UpsFreeVendor\Octolize\Onboarding\OnboardingStep;
use UpsFreeVendor\Octolize\Onboarding\OnboardingTrackerData;
use UpsFreeVendor\WPDesk\AbstractShipping\Settings\SettingsValuesAsArray;
use UpsFreeVendor\WPDesk\Forms\Field;
use UpsFreeVendor\WPDesk\Forms\Field\InputTextField;
use UpsFreeVendor\WPDesk\UpsShippingService\UpsApi\ConnectionChecker;
use UpsFreeVendor\WPDesk\UpsShippingService\UpsSettingsDefinition;
use UpsFreeVendor\WPDesk\UpsShippingService\UpsShippingService;

/**
 * Can display onboarding.
 */
class Onboarding extends \UpsFreeVendor\Octolize\Onboarding\Onboarding implements LoggerAwareInterface {

	use LoggerAwareTrait;

	const STEP_1             = 'step_1';
	const STEP_2             = 'step_2';
	const STEP_3             = 'step_3';
	const STEP_4             = 'step_4';
	const UPS_USER_ID        = 'ups_user_id';
	const UPS_PASSWORD       = 'ups_password';
	const UPS_ACCESS_KEY     = 'ups_access_key';
	const UPS_ACCOUNT_NUMBER = 'ups_account_number';
	const STATUS             = 'status';
	const OK                 = 'ok';
	const MESSAGE            = 'message';
	const ZONE_ID            = 'zone_id';

	/**
	 * @var array<string, string>
	 */
	private $global_ups_woocommerce_options;

	/**
	 * @var UpsShippingService
	 */
	private $ups_shipping_service;

	/**
	 * @var OnboardingOption
	 */
	private $onboarding_option;

	/**
	 * @var string
	 */
	private $plugin_file;

	/**
	 * @param UpsShippingService    $ups_shipping_service           .
	 * @param array<string, string> $global_ups_woocommerce_options .
	 * @param string                $plugin_file                    .
	 * @param LoggerInterface       $logger                         .
	 */
	public function __construct( UpsShippingService $ups_shipping_service, array $global_ups_woocommerce_options, string $plugin_file, LoggerInterface $logger ) {
		$this->global_ups_woocommerce_options = $global_ups_woocommerce_options;
		$this->ups_shipping_service           = $ups_shipping_service;
		$this->plugin_file                    = $plugin_file;
		$this->logger                         = $logger;
		$steps                                = $this->prepare_steps();
		$this->onboarding_option              = new OnboardingOption( UpsShippingService::UNIQUE_ID );
		if ( ! $this->onboarding_already_shown_3_times( $this->onboarding_option )
		     && $this->latest_display_before_30_mins( $this->onboarding_option )
		     && $this->plugin_has_no_settings()
		) {
			$onboarding_should_show_strategy = new OnboardingShouldShowGetParametersStrategy(
				[
					[
						'page'       => 'update-core',
						'parameters' => [],
					],
					[
						'page'       => 'plugins',
						'parameters' => [],
					],
					[
						'page'       => 'woocommerce_page_wc-settings',
						'parameters' => [
							'tab'     => 'shipping',
							'section' => ''
						],
					],
				]
			);
		} else {
			$onboarding_should_show_strategy = new OnboardingShouldShowNeverStrategy();
		}
		$onboarding_ajax = new OnboardingAjax( $this->onboarding_option );
		parent::__construct( UpsShippingService::UNIQUE_ID, true, $onboarding_should_show_strategy, $steps, $onboarding_ajax, $this->onboarding_option );
	}

	public function hooks() {
		parent::hooks();
		add_action( OnboardingAjax::OCTOLIZE_ONBOARDING_SAVE_FIELDS_ACTION . UpsShippingService::UNIQUE_ID, [
			$this,
			'save_onboarding_data'
		] );
		( new OnboardingTrackerData( UpsShippingService::UNIQUE_ID, $this->onboarding_option ) )->hooks();
		( new OnboardingDeactivationData( $this->plugin_file, $this->onboarding_option ) )->hooks();
	}

	/**
	 * @param array<string, string> $data
	 *
	 * @return void
	 */
	public function save_onboarding_data( $data ): void {
		$data = is_array( $data ) ? $data : [ $data ]; /** @phpstan-ignore-line */
		$popup_id = $data['popup_id'] ?? '';
		if ( $popup_id === self::STEP_2 ) {
			$popups = $this->save_ups_credentials( $data );

			wp_send_json( [ 'popups' => $popups ] );
		}
		if ( $popup_id === self::STEP_3 ) {
			$popups = $this->create_shipping_methods( $data );

			wp_send_json( [ 'popups' => $popups ] );
		}
	}

	private function onboarding_already_shown_3_times( OnboardingOption $onboarding_option ): bool {
		return (int) $onboarding_option->get_option_value( OnboardingOption::VIEWS, 0 ) > 2; /** @phpstan-ignore-line */
	}

	private function latest_display_before_30_mins( OnboardingOption $onboarding_option ): bool {
		return (int) $onboarding_option->get_option_value( OnboardingOption::EVENT_TIME, 0 ) < time() - 1800; /** @phpstan-ignore-line */
	}

	private function plugin_has_no_settings(): bool {
		return empty( $this->global_ups_woocommerce_options[ UpsSettingsDefinition::USER_ID ] )
		       && empty( $this->global_ups_woocommerce_options[ UpsSettingsDefinition::PASSWORD ] )
		       && empty( $this->global_ups_woocommerce_options[ UpsSettingsDefinition::ACCESS_KEY ] )
		       && empty( $this->global_ups_woocommerce_options[ UpsSettingsDefinition::ACCOUNT_NUMBER ] );
	}

	/**
	 * @param array<string, string> $connection_status
	 * @param bool                  $shipping_method_created
	 *
	 * @return OnboardingStep[]
	 */
	private function prepare_steps( array $connection_status = [], bool $shipping_method_created = true ) {
		return [
			$this->prepare_step_1(),
			$this->prepare_step_2( $connection_status ),
			$this->prepare_step_3( $shipping_method_created ),
			$this->prepare_step_4(),
		];
	}

	private function prepare_step_1(): OnboardingStep {
		$step1 = new OnboardingStep(
			self::STEP_1,
			1,
			__( 'Step 1', 'flexible-shipping-ups' ),
			[
				( new Html() )->set_default_value( '<img height="240" src="' . esc_url( plugin_dir_url( __FILE__ ) . '../../assets/images/onboarding.gif' ) . '" />' ),
				( new Html() )->set_default_value(
					sprintf(
						__( 'Thank you for activating the UPS Live Rates plugin.%1$s You are only a few steps away from using the real-time calculated UPS rates in your store. Let us walk you through the whole configuration process step by step.', 'flexible-shipping-ups' ),
						'<br />'
					)
				),
			],
			[
				( new OnboardingButton() )->set_label( __( 'No, thank you', 'flexible-shipping-ups' ) )->set_classes( OnboardingButton::BTN_LINK ),
				( new OnboardingButton() )->set_label( __( 'Start configuration', 'flexible-shipping-ups' ) )->set_popup( self::STEP_2 ),
			]
		);
		$step1->set_show( true );
		$step1->set_heading( __( 'UPS Live Rates plugin configuration', 'flexible-shipping-ups' ) );

		return $step1;
	}

	/**
	 * @param array<string, string> $connection_status
	 *
	 * @return OnboardingStep
	 */
	private function prepare_step_2( array $connection_status = [] ): OnboardingStep {
		$buttons = [
			( new OnboardingButton() )->set_label( __( 'Previous step', 'flexible-shipping-ups' ) )->set_popup( self::STEP_1 )->set_classes( OnboardingButton::BTN_LINK ),
			( new OnboardingButton() )->set_label( __( 'Connect with UPS API', 'flexible-shipping-ups' ) )->set_classes( OnboardingButton::BTN_SUCCESS )->set_type( OnboardingButton::TYPE_AJAX )->set_popup( self::STEP_2 ),
		];
		$step2   = new OnboardingStep(
			self::STEP_2,
			2,
			__( 'Step 2', 'flexible-shipping-ups' ),
			$this->prepare_connection_fields( $connection_status ),
			$buttons
		);
		$step2->set_show( false );
		$step2->set_heading( __( 'Connecting to your UPS account', 'flexible-shipping-ups' ) );

		return $step2;
	}

	private function prepare_step_3( bool $shipping_method_created = true ): OnboardingStep {
		$content = [
			( new Html() )->set_default_value( __( 'Choose the shipping zone you want to add the UPS Live Rates method within.', 'flexible-shipping-ups' ) ),
		];
		if ( ! $shipping_method_created ) {
			$content[] = ( new Html() )->set_default_value( sprintf( __( '%1$sNo shipping zone has been selected, please select at least one.%2$s' ), '<span class="text-danger"><strong>', '</strong></span>' ) );
		}
		$buttons = [
			( new OnboardingButton() )->set_label( __( 'Previous step', 'flexible-shipping-ups' ) )->set_popup( self::STEP_2 )->set_classes( OnboardingButton::BTN_LINK ),
			( new OnboardingButton() )->set_label( __( 'Add to chosen shipping zones', 'flexible-shipping-ups' ) )->set_classes( OnboardingButton::BTN_SUCCESS )->set_type( OnboardingButton::TYPE_AJAX )->set_popup( self::STEP_3 ),
		];
		$step3   = new OnboardingStep(
			self::STEP_3,
			3,
			__( 'Step 3', 'flexible-shipping-ups' ),
			$content,
			$buttons
		);
		$step3->set_show( false );
		$step3->set_heading( __( 'Adding the UPS Live Rates shipping method', 'flexible-shipping-ups' ) );

		return $step3;
	}

	private function prepare_step_4(): OnboardingStep {
		$content = [
			( new Html() )->set_default_value( '<img height="240" src="' . esc_url( plugin_dir_url( __FILE__ ) . '../../assets/images/onboarding.gif' ) . '" />' ),
			( new Html() )->set_default_value( sprintf( __( 'You are all set now! UPS Live Rates should be available from now on to your customers.%1$s Please mind that shipping methods provided by UPS Live Rates plugin show up if the shop’s address has been entered in the WooCommerce settings.%1$s Testing if everything works fine and the UPS Live Rates shipping methods are displayed properly, e.g., in the cart is highly advised.', 'flexible-shipping-ups' ), '<br/>' ) ),
		];
		$buttons = [
			( new OnboardingButton() )->set_label( __( 'Done!', 'flexible-shipping-ups' ) )->set_classes( OnboardingButton::BTN_SUCCESS )->set_type( OnboardingButton::TYPE_CLOSE ),
		];
		$step3   = new OnboardingStep(
			self::STEP_4,
			4,
			__( 'Step 4', 'flexible-shipping-ups' ),
			$content,
			$buttons
		);
		$step3->set_show( false );
		$step3->set_heading( __( 'Configuration complete!', 'flexible-shipping-ups' ) );

		return $step3;
	}

	/**
	 * @return array<string, string>
	 */
	private function prepare_connection_status_text(): array {
		$connection_status  = [ self::STATUS => self::OK ];
		$connection_checker = new ConnectionChecker( $this->ups_shipping_service, new SettingsValuesAsArray( $this->global_ups_woocommerce_options ), $this->logger );	/** @phpstan-ignore-line */
		try {
			$connection_checker->check_connection();
		} catch ( \Exception $e ) {
			$connection_status = [
				self::STATUS  => 'error',
				self::MESSAGE => $e->getMessage(),
			];
		}

		return $connection_status;
	}

	/**
	 * @param array<string, string> $connection_status
	 *
	 * @return Field[]
	 */
	private function prepare_connection_fields( array $connection_status ): array {
		$fields = [
			( new Html() )->set_default_value(
				sprintf(
					__( 'Let\'s begin with entering your UPS account credentials to be able to establish the UPS API connection.%1$s Please fill in the fields below with the proper data. All the information on how to obtain and where to find the UPS credentials can be found in %2$sthe plugin\'s documentation.%3$s', 'flexible-shipping-ups' ),
					'<br/>',
					'<a href="https://octol.io/ups-oboarding-credentials" target="_blank">',
					'</a>'
				)
			),
		];

		if ( $connection_status ) {
			$connection_status = $this->prepare_connection_status_text();
			if ( $connection_status[ self::STATUS ] === self::OK ) {
				$fields[] = ( new Html() )->set_default_value( sprintf( __( '%1$sConnection status: OK%2$s' ), '<span class="text-success">', '</span>' ) );
			} else {
				$fields[] = ( new Html() )->set_default_value( sprintf( __( '%1$sConnection status: %2$s%3$s' ), '<span class="text-danger"><strong>', $connection_status[ self::MESSAGE ], '</strong></span>' ) );
			}
		}

		$ups_user_id_field = new InputTextField();
		$ups_user_id_field->set_label( __( 'UPS User ID', 'flexible-shipping-ups' ) );
		$ups_user_id_field->set_name( self::UPS_USER_ID );
		$ups_user_id_field->set_attribute( 'autocomplete', 'new-password' );
		$ups_user_id_field->set_default_value( $this->global_ups_woocommerce_options[ UpsSettingsDefinition::USER_ID ] ?? '' );
		$fields[] = $ups_user_id_field;

		$ups_password_field = new InputTextField();
		$ups_password_field->set_type( 'password' );
		$ups_password_field->set_label( __( 'UPS Password', 'flexible-shipping-ups' ) );
		$ups_password_field->set_name( self::UPS_PASSWORD );
		$ups_password_field->set_attribute( 'autocomplete', 'new-password' );
		$ups_password_field->set_default_value( $this->global_ups_woocommerce_options[ UpsSettingsDefinition::PASSWORD ] ?? '' );
		$fields[] = $ups_password_field;

		$ups_access_key_field = new InputTextField();
		$ups_access_key_field->set_label( __( 'UPS Access Key', 'flexible-shipping-ups' ) );
		$ups_access_key_field->set_name( self::UPS_ACCESS_KEY );
		$ups_access_key_field->set_attribute( 'autocomplete', 'new-password' );
		$ups_access_key_field->set_default_value( $this->global_ups_woocommerce_options[ UpsSettingsDefinition::ACCESS_KEY ] ?? '' );
		$fields[] = $ups_access_key_field;

		$ups_account_number_field = new InputTextField();
		$ups_account_number_field->set_label( __( 'UPS Account Number', 'flexible-shipping-ups' ) );
		$ups_account_number_field->set_name( self::UPS_ACCOUNT_NUMBER );
		$ups_account_number_field->set_attribute( 'autocomplete', 'new-password' );
		$ups_account_number_field->set_default_value( $this->global_ups_woocommerce_options[ UpsSettingsDefinition::ACCOUNT_NUMBER ] ?? '' );
		$fields[] = $ups_account_number_field;

		return $fields;
	}

	/**
	 * @param Field[] $fields .
	 *
	 * @return Field[]
	 */
	private function prepare_shipping_zones_fields( array $fields ) {
		$shipping_zones = \WC_Shipping_Zones::get_zones();
		foreach ( $shipping_zones as $shipping_zone ) {
			$fields[] = $this->create_zone_checkbox( $shipping_zone[ self::ZONE_ID ] );
		}
		$fields[] = $this->create_zone_checkbox( 0 );

		return $fields;
	}

	/**
	 * @param int $zone_id .
	 *
	 * @return Field\CheckboxField
	 */
	private function create_zone_checkbox( $zone_id ) {
		$wc_shipping_zone = new \WC_Shipping_Zone( $zone_id );
		$checkbox         = new Field\CheckboxField();
		$checkbox->set_name( 'zone-' . $wc_shipping_zone->get_id() );
		$checkbox->set_default_value( (string) $wc_shipping_zone->get_id() );
		$checkbox->set_label( $wc_shipping_zone->get_zone_name() );
		$checkbox->set_sublabel( $wc_shipping_zone->get_formatted_location( 1 ) );
		$checkbox->set_attribute( 'autocomplete', '' );

		return $checkbox;
	}

	/**
	 * @param array<string, string> $data
	 *
	 * @return OnboardingStep[]
	 */
	public function save_ups_credentials( array $data ): array {
		$this->global_ups_woocommerce_options[ UpsSettingsDefinition::USER_ID ]        = sanitize_text_field( $data[ self::UPS_USER_ID ] ?? '' );
		$this->global_ups_woocommerce_options[ UpsSettingsDefinition::PASSWORD ]       = sanitize_text_field( $data[ self::UPS_PASSWORD ] ?? '' );
		$this->global_ups_woocommerce_options[ UpsSettingsDefinition::ACCOUNT_NUMBER ] = sanitize_text_field( $data[ self::UPS_ACCOUNT_NUMBER ] ?? '' );
		$this->global_ups_woocommerce_options[ UpsSettingsDefinition::ACCESS_KEY ]     = sanitize_text_field( $data[ self::UPS_ACCESS_KEY ] ?? '' );

		update_option( 'woocommerce_' . UpsShippingService::UNIQUE_ID . '_settings', $this->global_ups_woocommerce_options );

		$connection_status = $this->prepare_connection_status_text();

		$popups = $this->prepare_steps( $connection_status );
		$popups[0]->set_show( false );
		if ( $connection_status[ self::STATUS ] === self::OK ) {
			$popups[2]->set_show( true );
			$popups[2]->set_content( $this->prepare_shipping_zones_fields( $popups[2]->get_content() ) );
		} else {
			$popups[1]->set_show( true );
		}

		return $popups;
	}

	/**
	 * @param array<string, string> $data
	 *
	 * @return OnboardingStep[]
	 */
	public function create_shipping_methods( array $data ): array {
		$shipping_method_created = false;
		$shipping_zones          = \WC_Shipping_Zones::get_zones();
		foreach ( $shipping_zones as $shipping_zone ) {
			$wc_shipping_zone = new \WC_Shipping_Zone( $shipping_zone[ self::ZONE_ID ] );
			if ( isset( $data[ 'zone-' . $wc_shipping_zone->get_id() ] ) ) {
				$wc_shipping_zone->add_shipping_method( UpsShippingService::UNIQUE_ID );
				$shipping_method_created = true;
			}
		}
		$wc_shipping_zone = new \WC_Shipping_Zone( 0 );
		if ( isset( $data[ 'zone-' . $wc_shipping_zone->get_id() ] ) ) {
			$wc_shipping_zone->add_shipping_method( UpsShippingService::UNIQUE_ID );
			$shipping_method_created = true;
		}

		$popups = $this->prepare_steps( [ self::STATUS => self::OK ], $shipping_method_created );
		$popups[0]->set_show( false );
		if ( $shipping_method_created ) {
			$popups[3]->set_show( true );
		} else {
			$popups[2]->set_content( $this->prepare_shipping_zones_fields( $popups[2]->get_content() ) );
			$popups[2]->set_show( true );
		}

		return $popups;
	}

}
