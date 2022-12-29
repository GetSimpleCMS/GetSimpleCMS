<?php
$thisfile = basename(__FILE__, ".php");
require_once("blog/inc/common.php");

# add in this plugin's language file
if(file_exists(BLOGSETTINGS))
{
	$settings_lang = getXML(BLOGSETTINGS);
	$LANG = $settings_lang->lang;
}
else
{
	$LNAG = "en_US";
}
i18n_merge($thisfile) || i18n_merge($LANG);

# register plugin
register_plugin(
	$thisfile, // ID of plugin, should be filename minus php
	i18n_r(BLOGFILE.'/PLUGIN_TITLE'), 	
	'1.2.2', 		
	'Mike Henken',
	'http://michaelhenken.com/', 
	i18n_r(BLOGFILE.'/PLUGIN_DESC'),
	'pages',
	'blog_Admin'  
);

add_action('pages-sidebar','createSideMenu',array($thisfile, i18n_r(BLOGFILE.'/PLUGIN_SIDE')));
# add_filter('content', 'blog_display_posts');
add_action('index-pretemplate', 'blog_display_posts');
add_action('theme-header', 'shareThisToolHeader');
//Include Blog class
require_once(BLOGPLUGINFOLDER.'class/Blog.php');
require_once(BLOGPLUGINFOLDER.'class/customFields.php');

/** 
* Show admin plugin navigation bar
* 
* @return void echos
*/  
function showAdminNav()
{
	?>
	<style>
		img.rss_feed {
			margin-left:14px;
		}
		.odd_meta {
			float: left;
			width: 48%;
		}
		.even_meta {
			float: right;
			width: 48%;
		}
	</style>
	<div style="width:100%;margin:0 -15px -15px -10px;padding:0px;">
		<h3  class="floated"><?php i18n(BLOGFILE.'/PLUGIN_TITLE'); ?></h3>
		<div class="edit-nav clearfix">
			<p>
				<a href="load.php?id=blog&help" <?php echo (isset($_GET['help']) ? 'class="current"' : false); ?>><?php i18n(BLOGFILE.'/HELP'); ?></a>
				<a href="load.php?id=blog&settings" <?php echo (isset($_GET['settings']) ? 'class="current"' : false); ?>><?php i18n(BLOGFILE.'/SETTINGS'); ?></a>
				<a href="load.php?id=blog&custom_fields" <?php echo (isset($_GET['custom_fields']) ? 'class="current"' : false); ?>><?php i18n(BLOGFILE.'/CUSTOM_FIELDS'); ?></a>
				<a href="load.php?id=blog&auto_importer" <?php echo (isset($_GET['auto_importer']) ? 'class="current"' : false); ?>><?php i18n(BLOGFILE.'/RSS_FEEDS'); ?></a>
				<a href="load.php?id=blog&categories" <?php echo (isset($_GET['categories']) ? 'class="current"' : false); ?>><?php i18n(BLOGFILE.'/CATEGORIES'); ?></a>
				<a href="load.php?id=blog&create_post" <?php echo (isset($_GET['create_post']) ? 'class="current"' : false); ?>><?php i18n(BLOGFILE.'/CREATE_POST'); ?></a>
				<a href="load.php?id=blog&manage" <?php echo (isset($_GET['manage']) ? 'class="current"' : false); ?>><?php i18n(BLOGFILE.'/MANAGE_POSTS'); ?></a>
			</p>
		</div>
	</div>
	</div>
	<div class="main" style="margin-top:-10px;">
	<?php
}

/** 
* Handles conditionals for admin functions
* 
* @return void
*/  
function blog_admin()
{
	$Blog = new Blog;
	showAdminNav();

	if(isset($_GET['edit_post']))
	{
		editPost($_GET['edit_post']);
	}
	elseif(isset($_GET['create_post']))
	{
		editPost();
	}
	elseif(isset($_GET['categories']))
	{
		if(isset($_GET['edit_category']))
		{
			$add_category = $Blog->saveCategory($_POST['new_category']);
			if($add_category == true)
			{
				echo '<div class="updated">';
				i18n(BLOGFILE.'/CATEGORY_ADDED');
				echo '</div>';
			}
			else
			{
				echo '<div class="error">';
				i18n(BLOGFILE.'/CATEGORY_ERROR');
				echo '</div>';
			}
		}
		if(isset($_GET['delete_category']))
		{
			$Blog->deleteCategory($_GET['delete_category']);
		}
		edit_categories();
	}
	elseif(isset($_GET['auto_importer']))
	{
		if(isset($_POST['post-rss']))
		{
			$post_data = array();
			$post_data['name'] = $_POST['post-rss'];
			$post_data['category'] = $_POST['post-category'];
			$add_feed = $Blog->saveRSS($post_data);
			if($add_feed == true)
			{
				echo '<div class="updated">';
				i18n(BLOGFILE.'/FEED_ADDED');
				echo '</div>';
			}
			else
			{
				echo '<div class="error">';
				i18n(BLOGFILE.'/FEED_ERROR');
				echo '</div>';
			}
		}
		elseif(isset($_GET['delete_rss']))
		{
			$delete_feed = $Blog->deleteRSS($_GET['delete_rss']);
			if($delete_feed == true)
			{
				echo '<div class="updated">';
				i18n(BLOGFILE.'/FEED_DELETED');
				echo '</div>';
			}
			else
			{
				echo '<div class="error">';
				i18n(BLOGFILE.'/FEED_DELETE_ERROR');
				echo '</div>';
			}
		}
		edit_rss();
	}
	elseif(isset($_GET['settings']))
	{
		show_settings_admin();
	}
	elseif(isset($_GET['help']))
	{
		show_help_admin();
	}
	elseif(isset($_GET['custom_fields']))
	{
		$CustomFields = new customFields;
		if(isset($_POST['save_custom_fields']))
		{
			$saveCustomFields = $CustomFields->saveCustomFields();
			if($saveCustomFields)
			{
				echo '<div class="updated">'.i18n_r(BLOGFILE.'/EDIT_OK').'</div>';
			}
		}
		show_custom_fields();
	}
	else
	{
		if(isset($_GET['save_post']))
		{
			savePost();
		}
		elseif(isset($_GET['delete_post']))
		{
			$post_id = urldecode($_GET['delete_post']);
			$delete_post = $Blog->deletePost($post_id);
			if($delete_post == true)
			{
				echo '<div class="updated">';
				i18n(BLOGFILE.'/POST_DELETED');
				echo '</div>';
			}
			else
			{
				echo '<div class="error">';
				i18n(BLOGFILE.'/FEED_DELETE_ERROR');
				echo '</div>';
			}
		}
		show_posts_admin();
	}
}

/** 
* Shows blog posts in admin panel
* 
* @return void
*/  
function show_posts_admin()
{
	$Blog = new Blog;
	$all_posts = $Blog->listPosts(true, true);
	if($all_posts == false)
	{
		echo '<strong>'.i18n_r(BLOGFILE.'/NO_POSTS').'. <a href="load.php?id=blog&create_post">'.i18n_r(BLOGFILE.'/CLICK_TO_CREATE').'</a>';
	}
	else
	{
		?>
		<table class="edittable highlight paginate">
			<tr>
				<th><?php i18n(BLOGFILE.'/PAGE_TITLE'); ?></th>
				<th style="text-align:right;" ><?php i18n(BLOGFILE.'/DATE'); ?></th>
				<th></th>
			</tr>
		<?php
		foreach($all_posts as $post_name)
		{
			$post = $Blog->getPostData($post_name['filename']);
			?>
				<tr>
					<td class="blog_post_title"><a title="Edit Page: Agents" href="load.php?id=blog&edit_post=<?php echo $post->slug; ?>" ><?php echo $post->title; ?></a></td>
					<td style="text-align:right;"><span><?php echo $post->date; ?></span></td>
					<td class="delete" ><a class="delconfirm" href="load.php?id=blog&delete_post=<?php echo $post->slug; ?>" title="Delete Post: <?php echo $post->title; ?>" >X</a></td>
				</tr>
			<?php
		}
		echo '</table>';
	}
}

