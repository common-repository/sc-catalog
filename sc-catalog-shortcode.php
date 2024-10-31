<?php
/*
	SC Catalog
	Copyright 2011-2012 SARL SCOP Scil (email : contact@scil.coop)
	
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// Shortcode definition

function sc_catalog_shortcode_one( $item ) {
	$code = '<li class="sc-catalog-list-item sc-catalog-list-item-' . $item['id'] .'" data-id="' . $item['id'] .'">';
	if ( strlen( $item['image'] ) > 0 ) {
		$code .= '<img src="' . $item['image'] . '" />';
	}
	$code .= '<h3>' . $item['title'] . '</h3>';
	if ( strlen ( $item['catch'] ) > 0 ) {
		$code .= '<p class="sc-catalog-list-item-catch-phrase">' . $item['catch'] . '</p>';
	}
	if ( strlen ( $item['text'] ) > 0 ) {
	$code .= '<div class="sc-catalog-text" style="display:none;">' . wpautop($item['text']) . '</div>';
		$code .= '<div class="sc-catalog-clear sc-catalog-list-item-more"><a class="thickbox more-details" href="#">' . __( 'Read more', 'sc_cat' ) . '</a></div>';
	}
	$code .= '</li>';
	return $code;
}

function sc_catalog_shortcode( $attrs ) {
	if ( isset( $attrs['cat'] ) ) {
		$items = sc_catalog_get_by_category( $attrs['cat'] );
	} else {
		$items = sc_catalog_get_all();
	}
	$output = '<ul class="sc-catalog-list">';
	foreach ( $items as $item ) {
		$output .= sc_catalog_shortcode_one( $item );
	}
	$output .= '</ul>';
	$output .= '<div id="sc-catalog-details" style="display:none;"><div class="sc-catalog-details"></div></div>';
	$output .= '<script type="text/javascript" src="' . plugins_url( '/jquery_handle.js', __FILE__ ) . '"></script>';
	return $output;
}

// Register shortcode
add_shortcode( 'sc-catalog', 'sc_catalog_shortcode' );  // Generic
add_shortcode( 'catalog', 'sc_catalog_shortcode' );     // EN/ES
add_shortcode( 'catalogue', 'sc_catalog_shortcode' );   // FR
add_shortcode( 'קטלוג', 'sc_catalog_shortcode' );       // HE
add_shortcode( 'catalogus', 'sc_catalog_shortcode' );   // NL
add_shortcode( 'katalog', 'sc_catalog_shortcode' );     // DE

?>
