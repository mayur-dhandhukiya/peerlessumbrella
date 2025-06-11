<?php 
/* Template Name: Quote Template */
get_header();	
?>
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
<style type="text/css">
	.wp-Free-quote{
		padding: 0 15px;
		width: 100%;
	}
	.alert {
		position: relative;
		color: #3c3c3c;
		background-color: #fffadc;
		border: 2px solid #fcf5cd;
		background-position: 6px 6px;
		background-repeat: no-repeat;
		padding: 8px 40px 8px 43px;
		margin-top: 15px;
		line-height: 20px;
	}
	.alert #btnToggleOptions{
		background-color: transparent;
		padding: 0;
		color: #1869b8;
		text-decoration: underline;
	}
	.alert-info:before {
		content: "\f100";
		color: #2472be;
		display: block;
		position: absolute;
		left: 9px;
		top: 50%;
		transform: translateY(-50%);
		font-size: 24px;
		font-family: woodmart-font;
	}
	.wp-tabs .nav{
		display: flex;
		align-items: center;
		padding-left: 0;
		margin-bottom: 0;
	}
	.wp-tabs .nav .nav-item{
		margin-bottom: 0;
	}
	.wp-tabs .nav li{
		list-style: none;
		margin-right: 20px;
		position: relative;
	}
	.wp-tabs .nav li:last-child{
		margin-right: 0;
	}
	.wp-tabs .nav .nav-link, .save-btn a{
		padding: 8px 25px;
		display: block;
		color: #ffffff;
		position: relative;
		background-color: #00abc8;
	}
	.wp-tabs{
		margin: 20px 0;
		display: flex;
		align-items: center;
		justify-content: space-between;
	}
	.wp-paper{
		padding: 30px;
		border: 1px solid #c0c0c0;
	}
	.wp-meta-from label{
		font-size: 26px;
		font-weight: 500;
	}
	.wp-meta-from input{
		margin-bottom: 10px;
	}
	.wp-meta-from textarea, .wp-foot-left textarea{
		min-height: 120px;
	}
	.wp-meta-left .wp-meta-to{
		margin-top: 40px;
	}
	.wp-invoice-quete input{
		font-size: 24px;
		font-weight: 700;
		text-align: right;
	}
	.wp-meta{
		display: flex;
		margin-bottom: 40px;
		justify-content: space-between;
	}
	.wp-meta-left{
		width: 50%;
		flex-shrink: 0;
	}
	.wp-meta-right{
		display: grid;
		align-content: space-between;
	}
	.wp-invoice-quete{
		max-width: 300px;
	}
	.wp-form-invoice-dates .row label{
		margin-bottom: 0;
	}
	.wp-form-invoice-dates .row{
		margin-bottom: 10px;
	}
	.wp-datatable table tr th{
		color: #ffffff;
		background-color: #00abc8;
	}
	.wp-datatable table tr td{
		border: thin solid #e5e5e5;
		padding: 0;
	}
	.wp-datatable table tr td select, .wp-datatable table tr td textarea, .wp-datatable table tr td input{
		border: 0;
	}
	.wp-datatable table tr td input{
		text-align: right;
	}
	.wp-datatable table tr td textarea{
		min-height: 50px;
	}
	.wp-datatable table .btnDeleteRow{
		width: 100%;
		padding: 0;
		background-color: transparent;
	}
	.icon-new {
		display: inline-block;
		width: 14px;
		line-height: inherit;
		position: relative;
		margin-right: 5px
	}
	.icon-new:after {
		content: "";
		display: block;
		width: 14px;
		height: 14px;
		background-image: url(data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiIHdpZHRoPSIxNHB4IiBoZWlnaHQ9IjE0cHgiIHZpZXdCb3g9IjAgMCAxNCAxNCIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgMTQgMTQiIHhtbDpzcGFjZT0icHJlc2VydmUiPjxwYXRoIGZpbGw9IiM1NEEwRTciIGQ9Ik0yLDEzaDEwYzAuNTUyLDAsMS0wLjQ0OCwxLTFWMmMwLTAuNTUzLTAuNDQ4LTEtMS0xSDJDMS40NDcsMSwxLDEuNDQ3LDEsMnYxMEMxLDEyLjU1MiwxLjQ0NywxMywyLDEzeiBNMyw2aDNWM2gydjNoM3YySDh2M0g2VjhIM1Y2eiIvPjwvc3ZnPg==);
		background-repeat: no-repeat;
		position: absolute;
		top: 50%;
		margin-top: -7px;
		left: 50%;
		margin-left: -7px;
	}
	.wp-datatable{
		margin-bottom: 50px;
	}
	.wp-foot{
		display: flex;
		justify-content: space-between;
	}
	.wp-foot-left{
		width: 60%;
		flex-shrink: 0;
	}
	.wp-foot-right{
		width: 30%;
	}
	.wp-foot-right table{
		margin-bottom: 0;
	}
	.wp-foot-right table tr td{
		padding: 10px 12px;
		border-top: 2px solid #eaedee;
	}
	.wp-foot-right .sum-label{
		font-weight: bold;
		color: #000;
	}
	.sum-balance td{
		background-color: #fbff9e;
	}
	.ui-widget.ui-widget-content{
		box-shadow: inherit;
		color: #ffffff;
		text-align: center;
		max-width: 150px;
		background-color: #00abc8;
	}
	.ui-widget.ui-widget-content:before, .ui-widget.ui-widget-content:after {
		border-top: 0;
		border-left: solid transparent 5px;
		border-right: solid transparent 5px;
		border-bottom: solid #00abc8 5px;
		top: -4px;
		bottom: auto;
		content: "";
		height: 0;
		left: 50%;
		margin-left: -6px;
		position: absolute;
		width: 0;
	}
	#wp-options {
		display: none;
		margin: 15px 0 20px 0;
		background-color: #fafafa;
		border: thin solid #d2d2d2;
		padding: 14px 40px;
	}
	.wp-options-wrapper{
		display: flex;
		align-items: center;
		justify-content: space-between;
	}
	.wp-options-wrapper label{
		margin-bottom: 0;
	}
	.wp-upload-logo{
		margin-bottom: 20px;
	}
	#upload_area.empty {
		position: relative;
		text-align: right;
		max-width: 300px;
		min-height: 125px;
		border: 1px dashed #d2d2d2;
	}
	.yournamehere:before {
		content: "Your Logo Here";
		display: block;
		position: absolute;
		top: 19px;
		right: 15px;
		font-family: sans-serif;
		font-weight: bold;
		font-size: 22px;
		letter-spacing: 0.3px;
		color: #39c;
	}
	.yournamehere:after {
		content: "Your Logo Here";
		display: block;
		position: absolute;
		top: 40px;
		right: 15px;
		font-family: sans-serif;
		font-weight: bold;
		font-size: 22px;
		letter-spacing: 0.3px;
		color: #39c;
		transform: scale(1, -1);
		background: linear-gradient(to bottom, #fff 0%, #fff 35%, #8fcae7 100%);
		-webkit-background-clip: text;
		-webkit-text-fill-color: transparent;
		opacity: 0.5;
	}
	.input-file {
		position: relative;
		overflow: hidden;
		padding: 0;
		display: block;
		max-width: 100%;
		cursor: pointer;
		text-align: right;
		margin: 10px 0 0;
	}
	.input-file #logo_file_input{
		display: none;
	}
	.wp-viewinvoice-form-wrapper{
		display: none;
	}
	.viewinvoice-address-logo, .viewinvoice-toname-table{
		display: flex;
		justify-content: space-between;
		margin-bottom: 50px;
	}
	.invoice-table table{
		margin-bottom: 0;
	}
	.invoice-table h4{
		text-align: right;
		font-size: 24px;
	}
	.viewinvoice-dataTable table{
		border: 1px solid #00abc8;
	}
	.invoice-table table td {
		padding: 10px 12px;
		border-bottom: 0;
		text-align: right;
	}
	.viewinvoice-dataTable table tr th{
		background-color: #00abc8;
		color: #ffffff;
		border-bottom: 0;
	}
	.viewinvoice-dataTable table tr td{
		border-bottom: 0;
	}
	.invoice-notes{
		padding-top: 100px;
	}
	.invoice-subtotal-table table{
		margin-bottom: 0;
		border: 0;
		border-left: 1px solid #00abc8;
	}
	.invoice-row{
		border-top: 1px solid #00abc8;
	}
	.invoice-row td{
		padding: 0;
	}
	.invoice-subtotal-table table tr td{
		padding: 10px 12px;
	}
	.invoice-subtotal-table table tr td:first-child{
		font-weight: bold;
		color: #000000;
	}
	.balance-due-headline td{
		background-color: #eaedee;
		border-top: 1px solid #ccc;
	}
	.invoice-sum-tax td{
		color: #777777 !important;
		padding: 0px 12px 10px !important;
		font-weight: normal !important;
		border-bottom: 1px solid #ccc !important;
	}
	.close-icon{
		position: relative;
		background: #000;
		border-radius: 100%;
		FONT-WEIGHT: 700;
		width: 20px;
		height: 20px;
		color: #fff;
		display: flex;
		align-items: center;
		justify-content: center;
	}
	.close-icon::before{
		content: "\f112";
		font-size: 9px;
		font-family: woodmart-font;
	}


	@media (max-width: 991px){
		.viewinvoice-dataTable{
			overflow-x: scroll;
		}
		.viewinvoice-dataTable table{
			white-space: nowrap;
		}
	}

	@media (max-width: 767px){
		.wp-meta, .wp-foot{
			flex-direction: column;
		}
		.wp-meta-left, .wp-foot-left {
			width: 100%;
			margin-bottom: 35px;
		}
		.wp-invoice-quete{
			max-width: 100%;
			margin-bottom: 35px;
		}
		#upload_area.empty{
			max-width: 100%;
		}
		.wp-foot-right {
			width: 100%;
		}
		.wp-tabs{
			flex-direction: column;
		}
		.wp-tabs .nav{
			margin-bottom: 20px;
		}
		.wp-tabs .nav li{
			margin-right: 10px;
		}
		.wp-paper {
			padding: 30px 20px;
		}
		.wp-tabs .nav .nav-link, .save-btn a{
			padding: 8px 15px;
		}
		.wp-datatable{
			overflow-x: scroll;
			width: 100%;
		}
		.wp-datatable table{
			white-space: nowrap;
		}
		#wp-options{
			padding: 14px 20px;
		}
		.viewinvoice-address-logo, .viewinvoice-toname-table{
			flex-direction: column;
		}
	}