/** 
* Settings panel for admin area
* 
* @return void
*/  
function show_settings_admin()
{
	$Blog = new Blog;
	if(isset($_POST['blog_settings']))
	{
		$prettyurls = isset($_POST['pretty_urls']) ? $_POST['pretty_urls'] : '';
		$blog_page = $_POST['blog_page'];
		$blog_settings_array = array('blogurl' => $_POST['blog_url'],
									 'lang' => $_POST['language'],
									 'excerptlength' => $_POST['excerpt_length'],
									 'postformat' => $_POST['show_excerpt'],
									 'postperpage' => $_POST['posts_per_page'],
									 'recentposts' => $_POST['recent_posts'],
									 'prettyurls' => $prettyurls,
									 'autoimporter' => $_POST['auto_importer'],
									 'autoimporterpass' => $_POST['auto_importer_pass'],
									 'displaytags' => $_POST['show_tags'],
									 'rsstitle' => $_POST['rss_title'],
									 'rssdescription' => $_POST['rss_description'],
									 'comments' => $_POST['comments'],
									 'disqusshortname' => $_POST['disqus_shortname'],
									 'disquscount' => $_POST['disqus_count'],
									 'sharethis' => $_POST['sharethis'],
									 'sharethisid' => $_POST['sharethis_id'],
									 'addthis' => $_POST['addthis'],
									 'addthisid' => $_POST['addthis_id'],
									 'addata' => $_POST['ad_data'],
									 'allpostsadtop' => $_POST['all_posts_ad_top'],
									 'allpostsadbottom' => $_POST['all_posts_ad_bottom'],
									 'postadtop' => $_POST['post_ad_top'],
									 'postadbottom' => $_POST['post_ad_bottom'],
									 'postthumbnail' => $_POST['post_thumbnail'],
									 'displaydate' => $_POST['display_date'],
									 'previouspage' => $_POST['previous_page'],
									 'nextpage' => $_POST['next_page'],
									 'displaycss' => $_POST['display_css'],
									 'csscode' => $_POST['css_code'],
									 'rssfeedposts' => $_POST['rss_feed_num_posts'],
									 'customfields' => $_POST['custom_fields'],
									 'blogpage' => $blog_page);
		$Blog->saveSettings($blog_settings_array);
	}
	?>
	<h3><?php i18n(BLOGFILE.'/BLOG_SETTINGS'); ?></h3>
	<form class="largeform" action="load.php?id=blog&settings" method="post" accept-charset="utf-8">
		<div class="leftsec">
			<p>
				<label for="page-url"><?php i18n(BLOGFILE.'/PAGE_URL'); ?>:</label>
				<select class="text" name="blog_url">
					<?php
					$pages = get_available_pages();
					foreach ($pages as $page) 
					{
						$slug = $page['slug'];
						if ($slug == $Blog->getSettingsData("blogurl"))
						{
							echo "<option value=\"$slug\" selected=\"selected\">$slug</option>\n";
						}
						else
						{
							echo "<option value=\"$slug\">$slug</option>\n";	
						}
					}
					?>
				</select>
			</p>
		</div>
		<div class="rightsec">
			<p>
				<label for="language"><?php i18n(BLOGFILE.'/LANGUAGE'); ?></label>
				<select class="text" name="language">
					<?php
					$languages = $Blog->blog_get_languages();
					foreach ($languages as $lang) 
					{
						if ($lang == $Blog->getSettingsData("lang"))
						{
							echo '<option value="'.$lang.'" selected="selected">'.$lang.'</option>';
						}
						else
						{
							echo '<option value="'.$lang.'">'.$lang.'</option>';
						}
					}
					?>
				</select>
			</p>
		</div>
		<div class="clear"></div>
		<div class="leftsec">
			<p>
				<label for="excerpt_length"><?php i18n(BLOGFILE.'/EXCERPT_LENGTH'); ?>:</label>
				<input class="text" type="text" name="excerpt_length" value="<?php echo $Blog->getSettingsData("excerptlength"); ?>" />
			</p>
		</div>
		<div class="rightsec">
			<p>
				<label for="show_excerpt"><?php i18n(BLOGFILE.'/EXCERPT_OPTION'); ?>:</label>
				<input name="show_excerpt" type="radio" value="Y" <?php if ($Blog->getSettingsData("postformat") == 'Y') echo 'checked="checked"'; ?> style="vertical-align: middle;" />
				&nbsp;<?php i18n(BLOGFILE.'/FULL_TEXT'); ?>
				<span style="margin-left: 30px;">&nbsp;</span>
				<input name="show_excerpt" type="radio" value="N" <?php if ($Blog->getSettingsData("postformat") != 'Y') echo 'checked="checked"'; ?> style="vertical-align: middle;" />
				&nbsp;<?php i18n(BLOGFILE.'/EXCERPT'); ?>
			</p>
		</div>
		<div class="clear"></div>
		<div class="leftsec">
			<p>
				<label for="posts_per_page"><?php i18n(BLOGFILE.'/POSTS_PER_PAGE'); ?>:</label>
				<input class="text" type="text" name="posts_per_page" value="<?php echo $Blog->getSettingsData("postperpage"); ?>" />
			</p>
		</div>
		<div class="rightsec">
			<p>
				<label for="recent_posts"><?php i18n(BLOGFILE.'/RECENT_POSTS'); ?>:</label>
				<input class="text" type="text" name="recent_posts" value="<?php echo $Blog->getSettingsData("recentposts"); ?>" />
			</p>
		</div>
		<div class="clear"></div>
		<div class="leftsec">
			<p>
				<label for="auto_importer"><?php i18n(BLOGFILE.'/RSS_IMPORTER'); ?>:</label>
				<input name="auto_importer" type="radio" value="Y" <?php if ($Blog->getSettingsData("autoimporter") == 'Y') echo 'checked="checked"'; ?> style="vertical-align: middle;" />
				&nbsp;<?php i18n(BLOGFILE.'/YES'); ?>
				<span style="margin-left: 30px;">&nbsp;</span>
				<input name="auto_importer" type="radio" value="N" <?php if ($Blog->getSettingsData("autoimporter") != 'Y') echo 'checked="checked"'; ?> style="vertical-align: middle;" />
				&nbsp;<?php i18n(BLOGFILE.'/NO'); ?>
			</p>
		</div>
		<div class="rightsec">
			<p>
				<label for="recent_posts"><?php i18n(BLOGFILE.'/RSS_IMPORTER_PASS'); ?>:</label>
				<input class="text" type="text" name="auto_importer_pass" value="<?php echo $Blog->getSettingsData("autoimporterpass"); ?>" />
			</p>
		</div>
		<div class="clear"></div>
		<div class="leftsec">
			<p>
				<label for="posts_per_page"><?php i18n(BLOGFILE.'/DISPLAY_TAGS_UNDER_POST'); ?>:</label>
				<input name="show_tags" type="radio" value="Y" <?php if ($Blog->getSettingsData("displaytags") == 'Y') echo 'checked="checked"'; ?> style="vertical-align: middle;" />
				&nbsp;<?php i18n(BLOGFILE.'/YES'); ?>
				<span style="margin-left: 30px;">&nbsp;</span>
				<input name="show_tags" type="radio" value="N" <?php if ($Blog->getSettingsData("displaytags") != 'Y') echo 'checked="checked"'; ?> style="vertical-align: middle;" />
				&nbsp;<?php i18n(BLOGFILE.'/NO'); ?>
			</p>
		</div>
		<div class="rightsec">
			<p>
				<label for="post_thumbnail"><?php i18n(BLOGFILE.'/POST_THUMBNAIL'); ?>:</label>
				<input name="post_thumbnail" type="radio" value="Y" <?php if ($Blog->getSettingsData("postthumbnail") == 'Y') echo 'checked="checked"'; ?> style="vertical-align: middle;" />
				&nbsp;<?php i18n(BLOGFILE.'/YES'); ?>
				<span style="margin-left: 30px;">&nbsp;</span>
				<input name="post_thumbnail" type="radio" value="N" <?php if ($Blog->getSettingsData("postthumbnail") != 'Y') echo 'checked="checked"'; ?> style="vertical-align: middle;" />
				&nbsp;<?php i18n(BLOGFILE.'/NO'); ?>
			</p>
		</div>
		<div class="clear"></div>
		<div class="leftsec">
			<p>
				<label for="display_date"><?php i18n(BLOGFILE.'/DISPLAY_DATE'); ?>:</label>
				<input name="display_date" type="radio" value="Y" <?php if ($Blog->getSettingsData("displaydate") == 'Y') echo 'checked="checked"'; ?> style="vertical-align: middle;" />
				&nbsp;<?php i18n(BLOGFILE.'/YES'); ?>
				<span style="margin-left: 30px;">&nbsp;</span>
				<input name="display_date" type="radio" value="N" <?php if ($Blog->getSettingsData("displaydate") != 'Y') echo 'checked="checked"'; ?> style="vertical-align: middle;" />
				&nbsp;<?php i18n(BLOGFILE.'/NO'); ?>
			</p>
		</div>
		<div class="rightsec">
			<p>
				<label for="custom_fields"><?php i18n(BLOGFILE.'/USE_CUSTOM_BLOG_PAGE'); ?>:</label>
				<input name="custom_fields" type="radio" value="Y" <?php if ($Blog->getSettingsData("customfields") == 'Y') echo 'checked="checked"'; ?> style="vertical-align: middle;" />
				&nbsp;<?php i18n(BLOGFILE.'/YES'); ?>
				<span style="margin-left: 30px;">&nbsp;</span>
				<input name="custom_fields" type="radio" value="N" <?php if ($Blog->getSettingsData("customfields") != 'Y') echo 'checked="checked"'; ?> style="vertical-align: middle;" />
				&nbsp;<?php i18n(BLOGFILE.'/NO'); ?>
			</p>
		</div>
		<div class="clear"></div>
		<div class="leftsec">
			<p>
				<label for="previous_page"><?php i18n(BLOGFILE.'/PREVIOUS_PAGE_TEXT'); ?>:</label>
				<input class="text" type="text" name="previous_page" value="<?php echo $Blog->getSettingsData("previouspage"); ?>" />
			</p>
		</div>
		<div class="rightsec">
			<p>
				<label for="next_page"><?php i18n(BLOGFILE.'/NEXT_PAGE_TEXT'); ?>:</label>
				<input class="text" type="text" name="next_page" value="<?php echo $Blog->getSettingsData("nextpage"); ?>" />
			</p>
		</div>
		<div class="clear"></div>
		<h3><?php i18n(BLOGFILE.'/RSS_FILE_SETTINGS'); ?></h3>
		<div class="leftsec">
			<p>
				<label for="rss_title"><?php i18n(BLOGFILE.'/RSS_TITLE'); ?>:</label>
				<input class="text" type="text" name="rss_title" value="<?php echo $Blog->getSettingsData("rsstitle"); ?>" />
			</p>
		</div>
		<div class="rightsec">
			<p>
				<label for="rss_description"><?php i18n(BLOGFILE.'/RSS_DESCRIPTION'); ?>:</label>
				<input class="text" type="text" name="rss_description" value="<?php echo $Blog->getSettingsData("rssdescription"); ?>" />
			</p>
		</div>
		<div class="clear"></div>
		<div class="leftsec">
			<p>
				<label for="rss_feed_num_posts"><?php i18n(BLOGFILE.'/RSS_FEED_NUM_POSTS'); ?>:</label>
				<input class="text" type="text" name="rss_feed_num_posts" value="<?php echo $Blog->getSettingsData("rssfeedposts"); ?>" />
			</p>
		</div>
		<div class="clear"></div>
		<h3><?php i18n(BLOGFILE.'/SOCIAL_SETTINGS'); ?></h3>
		<div class="leftsec">
			<p>
				<label for="comments"><?php i18n(BLOGFILE.'/DISPLAY_DISQUS_COMMENTS'); ?>:</label>
				<input name="comments" type="radio" value="Y" <?php if ($Blog->getSettingsData("comments") == 'Y') echo 'checked="checked"'; ?> style="vertical-align: middle;" />
				&nbsp;<?php i18n(BLOGFILE.'/YES'); ?>
				<span style="margin-left: 30px;">&nbsp;</span>
				<input name="comments" type="radio" value="N" <?php if ($Blog->getSettingsData("comments") != 'Y') echo 'checked="checked"'; ?> style="vertical-align: middle;" />
				&nbsp;<?php i18n(BLOGFILE.'/NO'); ?>
			</p>
		</div>
		<div class="rightsec">
			<p>
				<label for="disqus_shortname"><?php i18n(BLOGFILE.'/DISQUS_SHORTNAME'); ?>:</label>
				<input class="text" type="text" name="disqus_shortname" value="<?php echo $Blog->getSettingsData("disqusshortname"); ?>" />
			</p>
		</div>
		<div class="clear"></div>
		<div class="leftsec">
			<p>
				<label for="posts_per_page"><?php i18n(BLOGFILE.'/DISPLAY_DISQUS_COUNT'); ?>:</label>
				<input name="disqus_count" type="radio" value="Y" <?php if ($Blog->getSettingsData("disquscount") == 'Y') echo 'checked="checked"'; ?> style="vertical-align: middle;" />
				&nbsp;<?php i18n(BLOGFILE.'/YES'); ?>
				<span style="margin-left: 30px;">&nbsp;</span>
				<input name="disqus_count" type="radio" value="N" <?php if ($Blog->getSettingsData("disquscount") != 'Y') echo 'checked="checked"'; ?> style="vertical-align: middle;" />
				&nbsp;<?php i18n(BLOGFILE.'/NO'); ?>
			</p>
		</div>
		<div class="clear"></div>
		<div class="leftsec">
			<p>
				<label for="sharethis"><?php i18n(BLOGFILE.'/ENABLE_SHARE_THIS'); ?>:</label>
				<input name="sharethis" type="radio" value="Y" <?php if ($Blog->getSettingsData("sharethis") == 'Y') echo 'checked="checked"'; ?> style="vertical-align: middle;" />
				&nbsp;<?php i18n(BLOGFILE.'/YES'); ?>
				<span style="margin-left: 30px;">&nbsp;</span>
				<input name="sharethis" type="radio" value="N" <?php if ($Blog->getSettingsData("sharethis") != 'Y') echo 'checked="checked"'; ?> style="vertical-align: middle;" />
				&nbsp;<?php i18n(BLOGFILE.'/NO'); ?>
			</p>
		</div>
		<div class="rightsec">
			<p>
				<label for="sharethis_id"><?php i18n(BLOGFILE.'/SHARE_THIS_ID'); ?>:</label>
				<input class="text" type="text" name="sharethis_id" value="<?php echo $Blog->getSettingsData("sharethisid"); ?>" />
			</p>
		</div>
		<div class="clear"></div>
		<div class="leftsec">
			<p>
				<label for="addthis"><?php i18n(BLOGFILE.'/ENABLE_ADD_THIS'); ?>:</label>
				<input name="addthis" type="radio" value="Y" <?php if ($Blog->getSettingsData("addthis") == 'Y') echo 'checked="checked"'; ?> style="vertical-align: middle;" />
				&nbsp;<?php i18n(BLOGFILE.'/YES'); ?>
				<span style="margin-left: 30px;">&nbsp;</span>
				<input name="addthis" type="radio" value="N" <?php if ($Blog->getSettingsData("addthis") != 'Y') echo 'checked="checked"'; ?> style="vertical-align: middle;" />
				&nbsp;<?php i18n(BLOGFILE.'/NO'); ?>
			</p>
		</div>
		<div class="rightsec">
			<p>
				<label for="addthis_id"><?php i18n(BLOGFILE.'/ADD_THIS_ID'); ?>:</label>
				<input class="text" type="text" name="addthis_id" value="<?php echo $Blog->getSettingsData("addthisid"); ?>" />
			</p>
		</div>
		<div class="clear"></div>
		<h3><?php i18n(BLOGFILE.'/AD_TITLE'); ?></h3>
		<div class="leftsec">
			<p>
				<label for="all_posts_ad_top"><?php i18n(BLOGFILE.'/DISPLAY_ALL_POSTS_AD_TOP'); ?>:</label>
				<input name="all_posts_ad_top" type="radio" value="Y" <?php if ($Blog->getSettingsData("allpostsadtop") == 'Y') echo 'checked="checked"'; ?> style="vertical-align: middle;" />
				&nbsp;<?php i18n(BLOGFILE.'/YES'); ?>
				<span style="margin-left: 30px;">&nbsp;</span>
				<input name="all_posts_ad_top" type="radio" value="N" <?php if ($Blog->getSettingsData("allpostsadtop") != 'Y') echo 'checked="checked"'; ?> style="vertical-align: middle;" />
				&nbsp;<?php i18n(BLOGFILE.'/NO'); ?>
			</p>
		</div>
		<div class="rightsec">
			<p>
				<label for="all_posts_ad_bottom"><?php i18n(BLOGFILE.'/DISPLAY_ALL_POSTS_AD_BOTTOM'); ?>:</label>
				<input name="all_posts_ad_bottom" type="radio" value="Y" <?php if ($Blog->getSettingsData("allpostsadbottom") == 'Y') echo 'checked="checked"'; ?> style="vertical-align: middle;" />
				&nbsp;<?php i18n(BLOGFILE.'/YES'); ?>
				<span style="margin-left: 30px;">&nbsp;</span>
				<input name="all_posts_ad_bottom" type="radio" value="N" <?php if ($Blog->getSettingsData("allpostsadbottom") != 'Y') echo 'checked="checked"'; ?> style="vertical-align: middle;" />
				&nbsp;<?php i18n(BLOGFILE.'/NO'); ?>
			</p>
		</div>
		<div class="clear"></div>
		<div class="leftsec">
			<p>
				<label for="post_ad_top"><?php i18n(BLOGFILE.'/DISPLAY_POST_AD_TOP'); ?>:</label>
				<input name="post_ad_top" type="radio" value="Y" <?php if ($Blog->getSettingsData("postadtop") == 'Y') echo 'checked="checked"'; ?> style="vertical-align: middle;" />
				&nbsp;<?php i18n(BLOGFILE.'/YES'); ?>
				<span style="margin-left: 30px;">&nbsp;</span>
				<input name="post_ad_top" type="radio" value="N" <?php if ($Blog->getSettingsData("postadtop") != 'Y') echo 'checked="checked"'; ?> style="vertical-align: middle;" />
				&nbsp;<?php i18n(BLOGFILE.'/NO'); ?>
			</p>
		</div>
		<div class="rightsec">
			<p>
				<label for="post_ad_bottom"><?php i18n(BLOGFILE.'/DISPLAY_POST_AD_BOTTOM'); ?>:</label>
				<input name="post_ad_bottom" type="radio" value="Y" <?php if ($Blog->getSettingsData("postadbottom") == 'Y') echo 'checked="checked"'; ?> style="vertical-align: middle;" />
				&nbsp;<?php i18n(BLOGFILE.'/YES'); ?>
				<span style="margin-left: 30px;">&nbsp;</span>
				<input name="post_ad_bottom" type="radio" value="N" <?php if ($Blog->getSettingsData("postadbottom") != 'Y') echo 'checked="checked"'; ?> style="vertical-align: middle;" />
				&nbsp;<?php i18n(BLOGFILE.'/NO'); ?>
			</p>
		</div>
		<div class="clear"></div>
		<div class="leftec" style="width:100%">
			<p>
				<label for="ad_data"><?php i18n(BLOGFILE.'/AD_DATA'); ?>:</label>
				<textarea name="ad_data" class="text"  style="width:100%;height:100px;"><?php echo $Blog->getSettingsData("addata"); ?></textarea>
			</p>
		</div>
		<div class="clear"></div>
		<h3><?php i18n(BLOGFILE.'/BLOG_PAGE'); ?></h3>
		<div class="leftec" style="width:100%">
			<p>
				<label for="ad_data"><?php i18n(BLOGFILE.'/BLOG_PAGE'); ?>: <span style="color:red;font-size:15px;"><?php i18n(BLOGFILE.'/BLOG_PAGE_WARNING'); ?></span></label>
				<label for="display_css"><a id="blog_page_help" href="#blog_page_help_data"><?php i18n(BLOGFILE.'/DISPLAY_BLOG_PAGE_HELP'); ?></a></label>
				<div style="display:none;">
					<div id="blog_page_help_data">
						<?php blog_page_help_html(); ?>
					</div>
				</div>
				<textarea name="blog_page" id="blog_page" style=""><?php echo $Blog->getSettingsData("blogpage"); ?></textarea>
			</p>
		</div>
		<div class="clear"></div>
		<h3><?php i18n(BLOGFILE.'/CSS_SETTINGS'); ?></h3>
		<div class="leftsec">
			<p>
				<label for="display_css"><?php i18n(BLOGFILE.'/DISPLAY_CSS'); ?>: <a id="css_help" href="#css_data">Click here to view available classes and ids</a></label>
				<div style="display:none;">
					<div id="css_data">
						<h3>Available ids and classes</h3>
						<ul>
							<li>.blog_post_container (<?php i18n(BLOGFILE.'/CSS_POST_CONTAINER_HINT'); ?>)</li>
							<li>.blog_post_title</li>
							<li>.blog_post_date</li>
							<li>.blog_post_content (<?php i18n(BLOGFILE.'/CSS_POST_CONTENT_HINT'); ?>)</li>
							<li>.blog_tags</li>
							<li>.blog_page_navigation</li>
							<li>.blog_prev_page</li>
							<li>.blog_next_page</li>
							<li>.blog_go_back</li>
							<li>.blog_search_button</li>
							<li>.blog_search_input</li>
							<li>.blog_search_header</li>
							<li>#disqus_thread</li>
							<li>#blog_search (id of search form)</li>
						</ul><br/>
						<h3>Below is an example of a single blog post</h3>
<pre>
&lt;div class=&quot;blog_post_container&quot;&gt;<br />
	&lt;h3 class=&quot;blog_post_title&quot;&gt;&lt;a href=&quot;http://link&quot; class=&quot;blog_post_link&quot;&gt;The Post Title&lt;/a&gt;&lt;/h3&gt;<br />
	&lt;p class=&quot;blog_post_date&quot;&gt;<br />
		May 22, 2012			<br />
	&lt;/p&gt;<br />
	&lt;p class=&quot;blog_post_content&quot;&gt;<br />
		&lt;img src=&quot;http://michaelhenken.com/plugin_tests/blog/data/uploads/math-fail-pics-421.jpg&quot; style=&quot;&quot; class=&quot;blog_post_thumbnail&quot; /&gt;<br />
		An essential part of programming is evaluating conditions using if/else and switch/case statements. If / Else statements are easy to code and..	<br />
	&lt;/p&gt;<br />
&lt;/div&gt;<br />
&lt;p class=&quot;blog_tags&quot;&gt;<br />
	&lt;b&gt;Tags :&lt;/b&gt; <br />
	&lt;a href=&quot;http://link&quot;&gt;tags1&lt;/a&gt; &lt;a href=&quot;http://link&quot;&gt;tags2&lt;/a&gt;<br />
&lt;/p&gt;<br />
&lt;div class=&quot;blog_page_navigation&quot;&gt;		<br />
	&lt;div class=&quot;blog_prev_page&quot;&gt;<br />
		&lt;a href=&quot;http://link&quot;&gt;<br />
		&amp;larr; Older Posts		&lt;/a&gt;<br />
	&lt;/div&gt;<br />
	&lt;div class=&quot;blog_next_page&quot;&gt;<br />
		&lt;a href=&quot;http://link&quot;&gt;<br />
			Newer Posts &amp;rarr;<br />
		&lt;/a&gt;<br />
	&lt;/div&gt;<br />
&lt;/div&gt;
</pre>
				</div>
			</div>
				<script type="text/javascript">
					$("a#css_help").fancybox({
						'hideOnContentClick': true
					});
					$("a#blog_page_help").fancybox({
						'hideOnContentClick': true
					});
				</script>
				<script>
			      var editor = CodeMirror.fromTextArea(document.getElementById("blog_page"), {
			        lineNumbers: true,
			        matchBrackets: true,
			        mode: "application/x-httpd-php",
			        indentUnit: 4,
			        indentWithTabs: true,
			        enterMode: "keep",
			        tabMode: "shift",
			        lineWrapping: "true"
			      });
			    </script>

				<input name="display_css" type="radio" value="Y" <?php if ($Blog->getSettingsData("displaycss") == 'Y') echo 'checked="checked"'; ?> style="vertical-align: middle;" />
				&nbsp;<?php i18n(BLOGFILE.'/YES'); ?>
				<span style="margin-left: 30px;">&nbsp;</span>
				<input name="display_css" type="radio" value="N" <?php if ($Blog->getSettingsData("displaycss") != 'Y') echo 'checked="checked"'; ?> style="vertical-align: middle;" />
				&nbsp;<?php i18n(BLOGFILE.'/NO'); ?>
			</p>
		</div>
		<div class="clear"></div>
		<div class="leftec" style="width:100%">
			<p>
				<label for="css_code"><?php i18n(BLOGFILE.'/CSS_CODE'); ?>:</label>
				<textarea name="css_code" class="text"  style="width:100%;height:100px;"><?php echo $Blog->getSettingsData("csscode"); ?></textarea>
			</p>
		</div>
		<div class="clear"></div>
		<h3><?php i18n(BLOGFILE.'/HTACCESS_HEADLINE'); ?></h3>
		<?php global $PRETTYURLS; if ($PRETTYURLS == 1) { ?>
			<p class="inline">
				<input name="pretty_urls" type="checkbox" value="Y" <?php if ($Blog->getSettingsData("prettyurls") == 'Y') echo 'checked'; ?> />&nbsp;
				<label for="pretty_urls"><?php i18n(BLOGFILE.'/PRETTY_URLS'); ?></label> - <span style="color:red;font-weight:bold;"><a href="load.php?id=blog&settings&htaccess#htaccess">View What Your Sites .htaccess Should Be!</a></span> - 
				<span class="hint"><?php i18n(BLOGFILE.'/PRETTY_URLS_PARA'); ?></span>
			</p>
			<?php if($Blog->getSettingsData("prettyurls") == 'Y' && isset($_GET['htaccess'])) { ?>
				<div class="htaccess" id="htaccess" style="padding: 10px;background-color: #F6F6F6;margin: 10px;">
					<pre>
AddDefaultCharset UTF-8
Options -Indexes

# blocks direct access to the XML files - they hold all the data!
&lt;Files ~ "\.xml$"&gt;
    Order allow,deny
    Deny from all
    Satisfy All
&lt;/Files&gt;
&lt;Files sitemap.xml&gt;
    Order allow,deny
    Allow from all
    Satisfy All
&lt;/Files&gt;

RewriteEngine on

# Usually RewriteBase is just '/', but
# replace it with your subdirectory path -- IMPORTANT -> if your site is located in subfolder you need to change this to reflect (eg: /subfolder/)
RewriteBase /

RewriteRule ^<?php if($Blog->getSettingsData("blogurl") != 'index') { echo $Blog->getSettingsData("blogurl").'/'; } ?>post/([^/.]+)/?$ index.php?id=<?php echo $Blog->getSettingsData("blogurl"); ?>&post=$1 [L]
RewriteRule ^<?php if($Blog->getSettingsData("blogurl") != 'index') { echo $Blog->getSettingsData("blogurl").'/'; } ?>tag/([^/.]+)/?$ index.php?id=<?php echo $Blog->getSettingsData("blogurl"); ?>&tag=$1 [L]
RewriteRule ^<?php if($Blog->getSettingsData("blogurl") != 'index') { echo $Blog->getSettingsData("blogurl").'/'; } ?>page/([^/.]+)/?$ index.php?id=<?php echo $Blog->getSettingsData("blogurl"); ?>&page=$1 [L]
RewriteRule ^<?php if($Blog->getSettingsData("blogurl") != 'index') { echo $Blog->getSettingsData("blogurl").'/'; } ?>archive/([^/.]+)/?$ index.php?id=<?php echo $Blog->getSettingsData("blogurl"); ?>&archive=$1 [L]
RewriteRule ^<?php if($Blog->getSettingsData("blogurl") != 'index') { echo $Blog->getSettingsData("blogurl").'/'; } ?>category/([^/.]+)/?$ index.php?id=<?php echo $Blog->getSettingsData("blogurl"); ?>&category=$1 [L]

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule /?([A-Za-z0-9_-]+)/?$ index.php?id=$1 [QSA,L]
					</pre>
				</div>
			<?php } ?>
		<?php } ?>
		<p>
		<span>
		<input class="submit" type="submit" name="blog_settings" value="<?php i18n(BLOGFILE.'/SAVE_SETTINGS'); ?>" />
		</span>
		&nbsp;&nbsp;<?php i18n(BLOGFILE.'/OR'); ?>&nbsp;&nbsp;
		<a href="load.php?id=blog&cancel" class="cancel"><?php i18n(BLOGFILE.'/CANCEL'); ?></a>
		</p>
	</form>
	<h3><?php i18n(BLOGFILE.'/AUTO_IMPORTER_TITLE'); ?></h3>
	<p>
		<?php i18n(BLOGFILE.'/AUTO_IMPORTER_DESC'); ?>
	</p>
<?php
}

