<?php
/**
 * Page Edit
 *
 * Edit or create new pages for the website.    
 *
 * @package GetSimple
 * @subpackage Page-Edit
 */

// Setup inclusions
$load['plugin'] = true;

// Include common.php
include('inc/common.php');
login_cookie_check();

// Variable settings

// Get passed variables
$id    = isset($_GET['id'])    ? var_in( $_GET['id']    ): null;
$uri   = isset($_GET['uri'])   ? var_in( $_GET['uri']   ): null;
$ptype = isset($_GET['type'])  ? var_in( $_GET['type']  ): null;
$nonce = isset($_GET['nonce']) ? var_in( $_GET['nonce'] ): null;
$path  = GSDATAPAGESPATH;
$bakpagespath = GSBACKUPSPATH .getRelPath(GSDATAPAGESPATH,GSDATAPATH); // backups/pages/                    

// Page variables reset
$theme_templates = ''; 
$parents_list    = ''; 
$keytags         = '';
$parent          = '';
$template        = '';
$menuStatus      = ''; 
$private         = ''; 
$menu            = ''; 
$content         = '';
$author          = '';
$title           = '';
$url             = '';
$metak           = '';
$metad           = '';

if ($id){
    // get saved page data
    $file = $id .'.xml';
    
    if (!file_exists($path . $file)){ 
        redirect('pages.php?error='.urlencode(i18n_r('PAGE_NOTEXIST')));
    }

    $data_edit  = getXML($path . $file);
    $title      = stripslashes($data_edit->title);
    $pubDate    = $data_edit->pubDate;
    $metak      = stripslashes($data_edit->meta);
    $metad      = stripslashes($data_edit->metad);
    $url        = $data_edit->url;
    $content    = stripslashes($data_edit->content);
    $template   = $data_edit->template;
    $parent     = $data_edit->parent;
    $author     = $data_edit->author;
    $menu       = stripslashes($data_edit->menu);
    $private    = $data_edit->private;
    $menuStatus = $data_edit->menuStatus;
    $menuOrder  = $data_edit->menuOrder;
    $buttonname = i18n_r('BTN_SAVEUPDATES');

    $titlelong  = stripslashes($data_edit->titlelong);
    $summary    = stripslashes($data_edit->summary);

    $metarNoIndex = $data_edit->metarNoIndex;
    $metarNoFollow = $data_edit->metarNoFollow;
    $metarNoArchive = $data_edit->metarNoArchive;
} else {
    // prefill fields is provided
    $title          =  isset( $_GET['title']      ) ? var_in( $_GET['title']      ) : '';
    $template       =  isset( $_GET['template']   ) ? var_in( $_GET['template']   ) : '';
    $parent         =  isset( $_GET['parent']     ) ? var_in( $_GET['parent']     ) : '';
    $menu           =  isset( $_GET['menu']       ) ? var_in( $_GET['menu']       ) : '';
    $private        =  isset( $_GET['private']    ) ? var_in( $_GET['private']    ) : '';
    $menuStatus     =  isset( $_GET['menuStatus'] ) ? var_in( $_GET['menuStatus'] ) : '';
    $menuOrder      =  isset( $_GET['menuOrder']  ) ? var_in( $_GET['menuOrder']  ) : '';
    
    $titlelong      =  isset( $_GET['titlelong']  ) ? var_in( $_GET['titlelong']  ) : '';
    $summary        =  isset( $_GET['summary']    ) ? var_in( $_GET['summary']    ) : '';
    
    $metarNoIndex   =  isset( $_GET['metarNoIndex'] )   ? var_in( $_GET['metarNoIndex'] ) : '';
    $metarNoFollow  =  isset( $_GET['metarNoFollow'] )  ? var_in( $_GET['metarNoFollow'] ) : '';
    $metarNoArchive =  isset( $_GET['metarNoArchive'] ) ? var_in( $_GET['metarNoArchive'] ) : '';

    $buttonname = i18n_r('BTN_SAVEPAGE');
}


// MAKE SELECT BOX OF AVAILABLE TEMPLATES
if ($template == '') { $template = 'template.php'; }

