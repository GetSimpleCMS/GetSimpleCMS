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

exec_action('load-edit');

// Variable settings

// Get passed variables
$id    = isset($_GET['id'])    ? var_in( $_GET['id']    ): null;
$uri   = isset($_GET['uri'])   ? var_in( $_GET['uri']   ): null;
$ptype = isset($_GET['type'])  ? var_in( $_GET['type']  ): null;
$nonce = isset($_GET['nonce']) ? var_in( $_GET['nonce'] ): null;

$draft           = false; // init draft edit mode flag
$draftsActive    = getDef('GSUSEDRAFTS',true); // are drafts active
$pagestackactive = $draftsActive && getDef('GSUSEPAGESTACK',true) ;
$pagestackdraft  = $pagestackactive && getDef('GSDRAFTSTACKDEFAULT',true);

// if drafts are enabled
if($draftsActive){
    if(!isset($_GET['nodraft']) && $pagestackdraft){
        // default to edit draft if `nodraft` not set, or GSEDITDRAFTDEFAULT is true
        $draft = true;
    }
    else if(isset($_GET['draft']) && !$pagestackdraft){
        // allow `draft` to force draft mode if GSEDITDRAFTDEFAULT is not true
        $draft = true;
    }
}

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

$draftExists = false; // (bool) does a draft exist
$pageExists  = false; // (bool) does a page exist
$newdraft    = false; // (bool) new (unsaved) draft being edited
$pageClass   = "";    // (str) classes to add to maincontent

