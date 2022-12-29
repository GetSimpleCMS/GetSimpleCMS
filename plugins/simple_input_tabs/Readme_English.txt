***WELCOME TO SIMPLE INPUT TABS PLUGIN***
A plugin for Get Simple CMS. You can find up to date installation instructions at:
http://simpleinputtabs.internetimagery.com/index.html

---Install Instructions:

	Installing this plugin is really quite simple.

	Double Click "simple input tabs . zip".

	Locate your "Plugins" directory, within the folder you saved your GetSimple CMS to.

	Place the contents of the zip file inside the folder.

	Thats it! You're done! You didn't even have time to get a coffee!

---Template Setup:

	In the primary section of your template where you wish to add the main content, place an insert page content tag (replacing the get page content tag):

		<?php insert_page_content(); ?>

	For any extra sections on the same page, place another tag with the title of the section in quotes. e.g.

		<?php insert_page_content("sidebar"); ?>

	AND THAT'S IT!

---To use the template:

	This is equally simple. Navigate to your pages menu and create or edit a page as normal.
	Scroll down (if necessary) and below the edit box you will be presented with some new tabs.
	If you have one tab only, it's purely cosmetic. But if you have more than one you can click each to save and switch input fields.

	From there, simply put in your data as normal and watch it pop up in the correct place on the website. Simple.

---Advanced Usage:

---Additional functionality.

insert_page_content() actually accepts three arguments.
The Tab name, then a custom blurb and finally a toggle to turn off plugins input.
Placed in the brackets as such:

	<?php insert_page_content( TAB, BLURB, TOGGLE); ?>

The blurb is a custom message (written in quotes) that will be displayed as default every time the tab is opened with no content, and when clicking the magnifying glass.
The toggle will stop allowing other plugins to modify that tabs content.
It is on unless specifically turned off with false. If you want it on, then do nothing.
The arguments made must be in order. So if you need to access the second or third argument without placing anything in the earlier slots just put some empty quotes "".
Some example usages are below.

	<?php insert_page_content("logo","This space holds your logo, insert the image here.",false); ?>
	<?php insert_page_content("","Place your main content in here."); ?>
	<?php insert_page_content("","",false); ?>

---Return function:

While insert_page_content() prints out the content to the page, you can also return the content back into your templates code for further processing. If no content can be found, there is a problem finding the content, or the content is empty, the following function will instead return false. To return data use the code:

	<?php return_tab_content(); ?>

---Dummy functions:

There are a bunch of other tags made available to you purely for cosmetic reasons. I find them more concise and easier to remember. Just remember when distributing a template to include this plugin. Simply replace your current tags with the corresponding ones below:

	<?php insert_page_header(); ?>
	<?php insert_page_footer(); ?>
	<?php insert_page_navigation(); ?>

---Specific Tab requests:

Here's a nifty trick. If you want to get content from a DIFFERENT page you can! Use a Slug->Tab syntax within quotes for your Tab name. For example:

	<?php insert_page_content("gallery->sidebar"); ?>

If you omit the Tab part (eg: "gallery->") the plugin will attempt to find the main tab of that page.
If you leave the Slug name blank and just point to the tab (eg: "->sidebar") the plugin will look for the Tab on the current page.
This syntax also works when using return_tab_content()

---Extra Information.

The tab names are only allowed to contain characters, numbers, underscores, spaces and dashes. If you make a tab that includes something else, it will not show up for editing.

If you double up on Tab names you will get a warning, but will still be able to use them. Useful if you really need identical data on two sections of the web page.