$themes_path   = GSTHEMESPATH . $TEMPLATE;
$themes_handle = opendir($themes_path) or die("Unable to open ". GSTHEMESPATH);     
while ($file = readdir($themes_handle)) {       
    if( isFile($file, $themes_path, 'php') ) {      
        if ($file != 'functions.php' && substr(strtolower($file),-8) !='.inc.php' && substr($file,0,1)!=='.') {     
      $templates[] = $file;     
    }       
    }       
}       

sort($templates);

foreach ($templates as $file){
    if ($template == $file) { 
        $sel="selected"; 
    } else{ 
        $sel=""; 
    }
    
    if ($file == 'template.php'){ 
        $templatename=i18n_r('DEFAULT_TEMPLATE'); 
    } else { 
        $templatename=$file;
    }
    
    $theme_templates .= '<option '.$sel.' value="'.$file.'" >'.$templatename.'</option>';
}

// SETUP CHECKBOXES
$sel_m  = ($menuStatus != '') ?    'checked'  : '';
$sel_p  = ($private == 'Y') ?      'selected' : '';
$sel_ri = $metarNoIndex == '1' ?   'checked'  : '';
$sel_rf = $metarNoFollow == '1' ?  'checked'  : '';
$sel_ra = $metarNoArchive == '1' ? 'checked'  : '';

if ($menu == '') { $menu = $title; } 

get_template('header', cl($SITENAME).' &raquo; '.i18n_r('EDIT').' '.$title); 

?>

<?php include('template/include-nav.php'); ?>

<div class="bodycontent clearfix">
    
    <div id="maincontent">
        <div class="main">
        
        <h3 class="floated"><?php if(isset($data_edit)) { i18n('PAGE_EDIT_MODE'); } else { i18n('CREATE_NEW_PAGE'); } ?></h3>   

        <!-- pill edit navigation -->
        <div class="edit-nav" >
            <?php 
            if(isset($id)) {
                echo '<a href="'. find_url($url, $parent) .'" target="_blank" accesskey="'. find_accesskey(i18n_r('VIEW')). '" >'. i18n_r('VIEW'). '</a>';
                if($url != '') {echo '<a href="pages.php?id='. $url .'&amp;action=clone&amp;nonce='.get_nonce("clone","pages.php").'" >'.i18n_r('CLONE').'</a>'; }
                echo '<span class="save-close"><a href="javascript:void(0)" >'.i18n_r('SAVE_AND_CLOSE').'</a></span>';
            } 
            ?>
            <!-- @todo: fix accesskey for options  -->
            <!-- <a href="javascript:void(0)" id="metadata_toggle" accesskey="<?php echo find_accesskey(i18n_r('PAGE_OPTIONS'));?>" ><?php i18n('PAGE_OPTIONS'); ?></a> -->
            <div class="clear" ></div>
        </div>  
            
        <form class="largeform" id="editform" action="changedata.php" method="post" accept-charset="utf-8" >
        <input id="nonce" name="nonce" type="hidden" value="<?php echo get_nonce("edit", "edit.php"); ?>" />            
        <input id="author" name="post-author" type="hidden" value="<?php echo $USR; ?>" />  

        <!-- page title toggle screen -->
        <p id="edit_window">
            <label for="post-title" style="display:none;"><?php i18n('PAGE_TITLE'); ?></label>
            <input class="text title" id="post-title" name="post-title" type="text" value="<?php echo $title; ?>" placeholder="<?php i18n('PAGE_TITLE'); ?>" />
        </p>

        <!-- TABS -->
        <div id="tabs">
                <ul class="tab-list">
                    <li><a href="#page_content"><span><?php i18n('CONTENT'); ?></span></a></li>
                    <li><a href="#page_options"><span><?php i18n('OPTIONS'); ?></span></a></li>
                    <li><a href="#page_meta"><span><?php i18n('META'); ?></span></a></li>
                </ul>


