<?php
function mts_create_review_tables(){

	global $wpdb;
	$table_name = $wpdb->prefix . MTS_WP_REVIEW_DB_TABLE;
	if (function_exists('is_multisite') && is_multisite()) $table_name = $wpdb->base_prefix . MTS_WP_REVIEW_DB_TABLE; 

	/*$sql = "DROP TABLE IF_EXISTS $table_name;";
	$e = $wpdb->query($sql);
	die(var_dump($e));*/
	$my_stat_sql = "CREATE TABLE IF NOT EXISTS $table_name (
      id int(11) NOT NULL auto_increment,
      blog_id int(11) NOT NULL,
      post_id int(11) NOT NULL,
	  user_id int(11) NOT NULL,
	  user_ip varchar(55) NOT NULL,
	  rate int(11) NOT NULL,
      date datetime NOT NULL,
      UNIQUE KEY id (id)
    );";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $my_stat_sql );
}
mts_create_review_tables();

?>