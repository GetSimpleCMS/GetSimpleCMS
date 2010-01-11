<?php 
/****************************************************
*
* @File: 		common.php
* @Package:	GetSimple
* @Action:	defined variables for every page. 
* !! Do not include any files or use relative paths in this page 	
*
*****************************************************/

global $SALT;
global $SITENAME;

// for Uploadify security
$SESSIONHASH = md5($SALT . $SITENAME);

