## Major Features of 3.4

_document in progress, each point will be annotated with specifics and details to be used as source for  actual documentation_

### Core Changes/Updates

* Relative Siteurl, asseturl loading #899
* Global Definitions for almost everything, including paths and hardcoded strings #558 #454
* Major Code rewrites
* FileIO Functions #658
* Customizable Error documents `404`, `403`, slugnames, and prefixes #416
* New Users page #408
* New Logs page with login logging #800
* Localized date/time #85
* Global site lang #578
* Login Language #577
* Orphaned Page detection #352
* Fancy Urls/custom URL, now requires prettyurl to be enabled in settings #1133
* Experimental symblink support php env GSROOTPATH, GSADMIN #582
* Improved pages change detection #983
* Removed sitemap pinging entirely #939
* Plugin toggle adds unique nonces #872
* Page cache initializations changes #870
* Page cache getters/filters #785
* Locked down error_checking on public aith pages #840
* nonce_timeout in configuration.php #761
* mb_internal_encoding #737
* removed anonymous data plugin #701
* secured backend template files #690
* refactored plugins core #679
* Theme functions always available #636
* Removed backend assets from public facing auth pages #592
* Improved health check php extensions #613
* Refactor/design health check #477
* Removed robots.txt #542
* Global cache control #456

### Plugins

* Generic i18n words/terms for quick plugin i18n support without needing plugin langs #811
* Auto sidebar form submit buttons #986
* Auto sidebar dirty form indication #1061
* All editors modularized, available to plugins #885
* Experimental ckeditor options, autosize/compact/inline editors
* Better styleguide standards for plugins, inputs, buttons, labels etc.
* Experimental security filter support #1004
* allow callbacks to be closures `GSEXECANON` #1186
* Settings xml changes not lost #1153
* Plugin custom plugin load order #1001
* Plugin hook action priorities #977
* admin theme color classes #698
* Get tags array #943
* Added lazyload library #629
* index-pretemplate before functions #539

#### Hooks / Filters

* Lotstons #719
* Plugin deactivate hook #604
* CSRF hook #954
* Theme files filter #1205
* Page Load Hooks
* page-edit-nav hooks for page actions #746
* page-body hooks for after title content
* Login hook #1235
* resetpw hook #1233
* Deletefile hooks #953
* Backup/Restore hooks #919
* Filters for all theme getters #831
* Meta Filters #347

### Debugging

* New Debbugging Features #922
* DebugLog now array friendly
* FileIO logging `GSDEBUGFILEIO`
* `GS_debug` can exist before common
* Custom environment callouts #960
* Debug Redirects `GSDEBUGREDIRECTS`

### Editing /  Backend
* Refactor components, overhaul #624
* Codemirror in core, themed #546
* Ckeditor updated #882
* Ajax Saving for themes, pages, components, snippets #874 #739
* `ctrl+s` Save shortcut on all forms (`ctrl+shift+s`) bypass
* Page Drafts /  with Autosave #570
* New theme editor #399
* Page Manager Tree Folding, with saved state #794
* Page Edit Meta Robots #596
* Page Edit tabbed interface #502
* Snippets html/wysiwyg  #507
* Component and Snippet Active Toggling #487
* Filebrowser Improvements #992
	* Uploading, and autouploading #992
	* Filebrowser lightbox
	* Filebrowser custom thumbnail size 
	* Filebrowser thumbnail re-generation
* Improved image manipulation, thumbnail creation #417
* Improved image page, thumbnail cropping, manual coords, full page width cropper
* Improved image preview
* Page List label and filter toggles save last state via localstorage
* Page List filter now works on tags "#tag" #988
* Pages List and Files List item dates of today have class .datetoday and emphasis #989
* ckeditor auto upload support 
* Force image thumbnails in browser
* Edit configuration files #856
* Included sample image #1196
* Auto dirty form handling #1061
* Backend responsiveness improvements #934
* Self closing notifications #936
* Better backend theme support, css cleaned up, modernized #924
* Website notes #792
* Inline uploading #723

### Dependencies / Assets

* All js assets updated
* New HTML5/Ajax uploader (Dropzone) replaces uploadify, modular , available to plugins #587
* FontAwesome icon library #590
* Pages List tree folding, via custom tree grid library #794
* new JS libraries/functions for notifications, spinners, table tree
* ajax spinner #703
* Most static inline js removed from php
* Load assets in footer
* Asset system allows scripts to load multiple css/js files #850
* Asset system allows styles to load multiple css files #850
* jQuery Migrate plugin to back support old jQuery dependency plugins, w/update assistance for authors #833
* All asset loading consolidated #704

### General Backend Customization

* Custom override files #649
	* Auto loaded custom `admin.css` #855
* Auto load ckeditor custom assets #611
	* Auto loaded ckeditor `config.js`
	* Auto loaded ckeditor `contents.css`
	* Auto loaded ckeditor `styles.js`
* Backend custom page width
* Backend custom `wide` pages width for editors etc. #885
* Disable login #857
* Disable resetpassword #846
* Disable archive download #877
* Upload restrictions #992
	* Disable uploading
	* Disable upload folder creation
	* Disable upload delete
* Better page titles #871
* Frontend templating system abstracted from index
* Frontend is now loadable, allows loading entire GS core into your own custom landing/loader #849
* Plugin hooks for style.php for advanced and real time styling/theming of backend #859
* Themable favicon #949
* Configurable Default Tabs #1005
* Configurable index homepage slug #1002
* Disable plugin api checks `GSNOPLUGINCHECK`
* Show thumbnails always `GSTHUMBSSHOW`
* User config chmodfile, chmoddir #627
* Safemode #1008
* Character Limits #575
* Toggle upgrade login #1185
* Customize backend landing page #923
* Admin.css fallbacks #763
* Email template #343


#### Forthcoming
* **THESE HAVE BEEN DEFERRED**
	* multi level urls
	* multi level menus
	* filter and sorting libraries for developers