/** 
* Edit/Create post screen
* 
* @param $post_id string the id of the post to edit. Null if creating new page
* @return void
*/  
function editPost($post_id=null)
{
	global $SITEURL;
	$Blog = new Blog;
	if($post_id != null)
	{
		$blog_data = getXML(BLOGPOSTSFOLDER.$post_id.'.xml');
	}
	else
	{
		$blog_data = $Blog->getXMLnodes();
	}
	?>
	<link href="../plugins/blog/uploader/client/fileuploader.css" rel="stylesheet" type="text/css">
	<script src="../plugins/blog/uploader/client/fileuploader.js" type="text/javascript"></script>
	<h3 class="floated">
	  <?php
	  if ($post_id == null)
	  {
	  	i18n(BLOGFILE.'/ADD_P');
	  }
	  else
	  {
	  	i18n(BLOGFILE.'/EDIT');
	  }
	  ?>
	</h3>
	<div class="edit-nav" >
		<?php
		if ($post_id != null && file_exists(BLOGPOSTSFOLDER.$blog_data->slug.'.xml')) 
		{
			$url = $Blog->get_blog_url('post');
			?>
			<a href="<?php echo $url.$blog_data->slug; ?>" target="_blank">
				<?php i18n(BLOGFILE.'/VIEW'); echo ' '; i18n(BLOGFILE.'/POST'); ?>
			</a>
			<?php
		}
		?>
		<a href="#" id="metadata_toggle">
			<?php i18n(BLOGFILE.'/POST_OPTIONS'); ?>
		</a>
		<div class="clear"></div>
	</div>
	<form class="largeform" action="load.php?id=blog&save_post" method="post" accept-charset="utf-8">
	<?php if($post_id != null) { echo "<p><input name=\"post-current_slug\" type=\"hidden\" value=\"$blog_data->slug\" /></p>"; } ?>
	<div id="metadata_window" style="display:none;text-align:left;">
		<?php displayCustomFields(); ?>
		<div class="leftopt">
				<label>Upload Thumbnail</label>
			<div class="uploader_container"> 
			    <div id="file-uploader-thumbnail"> 
			        <noscript> 
			            <p>Please enable JavaScript to use file uploader.</p>
			        </noscript> 
			    </div> 
			    <script> 
			   		 var uploader = new qq.FileUploader({
				        element: document.getElementById('file-uploader-thumbnail'),
				        // path to server-side upload script
				        action: '../plugins/blog/uploader/server/php.php',
			        	onComplete: function(id, fileName, responseJSON){
				        	$('#post-thumbnail').attr('value', responseJSON.newFilename);
				    	}

			    }, '<?php i18n(BLOGFILE.'/POST_THUMBNAIL_LABEL'); ?>');
			        window.onload = createUploader;
			    </script>
			</div>
			<input type="text" id="post-thumbnail" name="post-thumbnail" value="<?php echo $blog_data->thumbnail; ?>" style="width:130px;float:right;margin-top:12px !important;" />
		</div>
		<div class="clear"></div>
		</div>

		<?php displayCustomFields('main'); ?>
			<input name="post" type="submit" class="submit" value="<?php i18n(BLOGFILE.'/SAVE_POST'); ?>" />
			&nbsp;&nbsp;<?php i18n(BLOGFILE.'/OR'); ?>&nbsp;&nbsp;
			<a href="load.php?id=news_manager&cancel" class="cancel"><?php i18n(BLOGFILE.'/CANCEL'); ?></a>
			<?php
			if ($post_id != null) 
			{
				?>
				/
				<a href="load.php?id=blog" class="cancel">
					<?php i18n(BLOGFILE.'/DELETE'); ?>
				</a>
				<?php
			}
			?>
		</p>
	</form>
	<script>
	  $(document).ready(function(){
	    $("#post-title").focus();
	  });
	</script>
	<?php
	include BLOGPLUGINFOLDER."ckeditor.php";
}

