<?php
declare(strict_types=1);

namespace NodaPay;

use NodaPay\Base\Api\CommandFactory;
use NodaPay\Base\Api\Commands\PreparePayButton;
use NodaPay\Base\Api\Commands\PrepareBuffet;
use NodaPay\Base\DTO\DataTransferObjectFactory;
use NodaPay\Base\DTO\PreparePayButtonRequest;
use NodaPay\Base\DTO\RequestObject;
use NodaPay\Payment\Exceptions\PaymentFailedException;
use NodaPay\Payment\NodaPayment;
use NodaPay\Payment\Responses\ErrorResponse;
use NodaPay\Payment\Responses\PayUrl;
use NodaPay\Traits\NodaSettings;
use Throwable;
use WC_Order;
use WC_Payment_Gateway;


class Gateway extends WC_Payment_Gateway {

	use NodaSettings;

	const GATEWAY_ID = 'noda_pay';

	const API_KEY_DEV       = '24d0034-5a83-47d5-afa0-cca47298c516';
	const API_SIGNATURE_DEV = '028b9b98-f250-492c-a63a-dfd7c112cc0a';
	const SHOP_ID_DEV       = 'd0c3ccd9-162c-497e-808b-e769aea89c58';
	const NODA_HOME         = 'https://noda.live/';