<!-- ------- PAGE OPTIONS --------------------------------------------------- -->
            <div id="page_options" class="tab">
                <fieldset>
                    <legend>Page Options</legend>
                    <div class="wideopt">
                        <p>
                            <label for="post-titlelong"><?php i18n('TITLELONG'); ?>:</label>
                            <input class="text short" id="post-titlelong" name="post-titlelong" type="text" value="<?php echo $titlelong; ?>" />
                        </p>
                        <p>
                            <label for="post-summary" class=""><?php i18n('SUMMARY'); ?>: <span class="countdownwrap"><strong class="countdown" ></strong> <?php i18n('REMAINING'); ?></span></label>
                            <textarea class="text short charlimit" data-maxLength='256' id="post-summary" name="post-summary" ><?php echo $summary; ?></textarea>
                        </p>                        
                    </div>
                    <div class="leftopt">
                        <p class="inline clearfix" id="post-private-wrap" >
                            <label for="post-private" ><?php i18n('KEEP_PRIVATE'); ?>: &nbsp; </label>
                            <select id="post-private" name="post-private" class="text autowidth" >
                                <option value="" ><?php i18n('NORMAL'); ?></option>
                                <option value="Y" <?php echo $sel_p; ?> ><?php echo ucwords(i18n_r('PRIVATE_SUBTITLE')); ?></option>
                            </select>
                        </p>
                        <p class="inline clearfix" >
                            <label for="post-parent"><?php i18n('PARENT_PAGE'); ?>:</label>
                            <select class="text autowidth" id="post-parent" name="post-parent"> 
                                <?php 
                                getPagesXmlValues();
                                $count = 0;
                                foreach ($pagesArray as $page) {
                                    if ($page['parent'] != '') { 
								$parentTitle = returnPageField($page['parent'], "title");
                                        $sort = $parentTitle .' '. $page['title'];
                                    } else {
                                        $sort = $page['title'];
                                    }
                                    $page = array_merge($page, array('sort' => $sort));
                                    $pagesArray_tmp[$count] = $page;
                                    $count++;
                                }
                                // $pagesArray = $pagesArray_tmp;
                                $pagesSorted = subval_sort($pagesArray_tmp,'sort');
                                $ret=get_pages_menu_dropdown('','',0);
                                $ret=str_replace('value="'.$id.'"', 'value="'.$id.'" disabled', $ret);
                                
                                // handle 'no parents' correctly
                                if ($parent == '') { 
                                    $none='selected';
                                    $noneText='< '.i18n_r('NO_PARENT').' >'; 
                                } else { 
                                    $none=null; 
                                    $noneText='< '.i18n_r('NO_PARENT').' >'; 
                                }
                                
                                // Create base option
                                echo '<option '.$none.' value="" >'.$noneText.'</option>';
                                echo $ret;
                                ?>
                            </select>
                        </p>            
                        <p class="inline clearfix" >
                            <label for="post-template"><?php i18n('TEMPLATE'); ?>:</label>
                            <select class="text autowidth" id="post-template" name="post-template" >
                                <?php echo $theme_templates; ?>
                            </select>
                        </p>
                        
                        <p class="inline post-menu clearfix">
                            <input type="checkbox" id="post-menu-enable" name="post-menu-enable" <?php echo $sel_m; ?> />&nbsp;&nbsp;&nbsp;<label for="post-menu-enable" ><?php i18n('ADD_TO_MENU'); ?></label><a href="navigation.php" class="viewlink" rel="facybox" ><img src="template/images/search.png" id="tick" alt="<?php echo strip_tags(i18n_r('VIEW')); ?>" /></a>
                        </p>
                        <div id="menu-items">
                            <img src="template/images/tick.png" id="tick" />
                            <div style="float:left;width:210px;">
                                <span><label for="post-menu"><?php i18n('MENU_TEXT'); ?></label></span>
                                <input class="text" id="post-menu" name="post-menu" type="text" value="<?php echo $menu; ?>" />
                            </div>
                            <div style="float:right;width:40px;">
                                <span><label for="post-menu-order"><?php i18n('PRIORITY'); ?></label></span>                                
                                <select class="text" id="post-menu-order" name="post-menu-order" >
                                <?php if(isset($menuOrder)) { 
                                    if($menuOrder == 0) {
                                        echo '<option value="" selected>-</option>'; 
                                    } else {
                                        echo '<option value="'.$menuOrder.'" selected>'.$menuOrder.'</option>'; 
                                    }
                                } ?>
                                    <option value="">-</option>
                                    <?php
                                    $i = 1;
                                    while ($i <= 30) { 
                                        echo '<option value="'.$i.'">'.$i.'</option>';
                                        $i++;
                                    }
                                    ?>
                                </select>
                            </div> 
                        </div>                
                    <div class="clear"></div>
                    </div>
                    <div class="rightopt">
                        <p>
                            <label for="post-id"><?php i18n('SLUG_URL'); ?>:</label>
                            <input class="text short" type="text" id="post-id" name="post-id" value="<?php echo $url; ?>" <?php echo ($url=='index'?'readonly="readonly" ':''); ?>/>
                        </p>
                    </div>

                    <div class="clear"></div>
                    <?php exec_action('edit-extras'); ?>        
                </fieldset>
            </div> 
            <!-- / END PAGE OPTIONS -->