Avoid using ||][|| in your website (should be easy to do... I hope...) as that barcode-looking character combination is reserved by this plugin.

This plugin is non destructive. If you choose to remove the plugin all your saved information in all tabs will remain compressed into the default single edit box with titles defining each tab.

If you venture into the plugins code (open with a text editor) you will see a few options at the top of the page. Here you can set some default configurations if you choose, before distribution.

This plugin comes with i18n language functionality out of the box with built in languages. If you would like to add your own language, then upload the additional included files and edit the language file present.


Enjoy!


--- Change Log:

Changes 1.1 - Made a small tweak to ensure it plays nice with other plugins.

Changes 1.2 - Made another small tweak to further improve compatibility. There is now an option within the code itself (easy to find) to toggle plugin api.

Changes 1.2b - Reverted the last change. It broke things! Damn... Need to figure out why.

Changes 1.3 - Figured it out. Should play nicely with most plugins. Using get page content is not advised, but if it happens to be on the page by mistake it'll print out the default information correctly (if it exists). Added a return functionality to be used for further processing. Changed the name to something more appropriate.

Changes 1.4 - Added a button under the tabs to preview the location of each tab. So that you can see where the content is going to end up on the page. Added some simple multilingual functionality. For now it is all in the one file, but if it becomes unruly then I'll dump it out into its separate file, as is convention. Added some conditions for when the helpful text is displayed below tabs. This is to help avoid confusion when only one tab is visible.

Changes 1.5 - Fixed a bug where tab names would show up in page descriptions, if the description was left blank. Improved i18n functionality. The plugin now accepts external language files, and if none exist it defaults to an internal language. Added "blurb" functionality: insert_page_contents() now accepts a second parameter that will display its content as the default text in a new tab and under the tab name in the 'tab locations' page.
IMPORTANT: Any pages made with 1.4 or earlier need to have some text removed. Find the page in data/pages/page.xml and remove the text "||]Main[||".

Changes 1.6 - The diplay tab location link, now provides links back to the relevant tabs for editing. The plugin now alters the content variable before the template is called, to be more compatible with other plugins. Removed auto completion of page description as it is no longer needed. Added a new functionality: compatibility mode. Will activate automatically in the background if it can see a viable plugin is installed.  You can force it into compatibility mode by adding a variable in the code, at the top of the page. Do this if Simple Input Tabs cannot find the plugin it needs to be compatible with, but you know you have it installed.
Added a compatibility mode for the plugin i18n version 3.0.5.

Changes 1.7 - Fixed a bug where compatibility mode would not come on if this plugin loaded before the pluging needing compatilibity. Added a quick and nasty way to hide tabs in the template. Prefix your tab name with a # and even though it will be hidden for having illegal characters, it will suppress the warning message. Went through and broke up the code a lot more. Added a duplicate display tab location button, as a tab above the edit box. Added a compatibility mode for the plugin "Small Plugin Toolbox". Specific tab requests (using "->") will now create a tab on the requested page if one does not already exist. Added a config option in the php file to choose placement of page tabs. Above or below the edit box. Added a config option in the php file to turn off warnings and 404 errors. Added the option of having config options placed in the external language files. Configurations in a language file will take priority over the built in ones. If you do not want config items in the language file, simply delete them.

Changes 1.8 - Changed the config options for tab locations. Instead of true/false, it is now a number system that can select one of four options. Added a new Tab layout using Javascript. This layout is special in that it does not need a page reload each time you change a tab. It also fully uses the admin CSS styles. Added a new filter that will put content into an 'extra' tab only. In a plugin make a filter with your desired tab name followed by dashes and tab content. ie: add_filter("mytab-tab-content","myfunction");

Changes 1.8e Various bugfixes. Changes 1.8f - Fixed a bug where firefox would not trigger a save warning when content changed.

Changes 1.9 Fixed a bug where changing tabs while in source mode would cause the same data to be saved in all tabs. Now you cannot change tabs while in source mode. :P
return_tab_data(); now also accepts non-specific tab requests (ie: without the ->) and removed the "tab problem" error. If return_tab_data() cannot find any data, it will now return false. Added a content lock that will prevent access to the content while a page title hasn't been entered or the template has changed prior to the page being saved. Tabs should no longer save the default content. Added a new config option to stop the display of default content on new pages. Added some new dynamic errors on the page edit screen. Using tab specific requests in insert_page_content (ie: with ->) will now accept plugins content filter. However insert_page_content now has a new true/false parameter to turn off the content filter per tab: insert_page_content($tab,$blurb,$plugins); Removed the 'active-tab' config option and replaced it with an "active-tab" class on active tabs to style in CSS. Use !important to ensure your style is displayed on tab. Fixed a bug where multiple insert_page_content calls within one set of php tags would not all work. Commented out insert_page_content calls will now be ignored. Added a new phrase to the language file = SOURCE_MODE.

Changes 1.9g Fixed a bug where magic_quotes would screw up json encodes. RAWR.

Changes 2.0 THE BIG 2! Broke the plugin up into two pieces. Simple_input_tabs and Small_plugin_toolkit. Now some features that were developed in this plugin can be accessed by other plugins, and so this can be extended.
Added a settings page to adjust the plugins settings outside the code.

Changes 2.0b Fixed an issue when calling tabs from outside the page.

Changes 2.1 Shuffled the plugins files around to make them more compatible with the EXTEND system. That also involved removing Small plugin toolkit from the distribution, though it still remains a requirement. Added a warning message when small plugin toolkit cannot be detected and provides a link to find it. Fixed a couple of spelling errors.

Changes 2.2 Made the warning message (if small plugin toolkit cannot be found) a lot less harsh. Ensured the plugin would be the first off the mark when accessing content via the index-pretemplate hook. Added new phrase to the language file.

Changes 2.3 Fixed a bug with I18n compatability, where using setlang?=xx wouldn't change the language.

Changes 2.3c Quick fix where the compatibility mode may turn on without the plugin present.

Changes 2.4 Fixed an issue where json_decode would output a string instead of an array, and SIT would attempt to read it as an array.

Changes 2.5 Fixed a bug within some Php versions where - will not be interpreted correctly in regular expressions.

Changes 2.6 Simplifed method of discovering tabs. Tabs can now be created from within content theoretically.