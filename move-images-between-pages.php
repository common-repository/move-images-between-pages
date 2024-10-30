<?php
/*
Plugin Name: Move Images Between Pages
Plugin URI: http://oddjar.com/
Description: This plugin allows you to quickly reattach images to different pages. It places a simple drop-down menu in the media edit screen that lists all of the pages in your WordPress site and allows you to reattach the given image to a new page. Note that this plugin only works for images that are attached to pages. It ignores images that are attached to posts.
Version: 1.1
Author: Johnathon Williams
Author URI: http://oddjar.com/
*/

add_filter( "attachment_fields_to_save", "ojmi_image_attachment_fields_to_save", null, 2 );
add_filter( "attachment_fields_to_edit", "ojmi_image_attachment_fields_to_edit", null, 2 );



/**
 *
 *
 * @param array   $form_fields
 * @param object  $post
 * @return array
 */
function ojmi_image_attachment_fields_to_edit( $form_fields, $post ) {
	// only activate for images that already attached to pages, ignore images attached to posts
	if ( get_post_type( $post->post_parent ) == 'page' ) {
		// get the list of pages for our select box
		$all_pages = get_pages();
		$select_code = ojmi_get_pages_as_select_field( $post, $all_pages );
		// $form_fields is a special array of fields to include in the attachment form
		// $post is the attachment record in the database
		// $post->post_type == 'attachment'
		// (attachments are treated as posts in WordPress)
		// add our custom field to the $form_fields array
		// input type="text" name/id="attachments[$attachment->ID][custom1]"
		$form_fields["post_parent"] = array(
			"label" => __( "Attatched to page" ),
			"input" => "html",
			"html" => $select_code
		);
	}
	return $form_fields;
}

/**
 *
 *
 * @param object  $post
 * @param object  $all_pages
 * @return string
 */
function ojmi_get_pages_as_select_field( $post, $all_pages ) {

	$content = "<select name='attachments[{$post->ID}][post_parent]' id='attachments[{$post->ID}][post_parent]'>";
	foreach ( $all_pages as $page ) {
		if ( $page->ID == $post->post_parent ) {
			$selected = ' SELECTED ';
		} else {
			$selected = ' ';
		}
		$option_line = "<option" . $selected . "value='" . $page->ID . "'>" . $page->post_title . "</option>";
		$content = $content . $option_line;
	}
	$content = $content . "</select>";
	return $content;
}

/**
 *
 *
 * @param array   $post
 * @param array   $attachment
 * @return array
 */
function ojmi_image_attachment_fields_to_save( $post, $attachment ) {
	if ( isset( $attachment['post_parent'] ) ) {
		if ( trim( $attachment['post_parent'] ) == '' ) {
			// adding our custom error
			$post['errors']['post_parent']['errors'][] = __( 'No value found for post_parent.' );
		}else {
			$post['post_parent'] = $attachment['post_parent'];
		}
	}
	return $post;
}
?>