/** 
* Show Category management area
* 
* @return void
*/  
function edit_categories()
{
	$Blog = new Blog;
	$category_file = getXML(BLOGCATEGORYFILE);
?>
	<h3><?php i18n(BLOGFILE.'/MANAGE_CATEGORIES'); ?></h3>
	<form class="largeform" action="load.php?id=blog&categories&edit_category" method="post">
	  <div class="leftsec">
	    <p>
	      <label for="page-url"><?php i18n(BLOGFILE.'/ADD_CATEGORY'); ?></label>
		  <input class="text" type="text" name="new_category" value="" />
	    </p>
	  </div>
	  <div class="clear"></div>
	  <table class="highlight">
	  <tr>
	  <th><?php i18n(BLOGFILE.'/CATEGORY_NAME'); ?></th>
	  <th><?php i18n(BLOGFILE.'/RSS_FEED'); ?></th>
	  <th><?php i18n(BLOGFILE.'/DELETE'); ?></th>
	  </tr>
	  <?php
	foreach($category_file->category as $category)
	{
		?>
		<tr>
			<td><?php echo $category; ?></td>
			<td><a href="<?php echo $Blog->get_blog_url('rss').'?filter=category&value='.$category; ?>" target="_blank"><img src="../plugins/blog/images/rss_feed.png" class="rss_feed" /></a></td>
			<td class="delete" ><a href="load.php?id=blog&categories&delete_category=<?php echo $category; ?>" title="Delete Category: <?php echo $category; ?>" >X</a></td>
		</tr>
		<?php
	}
	  ?>
	  </table>
	  <p>
	    <span>
	      <input class="submit" type="submit" name="category_edit" value="<?php i18n(BLOGFILE.'/ADD_CATEGORY'); ?>" />
	    </span>
	    &nbsp;&nbsp;<?php i18n(BLOGFILE.'/OR'); ?>&nbsp;&nbsp;
	    <a href="load.php?id=blog" class="cancel"><?php i18n(BLOGFILE.'/CANCEL'); ?></a>
	  </p>
	</form>
<?php
}

