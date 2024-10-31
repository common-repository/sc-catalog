<?php
/*
	Plugin Name: SC Catalog
	Description: Introduce products or services (or anything else) with in a galery with picture, title and catch-phrase. Get more information by clicking on it.
	Version: devel
	Author: Scil
	Author URI: http://scil.coop
	Licence: GPL2 or any later

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

// check for WP context
if ( !defined('ABSPATH') ){ die(); }

define( "SC_CATALOG_DB_VERSION", 3 );
define( "SC_CATALOG_DB_TABLE_CTL", "sc_catalog" );
define( "SC_CATALOG_DB_TABLE_CTG", "sc_categories" );
define( "SC_CATALOG_OPTION_DB_VER", "sc_catalog_db_version" );

/** @private */
function _sc_catalog_table_ctl() {
	global $wpdb;
	return $wpdb->prefix . SC_CATALOG_DB_TABLE_CTL;
}

function _sc_catalog_table_ctg() {
	global $wpdb;
	return $wpdb->prefix . SC_CATALOG_DB_TABLE_CTG;
}

function sc_catalog_install() {
	global $wpdb;
	$table_ctl = _sc_catalog_table_ctl();
	$table_ctg = _sc_catalog_table_ctg();
	$current_version = get_option( SC_CATALOG_OPTION_DB_VER, NULL );
	$db_ok = ( 1 === $wpdb->query( 'SHOW TABLES like "' . $table_ctl . '";' )
			   && $wpdb->query( 'SHOW TABLES like "' . $table_ctg . '";' ));
	// Check if table is there and versionned
	if ( NULL === $current_version || ! $db_ok)  {
		// Delete potential junk
		$wpdb->query( "DROP TABLE $table_ctl;" );
		$wpdb->query( "DROP TABLE $table_ctg;" );
		delete_option( SC_CATALOG_OPTION_DB_VER );
		// Install database
		$sql_ctl = "CREATE TABLE $table_ctl (
		           id INT(11) NOT NULL AUTO_INCREMENT,
		           title TEXT NOT NULL,
		           catch TEXT NOT NULL,
		           image VARCHAR(255) NOT NULL,
		           text TEXT NOT NULL,
		           disp_order INT(11) NOT NULL,
		           category TEXT,
		           UNIQUE KEY id (id) );";
		$sql_ctg = "CREATE TABLE IF NOT EXISTS $table_ctg (
		           `id` int(11) NOT NULL AUTO_INCREMENT,
		           `name` varchar(255) NOT NULL,
		           PRIMARY KEY (`id`) );";
		$wpdb->query( $sql_ctl );
		$wpdb->query( $sql_ctg );
		add_option( SC_CATALOG_OPTION_DB_VER, SC_CATALOG_DB_VERSION, '', 'no');
	} else {
		// It is there but outdated, upgrade database
		switch ( $current_version ) {
		case 1:
			$sql = "ALTER TABLE $table_ctl ADD category TEXT";
			$wpdb->query( $sql );
		case 2:
			// Create the categories table
			$sql_ctg = "CREATE TABLE IF NOT EXISTS $table_ctg (
		           `id` int(11) NOT NULL AUTO_INCREMENT,
		           `name` varchar(255) NOT NULL,
		           PRIMARY KEY (`id`) );";
		    $wpdb->query( $sql_ctg );
		    // Migrate existing categories
			$query = $wpdb->prepare( "SELECT distinct(category) FROM "
			                         . _sc_catalog_table_ctl() );
			$old_cats = $wpdb->get_results( $query, ARRAY_A );
			foreach ( $old_cats as $cat ) {
				sc_catalog_add_category( $cat['category'] );
			}
			// Update link from item to categories
			$items = sc_catalog_get_all();
			$categories = sc_catalog_get_categories();
			foreach ( $items as $item ) {
				foreach ( $categories as $cat ) {
					if ( $item['category'] == $categories['name'] ) {
						sc_catalog_edit_item ( $item['id'], $item['title'],
						                       $item['catch'], $item['image'],
						                       $item['text'], $item['order'],
						                       $cat['id'] );
						continue;
					}
				}
			}
			// Update link type
			$sql_cat_upd = "ALTER TABLE " . _sc_catalog_table_ctl()
			               . " CHANGE category category INTEGER;";
			$wpdb->query( $sql_cat_upd );
		}
		update_option( SC_CATALOG_OPTION_DB_VER, SC_CATALOG_DB_VERSION );
	}
}
register_activation_hook( __FILE__, 'sc_catalog_install' );

function sc_catalog_upgrade() {
	// Check db version and update it if required
	if ( get_option( SC_CATALOG_OPTION_DB_VER, NULL ) !== SC_CATALOG_DB_VERSION) {
		sc_catalog_install();
	}
}
add_action('plugins_loaded', 'sc_catalog_upgrade');

function sc_catalog_uninstall() {
	global $wpdb;
	delete_option( SC_CATALOG_OPTION_DB_VER );
	$wpdb->query( 'DROP TABLE ' . _sc_catalog_table_ctl() );
	$wpdb->query( 'DROP TABLE ' . _sc_catalog_table_ctg() );
}
register_uninstall_hook( __FILE__, 'sc_catalog_uninstall' );

function sc_catalog_init() {
	$lang_dir = basename( dirname( __FILE__ ) ) . '/languages';
	load_plugin_textdomain( 'sc_cat', false, $lang_dir );
}
add_action( 'init', 'sc_catalog_init' );

// Add custom style
function sc_catalog_styles() {
	wp_enqueue_style( 'thickbox' );
	wp_enqueue_style( 'sc-catalog', plugins_url( '/sc-catalog.css', __FILE__ ) );
}
add_action( 'wp_print_styles', 'sc_catalog_styles' );

function sc_catalog_scripts() {
	wp_enqueue_script( 'thickbox' );
}
add_action( 'wp_print_scripts', 'sc_catalog_scripts' );

// Import data manipulation functions
require_once( dirname( __FILE__ ) . "/sc-catalog-data.php" );
// Register shortcode
require_once( dirname( __FILE__ ) . "/sc-catalog-shortcode.php" );
// Register admin options
require_once( dirname( __FILE__ ) . "/sc-catalog-admin.php" );