	public function __construct() {
		$this->id                 = self::GATEWAY_ID; // Unique ID for your payment gateway
		$this->icon               = ''; // URL to an icon for your payment gateway
		$this->has_fields         = false; // Set to true if you need custom input fields
		$this->method_title       = 'NodaPay Gateway';
		$this->method_description = 'Accept payments using NodaPay.';



		// Load settings
        if (is_admin()) {
            $this->init_form_fields();
        }

		$this->init_settings();
		$this->init_actions();
		$this->init_styles();

		// Define user set variables
		$this->title       = $this->get_option( 'nodalive_title' );
		$this->description = $this->get_option( 'nodalive_description' );

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ] );

        if (!is_admin()) {
            if ($this->get_option('nodalive_baffet_enable') === 'yes') {
                add_action( 'woocommerce_after_checkout_form', [ $this, 'addCheckoutBuffet' ] );
            } else {
                add_action( 'woocommerce_after_checkout_form', [ $this, 'addCheckoutScript' ] );
            }
        }
    }

	private function init_styles() {
		wp_register_style( 'nodalive_main_css', plugin_dir_url( __DIR__ ) . '/assets/css/main.css' );
		wp_enqueue_style( 'nodalive_main_css' );
	}

    public function addCheckoutBuffet()
    {
        $selectedBanks = $this->get_option('buffet_option_1');
        $buffetBanks = json_decode($this->get_option('buffet_banks'), true);

        $backgroundColorOption = $this->getOption('nodalive_button_background');
        $backgroundColorValue = !empty($backgroundColorOption) ? $backgroundColorOption : 'transparent';

        $borderColorOption = $this->getOption('nodalive_button_border');
        $borderColorValue = !empty($borderColorOption) ? $borderColorOption : 'transparent';

        ?>
        <div id="custom-place-order-options" style="display: none;">
            <?php
            if (!empty($selectedBanks) && is_array($selectedBanks)) {
                foreach ($selectedBanks as $bank) {
                    if (isset($buffetBanks[$bank])) {
                        ?>
                        <button class="custom-place-order-button" data-payment-type="<?= htmlspecialchars($buffetBanks[$bank]['id']) ?>">
                            <img src="<?= htmlspecialchars($buffetBanks[$bank]['logoUrl']) ?>" alt="<?= htmlspecialchars($buffetBanks[$bank]['name']) ?>">
                            <?= htmlspecialchars($buffetBanks[$bank]['name']) ?>
                        </button>
                        <?php
                    }
                }
            }
            ?>
        </div>


        <script type="text/javascript">
            jQuery(document).ready(function($) {
                function insertCustomButtons() {
                    let customButtons = $('#custom-place-order-options');

                    if (!customButtons.parent().hasClass('woocommerce-checkout')) {
                        $('.etheme-shipping-fields-wrapper.etheme-cart-checkout-tab[data-step="payment"]').append(customButtons);
                    }
                }

                function togglePlaceOrderButton(selectedId) {
                    let placeOrderButton = $('#place_order');
                    let customButtons = $('#custom-place-order-options');

                    if (selectedId === 'payment_method_noda_pay') {
                        placeOrderButton.hide();
                        customButtons.show();
                    } else {
                        placeOrderButton.show();
                        customButtons.hide();
                    }
                }

                $(document).on('payment_method_selected updated_checkout', function() {
                    insertCustomButtons();

                    let selectedPayment = $('input[name="payment_method"]:checked');
                    togglePlaceOrderButton(selectedPayment.attr('id'));
                });

                $(document).on('click', '.custom-place-order-button', function(e) {
                    e.preventDefault();
                    let paymentType = $(this).data('payment-type');

                    $('<input>').attr({
                        type: 'hidden',
                        name: 'payment_type',
                        value: paymentType
                    }).appendTo('form.checkout');

                    $('form.checkout').submit();
                });
            });
        </script>
        <style>
            #custom-place-order-options {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                justify-content: flex-start;
                margin-top: 20px;
            }

            #custom-place-order-options .custom-place-order-button {
                flex: 1 1 calc(33.333% - 10px);
                min-width: 200px;
                box-sizing: border-box;
                height: auto;
                padding: 25px;
                display: inline-flex;
                align-items: center;
                border: 2px solid #FFFFFF;
                border-radius: 10px;
                font-size: 18px;
                font-weight: bold;
                text-decoration: none;
                text-transform: uppercase;
                transition: all 0.3s ease;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.25);
                background-repeat: no-repeat;
                background-size: 100% auto;
                background-position: center;
                background-color: transparent;
                justify-content: center;
                cursor: pointer;
                text-align: center;
                color: #000;
            }

            #custom-place-order-options .custom-place-order-button img {
                margin-right: 10px;
                width: 24px;
                height: 24px;
                transition: transform 0.3s ease;
            }

            #custom-place-order-options .custom-place-order-button span {
                color: #FFFFFF;
                text-transform: uppercase;
            }

            #custom-place-order-options .custom-place-order-button:hover {
                box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
                transform: translateY(-3px);
            }

            #custom-place-order-options .custom-place-order-button:hover img {
                transform: scale(1.1) rotate(5deg);
            }

            #custom-place-order-options .custom-place-order-button:focus {
                outline: none;
                box-shadow: 0px 0px 0px 3px rgba(255, 255, 255, 0.5);
            }

            .amcheckout-wrapper #custom-place-order-options .custom-place-order-button {
                width: 100%;
                padding: 12px 24px;
            }
        </style>

        <?php
    }

	public function addCheckoutScript() {
        $logoUrl = $this->getButtonLogoUrl();

		if (!$logoUrl) {
		    return;
        }

        $backgroundColorOption = $this->getOption('nodalive_button_background');
        $backgroundColorValue = !empty($backgroundColorOption) ? $backgroundColorOption : 'transparent';

        $borderColorOption = $this->getOption('nodalive_button_border');
        $borderColorValue = !empty($borderColorOption) ? $borderColorOption : 'transparent';

		?>
        <script type="text/javascript">
            jQuery(document).on('payment_method_selected updated_checkout', function () {
                let selectedPayment = jQuery('input[name="payment_method"]:checked');
                togglePaymentButton(selectedPayment.attr('id'));
            });
            function togglePaymentButton(selectedId) {
                let placeOrder = jQuery('#place_order');
                if (selectedId === 'payment_method_noda_pay') {
                    placeOrder.addClass('nodalive-button')
                } else if (placeOrder.hasClass('nodalive-button')) {
                    placeOrder.removeClass('nodalive-button')
                }
            }
        </script>
		<style>
            button.nodalive-button {
				background: url('<?php echo $logoUrl; ?>') no-repeat center center <?php echo $backgroundColorValue; ?>;
                background-color: <?php echo $backgroundColorValue; ?> !important;
                border-color: <?php echo $borderColorValue; ?> !important;
                color: transparent !important;
                border: 5px solid <?php echo $borderColorValue; ?> !important;
                border-radius: 10px !important;
			}

            button.nodalive-button:hover {
                border-top-width: 0 !important;
                border-left-width: 5px !important;
                border-right-width: 0 !important;
                border-bottom-width: 5px !important;
                border-bottom-left-radius: 10px !important;
            }
		</style>
		<?php
	}

	private function getButtonLogoUrl() {
		$currency = get_woocommerce_currency();

        try {
            $buttonApiInstance = $this->getCommandFactory()::create(
                PreparePayButton::class,
                $this->getMainConfigs()
            );

            $requestDTO = DataTransferObjectFactory::create(
                PreparePayButtonRequest::class,
                [
                    'currency' => $currency,
                ]
            );

			/** @var RequestObject $requestDTO */
			$responseDTO = $buttonApiInstance->process( $requestDTO );
			$response    = $responseDTO->getResponse();

			if ( $responseDTO->getStatus() !== 200 ) {
				return null;
			}
		} catch ( Throwable $e ) {
			return null;
		}
		return $response->get( 'url' );
	}

	public function init_form_fields() {
		$this->form_fields = [
            'enabled' => [
                'title' 		=> __( 'Enable/Disable' ),
                'type' 			=> 'checkbox',
                'label' 		=> __( 'Enable Noda Payment' ),
                'default' 		=> 'yes'
            ],
			'nodalive_title'                  => [
				'title'       => 'Title',
				'type'        => 'text',
				'description' => 'This controls the title which the user sees during checkout.',
				'default'     => 'NodaPay',
				'desc_tip'    => true,
			],
			'nodalive_is_test'                => [
				'title'       => 'Enable test mode',
				'type'        => 'checkbox',
				'description' => 'If enabled, no real payments will be processed',
				'default'     => 'no',
			],
			'nodalive_api_key'                => [
				'title'       => 'Api Key',
				'type'        => 'password',
				'description' => sprintf(
					'Required for processing payments. Visit <a href="%s" target="_blank>Noda website</a> to get your Api Key',
					self::NODA_HOME
				),
				'default'     => self::API_KEY_DEV,
			],
			'nodalive_signature_key'          => [
				'title'       => 'Signature',
				'type'        => 'password',
				'description' => sprintf(
					'Required for processing payments. Visit <a href="%s" target="_blank>Noda website</a> to get your Signature Key',
					self::NODA_HOME
				),
				'default'     => self::API_SIGNATURE_DEV,
			],
			'nodalive_shop_id'                => [
				'title'       => 'Shop Id',
				'type'        => 'password',
				'description' => sprintf(
					'Required for processing payments. Visit <a href="%s" target="_blank>Noda website</a> to get your Shop Key',
					self::NODA_HOME
				),
				'default'     => self::SHOP_ID_DEV,
			],
            'nodalive_baffet_enable' => [
                'title'       => 'Is Buffet Enable',
                'type'        => 'select',
                'description' => 'Enable additional settings for buffet.',
                'default'     => 'no',
                'options'     => $this->getYesNoOptions(),
            ],
            'nodalive_country' => [
                'title'       => 'Buffet Country Bank',
                'type'        => 'select',
                'description' => 'Choose a country for buffet settings.',
                'default'     => '',
                'options'     => WC()->countries->get_countries(),
            ],
            'buffet_option_1' => [
                'title'       => 'Choose Buffet Banks',
                'type'        => 'multiselect',
                'description' => 'Choose multiple options for buffet.',
                'default'     => [],
                'options'     => $this->getBuffetBanks(),
            ],
			'nodalive_description' => [
				'title'       => 'Description',
				'type'        => 'textarea',
				'description' => 'This controls the description which the user sees during checkout.',
				'default'     => 'Pay securely using NodaPay.',
			],
            'nodalive_redirect_anonymous_users' => [
                'title'       => 'Redirect anonymous users to Homepage',
                'type'        => 'checkbox',
                'description' => 'If set to true, will redirect anonymous users from order view page to Home page ',
                'default'     => 'yes',
            ],
            'nodalive_button_background' => [
                'title'       => 'Button background color',
                'type'        => 'color',
                'description' => 'Select a color for the background of the NodaPay button<br>Background will be transparent case color not set',// Takes effect if transparency option is not selected'
            ],
            'nodalive_button_border' => [
                'title'       => 'Button border color',
                'type'        => 'color',
                'description' => 'Select a color for the border of the NodaPay button<br>Border will be transparent in case color not set', //Takes effect if transparency option is not selected'
                'default'     => '#000000'
            ]
		];
	}

	/**
	 * Initializes new instance of NodaPayment::class
	 */
	public function getPaymentServiceInstance(): NodaPayment {
		return new NodaPayment();
	}

    /**
     * Return if gateway is available for selected currency.
     *
     * @return bool
     */
    public function is_available()
    {
        if ( empty( $this->settings ) ) {
            $this->init_settings();
        }

        return parent::is_available();
    }

	/**
	 * @param int $order_id
	 * @return array
	 */
	public function process_payment( $order_id ) {
		$order = $this->getWCOrder( $order_id );

		// do not process orders if total amount <= 0
		if ( $order->get_total() <= 0 ) {
			$order->payment_complete();

			return $this->getResult( 'success', $order );
		}

		try {
			/** @var PayUrl $response */
			$response = $this->getPaymentServiceInstance()->process( $order );

			if ( $response instanceof ErrorResponse ) {
				throw new PaymentFailedException( $response->getMessage() );
			}

			$payUrl = $response->getUrl();

			$order->add_meta_data( 'payment_link', $payUrl );
            $order->add_meta_data( 'noda_payment_id', $response->getId() );

			// Mark as on-hold (we're awaiting the payment)
			$order->update_status( 'on-hold', 'Awaiting NodaPay payment' );

			// Reduce stock levels
			wc_reduce_stock_levels( $order_id );

			// Empty cart
			WC()->cart->empty_cart();

			// set transaction id for order
			$order->set_transaction_id( $response->getId() );

			if ( is_callable( [ $order, 'save' ] ) ) {
				$order->save();
			}

			return $this->getResult( 'success', $order, $payUrl );
		} catch ( \Throwable $e ) {
            die(var_dump($e->getMessage()));
            // here check different types of exceptions, add logging etc, add error messages to order etc
			$this->handlePaymentFailure( $order, $e );

			return $this->getResult( 'fail', $order );
		}
	}

	private function handlePaymentFailure( WC_Order $order, Throwable $e = null ) {
		wc_add_notice( WP_DEBUG ? $e->getMessage() : 'Failed to process payment', 'error' );
		Logger::log( $e->getMessage() );

		$order->update_status( 'failed' );
	}

	private function getResult( string $status, $order, string $redirectUrl = null ): array {
		return [
			'result'   => $status,
			'redirect' => $redirectUrl ?: $this->get_return_url( $order ),
		];
	}

	private function init_actions() {
		// may be we will need to add some actions
	}

	public function getPaymentRedirectToOptions(): array {
		return [
			static::$paymentRedirectOptionShop         => 'Shop',
			static::$paymentRedirectOptionCart         => 'Shopping cart',
			static::$paymentRedirectOptionHome         => 'Home',
			static::$paymentRedirectOptionOrdersList   => 'Orders',
			static::$paymentRedirectOptionCurrentOrder => 'Current order',
		];
	}

    public function getYesNoOptions(): array
    {
        return [
                'yes' => 'Yes',
                'no'  => 'No',
        ];
    }

    private function getBuffetBanks() {
        try {
            $buffetApiInstance = $this->getCommandFactory()::create(
                PrepareBuffet::class,
                $this->getMainConfigs()
            );

            $requestDTO = DataTransferObjectFactory::create(
                PreparePayButtonRequest::class,
                [
                    'country' => $this->get_option( 'nodalive_country' )
                ]
            );

            /** @var RequestObject $requestDTO */
            $responseDTO = $buffetApiInstance->process( $requestDTO );
            $response    = $responseDTO->getResponse();

            if ( $responseDTO->getStatus() !== 200 ) {
                return null;
            }
        } catch ( Throwable $e ) {
            return null;
        }

        $buffetBanks = [];
        $allBanks = [];

        foreach ($response as $item) {
            $allBanks[$item['id']] = $item;
        }

        $this->update_option('buffet_banks', json_encode($allBanks));

        foreach ($response as $item) {
            $buffetBanks[$item['id']] = $item['name'];
        }

        return $buffetBanks;
    }

	public function getWCOrder( int $orderId ) {
		return wc_get_order( $orderId );
	}

    /**
     * @return CommandFactory
     */
    public function getCommandFactory(): CommandFactory {
        return new CommandFactory();
    }
}
