<?php
/****************************************************
*
* @File: 		et_EE.php
* @Package:		GetSimple
* @Subject:		Estonian language file
* @Date:		01 Sept 2009
* @Revision:		01 Sept 2009
* @Version:		GetSimple 1.7
* @Status:		Final
* @Traductors:		Chris Cagle
* @Translator:		Rivo Zängov (www.eraser.ee)
*
*****************************************************/

$i18n = array(

/* 
 * For: install.php
*/
"PHPVER_ERROR"			=>	"<b>Jätkamine pole võimalik:</b> vajalik on PHP versioon 5.1.3 või uuem, sinu serveris on versioon",
"SIMPLEXML_ERROR"		=>	"<b>Jätkamine pole võimalik:</b> <em>SimpleXML</em> pole paigaldatud",
"CURL_WARNING"			=>	"<b>Hoiatus:</b> <em>cURL</em> pole paigaldatud",
"TZ_WARNING"				=>	"<b>Hoiatus:</b> <em>date_default_timezone_set</em> is missing",
"WEBSITENAME_ERROR"	=>	"<b>Viga:</b> Sinu saidi nimega on mingi probleem",
"WEBSITEURL_ERROR"	=>	"<b>Viga:</b> Sinu saidi aadressiga on mingi probleem",
"USERNAME_ERROR"		=>	"<b>Viga:</b> Kasutajanime ei sisestatud",
"EMAIL_ERROR"				=>	"<b>Viga:</b> Sinu e-posti aadress pole korrektne",
"CHMOD_ERROR"				=>	"<b>Jätkamine pole võimalik:</b> Seadetefaili kirjutamine pole võimalik. Pane kaustade /data/ ja /backups/ õigusteks <em>CHMOD 777</em> ning proovi siis uuesti",
"EMAIL_COMPLETE"		=>	"Paigaldamine on lõpetatud",
"EMAIL_USERNAME"		=>	"Sinu kasutajanimi on",
"EMAIL_PASSWORD"		=>	"Sinu uusparool on",
"EMAIL_LOGIN"				=>	"Logi siit sisse",
"EMAIL_THANKYOU"		=>	"Thank you for using",
"NOTE_REGISTRATION"	=>	"Sinu registreerumise info on saadetud aadressile",
"NOTE_REGERROR"			=>	"<b>Viga:</b> There was a problem sending out the registration information via email. Please make note of the password below",
"NOTE_USERNAME"			=>	"Sinu kasutajanimi on",
"NOTE_PASSWORD"			=>	"ja sinu parool on",
"INSTALLATION"			=>	"Paigaldamine",
"LABEL_WEBSITE"			=>	"Saidi nimi",
"LABEL_BASEURL"			=>	"Saidi aadres",
"LABEL_SUGGESTION"	=>	"Meie soovitus on",
"LABEL_USERNAME"		=>	"Kasutajanimi",
"LABEL_EMAIL"				=>	"E-posti aadress",
"LABEL_INSTALL"			=>	"Paiglda nüüd!",

/* 
 * For: pages.php
*/
"MENUITEM_SUBTITLE"	=>	"menüükirje",
"HOMEPAGE_SUBTITLE"	=>	"pealeht",
"PRIVATE_SUBTITLE"	=>	"privaatne",
"EDITPAGE_TITLE"		=>	"Muuda lehekülge",
"VIEWPAGE_TITLE"		=>	"Vaata lehekülge",
"DELETEPAGE_TITLE"	=>	"Kustuta lehekülg",
"PAGE_MANAGEMENT"		=>	"Lehekülgede haldamine",
"TOGGLE_STATUS"			=>	"Staatuse muutmine",
"TOTAL_PAGES"				=>	"lehekülge",
"ALL_PAGES"					=>	"Kõik leheküljed",

/* 
 * For: edit.php
*/
"PAGE_NOTEXIST"			=>	"Küsitud lehekülge pole olemas",
"BTN_SAVEPAGE"			=>	"Salvesta lehekülg",
"BTN_SAVEUPDATES"		=>	"Salvesta uuendused",
"DEFAULT_TEMPLATE"	=>	"Vaikimisi mall",
"NONE"							=>	"Pole",
"PAGE"							=>	"Lehekülg",
"NEW_PAGE"					=>	"Uus lehekülg",
"PAGE_EDIT_MODE"		=>	"Lehekülje muutmine",
"CREATE_NEW_PAGE"		=>	"Lisa uus lehekülg",
"VIEW"							=>	"<em>V</em>aata", // 'v' is the accesskey identifier
"PAGE_OPTIONS"			=>	"Lehekülje <em>v</em>alikud", // 'o' is the accesskey identifier
"TOGGLE_EDITOR"			=>	"<em>E</em>ditor sees/väljas", // 'g' is the accesskey identifier
"SLUG_URL"					=>	"Aadress",
"TAG_KEYWORDS"			=>	"Sildid &amp; Võtmesõnad",
"PARENT_PAGE"				=>	"Peamine leht",
"TEMPLATE"					=>	"Mall",
"KEEP_PRIVATE"			=>	"Privaatne",
"ADD_TO_MENU"				=>	"Lisa menüüsse",
"PRIORITY"					=>	"Järjekord",
"MENU_TEXT"					=>	"Menüü tekst",
"LABEL_PAGEBODY"		=>	"Lehekülje sisu",
"CANCEL"						=>	"Loobu",
"BACKUP_AVAILABLE"	=>	"Varukoopia on saadaval",
"MAX_FILE_SIZE"			=>	"Maks faili suurus",
"LAST_SAVED"				=>	"Viimati salvestatud",
"FILE_UPLOAD"				=>	"Faili üleslaadimine",
"OR"								=>	"või",

/* 
 * For: upload.php
*/
"ERROR_UPLOAD"			=>	"Faili üleslaadimisel tekkis viga",
"FILE_SUCCESS_MSG"	=>	"Korras! Faili asukoht",
"FILE_MANAGEMENT"		=>	"Failide haldamine",
"UPLOADED_FILES"		=>	"Üles laetud failid",
"SHOW_ALL"					=>	"Näita kõiki",
"VIEW_FILE"					=>	"Vaata faili",
"DELETE_FILE"				=>	"Kusuta fail",
"TOTAL_FILES"				=>	"faili",

/* 
 * For: logout.php
*/
"LOGGED_OUT"				=>	"Välju",
"MSG_LOGGEDOUT"			=>	"Sa oled nüüd välja loginud.",
"MSG_PLEASE"				=>	"Kui sa tahad oma kontole uuesti ligi saada, siis palun logi tagasi sisse", 

/* 
 * For: index.php
*/
"LOGIN"							=>	"Sisene",
"USERNAME"					=>	"Kasutajanimi",
"PASSWORD"					=>	"Parool",
"FORGOT_PWD"				=>	"Unustasid parooli?",
"CONTROL_PANEL"			=>	"Juhtpaneel",
"LOGIN_REQUIREMENT"	=>	"Nõuded sisse logimiseks",
"WARN_JS_COOKIES"		=>	"Korralikuks toimimiseks peavad veebilehitsejas olema sisse lülitatud küpsiste ja javaskripti kasutamine lubatud",
"WARN_IE6"					=>	"Internet Explorer 6 võib töötada, aga see pole ametlikult toetatud",

/* 
 * For: navigation.php
*/
"CURRENT_MENU" 			=> 	"Praegune menüü",
"NO_MENU_PAGES" 		=> 	"Pole ühtegi lehekülge, mida peals peamenüüs näitama",

/* 
 * For: theme-edit.php
*/
"TEMPLATE_FILE" 		=> 	"Malli faili <b>%s</b> on uuendatud!",
"THEME_MANAGEMENT" 	=> 	"Teemade haldamine",
"EDIT_THEME" 				=> 	"Muuda teemat",
"EDITING_FILE" 			=> 	"Faili muutmine",
"BTN_SAVECHANGES" 	=> 	"Salvesta muutused",
"EDIT" 							=> 	"Muuda",

/* 
 * For: support.php
*/
"SETTINGS_UPDATED"	=> 	"Sinu seaded on uuendatud",
"UNDO" 							=> 	"Tühista",
"SUPPORT" 					=> 	"Klienditugi",
"SETTINGS" 					=> 	"Seaded",
"ERROR" 						=> 	"Viga",
"BTN_SAVESETTINGS" 	=> 	"Salvesta seaded",
"EMAIL_ON_404" 			=> 	"Teavita administraatorit 404 veateadetest",
"VIEW_404" 					=> 	"Vaata 404 vealehti",
"VIEW_FAILED_LOGIN"	=> 	"Vaata ebaõnnestunud sisenemise katseid",
"VIEW_CONTACT_FORM"	=> 	"Vaata kontaktivormi sisestatud kirjeid",
"VIEW_TICKETS" 			=> 	"Vaata sinu poolt lisatud abi küsimisi",

/* 
 * For: log.php
*/
"MSG_HAS_BEEN_CLR" 	=> 	" has been cleared",
"LOGS" 							=> 	"Logi sissekanded",
"VIEWING" 					=> 	"Vaatamine",
"LOG_FILE" 					=> 	"Logifail",
"CLEAR_ALL_DATA" 		=> 	"Tühjendada kogu sisu failist",
"CLEAR_THIS_LOG" 		=> 	"<em>T</em>ühjenda see Logi", // 'c' is the accesskey identifier
"LOG_FILE_ENTRY" 		=> 	"LOGIFAILI SISSEKANNE",
"THIS_COMPUTER"			=>	"See arvuti",

/* 
 * For: backup-edit.php
*/
"BAK_MANAGEMENT"		=>	"Varukoopiate haldmine",
"ASK_CANCEL"				=>	"<em>L</em>oobu", // 'c' is the accesskey identifier
"ASK_RESTORE"				=>	"<em>T</em>aaasta", // 'r' is the accesskey identifier
"ASK_DELETE"				=>	"<em>K</em>ustuta", // 'd' is the accesskey identifier
"BACKUP_OF"					=>	"Varukoopia",
"PAGE_TITLE"				=>	"Lehekülje pealkiri",
"YES"								=>	"Jah",
"NO"								=>	"Ei",
"DATE"							=>	"Kuupäev",

/* 
 * For: components.php
*/
"COMPONENTS"				=>	"Komponendid",
"DELETE_COMPONENT"	=>	"Kusuta komponent",
"EDIT"							=>	"Muuda",
"ADD_COMPONENT"			=>	"Lis<em>a</em> komponent", // 'a' is the accesskey identifier
"SAVE_COMPONENTS"		=>	"Salvesta komponendid",

/* 
 * For: sitemap.php
*/
"SITEMAP_CREATED"		=>	"Sisukaart on loodud! Pingiti ka nelja otsingumootorit, et teavitada tehtud uuendustest",
"SITEMAP_ERRORPING"	=>	"Sisukaart on loodud, aga vähemalt ühe otsingumootori teavitamine tehtud muudatustest ebaõnnestus",
"SITEMAP_ERROR"			=>	"Saidi sisukaardi loomine ebaõnnestus",
"SITEMAP_WAIT"			=>	"<b>Palun oota:</b> Saidi sisukaardi loomine",

/* 
 * For: theme.php
*/
"THEME_CHANGED"			=>	"Teema on muudetud",
"CHOOSE_THEME"			=>	"Vali teema",
"ACTIVATE_THEME"		=>	"Aktiveeri teema",
"THEME_SCREENSHOT"	=>	"Teema pilt",
"THEME_PATH"				=>	"Praeguse teema asukoht",

/* 
 * For: resetpassword.php
*/
"RESET_PASSWORD"		=>	"Taaasta parool",
"YOUR_NEW"					=>	"Sinu uus",
"PASSWORD_IS"				=>	"parool on",
"ATTEMPT"						=>	"Proovi",
"MSG_PLEASE_EMAIL"	=>	"Palun sisesta e-posti aadress, millega sa oled siia saidile registreerunud ja sulle saadetakse uus parool",
"SEND_NEW_PWD"			=>	"Saada uus parool",

/* 
 * For: settings.php
*/
"GENERAL_SETTINGS"	=>	"Üldised seaded",
"WEBSITE_SETTINGS"	=>	"Saidi seaded",
"LOCAL_TIMEZONE"		=>	"Kohalik ajavöönd",
"LANGUAGE"					=>	"Keel",
"USE_FANCY_URLS"		=>	"<b>Ilusate aadresside (URL-ide) kasutamine</b> - veebiserveris peab olema moodul mod_rewrite sisse lülitatud",
"ENABLE_HTML_ED"		=>	"<b>HTML-redaktori kasutamine</b>",
"USER_SETTINGS"			=>	"Kasutaja sisselogimise seaded",
"WARN_EMAILINVALID"	=>	"Hoiatus: See e-posti aadress ei tundu olevat korrektne!",
"ONLY_NEW_PASSWORD"	=>	"Sisesta parool ainult siis, kui sa soovid seda muuta",
"NEW_PASSWORD"			=>	"Uus parool",
"CONFIRM_PASSWORD"	=>	"Korda parooli",
"PASSWORD_NO_MATCH"	=>	"Paroolid ei kattu",

/* 
 * For: contactform.php
*/
"MSG_CAPTCHA_FAILED"=>	"Kontrollkood oli vale, sinu puhul on arvatavasti tegu spämmirobotiga",
"CONTACT_FORM_SUB"	=>	"Kontaktivormilt",
"FROM"							=>	"saatja",
"MSG_CONTACTSUC"		=>	"Sinu kiri on saadetud",
"MSG_CONTACTERR"		=>	"Sinu kirja saatmisel tekkis viga",

/* 
 * For: 404-mailer.php
*/
"404_ENCOUNTERED"		=>	"Auto: Tekkis 404-viga",
"404_AUTO_MSG"			=>	"See on automaatne sõnum sinu veebisaidilt",
"PAGE_CANNOT_FOUND"	=>	"Tekkis 'lehte ei leitud' veateade ",
"DOMAIN"						=>	"domeen",
"DETAILS"						=>	"LISAINFO",
"WHEN"							=>	"Millal",
"WHO"								=>	"Kes",
"FAILED_PAGE"				=>	"Leht",
"REFERRER"					=>	"Viitaja",
"BROWSER"						=>	"Veebilehitseja",

/* 
 * For: health-check.php
*/
"WEB_HEALTH_CHECK"	=>	"Saidi tervise kontroll",
"VERSION"						=>	"Versioon",
"UPG_NEEDED"				=>	"Vajalik on uuendamine: ",
"CANNOT_CHECK"			=>	"Kontrollimine ebaõnnestus. Sinu versioon on",
"LATEST_VERSION"		=>	"Paigaldatud on viimane versioon",
"SERVER_SETUP"			=>	"Serveri seaded",
"OR_GREATER_REQ"		=>	"või uuem on vajalik",
"OK"								=>	"OK",
"INSTALLED"					=>	"Paigaldatud",
"NOT_INSTALLED"			=>	"Pole paigaldatud",
"WARNING"						=>	"Hoiatus",
"DATA_FILE_CHECK"		=>	"Andmefailide korrasoleku kontroll",
"DIR_PERMISSIONS"		=>	"Kaustade kirjutusõigused",
"EXISTANCE"					=>	"%s Existance",
"MISSING_FILE"			=>	"Puuduv fail",
"BAD_FILE"					=>	"Vigane fail",
"NO_FILE"						=>	"Faili pole",
"GOOD_D_FILE"				=>	"Korras 'Deny' fail",
"GOOD_A_FILE"				=>	"Korras 'Allow' fail",
"CANNOT_DEL_FILE"		=>	"Faili ei saa kustutada",
"DOWNLOAD"					=>	"Lae alla",
"WRITABLE"					=>	"Kirjutatav",
"NOT_WRITABLE"			=>	"Pole kirjutatav",

/* 
 * For: footer.php
*/
"POWERED_BY"				=>	"Kasutatud süsteem",
	"PRODUCTION"				=>	"Production",
"SUBMIT_TICKET"			=>	"Küsi abi",

/* 
 * For: backups.php
*/
"PAGE_BACKUPS"			=>	"Lehekülje varukoopiad",
"ASK_DELETE_ALL"		=>	"<em>K</em>ustuta kõik",
"DELETE_ALL_BAK"		=>	"Kusutada kõik varukoopiad?",
"TOTAL_BACKUPS"			=>	"varukoopiat",

/* 
 * For: archive.php
*/
"SUCC_WEB_ARCHIVE"	=>	"Edukat saidi arhiveerimine!",
"SUCC_WEB_ARC_DEL"	=>	"Saidi arhiiv on kustutatud",
"WEBSITE_ARCHIVES"	=>	"Saidi arhiivid",
"ARCHIVE_DELETED"		=>	"Arhiiv on kustutatud",
"CREATE_NEW_ARC"		=>	"Loo uus arhiiv",
"ASK_CREATE_ARC"		=>	"<em>L</em>oo uus arhiiv",
"CREATE_ARC_WAIT"		=>	"<b>Palun oota:</b> luuakse saidi arhiivi...",
"DOWNLOAD_ARCHIVES"	=>	"Lae arhiiv alla",
"DELETE_ARCHIVE"		=>	"Kusuta arhiiv",
"TOTAL_ARCHIVES"		=>	"arhiivi",

/* 
 * For: include-nav.php
*/
"WELCOME"						=>	"Tere,", // used as 'Welcome USERNAME!'
"TAB_PAGES"					=>	"<em>L</em>eheküljed",
"TAB_FILES"					=>	"<em>F</em>ailid",
"TAB_THEME"					=>	"<em>T</em>eema",
"TAB_BACKUPS"				=>	"<em>V</em>arukoopiad",
"TAB_SETTINGS"			=>	"<em>S</em>eaded",
"TAB_SUPPORT"				=>	"<em>K</em>lienditugi",
"TAB_LOGOUT"				=>	"Välj<em>u</em>",

/* 
 * For: sidebar-files.php
*/
"BROWSE_COMPUTER"		=>	"Vali oma arvutist",
"UPLOAD"						=>	"Lae üles",

/* 
 * For: sidebar-support.php
*/
"SIDE_SUPPORT_LOG"	=>	"Kliendit<em>o</em>e seaded &amp; Logid",
"SIDE_VIEW_LOG"			=>	"Vaata logifaili",
"SIDE_HEALTH_CHK"		=>	"Saidi <em>t</em>ervise kontroll",
"SIDE_SUBMIT_TICKET"=>	"<em>S</em>aada abipalve",
"SIDE_DOCUMENTATION"=>	"<em>D</em>okumentatsioon",

/* 
 * For: sidebar-theme.php
*/
"SIDE_VIEW_SITEMAP"	=>	"<em>V</em>aata sisukaarti",
"SIDE_GEN_SITEMAP"	=>	"<em>L</em>oo sisukaart",
"SIDE_COMPONENTS"		=>	"<em>M</em>uuda komponente",
"SIDE_EDIT_THEME"		=>	"Muuda <em>k</em>ujundust",
"SIDE_CHOOSE_THEME"	=>	"Vali <em>t</em>eema",

/* 
 * For: sidebar-pages.php
*/
"SIDE_CREATE_NEW"		=>	"<em>L</em>isa uus lehekülg",
"SIDE_VIEW_PAGES"		=>	"<em>V</em>aata kõiki lehekülgi",

/* 
 * For: sidebar-pages.php
*/
"SIDE_GEN_SETTINGS"	=>	"Üldised <em>s</em>eaded",
"SIDE_USER_PROFILE"	=>	"Kas<em>u</em>taja profiil",

/* 
 * For: sidebar-pages.php
*/
"SIDE_VIEW_BAK"			=>	"Vaata lehekülgede varukoopiat",
"SIDE_WEB_ARCHIVES"	=>	"<em>S</em>aidi arhiivid",
"SIDE_PAGE_BAK"			=>	"<em>V</em>arukoopiad lehekülgedest",

/* 
 * For: error_checking.php
*/
"ER_PWD_CHANGE"			=>	"Ära unusta to <a href=\"settings.php#profile\">oma parooli muuta</a>, et asendada see, mis sulle just automaatselt loodi...",
"ER_BAKUP_DELETED"	=>	"Varukoopia on kustutatud %s",
"ER_REQ_PROC_FAIL"	=>	"Tegevus ebaõnnestus",
"ER_YOUR_CHANGES"		=>	"Sinu muudatused lehel %s on salvestatud",
"ER_HASBEEN_REST"		=>	"%s on taastatud",
"ER_HASBEEN_DEL"		=>	"%s on kustutatud",
"ER_CANNOT_INDEX"		=>	"Sa ei saa muuta pealehe aadressi",
"ER_SETTINGS_UPD"		=>	"Sinu seadeid on uuendatud",
"ER_OLD_RESTORED"		=>	"Sinu vana parool on taastatud",
"ER_NEW_PWD_SENT"		=>	"Uus parool saadeti sinu poolt sisestatud e-posti aadressile",
"ER_SENDMAIL_ERR"		=>	"Kirja saatmisel tekkis viga. Palun proovi uuesti",
"ER_FILE_DEL_SUC"		=>	"Fail on kustutatud",
"ER_PROBLEM_DEL"		=>	"Faili kustutamisel tekkis viga",
"ER_COMPONENT_SAVE"	=>	"Sinu komponendid on salvestatud",
"ER_COMPONENT_REST"	=>	"Sinu komponendid on taastatatud",
"ER_CANCELLED_FAIL"	=>	"<b>Tühistatud:</b> Selle fail uuendamine on tühistatud",

/* 
 * For: changedata.php
*/
"CANNOT_SAVE_EMPTY"	=>	"Tühja lehekülge ei saa salvestada",

/* 
 * For: template_functions.php
*/
"FTYPE_COMPRESSED"	=>	"Kokku pakitud", //a file-type
"FTYPE_VECTOR"			=>	"Vektor", //a file-type
"FTYPE_FLASH"				=>	"Flash", //a file-type
"FTYPE_VIDEO"				=>	"Video", //a file-type
"FTYPE_AUDIO"				=>	"Heli", //a file-type
"FTYPE_WEB"					=>	"Veebifail", //a file-type
"FTYPE_DOCUMENTS"		=>	"Dokumendid", //a file-type
"FTYPE_SYSTEM"			=>	"Süsteem", //a file-type
"FTYPE_MISC"				=>	"Varia", //a file-type
"IMAGES"						=>	"Pildid",

/* 
 * For: login_functions.php
*/
"FILL_IN_REQ_FIELD"	=>	"Palun täida kõik kohustuslikud lahtrid",
"LOGIN_FAILED"			=>	"Sisenemine ebaõnnestus. Palun kontrolli uuesti oma kasutajanime ja parooli",

/* 
 * For: Date Format
*/
"DATE_FORMAT"				=>	"M j, Y" //please keep short


);

?>