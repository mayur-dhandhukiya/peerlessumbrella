<?php 

if ( ! defined( 'WPINC' ) ) {
    die;
}

if ( !class_exists( 'Addify_Registration_Fields_Addon_Front' ) ) {

	class Addify_Registration_Fields_Addon_Front extends Addify_Registration_Fields_Addon {

		public function __construct() {

			add_action( 'wp_loaded', array( $this, 'afreg_front_scripts' ) );
			add_action( 'woocommerce_register_form', array($this, 'afreg_extra_fields_show' ));
			add_action( 'woocommerce_register_post', array($this, 'afreg_validate_extra_register_fields'), 10, 3 );
			add_action( 'user_register', array( $this, 'afreg_save_extra_fields' ) );
			add_action( 'woocommerce_edit_account_form', array($this, 'afreg_update_extra_fields_my_account' ));
			add_action( 'woocommerce_save_account_details_errors', array($this, 'afreg_validate_update_role_my_account'), 10, 1 );
			add_action( 'woocommerce_save_account_details', array($this, 'afreg_save_update_role_my_account'), 12, 1 );
			add_action( 'woocommerce_checkout_fields', array($this, 'afreg_checkout_account_extra_fields' ), 10, 1);
			add_filter( 'woocommerce_form_field_multiselect', array($this, 'afreg_custom_multiselect_handler'), 10, 4 );

			//For wordpress
			add_filter('register_form', array($this, 'afreg_extra_fields_show_wordpress'));
			add_filter( 'registration_errors', array($this, 'aferg_wordpress_registration_errors'), 10, 3 );
			add_action('user_register', array($this, 'afreg_save_extra_fields'));
		}

		public function afreg_front_scripts() {

			wp_enqueue_style( 'afreg-front-css', plugins_url( '/css/afreg_front.css', __FILE__ ), false );
			wp_enqueue_style( 'color-spectrum-css', plugins_url( '/css/afreg_color_spectrum.css', __FILE__ ), false );
			wp_enqueue_script('jquery');
			wp_enqueue_script( 'afreg-front-js', plugins_url( '/js/afreg_front.js', __FILE__ ), false );
			wp_enqueue_script( 'color-spectrum-js', plugins_url( '/js/afreg_color_spectrum.js', __FILE__ ), false );
			wp_enqueue_script( 'Google reCaptcha JS', '//www.google.com/recaptcha/api.js', false );
        	
		}

		public function afreg_extra_fields_show() { ?>

			<div class="afreg_extra_fields">
				<h3><?php echo esc_html__(get_option('afreg_additional_fields_section_title'), 'addify_reg'); ?></h3>
				<?php

					$afreg_args = array( 
						'posts_per_page' => -1,
						'post_type' => 'afreg_fields',
						'post_status' => 'publish',
						'orderby' => 'menu_order',
						'order' => 'ASC'
					);

					$afreg_extra_fields = get_posts($afreg_args);
					if(!empty($afreg_extra_fields)) {

						foreach($afreg_extra_fields as $afreg_field) {

							$afreg_field_type = get_post_meta( intval($afreg_field->ID), 'afreg_field_type', true );
	    					$afreg_field_options = unserialize(get_post_meta( intval($afreg_field->ID), 'afreg_field_option', true )); 
							$afreg_field_required = get_post_meta( intval($afreg_field->ID), 'afreg_field_required', true );
					    	$afreg_field_width = get_post_meta( intval($afreg_field->ID), 'afreg_field_width', true );
					    	$afreg_field_placeholder = get_post_meta( intval($afreg_field->ID), 'afreg_field_placeholder', true );
					    	$afreg_field_description = get_post_meta( intval($afreg_field->ID), 'afreg_field_description', true );
					    	$afreg_field_css = get_post_meta( intval($afreg_field->ID), 'afreg_field_css', true );

					    	if(!empty($afreg_field_width) && $afreg_field_width == 'full') {

					    		$afreg_main_class = 'form-row-wide';

					    	} else if(!empty($afreg_field_width) && $afreg_field_width == 'half') {

					    		$afreg_main_class = 'half_width';
					    	}

					    	if($afreg_field_type == 'text') { ?>

								<p class="form-row <?php echo esc_attr($afreg_main_class); ?>">
									<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>
									<input type="text" class="input-text <?php if(!empty($afreg_field_css)) echo esc_attr($afreg_field_css); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]); ?>" placeholder="<?php if(!empty($afreg_field_placeholder)) echo esc_html__($afreg_field_placeholder , 'addify_reg' );  ?>" />
									<?php if(!empty($afreg_field_description)) { ?>
										<span class="afreg_field_message"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
									<?php } ?>
								</p>

					    	<?php } else if($afreg_field_type == 'textarea') { ?>

					    		<p class="form-row <?php echo esc_attr($afreg_main_class); ?>">
									<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>
									<textarea class="input-text <?php if(!empty($afreg_field_css)) echo esc_attr($afreg_field_css); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]); ?></textarea>
									<?php if(!empty($afreg_field_description)) { ?>
										<span class="afreg_field_message"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
									<?php } ?>
								</p>

					    	<?php } else if($afreg_field_type == 'email') { ?>

					    		<p class="form-row <?php echo esc_attr($afreg_main_class); ?>">
									<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>
									<input type="text" class="input-text <?php if(!empty($afreg_field_css)) echo esc_attr($afreg_field_css); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]); ?>" placeholder="<?php if(!empty($afreg_field_placeholder)) echo esc_html__($afreg_field_placeholder , 'addify_reg' );  ?>" />
									<?php if(!empty($afreg_field_description)) { ?>
										<span class="afreg_field_message"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
									<?php } ?>
								</p>

					    	<?php } else if($afreg_field_type == 'select') { ?>

					    		<p class="form-row <?php echo esc_attr($afreg_main_class); ?>">
									<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>
									<select class="input-select <?php if(!empty($afreg_field_css)) echo esc_attr($afreg_field_css); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
										<?php foreach($afreg_field_options as $afreg_field_option) { ?>
											<option value="<?php echo esc_attr($afreg_field_option['field_value']); ?>" <?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo selected(esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]), esc_attr($afreg_field_option['field_value'])); ?>>
												<?php if(!empty($afreg_field_option['field_text'])) echo esc_html__(esc_attr($afreg_field_option['field_text']), 'addify_reg'); ?>
											</option>
										<?php } ?>
									</select>
									<?php if(!empty($afreg_field_description)) { ?>
										<span class="afreg_field_message"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
									<?php } ?>
								</p>

					    	<?php } else if($afreg_field_type == 'multiselect') { ?>

					    		<p class="form-row <?php echo esc_attr($afreg_main_class); ?>">
									<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>
									<select class="input-select <?php if(!empty($afreg_field_css)) echo esc_attr($afreg_field_css); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>[]" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" multiple>
										<?php foreach($afreg_field_options as $afreg_field_option) {

											if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) { ?>
												<option value="<?php echo esc_attr($afreg_field_option['field_value']); ?>" <?php if(in_array(esc_attr($afreg_field_option['field_value']), $_POST['afreg_additional_'.intval($afreg_field->ID)])) echo "selected"; ?>>
													<?php echo esc_html__(esc_attr($afreg_field_option['field_text']), 'addify_reg'); ?>
												</option>
											<?php } else { ?>
											<option value="<?php echo esc_attr($afreg_field_option['field_value']); ?>">
												<?php echo esc_html__(esc_attr($afreg_field_option['field_text']), 'addify_reg'); ?>
											</option>
										<?php } } ?>
									</select>
									<?php if(!empty($afreg_field_description)) { ?>
										<span class="afreg_field_message"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
									<?php } ?>
								</p>

					    	<?php } else if($afreg_field_type == 'checkbox') { ?> 

					    		<p class="form-row <?php echo esc_attr($afreg_main_class); ?>">
									<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>
									<input type="checkbox" class="input-checkbox <?php if(!empty($afreg_field_css)) echo esc_attr($afreg_field_css); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" <?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo checked(esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]), 'on'); ?>  />
									
									<?php if(!empty($afreg_field_description)) { ?>
										<span class="afreg_field_message"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
									<?php } ?>
								</p>

					    	<?php } else if($afreg_field_type == 'radio') { ?>

					    		<p class="form-row <?php echo esc_attr($afreg_main_class); ?>">
									<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>
									
									<?php foreach($afreg_field_options as $afreg_field_option) { ?>
										<input type="radio" class="input-radio <?php if(!empty($afreg_field_css)) echo esc_attr($afreg_field_css); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php echo esc_attr($afreg_field_option['field_value']); ?>" <?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo checked(esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]), esc_attr($afreg_field_option['field_value'])); ?>  />
										<span class="afreg_radio"><?php if(!empty($afreg_field_option['field_text'])) echo esc_html__(esc_attr($afreg_field_option['field_text']), 'addify_reg'); ?></span>
									<?php } ?>
									
									<?php if(!empty($afreg_field_description)) { ?>
										<span class="afreg_field_message_radio"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
									<?php } ?>
								</p>

					    	<?php } else if($afreg_field_type == 'number') { ?>

					    		<p class="form-row <?php echo esc_attr($afreg_main_class); ?>">
									<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>
									<input type="number" class="input-text <?php if(!empty($afreg_field_css)) echo esc_attr($afreg_field_css); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]); ?>" placeholder="<?php if(!empty($afreg_field_placeholder)) echo esc_html__($afreg_field_placeholder , 'addify_reg' );  ?>" />
									<?php if(!empty($afreg_field_description)) { ?>
										<span class="afreg_field_message"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
									<?php } ?>
								</p>

					    	<?php } else if($afreg_field_type == 'password') { ?>

					    		<p class="form-row <?php echo esc_attr($afreg_main_class); ?>">
									<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>
									<input type="password" class="input-text <?php if(!empty($afreg_field_css)) echo esc_attr($afreg_field_css); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]); ?>" placeholder="<?php if(!empty($afreg_field_placeholder)) echo esc_html__($afreg_field_placeholder , 'addify_reg' );  ?>" />
									<?php if(!empty($afreg_field_description)) { ?>
										<span class="afreg_field_message"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
									<?php } ?>
								</p>

					    	<?php } else if($afreg_field_type == 'fileupload') { ?>

					    		<p class="form-row <?php echo esc_attr($afreg_main_class); ?>">
									<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>
									<input type="file" class="input-text <?php if(!empty($afreg_field_css)) echo esc_attr($afreg_field_css); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php if(!empty($_FILES['afreg_additional_'.intval($afreg_field->ID)])) echo esc_attr( $_FILES['afreg_additional_'.intval($afreg_field->ID)]); ?>" placeholder="<?php if(!empty($afreg_field_placeholder)) echo esc_html__($afreg_field_placeholder , 'addify_reg' );  ?>" />
									<?php if(!empty($afreg_field_description)) { ?>
										<span class="afreg_field_message"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
									<?php } ?>
								</p>

					    	<?php } else if($afreg_field_type == 'color') { ?>

					    		<p class="form-row <?php echo esc_attr($afreg_main_class); ?>">
									<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>
									<input type="color" class="input-text color_sepctrum <?php if(!empty($afreg_field_css)) echo esc_attr($afreg_field_css); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]); ?>" placeholder="<?php if(!empty($afreg_field_placeholder)) echo esc_html__($afreg_field_placeholder , 'addify_reg' );  ?>" />
									<?php if(!empty($afreg_field_description)) { ?>
										<span class="afreg_field_message_radio"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
									<?php } ?>
								</p>

					    	<?php } else if($afreg_field_type == 'datepicker') { ?>

					    		<p class="form-row <?php echo esc_attr($afreg_main_class); ?>">
									<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>
									<input type="date" class="input-text  <?php if(!empty($afreg_field_css)) echo esc_attr($afreg_field_css); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]); ?>" placeholder="<?php if(!empty($afreg_field_placeholder)) echo esc_html__($afreg_field_placeholder , 'addify_reg' );  ?>" />
									<?php if(!empty($afreg_field_description)) { ?>
										<span class="afreg_field_message_radio"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
									<?php } ?>
								</p>

					    	<?php } else if($afreg_field_type == 'timepicker') { ?>

					    		<p class="form-row <?php echo esc_attr($afreg_main_class); ?>">
									<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>
									<input type="time" class="input-text  <?php if(!empty($afreg_field_css)) echo esc_attr($afreg_field_css); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]); ?>" placeholder="<?php if(!empty($afreg_field_placeholder)) echo esc_html__($afreg_field_placeholder , 'addify_reg' );  ?>" />
									<?php if(!empty($afreg_field_description)) { ?>
										<span class="afreg_field_message_radio"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
									<?php } ?>
								</p>

					    	<?php } else if($afreg_field_type == 'googlecaptcha') { ?>

					    		<p class="form-row <?php echo esc_attr($afreg_main_class); ?>">
									<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required">*</span></label>
									
									<div class="g-recaptcha" data-sitekey="<?php echo get_option('afreg_site_key'); ?>"></div>

									<?php if(!empty($afreg_field_description)) { ?>
										<span class="afreg_field_message_radio"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
									<?php } ?>
								</p>

					    	<?php } ?>



					    	<?php 
						}
					}


				?>
			</div>

		<?php }

		public function afreg_validate_extra_register_fields( $username, $email, $validation_errors ) {

			$afreg_args = array( 
				'posts_per_page' => -1,
				'post_type' => 'afreg_fields',
				'post_status' => 'publish',
				'orderby' => 'menu_order',
				'order' => 'ASC'
			);

			$afreg_extra_fields = get_posts($afreg_args);
			if(!empty($afreg_extra_fields)) {

				foreach($afreg_extra_fields as $afreg_field) {

					$afreg_field_required = get_post_meta( intval($afreg_field->ID), 'afreg_field_required', true );
					$afreg_field_type = get_post_meta( intval($afreg_field->ID), 'afreg_field_type', true );
					$afreg_field_file_type = get_post_meta( intval($afreg_field->ID), 'afreg_field_file_type', true );
					$afreg_field_file_size = get_post_meta( intval($afreg_field->ID), 'afreg_field_file_size', true );

					if ( isset( $_POST['afreg_additional_'.intval($afreg_field->ID)] ) && empty( $_POST['afreg_additional_'.intval($afreg_field->ID)] ) && ($afreg_field_required == 'on') ) {

						$validation_errors->add( 'afreg_additional_'.intval($afreg_field->ID).'_error', esc_html__( $afreg_field->post_title.' is required!', 'addify_reg' ) );
					}

					if($afreg_field_type == 'email') {

						if ( isset( $_POST['afreg_additional_'.intval($afreg_field->ID)] ) && !empty( $_POST['afreg_additional_'.intval($afreg_field->ID)] ) && ($afreg_field_required == 'on') && !filter_var($_POST['afreg_additional_'.intval($afreg_field->ID)], FILTER_VALIDATE_EMAIL) ) {

							$validation_errors->add( 'afreg_additional_'.intval($afreg_field->ID).'_error', esc_html__( $afreg_field->post_title.' is not a valid email address!', 'addify_reg' ) );
						}

					}

					if($afreg_field_type == 'multiselect') {
        			
	        			if(!is_array($_POST['afreg_additional_'.intval($afreg_field->ID)]) && $afreg_field_required == 'on') {
		        			
		        			$validation_errors->add( 'afreg_additional_'.intval($afreg_field->ID).'_error', esc_html__( $afreg_field->post_title.' is required!', 'addify_reg' ) );
		        			
		        		}
	        		}

	        		if($afreg_field_type == 'number') {

						if ( isset( $_POST['afreg_additional_'.intval($afreg_field->ID)] ) && !empty( $_POST['afreg_additional_'.intval($afreg_field->ID)] ) && ($afreg_field_required == 'on') && !filter_var($_POST['afreg_additional_'.intval($afreg_field->ID)], FILTER_VALIDATE_INT) ) {

							$validation_errors->add( 'afreg_additional_'.intval($afreg_field->ID).'_error', esc_html__( $afreg_field->post_title.' is not a valid number!', 'addify_reg' ) );
						}

					}

					if($afreg_field_type == 'checkbox' || $afreg_field_type == 'radio') { 

						if ( !isset( $_POST['afreg_additional_'.intval($afreg_field->ID)] ) && ($afreg_field_required == 'on') ) {

							$validation_errors->add( 'afreg_additional_'.intval($afreg_field->ID).'_error', esc_html__( $afreg_field->post_title.' is required!', 'addify_reg' ) );
						}

					}


					if($afreg_field_type == 'googlecaptcha') { 
        			
	        			if(isset($_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response'] != '') {
	        				$ccheck = $this->captcha_check($_POST['g-recaptcha-response']);
	        				if($ccheck == 'error') {
	        					$validation_errors->add( 'afreg_additional_'.intval($afreg_field->ID).'_error', esc_html__( 'Invalid reCaptcha!', 'addify_reg' ) );
	        				}
	        			} else {
	        				$validation_errors->add( 'afreg_additional_'.intval($afreg_field->ID).'_error', esc_html__( $afreg_field->post_title.' is required!', 'addify_reg' ) );
	        			}
	        		}

					if($afreg_field_type == 'fileupload') {

						if(isset($_FILES['afreg_additional_'.intval($afreg_field->ID)]['name']) && empty($_FILES['afreg_additional_'.intval($afreg_field->ID)]['name']) && $afreg_field_required == 'on') {

							$validation_errors->add( 'afreg_additional_'.intval($afreg_field->ID).'_error', esc_html__( $afreg_field->post_title.' is required!', 'addify_reg' ) );

						}

						if(isset($_FILES['afreg_additional_'.intval($afreg_field->ID)]['name']) && !empty($_FILES['afreg_additional_'.intval($afreg_field->ID)]['name']) && $afreg_field_required == 'on') {

							$afreg_allowed_types =  explode(',', $afreg_field_file_type);
							$afreg_filename = $_FILES['afreg_additional_'.intval($afreg_field->ID)]['name'];
							$afreg_ext = pathinfo($afreg_filename, PATHINFO_EXTENSION);

							if(!in_array($afreg_ext,$afreg_allowed_types) ) {

								$validation_errors->add( 'afreg_additional_'.intval($afreg_field->ID).'_error', esc_html__( $afreg_field->post_title.': File type is not allowed!', 'addify_reg' ) );
							}

							$afreg_filesize = $_FILES['afreg_additional_'.intval($afreg_field->ID)]['size'];
							$afreg_allowed_size = $afreg_field_file_size * 1000000; // convert from MB to Bytes

							if($afreg_filesize > $afreg_allowed_size) {

								$validation_errors->add( 'afreg_additional_'.intval($afreg_field->ID).'_error', esc_html__( $afreg_field->post_title.': File size is too big!', 'addify_reg' ) );

							}
						}
					}
				}
			}

			return $validation_errors;
		}

		public function afreg_save_extra_fields($customer_id) {

			$afreg_args = array( 
				'posts_per_page' => -1,
				'post_type' => 'afreg_fields',
				'post_status' => 'publish',
				'orderby' => 'menu_order',
				'order' => 'ASC'
			);

			$afreg_extra_fields = get_posts($afreg_args);
			if(!empty($afreg_extra_fields)) {

				foreach($afreg_extra_fields as $afreg_field) {

					$afreg_field_type = get_post_meta( intval($afreg_field->ID), 'afreg_field_type', true );

					if ( isset( $_POST['afreg_additional_'.intval($afreg_field->ID)] ) || isset( $_FILES['afreg_additional_'.intval($afreg_field->ID)] ) ) {

						if($afreg_field_type == 'fileupload') {

							if($_FILES['afreg_additional_'.intval($afreg_field->ID)]['name']!='') { 

								$file = time('m').$_FILES['afreg_additional_'.intval($afreg_field->ID)]['name'];
								$target_path = AFREG_PLUGIN_DIR.'uploaded_files/';
								$target_path = $target_path . $file;
								$temp = move_uploaded_file($_FILES['afreg_additional_'.intval($afreg_field->ID)]['tmp_name'], $target_path);
								update_user_meta($customer_id,'afreg_additional_'.intval($afreg_field->ID),$file);

							}

						} else if($afreg_field_type == 'multiselect') { 
		        			$prefix = '';
		        			$multival = '';
		        			foreach ($_POST['afreg_additional_'.intval($afreg_field->ID)] as $value) {
		        				$multival .= $prefix.$value;
	    						$prefix = ', ';
		        			}
		        			update_user_meta( $customer_id, 'afreg_additional_'.intval($afreg_field->ID), esc_attr($multival) );

		        		} else {

							update_user_meta( $customer_id, 'afreg_additional_'.intval($afreg_field->ID),  esc_attr($_POST['afreg_additional_'.intval($afreg_field->ID)]));
						}

					}
				}

				$user = new WP_User($customer_id);
 
		        $user_login = stripslashes($user->user_login);
		        $user_email = stripslashes($user->user_email);

	        	$message  = sprintf(esc_html__('New user registration on your website %s: '), get_option('blogname')) . "\n\n";
		        
		        $message .= sprintf(esc_html__('Username: %s'), $user_login) . "\n\n";
		        $message .= sprintf(esc_html__('E-mail: %s'), $user_email) . "\n\n";

		        foreach($afreg_extra_fields as $afreg_field) {

					$afreg_field_type = get_post_meta( intval($afreg_field->ID), 'afreg_field_type', true );
					$afregcheck = get_user_meta( $customer_id, 'afreg_additional_'.intval($afreg_field->ID), true );

					if(!empty($afregcheck)) {

						$value = get_user_meta( $customer_id, 'afreg_additional_'.intval($afreg_field->ID), true );
						if($afreg_field_type =='checkbox') {
							if($value == 'on') {
								$message .= sprintf(esc_html__($afreg_field->post_title.': %s'), 'Yes') . "\n\n";
							} else {
								$message .= sprintf(esc_html__($afreg_field->post_title.': %s'), 'No') . "\n\n";
							}
							
						} else if($afreg_field_type =='fileupload') {

							$value = AFREG_URL.'uploaded_files/'.$value;
							$message .= sprintf(esc_html__($afreg_field->post_title.': %s'), $value) . "\n\n";

						} else {

							$message .= sprintf(esc_html__($afreg_field->post_title.': %s'), $value) . "\n\n";
						}

					}
				}

				wp_mail(get_option('admin_email'), sprintf(esc_html__('[%s] New User Registration'), get_option('blogname')), $message);
			}

		}

		public function afreg_update_extra_fields_my_account() {

			$user = wp_get_current_user();
			?>
			<div class="afreg_extra_fields">
				<h3><?php echo esc_html__(get_option('afreg_additional_fields_section_title'), 'addify_reg'); ?></h3>
				<fieldset>
				<?php
				$afreg_args = array( 
					'posts_per_page' => -1,
					'post_type' => 'afreg_fields',
					'post_status' => 'publish',
					'orderby' => 'menu_order',
					'order' => 'ASC'
				);

				$afreg_extra_fields = get_posts($afreg_args);
				if(!empty($afreg_extra_fields)) {

					foreach($afreg_extra_fields as $afreg_field) {

						$afreg_field_type = get_post_meta( intval($afreg_field->ID), 'afreg_field_type', true );
    					$afreg_field_options = unserialize(get_post_meta( intval($afreg_field->ID), 'afreg_field_option', true )); 
						$afreg_field_required = get_post_meta( intval($afreg_field->ID), 'afreg_field_required', true );
				    	$afreg_field_width = get_post_meta( intval($afreg_field->ID), 'afreg_field_width', true );
				    	$afreg_field_placeholder = get_post_meta( intval($afreg_field->ID), 'afreg_field_placeholder', true );
				    	$afreg_field_description = get_post_meta( intval($afreg_field->ID), 'afreg_field_description', true );
				    	$afreg_field_css = get_post_meta( intval($afreg_field->ID), 'afreg_field_css', true );
				    	$afreg_field_read_only = get_post_meta( $afreg_field->ID, 'afreg_field_read_only', true );

				    	if(!empty($afreg_field_width) && $afreg_field_width == 'full') {

				    		$afreg_main_class = 'form-row-wide';

				    	} else if(!empty($afreg_field_width) && $afreg_field_width == 'half') {

				    		$afreg_main_class = 'half_width';
				    	}

				    	$value = get_user_meta( intval($user->ID), 'afreg_additional_'.intval($afreg_field->ID), true );

				    	if($afreg_field_type == 'text') { ?>

							<p class="form-row <?php echo esc_attr($afreg_main_class); ?>">
								<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>

								<?php if($afreg_field_read_only == 'on') { 
									echo esc_attr($value);
								 } else { ?>

								<input type="text" class="input-text <?php if(!empty($afreg_field_css)) echo esc_attr($afreg_field_css); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]); else echo esc_attr($value); ?>" placeholder="<?php if(!empty($afreg_field_placeholder)) echo esc_html__($afreg_field_placeholder , 'addify_reg' );  ?>" />
								<?php if(!empty($afreg_field_description)) { ?>
									<span class="afreg_field_message"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
								<?php } } ?>
							</p>

				    	<?php } else if($afreg_field_type == 'textarea') { ?>

				    		<p class="form-row <?php echo esc_attr($afreg_main_class); ?>">
								<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>
								<?php if($afreg_field_read_only == 'on') { 
									echo esc_attr($value);
								 } else { ?>

								<textarea class="input-text <?php if(!empty($afreg_field_css)) echo esc_attr($afreg_field_css); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]); else echo esc_attr($value); ?></textarea>
								<?php if(!empty($afreg_field_description)) { ?>
									<span class="afreg_field_message"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
								<?php } } ?>
							</p>

				    	<?php } else if($afreg_field_type == 'email') { ?>

				    		<p class="form-row <?php echo esc_attr($afreg_main_class); ?>">
								<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>

								<?php if($afreg_field_read_only == 'on') { 
									echo esc_attr($value);
								 } else { ?>

								<input type="text" class="input-text <?php if(!empty($afreg_field_css)) echo esc_attr($afreg_field_css); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]); else echo esc_attr($value); ?>" placeholder="<?php if(!empty($afreg_field_placeholder)) echo esc_html__($afreg_field_placeholder , 'addify_reg' );  ?>" />
								<?php if(!empty($afreg_field_description)) { ?>
									<span class="afreg_field_message"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
								<?php } } ?>
							</p>

				    	<?php } else if($afreg_field_type == 'select') { ?>

				    		<p class="form-row <?php echo esc_attr($afreg_main_class); ?>">
								<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>

								<?php if($afreg_field_read_only == 'on') { 
									echo esc_attr($value);
								 } else { ?>

								<select class="input-select <?php if(!empty($afreg_field_css)) echo esc_attr($afreg_field_css); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
									<?php foreach($afreg_field_options as $afreg_field_option) { ?>
										<option value="<?php echo esc_attr($afreg_field_option['field_value']); ?>" <?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo selected(esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]), esc_attr($afreg_field_option['field_value'])); else echo selected(esc_attr($value), esc_attr($afreg_field_option['field_value'])); ?>>
											<?php if(!empty($afreg_field_option['field_text'])) echo esc_html__(esc_attr($afreg_field_option['field_text']), 'addify_reg'); ?>
										</option>
									<?php } ?>
								</select>
								<?php if(!empty($afreg_field_description)) { ?>
									<span class="afreg_field_message"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
								<?php } } ?>
							</p>

				    	<?php } else if($afreg_field_type == 'multiselect') { ?>

				    		<p class="form-row <?php echo esc_attr($afreg_main_class); ?>">
								<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>

								<?php if($afreg_field_read_only == 'on') { 
									echo esc_attr($value);
								 } else { ?>

								<select class="input-select <?php if(!empty($afreg_field_css)) echo esc_attr($afreg_field_css); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>[]" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" multiple>
									<?php foreach($afreg_field_options as $afreg_field_option) {

										$db_values = explode(', ', $value);

										if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) { ?>
											<option value="<?php echo esc_attr($afreg_field_option['field_value']); ?>" <?php if(in_array(esc_attr($afreg_field_option['field_value']), $_POST['afreg_additional_'.intval($afreg_field->ID)])) echo "selected"; ?>>
												<?php echo esc_html__(esc_attr($afreg_field_option['field_text']), 'addify_reg'); ?>
											</option>
										<?php } else if(!empty($db_values)) { ?>
											<option value="<?php echo esc_attr($afreg_field_option['field_value']); ?>" <?php if(in_array(esc_attr($afreg_field_option['field_value']),  $db_values)) echo "selected"; ?>>
												<?php echo esc_html__(esc_attr($afreg_field_option['field_text']), 'addify_reg'); ?>
										<?php } else { ?>
										<option value="<?php echo esc_attr($afreg_field_option['field_value']); ?>">
											<?php echo esc_html__(esc_attr($afreg_field_option['field_text']), 'addify_reg'); ?>
										</option>
									<?php } } ?>
								</select>
								<?php if(!empty($afreg_field_description)) { ?>
									<span class="afreg_field_message"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
								<?php } } ?>
							</p>

				    	<?php } else if($afreg_field_type == 'checkbox') { ?> 

				    		<p class="form-row <?php echo esc_attr($afreg_main_class); ?>">
								<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>

								<?php if($afreg_field_read_only == 'on') { 
									echo esc_attr($value);
								 } else { ?>

								<input type="checkbox" class="input-checkbox <?php if(!empty($afreg_field_css)) echo esc_attr($afreg_field_css); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" <?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo checked(esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]), 'on'); else echo checked(esc_attr($value), 'on'); ?>  />
								
								<?php if(!empty($afreg_field_description)) { ?>
									<span class="afreg_field_message"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
								<?php } } ?>
							</p>

				    	<?php } else if($afreg_field_type == 'radio') { ?>

				    		<p class="form-row <?php echo esc_attr($afreg_main_class); ?>">
								<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>

								<?php if($afreg_field_read_only == 'on') { 
									echo esc_attr($value);
								 } else { ?>
								
								<?php foreach($afreg_field_options as $afreg_field_option) { ?>
									<input type="radio" class="input-radio <?php if(!empty($afreg_field_css)) echo esc_attr($afreg_field_css); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php echo esc_attr($afreg_field_option['field_value']); ?>" <?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo checked(esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]), esc_attr($afreg_field_option['field_value'])); else echo checked(esc_attr($value), esc_attr($afreg_field_option['field_value'])); ?>  />
									<span class="afreg_radio"><?php if(!empty($afreg_field_option['field_text'])) echo esc_html__(esc_attr($afreg_field_option['field_text']), 'addify_reg'); ?></span>
								<?php } ?>
								
								<?php if(!empty($afreg_field_description)) { ?>
									<span class="afreg_field_message_radio"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
								<?php } } ?>
							</p>

				    	<?php } else if($afreg_field_type == 'number') { ?>

				    		<p class="form-row <?php echo esc_attr($afreg_main_class); ?>">
								<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>

								<?php if($afreg_field_read_only == 'on') { 
									echo esc_attr($value);
								 } else { ?>

								<input type="number" class="input-text <?php if(!empty($afreg_field_css)) echo esc_attr($afreg_field_css); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]); else echo esc_attr($value); ?>" placeholder="<?php if(!empty($afreg_field_placeholder)) echo esc_html__($afreg_field_placeholder , 'addify_reg' );  ?>" />
								<?php if(!empty($afreg_field_description)) { ?>
									<span class="afreg_field_message"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
								<?php } } ?>
							</p>

				    	<?php } else if($afreg_field_type == 'password') { ?>

				    		<p class="form-row <?php echo esc_attr($afreg_main_class); ?>">
								<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>

								<?php if($afreg_field_read_only == 'on') { 
									echo esc_attr($value);
								 } else { ?>

								<input type="password" class="input-text <?php if(!empty($afreg_field_css)) echo esc_attr($afreg_field_css); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]); else echo esc_attr($value); ?>" placeholder="<?php if(!empty($afreg_field_placeholder)) echo esc_html__($afreg_field_placeholder , 'addify_reg' );  ?>" />
								<?php if(!empty($afreg_field_description)) { ?>
									<span class="afreg_field_message"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
								<?php } } ?>
							</p>

				    	<?php } else if($afreg_field_type == 'fileupload') { ?>

				    		<p class="form-row <?php echo esc_attr($afreg_main_class); ?>">
				    			<label for="<?php echo esc_attr($field->field_name); ?>"><?php echo esc_html__('Current', 'addify_reg') ?> <?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?></label>

				    			<?php 
					
								$curr_image = AFREG_URL.'uploaded_files/'.$value;
								$ext = pathinfo($curr_image, PATHINFO_EXTENSION);
								if($ext == 'pdf' || $ext == 'PDF') { ?>
									<a href="<?php echo AFREG_URL; ?>uploaded_files/<?php echo esc_attr($value); ?>" target="_blank">
										<img src="<?php echo AFREG_URL; ?>images/pdf.png" width="150" height="150" title="Click to View" />
									</a>
								<?php } else { ?>
									<img src="<?php echo AFREG_URL; ?>uploaded_files/<?php echo esc_attr($value); ?>" width="150" height="150" />
								<?php } ?>

								<?php if($afreg_field_read_only == 'on') { 
									
								 } else { ?>

								<input type="hidden"  value="<?php echo esc_attr($value); ?>" id="curr_afreg_additional_<?php echo intval($afreg_field->ID); ?>" name="curr_afreg_additional_<?php echo intval($afreg_field->ID); ?>">

								<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>
								<input type="file" class="input-text <?php if(!empty($afreg_field_css)) echo esc_attr($afreg_field_css); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php if(!empty($_FILES['afreg_additional_'.intval($afreg_field->ID)])) echo esc_attr( $_FILES['afreg_additional_'.intval($afreg_field->ID)]); ?>" placeholder="<?php if(!empty($afreg_field_placeholder)) echo esc_html__($afreg_field_placeholder , 'addify_reg' );  ?>" />
								<?php if(!empty($afreg_field_description)) { ?>
									<span class="afreg_field_message"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
								<?php } } ?>
							</p>

				    	<?php } else if($afreg_field_type == 'color') { ?>
				    		<p class="form-row <?php echo esc_attr($afreg_main_class); ?>">
								<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>

								<?php if($afreg_field_read_only == 'on') { 
									echo esc_attr($value);
								 } else { ?>

								<input type="color" class="input-text color_sepctrumm <?php if(!empty($afreg_field_css)) echo esc_attr($afreg_field_css); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php echo $value; ?>" placeholder="<?php if(!empty($afreg_field_placeholder)) echo esc_html__($afreg_field_placeholder , 'addify_reg' );  ?>" />
								<?php if(!empty($afreg_field_description)) { ?>
									<span class="afreg_field_message_radio"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
								<?php } } ?>


								<script>
						
								jQuery(".color_sepctrumm").spectrum({
								    color: "<?php echo $value; ?>",
								    preferredFormat: "hex",
								});

								</script>
							</p>

				    	<?php } else if($afreg_field_type == 'datepicker') { ?>

				    		<p class="form-row <?php echo esc_attr($afreg_main_class); ?>">
								<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>

								<?php if($afreg_field_read_only == 'on') { 
									echo esc_attr($value);
								 } else { ?>

								<input type="date" class="input-text  <?php if(!empty($afreg_field_css)) echo esc_attr($afreg_field_css); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]); else echo esc_attr($value);?>" placeholder="<?php if(!empty($afreg_field_placeholder)) echo esc_html__($afreg_field_placeholder , 'addify_reg' );  ?>" />
								<?php if(!empty($afreg_field_description)) { ?>
									<span class="afreg_field_message_radio"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
								<?php } } ?>
							</p>

				    	<?php } else if($afreg_field_type == 'timepicker') { ?>

				    		<p class="form-row <?php echo esc_attr($afreg_main_class); ?>">
								<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>

								<?php if($afreg_field_read_only == 'on') { 
									echo esc_attr($value);
								 } else { ?>

								<input type="time" class="input-text  <?php if(!empty($afreg_field_css)) echo esc_attr($afreg_field_css); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]); else echo esc_attr($value); ?>" placeholder="<?php if(!empty($afreg_field_placeholder)) echo esc_html__($afreg_field_placeholder , 'addify_reg' );  ?>" />
								<?php if(!empty($afreg_field_description)) { ?>
									<span class="afreg_field_message_radio"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
								<?php } } ?>
							</p>

				    	<?php } else if($afreg_field_type == 'googlecaptcha') { ?>

				    		<p class="form-row <?php echo esc_attr($afreg_main_class); ?>">
								<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required">*</span></label>
								
								<div class="g-recaptcha" data-sitekey="<?php echo get_option('afreg_site_key'); ?>"></div>

								<?php if(!empty($afreg_field_description)) { ?>
									<span class="afreg_field_message_radio"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
								<?php } ?>
							</p>

				    	<?php } 
					}
				}


			?>
			</fieldset>
			</div>
			
			<?php 
		}

		public function afreg_validate_update_role_my_account($validation_errors) {

		  	$afreg_allowed_tags = array(
	        'strong' => array(),
	        );

			$afreg_args = array( 
				'posts_per_page' => -1,
				'post_type' => 'afreg_fields',
				'post_status' => 'publish',
				'orderby' => 'menu_order',
				'order' => 'ASC'
			);

			$afreg_extra_fields = get_posts($afreg_args);
			if(!empty($afreg_extra_fields)) {

				foreach($afreg_extra_fields as $afreg_field) {

					$afreg_field_required = get_post_meta( intval($afreg_field->ID), 'afreg_field_required', true );
					$afreg_field_type = get_post_meta( intval($afreg_field->ID), 'afreg_field_type', true );
					$afreg_field_file_type = get_post_meta( intval($afreg_field->ID), 'afreg_field_file_type', true );
					$afreg_field_file_size = get_post_meta( intval($afreg_field->ID), 'afreg_field_file_size', true );

					if($afreg_field_type != 'fileupload') {
						if ( isset( $_POST['afreg_additional_'.intval($afreg_field->ID)] ) && empty( $_POST['afreg_additional_'.intval($afreg_field->ID)] ) && ($afreg_field_required == 'on') ) {

							$validation_errors->add( 'afreg_additional_'.intval($afreg_field->ID).'_error', esc_html__( $afreg_field->post_title.' is required!', 'addify_reg' ) );
						}
					}

					if($afreg_field_type == 'email') {

						if ( isset( $_POST['afreg_additional_'.intval($afreg_field->ID)] ) && !empty( $_POST['afreg_additional_'.intval($afreg_field->ID)] ) && ($afreg_field_required == 'on') && !filter_var($_POST['afreg_additional_'.intval($afreg_field->ID)], FILTER_VALIDATE_EMAIL) ) {

							$validation_errors->add( 'afreg_additional_'.intval($afreg_field->ID).'_error', esc_html__( $afreg_field->post_title.' is not a valid email address!', 'addify_reg' ) );
						}

					}

					if($afreg_field_type == 'multiselect') {
        			
	        			if(!is_array($_POST['afreg_additional_'.intval($afreg_field->ID)]) && $afreg_field_required == 'on') {
		        			
		        			$validation_errors->add( 'afreg_additional_'.intval($afreg_field->ID).'_error', esc_html__( $afreg_field->post_title.' is required!', 'addify_reg' ) );
		        			
		        		}
	        		}

	        		if($afreg_field_type == 'number') {

						if ( isset( $_POST['afreg_additional_'.intval($afreg_field->ID)] ) && !empty( $_POST['afreg_additional_'.intval($afreg_field->ID)] ) && ($afreg_field_required == 'on') && !filter_var($_POST['afreg_additional_'.intval($afreg_field->ID)], FILTER_VALIDATE_INT) ) {

							$validation_errors->add( 'afreg_additional_'.intval($afreg_field->ID).'_error', esc_html__( $afreg_field->post_title.' is not a valid number!', 'addify_reg' ) );
						}

					}

					if($afreg_field_type == 'checkbox' || $afreg_field_type == 'radio') { 

						if ( !isset( $_POST['afreg_additional_'.intval($afreg_field->ID)] ) && ($afreg_field_required == 'on') ) {

							$validation_errors->add( 'afreg_additional_'.intval($afreg_field->ID).'_error', esc_html__( $afreg_field->post_title.' is required!', 'addify_reg' ) );
						}

					}

					if($afreg_field_type == 'googlecaptcha') { 
        			
	        			if(isset($_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response'] != '') {
	        				$ccheck = $this->captcha_check($_POST['g-recaptcha-response']);
	        				if($ccheck == 'error') {
	        					$validation_errors->add( 'afreg_additional_'.intval($afreg_field->ID).'_error', esc_html__( 'Invalid reCaptcha!', 'addify_reg' ) );
	        				}
	        			} else {
	        				$validation_errors->add( 'afreg_additional_'.intval($afreg_field->ID).'_error', esc_html__( $afreg_field->post_title.' is required!', 'addify_reg' ) );
	        			}
	        		}

					if($afreg_field_type == 'fileupload') {


						if(isset($_FILES['afreg_additional_'.intval($afreg_field->ID)]['name']) && !empty($_FILES['afreg_additional_'.intval($afreg_field->ID)]['name']) && $afreg_field_required == 'on') {

							$afreg_allowed_types =  explode(',', $afreg_field_file_type);
							$afreg_filename = $_FILES['afreg_additional_'.intval($afreg_field->ID)]['name'];
							$afreg_ext = pathinfo($afreg_filename, PATHINFO_EXTENSION);

							if(!in_array($afreg_ext,$afreg_allowed_types) ) {

								$validation_errors->add( 'afreg_additional_'.intval($afreg_field->ID).'_error', esc_html__( $afreg_field->post_title.': File type is not allowed!', 'addify_reg' ) );
							}

							$afreg_filesize = $_FILES['afreg_additional_'.intval($afreg_field->ID)]['size'];
							$afreg_allowed_size = $afreg_field_file_size * 1000000; // convert from MB to Bytes

							if($afreg_filesize > $afreg_allowed_size) {

								$validation_errors->add( 'afreg_additional_'.intval($afreg_field->ID).'_error', esc_html__( $afreg_field->post_title.': File size is too big!', 'addify_reg' ) );

							}
						}
					}
				}
			}

		}

		public function afreg_save_update_role_my_account($customer_id) {

		  	$afreg_args = array( 
				'posts_per_page' => -1,
				'post_type' => 'afreg_fields',
				'post_status' => 'publish',
				'orderby' => 'menu_order',
				'order' => 'ASC'
			);

			$afreg_extra_fields = get_posts($afreg_args);

			if(!empty($afreg_extra_fields)) {

				foreach($afreg_extra_fields as $afreg_field) {

					$afreg_field_type = get_post_meta( intval($afreg_field->ID), 'afreg_field_type', true );

					if ( isset( $_POST['afreg_additional_'.intval($afreg_field->ID)] ) || isset( $_FILES['afreg_additional_'.intval($afreg_field->ID)] ) ) {

						if($afreg_field_type == 'fileupload') {

							if($_FILES['afreg_additional_'.intval($afreg_field->ID)]['name']!='') { 

								$file = time('m').$_FILES['afreg_additional_'.intval($afreg_field->ID)]['name'];
								$target_path = AFREG_PLUGIN_DIR.'uploaded_files/';
								$target_path = $target_path . $file;
								$temp = move_uploaded_file($_FILES['afreg_additional_'.intval($afreg_field->ID)]['tmp_name'], $target_path);
								update_user_meta($customer_id,'afreg_additional_'.intval($afreg_field->ID),$file);

							}

						} else if($afreg_field_type == 'multiselect') { 
		        			$prefix = '';
		        			$multival = '';
		        			foreach ($_POST['afreg_additional_'.intval($afreg_field->ID)] as $value) {
		        				$multival .= $prefix.$value;
	    						$prefix = ', ';
		        			}
		        			update_user_meta( $customer_id, 'afreg_additional_'.intval($afreg_field->ID), esc_attr($multival) );

		        		} else {

							update_user_meta( $customer_id, 'afreg_additional_'.intval($afreg_field->ID),  esc_attr($_POST['afreg_additional_'.intval($afreg_field->ID)]));
						}

					}
				}

			}

		}

		public function afreg_checkout_account_extra_fields($fields) {

			if(!is_user_logged_in()) { 

				$afreg_args = array( 
					'posts_per_page' => -1,
					'post_type' => 'afreg_fields',
					'post_status' => 'publish',
					'orderby' => 'menu_order',
					'order' => 'ASC'
				);

				$afreg_extra_fields = get_posts($afreg_args);

				if(!empty($afreg_extra_fields)) {

					foreach($afreg_extra_fields as $afreg_field) {

						$afreg_field_type = get_post_meta( intval($afreg_field->ID), 'afreg_field_type', true );
    					$afreg_field_options = unserialize(get_post_meta( intval($afreg_field->ID), 'afreg_field_option', true )); 
						$afreg_field_required = get_post_meta( intval($afreg_field->ID), 'afreg_field_required', true );
				    	$afreg_field_width = get_post_meta( intval($afreg_field->ID), 'afreg_field_width', true );
				    	$afreg_field_placeholder = get_post_meta( intval($afreg_field->ID), 'afreg_field_placeholder', true );
				    	$afreg_field_description = get_post_meta( intval($afreg_field->ID), 'afreg_field_description', true );
				    	$afreg_field_css = get_post_meta( intval($afreg_field->ID), 'afreg_field_css', true );
				    	$afreg_field_read_only = get_post_meta( $afreg_field->ID, 'afreg_field_read_only', true );

				    	if(!empty($afreg_field_width) && $afreg_field_width == 'full') {

				    		$afreg_main_class = 'form-row-wide';

				    	} else if(!empty($afreg_field_width) && $afreg_field_width == 'half') {

				    		$afreg_main_class = 'half_width';
				    	}

				    	if($afreg_field_type == 'select') {
					   		$select_options = array();
					   		foreach ($afreg_field_options as $opt) {
					   			
					   			$select_options[$opt['field_value']] = $opt['field_text'];
					   		}
				   		}

				   		if($afreg_field_type == 'multiselect') {
					   		$multiselect_options = array();
					   		foreach ($afreg_field_options as $opt) {
					   			
					   			$multiselect_options[$opt['field_value']] = $opt['field_text'];
					   		}
				   		}

				   		if($afreg_field_type == 'radio') {
					   		$radio_options = array();
					   		foreach ($afreg_field_options as $opt) {
					   			
					   			$radio_options[$opt['field_value']] = $opt['field_text'];
					   		}
				   		}

				    	if($afreg_field_type == 'text') {

		        			$fields['account']['afreg_additional_'.intval($afreg_field->ID)] = array(
						        'label'         => esc_html__($afreg_field->post_title , 'addify_reg'),
						        'placeholder'   => esc_html__($afreg_field_placeholder, 'addify_reg'),
						        'required'      => ($afreg_field_required == 'on' ? true : false),
						        'class'         => array($afreg_main_class, $afreg_field_css),
						        'clear'         => false,
						        'id'         	=> 'afreg_additional_'.intval($afreg_field->ID),
						        'type'			=> 'text',
						        'description'   => $afreg_field_description
						    );

		        		} else if($afreg_field_type == 'textarea') {

		        			$fields['account']['afreg_additional_'.intval($afreg_field->ID)] = array(
						        'label'         => esc_html__($afreg_field->post_title , 'addify_reg'),
						        'placeholder'   => esc_html__($afreg_field_placeholder, 'addify_reg'),
						        'required'      => ($afreg_field_required == 'on' ? true : false),
						        'class'         => array($afreg_main_class, $afreg_field_css),
						        'clear'         => false,
						        'id'         	=> 'afreg_additional_'.intval($afreg_field->ID),
						        'type'			=> 'textarea',
						        'description'   => $afreg_field_description
						    );

		        		} else if($afreg_field_type == 'select') {

		        			$fields['account']['afreg_additional_'.intval($afreg_field->ID)] = array(
						        'label'         => esc_html__($afreg_field->post_title , 'addify_reg'),
						        'placeholder'   => esc_html__($afreg_field_placeholder, 'addify_reg'),
						        'required'      => ($afreg_field_required == 'on' ? true : false),
						        'class'         => array($afreg_main_class, $afreg_field_css),
						        'clear'         => false,
						        'id'         	=> 'afreg_additional_'.intval($afreg_field->ID),
						        'type'			=> 'select',
						        'description'   => $afreg_field_description,
						        'options'     	=> $select_options,
						    );

		        		} else if($afreg_field_type == 'multiselect') {

		        			$fields['account']['afreg_additional_'.intval($afreg_field->ID).'[]'] = array(
						        'label'         => esc_html__($afreg_field->post_title , 'addify_reg'),
						        'placeholder'   => esc_html__($afreg_field_placeholder, 'addify_reg'),
						        'required'      => ($afreg_field_required == 'on' ? true : false),
						        'class'         => array($afreg_main_class, $afreg_field_css),
						        'clear'         => false,
						        'id'         	=> 'afreg_additional_'.intval($afreg_field->ID),
						        'type'			=> 'multiselect',
						        'description'   => $afreg_field_description,
						        'options'     	=> $multiselect_options,
						    );

		        		} else if($afreg_field_type == 'radio') {

		        			$fields['account']['afreg_additional_'.intval($afreg_field->ID)] = array(
						        'label'         => esc_html__($afreg_field->post_title , 'addify_reg'),
						        'placeholder'   => esc_html__($afreg_field_placeholder, 'addify_reg'),
						        'required'      => ($afreg_field_required == 'on' ? true : false),
						        'class'         => array($afreg_main_class, $afreg_field_css, 'afreg_radio'),
						        'clear'         => false,
						        'id'         	=> 'afreg_additional_'.intval($afreg_field->ID),
						        'type'			=> 'radio',
						        'description'   => $afreg_field_description,
						        'options'     	=> $radio_options,
						    );

		        		} else if($afreg_field_type == 'checkbox') {

		        			$fields['account']['afreg_additional_'.intval($afreg_field->ID)] = array(
						        'label'         => esc_html__($afreg_field->post_title , 'addify_reg'),
						        'placeholder'   => esc_html__($afreg_field_placeholder, 'addify_reg'),
						        'required'      => ($afreg_field_required == 'on' ? true : false),
						        'class'         => array($afreg_main_class, $afreg_field_css, 'afreg_radio'),
						        'clear'         => false,
						        'id'         	=> 'afreg_additional_'.intval($afreg_field->ID),
						        'type'			=> 'checkbox',
						        'description'   => $afreg_field_description
						    );

		        		} else if($afreg_field_type == 'email') {

		        			$fields['account']['afreg_additional_'.intval($afreg_field->ID)] = array(
						        'label'         => esc_html__($afreg_field->post_title , 'addify_reg'),
						        'placeholder'   => esc_html__($afreg_field_placeholder, 'addify_reg'),
						        'required'      => ($afreg_field_required == 'on' ? true : false),
						        'class'         => array($afreg_main_class, $afreg_field_css),
						        'clear'         => false,
						        'id'         	=> 'afreg_additional_'.intval($afreg_field->ID),
						        'type'			=> 'email',
						        'description'   => $afreg_field_description
						    );

		        		} else if($afreg_field_type == 'number') {

		        			$fields['account']['afreg_additional_'.intval($afreg_field->ID)] = array(
						        'label'         => esc_html__($afreg_field->post_title , 'addify_reg'),
						        'placeholder'   => esc_html__($afreg_field_placeholder, 'addify_reg'),
						        'required'      => ($afreg_field_required == 'on' ? true : false),
						        'class'         => array($afreg_main_class, $afreg_field_css),
						        'clear'         => false,
						        'id'         	=> 'afreg_additional_'.intval($afreg_field->ID),
						        'type'			=> 'number',
						        'description'   => $afreg_field_description
						    );

		        		} else if($afreg_field_type == 'password') {

		        			$fields['account']['afreg_additional_'.intval($afreg_field->ID)] = array(
						        'label'         => esc_html__($afreg_field->post_title , 'addify_reg'),
						        'placeholder'   => esc_html__($afreg_field_placeholder, 'addify_reg'),
						        'required'      => ($afreg_field_required == 'on' ? true : false),
						        'class'         => array($afreg_main_class, $afreg_field_css),
						        'clear'         => false,
						        'id'         	=> 'afreg_additional_'.intval($afreg_field->ID),
						        'type'			=> 'password',
						        'description'   => $afreg_field_description
						    );

		        		} else if($afreg_field_type == 'datepicker') {

		        			$fields['account']['afreg_additional_'.intval($afreg_field->ID)] = array(
						        'label'         => esc_html__($afreg_field->post_title , 'addify_reg'),
						        'placeholder'   => esc_html__($afreg_field_placeholder, 'addify_reg'),
						        'required'      => ($afreg_field_required == 'on' ? true : false),
						        'class'         => array($afreg_main_class, $afreg_field_css),
						        'clear'         => false,
						        'id'         	=> 'afreg_additional_'.intval($afreg_field->ID),
						        'type'			=> 'date',
						        'description'   => $afreg_field_description
						    );

		        		} else if($afreg_field_type == 'timepicker') {

		        			$fields['account']['afreg_additional_'.intval($afreg_field->ID)] = array(
						        'label'         => esc_html__($afreg_field->post_title , 'addify_reg'),
						        'placeholder'   => esc_html__($afreg_field_placeholder, 'addify_reg'),
						        'required'      => ($afreg_field_required == 'on' ? true : false),
						        'class'         => array($afreg_main_class, $afreg_field_css),
						        'clear'         => false,
						        'id'         	=> 'afreg_additional_'.intval($afreg_field->ID),
						        'type'			=> 'time',
						        'description'   => $afreg_field_description
						    );

		        		} 

				    }
				}


			}

            return $fields;

		}


		public function afreg_custom_multiselect_handler( $field, $key, $args, $value  ) {
					
		    $options = '';
		    $ekey = explode('[', $key);
		    $field_id = explode('afreg_additional_', $ekey[0]);
		    $is_required = get_post_meta( intval($field_id[1]), 'afreg_field_required', true );

		    if($is_required!='') {
		    	if($is_required == "on") {
		    		$required = '<abbr class="required" title="required">*</abbr>';
		    	} else {
		    		$required = '';
		    	}
		    }
		    if ( ! empty( $args['options'] ) ) {
		        foreach ( $args['options'] as $option_key => $option_text ) {
		            $options .= '<option value="' . esc_attr($option_key) . '" '. selected( $value, $option_key, false ) . '>' . esc_attr($option_text) .'</option>';
		        }

		        $field = '<p class="form-row ' . implode( ' ', $args['class'] ) .'" id="' . $key . '_field">
		            <label for="' . $key . '" class="' . implode( ' ', $args['label_class'] ) .'">' . $args['label'].$required.'</label>
		            <select name="' . $key . '" id="' . $key . '" class="select" multiple="multiple">
		                ' . $options . '
		            </select>
		        </p>';
		    }

		    return $field;
		}

		public function afreg_extra_fields_show_wordpress() {

			?>
			<div class="wordpress_additional">
				<h3><?php echo esc_html__(get_option('afreg_additional_fields_section_title'), 'addify_reg'); ?></h3>
				<?php

				$afreg_args = array( 
					'posts_per_page' => -1,
					'post_type' => 'afreg_fields',
					'post_status' => 'publish',
					'orderby' => 'menu_order',
					'order' => 'ASC'
				);
				$afreg_extra_fields = get_posts($afreg_args);
				if(!empty($afreg_extra_fields)) {

					foreach($afreg_extra_fields as $afreg_field) {

						$afreg_field_type = get_post_meta( intval($afreg_field->ID), 'afreg_field_type', true );
						$afreg_field_options = unserialize(get_post_meta( intval($afreg_field->ID), 'afreg_field_option', true )); 
						$afreg_field_required = get_post_meta( intval($afreg_field->ID), 'afreg_field_required', true );
				    	$afreg_field_description = get_post_meta( intval($afreg_field->ID), 'afreg_field_description', true );
				    	$afreg_field_width = get_post_meta( intval($afreg_field->ID), 'afreg_field_width', true );

				    	if(!empty($afreg_field_width) && $afreg_field_width == 'full') {

				    		$afreg_main_class = 'form-row-wide';

				    	} else if(!empty($afreg_field_width) && $afreg_field_width == 'half') {

				    		$afreg_main_class = 'half_width';
				    	}

				    	if($afreg_field_type == 'text') { ?>

							<p class="form-row-wordpress <?php echo esc_attr($afreg_main_class); ?>">
								<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>
								<input type="text" class="input" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]); ?>" />
								<?php if(!empty($afreg_field_description)) { ?>
									<span class="afreg_field_message_wordpress"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
								<?php } ?>
							</p>

				    	<?php } else if($afreg_field_type == 'textarea') { ?>

				    		<p class="form-row-wordpress <?php echo esc_attr($afreg_main_class); ?>">
								<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>
								<textarea rows="7" cols="31" class="input" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]); ?></textarea>
								<?php if(!empty($afreg_field_description)) { ?>
									<span class="afreg_field_message_wordpress"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
								<?php } ?>
							</p>

				    	<?php } else if($afreg_field_type == 'email') { ?>

				    		<p class="form-row-wordpress <?php echo esc_attr($afreg_main_class); ?>">
								<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>
								<input type="text" class="input" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]); ?>" />
								<?php if(!empty($afreg_field_description)) { ?>
									<span class="afreg_field_message_wordpress"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
								<?php } ?>
							</p>

				    	<?php } else if($afreg_field_type == 'select') { ?>

				    		<p class="form-row-wordpress <?php echo esc_attr($afreg_main_class); ?>">
								<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>
								<select class="inputselect" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>">
									<?php foreach($afreg_field_options as $afreg_field_option) { ?>
										<option value="<?php echo esc_attr($afreg_field_option['field_value']); ?>" <?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo selected(esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]), esc_attr($afreg_field_option['field_value'])); ?>>
											<?php if(!empty($afreg_field_option['field_text'])) echo esc_html__(esc_attr($afreg_field_option['field_text']), 'addify_reg'); ?>
										</option>
									<?php } ?>
								</select>
								<?php if(!empty($afreg_field_description)) { ?>
									<span class="afreg_field_message_wordpress"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
								<?php } ?>
							</p>

				    	<?php } else if($afreg_field_type == 'multiselect') { ?>

				    		<p class="form-row-wordpress <?php echo esc_attr($afreg_main_class); ?>">
								<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>
								<select class="inputmselect" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>[]" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" multiple>
									<?php foreach($afreg_field_options as $afreg_field_option) {

										if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) { ?>
											<option value="<?php echo esc_attr($afreg_field_option['field_value']); ?>" <?php if(in_array(esc_attr($afreg_field_option['field_value']), $_POST['afreg_additional_'.intval($afreg_field->ID)])) echo "selected"; ?>>
												<?php echo esc_html__(esc_attr($afreg_field_option['field_text']), 'addify_reg'); ?>
											</option>
										<?php } else { ?>
										<option value="<?php echo esc_attr($afreg_field_option['field_value']); ?>">
											<?php echo esc_html__(esc_attr($afreg_field_option['field_text']), 'addify_reg'); ?>
										</option>
									<?php } } ?>
								</select>
								<?php if(!empty($afreg_field_description)) { ?>
									<span class="afreg_field_message_wordpress"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
								<?php } ?>
							</p>

				    	<?php } else if($afreg_field_type == 'checkbox') { ?> 

				    		<p class="form-row-wordpress <?php echo esc_attr($afreg_main_class); ?>">
								<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>
								<input type="checkbox" class="inputcheckbox" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" <?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo checked(esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]), 'on'); ?>  />
								
								<?php if(!empty($afreg_field_description)) { ?>
									<span class="afreg_field_message_wordpress_checkbox"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
								<?php } ?>
							</p>

				    	<?php } else if($afreg_field_type == 'radio') { ?>

				    		<p class="form-row-wordpress <?php echo esc_attr($afreg_main_class); ?>">
								<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>
								
								<?php foreach($afreg_field_options as $afreg_field_option) { ?>
									<input type="radio" class="inputradio" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php echo esc_attr($afreg_field_option['field_value']); ?>" <?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo checked(esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]), esc_attr($afreg_field_option['field_value'])); ?>  />
									<span class="afreg_radio"><?php if(!empty($afreg_field_option['field_text'])) echo esc_html__(esc_attr($afreg_field_option['field_text']), 'addify_reg'); ?></span>
								<?php } ?>
								
								<?php if(!empty($afreg_field_description)) { ?>
									<span class="afreg_field_message_radio_wordpress"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
								<?php } ?>
							</p>

				    	<?php } else if($afreg_field_type == 'number') { ?>

				    		<p class="form-row-wordpress <?php echo esc_attr($afreg_main_class); ?>">
								<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>
								<input type="number" class="input inputnumb" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]); ?>" />
								<?php if(!empty($afreg_field_description)) { ?>
									<span class="afreg_field_message_wordpress"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
								<?php } ?>
							</p>

				    	<?php } else if($afreg_field_type == 'password') { ?>

				    		<p class="form-row-wordpress <?php echo esc_attr($afreg_main_class); ?>">
								<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>
								<input type="password" class="input" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]); ?>" />
								<?php if(!empty($afreg_field_description)) { ?>
									<span class="afreg_field_message_wordpress"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
								<?php } ?>
							</p>

				    	<?php } else if($afreg_field_type == 'fileupload') { ?>

				    		<p class="form-row-wordpress <?php echo esc_attr($afreg_main_class); ?>">
								<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>
								<input type="file" class="input <?php if(!empty($afreg_field_css)) echo esc_attr($afreg_field_css); ?>" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php if(!empty($_FILES['afreg_additional_'.intval($afreg_field->ID)])) echo esc_attr( $_FILES['afreg_additional_'.intval($afreg_field->ID)]); ?>" placeholder="<?php if(!empty($afreg_field_placeholder)) echo esc_html__($afreg_field_placeholder , 'addify_reg' );  ?>" />
								<?php if(!empty($afreg_field_description)) { ?>
									<span class="afreg_field_message_wordpress"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
								<?php } ?>
							</p>

				    	<?php } else if($afreg_field_type == 'color') { ?>

				    		<p class="form-row-wordpress <?php echo esc_attr($afreg_main_class); ?>">
								<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>
								<input type="color" class="input color_sepctrum" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]); ?>" />
								<?php if(!empty($afreg_field_description)) { ?>
									<span class="afreg_field_message_wordpress"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
								<?php } ?>
							</p>

				    	<?php } else if($afreg_field_type == 'datepicker') { ?>

				    		<p class="form-row-wordpress <?php echo esc_attr($afreg_main_class); ?>">
								<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>
								<input type="date" class="input" name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]); ?>" />
								<?php if(!empty($afreg_field_description)) { ?>
									<span class="afreg_field_message_wordpress"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
								<?php } ?>
							</p>

				    	<?php } else if($afreg_field_type == 'timepicker') { ?>

				    		<p class="form-row-wordpress <?php echo esc_attr($afreg_main_class); ?>">
								<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required"><?php if(!empty($afreg_field_required) && $afreg_field_required == 'on') ?>*</span></label>
								<input type="time" class="input " name="afreg_additional_<?php echo intval($afreg_field->ID); ?>" id="afreg_additional_<?php echo intval($afreg_field->ID); ?>" value="<?php if(!empty($_POST['afreg_additional_'.intval($afreg_field->ID)])) echo esc_attr( $_POST['afreg_additional_'.intval($afreg_field->ID)]); ?>" />
								<?php if(!empty($afreg_field_description)) { ?>
									<span class="afreg_field_message_wordpress"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
								<?php } ?>
							</p>

				    	<?php } else if($afreg_field_type == 'googlecaptcha') { ?>

				    		<p class="form-row <?php echo esc_attr($afreg_main_class); ?>">
								<label for="afreg_additional_<?php echo intval($afreg_field->ID); ?>"><?php if(!empty($afreg_field->post_title)) echo esc_html__($afreg_field->post_title , 'addify_reg' ); ?><span class="required">*</span></label>
								
								<div class="g-recaptcha" data-sitekey="<?php echo get_option('afreg_site_key'); ?>"></div>

								<?php if(!empty($afreg_field_description)) { ?>
									<span class="afreg_field_message_wordpress"><?php echo esc_html__($afreg_field_description, 'addify_reg'); ?></span>
								<?php } ?>
							</p>

				    	<?php }

					}
				}
				?>
			</div>
			<?php 
		}

		public function aferg_wordpress_registration_errors($validation_errors, $sanitized_user_login, $user_email) {

			$afreg_args = array( 
				'posts_per_page' => -1,
				'post_type' => 'afreg_fields',
				'post_status' => 'publish',
				'orderby' => 'menu_order',
				'order' => 'ASC'
			);

			$afreg_extra_fields = get_posts($afreg_args);
			if(!empty($afreg_extra_fields)) {

				foreach($afreg_extra_fields as $afreg_field) {

					$afreg_field_required = get_post_meta( intval($afreg_field->ID), 'afreg_field_required', true );
					$afreg_field_type = get_post_meta( intval($afreg_field->ID), 'afreg_field_type', true );
					$afreg_field_file_type = get_post_meta( intval($afreg_field->ID), 'afreg_field_file_type', true );
					$afreg_field_file_size = get_post_meta( intval($afreg_field->ID), 'afreg_field_file_size', true );

					if ( isset( $_POST['afreg_additional_'.intval($afreg_field->ID)] ) && empty( $_POST['afreg_additional_'.intval($afreg_field->ID)] ) && ($afreg_field_required == 'on') ) {

						$validation_errors->add( 'afreg_additional_'.intval($afreg_field->ID).'_error', esc_html__( $afreg_field->post_title.' is required!', 'addify_reg' ) );
					}

					if($afreg_field_type == 'email') {

						if ( isset( $_POST['afreg_additional_'.intval($afreg_field->ID)] ) && !empty( $_POST['afreg_additional_'.intval($afreg_field->ID)] ) && ($afreg_field_required == 'on') && !filter_var($_POST['afreg_additional_'.intval($afreg_field->ID)], FILTER_VALIDATE_EMAIL) ) {

							$validation_errors->add( 'afreg_additional_'.intval($afreg_field->ID).'_error', esc_html__( $afreg_field->post_title.' is not a valid email address!', 'addify_reg' ) );
						}

					}

					if($afreg_field_type == 'multiselect') {
        			
	        			if(!is_array($_POST['afreg_additional_'.intval($afreg_field->ID)]) && $afreg_field_required == 'on') {
		        			
		        			$validation_errors->add( 'afreg_additional_'.intval($afreg_field->ID).'_error', esc_html__( $afreg_field->post_title.' is required!', 'addify_reg' ) );
		        			
		        		}
	        		}

	        		if($afreg_field_type == 'number') {

						if ( isset( $_POST['afreg_additional_'.intval($afreg_field->ID)] ) && !empty( $_POST['afreg_additional_'.intval($afreg_field->ID)] ) && ($afreg_field_required == 'on') && !filter_var($_POST['afreg_additional_'.intval($afreg_field->ID)], FILTER_VALIDATE_INT) ) {

							$validation_errors->add( 'afreg_additional_'.intval($afreg_field->ID).'_error', esc_html__( $afreg_field->post_title.' is not a valid number!', 'addify_reg' ) );
						}

					}

					if($afreg_field_type == 'checkbox' || $afreg_field_type == 'radio') { 

						if ( !isset( $_POST['afreg_additional_'.intval($afreg_field->ID)] ) && ($afreg_field_required == 'on') ) {

							$validation_errors->add( 'afreg_additional_'.intval($afreg_field->ID).'_error', esc_html__( $afreg_field->post_title.' is required!', 'addify_reg' ) );
						}

					}

					if($afreg_field_type == 'googlecaptcha') { 
        			
	        			if(isset($_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response'] != '') {
	        				$ccheck = $this->captcha_check($_POST['g-recaptcha-response']);
	        				if($ccheck == 'error') {
	        					$validation_errors->add( 'afreg_additional_'.intval($afreg_field->ID).'_error', esc_html__( 'Invalid reCaptcha!', 'addify_reg' ) );
	        				}
	        			} else {
	        				$validation_errors->add( 'afreg_additional_'.intval($afreg_field->ID).'_error', esc_html__( $afreg_field->post_title.' is required!', 'addify_reg' ) );
	        			}
	        		}

					if($afreg_field_type == 'fileupload') {

						if(isset($_FILES['afreg_additional_'.intval($afreg_field->ID)]['name']) && empty($_FILES['afreg_additional_'.intval($afreg_field->ID)]['name']) && $afreg_field_required == 'on') {

							$validation_errors->add( 'afreg_additional_'.intval($afreg_field->ID).'_error', esc_html__( $afreg_field->post_title.' is required!', 'addify_reg' ) );

						}

						if(isset($_FILES['afreg_additional_'.intval($afreg_field->ID)]['name']) && !empty($_FILES['afreg_additional_'.intval($afreg_field->ID)]['name']) && $afreg_field_required == 'on') {

							$afreg_allowed_types =  explode(',', $afreg_field_file_type);
							$afreg_filename = $_FILES['afreg_additional_'.intval($afreg_field->ID)]['name'];
							$afreg_ext = pathinfo($afreg_filename, PATHINFO_EXTENSION);

							if(!in_array($afreg_ext,$afreg_allowed_types) ) {

								$validation_errors->add( 'afreg_additional_'.intval($afreg_field->ID).'_error', esc_html__( $afreg_field->post_title.': File type is not allowed!', 'addify_reg' ) );
							}

							$afreg_filesize = $_FILES['afreg_additional_'.intval($afreg_field->ID)]['size'];
							$afreg_allowed_size = $afreg_field_file_size * 1000000; // convert from MB to Bytes

							if($afreg_filesize > $afreg_allowed_size) {

								$validation_errors->add( 'afreg_additional_'.intval($afreg_field->ID).'_error', esc_html__( $afreg_field->post_title.': File size is too big!', 'addify_reg' ) );

							}
						}
					}
				}
			}

			return $validation_errors;
		}


		public function captcha_check($res) {

				$secret = get_option('afreg_secret_key');
       
        		$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$res);
        		
        		$responseData = json_decode($verifyResponse);

        		if($responseData->success) {
        			return "success";
        		} else {
        			return "error";
        		}
       }


	}

	new Addify_Registration_Fields_Addon_Front();

} 