<?php

/**
 * Matches submitted content against restrictions set in the options panel
 *
 * @param array $content The content to check
 * @return string: An html string of errors
 */
function fep_post_has_errors($content)
{
	$fep_plugin_options = get_option('fep_post_restrictions');
	$fep_messages = fep_messages();
	$min_words_title = $fep_plugin_options['min_words_title'];
	$max_words_title = $fep_plugin_options['max_words_title'];
	$min_words_content = $fep_plugin_options['min_words_content'];
	$max_words_content = $fep_plugin_options['max_words_content'];
	$min_words_bio = $fep_plugin_options['min_words_bio'];
	$max_words_bio = $fep_plugin_options['max_words_bio'];
	$max_links = $fep_plugin_options['max_links'];
	$max_links_bio = $fep_plugin_options['max_links_bio'];
	$min_tags = $fep_plugin_options['min_tags'];
	$max_tags = $fep_plugin_options['max_tags'];
	$thumb_required = $fep_plugin_options['thumbnail_required'];
	$error_string = '';
	$format = '%s<br/>';

	if (($min_words_title && empty($content['post_title'])) || ($min_words_content && empty($content['post_content'])) || ($min_words_bio && empty($content['about_the_author'])) || ($min_tags && empty($content['post_tags']))) {
		$error_string .= sprintf($format, $fep_messages['required_field_error']);
	}

	$tags_array = explode(',', $content['post_tags']);
	$stripped_bio = strip_tags($content['about_the_author']);
	$stripped_content = strip_tags($content['post_content']);

	if (!empty($content['post_title']) && str_word_count($content['post_title']) < $min_words_title)
		$error_string .= sprintf($format, $fep_messages['title_short_error']);
	if (!empty($content['post_title']) && str_word_count($content['post_title']) > $max_words_title)
		$error_string .= sprintf($format, $fep_messages['title_long_error']);
	if (!empty($content['post_content']) && str_word_count($stripped_content) < $min_words_content)
		$error_string .= sprintf($format, $fep_messages['article_short_error']);
	if (str_word_count($stripped_content) > $max_words_content)
		$error_string .= sprintf($format, $fep_messages['article_long_error']);
	if (!empty($content['about_the_author']) && $stripped_bio != -1 && str_word_count($stripped_bio) < $min_words_bio)
		$error_string .= sprintf($format, $fep_messages['bio_short_error']);
	if ($stripped_bio != -1 && str_word_count($stripped_bio) > $max_words_bio)
		$error_string .= sprintf($format, $fep_messages['bio_long_error']);
	if (substr_count($content['post_content'], '</a>') > $max_links)
		$error_string .= sprintf($format, $fep_messages['too_many_article_links_error']);
	if (substr_count($content['about_the_author'], '</a>') > $max_links_bio)
		$error_string .= sprintf($format, $fep_messages['too_many_bio_links_error']);
	if (!empty($content['post_tags']) && count($tags_array) < $min_tags)
		$error_string .= sprintf($format, $fep_messages['too_few_tags_error']);
	if (count($tags_array) > $max_tags)
		$error_string .= sprintf($format, $fep_messages['too_many_tags_error']);
	if ($thumb_required == 'true' && $content['featured_img'] == -1)
		$error_string .= sprintf($format, $fep_messages['featured_image_error']);

	if (str_word_count($error_string) < 2)
		return false;
	else
		return $error_string;
}

/**
 * Ajax function for fetching a featured image
 *
 * @uses array $_POST The id of the image
 * @return string: A JSON encoded string
 */
function fep_fetch_featured_image()
{
	$image_id = $_POST['img'];
	echo wp_get_attachment_image($image_id, array(200, 200));
	die();
}

add_action('wp_ajax_fep_fetch_featured_image', 'fep_fetch_featured_image');

/**
 * Ajax function for deleting a post
 *
 * @uses array $_POST The id of the post and a nonce value
 * @return string: A JSON encoded string
 */
function fep_delete_posts()
{
	try {
		if (!wp_verify_nonce($_POST['delete_nonce'], 'fepnonce_delete_action'))
			throw new Exception(__('Sorry! You failed the security check', 'frontend-publishing'), 1);

		if (!current_user_can('delete_post', $_POST['post_id']))
			throw new Exception(__("You don't have permission to delete this post", 'frontend-publishing'), 1);

		$result = wp_delete_post($_POST['post_id'], true);
		if (!$result)
			throw new Exception(__("The article could not be deleted", 'frontend-publishing'), 1);

		$data['success'] = true;
		$data['message'] = __('The article has been deleted successfully!', 'frontend-publishing');
	} catch (Exception $ex) {
		$data['success'] = false;
		$data['message'] = $ex->getMessage();
	}
	die(json_encode($data));
}

add_action('wp_ajax_fep_delete_posts', 'fep_delete_posts');
add_action('wp_ajax_nopriv_fep_delete_posts', 'fep_delete_posts');

/**
 * Ajax function for adding a new post.
 *
 * @uses array $_POST The user submitted post
 * @return string: A JSON encoded string
 */