if ($id){
    // get saved page data

    $pageExists  = file_exists(GSDATAPAGESPATH . $id .'.xml');
    $draftExists = pageHasDraft($id);

    // fail if not using drafts and page does not exist
    // OR if neither page nor draft exists
    if ((!$draft && !$pageExists) || (!$draftExists && !$pageExists)){
        redirect('pages.php?error='.urlencode(i18n_r('PAGE_NOTEXIST')));
    }

    // if using drafts and no draft exists, load original
    if(!$draft || !$draftExists) $data_edit = getPageXML($id);
    else $data_edit = getDraftXML($id);

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
    $draft = false; // @todo this is to force no draft on new pages until we allow drafts
    // prefill fields if provided
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

$newdraft = $draft && !$draftExists; // (bool) is this a new never saved draft?
$path = find_url($url, $parent);

// make select box of available theme templates
if ($template == '') { $template = GSTEMPLATEFILE; }

$themes_path   = GSTHEMESPATH . $TEMPLATE;
$themes_handle = opendir($themes_path) or die("Unable to open ". GSTHEMESPATH);     
while ($getfile = readdir($themes_handle)) {       
    if( isFile($getfile, $themes_path, 'php') ) {
        // exclude functions.php, and include files .inc.php
        if ($getfile != 'functions.php' && substr(strtolower($getfile),-8) !='.inc.php' && substr($getfile,0,1)!=='.') {     
            $templates[] = $getfile;     
        }       
    }       
}       

sort($templates);

foreach ($templates as $file){
    $sel = $template == $file ? 'selected' : '';
    $templatename = $file == GSTEMPLATEFILE ?  i18n_r('DEFAULT_TEMPLATE') : $file;
    $theme_templates .= '<option '.$sel.' value="'.$file.'" >'.$templatename.'</option>';
}

// SETUP CHECKBOXES
$sel_m  = ($menuStatus != '') ?    'checked'  : '';
$sel_p  = ($private == 'Y') ?      'selected' : '';
$sel_ri = $metarNoIndex == '1' ?   'checked'  : '';
$sel_rf = $metarNoFollow == '1' ?  'checked'  : '';
$sel_ra = $metarNoArchive == '1' ? 'checked'  : '';

if ($menu == '') { $menu = $title; }

$pagetitle = empty($title) ? i18n_r('CREATE_NEW_PAGE') : i18n_r('EDIT').' &middot; '.$title;
get_template('header');

include('template/include-nav.php');


function getPublishedPageHead($editing = true, $path = ''){
    global $id,$draftExists,$pageExists;
    echo '<h3 class="floated">'. ($editing ? i18n_r('PAGE_EDIT_MODE') : i18n_r('CREATE_NEW_PAGE')).'</h3>';
    if(getDef('GSUSEDRAFTS',true) && $pageExists && getDef('GSSDRAFTSPUBLISHEDTAG',true)) echo '<div class="title label label-ok unselectable">'.i18n_r('LABEL_PUBLISHED').'</div>';
    echo '<!-- pill edit navigation -->',"\n",'<div class="edit-nav clearfix" >';
    if($editing) {
        echo '<a class="pageview" href="'. $path .'" target="_blank" accesskey="'. find_accesskey(i18n_r('VIEW')). '" >'. i18n_r('VIEW'). '</a>';
        if($path != '') {echo '<a class="pageclone" href="pages.php?id='. $id .'&amp;action=clone&amp;nonce='.get_nonce("clone","pages.php").'" >'.i18n_r('CLONE').'</a>'; }
    }
    exec_action(get_filename_id().'-edit-nav'); 
    echo "\n</div>";
}

function getDraftPageHead($editing = true, $path = ''){
    global $id,$draftExists,$pageExists,$PRETTYURLS;
    echo '<h3 class="floated">'. ($editing ? i18n_r('PAGE_EDIT_MODE') : i18n_r('CREATE_NEW_PAGE')) .'</h3>';
    echo '<div class="title label label-draft secondary-lightest-back unselectable">'.i18n_r('LABEL_DRAFT').'</div>';
    echo '<!-- pill edit navigation -->',"\n",'<div class="edit-nav clearfix" >';
    if($editing) {
        echo '<a class="draftview" href="'. $path . ($PRETTYURLS ? '?' : '&amp;') .'draft" target="_blank" accesskey="'. find_accesskey(i18n_r('VIEW')). '" >'. i18n_r('VIEW'). '</a>';
        echo '<a class="draftpublish" href="changedata.php?publish&id='.$id.'" accesskey="'. find_accesskey(i18n_r('PUBLISH')). '" >'. i18n_r('PUBLISH'). '</a>';
    }
    exec_action(get_filename_id().'-edit-nav'); 
    echo "\n</div>";
}

if($newdraft) $pageClass.=' newdraft';

?>

<div class="bodycontent clearfix">

    <div id="maincontent" class="<?php echo $pageClass; ?>">
        <div class="main">
        <div id="pagestack">
<?php
    exec_action('page-stack'); // experimental

    if(isset($id) && $pagestackactive) {
        /**
         * Editing draft page, published page exists
         */
        if($draft && $pageExists){
            $publishdata    = getPageXML($id,$nocdata = true);
            $publishAuthor  = (string)$publishdata->author;
            $publishPubdate = output_datetime($publishdata->pubDate);

            if(empty($publishAuthor)) $publishAuthor = i18n_r('UNKNOWN');
?>
        <!-- PUBLISHED pagestack -->
        <div class="pagestack existingpage shadow peek">
            <div style="float: left;">
                <i class="fa fa-clock-o">&nbsp;</i><?php echo sprintf(i18n_r('LAST_SAVED'),$publishAuthor)," ",$publishPubdate;?>&nbsp;
            </div>
            <div style="float:right">
                <a href="edit.php?id=<?php echo $id;?>&amp;nodraft" class="label label-ghost label-inline">
                    <i class="fa fa-pencil"></i>
                </a>
                <div class="label label-ok label-inline unselectable"><?php i18n('LABEL_PUBLISHED'); ?></div>
            </div>
            <div class="pagehead clear" >
            <?php
                getPublishedPageHead(isset($id),$path);
            ?>
            </div>
        </div>
<?php
        }
        /**
         * Editing published page, draft exists
         */
        if(!$draft && $draftExists){
            $draftdata    = getDraftXML($id,$nocdata = true);
            $draftAuthor  = (string)$draftdata->author;
            $draftPubdate = output_datetime($draftdata->pubDate);
            
            if(empty($draftAuthor)) $draftAuthor = i18n_r('UNKNOWN');
?>
        <!-- DRAFT page stack -->
        <div class="pagestack existingdraft shadow peek">
            <div style="float: left;">
                <i class="fa fa-clock-o">&nbsp;</i><?php echo sprintf(i18n_r('DRAFT_LAST_SAVED'),$draftAuthor)," ",$draftPubdate;?>&nbsp;
            </div>
            <div style="float:right">
                <a href="edit.php?id=<?php echo $id;?>&amp;draft" class="label label-ghost label-inline">
                    <i class="fa fa-pencil"></i>
                </a>
                <div class="label secondary-lightest-back label-inline unselectable"><?php i18n('LABEL_DRAFT'); ?></div>
            </div>
            <div class="pagehead clear" >
            <?php
                getDraftPageHead(isset($id),$path);
            ?>
            </div>
        </div>
<?php
        }
        else if(!$draft && !$draftExists){
        /**
         * Editing published page, draft does not exist
         */
?>
        <!-- NEWDRAFT page stack -->
        <div class="pagestack newdraft shadow nopeek">
            <div style="float: left;">
                <i class="fa fa-info-circle">&nbsp;</i><?php i18n('PAGE_NO_DRAFT'); ?>&nbsp;
            </div>
            <div style="float:right">
                <a href="edit.php?id=<?php echo $id;?>&amp;draft" class="label label-ghost label-inline">
                    <i class="fa fa-pencil"></i>
                </a>
                <div class="label label-ghost label-inline unselectable"><?php i18n('LABEL_DRAFT'); ?></div>
            </div>
        </div>
<?php
        }
    }
    echo '</div>';
    $draft ? getDraftPageHead(isset($id),$path) : getPublishedPageHead(isset($id),$path);
    exec_action(get_filename_id().'-body');
?>
        <form class="largeform" id="editform" action="changedata.php" method="post" accept-charset="utf-8" >
        <input id="nonce" name="nonce" type="hidden" value="<?php echo get_nonce("edit", "edit.php"); ?>" />
        <input id="author" name="post-author" type="hidden" value="<?php echo $USR; ?>" />
        <?php if($draftsActive && !$draft){ ?><input id="nodraft" name="post-nodraft" type="hidden" value="1" /><?php } ?>
 
        <!-- page title toggle screen -->
        <p id="edit_window">
            <label for="post-title" style="display:none;"><?php i18n('PAGE_TITLE'); ?></label>
            <input class="text title" id="post-title" name="post-title" type="text" maxlength="<?php echo GSTITLEMAX; ?>" value="<?php echo $title; ?>" placeholder="<?php i18n('PAGE_TITLE'); ?>" />
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
                            <input type="checkbox" id="post-menu-enable" name="post-menu-enable" <?php echo $sel_m; ?> />&nbsp;&nbsp;&nbsp;
                            <label for="post-menu-enable" ><?php i18n('ADD_TO_MENU'); ?></label>
                            <a href="navigation.php" class="viewlink" rel="facybox" alt="<?php echo strip_tags(i18n_r('VIEW')); ?>" >
                                <span class="fa fa-search icon-right" style="opacity:0.2"></span>
                            </a>
                        </p>
                        <div id="menu-items">
                            <img src="template/images/tick.png" id="tick" />
                            <div style="float:left;width:70%">
                                <span><label for="post-menu"><?php i18n('MENU_TEXT'); ?></label></span>
                                <input class="text" id="post-menu" name="post-menu" type="text" value="<?php echo $menu; ?>" />
                            </div>
                            <div style="float:right;width:20%">
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
                            <input class="text short" type="text" id="post-id" name="post-id" value="<?php echo $url; ?>" <?php echo (($url=='index' || $draft)?'readonly="readonly" ':''); ?>/>
                        </p>
                    </div>

                    <div class="clear"></div>
                    <?php exec_action('edit-extras'); //@hook edit-extras after page edit options html output ?>        
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
                <div class="codewrap"><textarea id="post-content" class="html_edit boxsizingBorder" name="post-content"><?php echo $content; ?></textarea></div>

            <?php exec_action('edit-content'); //@hook edit-content after page edit content html output ?> 

            <?php 
                if(isset($data_edit)) { 
                    echo '<input id="existing-url" type="hidden" name="existing-url" value="'. $url .'" />'; 
                }

            exec_action('html-editor-init'); //@hook html-edit-init LEGACY deprecated
            echo "<!-- html-editor-init -->";
            if (empty($HTMLEDITOR)) echo '</fieldset>';

        ?>
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
            <span class="editing"><?php echo sprintf($draft ? i18n_r('EDITING_DRAFT_TITLE') : i18n_r('EDITING_PAGE_TITLE'),'<b>'.$title.'</b>'); ?></span>
            <div id="submit_line" >
                <input type="hidden" name="redirectto" value="" />
                
                <span><input id="page_submit" class="submit" type="submit" name="submitted" value="<?php echo $buttonname; ?>" /></span>
                
                <div id="dropdown">
                    <h6 class="dropdownaction"><?php i18n('ADDITIONAL_ACTIONS'); ?></h6>
                    <ul class="dropdownmenu">
                        <li class="save-close" ><a href="javascript:void(0)" ><?php i18n('SAVE_AND_CLOSE'); ?></a></li>
                        <?php 
                            if($url != '' && !$draft) { ?>
                            <li><a href="pages.php?id=<?php echo $url; ?>&amp;action=clone&amp;nonce=<?php echo get_nonce("clone","pages.php"); ?>" ><?php i18n('CLONE'); ?></a></li>
                        <?php } ?>
                        <li id="cancel-updates" class="alertme"><a href="pages.php?cancel" ><?php i18n('CANCEL'); ?></a></li>
                        <?php if($draft && !$newdraft && $url != 'index' && $url != '') { ?>
                            <li class="alertme" ><a href="deletefile.php?draft=<?php echo $url; ?>&amp;nonce=<?php echo get_nonce("delete","deletefile.php"); ?>" ><?php echo strip_tags(i18n_r('ASK_DELETE')); ?></a></li>
                        <?php }
                            else if(!$draft && $url != 'index' && $url != '') { ?>
                            <li class="alertme" ><a href="deletefile.php?id=<?php echo $url; ?>&amp;nonce=<?php echo get_nonce("delete","deletefile.php"); ?>" ><?php echo strip_tags(i18n_r('ASK_DELETE')); ?></a></li>
                        <?php } ?>
                    </ul>
                </div>
                
            </div>
            
            <?php if($url != '') { ?>
                <p class="editfooter"><?php 
                    if (!$newdraft && isset($pubDate)) { 
                        echo '<span><i class="fa fa-clock-o"></i>';
                            echo sprintf(($draft ? i18n_r('DRAFT_LAST_SAVED') : i18n_r('LAST_SAVED')), '<em>'. (empty($author) ? i18n_r('UNKNOWN') : $author.'</em>')) .' ' . output_datetime($pubDate).'</span>';
                    }
                    if ( $draft && fileHasBackup(GSDATADRAFTSPATH.$url.'.xml') ) {
                        echo '<span>&bull;</span><a href="backup-edit.php?p=view&amp;draft&amp;id='.$url.'" target="_blank" ><i class="fa fa-file-archive-o"></i>'.i18n_r('BACKUP_AVAILABLE').'</a></span>';
                    }
                    else if( !$draft && fileHasBackup(GSDATAPAGESPATH.$url.'.xml') ) {
                        echo '<span>&bull;</span><span><a href="backup-edit.php?p=view&amp;id='.$url.'" target="_blank" ><i class="fa fa-file-archive-o"></i>'.i18n_r('BACKUP_AVAILABLE').'</a></span>';
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
