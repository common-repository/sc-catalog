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

// Data manipulation functions

function sc_catalog_cat( $cat ) {
	if ( $cat != "" ) {
		return $cat;
	} else {
		return __( 'No category', 'sc_cat' );
	}
}

/** Add a new item in database.
 * @param string $title Item title.
 * @param string $catch Item catch phrase.
 * @param string $image Item image location.
 * @param string $text Item description.
 * @param string $order Optional, default is 0.
 * Item display order. If 0 order is set to last.
 * @return Inserted item as an associative array with updated id and order.
 */
function sc_catalog_add_item( $title, $catch, $image, $text, $category = "",
                              $order = 0 ) {
	global $wpdb;
	if ( $order == 0 ) {
		$query = "SELECT max(disp_order) FROM " . _sc_catalog_table_ctl();
		$order = $wpdb->get_var( $query, 0, 0 );
		if ( $order == 0 ) {
			$order = 1;
		} else {
			$order++;
		}
	}
	$rows_affected = $wpdb->insert( _sc_catalog_table_ctl(),
	                                array( 'title' => $title,
	                                       'image' => $image,
	                                       'catch' => $catch,
	                                       'text'  => $text,
	                                       'disp_order' => $order,
	                                       'category' => $category ) );
	if ( $rows_affected == 0 ) {
		return false;
	}
	// Get inserted id
	$query = $wpdb->prepare( "SELECT * FROM " . _sc_catalog_table_ctl() . " WHERE "
	                         . " title = %s AND image = %s AND "
	                         . "catch = %s AND text = %s AND disp_order = %d "
	                         . "ORDER BY id DESC", $title, $image,
	                         $catch, $text, $order );
	$row = $wpdb->get_row( $query, ARRAY_A );
	return $row;
}

function sc_catalog_edit_item ( $id, $title, $catch, $image, $text, $order,
                                $category ) {
	global $wpdb;
	$rows_affected = $wpdb->update( _sc_catalog_table_ctl(),
	                                array( 'title' => $title,
	                                       'image' => $image,
	                                       'catch' => $catch,
	                                       'text'  => $text,
	                                       'disp_order' => $order,
	                                       'category' => $category ),
	                                array( 'id' => $id ) );
	if ( $rows_affected == 0 ) {
		// Check if the object is the same as in database
		$prev_item = sc_catalog_get_item( $id );
		if ( $prev_item['title'] == $title
		     && $prev_item['image'] == $image
		     && $prev_item['catch'] == $catch
		     && $prev_item['text'] == $text
		     && $prev_item['disp_order'] == $order
		     && $prev_item['category'] == $category ) {
			return true;
		}
	}
	return $rows_affected > 0;
}

function sc_catalog_delete_item ( $id ) {
	global $wpdb;
	$item = sc_catalog_get_item( $id );
	if ( ! $item ) {
		return false;
	}
	$query = $wpdb->prepare( "DELETE FROM " . _sc_catalog_table_ctl()
	                           . " WHERE id = %d", $id );
	$rows_affected = $wpdb->query($query);
	return $rows_affected > 0;
}

function sc_catalog_get_item( $id ) {
	global $wpdb;
	$query = $wpdb->prepare( "SELECT * FROM " . _sc_catalog_table_ctl()
	                         . " WHERE id = %d", $id );
	$row = $wpdb->get_row( $query, ARRAY_A );
	return $row;
}

function sc_catalog_get_all() {
	global $wpdb;
	$query = "SELECT * FROM " . _sc_catalog_table_ctl()
	         . " ORDER BY disp_order" );
	$rows = $wpdb->get_results( $query, ARRAY_A );
	return $rows;
}
function sc_catalog_get_categories() {
	global $wpdb;
	$query = "SELECT * FROM " . _sc_catalog_table_ctg() 
	         . " ORDER BY id DESC" );
	$rows = $wpdb->get_results( $query, ARRAY_A );
	return $rows;
}