function fep_process_form_input()
{
	$fep_messages = fep_messages();
	try {
		if (!wp_verify_nonce($_POST['post_nonce'], 'fepnonce_action'))
			throw new Exception(
				__("Sorry! You failed the security check", 'frontend-publishing'),
				1
			);

		if ($_POST['post_id'] != -1 && !current_user_can('edit_post', $_POST['post_id']))
			throw new Exception(
				__("You don't have permission to edit this post.", 'frontend-publishing'),
				1
			);

		$fep_role_settings = get_option('fep_role_settings');
		$fep_misc = get_option('fep_misc');

		if ($fep_role_settings['no_check'] && current_user_can($fep_role_settings['no_check']))
			$errors = false;
		else
			$errors = fep_post_has_errors($_POST);

		if ($errors)
			throw new Exception($errors, 1);

		if ($fep_misc['nofollow_body_links'])
			$post_content = wp_rel_nofollow($_POST['post_content']);
		else
			$post_content = $_POST['post_content'];

		$current_post = empty($_POST['post_id']) ? null : get_post($_POST['post_id']);
		$current_post_date = is_a($current_post, 'WP_Post') ? $current_post->post_date : '';

		$new_post = array(
			'post_title'     => sanitize_text_field($_POST['post_title']),
			'post_category'  => array($_POST['post_category']),
			'tags_input'     => sanitize_text_field($_POST['post_tags']),
			'post_content'   => wp_kses_post($post_content),
			'post_date'      => $current_post_date,
			'comment_status' => get_option('default_comment_status')
		);

		if ($fep_role_settings['instantly_publish'] && current_user_can($fep_role_settings['instantly_publish'])) {
			$post_action = __('published', 'frontend-publishing');
			$new_post['post_status'] = 'publish';
		} else {
			$post_action = __('submitted', 'frontend-publishing');
			$new_post['post_status'] = 'pending';
		}

		if ($_POST['post_id'] != -1) {
			$new_post['ID'] = $_POST['post_id'];
			$post_action = __('updated', 'frontend-publishing');
		}

		$new_post_id = wp_insert_post($new_post, true);
		if (is_wp_error($new_post_id))
			throw new Exception($new_post_id->get_error_message(), 1);

		if (true === apply_filters('fep_assign_images', true)) {
			$images = array();
			global $wpdb;

			$dom = new DOMDocument("1.0", "UTF-8");
			@$dom->loadHTML($post_content);
			foreach ($dom->getElementsByTagName('img') as $key => $tag) {
				$img_src = $tag->getAttribute('src');
				if (strpos($img_src, site_url())) {
					$maybe_attachment = fep_get_attachment_id_from_url(esc_url_raw($tag->getAttribute('src')));
					if ( ! empty( $maybe_attachment )) {
						$images[] = $maybe_attachment;
					}
				}
			}

			$image_count = count($images);

			if ($image_count > 0) {
				if ($image_count === 1) {
					$post_ids = "'" . $images[0] . "'";
				} else {
					$post_ids = "'" . implode("','", $images) . "'";
				}
				$qry    = "UPDATE " . $wpdb->prefix . 'posts' . " set post_parent='" . $new_post_id
				          . "' where post_parent='0' and ID in($post_ids) and post_status='inherit' and post_type='attachment' and  post_mime_type like 'image/%'";
				$status = $wpdb->query($qry);

				if (false === $status) {
					throw new Exception(__("An error occurred while associating images to article", 'frontend-publishing'), 1);
				}
			}
		}

		if (!$fep_misc['disable_author_bio']) {
			if ($fep_misc['nofollow_bio_links'])
				$about_the_author = wp_rel_nofollow($_POST['about_the_author']);
			else
				$about_the_author = $_POST['about_the_author'];
			update_post_meta($new_post_id, 'about_the_author', $about_the_author);
		}

		if ($_POST['featured_img'] != -1)
			set_post_thumbnail($new_post_id, $_POST['featured_img']);

		$data['success'] = true;
		$data['post_id'] = $new_post_id;
		$data['message'] = sprintf(
			'%s<br/><a href="#" id="fep-continue-editing">%s</a>',
			sprintf(__('Your article has been %s successfully!', 'frontend-publishing'), $post_action),
			__('Continue Editing', 'frontend-publishing')
		);
	} catch (Exception $ex) {
		$data['success'] = false;
		$data['message'] = sprintf(
			'<strong>%s</strong><br/>%s',
			$fep_messages['general_form_error'],
			$ex->getMessage()
		);
	}
	die(json_encode($data));
}

add_action('wp_ajax_fep_process_form_input', 'fep_process_form_input');
add_action('wp_ajax_nopriv_fep_process_form_input', 'fep_process_form_input');


/**
 * Fetch $attachment_id from $attachment_url
 * This might be slow on large setups. FYI
 *
 * @link https://philipnewcomer.net/2012/11/get-the-attachment-id-from-an-image-url-in-wordpress/
 * @param string $attachment_url
 *
 * @return bool|null|string|void
 */
function fep_get_attachment_id_from_url( $attachment_url = '' ) {

	global $wpdb;
	$attachment_id = false;

	// If there is no url, return.
	if ( '' == $attachment_url )
		return;

	// Get the upload directory paths
	$upload_dir_paths = wp_upload_dir();

	// Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
	if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {

		// If this is the URL of an auto-generated thumbnail, get the URL of the original image
		$attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );

		// Remove the upload path base directory from the attachment URL
		$attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );

		// Finally, run a custom database query to get the attachment ID from the modified attachment URL
		$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );

	}

	return $attachment_id;
}