/** 
* RSS Feed management area
* 
* @return void
*/  
function edit_rss()
{
	  $rss_file = getXML(BLOGRSSFILE);
?>
	<h3 class="floated"><?php i18n(BLOGFILE.'/MANAGE_FEEDS'); ?></h3>
	<div class="edit-nav" >
		<a href="#" id="metadata_toggle">
			<?php i18n(BLOGFILE.'/ADD_FEED'); ?>
		</a>
	</div>
	  <div class="clear"></div>
	<div id="metadata_window" style="display:none;text-align:left;">
		<form class="largeform" action="load.php?id=blog&auto_importer&add_rss" method="post">
		    <p style="float:left;width:150px;clear:both">
		      <label for="page-url"><?php i18n(BLOGFILE.'/ADD_NEW_FEED'); ?></label>
			  <input class="text" type="text" name="post-rss" value="" style="padding-bottom:5px;" />
		    </p>
		    <p style="float:left;width:100px;margin-left:20px;">
		    	<label for="page-url"><?php i18n(BLOGFILE.'/BLOG_CATEGORY'); ?></label>
				<select class="text" name="post-category">	
					<?php category_dropdown($blog_data->category); ?>
				</select>
		    </p>
		    <p style="float:left;width:200px;margin-left:0px;clear:both">
		    <span>
		      <input class="submit" type="submit" name="rss_edit" value="<?php i18n(BLOGFILE.'/ADD_FEED'); ?>" style="width:auto;" />
		    </span>
		    &nbsp;&nbsp;<?php i18n(BLOGFILE.'/OR'); ?>&nbsp;&nbsp;
		    <a href="load.php?id=blog" class="cancel"><?php i18n(BLOGFILE.'/CANCEL'); ?></a>
		  </p>
		</form>
	</div>
	  <div class="clear"></div>
	  <table class="highlight">
	  <tr>
	  <th><?php i18n(BLOGFILE.'/RSS_FEED'); ?></th><th><?php i18n(BLOGFILE.'/FEED_CATEGORY'); ?></th><th><?php i18n(BLOGFILE.'/DELETE_FEED'); ?></th>
	  </tr>
	  <?php
	foreach($rss_file->rssfeed as $feed)
	{
		$rss_atts = $feed->attributes();
	echo '
	<tr><td>'.$feed->feed.'</td><td>'.$feed->category.'</td><td><a href="load.php?id=blog&auto_importer&delete_rss='.$feed['id'].'">X</a></td></tr>
	';
	}
	  ?>
	  </table>
<?php
}

