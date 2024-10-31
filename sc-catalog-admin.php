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

// Administration interface

function sc_catalog_admin_scripts() {
	wp_enqueue_script( 'media-upload' );
	wp_enqueue_script( 'thickbox' );
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'editor' );
	wp_enqueue_script( 'editor-functions' );
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-tabs' );
	wp_enqueue_script( 'tiny_mce' );
	wp_enqueue_script( 'autocomplete',
	                   plugins_url( 'jquery-ui-1.9.2.custom.min.js',
	                                __FILE__ ) );
}
add_action( 'admin_print_scripts', 'sc_catalog_admin_scripts' );

function sc_catalog_admin_styles() {
	wp_enqueue_style( 'thickbox' ); // For media library
	wp_enqueue_style( 'sc-catalog', plugins_url( '/sc-catalog.css', __FILE__ ) );
	wp_enqueue_style( 'autocomplete',
	                  plugins_url( 'jquery-ui-1.9.2.custom.css', __FILE__ ) );
}
add_action( 'admin_print_styles', 'sc_catalog_admin_styles' );

function sc_catalog_get_help_text() {
	$help_text = '<p><strong>' . __( 'How to display your catalog', 'sc_cat' ) . '<p></strong>';
	$help_text .= '<p>' . __( 'Add a few items on the catalog admin panel and insert the shortcode [catalog] (including square backets) in a page or a post, where you want it to be displayed.', 'sc_cat' ) . '</p>';
	return $help_text;
}

function sc_catalog_create_menu() {
	//create top-level menu
	$list_page = add_menu_page( __( 'Catalog', 'sc_cat' ),
	                            __( 'Catalog', 'sc_cat' ),
	                            'edit_pages',
	                            'sc-catalog-list',
	                            'sc_catalog_list',
	                            plugins_url( '/images/icon.png', __FILE__ ) );
	//add member page							  
    $cat_page = add_submenu_page( 'sc-catalog-list',
	                              __( 'categories', 'sc_cat' ),
	                              __( 'categories', 'sc_cat' ),
	                              'edit_pages',
	                              'sc-catalog-category',
	                              'sc_catalog_category' );
	// Add member page
	$add_page = add_submenu_page( 'sc-catalog-list',
	                              __( 'Add item', 'sc_cat' ),
	                              __( 'Add item', 'sc_cat' ),
	                              'edit_pages',
	                              'sc-catalog-item',
	                              'sc_catalog_edit' );
	
	
	global $wp_version;
	if ( class_exists( "WP_Screen" ) ) {
		// Post 3.3
		add_action( 'load-' . $list_page, 'sc_catalog_help' );
	} else if ( function_exists( "add_contextual_help" ) ) {
		// Post 2.7
		add_contextual_help( $list_page, sc_catalog_get_help_text() );
	}
}
function sc_catalog_help() {
	$screen = get_current_screen();
	$screen->add_help_tab( array ( 'id' => 'sc-catalog-help',
	                               'title' => __( 'Display your catalog', 'sc_cat' ),
	                               'content' => sc_catalog_get_help_text() ) );
}
add_action('admin_menu', 'sc_catalog_create_menu');

function sc_catalog_list() {
	if ( isset( $_REQUEST['action'] ) ) {
		switch ( $_REQUEST['action'] ) {
		case 'delete':
			sc_catalog_delete_item( $_REQUEST['id'] );
			break;
		case 'up':
			sc_catalog_move_up( $_REQUEST['id'] );
			break;
		case 'down':
			sc_catalog_move_down( $_REQUEST['id'] );
			break;
		}
	}
	$items = sc_catalog_get_all();
?>
	<div class="wrap">
		<h2><?php _e( 'Catalog', 'sc_cat' ); ?><a class="add-new-h2" href="?page=sc-catalog-item"><?php _e( 'Add', 'sc_cat'); ?></a></h2>
<?php
	if ( count( $items ) == 0 ) {
?>
		<p><?php _e( 'There is currently no item in your catalog', 'sc_cat' ); ?></p>
<?php
	} else {
?>
		<ul class="sc-catalog-list" cellspacing="0">
		<?php
		for ( $i = 0; $i < count( $items ); $i++ ) {
			$item = $items[$i];
		?>
			<li class="sc-catalog-list-item">
				<?php
				if ( $item['image'] != "" ) { ?>
					<img src="<?php echo $item['image']; ?>" />
				<?php
				} ?>
				<h3><?php echo $item['title']; ?></h3>
				<p><?php echo $item['catch']; ?></p>
				<p class="sc-catalog-category"><?php echo sc_catalog_cat( $item['category'] ); ?>
				<div class="sc-catalog-clear"></div>
				<a class="button-secondary" href="?page=sc-catalog-item&id=<?php echo $item['id']; ?>"><?php _e( 'Edit', 'sc_cat' ); ?></a>
				<a class="button-secondary" href="?page=sc-catalog-list&id=<?php echo $item['id']; ?>&action=delete" onclick="javascript:return confirm(\'<?php htmlspecialchars( addslashes( sprintf( __( 'Do you really want to delete %s?', 'sc-cat' ), $item['title'] ) ), ENT_QUOTES ); ?>\');"><?php _e( 'Delete', 'sc_cat' ); ?></a>
				<br />
				<?php
				if ( $i != 0 ) { ?>
				<a class="button-secondary" href="?page=sc-catalog-list&id=<?php echo $item['id']; ?>&action=up"><?php _e( 'Up', 'sc_cat' ); ?></a>
				<?php
				}
				if ( $i != count ( $items ) - 1 ) { ?>
				<a class="button-secondary" href="?page=sc-catalog-list&id=<?php echo $item['id']; ?>&action=down"><?php _e( 'Down', 'sc_cat' ); ?></a>
				<?php
				} ?>
			</li>
		<?php
		} ?>
		</ul>
	<?php
	} ?>
	</div>
<?php
}

