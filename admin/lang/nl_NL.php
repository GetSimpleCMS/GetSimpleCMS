<?php
/****************************************************
*
* @File: 				nl_NL.php
* @Package:			GetSimple
* @Subject:			Nederlands taalbestand
* @Date:				25 Oct 2009
* @Revision:		25 Oct 2009
* @Version:			GetSimple 1.7
* @Status:			Alpha
* @Traductors: 	Martijn van der Ven (Zegnåt)
*
*****************************************************/

$i18n = array(

/* 
 * For: install.php
*/
"PHPVER_ERROR"			=>	"<b>Voortgang mislukt:</b> PHP 5.1.3 of hoger is benodigd, uw versie ",
"SIMPLEXML_ERROR"		=>	"<b>Voortgang mislukt:<</b> <em>SimpleXML</em> is niet geinstalleerd",
"CURL_WARNING"			=>	"<b>Waarschuwing:</b> <em>cURL</em> Niet geinstalleerd",
"TZ_WARNING"				=>	"<b>Waarschuwing:</b> <em>date_default_timezone_set</em> is niet gevonden",
"WEBSITENAME_ERROR"	=>	"<b>Fout:</b> Er is een probleem met de titel van de website",
"WEBSITEURL_ERROR"	=>	"<b>Fout:</b> Er is een probleem met de URL van de website",
"USERNAME_ERROR"		=>	"<b>Fout:</b> Gebruikersnaam is niet ingevuld",
"EMAIL_ERROR"				=>	"<b>Fout:</b> Er is een probleem met uw Email adres",
"CHMOD_ERROR"				=>	"<b>Voortgang mislukt:</b> Niet mogelijk om te schrijven in config file. <em>CHMOD 777</em> de folders /data/ en /backups/ en probeer opnieuw",
"EMAIL_COMPLETE"		=>	"Installatie geslaagd",
"EMAIL_USERNAME"		=>	"Uw gebruikersnaam is",
"EMAIL_PASSWORD"		=>	"Uw nieuwe wachtwoord is",
"EMAIL_LOGIN"				=>	"Hier inloggen",
"EMAIL_THANKYOU"		=>	"Bedankt voor het gebruiken van",
"NOTE_REGISTRATION"	=>	"Uw registratie informatie is verstuurd aan",
"NOTE_REGERROR"			=>	"<b>Fout:</b> Er is een probleem met het verzenden van de registratie informatie via email. Noteer het onderstaande wachtwoord",
"NOTE_USERNAME"			=>	"Uw gebruikersnaam is",
"NOTE_PASSWORD"			=>	"en uw wachtwoord is",
"INSTALLATION"			=>	"Installatie",
"LABEL_WEBSITE"			=>	"Website naam",
"LABEL_BASEURL"			=>	"Website basis URL",
"LABEL_SUGGESTION"	=>	"Uw suggestie is",
"LABEL_USERNAME"		=>	"Gebruikersnaam",
"LABEL_EMAIL"				=>	"Emailadres",
"LABEL_INSTALL"			=>	"Installeren!",

/* 
 * For: pages.php
*/
"MENUITEM_SUBTITLE"	=>	"menu item",
"HOMEPAGE_SUBTITLE"	=>	"homepage",
"PRIVATE_SUBTITLE"	=>	"privé",
"EDITPAGE_TITLE"		=>	"Pagina aanpassen",
"VIEWPAGE_TITLE"		=>	"Bekijk",
"DELETEPAGE_TITLE"	=>	"Verwijder",
"PAGE_MANAGEMENT"		=>	"Pagina beheer",
"TOGGLE_STATUS"			=>	"Status weergeven",
"TOTAL_PAGES"				=>	"pagina&rsquo;s in totaal.",
"ALL_PAGES"					=>	"Alle pagina&rsquo;s",

/* 
 * For: edit.php
*/
"PAGE_NOTEXIST"			=>	"De opgevraagde pagina bestaat niet",
"BTN_SAVEPAGE"			=>	"Bewaar pagina",
"BTN_SAVEUPDATES"		=>	"Bewaar aanpassingen",
"DEFAULT_TEMPLATE"	=>	"Standaard template",
"NONE"							=>	"Geen",
"PAGE"							=>	"pagina",
"NEW_PAGE"					=>	"Nieuwe pagina",
"PAGE_EDIT_MODE"		=>	"Pagina aanpassen",
"CREATE_NEW_PAGE"		=>	"Creëer nieuwe pagina",
"VIEW"							=>	"Pagina bekijken (<em>v</em>)", // 'v' is the accesskey identifier
"PAGE_OPTIONS"			=>	"Pagina <em>o</em>pties", // 'o' is the accesskey identifier
"TOGGLE_EDITOR"			=>	"HTML <em>g</em>ebruiken", // 'g' is the accesskey identifier
"SLUG_URL"					=>	"Slug/URL",
"TAG_KEYWORDS"			=>	"Tags &amp; keywords",
"PARENT_PAGE"				=>	"Bovenliggende pagina",
"TEMPLATE"					=>	"Template",
"KEEP_PRIVATE"			=>	"Pagina privé houden?",
"ADD_TO_MENU"				=>	"Aan het menu toevoegen",
"PRIORITY"					=>	"Prioriteit",
"MENU_TEXT"					=>	"Menu tekst",
"LABEL_PAGEBODY"		=>	"Pagina inhoud",
"CANCEL"						=>	"Annuleren",
"BACKUP_AVAILABLE"	=>	"Backup aanwezig",
"MAX_FILE_SIZE"			=>	"Max. bestandsgrootte",
"LAST_SAVED"				=>	"Laatst bewaard",
"FILE_UPLOAD"				=>	"Bestand uploaden",
"OR"								=>	"of",

/* 
 * For: upload.php
*/
"ERROR_UPLOAD"			=>	"Er is een probleem met het uploaden van uw bestand",
"FILE_SUCCESS_MSG"	=>	"Gelukt! Bestandslocatie",
"FILE_MANAGEMENT"		=>	"Bestanden beheer",
"UPLOADED_FILES"		=>	"Geüploade bestanden",
"SHOW_ALL"					=>	"Alles tonen",
"VIEW_FILE"					=>	"Bekijk",
"DELETE_FILE"				=>	"Verwijder",
"TOTAL_FILES"				=>	"bestanden in totaal.",

/* 
 * For: logout.php
*/
"LOGGED_OUT"				=>	"Uitgelogd",
"MSG_LOGGEDOUT"			=>	"U bent nu uitgelogd.",
"MSG_PLEASE"				=>	"Log opnieuw in om uw account te beheren", 

/* 
 * For: index.php
*/
"LOGIN"							=>	"Login",
"USERNAME"					=>	"Gebruikersnaam",
"PASSWORD"					=>	"Wachtwoord",
"FORGOT_PWD"				=>	"Wachtwoord vergeten?",
"CONTROL_PANEL"			=>	"Controle Paneel",
"LOGIN_REQUIREMENT"	=>	"Login benodigdheden",
"WARN_JS_COOKIES"		=>	"Cookies en javascript moeten aan staan om goed te functioneren",
"WARN_IE6"					=>	"Internet Explorer 6 werkt mogelijk wel, maar wordt niet ondersteund",

/* 
 * For: navigation.php
*/
"CURRENT_MENU" 			=> 	"Huidige menu",
"NO_MENU_PAGES" 		=> 	"Er zijn geen pagina&rsquo;s toegewezen aan het menu.",

/* 
 * For: theme-edit.php
*/
"TEMPLATE_FILE" 		=> 	"Template bestand <b>%en</b> zijn geupdate!",
"THEME_MANAGEMENT" 	=> 	"Thema beheer",
"EDIT_THEME" 				=> 	"Thema aanpassen",
"EDITING_FILE" 			=> 	"Aan te passen bestand",
"BTN_SAVECHANGES" 	=> 	"Bewaar aanpassingen",
"EDIT" 							=> 	"Aanpassen",

/* 
 * For: support.php
*/
"SETTINGS_UPDATED"	=> 	"Uw instellingen zijn gewijzigd",
"UNDO" 							=> 	"Wijziging ongedaan maken.",
"SUPPORT" 					=> 	"Ondersteuning",
"SETTINGS" 					=> 	"instellingen",
"ERROR" 						=> 	"Fout",
"BTN_SAVESETTINGS" 	=> 	"Instellingen bewaren",
"EMAIL_ON_404" 			=> 	"Email beheerder bij 404 fouten",
"VIEW_404" 					=> 	"Bekijk 404 fouten",
"VIEW_FAILED_LOGIN"	=> 	"Bekijk mislukte login pogingen",
"VIEW_CONTACT_FORM"	=> 	"Bekijk contact formulier inzendingen",
"VIEW_TICKETS" 			=> 	"Bekijk alle tickets",

/* 
 * For: log.php
*/
"MSG_HAS_BEEN_CLR" 	=> 	" is geleegd",
"LOGS" 							=> 	"Logs",
"VIEWING" 					=> 	"Geopend",
"LOG_FILE" 					=> 	"logbestand",
"CLEAR_ALL_DATA" 		=> 	"Leeg alle data van",
"CLEAR_THIS_LOG" 		=> 	"Leeg deze log (<em>C</em>)", // 'c' is the accesskey identifier
"LOG_FILE_ENTRY" 		=> 	"Vermelding in dit logbestand:",
"THIS_COMPUTER"			=>	"Deze Computer",

/* 
 * For: backup-edit.php
*/
"BAK_MANAGEMENT"		=>	"Backup beheer",
"ASK_CANCEL"				=>	"Annuleren (<em>C</em>)", // 'c' is the accesskey identifier
"ASK_RESTORE"				=>	"He<em>r</em>stellen", // 'r' is the accesskey identifier
"ASK_DELETE"				=>	"Verwij<em>d</em>eren", // 'd' is the accesskey identifier
"BACKUP_OF"					=>	"Backup van",
"PAGE_TITLE"				=>	"Pagina titel",
"YES"								=>	"Ja",
"NO"								=>	"Nee",
"DATE"							=>	"Datum",

/* 
 * For: components.php
*/
"COMPONENTS"				=>	"componenten",
"DELETE_COMPONENT"	=>	"Component verwijderen",
"EDIT"							=>	"Aanpassen van de thema",
"ADD_COMPONENT"			=>	"Een component toevoegen (<em>A</em>)", // 'a' is the accesskey identifier
"SAVE_COMPONENTS"		=>	"Bewaar componenten",

/* 
 * For: sitemap.php
*/
"SITEMAP_CREATED"		=>	"Sitemap gecreëerd! Tevens hebben we 4 zoekmachines gepinged over de updates",
"SITEMAP_ERRORPING"	=>	"Sitemap gecreëerd, maar er is een fout geconstateerd bij één of meerder zoekmachines tijdens het pingen",
"SITEMAP_ERROR"			=>	"Het is niet mogelijk om uw sitemap te genareren",
"SITEMAP_WAIT"			=>	"<b>Wacht a.u.b:</b> Sitemap website wordt gecreëerd",

/* 
 * For: theme.php
*/
"THEME_CHANGED"			=>	"Uw thema is met succes gewijzigd",
"CHOOSE_THEME"			=>	"Kies uw thema",
"ACTIVATE_THEME"		=>	"Activeer thema",
"THEME_SCREENSHOT"	=>	"Thema afbeelding",
"THEME_PATH"				=>	"Locatie van het huidig thema",

/* 
 * For: resetwachtwoord.php
*/
"RESET_wachtwoord"		=>	"Reset wachtwoord",
"Uw_NEW"					=>	"Uw nieuwe",
"wachtwoord_IS"				=>	"wachtwoord is",
"ATTEMPT"						=>	"Poging",
"MSG_PLEASE_EMAIL"	=>	"Vul a.u.b. uw email adres in welke op dit system is geregistreerd, een nieuw wachtwoord wordt dan verzonden",
"SEND_NEW_PWD"			=>	"Verstuur nieuw wachtwoord",

/* 
 * For: settings.php
*/
"GENERAL_SETTINGS"	=>	"Algemene instellingen",
"WEBSITE_SETTINGS"	=>	"Website instellingen",
"LOCAL_TIMEZONE"		=>	"Lokale tijdszone",
"LANGUAGE"					=>	"Taal",
"USE_FANCY_URLS"		=>	"<b>Gebruik Fancy URLs</b> - Uw host moet mod_rewrite ondersteunen",
"ENABLE_HTML_ED"		=>	"<b>Gebruik de HTML editor</b>",
"USER_SETTINGS"			=>	"Gebruiker login instellingen",
"WARN_EMAILINVALID"	=>	"WAARSCHUWING: Dit email adres lijkt niet geldig te zijn!",
"ONLY_NEW_PASSWORD"	=>	"Vul hieronder alleen een wachtwoord in als u uw huidige wilt veranderen",
"NEW_PASSWORD"			=>	"Nieuw wachtwoord",
"CONFIRM_PASSWORD"	=>	"Bevestig wachtwoord",
"PASSWORD_NO_MATCH"	=>	"Wachtwoorden komen niet overeen",

/* 
 * For: contactform.php
*/
"MSG_CAPTCHA_FAILED"=>	"Captcha mislukt, We denken dat u een Spam robot bent!",
"CONTACT_FORM_SUB"	=>	"Contact formulier invoer",
"FROM"							=>	"van",
"MSG_CONTACTSUC"		=>	"Uw email is met succes verstuurt",
"MSG_CONTACTERR"		=>	"Er is een fout opgetreden tijdens het versturen van uw email",

/* 
 * For: 404-mailer.php
*/
"404_ENCOUNTERED"		=>	"Auto: 404 Error Encountered on",
"404_AUTO_MSG"			=>	"Dit is een automatisch bericht van uw website",
"PAGE_CANNOT_FOUND"	=>	"Een &lsquo;pagina niet gevonden&rsquo; waarschuwing is gevonden op",
"DOMAIN"						=>	"domein",
"DETAILS"						=>	"DETAILS",
"WHEN"							=>	"Wanneer",
"WHO"								=>	"Wie",
"FAILED_PAGE"				=>	"Foutive pagina",
"REFERRER"					=>	"Referentie",
"BROWSER"						=>	"Browser",

/* 
 * For: health-check.php
*/
"WEB_HEALTH_CHECK"	=>	"Website status controle",
"VERSION"						=>	"versie",
"UPG_NEEDED"				=>	"Upgrade benodigd naar",
"CANNOT_CHECK"			=>	"Kan niet gecontroleerd worden. Uw versie is",
"LATEST_VERSION"		=>	"Laaste versie geïnstalleerd",
"SERVER_SETUP"			=>	"Server setup",
"OR_GREATER_REQ"		=>	"of hoger is benodigd",
"OK"								=>	"OK",
"INSTALLED"					=>	"Geïnstalleerd",
"NOT_INSTALLED"			=>	"Niet geïnstalleerd",
"WARNING"						=>	"Waarschuwing",
"DATA_FILE_CHECK"		=>	"Data bestand integriteit controle",
"DIR_PERMISSIONS"		=>	"Map machtigingen",
"EXISTANCE"					=>	"%s gevonden",
"MISSING_FILE"			=>	"Bestand ontbreekt",
"BAD_FILE"					=>	"Foutief bestand",
"NO_FILE"						=>	"Geen bestand",
"GOOD_D_FILE"				=>	"Goed &lsquo;niet toegestaan&rsquo; bestand",
"GOOD_A_FILE"				=>	"Goed &lsquo;toegestaan&rsquo; bestand",
"CANNOT_DEL_FILE"		=>	"Kan bestand niet verwijderen",
"DOWNLOAD"					=>	"Download",
"WRITABLE"					=>	"Schrijfbaar",
"NOT_WRITABLE"			=>	"Niet schrijfbaar",

/* 
 * For: footer.php
*/
"POWERED_BY"				=>	"Mede mogelijk gemaakt door",
"PRODUCTION"				=>	"Production",
"SUBMIT_TICKET"			=>	"Stuur Ticket",

/* 
 * For: backups.php
*/
"PAGE_BACKUPS"			=>	"Pagina Backups",
"ASK_DELETE_ALL"		=>	"<em>V</em>erwijder alles",
"DELETE_ALL_BAK"		=>	"Verwijder alle backups?",
"TOTAL_BACKUPS"			=>	"totaal backups",

/* 
 * For: archive.php
*/
"SUCC_WEB_ARCHIVE"	=>	"Website met succes gearchiveerd!",
"SUCC_WEB_ARC_DEL"	=>	"Website archief met succes verwijderd",
"WEBSITE_ARCHIVES"	=>	"Website archieven",
"ARCHIVE_DELETED"		=>	"Archief met succes verwijderd",
"CREATE_NEW_ARC"		=>	"Creëer een nieuw archief",
"ASK_CREATE_ARC"		=>	"<em>C</em>reëer nu een nieuw archief",
"CREATE_ARC_WAIT"		=>	"<b>Wacht a.u.b.:</b> Webiste archief wordt gecreëerd...",
"DOWNLOAD_ARCHIVES"	=>	"Download archief",
"DELETE_ARCHIVE"		=>	"Verwijder archief",
"TOTAL_ARCHIVES"		=>	"Totaal archief",

/* 
 * For: include-nav.php
*/
"WELCOME"						=>	"Welkom", // used as 'Welcome USERNAME!'
"TAB_PAGES"					=>	"<em>P</em>agina&rsquo;s",
"TAB_FILES"					=>	"Bestanden (<em>F</em>)",
"TAB_THEME"					=>	"<em>T</em>hema",
"TAB_BACKUPS"				=>	"<em>B</em>ackups",
"TAB_SETTINGS"			=>	"In<em>s</em>tellingen",
"TAB_SUPPORT"				=>	"<em>O</em>ndersteuning",
"TAB_LOGOUT"				=>	"Uit<em>l</em>oggen",

/* 
 * For: sidebar-files.php
*/
"BROWSE_COMPUTER"		=>	"Bestand uploaden",
"UPLOAD"						=>	"Upload",

/* 
 * For: sidebar-support.php
*/
"SIDE_SUPPORT_LOG"	=>	"Instellingen &amp; l<em>o</em>gs",
"SIDE_VIEW_LOG"			=>	"Bekijk log",
"SIDE_HEALTH_CHK"		=>	"Website <em>s</em>tatus controle",
"SIDE_SUBMIT_TICKET"=>	"Stuur Tic<em>k</em>et",
"SIDE_DOCUMENTATION"=>	"<em>d</em>ocumentatie",

/* 
 * For: sidebar-theme.php
*/
"SIDE_VIEW_SITEMAP"	=>	"Bekijk sitemap (<em>V</em>)",
"SIDE_GEN_SITEMAP"	=>	"<em>G</em>enereer sitemap",
"SIDE_COMPONENTS"		=>	"Wijzig compon<em>e</em>nten",
"SIDE_EDIT_THEME"		=>	"Wijzig t<em>h</em>ema",
"SIDE_CHOOSE_THEME"	=>	"Kies <em>t</em>hema",

/* 
 * For: sidebar-pages.php
*/
"SIDE_CREATE_NEW"		=>	"<em>C</em>reëer nieuwe pagina",
"SIDE_VIEW_PAGES"		=>	"Bekijk alle <em>p</em>agina&rsquo;s",

/* 
 * For: sidebar-pages.php
*/
"SIDE_GEN_SETTINGS"	=>	"Algemene <em>V</em>oorkeuren",
"SIDE_USER_PROFILE"	=>	"<em>G</em>ebruikers profiel",

/* 
 * For: sidebar-pages.php
*/
"SIDE_VIEW_BAK"			=>	"Bekijk pagina backup",
"SIDE_WEB_ARCHIVES"	=>	"<em>W</em>ebsite archief",
"SIDE_PAGE_BAK"			=>	"Pagina <em>B</em>ackups",

/* 
 * For: error_checking.php
*/
"ER_PWD_CHANGE"			=>	"Niet vergeten uw <a href=\"settings.php#profile\">wachtwoord</a> te veranderen, dit in plaats van het gegeneerde wachtwoord...",
"ER_BAKUP_DELETED"	=>	"De backup is verwijderd voor %s",
"ER_REQ_PROC_FAIL"	=>	"Het gevraagde proces is mislukt",
"ER_Uw_CHANGES"		=>	"Uw veranderingen voor %s zijn bewaard",
"ER_HASBEEN_REST"		=>	"%s has been restored",
"ER_HASBEEN_DEL"		=>	"%s is verwijderd",
"ER_CANNOT_INDEX"		=>	"Het is niet mogelijk om de URl van de index pagina te wijzigen",
"ER_SETTINGS_UPD"		=>	"Uw voorkeuren zijn geupdate",
"ER_OLD_RESTORED"		=>	"Uw oude voorkeuren zijn terug gezet",
"ER_NEW_PWD_SENT"		=>	"Een nieuw wachtwoord is verzonden naar uw email",
"ER_SENDMAIL_ERR"		=>	"Er is een probleem met het verzenden van de email, probeer opnieuw",
"ER_FILE_DEL_SUC"		=>	"Bestand met succes verwijderd",
"ER_PROBLEM_DEL"		=>	"Er is een probleem met het verwijderen van het bestand",
"ER_COMPONENT_SAVE"	=>	"Uw components zijn bewaard",
"ER_COMPONENT_REST"	=>	"Uw components zijn teruggezet",
"ER_CANCELLED_FAIL"	=>	"<b>Geannuleerd:</b> De update voor dit bestand is geannuleerd",

/* 
 * For: changedata.php
*/
"CANNOT_SAVE_EMPTY"	=>	"Lege pagina opslaan is niet mogelijk",

/* 
 * For: template_functions.php
*/
"FTYPE_COMPRESSED"	=>	"Gecomprimeerd", //a file-type
"FTYPE_VECTOR"			=>	"Vector", //a file-type
"FTYPE_FLASH"				=>	"Flash", //a file-type
"FTYPE_VIDEO"				=>	"Video", //a file-type
"FTYPE_AUDIO"				=>	"Audio", //a file-type
"FTYPE_WEB"					=>	"Web", //a file-type
"FTYPE_DOCUMENTS"		=>	"Document", //a file-type
"FTYPE_SYSTEM"			=>	"Systeem", //a file-type
"FTYPE_MISC"				=>	"Overig", //a file-type
"IMAGES"						=>	"Foto",

/* 
 * For: login_functions.php
*/
"FILL_IN_REQ_FIELD"	=>	"Vul a.u.b. alle benodigde velden in",
"LOGIN_FAILED"			=>	"Login mislukt, controleer uw gebruikersnaam en wachtwoord",

/* 
 * For: Date Format
*/
"DATE_FORMAT"				=>	"j m, Y" //please keep short


);

?>
