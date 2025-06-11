<?php

add_action( 'wp_head', 'wc_colors_pettle_css_func' );

function wc_colors_pettle_css_func(){
	?>
	<style>
		/*<------------ start-sub-header ------------>*/
		.save_palette .fa-spinner {
			display: none;
		}
		.palette-like-btn .fa-spinner {
			display: none;
		}
		.wc-search-platters {
			width: 100%;
		}
		.header {
			position: sticky;
			align-items: center;
			justify-content: center;
			display: flex;
			top: 0;
			left: 0;
			width: 100%;
			z-index: 999;
			background-color: transparent;
			transition: 0.6s all ease;
			transition: all .4s;
		}

		header.header.sticky {
			height: 100px;
		}

		header.header.sticky {
			background-color: rgb(0 0 0);
			position: fixed;
		}

		header.header.sticky .header-contant {
			padding: 15px 0;
		}

		.header-contant {
			display: flex;
			justify-content: space-between;
			align-items: center;
			width: 100%;
			border-bottom: 1px solid #dbdbdb;
			border-top: 1px solid #dbdbdb;
			padding: 10px 20px;
		}

		.header .logo-content {
			width: 100%;
			text-align: center;
		}   

		.header-contant .menu-wrapper {
			display: flex;
			align-items: center;
			grid-gap: 60px;
		}
		.header-contant .menu-wrapper ul.manu-bar {
			list-style: none;
			margin: 0;
		}
		.header-contant .search-palettes-container > i {
			margin: 0 5px 0;
			font-size: 20px;
			color: #000;
		}
		.header-contant .search-palettes-container{
			padding: 4px;
			border-radius: 14px;
			display: flex;
			transition: all 0.15s ease;
			position: relative;
			align-items: center;
			flex-grow: 1;
		}
		.header-contant .search-palettes-container input {
			border: none;
			flex-grow: 1;
			margin: 4px;
			height: 39px;
			color: #878787 !important;
			font-family: 'Poppins';
			font-size: 16px;
			position: relative;
		}
		.tax-color-category .search-palettes-container > i {
			display: none;
		}
		.tax-color-category .search-palettes-container a.tag {
			padding: 10px 16px;
			border-radius: 5px;
			margin: 4px;
			display: inline-flex;
			align-items: center;
			cursor: pointer;
			transition: all 0.15s ease;
			white-space: nowrap;
			color: black;
			text-transform: capitalize;
			background: #f2f2f3;
		}
		.tax-color-category .search-palettes-container a.tag > i.fa.fa-circle {
			margin-right: 10px;
		}
		.tax-color-category .search-palettes-container a.tag > i.fa.fa-close {
			margin-left: 10px;
			font-size: 14px;
			color: #b5b0b0;
		}
		.tax-color-category .search-palettes-container a.tag:hover {
			background-color:#ededee;
		}
		.tax-color-category .search-palettes-container a.tag > i.fa.fa-close:hover {
			color: #000;
		}
		.wc-search-platters .palette-card-colors{
			padding: 30px;
			box-shadow: rgb(0 0 0 / 5%) 0 0 0 1px, rgb(0 0 0 / 12%) 0 15px 30px 0px;
			display: none;
		}
		.wc-search-platters .palette-card-colors h2 {
			font-size: 16px;
			margin-bottom: 10px;
		}
		.wc-search-platters .palette-card-colors .megasearch_menu_color {
			display: flex;
			flex-wrap: wrap;
			margin: -4px;
		}
		.wc-search-platters .palette-card-colors .megasearch_menu_color .tag {
			padding: 10px 16px;
			border: 1px solid #bababa;
			border-radius: 8px;
			margin: 4px;
			display: inline-flex;
			align-items: center;
			cursor: pointer;
			transition: all 0.15s ease;
			white-space: nowrap;
			color: black;
			text-decoration: none;
			text-transform: capitalize;
		}
		.wc-search-platters .palette-card-colors .megasearch_menu_color .tag:hover{
			border: 1px solid #000;
		}
		.wc-search-platters .palette-card-colors .megasearch_menu_color .tag i {
			font-size: 14px;
			margin-right: 10px;
		}
		.wc-search-platters .palette-card-colors.active {
			display: block;
		}

		/*<------- dropdown ------> */

		.side-bar-content {
			display: block !important;
		}

		.side-bar-content {
			background-image: url(../images/sidebar.png);
			background-blend-mode: lighten;
			background-color: #fff;
			border: 1px solid #ececec;
			width:295px;
			height: 100%;
			position: fixed;
			top: 0;
			right: -300px;
			z-index: 9999;
			transition: 1s all ease;
		}

		.side-bar-content.active {
			right: 0;
		}

		.mobile-menu-icon.active a.open-menu,
		.mobile-menu-icon .side-bar-close {
			display: none;
		}

		.mobile-menu-icon.active .side-bar-close {
			display: block;
		}

		.side-bar-text-content {
			overflow-y: scroll;
			height: 100%;
			-ms-overflow-style: none;
			scrollbar-width: none;
		}

		.side-bar-text-content::-webkit-scrollbar {
			display: none;
		}
		.side-bar-text-content .palettes-sidebar_palettes {
			position: relative;
			padding: 25px 20px;
			vertical-align: middle;
			height: 100%;
		}
		.side-bar-text-content .Explore-tabs {
			box-shadow: rgb(0 0 0 / 8%) 0 1px;
			width: 100%;
			position: relative;
			padding: 11px 15px;
			z-index: 6;
			font-size: 16px;
			text-align: center;
			font-weight: 800;
			display: flex;
			align-items: center;
			justify-content: space-between;
		}
		.side-bar-text-content .palette ul.palette_colors {
			padding: 0;
			display: flex;
			align-items: center;
			margin: 0;
			border: 1px solid #000000;
			border-radius: 5px;
			width: 100%;
			overflow: hidden;
			justify-content: flex-end;
		}
		.side-bar-text-content .palette ul.palette_colors li{
			list-style: none;
			margin: 0;
			display: flex;
			align-items: center;
			justify-content: center;
			min-height: 42px;
			flex-grow: 1;
			transition: all 0.1s ease-in-out;
			cursor: pointer;
			position: relative;
			overflow: hidden;
			flex-basis: 2px;
			text-align: center;
		}

		.wc-colors li.wc-sinle-color:hover,
		.side-bar-text-content .palette ul.palette_colors li:hover {
			flex-basis: 70px;
		}
		.side-bar-text-content .palette .palette_text {
			color: black;
			font-size: 0;
			padding: 0 10px 0 5px;
			line-height: 27px;
			cursor: pointer;
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
			font-weight: 500;
		}
		.side-bar-text-content .palettes-sidebar_palettes .palettes-sidebar-loader {
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			text-align: center;
		}
		.palettes-sidebar_palettes i.fa-spin {
			text-align: center;
			font-size: 22px;
			color: #000;
		}

		.side-bar-text-content .palettes-sidebar_palettes .palette p {
			text-align: center;
			font-size: 16px;
			font-weight: 600;
			color: #9d9898;
		}
		.logged-in .side-bar-content {
			top: 30px;
		}
		/*<------------ End-sub-header ------------>*/

		ul.wc-colors {
			padding: 0;
			display: flex;
			align-items: center;
			margin: 0;
			border: 1px solid #00000021;
			border-radius: 15px;
			width: 100%;
			overflow: hidden;
			justify-content: flex-end;
		}

		ul.wc-colors li.wc-sinle-color {
			list-style: none;
			margin: 0;
			display: flex;
			align-items: center;
			justify-content: center;
			min-height: 130px;
			flex-grow: 1;
			transition: all 0.1s ease-in-out;
			cursor: pointer;
			position: relative;
			overflow: hidden;
			flex-basis: 1px;
		}

		ul.wc-colors li.wc-sinle-color span.color-name, .palette ul.palette_colors span.color-name {
			transform: scale(0);
			transition: all .4s;
			color: #000;
			font-family: 'Lato';
			font-weight: 700;
			text-transform: uppercase;
			font-size: 0px;
			display: block;
		}
		ul.wc-colors li.wc-sinle-color.has-dark-color span.color-name,   .palette ul.palette_colors li.has-dark-color span.color-name {
			color: #fff;
		}

		ul.wc-colors li.wc-sinle-color span.tooltiptext, .palette ul.palette_colors li span.tooltiptext{
			color: #000;
		}
		
		.wc-color-palette-wrap ul.wc-colors li.wc-sinle-color:hover, .palette ul.palette_colors li:hover {
			flex-basis: 80px;
		}

		.wc-color-palette-wrap {
			display: grid;
			grid-template-columns: repeat(1, 1fr);
			grid-gap: 35px;
			width: 100%;
			margin-top: 50px;
		}
		.wc-color-palette-wrap .palette p {
			margin: 0;
			font-size: 16px;
			font-weight: 600;
			margin-top: -15px;
			padding-left: 10px;
			color: #fc0a0ab5;
			font-family: 'Poppins';
		}

		ul.wc-colors li.wc-sinle-color:hover .color-name, .palette ul.palette_colors li:hover .color-name {
			transform: scale(1);
			font-size: 14px;
		}
		.palette ul.palette_colors li:hover .color-name{
			
			font-size: 12px;
			line-height: 1;

		}
		.wc-color-palette  .popup {
			text-align: end;
		}

		.wc-color-palette .popup a.color-popup {
			color: #9aa2ab;
		}
		.color-frame {
			padding: 0;
			border-radius: 15px;
			overflow: hidden;
		}
		.color-frame .popup-title {
			padding: 12px;
			background: #f3eeee;
		}

		.color-frame .popup-title h2 {
			text-align: center;
			font-size: 18px;
			margin: 0;
		}

		.color-frame .popup-content ul.color-types {
			padding: 0;
			margin: 0;
		}
		.color-frame .popup-content ul.color-types li.code{
			position:relative;
			cursor: pointer;
		}
		.color-frame .popup-content ul.color-types li.code:not(:last-child){
			border-bottom: 1px solid #00000017;
		}

		.color-frame .popup-content ul.color-types li.code {
			padding: 12px 30px;
			list-style: none;
			margin: 0;
		}

		.color-frame .popup-content ul.color-types li.code span.code-type {
			display: block;
			font-weight: 500;
			color: #777;
			font-family: 'Poppins';
		}

		.color-frame .popup-content ul.color-types li.code  span.color-code {
			font-size: 16px;
			color: #000;
			font-family: 'Poppins';
		}

		.color-frame .popup-footer {
			padding: 15px;
		}

		.color-frame .popup-footer li.wc-sinle-color {
			min-height: 45px;
		}

		.color-frame .popup-footer li.wc-sinle-color span.color-name {
			display: none;
		}
		.color-frame  .popup-modal-dismiss {
			position: absolute;
			top: 15px;
			right: 15px;
		}
		.fav-pallete-name{
			font-size: 12px;
			text-transform: uppercase;
			font-family: 'Poppins';
			color: #000;
			font-weight: 700;
			margin-bottom: 10px;
		}
		.pallete-name{
			font-size: 16px;
			text-transform: uppercase;
			font-family: 'Poppins';
			color: #000;
			font-weight: 700;
			margin-bottom: 10px;
		}
		ul.wc-colors li.wc-sinle-color span.tooltiptext {
			color: #000;
			position: absolute;
			background: #ffffffd9;
			border-radius: 4px 4px 0 0;
			padding: 3px 7px;
			z-index: 2;
			bottom: 0;
			font-weight: 600;
			font-family: 'Poppins';
			font-size: 0px;
			opacity: 0;
		}
		ul.wc-colors li.wc-sinle-color span.tooltiptext.active{
			opacity: 1;
			font-size: 12px;
		}
		.color-frame .popup-content ul.color-types.has-dark-color li.code span.code-type,
		.color-frame .popup-content ul.color-types.has-dark-color li.code span.color-code {
			color: #fff;
		}
		.color-frame .popup-content ul.color-types.has-dark-color li.code:not(:last-child) {
			border-bottom: 1px solid #ffffff29;
		}
		.color-frame .popup-footer li.wc-sinle-color.active:after {
			content: '';
			display: block;
			width: 8px;
			height: 8px;
			background: #1b1a1a;
			border-radius: 50px;
		}
		.color-frame .popup-footer li.wc-sinle-color.has-dark-color.active:after {
			background: #fff;
		}
		.color-frame .popup-content ul.color-types li span.tooltiptext {
			position: absolute;
			right: 10px;
			top: 50%;
			transform: translateY(-50%);
			background: #ffffffc7;
			color: #000;
			font-family: 'Poppins';
			font-size: 12px;
			font-weight: 700;
			border-radius: 3px;
			padding: 3px 7px;
		}
		.palette-card-btns {
			display: flex;
			align-items: center;
			justify-content: end;
			padding-right: 4px;
			padding-top: 6px;
		}
		.palette-card-btns a.palette-like-btn {
			color: #9aa2ab;
			padding-right: 10px;
		}
		.palette-card-btns a.palette-like-btn {
			color: #9aa2ab;
			padding-right: 13px;
			font-size: 14px;
			font-weight: 500;
			font-family: 'Lato';
		}
		.palette-card-btns a.palette-like-btn i {
			font-weight: 500;
			font-size: 15px;
		}
		.palette-card-btns a.palette-like-btn i.fa-spin {
			font-family: 'FontAwesome';		}

			.wc-color-palette.active .palette-card-btns a.palette-like-btn i {
				color: #3e3d3d;
				font-weight: 800;
			}


			.menu-nav {
				position: relative;
			}
			.menu-nav .three-dots:after {
				cursor: pointer;
				color: #9aa2ae;
				content: '\f141';
				font-size: 20px;
				font-family: 'FontAwesome';
			}
			.menu-nav .dropdown {
				left: 50%;
				transform: translateX(-50%);
				outline: none;
				opacity: 0;
				z-index: -1;
				max-height: 0;
				background: white;
				border-radius: 14px;
				box-shadow: rgb(0 0 0 / 5%) 0 0 0 1px, rgb(0 0 0 / 12%) 0 15px 30px 0px;
				position: absolute;
				min-width: 220px;
				transition: opacity 0.1s, z-index 0.1s, max-height: 5s;
			}
			.menu-nav .dropdown:before, .menu-nav .dropdown:after {
				border-radius: 0 4px 0 0;
				margin-left: -5px;
				left: 50%;
				transform: rotate(135deg);
				content: '';
				height: 12px;
				width: 12px;
				position: absolute;
			}
			.menu-nav .dropdown:before {
				top: -7px;
				background: rgba(0, 0, 0, 0.15);
			}
			.menu-nav .dropdown:after {
				top: -6px;
				background: white;
			}

			.menu-nav .dropdown-container:focus {
				outline: none;
			}

			.menu-nav .dropdown-container .dropdown.active {
				opacity: 1;
				z-index: 100;
				max-height: 100vh;
				transition: opacity 0.2s, z-index 0.2s, max-height: 0.2s;
			}
			.menu-nav .dropdown ul {
				list-style: none;
				margin: 0;
				display: block;
				white-space: nowrap;
				padding: 8px;
			}
			.menu-nav .dropdown ul li{
				margin: 0;
			}
			.menu-nav .dropdown ul li a {
				padding: 8px 16px 8px 12px;
				display: flex;
				align-items: center;
				color: black;
				font-size: 16px;
				border-radius: 6px;
				font-family: 'Lato';
			}
			.menu-nav .dropdown ul li a:hover {
				background: #f2f2f3;
			}
			.menu-nav .dropdown ul li a i {
				font-size: 18px;
				margin-right: 14px;
				font-weight: 400;
				font-family: 'FontAwesome';
			}
			.menu-nav .dropdown ul li a i.check {
				margin-right: 0;
				margin-left: 10px;
				color: #4BB543;
			}
			.menu-nav .dropdown ul li a i.close {
				margin-right: 0;
				margin-left: 10px;
				color: red;
			}
			.menu-nav .dropdown ul li a i.fa-heart::before{
				font-weight: 100;
				font-family: 'Font Awesome 6 Free';
			}
			.menu-nav .dropdown-container .dropdown ul li.active a i.fa-heart::before{
				font-weight: 800;
			}
			/*popup*/
			.mfp-container{
				padding: 0;
			}
			.white-popup {
				position: relative;
				background: #FFF;
				padding: 0;
				width: 100%;
				transition: 1s all;
				overflow: hidden;
			}
			.white-popup ul.palette_colors-fullscreen {
				list-style: none;
				display: flex;
				padding: 0;
				margin: 0;
				width: 100%;
				height: 100%;
				overflow: hidden;
			}
			.white-popup ul.palette_colors-fullscreen li {
				list-style: none;
				margin: 0;
				display: flex;
				align-items: center;
				justify-content: center;
				height: 100vh;
				flex-grow: 1;
				transition: all .2s ease-in-out;
				cursor: pointer;
				position: relative;
				overflow: hidden;
				flex-basis: 1px;
				animation-name: show;
				animation-duration: 1s;
				animation-iteration-count: 1;
				animation-fill-mode: forwards;
				transform: scaleY(0);
			}
	/*	.white-popup ul.palette_colors-fullscreen li:nth-child(1) {
			animation-delay: 0.3s;
		}

		.white-popup ul.palette_colors-fullscreen li:nth-child(2) {
			animation-delay: 0.4s;
		}

		.white-popup ul.palette_colors-fullscreen li:nth-child(3) {
			animation-delay: 0.5s;
		}

		.white-popup ul.palette_colors-fullscreen li:nth-child(4) {
			animation-delay: 0.6s;
		}

		.white-popup ul.palette_colors-fullscreen li:nth-child(5) {
			animation-delay: 0.7s;
		}

		.white-popup ul.palette_colors-fullscreen li:nth-child(6) {
			animation-delay: 0.8s;
		}

		.white-popup ul.palette_colors-fullscreen li:nth-child(7) {
			animation-delay: 0.9s;
		}

		.white-popup ul.palette_colors-fullscreen li:nth-child(8) {
			animation-delay: 0.10s;
		}

		.white-popup ul.palette_colors-fullscreen li:nth-child(9) {
			animation-delay: 0.11s;
		}*/

		@keyframes show {
			0% {
				transform: scaleY(0);
				transform-origin: bottom;

			}
			100% {
				transform: scaleY(1);
				transform-origin: bottom;

			}

		}
		.white-popup button.mfp-close {
			right: 30px;
			top: 50px;
			height: 20px;
			width: 20px;
			padding: 17px;
			display: flex;
			align-items: center;
			justify-content: center;
			background: white;
			box-shadow: rgb(0 0 0 / 8%) 0 0 0 1px, rgb(0 0 0 / 5%) 0 10px 10px -5px;
			border-radius: 50%;
			cursor: pointer;
			color: black;
		}

		@media (max-width:767px) {
			.wc-color-palette-wrap {
				grid-template-columns: repeat(1, 1fr);
			}
			.wc-search-platters .palette-card-colors {
				padding: 14px;
			}
			.wc-search-platters .palette-card-colors .megasearch_menu_color .tag {
				padding: 7px 12px;
			}
		}
		@media (max-width:1299px) {
			.wc-color-palette-wrap {
				padding: 20px;
			}
		}
		@media (max-width:575px) {
			.header-contant .search-palettes-container input{
				font-size: 15px;
				height: 24px;
				margin: 0;
			}
			.header-contant .search-palettes-container > i{
				font-size: 16px;
			}
		}

		/*------------------- View Product Button ----------------------*/
		.color_url {text-align: center;}

		.color_url a {
			font-family: 'Poppins';
			font-weight: 500;
			color: #242424;
			background: #fdfdfd;
			padding: 8px 20px;
			font-size: 15px;
			border-radius: 6px;
			border: 1px solid #000;
			display: inline-block;
		}

		.color_url a:after {content: "\f06e";font-family: 'Font Awesome 5 Free';font-weight: 700;display: inline-block;vertical-align: middle;margin-left: 6px;font-size: 14px;}

		.color_url a:hover {
			background: #242424;
			color: #fff;
		}
		
	</style>
	<?php
}

