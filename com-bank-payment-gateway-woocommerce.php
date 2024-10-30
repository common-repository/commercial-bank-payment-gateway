<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/*
Plugin Name: Commercial Bank IPG
Plugin URI: commercialipg.oganro.net
Description: Commercial Bank Payment Gateway from Oganro (Pvt)Ltd.
Version: 1.1
Author: Oganro
Author URI: www.oganro.com
*/

add_action('plugins_loaded', 'woocommerce_combank_gateway', 0);

function woocommerce_combank_gateway(){
  if(!class_exists('WC_Payment_Gateway')) return;

  class WC_Commercial extends WC_Payment_Gateway{
  	
    public function __construct(){
	  $plugin_dir = plugin_dir_url(__FILE__);
      $this->id = 'CommercialIPG';	  
	  $this->icon = apply_filters('woocommerce_Paysecure_icon', ''.$plugin_dir.'commercial.jpg');
      $this->medthod_title = 'CommercialIPG';
      $this->has_fields = false;
 
      $this->init_form_fields();
      $this->init_settings(); 
	  
      $this->title 					= $this -> settings['title'];
	  $this->description 			= $this -> settings['description'];
	  $this->vpc_Version 			= $this -> settings['vpc_Version'];
	  $this->vpc_Merchant 			= $this -> settings['vpc_Merchant'];
	  $this->vpc_AccessCode 		= $this -> settings['vpc_AccessCode'];
	  $this->vpc_Command 			= $this -> settings['vpc_Command'];
	  $this->vpc_Locale 			= $this -> settings['vpc_Locale'];	  	  
	  $this->vpc_SecureHash 		= $this -> settings['vpc_SecureHash'];	  
	  $this->virtualPaymentClientURL= $this -> settings['virtualPaymentClientURL'];
	  $this->sucess_responce_code	= $this -> settings['sucess_responce_code'];	  
	  $this->responce_url_sucess	= $this -> settings['responce_url_sucess'];
	  $this->responce_url_fail		= $this -> settings['responce_url_fail'];	  	  
	  $this->checkout_msg			= $this -> settings['checkout_msg'];	  
	   
      $this->msg['message'] 	= "";
      $this->msg['class'] 		= "";
 
      add_action('init', array(&$this, 'check_CommercialIPG_response'));	  
				  
				if ( version_compare( WOOCOMMERCE_VERSION, '2.0.0', '>=' ) ) {
					add_action( 'woocommerce_update_options_payment_gateways_'.$this->id, array( &$this, 'process_admin_options' ) );
				} else {
					add_action( 'woocommerce_update_options_payment_gateways', array( &$this, 'process_admin_options' ) );
				}
				
	  add_action('woocommerce_receipt_CommercialIPG', array(&$this, 'receipt_page'));
	 
   }
	
  function init_form_fields(){
 
			   $this -> form_fields = array(
						'enabled' => array(
							'title' 	=> __('Enable/Disable', 'ogn'),
							'type' 		=> 'checkbox',
							'label' 	=> __('Enable Commercial IPG Module.', 'ognro'),
							'default' 	=> 'no'),
							
						'title' => array(
							'title' 	=> __('Title:', 'ognro'),
							'type'		=> 'text',
							'description' => __('This controls the title which the user sees during checkout.', 'ognro'),
							'default' 	=> __('Commercial IPG', 'ognro')),
						
						'description' => array(
							'title' 	=> __('Description:', 'ognro'),
							'type'		=> 'textarea',
							'description' => __('This controls the description which the user sees during checkout.', 'ognro'),
							'default' 	=> __('Commercial IPG', 'ognro')),	
							
						'virtualPaymentClientURL' => array(
							'title' 	=> __('VPC URL:', 'ognro'),
							'type'		=> 'text',
							'description' => __('IPG data submiting to this URL', 'ognro'),
							'default' 	=> __('https://migs.mastercard.com.au/vpcpay', 'ognro')),	
			   		
						'vpc_Version' => array(
							'title' 	=> __('VPC Version:', 'ognro'),
							'type'		=> 'text',
							'description' => __('The version of the Virtual Payment Client API being used', 'ognro'),
							'default' 	=> __('1', 'ognro')),	
							
						'vpc_Merchant' => array(
							'title' 	=> __('PG Merchant Id:', 'ognro'),
							'type'		=> 'text',
							'description' => __('Unique ID for the merchant acc, given by bank.', 'ognro'),
							'default' 	=> __('', 'ognro')),
						
						'vpc_AccessCode' => array(
							'title' 	=> __('Merchant Access Code:', 'ognro'),
							'type'		=> 'text',
							'description' => __('Collection of Alphanumerics, given by bank.', 'ognro'),
							'default' 	=> __('', 'ognro')),
						
						'vpc_Command' => array(
							'title' 	=> __('PG command:', 'ognro'),
							'type'		=> 'text',
							'description' => __('Indicates the transaction type, given by bank.', 'ognro'),
							'default' 	=> __('pay', 'ognro')),
						
						'vpc_Locale' => array(
							'title' 	=> __('PG Display Language', 'ognro'),
							'type'		=> 'text',
							'description' => __('Specifies the language used on the Payment Server pages', 'ognro'),
							'default' 	=> __('en', 'ognro')),
							
						'vpc_SecureHash' => array(
							'title' 	=> __('PG Hash Key:', 'ognro'),
							'type'		=> 'text',
							'description' => __('Collection of mix intigers and strings , given by bank.', 'ognro'),
							'default' 	=> __('', 'ognro')),
							
						'sucess_responce_code' => array(
							'title' 	=> __('Sucess responce code :', 'ognro'),
							'type'		=> 'text',
							'description' => __('00 - Transaction Successful ', 'ognro'),
							'default' 	=> __('00', 'ognro')),	  
										
						'checkout_msg' => array(
							'title' 	=> __('Checkout Message:', 'ognro'),
							'type'		=> 'textarea',
							'description' => __('Message display when checkout'),
							'default' 	=> __('Thank you for your order, please click the button below to pay with the secured Commercial Bank payment gateway.', 'ognro')),		
							
						'responce_url_sucess' => array(
							'title' 	=> __('Sucess redirect URL :', 'ognro'),
							'type'		=> 'text',
							'description' => __('After payment is sucess redirecting to this page.'),
							'default' 	=> __('http://your-site.com/thank-you-page/', 'ognro')),
							
						'responce_url_fail' => array(
							'title' 	=> __('Fail redirect URL :', 'ognro'),
							'type'		=> 'text',
							'description' => __('After payment if there is an error redirecting to this page.', 'ognro'),
							'default' 	=> __('http://your-site.com/error-page/', 'ognro'))	
					);
			}
 
  public function admin_options(){
  	
	  	$plugin_path = plugin_dir_path( __FILE__ );
		$file = $plugin_path.'includes/auth.php';
		if(file_exists($file)){
			include 'includes/auth.php';
			$auth = new Auth();
			$auth->check_auth();
			if ( !$auth->get_status() ) {
				deactivate_plugins( plugin_basename( __FILE__ ) );
				if($auth->get_code() == 2){
					wp_die( "<h1>".ucfirst($auth->get_message())."</h1><br>Visit <a href='http://www.oganro.com/plugins/profile'>www.oganro.com/profile</a> and change the domain" ,"Activation Error","ltr" );
				}else{
					wp_die( "<h1>".ucfirst($auth->get_message())."</h1><br>Visit <a href='http://www.oganro.com/wordpress-plug-in-support'>www.oganro.com/wordpress-plug-in-support</a> for more info" ,"Activation Error","ltr" );
				}
			}
		}else{
			deactivate_plugins( plugin_basename( __FILE__ ) );
			$path =  plugin_basename( __FILE__ );
			$dir  = explode("/", $path);
			wp_die( "<h1>Buy serial key to activate this plugin</h1><br><a href='http://www.oganro.com/wordpress-plug-in-support'><img src=".site_url('wp-content/plugins/'.$dir[0].'/support.jpg')." style='width:700px;height:auto;' /></a><p>Visit <a href='http://www.oganro.com/plugins'>www.oganro.com/plugins</a> to buy this plugin<p>" ,"Activation Error","ltr" );
		}
			   echo '<style type="text/css">
				.wpimage {
				margin:3px;
				float:left;
				}		
				</style>';
				echo '<h3>'.__('Commercial bank online payment gateway', 'ognro').'</h3>';
				echo '<p>'.__('<a target="_blank" href="http://www.oganro.com/">Oganro</a> is a fresh and dynamic web design and custom software development company with offices based in East London, Essex, Brisbane (Queensland, Australia) and in Colombo (Sri Lanka).').'</p>';
				//echo'<a href="http://www.oganro.com/support-tickets" target="_blank"><img src="/wp-content/plugins/sampath-bank-ipg/plug-inimg.jpg" alt="payment gateway" class="wpimage"/></a>';
				
				echo '<table class="form-table">';        
				$this->generate_settings_html();
				echo '</table>'; 
			}
	

    function payment_fields(){
        if($this -> description) echo wpautop(wptexturize($this -> description));
    }

    function receipt_page($order){        		
		global $woocommerce;
        $order_details = new WC_Order($order);
        
        echo $this->generate_ipg_form($order);		
		echo '<br>'.$this->checkout_msg.'</b>';        
    }
    	
  public function generate_ipg_form($order_id){
 
				global $wpdb;
				global $woocommerce;
				
				$order         = new WC_Order($order_id);
				$productinfo   = "Order $order_id";		
				$vpc_language  = $this -> vpc_Locale;		
				$curr_symbole  = get_woocommerce_currency();		
				
				/*$messageHash = $this -> vpc_AccessCode."|".$this -> vpc_Merchant."|".$this -> vpc_Command."|".$vpc_Locale."|".(($order -> order_total) * 100)."|".$order_id."|".$this	-> vpc_SecureHash."|";
				$message_hash = "CURRENCY:7:".base64_encode(sha1($messageHash, true)); */
				
								
				$table_name = $wpdb->prefix . 'commercial_ipg';		
				$check_oder = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE merchant_reference_no = '".$order_id."'" );
				
				if($check_oder > 0){
					$wpdb->update( 
						$table_name, 
						array( 
							'transaction_id' 	=> '',
							'message'			=> '',
							'amount' 			=> ($order->order_total),
							'status' 			=> 0000,
							'or_date' 			=> date('Y-m-d'),
							'order_info' 		=> '',
							'receipt_no' 		=> '',
							'batch_no' 			=> '',
							'authorize_id' 		=> '',
							'card_type' 		=> '',
							'aqr_response_code' => ''
						), 
						array( 'merchant_reference_no' => $order_id ));								
				}else{
					
					$wpdb->insert(
						$table_name, 
						array( 
							'transaction_id'		=> '',
							'merchant_reference_no'	=> $order_id,
							'message'				=> '',
							'amount'				=> $order->order_total,
							'status'				=> 00000,
							'or_date' 				=> date('Y-m-d'),
							'order_info'			=> '',
							'receipt_no'			=> '',
							'batch_no'				=> '',
							'authorize_id'			=> '',
							'card_type'				=> '',
							'aqr_response_code'		=> ''
						),
						array( '%s', '%d' ) );					
				}		
						
				
				$form_args = array(
				  	'vpc_Version'		=> $this -> vpc_Version,
				  	'vpc_Command' 		=> $this -> vpc_Command,
				  	'vpc_AccessCode' 	=> $this -> vpc_AccessCode,
				  	'vpc_MerchTxnRef' 	=> $order_id,
				  	'vpc_Merchant' 		=> $this -> vpc_Merchant,
				  	'vpc_OrderInfo' 	=> $productinfo,
				  	'vpc_Amount' 		=> (($order -> order_total ) * 100 ),
				  	'vpc_Locale' 		=> $vpc_Locale,
					'vpc_SecureHash'	=> $this->vpc_SecureHash
				);
				  
				$form_args_array = array();
				foreach($form_args as $key => $value){
				  $form_args_array[] = "<input type='hidden' name='$key' value='$value'/>";
				}
				return '<p>'.$percentage_msg.'</p>
				<p>Total amount will be <b>'.$curr_symbole.' '.number_format(($order->order_total)).'</b></p>
				<form action="'.$this -> virtualPaymentClientURL.'" method="post" id="merchantForm">
					' . implode('', $form_args_array) . '
					<input type="submit" class="button-alt" id="submit_ipg_payment_form" value="'.__('Pay via Credit Card', 'ognro').'" /> 
					<a class="button cancel" href="'.$order->get_cancel_order_url().'">'.__('Cancel order &amp; restore cart', 'ognro').'</a>            
					</form>'; 
			}
    	
    function process_payment($order_id){
        $order = new WC_Order($order_id);
        return array('result' => 'success', 'redirect' => add_query_arg('order',           
		   $order->id, add_query_arg('key', $order->order_key, get_permalink(woocommerce_get_page_id('pay' ))))
        );
    }
 
   	 
  function check_CommercialIPG_response(){				
				global $wpdb;
				global $woocommerce;
				
				if(isset($_GET["vpc_TxnResponseCode"]) && isset($_GET["vpc_MerchTxnRef"])){			
					$order_id = $_GET["vpc_MerchTxnRef"];
					
					if($order_id != ''){				
						$order 	= new WC_Order($order_id);
						
						$amount = $_GET['vpc_Amount'];
						$status = $_GET["vpc_TxnResponseCode"];
						if($this->sucess_responce_code == $status){
								
							$table_name = $wpdb->prefix . 'commercial_ipg';	
							$wpdb->update( 
							$table_name, 
							array( 
								'transaction_id' 		=> $_GET["vpc_TransactionNo"],					
								'merchant_reference_no' => $_GET["vpc_MerchTxnRef"],					
								'message' 				=> $_GET["vpc_Message"],					
								'amount' 				=> $_GET["vpc_Amount"],					
								'status' 				=> $status,
								'order_info' 			=> $_GET["vpc_OrderInfo"],
								'receipt_no' 			=> $_GET["vpc_ReceiptNo"],
								'batch_no' 				=> $_GET["vpc_BatchNo"],
								'authorize_id' 			=> $_GET["vpc_AuthorizeId"],
								'card_type' 			=> $_GET["vpc_Card"],
								'aqr_response_code' 	=> $_GET["vpc_AcqResponseCode"]
							), 
							array( 'merchant_reference_no' => $order_id ));
											
							$order->add_order_note('Commercial payment successful<br/>Unnique Id from Commercial IPG: '.$_GET["vpc_TransactionNo"]);
							$order->add_order_note($this->msg['message']);
							$woocommerce->cart->empty_cart();
							
							$mailer = $woocommerce->mailer();

							$admin_email = get_option( 'admin_email', '' );

							$message = $mailer->wrap_message(__( 'Order confirmed','woocommerce'),sprintf(__('Order '.$_GET["vpc_TransactionNo"].' has been confirmed', 'woocommerce' ), $order->get_order_number(), $posted['reason_code']));	
							$mailer->send( $admin_email, sprintf( __( 'Payment for order %s confirmed', 'woocommerce' ), $order->get_order_number() ), $message );					
												
												
							$message = $mailer->wrap_message(__( 'Order confirmed','woocommerce'),sprintf(__('Order '.$_GET["vpc_TransactionNo"].' has been confirmed', 'woocommerce' ), $order->get_order_number(), $posted['reason_code']));	
							$mailer->send( $order->billing_email, sprintf( __( 'Payment for order %s confirmed', 'woocommerce' ), $order->get_order_number() ), $message );

							$order->payment_complete();
							wp_redirect( $this->responce_url_sucess, 200 ); exit;
							
						}else{					
							global $wpdb;
							
							$order->update_status('failed');
							$order->add_order_note('Failed - Code'.$_POST['pgErrorCode']);
							$order->add_order_note($this->msg['message']);
									
							$table_name = $wpdb->prefix . 'commercial_ipg';	
							$wpdb->update( 
							$table_name, 
							array( 
								'transaction_id' 		=> $_GET["vpc_TransactionNo"],					
								'merchant_reference_no' => $_GET["vpc_MerchTxnRef"],					
								'message' 				=> $_GET["vpc_Message"],					
								'amount' 				=> $_GET["vpc_Amount"],					
								'status' 				=> $status,
								'order_info' 			=> $_GET["vpc_OrderInfo"],
								'receipt_no' 			=> $_GET["vpc_ReceiptNo"],
								'batch_no' 				=> $_GET["vpc_BatchNo"],
								'authorize_id' 			=> $_GET["vpc_AuthorizeId"],
								'card_type' 			=> $_GET["vpc_Card"],
								'aqr_response_code' 	=> $_GET["vpc_AcqResponseCode"]
							), 
							array( 'merchant_reference_no' => $_GET["vpc_MerchTxnRef"] ));
							
							wp_redirect( $this->responce_url_fail, 200 ); exit;
						}				 
					}
					
				}
			}
    
  function get_pages($title = false, $indent = true) {
				$wp_pages = get_pages('sort_column=menu_order');
				$page_list = array();
				if ($title) $page_list[] = $title;
				foreach ($wp_pages as $page) {
					$prefix = '';            
					if ($indent) {
						$has_parent = $page->post_parent;
						while($has_parent) {
							$prefix .=  ' - ';
							$next_page = get_page($has_parent);
							$has_parent = $next_page->post_parent;
						}
					}            
					$page_list[$page->ID] = $prefix . $page->post_title;
				}
				return $page_list;
			}
}


if(isset($_GET["vpc_TxnResponseCode"]) && isset($_GET["vpc_MerchTxnRef"])){
		$WC = new WC_Commercial();
	}
	
	function woocommerce_add_commercial_gateway($methods) {
       $methods[] = 'WC_Commercial';
       return $methods;
	}
	
	add_filter('woocommerce_payment_gateways', 'woocommerce_add_commercial_gateway' );
}

	global $jal_db_version;
	$jal_db_version = '1.0';
	
	function jal_install_2() {	
		
		$plugin_path = plugin_dir_path( __FILE__ );
		$file = $plugin_path.'includes/auth.php';
		if(file_exists($file)){
			include 'includes/auth.php';
			$auth = new Auth();
			$auth->check_auth();
			if ( !$auth->get_status() ) {
				deactivate_plugins( plugin_basename( __FILE__ ) );
				if($auth->get_code() == 2){
					wp_die( "<h1>".ucfirst($auth->get_message())."</h1><br>Visit <a href='http://www.oganro.com/plugins/profile'>www.oganro.com/profile</a> and change the domain" ,"Activation Error","ltr" );
				}else{
					wp_die( "<h1>".ucfirst($auth->get_message())."</h1><br>Visit <a href='http://www.oganro.com/wordpress-plug-in-support'>www.oganro.com/wordpress-plug-in-support</a> for more info" ,"Activation Error","ltr" );
				}
			}
		}else{
			deactivate_plugins( plugin_basename( __FILE__ ) );
			$path =  plugin_basename( __FILE__ );
			$dir  = explode("/", $path);
			wp_die( "<h1>Buy serial key to activate this plugin</h1><br><a href='http://www.oganro.com/wordpress-plug-in-support'><img src=".site_url('wp-content/plugins/'.$dir[0].'/support.jpg')." style='width:700px;height:auto;' /></a><p>Visit <a href='http://www.oganro.com/plugins'>www.oganro.com/plugins</a> to buy this plugin<p>" ,"Activation Error","ltr" );
		}
		global $wpdb;
		global $jal_db_version;
	
		$table_name = $wpdb->prefix . 'commercial_ipg';
		$charset_collate = '';
	
		if ( ! empty( $wpdb->charset ) ) {
		  $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
		}
	
		if ( ! empty( $wpdb->collate ) ) {
		  $charset_collate .= " COLLATE {$wpdb->collate}";
		}
	
		$sql = "CREATE TABLE $table_name (
					id int(9) NOT NULL AUTO_INCREMENT,
					transaction_id int(20) NOT NULL,
					merchant_reference_no VARCHAR(40) NOT NULL,
					message text NOT NULL,
					amount VARCHAR(10) NOT NULL,
					status int(6) NOT NULL,
					or_date DATE NOT NULL,
					order_info VARCHAR(35) NOT NULL,
					receipt_no VARCHAR(20) NOT NULL,
					batch_no int(10) NOT NULL,
					authorize_id VARCHAR(10) NOT NULL,
					card_type VARCHAR(10) NOT NULL,
					aqr_response_code VARCHAR(10) NOT NULL,			
					UNIQUE KEY id (id)
				) $charset_collate;";
				
	
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	
		add_option( 'jal_db_version', $jal_db_version );
	}
	
	function jal_install_data_2() {
		global $wpdb;
		
		$welcome_name = 'Commercial IPG';
		$welcome_text = 'Congratulations, you just completed the installation!';
		
		$table_name = $wpdb->prefix . 'commercial_ipg';
		
		$wpdb->insert( 
			$table_name, 
			array( 
				'time' => current_time( 'mysql' ), 
				'name' => $welcome_name, 
				'text' => $welcome_text, 
			) 
		);
	}
	
	register_activation_hook( __FILE__, 'jal_install_2' );
	register_activation_hook( __FILE__, 'jal_install_data_2' );