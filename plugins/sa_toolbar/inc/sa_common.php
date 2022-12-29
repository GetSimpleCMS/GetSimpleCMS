<?php


# define('SA_COMMON_LOADED','1.0');

//GS FUNCTIONS
//

// COMPATABILITY
# Backwards Compatability for 3.0 Script Queing
function SA_tb_register_style($handle, $src, $ver){echo '<link rel="stylesheet" href="'.$src.'" type="text/css" charset="utf-8" />'."\n";}
function SA_tb_queue_style($name,$where){}
function SA_tb_register_script($handle, $src, $ver, $in_footer=FALSE){echo '<script type="text/javascript" src="'.$src.'"></script>'."\n";}
function SA_tb_queue_script($name,$where){}


function sa_tb_user_is_admin(){
  GLOBAL $USR;
    
  if (isset($USR) && $USR == get_cookie('GS_ADMIN_USERNAME')) {
    return true;
  }
}

function sa_tb_array_index($ary,$idx){ // handles all the isset error avoidance bullshit when checking an array for a key that might not exist
  if( isset($ary) and isset($idx) and isset($ary[$idx]) ) return $ary[$idx];
}

?>