/** 
* Echos all categories to place into select menu
* 
* @return void
*/  
function category_dropdown($current_category=null)
{
	$category_file = getXML(BLOGCATEGORYFILE);	
	$current_category = to7bit($current_category, 'UTF-8');
	foreach($category_file->category as $category_item)	
	{		
		$category_item = to7bit($category_item, 'UTF-8');
		if($category_item == $current_category)
		{
			echo '<option value="'.$current_category.'" selected>'.$current_category.'</option>';	
		}
		else
		{
			echo '<option value="'.$category_item.'">'.$category_item.'</option>';	
		}	
	}	
	if($current_category == null)
	{
		echo '<option value="" selected></option>';	
	}
	else
	{
		echo '<option value=""></option>';	
	}
}

/** 
* Saves A Post
* 
* @return void success or error message
*/  
function savePost()
{
	$Blog = new Blog;
	$xmlNodes = $Blog->getXMLnodes(true);
	foreach($xmlNodes as $key => $value)
	{
		if(!isset($_POST["post-".$key]))
		{
			$post_value = '';
		}
		else
		{
			$post_value = $_POST["post-".$key];
		}
		$post_data[$key] = $post_value;
	}
	$savePost = $Blog->savePost($post_data);
	$generateRSS = $Blog->generateRSSFeed();
	if($savePost != false)
	{
		echo '<div class="updated">';
		i18n(BLOGFILE.'/POST_ADDED');
		echo '</div>';
	}
	else
	{
		echo '<div class="error">';
		i18n(BLOGFILE.'/POST_ERROR');
		echo '</div>';
	}
}

/** 
* Conditionals to display posts/search/archive/tags/category/importer on front end of website
* 
* @return void
*/  
function blog_display_posts() 
{
	GLOBAL $content;
	
	$Blog = new Blog;
	$slug = base64_encode(return_page_slug());
	$blog_slug = base64_encode($Blog->getSettingsData("blogurl"));
	if($slug == $blog_slug)
	{
		$content = '';
		ob_start();
		if($Blog->getSettingsData("displaycss") == 'Y')
		{
			echo "<style>\n";
			echo $Blog->getSettingsData("csscode");
			echo "\n</style>";
		}
		if(isset($_GET['post']))
		{
			$post_file = BLOGPOSTSFOLDER.$_GET['post'].'.xml';
			show_blog_post($post_file);
		}
		elseif (isset($_POST['search_blog'])) 
		{
			search_posts($_POST['keyphrase']);
		} 
		elseif (isset($_GET['archive'])) 
		{
			$archive = $_GET['archive'];
			show_blog_archive($archive);
		} 
		elseif(isset($_GET['tag'])) 
		{
			$tag = $_GET['tag'];
			show_blog_tag($tag);
		} 
		elseif (isset($_GET['category'])) 
		{      
			$category = $_GET['category'];      
			show_blog_category($category);	 
		}    
		elseif(isset($_GET['import']))
		{
			auto_import();
		}
		else 
		{
			show_all_blog_posts();
		}
		
		$content = ob_get_contents();
    ob_end_clean();		
	}
		return $content; // legacy support for non filter hook calls to this function
}

/** 
* show individual blog post
* 
* @param $slug slug of post to display
* @param $excerpt bool Whether an excerpt should be displayed. It would be false or null if a user was on the blog details page rather then a results or list all page
* @return void
*/  
function show_blog_post($slug, $excerpt=false)
{
	$Blog = new Blog;
	global $SITEURL;
	$post = getXML($slug);
	$url = $Blog->get_blog_url('post').$post->slug;
	$date = $Blog->get_locale_date(strtotime($post->date), '%b %e, %Y');
	if($Blog->getSettingsData("customfields") != 'Y')
	{
		if(isset($_GET['post']) && $Blog->getSettingsData("postadtop") == 'Y')
		{
			?>
			<div class="blog_all_posts_ad">
				<?php echo $Blog->getSettingsData("addata"); ?>
			</div>
			<?php
		}
		if(isset($_GET['post']) && $Blog->getSettingsData("disquscount") == 'Y') { 
		?>
			<a href="<?php echo $url; ?>/#disqus_thread" data-disqus-identifier="<?php echo $_GET['post']; ?>" style="float:right"></a>
		<?php } ?>
		<div class="blog_post_container">
			<h3 class="blog_post_title"><a href="<?php echo $url; ?>" class="blog_post_link"><?php echo $post->title; ?></a></h3>
			<?php if($Blog->getSettingsData("displaydate") == 'Y') {  ?>
				<p class="blog_post_date">
					<?php echo $date; ?>
				</p>
			<?php } ?>
			<p class="blog_post_content">
				<?php
				if(!isset($_GET['post']) && $Blog->getSettingsData("postthumbnail") == 'Y' && !empty($post->thumbnail)) 
				{ 
					echo '<img src="'.$SITEURL.'data/uploads/'.$post->thumbnail.'" style="" class="blog_post_thumbnail" />';
				}
				if($excerpt == false || $excerpt == true && $Blog->getSettingsData("postformat") == "Y")
				{
					echo html_entity_decode($post->content);
				}
				else
				{
					if($excerpt == true && $Blog->getSettingsData("postformat") == "N")
					{
						if($Blog->getSettingsData("excerptlength") == '')
						{
							$excerpt_length = 250;
						}
						else
						{
							$excerpt_length = $Blog->getSettingsData("excerptlength");
						}
						echo $Blog->create_excerpt(html_entity_decode($post->content), 0, $excerpt_length);
					}
				}
				if(isset($_GET['post']))
				{
					echo '<p class="blog_go_back"><a href="javascript:history.back()">&lt;&lt; '.i18n_r(BLOGFILE.'/GO_BACK').'</a></p>';
				}
				?>
			</p>
		</div>
		<?php
		if(!empty($post->tags) && $Blog->getSettingsData("displaytags") != 'N')
		{
			$tag_url = $Blog->get_blog_url('tag');
			$tags = explode(",", $post->tags);
			?>
			<p class="blog_tags"><b><?php i18n(BLOGFILE.'/TAGS'); ?> :</b> 
			<?php
			foreach($tags as $tag)
			{
				echo '<a href="'.$tag_url.$tag.'">'.$tag.'</a> ';
			}
			echo  '</p>';
		}
		if(isset($_GET['post']) && $Blog->getSettingsData("postadbottom") == 'Y')
		{
			?>
			<div class="blog_all_posts_ad">
				<?php echo $Blog->getSettingsData("addata"); ?>
			</div>
			<?php
		}
		if(isset($_GET['post']) && $Blog->getSettingsData("addthis") == 'Y')
		{
			addThisTool();
		}
		if(isset($_GET['post']) && $Blog->getSettingsData("sharethis") == 'Y')
		{
			shareThisTool();
		}
		if(isset($_GET['post']) && $Blog->getSettingsData("comments") == 'Y' && isset($_GET['post']))
		{
			disqusTool();
		}
	}
	else
	{	
		$blog_code = (string) $Blog->getSettingsData("blogpage");
		eval(' ?>'.$blog_code.'<?php ');
	}
}

/** 
* Shows blog categories list
* 
* @return void
*/  
function show_blog_categories()
{
	$Blog = new Blog;
	$categories = getXML(BLOGCATEGORYFILE);
	$url = $Blog->get_blog_url('category');
	foreach($categories as $category)
	{
		echo '<li><a href="'.$url.$category.'">'.$category.'</a></li>';
	}
	echo '<li><a href="'.$url.'">';
	i18n(BLOGFILE.'/ALL_CATEOGIRES');
	echo '</a></li>';
}

/** 
* Shows posts from a requested category
* 
* @param $category the category to show posts from
* @return void
*/  
function show_blog_category($category)
{
	$Blog = new Blog;
	$all_posts = $Blog->listPosts(true, true);
	$count = 0;
	foreach($all_posts as $file)
	{
		$data = getXML($file['filename']);
		if($data->category == $category)
		{
			$count++;
			show_blog_post($file['filename'], true);
		}
	}
	if($count < 1)
	{
		echo '<p class="blog_category_noposts">'.i18n_r(BLOGFILE.'/NO_POSTS').'</p>';
	}
}

/** 
* Show blog search bar
* 
* @return void
*/  
function show_blog_search()
{
	$Blog = new Blog;
	$url = $Blog->get_blog_url();
	?>
	<form id="blog_search" action="<?php echo $url; ?>" method="post">
		<input type="text" class="blog_search_input" name="keyphrase" />
		<input type="submit" class="blog_search_button" name="search_blog" value="<?php i18n(BLOGFILE.'/SEARCH'); ?>" />
	</form>
	<?php
}