/*single color pattel css*/

add_action('wp_head','custom_single_colors_css_function');

function custom_single_colors_css_function(){

	if ( is_singular( 'colors' ) ) {
		?>
		<style type="text/css">
			.row.content-layout-wrapper{
				display: block;
			}
			.wc-single-palette .wc-plalette-top {
				display: flex;
				align-items: center;
				justify-content: space-between;
				width: 100%;
			}
			.wc-single-palette .wc-plalette-top h2.wc-palette-name {
				font-size: 50px;
				font-weight: 800;
				margin: 0;
			}
			.wc-single-palette .wc-plalette-top .palette-page_button a.palette-like-btn {
				border: 1px solid #d8d8da;
				padding: 12px 20px;
				font-size: 16px;
				font-weight: 600;
				height: 45px;
				display: flex;
				align-items: center;
				justify-content: center;
				border-radius: 10px 0px 0px 10px;
			}
			.wc-single-palette .wc-plalette-top .palette-page_button a.palette-like-btn i {
				font-size: 18px;
				font-weight: 300;
			}
			.wc-single-palette .palette-page_button{
				display: flex;
				align-items: center;
				justify-content: center;
			}
			.wc-single-palette .palette-page_button.active a.palette-like-btn i {
				color: #000;
				font-weight: 800;
			}
			.wc-plalette-top .menu-nav {
				position: relative;
				background: #f7f7f8;
				padding: 10px 20px;
				height: 45px;
				display: flex;
				align-items: center;
				justify-content: center;
				border: 1px solid #d8d8da;
				border-width: 1px 1px 1px 0;
				border-color: #d8d8da;
				border-radius: 0 10px 10px 0;
			}
			.wc-plalette-top .menu-nav .three-dots:after {
				cursor: pointer;
				color: #000000;
				content: '\f078';
				font-size: 16px;
				font-family: 'FontAwesome';
			}
			.wc-plalette-top .menu-nav .dropdown {
				left: 50%;
				transform: translateX(-50%);
				outline: none;
				opacity: 0;
				z-index: -1;
				max-height: 0;
				background: white;
				border-radius: 14px;
				box-shadow: rgb(0 0 0 / 5%) 0 0 0 1px, rgb(0 0 0 / 12%) 0 15px 30px 0px;
				position: absolute;
				min-width: 220px;
				transition: opacity 0.1s, z-index 0.1s, max-height: 5s;
			}
			.wc-plalette-top .menu-nav .dropdown:before, .menu-nav .dropdown:after {
				border-radius: 0 4px 0 0;
				margin-left: -5px;
				left: 50%;
				transform: rotate(135deg);
				content: '';
				height: 12px;
				width: 12px;
				position: absolute;
			}
			.wc-plalette-top .menu-nav .dropdown:before {
				top: -7px;
				background: rgba(0, 0, 0, 0.15);
			}
			.wc-plalette-top .menu-nav .dropdown:after {
				top: -6px;
				background: white;
			}

			.wc-plalette-top .menu-nav .dropdown-container:focus {
				outline: none;
			}

			.wc-plalette-top .menu-nav .dropdown-container .dropdown.active {
				opacity: 1;
				z-index: 100;
				max-height: 100vh;
				transition: opacity 0.2s, z-index 0.2s, max-height: 0.2s;
			}
			.wc-plalette-top .menu-nav .dropdown ul {
				list-style: none;
				margin: 0;
				display: block;
				white-space: nowrap;
				padding: 8px;
			}
			.wc-plalette-top .menu-nav .dropdown ul li{
				margin: 0;
			}
			.wc-plalette-top .menu-nav .dropdown ul li a {
				padding: 8px 16px 8px 12px;
				display: flex;
				align-items: center;
				color: black;
				font-size: 16px;
				border-radius: 6px;
				font-family: 'Lato';
			}
			.wc-plalette-top .menu-nav .dropdown ul li a:hover {
				background: #f2f2f3;
			}
			.wc-plalette-top .menu-nav .dropdown ul li a i {
				font-size: 18px;
				margin-right: 14px;
				font-weight: 400;
				font-family: 'FontAwesome';
			}
			.palette-big_copy ul {
				list-style: none;
				padding: 0;
				display: flex;
				align-items: center;
				margin: 0;
				border-radius: 15px;
				width: 100%;
				overflow: hidden;
				margin-top: 50px;
			}
			.palette-big_copy ul li.wc-sinle-color {
				width: 100%;
				margin: 0;
				height: 400px;
				text-align: center;
				display: flex;
				flex-direction: column;
				align-items: center;
				justify-content: flex-end;
				flex-grow: 1;
				padding:20px;
			}
/* 			.palette-big_copy ul li.wc-sinle-color span.color-name {
				font-size: 22px;
				color: #000;
				text-transform: uppercase;
			} */
			.palette-big_copy ul li.wc-sinle-color.has-dark-color span.color-name.has-dark-color {
				color: #fff;
			}
			.wc-single-palette-card {
				margin-top: 120px;
			}
			.wc-single-palette-card h2 {
				text-align: center;
				font-size: 33px;
				font-weight: 900;
				margin-bottom: 50px;
			}
			.wc-single-card-grid{
				display: grid;
				grid-template-columns: repeat(4, 1fr);
				grid-gap: 20px 30px;
			}
			.wc-single-card-grid .palette-card-colors ul {
				list-style: none;
				padding: 0;
				display: flex;
				align-items: center;
				margin: 0;
				border: 1px solid #00000021;
				border-radius: 15px;
				width: 100%;
				overflow: hidden;
				justify-content: flex-end;
			}
			.wc-single-card-grid .palette-card-colors > ul li {
				list-style: none;
				margin: 0;
				display: flex;
				align-items: center;
				justify-content: center;
				min-height: 130px;
				flex-grow: 1;
				transition: all 0.1s ease-in-out;
				cursor: pointer;
				position: relative;
				overflow: hidden;
				flex-basis: 1px;
			}
			.wc-single-card-grid .palette-card-colors ul li:hover {
				flex-basis: 80px;
			}
			.wc-single-card-grid .palette-card-colors ul li span.color-name {
				transform: scale(0);
				transition: all .4s;
				color: #000;
				font-family: 'Lato';
				font-weight: 700;
				text-transform: uppercase;
				font-size: 0px;
				display: block;
			}
			.wc-single-card-grid .palette-card-colors ul li.has-dark-color span.color-name {
				color: #fff;
			}
			.wc-single-card-grid .palette-card-colors ul li:hover .color-name {
				transform: scale(1);
				font-size: 14px;
			}
			.wc-single-palette-card .palette-card-btns {
				display: flex;
				align-items: center;
				justify-content: end;
				padding-right: 4px;
				padding-top: 6px;
			}
			.wc-single-palette-card .palette-card-btns a.palette-like-btn {
				color: #9aa2ab;
				padding-right: 10px;
			}
			.wc-single-palette-card .palette-card-btns a.palette-like-btn {
				color: #9aa2ab;
				padding-right: 13px;
				font-size: 14px;
				font-weight: 500;
				font-family: 'Lato';
			}
			.wc-single-palette-card .palette-card-btns a.palette-like-btn i {
				font-weight: 500;
				font-size: 15px;
			}

			.wc-single-palette-card .palette-card-colors .menu-nav {
				position: relative;
			}
			.wc-single-palette-card .palette-card-colors .menu-nav .three-dots:after {
				cursor: pointer;
				color: #9aa2ae;
				content: '\f141';
				font-size: 20px;
				font-family: 'FontAwesome';
			}
			.wc-single-palette-card .palette-card-colors .menu-nav .dropdown {
				left: 50%;
				transform: translateX(-50%);
				outline: none;
				opacity: 0;
				z-index: -1;
				max-height: 0;
				background: white;
				border-radius: 14px;
				box-shadow: rgb(0 0 0 / 5%) 0 0 0 1px, rgb(0 0 0 / 12%) 0 15px 30px 0px;
				position: absolute;
				min-width: 220px;
				transition: opacity 0.1s, z-index 0.1s, max-height: 5s;
			}
			.wc-single-palette-card .palette-card-colors .menu-nav .dropdown:before, .menu-nav .dropdown:after {
				border-radius: 0 4px 0 0;
				margin-left: -5px;
				left: 50%;
				transform: rotate(135deg);
				content: '';
				height: 12px;
				width: 12px;
				position: absolute;
			}
			.wc-single-palette-card .palette-card-colors .menu-nav .dropdown:before {
				top: -7px;
				background: rgba(0, 0, 0, 0.15);
			}
			.wc-single-palette-card .palette-card-colors .menu-nav .dropdown:after {
				top: -6px;
				background: white;
			}

			.wc-single-palette-card .palette-card-colors .menu-nav .dropdown-container:focus {
				outline: none;
			}

			.wc-single-palette-card .palette-card-colors .menu-nav .dropdown-container .dropdown.active {
				opacity: 1;
				z-index: 100;
				max-height: 100vh;
				transition: opacity 0.2s, z-index 0.2s, max-height: 0.2s;
			}
			.wc-single-palette-card .palette-card-colors .menu-nav .dropdown ul {
				list-style: none;
				margin: 0;
				display: block;
				white-space: nowrap;
				padding: 8px;
			}
			.wc-single-palette-card .palette-card-colors .menu-nav .dropdown ul li{
				margin: 0;
			}
			.wc-single-palette-card .palette-card-colors .menu-nav .dropdown ul li a {
				padding: 8px 16px 8px 12px;
				display: flex;
				align-items: center;
				color: black;
				font-size: 16px;
				border-radius: 6px;
				font-family: 'Lato';
			}
			.wc-single-palette-card .palette-card-colors .menu-nav .dropdown ul li a:hover {
				background: #f2f2f3;
			}
			.wc-single-palette-card .palette-card-colors .menu-nav .dropdown ul li a i {
				font-size: 18px;
				margin-right: 14px;
				font-weight: 400;
				font-family: 'FontAwesome';
			}

			.palette-big_copy .single_colors li.wc-sinle-color:hover{
				flex-basis: 150px;
			}






			@media only screen and (max-width:991px) {
				.wc-single-card-grid {
					grid-template-columns: repeat(2, 1fr);
				}
			}
			@media only screen and (max-width:767px) {
				.wc-single-card-grid {
					grid-template-columns: repeat(1, 1fr);
				}
				.palette-big_copy ul li.wc-sinle-color{
					height: 160px;
				}
				.palette-big_copy ul li.wc-sinle-color span.color-name{
					font-size: 18px;
				}
			}
			@media only screen and (max-width:575px) {
				.palette-big_copy ul{
					flex-wrap: wrap;
				}
				.wc-single-palette-card {
					margin-top: 75px;
				}
				.wc-single-palette-card h2 {
					font-size: 30px;
					margin-bottom: 30px;
				}
				.wc-single-palette .wc-plalette-top {
					justify-content: center;
					flex-wrap: wrap;
				}
				.wc-single-palette .wc-plalette-top h2.wc-palette-name {
					font-size: 40px;
					margin-bottom: 14px;
				}
			}


			/*------------------- View Product Button ----------------------*/
			.color_url {text-align: center;}

			.color_url a {
				font-family: 'Poppins';
				font-weight: 500;
				color: #242424;
				background: #fdfdfd;
				padding: 8px 20px;
				font-size: 15px;
				border-radius: 6px;
				border: 1px solid #000;
				display: inline-block;
			}

			.color_url a:after {content: "\f06e";font-family: 'Font Awesome 5 Free';font-weight: 700;display: inline-block;vertical-align: middle;margin-left: 6px;font-size: 14px;}

			.color_url a:hover {
				background: #242424;
				color: #fff;
			}
		</style>
		<?php
	}
}


add_action( 'wp_footer' ,'custom_single_colors_js_js_func' );


function custom_single_colors_js_js_func(){
	?>
	<script type="text/javascript">
		jQuery(document).ready(function(){
			jQuery('.open-popup-link').magnificPopup({
				type: 'inline',
				midClick: true,
				mainClass: 'mfp-fade'
			});
		});
	</script>
	<?php
}