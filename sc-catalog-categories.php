<script type="text/javascript">
function delConfirm() {
	return confirm(__( "Are you sure you want to delete this category?",
	                  'sc_cat') );
}
</script>
<?php
function eci_message( $message ) {
	echo '<div id="message" class="updated"><p>'. $message .'</p></div>';
}
function eci_error( $message ) {
	echo '<div id="error" class="error"><p>'. $message .'</p></div>';
}


$action = $_GET['action'];
$message = NULL;
$error = NULL;

if(isset($_POST) && ( $action == "add" || !isset($action)) ) {
	if( !empty($_POST['sc_category_name']) ) {
		if ( sc_catalog_add_category($_POST['sc_category_name']) ) {
			$message = '<strong>' . __( 'Category added', 'sc_cat' ) . '</strong><p>' . __( 'Category have been added successfully.', 'sc_cat' ) . '</p>';
			unset($error);
		} else {
			unset($message);
			$error = '<strong>' . __( 'Operation failed', 'sc_cat' ) . '</strong><p>' . __( 'An error occured while adding category.', 'sc_cat' ) . '</p>';
		}
	}
} else if (isset($_POST) && $action =="update") {
	if( !empty($_POST['sc_category_name']) ) {
		if ( sc_catalog_update_category($_POST['sc_category_name'],$_GET['id']) ) {
			$message = '<strong>' . __( 'Category update', 'sc_cat' ) . '</strong><p>' . __( 'Category have been updated successfully.', 'sc_cat' ) . '</p>';
			unset($error);
		} else {
			unset($message);
			$error = '<strong>' . __( 'Operation failed', 'sc_cat' ) . '</strong><p>' . __( 'An error occured while updating category.', 'sc_cat' ) . '</p>';
		}
	}
} else if ($action == "delete") {
	if( sc_catalog_delete_category($_GET['id']) ) {
		$message = '<strong>' . __( 'Category deleted', 'sc_cat' ) . '</strong><p>' . __( 'Category have been delete successfully.', 'sc_cat' ) . '</p>';
		unset($error);
	} else {
		unset($message);
		$error = '<strong>' . __( 'Operation failed', 'sc_cat' ) . '</strong><p>' . __( 'An error occured while deleting category.', 'sc_cat' ) . '</p>';
	}
}

?>
<div id="icon-tools" class="icon32"></div><h1> Categories Management</h1>
<?Php
if( $message ) {
	eci_message($message);
} else if ($error) {
	eci_error($error);
}
?>
<table align="center">
<form method="post" 
action="<?php if( $action == "delete" ) { echo add_query_arg( 'action', 'add' ); } ?>">
	<tr>
    	<td colspan="2" align="center">
        	<div id="icon-edit" class="icon32"></div><h2><?php _e( 'Add Category', 'sc_cat' ); ?></h2>
        </td>
    </tr>
    <tr>
        <td>
            <?php _e( 'Category Name:', 'sc_cat' );<span>*</span>
        </td>
        <td>
        	<?php 
			$value = NULL;
			if( $action == "update" && isset( $_GET['id'] ) ) {
				$value = sc_catalog_get_category_by_id( $_GET['id'] );
			} else {
				$value = NULL;
			}
			?>
            <input type="text" name="sc_category_name" value="<?php echo esc_attr( $value );?>" />
        </td>
    </tr>
    <tr>
    	<td colspan="2" align="center">
        	<?php if( $action == "add" || $action == "delete" || ! isset( $action ) ) { ?>
        	<input class="button-primary" type="submit" name="Save"
             value="<?php _e( 'Add Category', 'sc_cat' ); ?>" id="submitbutton" />
            <?php } else if( $action == "update" ) {  ?>
            <input class="button-primary" type="submit" name="Update"
             value="<?php _e( 'Update Category', 'sc_cat' ); ?>" id="submitbutton" />
             <a class="button-secondary" href="<?php echo remove_query_arg( array("action","id"), false);?>" title="<?php _e( 'Cancel', 'sc_cat' ); ?>"><?php _e( 'Cancel', 'sc_cat' ); ?></a>
            <?php } ?>
        </td>
    </tr>
        	
</table>

<div id="icon-edit-pages" class="icon32"></div><h2><?php _e( 'Categories', 'sc_cat' ); ?></h2>
<table class="widefat">
<thead>
	<tr>
		<th>#</th>
		<th><?php _e( 'Name', 'sc_cat' ); ?></th>		
		<th><?php _e( 'Action', 'sc_cat' ); ?></th>
	</tr>
</thead>
<tfoot>
    <tr>
	<th>#</th>
	<th><?php _e( 'Name', 'sc_cat' ); ?></th>
	<th><?php _e( 'Action', 'sc_cat' ); ?></th>
    </tr>
</tfoot>
<tbody>
	<?php 
		$categories = sc_catalog_get_categories();
		$counter  = 1;
		foreach ($categories as $category) {
			$arr_params_del = array ( 'action' => 'delete', 'id' => $category['id'] );
			$arr_params_edit = array ( 'action' => 'update', 'id' => $category['id'] );
			echo "<tr>
					<td>".$counter."</td>
					<td>".$category['name']."</rd>
					<td>[<a href='".add_query_arg( $arr_params_edit )."'>" . __( 'edit', 'sc_cat' ) . "</a>]
						[<a href='".add_query_arg( $arr_params_del )."' onclick='return delConfirm();'>" . __( 'delete', 'sc_cat' ) . "</a>]</td>
				</tr>";
				$coutner ++;
		}
	?>
</tbody>
</table>