<!-- ------- PAGE CONTENT --------------------------------------------------- -->            
            <div id="page_content" class="tab">
            <?php if (empty($HTMLEDITOR)) { ?>
            <fieldset>
            <legend>Page Content</legend>
            <?php } ?>

                <label for="post-content" style="display:none;"><?php i18n('LABEL_PAGEBODY'); ?></label>
                <div class="codewrap"><textarea id="post-content" class="boxsizingBorder" name="post-content"><?php echo $content; ?></textarea></div>
            
            <?php exec_action('edit-content'); ?> 
            
            <?php if(isset($data_edit)) { 
                echo '<input type="hidden" name="existing-url" value="'. $url .'" />'; 
            } 
            
        // HTMLEDITOR INIT
        if ($HTMLEDITOR != '') {       

			if(isset($EDTOOL)) $EDTOOL = returnJsArray($EDTOOL);
			if(isset($toolbar)) $toolbar = returnJsArray($toolbar); // handle plugins that corrupt this

			$toolbar = isset($EDTOOL) ? ",toolbar: ".trim($EDTOOL,",") : '';
			$options = isset($EDOPTIONS) ? ','.trim($EDOPTIONS,",") : '';

            // convert to js string if php array
            if(is_array($toolbar)){
                // add about button if debug
                if(isDebug()){
                    $toolbar[]=array('About');
                }
                $toolbar = json_encode($toolbar);                
            }

            if (file_exists(GSTHEMESPATH .$TEMPLATE."/editor.css")) { 
                $fullpath = suggest_site_path();
                $contentsCss = $fullpath.getRelPath(GSTHEMESPATH).$TEMPLATE.'/editor.css';
            }

		?>

        <script type="text/javascript">
            
            var editorCfg = {
                language                     : '<?php echo $EDLANG; ?>',
                <?php if(!empty($contentsCss)) echo "contentsCss                   : '$contentsCss',"; ?>
                height                       : '<?php echo $EDHEIGHT; ?>',
                baseHref                     : '<?php echo $SITEURL; ?>'
					<?php echo $toolbar; ?>
					<?php echo $options; ?>
			};
            
            var editor = CKEDITOR.replace( 'post-content',editorCfg);         

            // ctr+s save handler
            CKEDITOR.on('instanceReady', function (ev) {
                ev.editor.setKeystroke(CKEDITOR.CTRL + 83 /*S*/, 'customSave' );              
            });

            // custom save function
            editor.addCommand( 'customSave',{
                exec : function( editor ){
                    // Debugger.log('customsave');
                    dosave(); // gs global save function
                }
            });
            
            // on change listener for cke ( source mode not supported )
            editor.on( 'change', function() {
                // Debugger.log('cke change');
                $('#editform #post-content').trigger('change');                
            });
            
            // onchange listener for cke source mode
            editor.on( 'mode', function() {
                if ( this.mode == 'source' ) {
                    var editable = editor.editable();
                    editable.attachListener( editable, 'input', function() {
                        $('#editform #post-content').trigger('change');
                    } );
                }
            } );

        </script>
            
        <?php
            # CKEditor setup functions
            ckeditor_add_page_link();
            exec_action('html-editor-init'); 
        ?>
            
        <?php } ?>
        
        <script type="text/javascript">
            /* Warning for unsaved Data */
            var yourText    = null;
            var warnme      = false;
            var pageisdirty = false;
            
            $('#cancel-updates').hide();
    
            window.onbeforeunload = function () {
                if (warnme || pageisdirty == true) {
                    return "<?php i18n('UNSAVED_INFORMATION'); ?>";
                }
            }
            
            $('#editform').submit(function(){
                warnme = false;
                return checkTitle();
            });

            checkTitle = function(){
                if($.trim($("#post-title").val()).length == 0){
                    alert("<?php i18n('CANNOT_SAVE_EMPTY'); ?>");
                    return false;
                }                   
            }

            jQuery(document).ready(function() { 

            <?php if (getDef('GSAUTOSAVE',true) && (int)GSAUTOSAVE != 0) { /* IF AUTOSAVE IS TURNED ON via GSCONFIG.PHP */ ?>

                    $('#pagechangednotify').hide();
                    $('#autosavenotify').show();
                    $('#autosavenotify').html('Autosaving is <b>ON</b> (<?php echo (int)GSAUTOSAVE; ?> s)');                
                    
                    function autoSaveIntvl(){
                        // Debugger.log('autoSaveIntvl called, isdirty:' + pageisdirty);
                        if(pageisdirty == true){
                            autoSave();
                            pageisdirty = false;
                        }                       
                    }
                    
                    function autoSave() {
                        $('input[type=submit]').attr('disabled', 'disabled');

                        // we are using ajax, so ckeditor wont copy data to our textarea for us, so we do it manually
                        if(typeof(editor)!='undefined'){ $('#post-content').val(CKEDITOR.instances["post-content"].getData()); }
                        
                        var dataString = $("#editform").serialize();
                        
                        // not internalionalized or using GS date format!
                        var currentTime = new Date();
                        var hours       = currentTime.getHours();
                        var minutes     = currentTime.getMinutes();
                        if (minutes < 10){ minutes = "0" + minutes; }
                        if (hours > 11){ daypart = "PM"; } else {    daypart = "AM"; }
                        if (hours > 12){ hours-=12; }
                        
                        $.ajax({
                            type: "POST",
                            url: "changedata.php",
                            data: dataString+'&autosave=true&submitted=true',
                            success: function(msg) {
                                if (msg.toString()=='OK') {
                                    $('#autosavenotify').text("<?php i18n('AUTOSAVE_NOTIFY'); ?> "+ hours +":"+minutes+" "+daypart);
                                    $('#pagechangednotify').hide();
                                    $('#pagechangednotify').text('');                    
                                    $('input[type=submit]').attr('disabled', false);
                                    $('input[type=submit]').css('border-color','#ABABAB');
                                    warnme = false;
                                    $('#cancel-updates').hide();
                                }
                                else {
                                    pageisdirty = true;
                                    $('#autosavenotify').text("<?php i18n('AUTOSAVE_FAILED'); ?>");                
                                }
                            }
                        }); 
                    }
                    
                    // We register title and slug changes with change() which only fires when you lose focus to prevent midchange saves.
                    $('#post-title, #post-id').change(function () {
                        $('#editform #post-content').trigger('change');
                    });                   
                    
                    // We register all other form elements to detect changes of any type by using bind
                    $('#editform input,#editform textarea,#editform select').not('#post-title').not('#post-id').bind('change keypress paste textInput input',function(){
                        pageisdirty = true;
                        warnme      = true;
                        autoSaveInd();
                    });
                
                setInterval(autoSaveIntvl, <?php echo (int)GSAUTOSAVE*1000; ?>);

                <?php } else { /* AUTOSAVE IS NOT TURNED ON */ ?>
                    $('#editform').bind('change keypress paste focus textInput input',function(){                   
                        warnme      = true;
                        pageisdirty = false;
                        autoSaveInd();
                    });
                    <?php } ?>
                    
                    function autoSaveInd(){
                        $('#pagechangednotify').show();                
                        $('#pagechangednotify').text("<?php i18n('PAGE_UNSAVED')?>");  
                        $('input[type=submit]').css('border-color','#CC0000');              
                        $('#cancel-updates').show();                        
                    }
            });
        </script>
        <?php if (empty($HTMLEDITOR)) echo '</fieldset>'; ?>
        </div> 
        <!-- / END PAGE CONTENT -->

