<?php

/**
 * News Manager English language file by Rogier Koppejan
 */


$i18n = array(

# general
"PLUGIN_NAME"         =>  "News Manager",

# error messages
"ERROR_ENV"           =>  "There was an error accessing the data folders. <em>CHMOD 777</em> the folders /data, /backups and their sub-folders and retry.",
"ERROR_SAVE"          =>  "<b>Error:</b> Unable to save your changes. <em>CHMOD 777</em> the folders /data, /backups and their sub-folders and retry.",
"ERROR_DELETE"        =>  "<b>Error:</b> Unable to delete the post. <em>CHMOD 777</em> the folders /data, /backups and their sub-folders and retry.",
"ERROR_RESTORE"       =>  "<b>Error:</b> Unable to restore the post. <em>CHMOD 777</em> the folders /data, /backups and their sub-folders and retry.",

# success messages
"SUCCESS_SAVE"        =>  "Your changes have been saved.",
"SUCCESS_DELETE"      =>  "The post has been deleted.",
"SUCCESS_RESTORE"     =>  "The post has been restored.",

# other messages
"UPDATE_HTACCESS"     =>  "<b>Note:</b> You probably have to update your <a href=\"%s\">.htaccess</a> file!",

# admin button (top-right)
"NEWS_TAB"            =>  "News",
"SETTINGS"            =>  "Settings",
"NEW_POST"            =>  "Create New Post",

# admin panel
"POST_TITLE"          =>  "Post Title",
"DATE"                =>  "Date",
"EDIT_POST"           =>  "Edit Post",
"VIEW_POST"           =>  "View Post",
"DELETE_POST"         =>  "Delete Post",
"POSTS"               =>  "post(s)",

# edit settings
"NM_SETTINGS"         =>  "News Manager Settings",
"DOCUMENTATION"       =>  "For more information on these settings, visit the <a href=\"%s\" target=\"_blank\">documentation page</a>.",
"PAGE_URL"            =>  "Page to display posts",
"NO_PAGE_SELECTED"    =>  "No page selected",
"LANGUAGE"            =>  "Language used on News Page",
"SHOW_POSTS_AS"       =>  "Posts on News Page are shown as",
"FULL_TEXT"           =>  "Full Text",
"EXCERPT"             =>  "Excerpt",
"PRETTY_URLS"         =>  "Use Fancy URLs for posts, archives, etc.",
"PRETTY_URLS_NOTE"    =>  "If you have Fancy URLs enabled, you might have to update your .htaccess file after saving these settings.",
"EXCERPT_LENGTH"      =>  "Excerpt length (characters)",
"POSTS_PER_PAGE"      =>  "Number of posts on News Page",
"RECENT_POSTS"        =>  "Number of recent posts (in sidebar)",
"ENABLE_ARCHIVES"     =>  "Enable archives",
"BY_MONTH"            =>  "By month",
"BY_YEAR"             =>  "By year",
"READ_MORE_LINK"      =>  "Add \"read more\" link to excerpts",
"ALWAYS"              =>  "Always",
"NOT_SINGLE"          =>  "Yes, except in single post view",
"GO_BACK_LINK"        =>  "\"Go back\" link in single post view",
"TITLE_LINK"          =>  "Post Title links to Post",
"BROWSER_BACK"        =>  "Previously visited page",
"MAIN_NEWS_PAGE"      =>  "Main News Page",
"ENABLE_IMAGES"       =>  "Enable post images",
"IMAGE_LINKS"         =>  "Link images to posts",
"IMAGE_WIDTH"         =>  "Post image width (pixels)",
"IMAGE_HEIGHT"        =>  "Post image height (pixels)",
"FULL"                =>  "full",
"IMAGE_CROP"          =>  "Crop post images to fit width/height ratio",
"IMAGE_ALT"           =>  "Insert post title in post image <em>alt</em> attribute",
"CUSTOM_SETTINGS"     =>  "Custom settings",

# edit post
"POST_OPTIONS"        =>  "Post Options",
"POST_SLUG"           =>  "Slug/URL",
"POST_TAGS"           =>  "Tags (separate tags with commas)",
"POST_DATE"           =>  "Publish date (<i>yyyy-mm-dd</i>)",
"POST_TIME"           =>  "Publish time (<i>hh:mm</i>)",
"POST_PRIVATE"        =>  "Post is private",
"POST_IMAGE"          =>  "Image",
"LAST_SAVED"          =>  "Last Saved",

# validation
"FIELD_IS_REQUIRED"   => "This field is required",
"ENTER_VALID_DATE"    => "Please enter a valid date / Leave blank for current date",
"ENTER_VALID_TIME"    => "Please enter a valid time / Leave blank for current time",
"ENTER_VALUE_MIN"     => "Please enter a value greater than or equal to %d",

# date picker - first day of week (0=Sunday, 1=Monday, ... 6=Saturday)
"DAY_OF_WEEK_START"   => "0",

# htaccess
"HTACCESS_HELP"       =>  "To enable Fancy URLs for posts, archives, etc., replace the contents of your <code>.htaccess</code> file with the lines below.",
"GO_BACK_WHEN_DONE"   =>  "When you are done with this page, click the button below to go back to the main panel.",

# save/cancel/delete
"SAVE_SETTINGS"       =>  "Save Settings",
"SAVE_POST"           =>  "Save Post",
"FINISHED"            =>  "Finished",
"CANCEL"              =>  "Cancel",
"DELETE"              =>  "Delete",
"OR"                  =>  "or",

# front-end/site
"FOUND"               =>  "The following posts have been found:",
"NOT_FOUND"           =>  "Sorry, your search returned no hits.",
"NOT_EXIST"           =>  "The requested post does not exist.",
"NO_POSTS"            =>  "No posts have been found.",
"PUBLISHED"           =>  "Published on",
"TAGS"                =>  "Tags",
"OLDER_POSTS"         =>  "&larr; Older Posts",
"NEWER_POSTS"         =>  "Newer Posts &rarr;",
"SEARCH"              =>  "Search",
"GO_BACK"             =>  "&lt;&lt; Go back to the previous page",
"ELLIPSIS"            =>  " [...] ",
"READ_MORE"           =>  "Read more",
"AUTHOR"              =>  "Author:",
"PREV_TEXT"           =>  "&lt;",
"PREV_TITLE"          =>  "Previous page",
"NEXT_TEXT"           =>  "&gt;",
"NEXT_TITLE"          =>  "Next page",

# language localization
"LOCALE"              =>  "en_US.utf8,en.utf8,en_US.UTF-8,en.UTF-8,en_US,en",

# date settings - list of available parameters: http://php.net/strftime
"DATE_FORMAT"         =>  "%b %e, %Y",
"MONTHLY_FORMAT"      =>  "%B %Y",
"YEARLY_FORMAT"       =>  "%Y"

);