function sc_catalog_edit() {
	if ( isset( $_REQUEST['save'] ) ) {
		// Register changes
		$_REQUEST = array_map( 'stripslashes_deep', $_REQUEST );
		if ( isset( $_REQUEST['id'] ) ) {
			if ( ! sc_catalog_edit_item ( $_REQUEST['id'],
			                              $_REQUEST['title'],
			                              $_REQUEST['catch'],
			                              $_REQUEST['image'],
			                              $_REQUEST['text'],
			                              $_REQUEST['order'],
			                              $_REQUEST['category'] ) ) {
				$error = "Unable to save changes";
			} else {
				$msg = "%s updated";
			}
		} else {
			$item = sc_catalog_add_item( $_REQUEST['title'],
			                             $_REQUEST['catch'],
			                             $_REQUEST['image'],
			                             $_REQUEST['text'],
			                             $_REQUEST['category'] );
			if ( ! $item ) {
				$error = "Unable to save changes";
			} else {
				$msg = "%s added";
			}
			$id = $item['id'];
			$title = $item['title'];
			$image = $item['image'];
			$catch = $item['catch'];
			$text = $item['text'];
			$order = $item['disp_order'];
			$category = $item['category'];
		}
	}
	if( isset( $_REQUEST['id'] ) && ! isset( $id ) ) {
		$id = $_REQUEST['id'];
		$item = sc_catalog_get_item( $id );
		$title = $item['title'];
		$image = $item['image'];
		$catch = $item['catch'];
		$text = $item['text'];
		$order = $item['disp_order'];
		$category = $item['category'];
	}
?>
	<div class="wrap">
		<?php if ( $error ) { ?>
		<div class="error"><?php _e( $error, 'sc_cat' ); ?></div>
		<?php } ?>
		<?php if ( $msg ) { ?>
		<div class="updated"><?php printf( __( $msg, 'sc_cat' ), $item['title'] ); ?></div>
		<?php } ?>
		<h2><?php _e( 'Catalog item', 'sc_cat' ); ?></h2>
		<form action="?page=sc-catalog-item" method="post">
		<div id="poststuff">
			<input type="hidden" name="save" value="1" />
			<?php if ( $id ) { ?>
			<input type="hidden" name="id" value="<?php echo $id; ?>" />
			<input type="hidden" name="order" value="<?php echo $order; ?>" />
			<?php } ?>
			<table class="form-table">
				<tbody>
					<tr class="form-field">
						<th><label for="title"><?php _e( 'Title', 'sc_cat' ); ?></label></th>
						<td><input type="text" id="title" name="title" value="<?php echo htmlspecialchars( $title, ENT_QUOTES ); ?>" /></td>
					</tr>
					<tr class="form-field">
						<th><label for="image"><?php _e( 'Image', 'sc_cat' ); ?></label></th>
						<td>
							<img id="item_image" src="<?php echo $image; ?>" />
							<input type="hidden" id="upload_image" name="image" value="<?php echo $image; ?>" />
							<input id="upload_image_button" type="button" value="<?php _e( 'Choose image', 'sc_cat' ); ?>" />
							<input type="button" value="<?php _e( 'Remove image', 'sc_cat' ); ?>" onclick="javascript:jQuery('#item_image').attr('src', '');" ?>
						</td>
					</tr>
					<tr class="form-field">
						<th><label for="catch"><?php _e( 'Catch phrase', 'sc_cat' ); ?></label></th>
						<td><input type="text" id="catch" name="catch" value="<?php echo htmlspecialchars( $catch, ENT_QUOTES ); ?>" /></td>
					</tr>
					<tr>
						<th><label for="text"><?php _e( 'Description', 'sc_cat' ); ?></label></th>
						<td>
<?php
	if ( function_exists( "wp_editor" ) ) {
		wp_editor( $text, 'text' );
	} else {
		// Pre 3.3 editor
		the_editor( $text, 'text', "text_prev", true, 1 );
	}
?>
						</td>
					<tr>
					<tr class="form-field">
						<th><label for="category"><?php _e( 'Category', 'sc_cat' ); ?></label></th>
						<td>
                        <select id="category" name="category">
                         <?php
						$categories = sc_catalog_get_categories();
						foreach($categories as $catg) {
							$selected = ($category == $catg['name']) ? "selected": "";
							echo '<option value="'.$catg['name'].'" '.$selected.'>'.$catg['name'].'</option>';
						}
						?>
                        </select>
                        </td>
					</tr>
				</tbody>
			</table>
			<input type="hidden" name="order" value="<?php echo $order; ?>" />
			<p class="submit">
				<input class="button-primary" type="submit" value="<?php _e( 'Save', 'sc_cat' ); ?>" />
			</p>
		</div>
		</form>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				var categories = [
				<?php
				foreach ( sc_catalog_get_categories() as $category ) {
					echo sprintf("\t\t\t\t\t" .'"%s",', $category);
					echo "\n";
				}
				?>
				];
				jQuery("#category").autocomplete({ source: categories });
			});
		</script>
<?php
	$media_lib_url = plugins_url( '/media_library.js', __FILE__ );
	echo( '<script type="text/javascript" src="' . $media_lib_url . '"></script>' );
}

function sc_catalog_category()
{
		require_once "sc_catalog_categories.php";
}
?>
