<?php
$tamplatePath = FrameWpf::_()->getModule('woofilters')->getView()->getPath('woofiltersEditTabCommonTitle');

// fallback for depricated optionality
if ( $tamplatePath ) {
	FrameWpf::_()->getModule('woofilters')->getView()->display('woofiltersEditTabCommonTitle');
} else {
	ViewWpf::display('woofiltersEditTabCommonTitleDepricated');
}
?>
<div class="row-settings-block">
	<div class="settings-block-label settings-w100 col-xs-4 col-sm-3">
		<?php esc_html_e('Show on frontend as', 'woo-product-filter'); ?>
		<i class="fa fa-question woobewoo-tooltip no-tooltip" title="<?php echo esc_attr__('Show your roles list as checkbox or dropdown.', 'woo-product-filter'); ?>"></i>
	</div>
	<div class="settings-block-values settings-w100 col-xs-8 col-sm-9">
		<div class="settings-value settings-w100">
			<?php
			HtmlWpf::selectbox('f_frontend_type', array(
				'options' => array(
					'list' => 'Checkbox list',
					'dropdown' => 'Dropdown',
					'mul_dropdown' => 'Multiple Dropdown',
				),
				'attrs' => 'class="woobewoo-flat-input"',
			));
			?>
		</div>
	</div>
</div>
<?php
DispatcherWpf::doAction( 'addEditTabFilters', 'partEditTabFiltersMultiSelect');
?>
<div class="row-settings-block wpfTypeSwitchable" data-type="dropdown mul_dropdown">
	<div class="settings-block-label settings-w100 col-xs-4 col-sm-3">
		<?php esc_html_e('Dropdown label', 'woo-product-filter'); ?>
		<i class="fa fa-question woobewoo-tooltip no-tooltip" title="<?php echo esc_attr__('Dropdown first option text.', 'woo-product-filter'); ?>"></i>
	</div>
	<div class="settings-block-values settings-w100 col-xs-8 col-sm-9">
		<div class="settings-value settings-w100">
			<?php
			HtmlWpf::text('f_dropdown_first_option_text', array(
				'placeholder' => esc_attr__('Select all', 'woo-product-filter'),
				'attrs' => 'class="woobewoo-flat-input"',
			));
			?>
		</div>
	</div>
</div>
<div class="row-settings-block wpfTypeSwitchable" data-type="mul_dropdown">
	<div class="settings-block-label settings-w100 col-xs-4 col-sm-3">
		<?php esc_html_e('Show search for dropdown', 'woo-product-filter'); ?>
		<i class="fa fa-question woobewoo-tooltip no-tooltip" title="<?php echo esc_attr(__('Show search field in multiple dropdown box.', 'woo-product-filter') . ' <a href="' . esc_url('https://' . WPF_WP_PLUGIN_URL . '/documentation/product-tags/') . '" class="wupsales-wc-hidden" target="_blank">' . __('Learn More', 'woo-product-filter') . '</a>'); ?>"></i>
	</div>
	<div class="settings-block-values settings-values-w100 col-xs-8 col-sm-9">
		<div class="settings-value settings-w100">
			<?php HtmlWpf::checkboxToggle('f_dropdown_search', array()); ?>
		</div>
		<div class="settings-value settings-w100" data-parent="f_dropdown_search">
			<?php HtmlWpf::text('f_dropdown_search_text', array('placeholder' => esc_attr__('Search', 'woo-product-filter'), 'attrs' => 'class="woobewoo-flat-input"')); ?>
		</div>
	</div>
</div>
<div class="row-settings-block wpfTypeSwitchable" data-type="list">
	<div class="settings-block-label col-xs-4 col-sm-3">
		<?php esc_html_e('Show search', 'woo-product-filter'); ?>
		<i class="fa fa-question woobewoo-tooltip no-tooltip" title="<?php echo esc_attr__('Show search display the bar for searching by author name in the filter.', 'woo-product-filter'); ?>"></i>
	</div>
	<div class="settings-block-values settings-values-w100 col-xs-8 col-sm-9">
		<div class="settings-value settings-w100">
			<?php HtmlWpf::checkboxToggle('f_show_search_input', array()); ?>
		</div>
		<div class="settings-value settings-w100" data-parent="f_show_search_input">
			<?php
			$labels = FrameWpf::_()->getModule('woofilters')->getModel('woofilters')->getFilterLabels('Author');
			HtmlWpf::text('f_search_label', array('placeholder' => esc_attr($labels['search']), 'attrs' => 'class="woobewoo-flat-input"'));
			?>
		</div>
	</div>
</div>
<div class="row-settings-block wpfTypeSwitchable" data-not-type="dropdown">
	<div class="settings-block-label settings-w100 col-xs-4 col-sm-3">
		<?php esc_html_e('Layout', 'woo-product-filter'); ?>
		<i class="fa fa-question woobewoo-tooltip no-tooltip" title="<?php echo esc_attr__('Select a vertical or horizontal layout and set the count of columns.', 'woo-product-filter'); ?>"></i>
	</div>
	<div class="settings-block-values settings-w100 col-xs-8 col-sm-9">
		<div class="settings-value settings-w50">
			<?php
			HtmlWpf::selectbox('f_layout', array(
				'options' => array('ver' => esc_attr__('Vertical', 'woo-product-filter'), 'hor' => esc_attr__('Horizontal', 'woo-product-filter')),
				'attrs' => 'class="woobewoo-flat-input"',
			));
			?>
		</div>
		<div class="settings-value settings-w50" data-select="f_layout" data-select-value="ver">
			<div class="settings-value-label">
				<?php esc_html_e('Columns', 'woo-product-filter'); ?>
			</div>
			<?php HtmlWpf::text('f_ver_columns', array('value' => 1, 'attrs' => 'class="woobewoo-flat-input woobewoo-number woobewoo-width40"')); ?>
		</div>
	</div>
</div>
<div class="row-settings-block wpfTypeSwitchable" data-not-type="dropdown">
	<div class="settings-block-label settings-w100 col-xs-4 col-sm-3">
		<?php esc_html_e('Maximum height in frontend', 'woo-product-filter'); ?>
		<i class="fa fa-question woobewoo-tooltip no-tooltip" title="<?php echo esc_attr__('Set maximum displayed height in frontend.', 'woo-product-filter'); ?>"></i>
	</div>
	<div class="settings-block-values settings-w100 col-xs-8 col-sm-9">
		<div class="settings-value settings-w100">
			<?php HtmlWpf::text('f_max_height', array('value'=>'200', 'attrs' => 'class="woobewoo-flat-input woobewoo-number woobewoo-width60"')); ?> px
		</div>
	</div>
</div>
