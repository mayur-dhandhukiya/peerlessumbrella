<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Invoice Me Payment Gateway
 *
 * Provides a Invoice Payment Gateway, mainly for invoice purposes.
 *
 * @class 		WC_Gateway_InvoiceMe
 * @extends		WC_Payment_Gateway
 */
class WC_Gateway_InvoiceMe extends WC_Payment_Gateway {

    /**
     * Constructor for the gateway.
     */
		public function __construct() {
			$this->id                 = 'invoiceme';
			$this->icon               = apply_filters( 'woocommerce_invoiceme_logo', plugins_url( 'images/invoiceme.png', dirname( __FILE__ ) ) );
			$this->has_fields         = true;
			$this->method_title       = __( 'Invoice Me', 'woocommerce-gateway-invoiceme' );
			$this->method_description = __( 'Allows Invoice Me Option For Selected Customers In Checkout Page', 'woocommerce-gateway-invoiceme' );

			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();

			// Define user set variables
			$this->title        = $this->get_option( 'title' );
			$this->description  = $this->get_option( 'description' );
			$this->instructions = $this->get_option( 'instructions', $this->description );
			$this->default_order_status  = $this->get_option( 'default_order_status' );
			$this->order_button_text  = $this->get_option( 'btn_text_paynow' );
			$this->visitors_enabled  = $this->get_option( 'visitors_enabled' );
			$this->reducing_stock  = $this->get_option( 'reducing_stock' );
			$this->invoiceme_customer_roles = $this->get_option( 'invoiceme_customer_roles', array() );

			//settings pass to gateway register
			if(is_array($this->invoiceme_customer_roles) && !empty($this->invoiceme_customer_roles)){
				update_option( 'invoiceme_allow_roles', $this->invoiceme_customer_roles );
			}else{
				update_option( 'invoiceme_allow_roles', '');
			}

			if(($this->visitors_enabled != '') && ($this->visitors_enabled == 'yes')){
				update_option( 'invoiceme_allow_visitors', 'yes' );
			}else{
				update_option( 'invoiceme_allow_visitors', '' );
			}

			// Actions
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
    	add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
    	// Customer Emails
    	add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
    }

