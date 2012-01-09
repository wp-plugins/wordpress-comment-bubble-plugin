<?php
/*
Plugin Name: Wordpress Comment Bubble Plugin
Version: 1.0
Plugin URI: http://patrick.bloggles.info/
Description: Add comment bubble count to each post title on front page.
Author: mypatricks, Patrick Chia
Author URI: http://patrickchia.com/
Plugin URI: http://patrick.bloggles.info/plugins/
Tags: comments, bubble, themes, count
Donate link: http://bit.ly/aYeS92
*/

function comments_bubble_admin_menu(){
	add_submenu_page('themes.php', 'Comment Bubble', 'Comment Bubble', 8, 'comment-bubble', 'comments_bubble_admin');
}

function comment_bubble_style() {
	if ( is_home() || is_front_page() || !get_option('cb') == 1 ) {
?>
<script type="text/javascript">

jQuery(document).ready(function(){
	jQuery("count").each( function(){
		$self = jQuery(this)
		var comment_num = $self.attr("id");
		$self.replaceWith( "<span class='comment-bubble count-" + comment_num + "'>" + comment_num + "</span>" );
	});
});
</script>
<style type="text/css">.comment-bubble{background-image:-ms-linear-gradient(top, #FF0000 0%, #BE4958 50%, #A40717 100%);background-image: -moz-linear-gradient(top, #FF0000 0%, #BE4958 50%, #A40717 100%);background-image: -o-linear-gradient(top, #FF0000 0%, #BE4958 50%, #A40717 100%);background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #FF0000), color-stop(0.5, #BE4958), color-stop(1, #A40717));background-image: -webkit-linear-gradient(top, #FF0000 0%, #BE4958 50%, #A40717 100%);background-image: linear-gradient(top, #FF0000 0%, #BE4958 50%, #A40717 100%);-webkit-background-clip: padding-box;-moz-background-clip:padding-box;display:block;-webkit-border-radius:32px;-moz-border-radius:32px;border-radius:32px;padding:0 5px;color:#fff;text-shadow: 0px 1px 1px #7b0805;filter:dropshadow(color=#7b0805, offx=0, offy=1);text-align:center;border-style:solid;border-width:2px;-webkit-box-shadow: 0px 2px 3px 0px rgba(0, 0, 0, 0.6);-moz-box-shadow: 0px 2px 3px 0px rgba(0, 0, 0, 0.6);box-shadow: 0px 2px 3px 0px rgba(0, 0, 0, 0.6);float:right;font:bold 12px Helvetica, "Helvetica Neue", Geneva, Arial, sans-serif;height:16px;width:auto;}</style>
<?php
	}
}

// register settings
function register_settings_comment_bubble(){
	register_setting('extra_settings','cb');
	//register_setting('extra_settings','cb_align');
}

function comments_bubble_admin(){
	if ( isset($_POST['submit']) ) {
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
			die(__('Cheatin&#8217; uh?'));

		check_admin_referer( 'comment-bubble' );

		if ( isset ( $_POST['cb'] ) ) {
			update_option( 'cb', '1' );
		} else {
			delete_option( 'cb' );
		}

/*		if ( isset( $_POST['cb_align'] ) ) {
			update_option( 'cb_align', '1' );
		} else {
			delete_option( 'cb_align' );
		}*/


		echo '<div id="message" class="updated fade"><p>Options saved successfully.</p></div>';
	}

?>
<div class="wrap">
<div id="icon-themes" class="icon32"><br /></div>
<h2><?php _e('Theme Option'); ?></h2>
<p><strong><?php _e('These are general extras that you can enable for your entire blog.')?></strong></p>
<form action="" method="post">
<?php settings_fields( 'extra_settings' ); ?>
<h2><?php _e('Comment Bubble'); ?></h2>
<p><label><input name="cb" value="1" type="checkbox" <?php if ( get_option('cb') == 1 ) { ?> checked="checked" <?php } ?> /> Disable comment bubble on your blog.</label></p>
<!--<p><label><input name="cb_align" value="1" type="checkbox" <?php if ( get_option('cb_align') == 1 ) { ?> checked="checked" <?php } ?> /> Align to left of post title.</label></p>-->
<?php wp_nonce_field( 'comment-bubble' ); ?>

<p class="submit"><input type="submit" name="submit" value="<?php _e('Update options &raquo;'); ?>" /></p>

</form>
</div>
<?php
}

function comments_bubble($title) {
	global $id, $comment;

	if ( get_option('cb') == 1 )
		return $title;

	if (! in_the_loop() )
		return $title;

	if ( is_home() ) {
		$number = get_comments_number( $id );

		if ( $number  > 1 ) {
			$bubble = '<count id="'.$number.'"></count>';
			return $bubble . $title;
		}
	}

	return $title;
}

function get_comments_bubble($id) {

	if ( get_option('cb') == 1 )
		return;

	if ( is_home() ) {
		$number = get_comments_number( $id );

		if ( $number  > 1 ) {
			echo '<span class="comment-bubble count-'.$number.'">'. number_format_i18n($number) .'</span>';
		}
	}

}

function comment_bubble_jquery() {
	if ( get_option('cb') == 1 )
		return;

	if ( is_home() ) {
		wp_enqueue_script('jquery');
	}
}

add_action( 'init', 'comment_bubble_jquery' );
add_action( 'admin_init', 'register_settings_comment_bubble' ); 
add_filter( 'the_title', 'comments_bubble', 10, 1 );
add_action( 'admin_menu', 'comments_bubble_admin_menu' );
add_action( 'wp_head', 'comment_bubble_style' );

?>