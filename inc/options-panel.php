<?php
/*
The settings page
*/

function fep_menu_item()
{
	global $fep_settings_page_hook;
	$fep_settings_page_hook = add_menu_page(
		__('Frontend Publishing Settings', 'frontend-publishing'),
		__('Frontend Publishing', 'frontend-publishing'),
		'manage_options',
		'fep_settings',
		'fep_render_settings_page'
	);
}

add_action('admin_menu', 'fep_menu_item');

function fep_scripts_styles($hook)
{
	global $fep_settings_page_hook;
	if ($fep_settings_page_hook != $hook)
		return;
	wp_enqueue_style("options_panel_stylesheet", plugins_url("static/css/options-panel.css", dirname(__FILE__)), false, "1.0", "all");
	wp_enqueue_script("options_panel_script", plugins_url("static/js/options-panel.js", dirname(__FILE__)), false, "1.0");
	wp_enqueue_script('common');
	wp_enqueue_script('wp-lists');
	wp_enqueue_script('postbox');
}

add_action('admin_enqueue_scripts', 'fep_scripts_styles');

function fep_render_settings_page()
{
	?>
	<div class="wrap">
		<div id="icon-options-general" class="icon32"></div>
		<h2><?php _e('Frontend Publishing Settings', 'frontend-publishing'); ?></h2>
		<?php settings_errors(); ?>
		<div class="clearfix paddingtop20">
			<div class="first ninecol">
				<form method="post" action="options.php">
					<?php settings_fields('fep_settings'); ?>
					<?php do_meta_boxes('fep_metaboxes', 'advanced', null); ?>
					<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false); ?>
					<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false); ?>
				</form>
			</div>
			<div class="last threecol">
				<div class="side-block">
					<?php _e("Like the plugin? Don't forget to give it a good rating on WordPress.org.", 'frontend-publishing'); ?>
				</div>
				<div class="side-block">
					<h3><?php _e('Frontend Publishing Pro', 'frontend-publishing'); ?></h3>
					<?php _e('Supports:', 'frontend-publishing'); ?>
					<ul>
						<li>- <?php _e('Custom fields', 'frontend-publishing'); ?></li>
						<li>- <?php _e('Custom post types', 'frontend-publishing'); ?></li>
						<li>- <?php _e('Custom taxonomies', 'frontend-publishing'); ?></li>
						<li>- <?php _e('Unlimited forms', 'frontend-publishing'); ?></li>
						<li>- <?php _e('Drag and drop form builder', 'frontend-publishing'); ?></li>
						<li>- <?php _e('Media restrictions', 'frontend-publishing'); ?></li>
						<li>- <?php _e('Auto-drafts', 'frontend-publishing'); ?></li>
						<li>- <?php _e('Anonymous Posting', 'frontend-publishing'); ?></li>
					</ul>
					<div style="text-align:center;"><a class="button button-primary"
													   href="http://wpfrontendpublishing.com/"><?php _e('Try It Now!', 'frontend-publishing'); ?></a>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php }