    /**
     * Initialise Gateway Settings Form Fields
     */
    public function init_form_fields() {

			$userroles  = array();

			global $wp_roles;
			$all_roles = $wp_roles->roles;
			$editable_roles = apply_filters('editable_roles', $all_roles);
			foreach ($editable_roles as $role_name => $role_info){
				$userroles[ $role_name ] = $role_info['name'];
			}


    	$this->form_fields = array(
					'enabled' => array(
						'title'   => __( 'Enable/Disable', 'woocommerce-gateway-invoiceme' ),
						'type'    => 'checkbox',
						'label'   => __( 'Enable Invoice Me', 'woocommerce-gateway-invoiceme' ),
						'default' => 'yes'
					),
					'title' => array(
						'title'       => __( 'Title', 'woocommerce-gateway-invoiceme' ),
						'type'        => 'text',
						'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-gateway-invoiceme' ),
						'default'     => __( 'Invoice Me', 'woocommerce-gateway-invoiceme' ),
						'desc_tip'    => true,
					),
					'description' => array(
						'title'       => __( 'Description', 'woocommerce-gateway-invoiceme' ),
						'type'        => 'textarea',
						'description' => __( 'Payment method description that the customer will see on your checkout.', 'woocommerce-gateway-invoiceme' ),
						'default'     => __( 'You are allowed to use our Manual invoice checkout.', 'woocommerce-gateway-invoiceme' ),
						'desc_tip'    => true,
					),
					'instructions' => array(
						'title'       => __( 'Instructions', 'woocommerce-gateway-invoiceme' ),
						'type'        => 'textarea',
						'description' => __( 'Instructions message that display on checkout confirmation page.', 'woocommerce-gateway-invoiceme' ),
						'default'     => __( 'Thank you for staying with us.', 'woocommerce-gateway-invoiceme' ),
						'desc_tip'    => true,
					),
					'btn_text_paynow' => array(
						'title'       => __( 'Button Text', 'woocommerce-gateway-invoiceme' ),
						'type'        => 'text',
						'description' => __( 'Place order button text', 'woocommerce-gateway-invoiceme' ),
						'default'     => __( 'Proceed Invoice Me', 'woocommerce-gateway-invoiceme' ),
						'desc_tip'    => true,
					),
					'default_order_status' => array(
						'title'       => __( 'Order Status', 'woocommerce-gateway-invoiceme' ),
						'type'        => 'select',
						'description' => __( 'Choose immediate order status at customer checkout.', 'woocommerce-gateway-invoiceme' ),
						'default'     => 'pending',
						'desc_tip'    => true,
						'options'     => array(
							'pending'          => __( 'Pending payment', 'woocommerce-gateway-invoiceme' ),
							'on-hold'          => __( 'On Hold', 'woocommerce-gateway-invoiceme' ),
							'processing' => __( 'Processing', 'woocommerce-gateway-invoiceme' ),
							'completed' => __( 'Completed', 'woocommerce-gateway-invoiceme' )
						)
					),
					'invoiceme_customer_roles' => array(
						'title'       => __( 'Allow Role Based Customers', 'woocommerce-gateway-invoiceme' ),
						'type'              => 'multiselect',
						'class'             => 'chosen_select',
						'css'               => 'width: 450px;',
						'default'           => '',
						'description'       => __( 'Allow group of users to use Invoice Me Checkout. You can select multiple user Role. Also available for individual customers using customer profile page.', 'woocommerce-gateway-invoiceme' ),
						'options'           => $userroles,
						'desc_tip'          => true,
						'custom_attributes' => array(
							'data-placeholder' => __( 'Select Customer Roles', 'woocommerce-gateway-invoiceme' )
						)
					),
					'visitors_enabled' => array(
						'title'   => __( 'Visitor Enable', 'woocommerce-gateway-invoiceme' ),
						'type'    => 'checkbox',
						'desc_tip'          => true,
						'description'       => __( 'Important!!! By Eanbling this feature, anyone who visit this site can checkout using Invoice Me gateway.', 'woocommerce-gateway-invoiceme' ),
						'label'   => __( 'Enable Invoice Me For Visitors', 'woocommerce-gateway-invoiceme' ),
						'default' => 'no'
					),
					'reducing_stock' => array(
						'title'   => __( 'Reducing Stock', 'woocommerce-gateway-invoiceme' ),
						'type'    => 'checkbox',
						'desc_tip'          => true,
						'description'       => __( 'By Eanbling this feature, Stock will reduce automatically when checkout.', 'woocommerce-gateway-invoiceme' ),
						'label'   => __( 'Reduce Stock When Checkout', 'woocommerce-gateway-invoiceme' ),
						'default' => 'yes'
					),
			);
    }

    /**
     * Output for the order received page.
     */
		public function thankyou_page() {
			if ( $this->instructions ){
				echo wpautop( wptexturize( $this->instructions ) );
			}
		}

    /**
     * Add content to the WC emails.
     *
     * @access public
     * @param WC_Order $order
     * @param bool $sent_to_admin
     * @param bool $plain_text
     */
		public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {

				$payment_method    = (true === version_compare(WOOCOMMERCE_VERSION, '3.0', '<')) ? $order->payment_method          : $order->get_payment_method();

				if ( $this->instructions && ! $sent_to_admin && 'invoiceme' === $payment_method && $order->has_status( $this->default_order_status ) ) {
					echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
				}
		}


    /**
     * Process the payment and return the result
     *
     * @param int $order_id
     * @return array
     */
		public function process_payment( $order_id ) {

			$order = wc_get_order( $order_id );

			update_post_meta( $order_id, '_invoice_me', 'yes' );
			update_post_meta( $order_id, '_invoice_me_status', 'pending' );

			// Mark as on-hold (we're awaiting shop manager approval)
			$order->update_status( $this->default_order_status, __( 'Awaiting Invoice Me', 'woocommerce-gateway-invoiceme' ) );

			// Reduce stock levels
			if(($this->reducing_stock != '') && ($this->reducing_stock == 'yes')){
				$order->reduce_order_stock();
			}


			// Remove cart
			WC()->cart->empty_cart();

			// Return thankyou redirect
			return array(
				'result' 	=> 'success',
				'redirect'	=> $this->get_return_url( $order )
			);
		}
}