function sc_catalog_get_by_categories( $cats ) {
	global $wpdb;
	$size = count( $cats );
	$req = "SELECT * FROM " . _sc_catalog_table() . " WHERE category IN (";
	for ( $i = 0; $i < $size; $i++ ) {
		$req .= "%s, ";
	}
	$req = substr( $req, 0, -2 );
	$req .= ") ORDER BY disp_order";
	$query = $wpdb->prepare( $req, $cats );
	$row = $wpdb->get_results( $query, ARRAY_A );
	return $row;
}

function sc_catalog_move_up( $id ) {
	/* Move up: decrease disp_order */
	global $wpdb;
	$item = sc_catalog_get_item( $id );
	if ( ! $item ) {
		return false;
	}
	// Get the order just below the current one
	$prev_order_query = $wpdb->prepare( "SELECT max(disp_order) FROM "
	                                    . _sc_catalog_table_ctl() . " WHERE "
	                                    . "disp_order < %d",
	                                    $item['disp_order'] );
	$prev_order = $wpdb->get_var( $prev_order_query, 0, 0);
	if ( $prev_order !== NULL ) {
		// Increase order of previous item
		$wpdb->query( $wpdb->prepare( "UPDATE " . _sc_catalog_table_ctl() . " SET "
		                              . "disp_order = %d WHERE disp_order = %d",
		                              $prev_order + 1, $prev_order ) );
		// Set current item order
		$wpdb->query( $wpdb->prepare( "UPDATE " . _sc_catalog_table_ctl() . " SET "
		                              . "disp_order = %d WHERE id = %d",
		                              $prev_order, $id ) );
	} else {
		return false; // selected item is already the first
	}
	return true;
}
function sc_catalog_add_category( $name ) {
	global $wpdb;
	$rows_affected = $wpdb->insert( _sc_catalog_table_ctg(),
	                                array( 'name' => $name ) );
	if ( $rows_affected == 0 ) {
		return false;
	} else {
		return true;
	}
}

function sc_catalog_delete_category( $id ) {
	global $wpdb;
	$query = $wpdb->prepare( "DELETE FROM " . _sc_catalog_table_ctg()
	                           . " WHERE id = %d", $id );
	$rows_affected = $wpdb->query($query);
	return $rows_affected > 0;
}

function sc_catalog_get_category_by_id( $id ) {
	global $wpdb;
	$query = $wpdb->prepare( "SELECT * FROM " . _sc_catalog_table_ctg()
	                         . " WHERE id = %d", $id );
	$row = $wpdb->get_row( $query, ARRAY_A );
	return $row['name'];
}
function sc_catalog_update_category( $name, $id ) {
	
	global $wpdb;
	$query = $wpdb->prepare( "UPDATE " . _sc_catalog_table_ctg() . " SET "
		                              . "name = %s WHERE id = %d",
		                              $name, $id );
	
	$rows_affected = $wpdb->query($query);
	return $rows_affected > 0;
}

function sc_catalog_move_down( $id ) {
	/* Move down: increase disp_order */
	global $wpdb;
	$item = sc_catalog_get_item( $id );
	if ( ! $item ) {
		return false;
	}
	// Get the order just above the current one
	$next_order_query = $wpdb->prepare( "SELECT min(disp_order) FROM "
	                                    . _sc_catalog_table_ctl() . " WHERE "
	                                    . "disp_order > %d",
	                                    $item['disp_order'] );
	$next_order = $wpdb->get_var( $next_order_query, 0, 0);
	if ( $next_order !== NULL ) {
		// Decrease order of the next item
		$wpdb->query( $wpdb->prepare( "UPDATE " . _sc_catalog_table_ctl() . " SET "
		                              . "disp_order = %d WHERE disp_order = %d",
		                              $next_order - 1, $next_order ) );
		// Set current item order
		$wpdb->query( $wpdb->prepare( "UPDATE " . _sc_catalog_table_ctl() . " SET "
		                              . "disp_order = %d WHERE id = %d",
		                              $next_order, $id ) );
	} else {
		return false; // selected item is already the last
	}
	return true;
}
?>