/** 
* Show Blog archives list
* 
* @return void
*/  
function show_blog_archives()
{
	$Blog = new Blog;
	$archives = $Blog->get_blog_archives();
	if (!empty($archives)) 
	{
		echo '<ul>';
		foreach ($archives as $archive=>$title) 
		{
			$url = $Blog->get_blog_url('archive') . $archive;
			echo "<li><a href=\"$url\">$title</a></li>";
		}
		echo '</ul>';
	}
}

/** 
* Show Posts from requested archive
* 
* @return void
*/  
function show_blog_archive($archive)
{
	$Blog = new Blog;
	$posts = $Blog->listPosts(true, true);
	foreach ($posts as $file) 
	{
		$data = getXML($file['filename']);
		$date = strtotime($data->date);
		if (date('Ym', $date) == $archive)
		{
			show_blog_post($file['filename'], true);
		}
	}
}

/** 
* Show recent posts list
*
* @param $excerpt bool Choose true to display excerpts of post below post title. Defaults to false (no excerpt)
* @param $excerpt_length int Choose length of excerpt. If no value is provided, it will default to the length defined on the blog settings page
* @param $thumbnail int If true a thumbnail will be displayed for each post
* @param $read_more string if not null, a "Read More" link will be placed at the end of the excerpt. Pass the text you would like to be displayed inside the link
* @return string or void
*/
function show_blog_recent_posts($excerpt=false, $excerpt_length=null, $thumbnail=null, $read_more=null)
{
	$Blog = new Blog;
	$posts = $Blog->listPosts(true, true);
	global $SITEURL;
	if (!empty($posts)) 
	{
		echo '<ul>';
		$posts = array_slice($posts, 0, $Blog->getSettingsData("recentposts"), TRUE);
		foreach ($posts as $file) 
		{
			$data = getXML($file['filename']);
			$url = $Blog->get_blog_url('post') . $data->slug;
			$title = strip_tags(strip_decode($data->title));

			if($excerpt != false)
			{
				if($excerpt_length == null)
				{
					$excerpt_length = $Blog->getSettingsData("excerptlength");
				}
				$excerpt = $Blog->create_excerpt(html_entity_decode($data->content), 0, $excerpt_length);
				if($thumbnail != null)
				{
					if(!empty($data->thumbnail))
					{
						$excerpt = '<img src="'.$SITEURL.'data/uploads/'.$data->thumbnail.'" class="blog_recent_posts_thumbnail" />'.$excerpt;
					}
				}
				if($read_more != null)
				{
					$excerpt = $excerpt.'<br/><a href="'.$url.'" class="recent_posts_read_more">'.$read_more.'</a>';
				}
				echo '<li><a href="'.$url.'">'.$title.'</a><p class="blog_recent_posts_excerpt">'.$excerpt.'</p></li>';
			}
			else
			{
				echo "<li><a href=\"$url\">$title</a></li>";
			}
		}
		echo '</ul>';
	}
}

/** 
* Show posts for requested tag
* 
* @return void
*/  
function show_blog_tag($tag)
{
	$Blog = new Blog;
	$all_posts = $Blog->listPosts(true, true);
	foreach ($all_posts as $file) 
	{
		$data = getXML($file['filename']);
		$tags = explode(',', $data->tags);
		if (in_array($tag, $tags))
		{
			show_blog_post($file['filename'], true);	
		}
	}
}

/** 
* Show all postts
* 
* @return void
*/  
function show_all_blog_posts()
{
	$Blog = new Blog;
	if(isset($_GET['page']))
	{
		$page = $_GET['page'];
	}
	else
	{
		$page = 0;
	}
	show_posts_page($page);
}

/** 
* Display blog posts results from a search
* 
* @return void
*/  
function search_posts($keyphrase)
{
	$Blog = new Blog;
	$posts = $Blog->searchPosts($keyphrase);
	if (!empty($posts)) 
	{
		echo '<p class="blog_search_header">';
			i18n(BLOGFILE.'/FOUND');
		echo '</p>';
		foreach ($posts as $file)
		{
			show_blog_post($file, TRUE);
		}
	} 
	else 
	{
		echo '<p class="blog_search_header">';
			i18n(BLOGFILE.'/NOT_FOUND');
		echo '</p>';
	}
}

/** 
* RSS Feed Auto Importer
* Auto imports RSS feeds. Can be launched by a cron job 
* 
* @return void
*/  
function auto_import()
{
	$Blog = new Blog;
	if($_GET['import'] == urldecode($Blog->getSettingsData("autoimporterpass")) && $Blog->getSettingsData("autoimporter") =='Y')
	{
		ini_set("memory_limit","350M");

		require_once(BLOGPLUGINFOLDER.'magpierss/rss_fetch.inc');

		$rss_feed_file = getXML(BLOGRSSFILE);
		foreach($rss_feed_file->rssfeed as $the_fed)
		{
		    $rss_uri = $the_fed->feed;
		    $rss_category = $the_fed->category;
		        
		    $rss = fetch_rss($rss_uri);
		    $items = array_slice($rss->items, 0);
		    foreach ($items as $item )
		    {
		        $post_data['title']         = $item['title'];
		        $post_data['slug']          = '';
		        $post_data['date']          = $item['pubdate'];
		        $post_data['private']       = '';
		        $post_data['tags']          = '';
		        $post_data['category']      = $rss_category;
		        $post_data['content']       = $item['summary'].'<p class="blog_auto_import_readmore"><a href="'.$item['link'].'" target="_blank">'.i18n_r(BLOGFILE.'/READ_FULL_ARTICLE').'</a></p>';
		        $post_data['excerpt']       = '';
		        $post_data['thumbnail']     = '';
		        $post_data['current_slug']  = '';

		        $Blog->savePost($post_data);
		    }
		}
	}
}

/** 
* RSS Feed Auto Importer
* Auto imports RSS feeds. Can be launched by a cron job 
* 
* @return void
*/  
/*******************************************************
 * @function nm_show_page
 * param $index - page index (pagination)
 * @action show posts on news page
 */
function show_posts_page($index=0) 
{
	$Blog = new Blog;
	$posts = $Blog->listPosts(true, true);
	if($Blog->getSettingsData("allpostsadtop") == 'Y')
	{
		?>
		<div class="blog_all_posts_ad">
			<?php echo $Blog->getSettingsData("addata"); ?>
		</div>
		<?php
	}
	if(!empty($posts))
	{
		$pages = array_chunk($posts, intval($Blog->getSettingsData("postperpage")), TRUE);
		if (is_numeric($index) && $index >= 0 && $index < sizeof($pages))
		{
			$posts = $pages[$index];
		}
		else
		{
			$posts = array();	
		}
		$count = 0;
		$lastPostOfPage = false;
		foreach ($posts as $file)
		{
			$count++;
			show_blog_post($file['filename'], true);

			if($count == sizeof($posts) && sizeof($posts) > 0) 
			{
				$lastPostOfPage = true;	
			}

			if (sizeof($pages) > 1)
			{
				// We know here that we have more than one page.
				$maxPageIndex = sizeof($pages) - 1;
				show_blog_navigation($index, $maxPageIndex, $count, $lastPostOfPage);
				if($count == $Blog->getSettingsData("postsperpage"))
				{
					$count = 0;
				}
			}
		}
	} 
	else 
	{
		echo '<p>' . i18n(BLOGFILE.'/NO_POSTS') . '</p>';
	}
	if($Blog->getSettingsData("allpostsadbottom") == 'Y')
	{
		?>
		<div class="blog_all_posts_ad">
			<?php echo $Blog->getSettingsData("addata"); ?>
		</div>
		<?php
	}
}

/** 
* Blog posts navigation (pagination)
* 
* @param $index the current page index
* @param $total total number of pages
* @param $count current post
* @return void
*/  
function show_blog_navigation($index, $total, $count, $lastPostOfPage) 
{

	$Blog = new Blog;
	$url = $Blog->get_blog_url('page');

	if ($lastPostOfPage) 
	{
		echo '<div class="blog_page_navigation">';
	}
	
	if($index < $total && $lastPostOfPage)
	{
	?>
		<div class="left">
		<a href="<?php echo $url . ($index+1); ?>">
			&larr; <?php echo $Blog->getSettingsData("nextpage"); ?>
		</a>
		</div>
	<?php	
	}
	?>
		
	<?php
	if ($index > 0 && $lastPostOfPage)
	{
	?>
		<div class="right">
		<a href="<?php echo ($index > 1) ? $url . ($index-1) : substr($url, 0, -6); ?>">
			<?php echo $Blog->getSettingsData("previouspage"); ?> &rarr;
		</a>
		</div>
	<?php
	}
	?>
	
	<?php
	if ($lastPostOfPage) 
	{
		echo '<div id="clear"></div>';
		echo '</div>';
	}

}

