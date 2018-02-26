## Major Features of 3.4

_document in progress, each point will be annotated with specifics and details to be used as source for  actual documentation_

### Core Changes/Updates

Relative asseturl loading, allowing multiple host support, and https for assets
Relative Siteurl paths allowing not storing host info in content, etc.
Global Definitions for almost everything, including paths
Major Code rewrites
FileIO Functions
Customizable Error documents `404`, `403`, slugnames, and prefixes #416
New Users page
New Logs page
Localized date/time
Login log
Orphaned Page detection
Fancy Urls/custom URL, now requires prettyurl to be enabled in settings #1133
Experimental symblink support php env GSROOTPATH, GSADMIN
Improved pages change detection #983
Removed sitemap pinging entirely #939


### Plugins
Filters for all theme getters #831
Meta Filters #347
Page Load Hooks
page-edit-nav hooks for page actions #746
page-body hooks for after title content
Bunch of new misc hooks
Generic i18n words/terms for quick plugin i18n support without needing plugin langs
Auto sidebar form submit buttons #986
Auto sidebar dirty form indication #1061
All Editors modularized, available to plugins
Experimental ckeditor options, autosize/compact/inline editors
Better styleguide standards for plugins, inputs, buttons, labels etc.
Plugin action priorities
Experimental security filter support #1004
Image Manipulation library improvements
`GSEXECANON` allow callbacks to be closures #1186
Theme files filter #1205
Settings xml edit 
CSRF hook #954
Plugin custom plugin load order #1001
Plugin Hook priority order override #977
admin theme color classes #698
Get tags array #943

#### Hooks #719
Login hook #1235
resetpw hook #1233
Deletefile hooks #953
Backup/Restore hooks #919


### Debugging

New Debbugging Features #922
DebugLog now array friendly
FileIO logging `GSDEBUGFILEIO`
`GS_debug` can exist before common
Custom environment callouts #960
Debug Redirects `GSDEBUGREDIRECTS`


### Editing /  Backend

Codemirror in core, themed #546
Ckeditor updated #882
Ajax Saving for themes, pages, components, snippets #874 #739
`ctrl+s` Save shortcut on all forms (`ctrl+shift+s`) bypass
Page Drafts /  with Autosave #570
New theme editor #399
Page Manager Tree Folding, with saved state #794
Page Edit Meta Robots #596
Page Edit tabbed interface #502
Snippets html/wysiwyg  #507
Component and Snippet Active Toggling #487
Filebrowser uploading #992
Filebrowser light box 
Filebrowser custom thumbnail size 
Filebrowser thumbnail re-generation 992#issuecomment-67914509
Improved thumbnail cropping, manual coords, full page width cropper #417
Improved image preview
Page List label and filter toggles save last state via localstorage
Page List filter now works on tags "#tag" #988
Pages List and Files List item dates of today have class .datetoday and emphasis #989
ckeditor auto upload support 
Force image thumbnails in browser
Edit configuration files #856
Included sample image #1196
Auto dirty form handling #
Backend responsiveness improvements #934
Self closing notifications #936
Better backend theme support, css cleaned up, modernized #924

### Dependencies / Assets

All js assets updated
New HTML5/Ajax uploader (Dropzone) replaces uploadify, modular , available to plugins #587
FontAwesome icon library #590
Pages List tree folding, via custom tree grid library
new JS libraries/functions for notifications, spinners, table tree
Most static inline js removed from php
Load assets in footer
Asset system allows scripts to load multiple css/js files #850
Asset system allows styles to load multiple css files #850
jQuery Migrate plugin to back support old jQuery dependency plugins, w/update assistance for authors

### General Backend Customization

Auto loaded ckeditor `config.js`
Auto loaded ckeditor `contents.css`
Auto loaded ckeditor `styles.js`
Auto loaded custom `admin.css` #855
Backend custom page width
Backend custom `wide` pages width for editors etc.
Disable login #857
Disable resetpassword #846
Disable archive download
Disable uploading
Disable upload folder creation
Disable upload delete
Better page titles
Frontend templating system abstracted from index
Frontend is now loadable, allows loading entire GS core into your own custom landing/loader
Plugin hooks for style.php for advanced and real time styling/theming of backend
Themable favicon #949
Configurable Default Tabs #1005
Configurable index homepage slug #1002
Disable plugin api checks GSNOPLUGINCHECK
Show thumbnails always GSTHUMBSSHOW
User config chmodfile, chmoddir
Safemode #1008
Character Limits #575
Toggle upgrade login #1185
Customize backend landing page #923

#### Forthcoming
**THESE HAVE BEEN DEFERRED**
- [ ] multi level urls
- [ ] multi level menus
- [ ] filter and sorting libraries for developers
