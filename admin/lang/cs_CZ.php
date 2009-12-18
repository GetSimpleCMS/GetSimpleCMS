<?php
/****************************************************
*
* @File: 				cs_CZ.php
* @Package:			GetSimple
* @Subject:			Czech language file
* @Date:				05 Sept 2009
* @Revision:		06 Sept 2009
* @Version:			GetSimple 1.7
* @Status:			Final
* @Traductors: 	Martin Jurica 	
*
*****************************************************/

$i18n = array(

/* 
 * For: install.php
*/
"PHPVER_ERROR"			=>	"<b>Nelze pokračovat:</b> PHP 5.1.3 nebo vyšší je vyžadováno. Na serveru je ",
"SIMPLEXML_ERROR"		=>	"<b>Nelze pokračovat:</b> <em>SimpleXML</em> není nainstalováno",
"CURL_WARNING"			=>	"<b>Upozornění:</b> <em>cURL</em> není nainstalováno",
"TZ_WARNING"				=>	"<b>Upozornění:</b> chybí <em>date_default_timezone_set</em>",
"WEBSITENAME_ERROR"	=>	"<b>Chyba:</b> Vyskytl se problém s názvem vašeho webu",
"WEBSITEURL_ERROR"	=>	"<b>Chyba:</b> Vyskytl se problém s URL vašeho webu",
"USERNAME_ERROR"		=>	"<b>Chyba:</b> Uživatelské jméno nenastaveno",
"EMAIL_ERROR"				=>	"<b>Chyba:</b> Vyskytl se problém s vaší mailovou adresou",
"CHMOD_ERROR"				=>	"<b>Nelze pokračovat:</b> Zápis do konfiguračního souboru není povolen. Nastavte prosím <em>CHMOD 777</em> pro složky /data/ a /backups/ a pak pokračujte",
"EMAIL_COMPLETE"		=>	"Setup dokončen",
"EMAIL_USERNAME"		=>	"Vaše uživatelské jméno je",
"EMAIL_PASSWORD"		=>	"Vaše heslo je",
"EMAIL_LOGIN"				=>	"Přihlaste se",
"EMAIL_THANKYOU"		=>	"Díky",
"NOTE_REGISTRATION"	=>	"Vaše registrační údaje byly odeslány na",
"NOTE_REGERROR"			=>	"<b>Chyba:</b> Vyskytl se problém při odesílání vašich registračních údajů na zadaný e-mail. Prosím poznamenejte si heslo",
"NOTE_USERNAME"			=>	"Vaše uživatelské jméno je",
"NOTE_PASSWORD"			=>	"a Vaše heslo je",
"INSTALLATION"			=>	"Instalace",
"LABEL_WEBSITE"			=>	"Název webu",
"LABEL_BASEURL"			=>	"Výchozí URL webu",
"LABEL_SUGGESTION"	=>	"Doporučujeme",
"LABEL_USERNAME"		=>	"Uživatelské jméno",
"LABEL_EMAIL"				=>	"Email",
"LABEL_INSTALL"			=>	"Instalovat!",

/* 
 * For: pages.php
*/
"MENUITEM_SUBTITLE"	=>	"položka menu",
"HOMEPAGE_SUBTITLE"	=>	"úvodní stránka",
"PRIVATE_SUBTITLE"	=>	"soukromé",
"EDITPAGE_TITLE"		=>	"Upravit Stránku",
"VIEWPAGE_TITLE"		=>	"Zobrazit Stránku",
"DELETEPAGE_TITLE"	=>	"Odstranit Stránku",
"PAGE_MANAGEMENT"		=>	"Správa Stránky",
"TOGGLE_STATUS"			=>	"Zobrazit podrobnosti",
"TOTAL_PAGES"				=>	"stránek celkem",
"ALL_PAGES"					=>	"Všechny Stránky",

/* 
 * For: edit.php
*/
"PAGE_NOTEXIST"			=>	"Požadovaná stránka neexistuje",
"BTN_SAVEPAGE"			=>	"Uložit stránku",
"BTN_SAVEUPDATES"		=>	"Uložit aktualizace",
"DEFAULT_TEMPLATE"	=>	"Výchozí šablona",
"NONE"							=>	"Nezvoleno",
"PAGE"							=>	"Stránka",
"NEW_PAGE"					=>	"Nová stránka",
"PAGE_EDIT_MODE"		=>	"Režim úprav",
"CREATE_NEW_PAGE"		=>	"Vytvořit novou stránku",
"VIEW"							=>	"Zobrazit", // 'v' is the accesskey identifier
"PAGE_OPTIONS"			=>	"Vlastnosti stránky", // 'o' is the accesskey identifier
"TOGGLE_EDITOR"			=>	"Přepnout editor", // 'g' is the accesskey identifier
"SLUG_URL"					=>	"Slug/URL",
"TAG_KEYWORDS"			=>	"Tagy &amp; Klíčová slova",
"PARENT_PAGE"				=>	"Nadřazená stránka",
"TEMPLATE"					=>	"Šablona",
"KEEP_PRIVATE"			=>	"Ponechat soukromé?",
"ADD_TO_MENU"				=>	"Přidat do menu",
"PRIORITY"					=>	"Priorita",
"MENU_TEXT"					=>	"Text v menu",
"LABEL_PAGEBODY"		=>	"Tělo stránky",
"CANCEL"						=>	"Zrušit",
"BACKUP_AVAILABLE"	=>	"Záloha je k dispozici",
"MAX_FILE_SIZE"			=>	"Maximální velikost souboru",
"LAST_SAVED"				=>	"Naposledy uloženo",
"FILE_UPLOAD"				=>	"Upload souboru",
"OR"								=>	"nebo",

/* 
 * For: upload.php
*/
"ERROR_UPLOAD"			=>	"Během uploadu se vyskytl problém",
"FILE_SUCCESS_MSG"	=>	"Úspěch! Umístění souboru",
"FILE_MANAGEMENT"		=>	"Správa souborů",
"UPLOADED_FILES"		=>	"Nahrané soubory",
"SHOW_ALL"					=>	"Zobrazit vše",
"VIEW_FILE"					=>	"Zobrazit soubor",
"DELETE_FILE"				=>	"Odstranit soubor",
"TOTAL_FILES"				=>	"celkem souborů",

/* 
 * For: logout.php
*/
"LOGGED_OUT"				=>	"Odhlášení",
"MSG_LOGGEDOUT"			=>	"Odhlášení ze systému proběhlo úspěšně.",
"MSG_PLEASE"				=>	"Pro přístup k vašemu účtu se prosím opět přihlaste", 

/* 
 * For: index.php
*/
"LOGIN"							=>	"Přihlášení",
"USERNAME"					=>	"Uživatelské jméno",
"PASSWORD"					=>	"Heslo",
"FORGOT_PWD"				=>	"Zapomněli jste heslo?",
"CONTROL_PANEL"			=>	"Ovládací panel",
"LOGIN_REQUIREMENT"	=>	"Požadavky pro přihlášení",
"WARN_JS_COOKIES"		=>	"Váš prohlížeč musí mít povoleny cookies a javascript",
"WARN_IE6"					=>	"Internet Explorer 6 asi bude fungovat, ale není oficiálně podporován",

/* 
 * For: navigation.php
*/
"CURRENT_MENU" 			=> 	"Aktuální menu",
"NO_MENU_PAGES" 		=> 	"Pro zobrazení v hlavním menu nejsou nastaveny žádné stránky",

/* 
 * For: theme-edit.php
*/
"TEMPLATE_FILE" 		=> 	"Šablona <b>%s</b> úspěšně aktualizována!",
"THEME_MANAGEMENT" 	=> 	"Správa šablon",
"EDIT_THEME" 				=> 	"Upravit šablonu",
"EDITING_FILE" 			=> 	"Úprava souboru",
"BTN_SAVECHANGES" 	=> 	"Uložit změny",
"EDIT" 							=> 	"Upravit",

/* 
 * For: support.php
*/
"SETTINGS_UPDATED"	=> 	"Vaše nastavení byla aktualizována",
"UNDO" 							=> 	"Vzít zpět",
"SUPPORT" 					=> 	"Podpora",
"SETTINGS" 					=> 	"Nastavení",
"ERROR" 						=> 	"Chyba",
"BTN_SAVESETTINGS" 	=> 	"Uložit nastavení",
"EMAIL_ON_404" 			=> 	"Upozorňovat administrátora e-mailem na chyby 404",
"VIEW_404" 					=> 	"Zobrazit chyby 404",
"VIEW_FAILED_LOGIN"	=> 	"Zobrazit neúspěšné pokusy o přihlášení",
"VIEW_CONTACT_FORM"	=> 	"Zobrazit zprávy z kontaktního formuláře",
"VIEW_TICKETS" 			=> 	"Zobrazit odeslané tickety",

/* 
 * For: log.php
*/
"MSG_HAS_BEEN_CLR" 	=> 	" byl vyčištěn",
"LOGS" 							=> 	"Logy",
"VIEWING" 					=> 	"Prohlížení",
"LOG_FILE" 					=> 	"Logu",
"CLEAR_ALL_DATA" 		=> 	"Odstranit všechna data z",
"CLEAR_THIS_LOG" 		=> 	"Vyčistit tento log", // 'c' is the accesskey identifier
"LOG_FILE_ENTRY" 		=> 	"Log soubor",
"THIS_COMPUTER"			=>	"Tento počítač",

/* 
 * For: backup-edit.php
*/
"BAK_MANAGEMENT"		=>	"Správa záloh",
"ASK_CANCEL"				=>	"Zrušit", // 'c' is the accesskey identifier
"ASK_RESTORE"				=>	"Obnovit", // 'r' is the accesskey identifier
"ASK_DELETE"				=>	"Odstranit", // 'd' is the accesskey identifier
"BACKUP_OF"					=>	"Záloha",
"PAGE_TITLE"				=>	"Název stránky",
"YES"								=>	"Ano",
"NO"								=>	"Ne",
"DATE"							=>	"Datum",

/* 
 * For: components.php
*/
"COMPONENTS"				=>	"Komponenty",
"DELETE_COMPONENT"	=>	"Odstranit komponentu",
"EDIT"							=>	"Upravit",
"ADD_COMPONENT"			=>	"Přidat komponentu)", // 'a' is the accesskey identifier
"SAVE_COMPONENTS"		=>	"Uložit komponenty",

/* 
 * For: sitemap.php
*/
"SITEMAP_CREATED"		=>	"Mapa webu (sitemap) vytvořena! Současně byly upozorněny 4 vyhledávače na provedené změny",
"SITEMAP_ERRORPING"	=>	"Mapa webu (sitemap) vytvořena, nicméně se nepodařilo o změně informovat jeden nebo více vyhledávačů",
"SITEMAP_ERROR"			=>	"Mapa webu nemohla být vytvořena, něco je špatně...",
"SITEMAP_WAIT"			=>	"<b>Čekejte prosím:</b> Právě se vytváří mapa webu",

/* 
 * For: theme.php
*/
"THEME_CHANGED"			=>	"Šablona webu byla úspěšně změněna",
"CHOOSE_THEME"			=>	"Zvolte si šablonu",
"ACTIVATE_THEME"		=>	"Aktivovat šablonu",
"THEME_SCREENSHOT"	=>	"Náhled šablony",
"THEME_PATH"				=>	"Cesta k aktuální šabloně",

/* 
 * For: resetpassword.php
*/
"RESET_PASSWORD"		=>	"Reset hesla",
"YOUR_NEW"					=>	"Vaše nové",
"PASSWORD_IS"				=>	"heslo je",
"ATTEMPT"						=>	"Pokus",
"MSG_PLEASE_EMAIL"	=>	"Zadejte prosím e-mail, který jste použili při registraci. Na tento e-mail bude zasláno nové heslo",
"SEND_NEW_PWD"			=>	"Poslat nové heslo",

/* 
 * For: settings.php
*/
"GENERAL_SETTINGS"	=>	"Obecná nastavení",
"WEBSITE_SETTINGS"	=>	"Nastavení webu",
"LOCAL_TIMEZONE"		=>	"Místní časové pásmo",
"LANGUAGE"					=>	"Jazyk",
"USE_FANCY_URLS"		=>	"<b>Použít Fancy URLs</b> - Vyžaduje povolený mod_rewrite na vašem hostingu",
"ENABLE_HTML_ED"		=>	"<b>Povolit HTML editor</b>",
"USER_SETTINGS"			=>	"Nastavení přihlašovacích údajů",
"WARN_EMAILINVALID"	=>	"Upozornění: Zadaný e-mail není platný - formát neodpovídá!",
"ONLY_NEW_PASSWORD"	=>	"Stačí zadat pouze nové heslo, staré jím bude přepsáno",
"NEW_PASSWORD"			=>	"Nové heslo",
"CONFIRM_PASSWORD"	=>	"Potvrdit heslo",
"PASSWORD_NO_MATCH"	=>	"Hesla se neshodují",

/* 
 * For: contactform.php
*/
"MSG_CAPTCHA_FAILED"=>	"V opsání Captcha kódu jste udělali chybu, nebo jste spam bot?",
"CONTACT_FORM_SUB"	=>	"Kontaktní formulář",
"FROM"							=>	"od",
"MSG_CONTACTSUC"		=>	"Váš e-mail byl úspěšně odeslán",
"MSG_CONTACTERR"		=>	"Došlo k chybě při odeslání vašeho e-mailu",

/* 
 * For: 404-mailer.php
*/
"404_ENCOUNTERED"		=>	"Automatická zpráva: 404 Error se vyskytl na",
"404_AUTO_MSG"			=>	"Toto je automatická zpráva z vašich webových stránek",
"PAGE_CANNOT_FOUND"	=>	"Chyba 'Stránka nebyla nalezena' se objevila na",
"DOMAIN"						=>	"doména",
"DETAILS"						=>	"PODROBNOSTI",
"WHEN"							=>	"Kdy",
"WHO"								=>	"Kdo",
"FAILED_PAGE"				=>	"Chybná stránka",
"REFERRER"					=>	"Referrer",
"BROWSER"						=>	"Prohlížeč",

/* 
 * For: health-check.php
*/
"WEB_HEALTH_CHECK"	=>	"Kontrola stavu webu",
"VERSION"						=>	"Verze",
"UPG_NEEDED"				=>	"Je vyžadován update na",
"CANNOT_CHECK"			=>	"Nelze zkontrolovat. Vaše verze je",
"LATEST_VERSION"		=>	"Nainstalována nejnovější verze",
"SERVER_SETUP"			=>	"Nastavení serveru",
"OR_GREATER_REQ"		=>	"nebo vyšší je vyžadováno",
"OK"								=>	"OK",
"INSTALLED"					=>	"Instalováno",
"NOT_INSTALLED"			=>	"Nenainstalováno",
"WARNING"						=>	"Varování",
"DATA_FILE_CHECK"		=>	"Kontrola integrity datového souboru",
"DIR_PERMISSIONS"		=>	"Oprávnění složek",
"EXISTANCE"					=>	"%s existuje",
"MISSING_FILE"			=>	"Chybějící soubor",
"BAD_FILE"					=>	"Špatný soubor",
"NO_FILE"						=>	"Není soubor",
"GOOD_D_FILE"				=>	"Good 'Deny' file",
"GOOD_A_FILE"				=>	"Good 'Allow' file",
"CANNOT_DEL_FILE"		=>	"Soubor nelze odstranit",
"DOWNLOAD"					=>	"Download",
"WRITABLE"					=>	"Zapisovatelný",
"NOT_WRITABLE"			=>	"Nelze zapisovat",

/* 
 * For: footer.php
*/
"POWERED_BY"				=>	"Vytvořeno s",
"PRODUCTION"				=>	"Production",
"SUBMIT_TICKET"			=>	"Odeslat ticket",

/* 
 * For: backups.php
*/
"PAGE_BACKUPS"			=>	"Zálohy stránek",
"ASK_DELETE_ALL"		=>	"Odstranit vše",
"DELETE_ALL_BAK"		=>	"Odstranit všechny zálohy?",
"TOTAL_BACKUPS"			=>	"záloh celkem",

/* 
 * For: archive.php
*/
"SUCC_WEB_ARCHIVE"	=>	"Úspěšně archivováno!",
"SUCC_WEB_ARC_DEL"	=>	"Archiv webu úspěšně odstraněn",
"WEBSITE_ARCHIVES"	=>	"Archivy webu",
"ARCHIVE_DELETED"		=>	"Archiv úspěšně odstraněn",
"CREATE_NEW_ARC"		=>	"Vytvořit nový archiv",
"ASK_CREATE_ARC"		=>	"Vytvořit nový archiv",
"CREATE_ARC_WAIT"		=>	"<b>Čekejte prosím:</b> vytvářím archiv webu...",
"DOWNLOAD_ARCHIVES"	=>	"Stáhnout archiv",
"DELETE_ARCHIVE"		=>	"Smazat archiv",
"TOTAL_ARCHIVES"		=>	"archivů celkem",

/* 
 * For: include-nav.php
*/
"WELCOME"						=>	"Vítejte", // used as 'Welcome USERNAME!'
"TAB_PAGES"					=>	"Stránky",
"TAB_FILES"					=>	"Soubory",
"TAB_THEME"					=>	"Šablony",
"TAB_BACKUPS"				=>	"Zálohy",
"TAB_SETTINGS"			=>	"Nastavení",
"TAB_SUPPORT"				=>	"Podpora",
"TAB_LOGOUT"				=>	"Odhlášení",

/* 
 * For: sidebar-files.php
*/
"BROWSE_COMPUTER"		=>	"Procházet váš počítač",
"UPLOAD"						=>	"Nahrát",

/* 
 * For: sidebar-support.php
*/
"SIDE_SUPPORT_LOG"	=>	"Nastavení podpory a logů",
"SIDE_VIEW_LOG"			=>	"Zobrazit Log",
"SIDE_HEALTH_CHK"		=>	"Kontrola stavu webu",
"SIDE_SUBMIT_TICKET"=>	"Odeslat Ticket",
"SIDE_DOCUMENTATION"=>	"Dokumentace",

/* 
 * For: sidebar-theme.php
*/
"SIDE_VIEW_SITEMAP"	=>	"Zobrazit mapu webu",
"SIDE_GEN_SITEMAP"	=>	"Vytvořit mapu webu",
"SIDE_COMPONENTS"		=>	"Upravit komponenty",
"SIDE_EDIT_THEME"		=>	"Upravit šablonu",
"SIDE_CHOOSE_THEME"	=>	"Vybrat šablonu",

/* 
 * For: sidebar-pages.php
*/
"SIDE_CREATE_NEW"		=>	"Vytvořit novou stránku",
"SIDE_VIEW_PAGES"		=>	"Zobrazit všechny stránky",

/* 
 * For: sidebar-pages.php
*/
"SIDE_GEN_SETTINGS"	=>	"Obecná nastavení",
"SIDE_USER_PROFILE"	=>	"Uživatelský profil",

/* 
 * For: sidebar-pages.php
*/
"SIDE_VIEW_BAK"			=>	"Zobrazit zálohu stránky",
"SIDE_WEB_ARCHIVES"	=>	"Archiv webu",
"SIDE_PAGE_BAK"			=>	"Zálohy stránky",

/* 
 * For: error_checking.php
*/
"ER_PWD_CHANGE"			=>	"Nezapomeňte si <a href=\"settings.php\">změnit heslo</a>...",
"ER_BAKUP_DELETED"	=>	"Záloha %s byla odstraněna",
"ER_REQ_PROC_FAIL"	=>	"Požadovaný proces selhal",
"ER_YOUR_CHANGES"		=>	"Změny v %s byly uloženy",
"ER_HASBEEN_REST"		=>	"%s obnoveno",
"ER_HASBEEN_DEL"		=>	"%s odstraněno",
"ER_CANNOT_INDEX"		=>	"Nemůžete změnit URL úvodní stránky (indexu)",
"ER_SETTINGS_UPD"		=>	"Vaše nastavení byla aktualizována",
"ER_OLD_RESTORED"		=>	"Vaše původní nastavení byla obnovena",
"ER_NEW_PWD_SENT"		=>	"Nové heslo bylo zasláno na zadanou e-mailovou adresu",
"ER_SENDMAIL_ERR"		=>	"Došlo k problému při odesílání e-mailu. Zkuste to prosím znovu",
"ER_FILE_DEL_SUC"		=>	"Soubor úspěšně odstraněn",
"ER_PROBLEM_DEL"		=>	"Došlo k problému při odstraňování souboru",
"ER_COMPONENT_SAVE"	=>	"Vaše komponenty byly uloženy",
"ER_COMPONENT_REST"	=>	"Vaše komponenty byly obnoveny",
"ER_CANCELLED_FAIL"	=>	"<b>Zrušeno:</b> Aktualizace souboru byla zrušena",

/* 
 * For: changedata.php
*/
"CANNOT_SAVE_EMPTY"	=>	"Není možné ukládat prázdné stránky",

/* 
 * For: template_functions.php
*/
"FTYPE_COMPRESSED"	=>	"Archiv", //a file-type
"FTYPE_VECTOR"			=>	"Vektor", //a file-type
"FTYPE_FLASH"				=>	"Flash", //a file-type
"FTYPE_VIDEO"				=>	"Video", //a file-type
"FTYPE_AUDIO"				=>	"Audio", //a file-type
"FTYPE_WEB"					=>	"Web", //a file-type
"FTYPE_DOCUMENTS"		=>	"Dokumenty", //a file-type
"FTYPE_SYSTEM"			=>	"Systémové", //a file-type
"FTYPE_MISC"				=>	"Ostatní", //a file-type
"IMAGES"						=>	"Obrázky",

/* 
 * For: login_functions.php
*/
"FILL_IN_REQ_FIELD"	=>	"Prosím vyplňte všechna povinná pole",
"LOGIN_FAILED"			=>	"Přihlášení se nezdařilo. Prosím zkontrolujte své uživatelské jméno a heslo",

/* 
 * For: Date Format
*/
"DATE_FORMAT"				=>	"j. m. Y" //please keep short


);

?>