function fep_create_options()
{
	add_settings_section('fep_restrictions_section', null, null, 'fep_settings');
	add_settings_section('fep_role_section', null, null, 'fep_settings');
	add_settings_section('fep_misc_section', null, null, 'fep_settings');

	add_settings_field(
		'title_word_count', '', 'fep_render_settings_field', 'fep_settings', 'fep_restrictions_section',
		array(
			'title' => __('Title Word Count', 'frontend-publishing'),
			'desc'  => __('Required word count for article title', 'frontend-publishing'),
			'id'    => 'title_word_count',
			'type'  => 'multitext',
			'items' => array(
				'min_words_title' => __('Minimum', 'frontend-publishing'),
				'max_words_title' => __('Maximum', 'frontend-publishing')
			),
			'group' => 'fep_post_restrictions'
		)
	);
	add_settings_field(
		'content_word_count', '', 'fep_render_settings_field', 'fep_settings', 'fep_restrictions_section',
		array(
			'title' => __('Content Word Count', 'frontend-publishing'),
			'desc'  => __('Required word count for article content', 'frontend-publishing'),
			'id'    => 'content_word_count',
			'type'  => 'multitext',
			'items' => array(
				'min_words_content' => __('Minimum', 'frontend-publishing'),
				'max_words_content' => __('Maximum', 'frontend-publishing')
			),
			'group' => 'fep_post_restrictions'
		)
	);
	add_settings_field(
		'bio_word_count', '', 'fep_render_settings_field', 'fep_settings', 'fep_restrictions_section',
		array(
			'title' => __('Bio Word Count', 'frontend-publishing'),
			'desc'  => __('Required word count for author bio', 'frontend-publishing'),
			'id'    => 'bio_word_count',
			'type'  => 'multitext',
			'items' => array(
				'min_words_bio' => __('Minimum', 'frontend-publishing'),
				'max_words_bio' => __('Maximum', 'frontend-publishing')
			),
			'group' => 'fep_post_restrictions'
		)
	);
	add_settings_field(
		'tag_count', '', 'fep_render_settings_field', 'fep_settings', 'fep_restrictions_section',
		array(
			'title' => __('Tag Count', 'frontend-publishing'),
			'desc'  => __('Required number of tags', 'frontend-publishing'),
			'id'    => 'tag_count',
			'type'  => 'multitext',
			'items' => array(
				'min_tags' => __('Minimum', 'frontend-publishing'),
				'max_tags' => __('Maximum', 'frontend-publishing')
			),
			'group' => 'fep_post_restrictions'
		)
	);
	add_settings_field(
		'max_links', '', 'fep_render_settings_field', 'fep_settings', 'fep_restrictions_section',
		array(
			'title' => __('Maximum Links in Body', 'frontend-publishing'),
			'desc'  => '',
			'id'    => 'max_links',
			'type'  => 'text',
			'group' => 'fep_post_restrictions'
		)
	);
	add_settings_field(
		'max_links_bio', '', 'fep_render_settings_field', 'fep_settings', 'fep_restrictions_section',
		array(
			'title' => __('Maximum links in bio', 'frontend-publishing'),
			'desc'  => '',
			'id'    => 'max_links_bio',
			'type'  => 'text',
			'group' => 'fep_post_restrictions'
		)
	);
	add_settings_field(
		'thumbnail_required', '', 'fep_render_settings_field', 'fep_settings', 'fep_restrictions_section',
		array(
			'title' => __('Make featured image required', 'frontend-publishing'),
			'desc'  => '',
			'id'    => 'thumbnail_required',
			'type'  => 'checkbox',
			'group' => 'fep_post_restrictions'
		)
	);
	$user_roles = array(
		0                      => __('No one', 'frontend-publishing'),
		'update_core'          => __('Administrator', 'frontend-publishing'),
		'moderate_comments'    => __('Editor', 'frontend-publishing'),
		'edit_published_posts' => __('Author', 'frontend-publishing'),
		'edit_posts'           => __('Contributor', 'frontend-publishing'),
		'read'                 => __('Subscriber', 'frontend-publishing')
	);
	add_settings_field(
		'no_check', '', 'fep_render_settings_field', 'fep_settings', 'fep_role_section',
		array(
			'title'   => __('Disable checks for', 'frontend-publishing'),
			'desc'    => __('Submissions by users of this level and levels higher than this will not be checked', 'frontend-publishing'),
			'id'      => 'no_check',
			'type'    => 'select',
			'options' => $user_roles,
			'group'   => 'fep_role_settings'
		)
	);
	add_settings_field(
		'instantly_publish', '', 'fep_render_settings_field', 'fep_settings', 'fep_role_section',
		array(
			'title'   => __('Instantly publish posts by', 'frontend-publishing'),
			'desc'    => __('Submissions by users of this level and levels higher than this will be instantly published', 'frontend-publishing'),
			'id'      => 'instantly_publish',
			'type'    => 'select',
			'options' => $user_roles,
			'group'   => 'fep_role_settings'
		)
	);

	$media_roles = $user_roles;
	$media_roles[0] = __('Everybody', 'frontend-publishing');
	add_settings_field(
		'enable_media', '', 'fep_render_settings_field', 'fep_settings', 'fep_role_section',
		array(
			'title'   => __('Display media buttons to', 'frontend-publishing'),
			'desc'    => __('Users of this level and levels higher than this will see the media buttons', 'frontend-publishing'),
			'id'      => 'enable_media',
			'type'    => 'select',
			'options' => $media_roles,
			'group'   => 'fep_role_settings'
		)
	);

	add_settings_field(
		'before_author_bio', '', 'fep_render_settings_field', 'fep_settings', 'fep_misc_section',
		array(
			'title' => __('Display before bio', 'frontend-publishing'),
			'desc'  => __('The contents of this textarea will be placed before the author bio throughout the website (If author bios are visible)', 'frontend-publishing'),
			'id'    => 'before_author_bio',
			'type'  => 'textarea',
			'group' => 'fep_misc'
		)
	);

	add_settings_field(
		'disable_author_bio', '', 'fep_render_settings_field', 'fep_settings', 'fep_misc_section',
		array(
			'title' => __('Disable Author Bio', 'frontend-publishing'),
			'desc'  => __('Check to disable and hide the author bio field on the submission form. Author bios will still be visible on the website', 'frontend-publishing'),
			'id'    => 'disable_author_bio',
			'type'  => 'checkbox',
			'group' => 'fep_misc'
		)
	);
	add_settings_field(
		'remove_bios', '', 'fep_render_settings_field', 'fep_settings', 'fep_misc_section',
		array(
			'title' => __('Hide all Author Bios', 'frontend-publishing'),
			'desc'  => __('Check to hide author bios from the website', 'frontend-publishing'),
			'id'    => 'remove_bios',
			'type'  => 'checkbox',
			'group' => 'fep_misc'
		)
	);
	add_settings_field(
		'nofollow_body_links', '', 'fep_render_settings_field', 'fep_settings', 'fep_misc_section',
		array(
			'title' => __('Nofollow Body Links', 'frontend-publishing'),
			'desc'  => __('The nofollow attribute will be added to all links in article content', 'frontend-publishing'),
			'id'    => 'nofollow_body_links',
			'type'  => 'checkbox',
			'group' => 'fep_misc'
		)
	);
	add_settings_field(
		'nofollow_bio_links', '', 'fep_render_settings_field', 'fep_settings', 'fep_misc_section',
		array(
			'title' => __('Nofollow Bio Links', 'frontend-publishing'),
			'desc'  => __('The nofollow attribute will be added to all links in author bio'),
			'id'    => 'nofollow_bio_links',
			'type'  => 'checkbox',
			'group' => 'fep_misc'
		)
	);
	add_settings_field(
		'disable_login_redirection', '', 'fep_render_settings_field', 'fep_settings', 'fep_misc_section',
		array(
			'title' => __('Disable Redirection to Login Page', 'frontend-publishing'),
			'desc'  => __('Instead of being sent to the login page, users will be shown an error message', 'frontend-publishing'),
			'id'    => 'disable_login_redirection',
			'type'  => 'checkbox',
			'group' => 'fep_misc'
		)
	);
	add_settings_field(
		'posts_per_page', '', 'fep_render_settings_field', 'fep_settings', 'fep_misc_section',
		array(
			'title' => __('Posts Per Page', 'frontend-publishing'),
			'desc'  => __('Number of posts to display at a time on the interface created with the help of [fep_article_list]', 'frontend-publishing'),
			'id'    => 'posts_per_page',
			'type'  => 'text',
			'group' => 'fep_misc'
		)
	);
	// Finally, we register the fields with WordPress
	register_setting('fep_settings', 'fep_post_restrictions', 'fep_settings_validation');
	register_setting('fep_settings', 'fep_role_settings', 'fep_settings_validation');
	register_setting('fep_settings', 'fep_misc', 'fep_settings_validation');

} // end sandbox_initialize_theme_options 
add_action('admin_init', 'fep_create_options');