<!-- ------- PAGE META OPTIONS --------------------------------------------------- -->
        <div id="page_meta" class="tab">
            <fieldset>    
            <legend>Page Meta</legend>                
            <div class="leftopt">             
                <p class="inline clearfix">
                    <label for="post-metak"><?php i18n('TAG_KEYWORDS'); ?>:</label>
                    <input class="text short" id="post-metak" name="post-metak" type="text" value="<?php echo $metak; ?>" />
                </p>
                <p class="clearfix">
                    <label for="post-metad" class="clearfix"><?php i18n('META_DESC'); ?>: <span class="countdownwrap"><strong class="countdown" ></strong> <?php i18n('REMAINING'); ?></span></label>
                    <textarea class="text short charlimit" data-maxLength='155' id="post-metad" name="post-metad" ><?php echo $metad; ?></textarea>
                </p>
            </div>    
            <div class="rightopt">
                <p class="inline clearfix">
                <label>Robots:</label><br/>
                    <label class="checkbox" for="post-metar-noindex" >NOINDEX</label>
                    <input type="checkbox" id="post-metar-noindex" name="post-metar-noindex" <?php echo $sel_ri; ?> />
                    <br/>               
                    <label class="checkbox" for="post-metar-nofollow" >NOFOLLOW</label>
                    <input type="checkbox" id="post-metar-nofollow" name="post-metar-nofollow" <?php echo $sel_rf; ?> />
                    <br/>
                    <label class="checkbox" for="post-metar-noarchive" >NOARCHIVE</label>
                    <input type="checkbox" id="post-metar-noarchive" name="post-metar-noarchive" <?php echo $sel_ra; ?> />
                    <br/>
                </p>   
            </div>
            <div class="clear"></div>    
            </fieldset>            
        </div>
    </div> <!-- / END TABS -->
                <span class="editing"><?php echo i18n_r('EDITPAGE_TITLE') .': ' . $title; ?></span>
            <div id="submit_line" >
                <input type="hidden" name="redirectto" value="" />
                
                <span><input id="page_submit" class="submit" type="submit" name="submitted" value="<?php echo $buttonname; ?>" /></span>
                
                <div id="dropdown">
                    <h6 class="dropdownaction"><?php i18n('ADDITIONAL_ACTIONS'); ?></h6>
                    <ul class="dropdownmenu">
                        <li class="save-close" ><a href="javascript:void(0)" ><?php i18n('SAVE_AND_CLOSE'); ?></a></li>
                        <?php if($url != '') { ?>
                            <li><a href="pages.php?id=<?php echo $url; ?>&amp;action=clone&amp;nonce=<?php echo get_nonce("clone","pages.php"); ?>" ><?php i18n('CLONE'); ?></a></li>
                        <?php } ?>
                        <li id="cancel-updates" class="alertme"><a href="pages.php?cancel" ><?php i18n('CANCEL'); ?></a></li>
                        <?php if($url != 'index' && $url != '') { ?>
                            <li class="alertme" ><a href="deletefile.php?id=<?php echo $url; ?>&amp;nonce=<?php echo get_nonce("delete","deletefile.php"); ?>" ><?php echo strip_tags(i18n_r('ASK_DELETE')); ?></a></li>
                        <?php } ?>
                    </ul>
                </div>
                
            </div>
            
            <?php if($url != '') { ?>
                <p class="backuplink" ><?php 
                    if (isset($pubDate)) { 
                        echo sprintf(i18n_r('LAST_SAVED'), '<em>'.$author.'</em>').' '. lngDate($pubDate).'&nbsp;&nbsp; ';
                    }
                    if ( file_exists($bakpagespath.$url.'.bak.xml') ) {    
                        echo '&bull;&nbsp;&nbsp; <a href="backup-edit.php?p=view&amp;id='.$url.'" target="_blank" >'.i18n_r('BACKUP_AVAILABLE').'</a>';
                    } 
                ?></p>
            <?php } ?>
    </form>
    </div><!-- end main -->
    </div><!-- end maincontent -->
    
    <div id="sidebar" >
        <?php include('template/sidebar-pages.php'); ?> 
    </div>

</div> <!-- end bodycontent -->
<?php get_template('footer'); ?>