function show_help_admin()
{
	global $SITEURL; 
	?>
	<h3>
		<?php i18n(BLOGFILE.'/PLUGIN_TITLE'); ?> <?php i18n(BLOGFILE.'/HELP'); ?>
	</h3>

	<h2 style="font-size:16px;"><?php i18n(BLOGFILE.'/FRONT_END_FUNCTIONS'); ?></h2>
	<p>
		<label><?php i18n(BLOGFILE.'/HELP_CATEGORIES'); ?><?php i18n(BLOGFILE.'/RSS_LOCATION'); ?>:</label>
		<?php highlight_string('<?php show_blog_categories(); ?>'); ?>
	</p>
	<p>
		<label><?php i18n(BLOGFILE.'/HELP_SEARCH'); ?>:</label>
		<?php highlight_string('<?php show_blog_search(); ?>'); ?>
	</p>
	<p>
		<label><?php i18n(BLOGFILE.'/HELP_ARCHIVES'); ?>:</label>
		<?php highlight_string('<?php show_blog_archives(); ?>'); ?>
	</p>
	<p>
		<label><?php i18n(BLOGFILE.'/HELP_RECENT'); ?>:</label>
		<?php highlight_string('<?php show_blog_recent_posts(); ?>'); ?>
	</p>
	<p>
		<label><?php i18n(BLOGFILE.'/RSS_LOCATION'); ?> :</label>
		<a href="<?php echo $SITEURL."rss.rss"; ?>" target="_blank"><?php echo $SITEURL."rss.rss"; ?></a>
	</p>
	<p>
		<label><?php i18n(BLOGFILE.'/DYNAMIC_RSS_LOCATION'); ?> :</label>
		<a href="<?php echo $SITEURL."plugins/blog/rss.php"; ?>" target="_blank"><?php echo $SITEURL."plugins/blog/rss.php"; ?></a>
	</p>
	<?php
	blog_page_help_html();
}

function addThisTool()
{
	$Blog = new Blog;
	?>
	<div class="addthis_toolbox addthis_default_style addthis_32x32_style">
	<a class="addthis_button_preferred_1"></a>
	<a class="addthis_button_preferred_2"></a>
	<a class="addthis_button_preferred_3"></a>
	<a class="addthis_button_preferred_4"></a>
	<a class="addthis_button_compact"></a>
	<a class="addthis_counter addthis_bubble_style"></a>
	</div>
	<script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
	<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=<?php echo $Blog->getSettingsData("addthisid"); ?>"></script>
	<!-- AddThis Button END -->
<?php
}

function shareThisTool()
{
	?>
	<span class='st_sharethis_large' displayText='ShareThis'></span>
	<span class='st_facebook_large' displayText='Facebook'></span>
	<span class='st_twitter_large' displayText='Tweet'></span>
	<span class='st_pinterest_large' displayText='Pinterest'></span>
	<span class='st_linkedin_large' displayText='LinkedIn'></span>
	<span class='st_googleplus_large' displayText='Google +'></span>
	<span class='st_delicious_large' displayText='Delicious'></span>
	<span class='st_digg_large' displayText='Digg'></span>
	<span class='st_email_large' displayText='Email'></span>
	<?php
}

function shareThisToolHeader()
{
	$Blog = new Blog;
	if($Blog->getSettingsData("sharethis") == 'Y') 
	{
		?>
		<script type="text/javascript">var switchTo5x=true;</script>
		<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
		<script type="text/javascript">stLight.options({publisher: "<?php echo $Blog->getSettingsData("sharethisid"); ?>"}); </script>
		<?php
	}

}

function feedBurnerTool()
{
	$Blog = new Blog;
	?>
		<a href="<?php echo $Blog->getSettingsData("feedburnerlink"); ?>" title="Subscribe to my feed" rel="alternate" type="application/rss+xml"><img src="http://www.feedburner.com/fb/images/pub/feed-icon32x32.png" alt="" style="border:0"/></a><a href="<?php echo $Blog->getSettingsData("feedburnerlink"); ?>" title="Subscribe to my feed" rel="alternate" type="application/rss+xml">Subscribe in a reader</a>
	<?php
}

function disqusTool()
{
	$Blog = new Blog;
	?>
	<div id="disqus_thread"></div>
	<script type="text/javascript">
	/* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
	    var disqus_shortname = '<?php echo $Blog->getSettingsData("disqusshortname"); ?>'; // required: replace example with your forum shortname
		var disqus_identifier = '<?php echo $_GET['post']; ?>';

	/* * * DON'T EDIT BELOW THIS LINE * * */
	(function() {
	    var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
	    dsq.src = 'http://' + disqus_shortname + '.disqus.com/embed.js';
	    (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
	})();
	</script>
	<?php if($Blog->getSettingsData("disquscount") == 'Y') { ?>
		<script type="text/javascript">
			/* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
		    var disqus_shortname = '<?php echo $Blog->getSettingsData("disqusshortname") ?>'; // required: replace example with your forum shortname
			var disqus_identifier = '<?php echo $_GET['post']; ?>';

			/* * * DON'T EDIT BELOW THIS LINE * * */
			(function () {
			var s = document.createElement('script'); s.async = true;
			s.type = 'text/javascript';
			s.src = 'http://' + disqus_shortname + '.disqus.com/count.js';
			(document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
			}());
		</script>
	<?php
	}
}

function blog_page_help_html()
{
	?>
	<h3><?php i18n(BLOGFILE.'/BLOG_PAGE_DESC_TITLE'); ?></h3>
	<p>
		<strong><?php i18n(BLOGFILE.'/BLOG_PAGE_DESC_LINE_1'); ?></strong> <br/>
		<?php i18n(BLOGFILE.'/BLOG_PAGE_DESC_LINE_2'); ?><br/>
		<?php i18n(BLOGFILE.'/BLOG_PAGE_DESC_LINE_3'); ?>: <br/>
		<?php highlight_string('<h1 class="title"><?php echo $post->title; ?></h1>'); ?><br/>
		<?php highlight_string('<p><img src="<?php echo $post->thumbnail; ?>" />'); ?><br/>
		<?php highlight_string('<?php echo $post->content; ?></p>'); ?><br/><br/>
	</p>

	<h3><?php i18n(BLOGFILE.'/BLOG_PAGE_AVAILABLE_FUNCTIONS'); ?></h3>
	<ul>
		<li>
			<strong><?php i18n(BLOGFILE.'/BLOG_PAGE_FORMAT_DATE_LABEL'); ?>: </strong><?php highlight_string('<?php echo formatPostDate($post->date); ?>'); ?><br/>
			<?php i18n(BLOGFILE.'/BLOG_PAGE_FORMAT_DATA_DESC'); ?><br/><br/>
		</li>
		<li>
			<strong><?php i18n(BLOGFILE.'/BLOG_PAGE_GET_URL_TO_AREAS'); ?>: <strong><?php highlight_string('<?php $Blog->get_blog_url(\'post\'); ?>'); ?><br/>
			<strong><?php i18n(BLOGFILE.'/BLOG_PAGE_URL_EX_LABEL'); ?>: </strong> <?php highlight_string('<?php echo $Blog->get_blog_url(\'post\').$post->slug; ?>'); ?><br/>
			<strong><?php i18n(BLOGFILE.'/BLOG_PAGE_AVAILABLE_AREAS'); ?></strong>
			<ul style="margin-left:20px;list-style:disc">
				<li><?php i18n(BLOGFILE.'/BLOG_PAGE_POST'); ?></li>
				<li><?php i18n(BLOGFILE.'/BLOG_PAGE_TAG'); ?></li>
				<li><?php i18n(BLOGFILE.'/BLOG_PAGE_PAGE'); ?></li>
				<li><?php i18n(BLOGFILE.'/BLOG_PAGE_ARCHIVE'); ?></li>
				<li><?php i18n(BLOGFILE.'/BLOG_PAGE_CATEGORY'); ?></li>
			</ul><br/>
		</li>
		<li>
			<strong><?php i18n(BLOGFILE.'/BLOG_PAGE_ADD_THIS'); ?><strong>
			<?php highlight_string('<?php addThisTool(); ?>'); ?>
			<br/><br/>
		</li>
		<li>
			<strong><?php i18n(BLOGFILE.'/BLOG_PAGE_SHARE_THIS'); ?>: <strong>
			<?php highlight_string('<?php shareThisTool(); ?>'); ?>
			<br/><br/>
		</li>
		<li>
			<strong><?php i18n(BLOGFILE.'/BLOG_PAGE_DISQUS_COMMENTS'); ?>: <strong>
			<?php highlight_string('<?php disqusTool(); ?>'); ?>
			<br/><br/>
		</li>
		<li>
			<strong><?php i18n(BLOGFILE.'/BLOG_PAGE_CREATE_EXCERPT'); ?>: <strong>
			<?php highlight_string('<?php echo $Blog->create_excerpt(html_entity_decode($post->content), 0, $excerpt_length); ?>'); ?><br/>
			<?php i18n(BLOGFILE.'/BLOG_PAGE_CREATE_EXCERPT_DESC'); ?>
			<br/><br/>
		</li>
		<li>
			<strong><?php i18n(BLOGFILE.'/BLOG_PAGE_DECODE_CONTENT'); ?>: <strong>
			<?php highlight_string('<?php echo html_entity_decode($post->content); ?>'); ?>
			<br/><br/>
		</li>
		<li>
			<strong><?php i18n(BLOGFILE.'/BLOG_PAGE_ADD_DATA_LABEL'); ?>: <strong>
			<?php highlight_string('<?php echo $Blog->getSettingsData("addata"); ?>'); ?>
			<br/><br/>
		</li>
	</ul>
	<?php
}

require_once("blog/inc/manage_custom_fields.php");