</style>

<div class="wp-Free-quote">
	<div class="printable-quote-title">
		<h2>Free Printable Quote</h2>
		<p>
			To create a free quote, just fill out the template below. To print, download or send your quote for free, click the save button. If you need more options, for example to upload a logo, click the link below.
		</p>
	</div>
	<div class="wp-Free-quote-form">
		<form>
			<div class="alert alert-info">
				<span class="hidden-small">Want to customize your quote?</span>
				<a href="javascript:;" id="btnToggleOptions">Show Customization Options</a>
			</div>
			<div id="wp-options" class="hidden-print open">
				<div class="wp-options-wrapper">
					<label class="option label-checkbox">
						<input type="checkbox" onclick="toggleLogo()" name="customization_logo" id="customization_logo">
						<span>Logo</span> 
					</label>
					<label class="option label-checkbox">
						<input type="checkbox" name="customization_poNumber" id="customization_poNumber">
						<span>P.O. #</span> 
					</label>
					<label class="option label-checkbox alertTax" data-alert="Please save before adding tax.">
						<input type="checkbox">
						<span>Sales Tax</span> 
					</label>
				</div>
			</div>
			<div class="wp-tabs">
				<ul class="nav tabs">
					<li class="nav-item tab-link current" data-tab="tab-1">
						<a id="preview" class="nav-link active" aria-current="page" href="javascript:;">Preview</a>
					</li>
					<li class="nav-item tab-link" data-tab="tab-2">
						<a id="age" title="We ask for your age only for statistical purposes." class="nav-link" href="javascript:;">Print</a>
					</li>
					<li class="nav-item tab-link" data-tab="tab-3">
						<a id="age" title="We ask for your age only for statistical purposes." class="nav-link" href="javascript:;">PDF</a>
					</li>
					<li class="nav-item tab-link" data-tab="tab-4">
						<a id="age" title="We ask for your age only for statistical purposes." class="nav-link" href="javascript:;">Send</a>
					</li>
				</ul>
				<div class="save-btn">
					<a href="javascript:;">Save</a>
				</div>
			</div>
			<div class="wp-paper">
				<div class="wp-quote-form-wrapper">
					<div class="wp-meta">
						<div class="wp-meta-left">
							<div class="wp-meta-from">
								<form>
									<label>From</label>
									<input type="text" name="your-name" value="" size="40" aria-required="true" aria-invalid="false" placeholder="Your Name">
									<textarea name="your-address" cols="40" rows="10" aria-required="true" aria-invalid="false" placeholder="Your address"></textarea>
								</form>
							</div>
							<div class="wp-meta-from wp-meta-to">
								<form>
									<label>To</label>
									<input type="text" name="your-name" value="" size="40" aria-required="true" aria-invalid="false" placeholder="Customer name">
									<textarea name="your-address" cols="40" rows="10" aria-required="true" aria-invalid="false" placeholder="Customer address"></textarea>
								</form>
							</div>
						</div>
						<div class="wp-meta-right">
							<div class="wp-invoice-quete">
								<div class="wp-upload-logo">
									<div id="upload_area" style="" class="empty yournamehere"></div>
									<div class="wp-clearfix">
										<label id="btnUploadLogo" class="input-file" style="">
											<b class="btn btn-xs right">Browse...</b>
											<input name="filename" type="file" id="logo_file_input" accept=".jpg, .jpeg, .gif, .bmp, .png">
										</label>
									</div>
								</div>
								<form>
									<input type="text" name="your-name" value="" size="40" aria-required="true" aria-invalid="false" placeholder="QUOTE">
								</form>
							</div>
							<div class="wp-form-invoice-dates">
								<div class="row align-items-center">
									<div class="col-sm-3">
										<label for="inputtext" class="col-form-label">Quote #</label>
									</div>
									<div class="col-sm-9">
										<input type="text" class="form-control" placeholder="0000001">
									</div>
								</div>
								<div class="row align-items-center po-number">
									<div class="col-sm-3">
										<label for="inputtext" class="col-form-label">P.O. #</label>
									</div>
									<div class="col-sm-9">
										<input type="text" class="form-control" placeholder="">
									</div>
								</div>
								<div class="row align-items-center">
									<div class="col-sm-3">
										<label for="inputPassword" class="col-form-label">Date</label>
									</div>
									<div class="col-sm-9">
										<input type="date" id="start" name="trip-start" value="2018-07-22" min="2018-01-01" max="2018-12-31">
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="wp-datatable wc_invoice_repeter">
						<?php
						$args = array(
							'post_type' => 'product',
							'posts_per_page' => -1,
							'post_status' => 'publish'
						);
						$loop = new WP_Query( $args );
						$product_html = '';
						$count = 1; 
						ob_start();
						if ( $loop->have_posts() ) {
							while ( $loop->have_posts() ) { $loop->the_post();
								$product_id = get_the_ID();
								$product_title = get_the_title();
								?>
								<option value="<?php echo $product_id; ?>"><?php echo $product_title; ?></option>
								<?php
							}
							$count++;
						} 
						wp_reset_postdata();
						$product_html .= ob_get_clean();
						?>
						<table id="repeatable-fieldset-one">
							<tbody>
								<tr>
									<th></th>
									<th>Item</th>
									<th>Description</th>
									<th>Unit Price</th>
									<th>Quantity</th>
									<th>Amount</th>	
								</tr>
								<tr>
									<td>
										<button tabindex="-1" type="button" class="button remove-row btnDeleteRow">
											<i class="wd-tools-icon close-icon"></i>
										</button>
									</td>
									<td>
										<select class="form-select" aria-label="Default select example" name="invoice_data[0][item]">
											<option value=""></option> 
											<?php echo $product_html; ?>
										</select>
									</td>
									<td>
										<textarea name="invoice_data[0][description]" aria-required="true" aria-invalid="false" placeholder=""></textarea>
									</td>
									<td>
										<input type="number" name="invoice_data[0][unit]" id="unit_price" class="text-right linePrice inputNumber" value="" autocomplete="nope" placeholder="0.00">
									</td>
									<td>
										<input type="number" name="invoice_data[0][qty]" id="qty" class="text-right lineQty inputNumber" value="" autocomplete="nope" placeholder="0.00">
									</td>
									<td>
										<input type="text" tabindex="-1" name="invoice_data[0][total]" id="total" class="lineTotal" placeholder="0.00" value="" readonly="">
									</td>

								</tr>
								<tr>
									<td>
										<button tabindex="-1" type="button" class="button remove-row btnDeleteRow">
											<i class="wd-tools-icon close-icon"></i>
										</button>
									</td>
									<td>
										<select class="form-select" aria-label="Default select example" name="invoice_data[1][item]"> 
											<option value=""></option> 
											<?php echo $product_html; ?>
										</select>
									</td>
									<td>
										<textarea name="invoice_data[1][description]" aria-required="true" aria-invalid="false" placeholder=""></textarea>
									</td>
									<td>
										<input type="number" name="invoice_data[1][unit]" id="unit_price" class="text-right linePrice inputNumber" value="" autocomplete="nope" placeholder="0.00">
									</td>
									<td>
										<input type="number" name="invoice_data[1][qty]" id="qty" class="text-right lineQty inputNumber" value="" autocomplete="nope" placeholder="0.00">
									</td>
									<td>
										<input type="text" tabindex="-1" name="invoice_data[1][total]" id="total" class="lineTotal" placeholder="0.00" value="" readonly="">
									</td>
								</tr>
							</tbody>
						</table>
						<div class="button-row">
							<button type="button" class="btn btn-xs btnAddRow" id="add-row" data-count="<?php echo $count; ?>">
								<i class="icon-new"></i><span>New Line</span>
							</button>
						</div>
						<div class="wc_ship_repeter">
							<div style="display: none;">
								<table>
									<tr class="empty-row screen-reader-text">
										<td>
											<button tabindex="-1" type="button" class="button remove-row btnDeleteRow">
												<i class="wd-tools-icon close-icon"></i>
											</button>
										</td>
										<td>
											<select class="form-select" aria-label="Default select example" name="invoice_data[rand_no][item]"> 
												<option value=""></option> 
												<?php echo $product_html; ?>
											</select>
										</td>
										<td>
											<textarea name="invoice_data[rand_no][description]" aria-required="true" aria-invalid="false" placeholder=""></textarea>
										</td>
										<td>
											<input type="number" name="invoice_data[rand_no][unit]" id="unit_price" class="text-right linePrice inputNumber" value="" autocomplete="nope" placeholder="0.00">
										</td>
										<td>
											<input type="number" name="invoice_data[rand_no][qty]" id="qty" class="text-right lineQty inputNumber" value="" autocomplete="nope" placeholder="0.00">
										</td>
										<td>
											<input type="text" tabindex="-1" name="invoice_data[rand_no][total]" id="total" class="lineTotal" placeholder="0.00" value="" readonly="">
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
					<div class="wp-foot">
						<div class="wp-foot-left">
							<label class="input-label">Notes</label>
							<textarea name="invoice_notes" id="invoice_notes" rows="1" class="growTextarea"></textarea>
						</div>
						<div class="wp-foot-right">
							<table>
								<tbody>
									<tr>
										<td class="sum-label">Subtotal</td>
										<td>0.00</td>
									</tr>
									<tr>
										<td class="sum-label">Total</td>
										<td>0.00</td>
									</tr>
									<tr>
										<td class="sum-label">Amount Paid</td>
										<td>0.00</td>
									</tr>
									<tr class="sum-balance">
										<td class="sum-label">Quote</td>
										<td>$0.00</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="wp-viewinvoice-form-wrapper">
					<div class="viewinvoice-address-logo">
						<div class="invoice-from-address">
							<p>Testsacasc</p>
						</div>
						<div class="invoice-from-logo">
							<img src="https://www.peerlessumbrella.com/wp-content/uploads/2022/05/www_skynova_com_1652935147.jpg" alt="">
						</div>
					</div>
					<div class="viewinvoice-toname-table">
						<div class="invoice-from-toname">
							<p>Testsacasc</p>
						</div>
						<div class="invoice-table">
							<h4>INVOICE</h4>
							<table>
								<tbody>
									<tr>
										<td>Invoice #</td>
										<td>0000001</td>
									</tr>
									<tr>
										<td>P.O. #</td>
										<td>1256514</td>
									</tr>
									<tr>
										<td>Invoice Date</td>
										<td>19/05/2022</td>
									</tr>
									<tr>
										<td>Due Date</td>
										<td>19/05/2022</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="viewinvoice-dataTable">
						<table>
							<tbody>
								<tr>
									<th>Item</th>
									<th>Description</th>
									<th>Unit Price</th>
									<th>Quantity</th>
									<th>Tax</th>
									<th>Amount</th>
								</tr>
								<tr>
									<td>Hours</td>
									<td>ewfcewfwefqewf</td>
									<td>5.00</td>
									<td>5.00</td>
									<td>5.00%</td>
									<td>25.00</td>
								</tr>
								<tr>
									<td>Hours</td>
									<td>fefewfewf</td>
									<td>150.00</td>
									<td>150.00</td>
									<td>5.00%</td>
									<td>22,500.00</td>
								</tr>
								<tr>
									<td class="invoice-notes" colspan="6"><u>NOTES:</u> feffefewfewf</td>
								</tr>
								<tr class="invoice-row">
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td>
										<div class="invoice-subtotal-table">
											<table>
												<tbody>
													<tr>
														<td>Subtotal</td>
														<td>22,525.00</td>
													</tr>
													<tr class="invoice-sum-tax">
														<td><span>+ qweqweqwdeTax (5.00%)</span></td>
														<td><span>1,126.25</span></td>
													</tr>
													<tr>
														<td>Total</td>
														<td>23,651.25</td>
													</tr>
													<tr>
														<td>Amount Paid</td>
														<td>0.00</td>
													</tr>
													<tr class="balance-due-headline">
														<td>Balance Due</td>
														<td>$23,651.25</td>
													</tr>
												</tbody>
											</table>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>


<script>
	jQuery( function() {
		jQuery( document ).tooltip({
			my: "top center",
			at: "bottom center",
		});
	} );

	jQuery( document ).ready(function() {
		jQuery( document ).on('click','.wc_invoice_repeter #add-row', function() {
			var count = jQuery(this).attr('data-count');
			count = parseInt(count) + 1;
			jQuery(this).attr('data-count',count);
			var row = jQuery( '.empty-row.screen-reader-text' ).html().replace(/rand_no/g, count);      
			if( jQuery('#repeatable-fieldset-one tbody>tr:last').length == 0  )  {    
				jQuery('#repeatable-fieldset-one tbody').append('<tr>'+row+'</tr>');
			} else {
				jQuery('#repeatable-fieldset-one tbody>tr:last').after('<tr>'+row+'</tr>');
			}
			return false;
		});

		jQuery(document).on('click','.wc_invoice_repeter .remove-row',function(){
			jQuery(this).parent().parent().remove();
			return false;
		});
		jQuery("body").on('click', '#btnToggleOptions', function () {
			jQuery("#wp-options").slideToggle();
		});
	});

</script>


<?php get_footer(); ?>
