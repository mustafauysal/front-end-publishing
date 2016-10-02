<?php

/**
 * Creates shortcode fep_submission_form
 *
 * @return string: HTML content for the shortcode
 */
function fep_add_new_post()
{
	$fep_misc = get_option('fep_misc');
	$fep_roles = get_option('fep_role_settings');
	if (!is_user_logged_in()) {
		if (isset($fep_misc['disable_login_redirection']) && $fep_misc['disable_login_redirection']) {
			return sprintf(
				__('You need to %s to see this page.', 'frontend-publishing'),
				sprintf(
					'<a href="' . wp_login_url(get_permalink()) . '" title="%s">%s</a>',
					__('Login', 'frontend-publishing'),
					__('log in', 'frontend-publishing')
				)
			);
		} else auth_redirect();
	}

	ob_start();
	include(dirname(dirname(__FILE__)) . '/views/submission-form.php');
	return ob_get_clean();
}

add_shortcode('fep_submission_form', 'fep_add_new_post');

/**
 * Creates shortcode fep_article_list
 *
 * @return string: HTML content for the shortcode
 */
function fep_manage_posts()
{
	$fep_misc = get_option('fep_misc');
	if (!is_user_logged_in()) {
		if (isset($fep_misc['disable_login_redirection']) && $fep_misc['disable_login_redirection']) {
			return sprintf(
				__('You need to %s to see this page.', 'frontend-publishing'),
				sprintf(
					'<a href="' . wp_login_url(get_permalink()) . '" title="%s">%s</a>',
					__('Login', 'frontend-publishing'),
					__('log in', 'frontend-publishing')
				)
			);
		} else auth_redirect();
	}

	ob_start();
	if (isset($_GET['fep_id']) && isset($_GET['fep_action']) && $_GET['fep_action'] == 'edit') {
		include(dirname(dirname(__FILE__)) . '/views/submission-form.php');
	} else {
		include(dirname(dirname(__FILE__)) . '/views/post-tabs.php');
	}
	return ob_get_clean();
}

add_shortcode('fep_article_list', 'fep_manage_posts');

?>