function fep_settings_validation($input)
{
	return $input;
}

function fep_add_meta_boxes()
{
	add_meta_box("fep_post_restrictions_metabox", __('Post Restrictions', 'frontend-publishing'), "fep_metaboxes_callback", "fep_metaboxes", 'advanced', 'default', array('settings_section' => 'fep_restrictions_section'));
	add_meta_box("fep_role_settings_metabox", __('Role Settings', 'frontend-publishing'), "fep_metaboxes_callback", "fep_metaboxes", 'advanced', 'default', array('settings_section' => 'fep_role_section'));
	add_meta_box("fep_misc_metabox", __('Misc Settings', 'frontend-publishing'), "fep_metaboxes_callback", "fep_metaboxes", 'advanced', 'default', array('settings_section' => 'fep_misc_section'));
}

add_action('admin_init', 'fep_add_meta_boxes');

function fep_metaboxes_callback($post, $args)
{
	do_settings_fields("fep_settings", $args['args']['settings_section']);
	submit_button(__('Save Changes', 'frontend-publishing'), 'secondary');
}

function fep_render_settings_field($args)
{
	$option_value = get_option($args['group']);
	?>
	<div class="row clearfix">
		<div class="col colone"><?php echo $args['title']; ?></div>
		<div class="col coltwo">
			<?php if ($args['type'] == 'text'): ?>
				<input type="text" id="<?php echo $args['id'] ?>"
					   name="<?php echo $args['group'] . '[' . $args['id'] . ']'; ?>"
					   value="<?php echo (isset($option_value[ $args['id'] ])) ? esc_attr($option_value[ $args['id'] ]) : ''; ?>">
			<?php elseif ($args['type'] == 'select'): ?>
				<select name="<?php echo $args['group'] . '[' . $args['id'] . ']'; ?>" id="<?php echo $args['id']; ?>">
					<?php foreach ($args['options'] as $key => $option) { ?>
						<option <?php if (isset($option_value[ $args['id'] ])) selected($option_value[ $args['id'] ], $key);
						echo 'value="' . $key . '"'; ?>><?php echo $option; ?></option><?php } ?>
				</select>
			<?php elseif ($args['type'] == 'checkbox'): ?>
				<input type="hidden" name="<?php echo $args['group'] . '[' . $args['id'] . ']'; ?>" value="0"/>
				<input type="checkbox" name="<?php echo $args['group'] . '[' . $args['id'] . ']'; ?>"
					   id="<?php echo $args['id']; ?>"
					   value="true" <?php if (isset($option_value[ $args['id'] ])) checked($option_value[ $args['id'] ], 'true'); ?> />
			<?php elseif ($args['type'] == 'textarea'): ?>
				<textarea name="<?php echo $args['group'] . '[' . $args['id'] . ']'; ?>"
						  type="<?php echo $args['type']; ?>" cols=""
						  rows=""><?php echo isset($option_value[ $args['id'] ]) ? stripslashes(esc_textarea($option_value[ $args['id'] ])) : ''; ?></textarea>
			<?php elseif ($args['type'] == 'multicheckbox'):
				foreach ($args['items'] as $key => $checkboxitem):
					?>
					<input type="hidden" name="<?php echo $args['group'] . '[' . $args['id'] . '][' . $key . ']'; ?>"
						   value="0"/>
					<label
						for="<?php echo $args['group'] . '[' . $args['id'] . '][' . $key . ']'; ?>"><?php echo $checkboxitem; ?></label>
					<input type="checkbox" name="<?php echo $args['group'] . '[' . $args['id'] . '][' . $key . ']'; ?>"
						   id="<?php echo $args['group'] . '[' . $args['id'] . '][' . $key . ']'; ?>" value="1"
						   <?php if ($key == 'reason'){ ?>checked="checked" disabled="disabled"<?php } else {
						checked($option_value[ $args['id'] ][ $key ]);
					} ?> />
				<?php endforeach; ?>
			<?php elseif ($args['type'] == 'multitext'):
				foreach ($args['items'] as $key => $textitem):
					?>
					<label for="<?php echo $args['group'] . '[' . $key . ']'; ?>"><?php echo $textitem; ?></label>
					<input type="text" id="<?php echo $args['group'] . '[' . $key . ']'; ?>" class="multitext"
						   name="<?php echo $args['group'] . '[' . $key . ']'; ?>"
						   value="<?php echo (isset($option_value[ $key ])) ? esc_attr($option_value[ $key ]) : ''; ?>">
				<?php endforeach; endif; ?>
		</div>
		<div class="col colthree">
			<small><?php echo $args['desc'] ?></small>
		</div>
	</div>
	<?php
}

?>