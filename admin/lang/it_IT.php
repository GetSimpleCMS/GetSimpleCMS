<?php
/****************************************************
*
* @File: 			it_IT.php
* @Package:			GetSimple
* @Subject:			IT Italian language file
* @Date:			14 Sept 2009
* @Revision:		14 Sept 2009
* @Version:			GetSimple 1.7
* @Status:			Final
* @Traductors: 		Simone Lonardoni
*
*****************************************************/

$i18n = array(

/* 
 * For: install.php
*/
"PHPVER_ERROR"			=>	"<b>Impossibile continuare:</b> E' richiesto PHP 5.1.3 o seguente",
"SIMPLEXML_ERROR"		=>	"<b>Impossibile continuare:</b> <em>SimpleXML</em> non &egrave; installato",
"CURL_WARNING"			=>	"<b>Attenzione:</b> <em>cURL</em> non installato",
"TZ_WARNING"			=>	"<b>Attenzione:</b> manca <em>date_default_timezone_set</em>",
"WEBSITENAME_ERROR"		=>	"<b>Errore:</b> C'&egrave; stato un problema con il titolo del sito",
"WEBSITEURL_ERROR"		=>	"<b>Errore:</b> C'&egrave; stato un problema con l'URL del sito",
"USERNAME_ERROR"		=>	"<b>Errore:</b> Non &egrave; stato impostato il nome utente",
"EMAIL_ERROR"			=>	"<b>Errore:</b> C'&egrave; stato un problema con l' indirizzo e-mail",
"CHMOD_ERROR"			=>	"<b>Impossibile continuare:</b>Impossibile scrivere il file di configurazione. Imposta <em>CHMOD 777</em> nella cartelle /data/ e /backups/ e riprova",
"EMAIL_COMPLETE"		=>	"Setup completo",
"EMAIL_USERNAME"		=>	"Il tuo nome utente &egrave;",
"EMAIL_PASSWORD"		=>	"La tua nuova password &egrave;",
"EMAIL_LOGIN"			=>	"Accedi qui",
"EMAIL_THANKYOU"		=>	"Grazie per l'uso",
"NOTE_REGISTRATION"		=>	"Le informazioni di registrazione sono state inviate a",
"NOTE_REGERROR"			=>	"<b>Errore:</b> C'&egrave; stato un problema nell'inviare le informazioni di registrazione via e-mail. Si prega di prendere nota della password qui sotto",
"NOTE_USERNAME"			=>	"Il tuo nome utente &egrave;",
"NOTE_PASSWORD"			=>	"e la tua password &egrave;",
"INSTALLATION"			=>	"Installazione",
"LABEL_WEBSITE"			=>	"Nome del sito",
"LABEL_BASEURL"			=>	"URL di base del sito",
"LABEL_SUGGESTION"		=>	"Il nostro suggerimento &egrave;",
"LABEL_USERNAME"		=>	"Nome utente",
"LABEL_EMAIL"			=>	"Indirizzo Email",
"LABEL_INSTALL"			=>	"Installa!",

/* 
 * For: pages.php
*/
"MENUITEM_SUBTITLE"		=>	"oggeti del menu",
"HOMEPAGE_SUBTITLE"		=>	"homepage",
"PRIVATE_SUBTITLE"		=>	"privato",
"EDITPAGE_TITLE"		=>	"Modifica",
"VIEWPAGE_TITLE"		=>	"Visualizza",
"DELETEPAGE_TITLE"		=>	"Elimina",
"PAGE_MANAGEMENT"		=>	"Gestione Pagine",
"TOGGLE_STATUS"			=>	"Cambia stato",
"TOTAL_PAGES"			=>	"pagine totali",
"ALL_PAGES"				=>	"Tutte le pagine",

/* 
 * For: edit.php
*/
"PAGE_NOTEXIST"			=>	"La pagina richiesta non esiste",
"BTN_SAVEPAGE"			=>	"Salva",
"BTN_SAVEUPDATES"		=>	"Aggiorna",
"DEFAULT_TEMPLATE"		=>	"Modello predefinito",
"NONE"					=>	"Nessuno",
"PAGE"					=>	"Pagina",
"NEW_PAGE"				=>	"Nuova pagina",
"PAGE_EDIT_MODE"		=>	"Modifica pagine",
"CREATE_NEW_PAGE"		=>	"Crea una nuova pagina",
"VIEW"					=>	"<em>V</em>isualizza", 		// 'v' is the accesskey identifier
"PAGE_OPTIONS"			=>	"<em>O</em>pzioni", 		// 'o' is the accesskey identifier
"TOGGLE_EDITOR"			=>	"<em>C</em>ambia editor", 	// 'c' is the accesskey identifier
"SLUG_URL"				=>	"Slug/URL",
"TAG_KEYWORDS"			=>	"Tags &amp; Parole chiave",
"PARENT_PAGE"			=>	"Pagina di riferimento",
"TEMPLATE"				=>	"Modello",
"KEEP_PRIVATE"			=>	"Mantieni privata?",
"ADD_TO_MENU"			=>	"Aggiungi al men&ugrave;",
"PRIORITY"				=>	"Priorit&agrave;",
"MENU_TEXT"				=>	"Testo men&ugrave;",
"LABEL_PAGEBODY"		=>	"Corpo pagina",
"CANCEL"				=>	"Cancella",
"BACKUP_AVAILABLE"		=>	"Backup disponibile",
"MAX_FILE_SIZE"			=>	"Dimensione massima del file",
"LAST_SAVED"			=>	"Ultimo salvataggio",
"FILE_UPLOAD"			=>	"Carica file",
"OR"					=>	"o",

/* 
 * For: upload.php
*/
"ERROR_UPLOAD"			=>	"C'&egrave; stato un problema con il caricamento del file",
"FILE_SUCCESS_MSG"		=>	"Successo! File localizzato",
"FILE_MANAGEMENT"		=>	"Gestione File",
"UPLOADED_FILES"		=>	"Caricamento File",
"SHOW_ALL"				=>	"Visualizza Tutti",
"VIEW_FILE"				=>	"Visualizza File",
"DELETE_FILE"			=>	"elimina file",
"TOTAL_FILES"			=>	"file totali",

/* 
 * For: logout.php
*/
"LOGGED_OUT"			=>	"Uscito",
"MSG_LOGGEDOUT"			=>	"Ora sei uscito.",
"MSG_PLEASE"			=>	"Si prega di loggarsi se avete bisogno di ri-accedere al tuo account", 

/* 
 * For: index.php
*/
"LOGIN"					=>	"Accedi",
"USERNAME"				=>	"Nome utente",
"PASSWORD"				=>	"Password",
"FORGOT_PWD"			=>	"Hai dimenticato la password?",
"CONTROL_PANEL"			=>	"Panello di controllo",
"LOGIN_REQUIREMENT"		=>	"Requisiti di accesso",
"WARN_JS_COOKIES"		=>	"Controlla che i Cookies e Javascript siano abilitati nel tuo browser per funzionare correttamente",
"WARN_IE6"				=>	"Internet Explorer 6 pu&ograve; funzionare, ma non &egrave; supportato",

/* 
 * For: navigation.php
*/
"CURRENT_MENU" 			=> 	"Men&ugrave; attuale",
"NO_MENU_PAGES" 		=> 	"Non ci sono pagine impostate per apparire all'interno del men&ugrave; principale",

/* 
 * For: theme-edit.php
*/
"TEMPLATE_FILE" 		=> 	"Il modello <b>%s</b> &egrave; stato aggiornato con successo!",
"THEME_MANAGEMENT" 		=> 	"Gestione Temi",
"EDIT_THEME" 			=> 	"Modifica",
"EDITING_FILE" 			=> 	"Modifica File",
"BTN_SAVECHANGES" 		=> 	"Salva Modifiche",
"EDIT" 					=> 	"Modifica",

/* 
 * For: support.php
*/
"SETTINGS_UPDATED"		=> 	"Le impostazioni sono state aggiornate",
"UNDO" 					=> 	"Annulla",
"SUPPORT" 				=> 	"Assistenza",
"SETTINGS" 				=> 	"Impostazioni",
"ERROR" 				=> 	"Errore",
"BTN_SAVESETTINGS" 		=> 	"Salva impostazioni",
"EMAIL_ON_404" 			=> 	"Email administrator on 404 errors",
"VIEW_404" 				=> 	"Visualizza 404 Errori",
"VIEW_FAILED_LOGIN"		=> 	"Visualizza i tentativi di accesso falliti",
"VIEW_CONTACT_FORM"		=> 	"Visualizza il modulo di contatto Submissions",
"VIEW_TICKETS" 			=> 	"Visualizza Your Submitted Tickets",

/* 
 * For: log.php
*/
"MSG_HAS_BEEN_CLR" 		=> 	" &egrave; stato eliminato",
"LOGS" 					=> 	"Logs",
"VIEWING" 				=> 	"Visualizzazione",
"LOG_FILE" 				=> 	"Log file",
"CLEAR_ALL_DATA" 		=> 	"Cancella tutti i dati da",
"CLEAR_THIS_LOG" 		=> 	"<em>P</em>ulisci questo Log", // 'p' is the accesskey identifier
"LOG_FILE_ENTRY" 		=> 	"LOG FILE ENTRY",
"THIS_COMPUTER"			=>	"Questo computer",

/* 
 * For: backup-edit.php
*/
"BAK_MANAGEMENT"		=>	"Gestione Backup",
"ASK_CANCEL"			=>	"<em>A</em>nnulla", 	// 'a' is the accesskey identifier
"ASK_RESTORE"			=>	"<em>R</em>ipristina", 	// 'r' is the accesskey identifier
"ASK_DELETE"			=>	"<em>E</em>limina", 	// 'e' is the accesskey identifier
"BACKUP_OF"				=>	"Copia di backup dei",
"PAGE_TITLE"			=>	"Titolo pagina",
"YES"					=>	"Si",
"NO"					=>	"No",
"DATE"					=>	"Data",

/* 
 * For: components.php
*/
"COMPONENTS"			=>	"Componenti",
"DELETE_COMPONENT"		=>	"Elimina Componenti",
"EDIT"					=>	"Modifica",
"ADD_COMPONENT"			=>	"<em>A</em>ggiungi Componenti", // 'a' is the accesskey identifier
"SAVE_COMPONENTS"		=>	"Salva Componenti",

/* 
 * For: sitemap.php
*/
"SITEMAP_CREATED"		=>	"Sitemap creato! E' stato eseguito con successo il ping a 4 motori di ricerca",
"SITEMAP_ERRORPING"		=>	"Sitemap creato, per&ograve; c'&egrave; stato un errore nel pingare uno o pi&ugrave; motori di ricerca",
"SITEMAP_ERROR"			=>	"La tua Sitemap non pu&ograve; essere generata",
"SITEMAP_WAIT"			=>	"<b>Attendi:</b> Creazione della Sitemap del sito",

/* 
 * For: theme.php
*/
"THEME_CHANGED"			=>	"Il tema &egrave; stato cambiato con successo",
"CHOOSE_THEME"			=>	"Scegli il tema",
"ACTIVATE_THEME"		=>	"Attiva",
"THEME_SCREENSHOT"		=>	"Screenshot del tema",
"THEME_PATH"			=>	"Percorso del tema attuale",

/* 
 * For: resetpassword.php
*/
"RESET_PASSWORD"		=>	"Reset Password",
"YOUR_NEW"				=>	"Tu nuovo",
"PASSWORD_IS"			=>	"password &egrave;",
"ATTEMPT"				=>	"Tentativo",
"MSG_PLEASE_EMAIL"		=>	"Inserisci l'indirizzo email registrato su questo sistema, e ti sar&agrave; inviata una nuova password",
"SEND_NEW_PWD"			=>	"Invia nuova Password",

/* 
 * For: settings.php
*/
"GENERAL_SETTINGS"		=>	"Impostazioni generali",
"WEBSITE_SETTINGS"		=>	"Impostazioni sito web",
"LOCAL_TIMEZONE"		=>	"Timezone locale",
"LANGUAGE"				=>	"Lingua",
"USE_FANCY_URLS"		=>	"<b>Usa Fancy URLs</b> - Richiede che l'host abbia abilitato mod_rewrite",
"ENABLE_HTML_ED"		=>	"<b>Abilita l'editor HTML</b>",
"USER_SETTINGS"			=>	"Impostazioni accesso Utente",
"WARN_EMAILINVALID"		=>	"ATTENZIONE: Questo indirizzo email non sembra valido!",
"ONLY_NEW_PASSWORD"		=>	"Inserisci una password qui sotto se vuoi modificare quella corrente",
"NEW_PASSWORD"			=>	"Nuova password",
"CONFIRM_PASSWORD"		=>	"Conferma password",
"PASSWORD_NO_MATCH"		=>	"Le password non corrispondono",

/* 
 * For: contactform.php
*/
"MSG_CAPTCHA_FAILED"	=>	"Captcha fallito, pensiamo che sei un bot spam",
"CONTACT_FORM_SUB"		=>	"Modulo di inoltro contatti",
"FROM"					=>	"da",
"MSG_CONTACTSUC"		=>	"La tua mail &egrave; stata inviata con successo",
"MSG_CONTACTERR"		=>	"Si &egrave; verificato un errore durante l'invio",

/* 
 * For: 404-mailer.php
*/
"404_ENCOUNTERED"		=>	"Auto: 404 Error Encountered on",
"404_AUTO_MSG"			=>	"Questo &egrave; un messaggio automatico dal tuo sito web",
"PAGE_CANNOT_FOUND"		=>	"Un errore di 'page not found' si &egrave; verificato sul",
"DOMAIN"				=>	"dominio",
"DETAILS"				=>	"DETTAGLI",
"WHEN"					=>	"Quando",
"WHO"					=>	"Chi",
"FAILED_PAGE"			=>	"Pagina fallita",
"REFERRER"				=>	"Referrer",
"BROWSER"				=>	"Sfoglia",

/* 
 * For: health-check.php
*/
"WEB_HEALTH_CHECK"		=>	"Website Health Check",
"VERSION"				=>	"Versione",
"UPG_NEEDED"			=>	"Aggiornamento necessario per",
"CANNOT_CHECK"			=>	"In grado di verificare. La versione &egrave;",
"LATEST_VERSION"		=>	"Ultima versione installata",
"SERVER_SETUP"			=>	"Server Setup",
"OR_GREATER_REQ"		=>	"o maggiore &egrave; richiesto",
"OK"					=>	"OK",
"INSTALLED"				=>	"Installato",
"NOT_INSTALLED"			=>	"Non installato",
"WARNING"				=>	"Attenzione",
"DATA_FILE_CHECK"		=>	"Data File Integrity Check",
"DIR_PERMISSIONS"		=>	"Autorizzazioni cartella",
"EXISTANCE"				=>	"%s esistenza",
"MISSING_FILE"			=>	"File mancante",
"BAD_FILE"				=>	"File rovinato",
"NO_FILE"				=>	"No file",
"GOOD_D_FILE"			=>	"Good 'Deny' file",
"GOOD_A_FILE"			=>	"Good 'Allow' file",
"CANNOT_DEL_FILE"		=>	"Impossibile eliminare il file",
"DOWNLOAD"				=>	"Scarica",
"WRITABLE"				=>	"Scrivibile",
"NOT_WRITABLE"			=>	"Non scrivibile",

/* 
 * For: footer.php
*/
"POWERED_BY"			=>	"Powered by",
"PRODUCTION"			=>	"Produzione",
"SUBMIT_TICKET"			=>	"Inoltra Ticket",

/* 
 * For: backups.php
*/
"PAGE_BACKUPS"			=>	"Backup pagine",
"ASK_DELETE_ALL"		=>	"<em>E</em>limina tutto",
"DELETE_ALL_BAK"		=>	"Elimina tutti i backup?",
"TOTAL_BACKUPS"			=>	"backup totali",

/* 
 * For: archive.php
*/
"SUCC_WEB_ARCHIVE"		=>	"Sito web archiviato con successo!",
"SUCC_WEB_ARC_DEL"		=>	"Sito web eliminato con successo",
"WEBSITE_ARCHIVES"		=>	"Archivi siti web",
"ARCHIVE_DELETED"		=>	"Archivio eliminato con successo",
"CREATE_NEW_ARC"		=>	"Crea un nuovo archivio",
"ASK_CREATE_ARC"		=>	"<em>C</em>rea un nuovo archivio",
"CREATE_ARC_WAIT"		=>	"<b>Attendere:</b> Creazione dell'archivio del sito web ...",
"DOWNLOAD_ARCHIVES"		=>	"Scarica l'archivio",
"DELETE_ARCHIVE"		=>	"Elimina l'archivio",
"TOTAL_ARCHIVES"		=>	"archivi totali",

/* 
 * For: include-nav.php
*/
"WELCOME"				=>	"Benvenuto", 			// used as 'Welcome USERNAME!'
"TAB_PAGES"				=>	"<em>P</em>agine",
"TAB_FILES"				=>	"<em>F</em>ile",
"TAB_THEME"				=>	"<em>T</em>emi",
"TAB_BACKUPS"			=>	"<em>B</em>ackup",
"TAB_SETTINGS"			=>	"<em>I</em>mpostazioni",
"TAB_SUPPORT"			=>	"<em>A</em>ssistenza",
"TAB_LOGOUT"			=>	"<em>E</em>sci",

/* 
 * For: sidebar-files.php
*/
"BROWSE_COMPUTER"		=>	"Sfoglia il tuo computer",
"UPLOAD"				=>	"Carica",

/* 
 * For: sidebar-support.php
*/
"SIDE_SUPPORT_LOG"		=>	"<em>A</em>ssistenza impostazioni &amp; Logs",
"SIDE_VIEW_LOG"			=>	"Visualizza Log",
"SIDE_HEALTH_CHK"		=>	"Website <em>H</em>ealth Check",
"SIDE_SUBMIT_TICKET"	=>	"Inoltra Tic<em>k</em>et",
"SIDE_DOCUMENTATION"	=>	"<em>D</em>ocumentazione",

/* 
 * For: sidebar-theme.php
*/
"SIDE_VIEW_SITEMAP"		=>	"<em>V</em>isualizza la Sitemap",
"SIDE_GEN_SITEMAP"		=>	"<em>G</em>enera la Sitemap",
"SIDE_COMPONENTS"		=>	"Modifica i <em>c</em>omponenti",
"SIDE_EDIT_THEME"		=>	"<em>M</em>odifica",
"SIDE_CHOOSE_THEME"		=>	"<em>S</em>cegli",

/* 
 * For: sidebar-pages.php
*/
"SIDE_CREATE_NEW"		=>	"<em>C</em>rea una nuova pagina",
"SIDE_VIEW_PAGES"		=>	"Visualizza tutte le <em>p</em>agine",

/* 
 * For: sidebar-pages.php
*/
"SIDE_GEN_SETTINGS"		=>	"<em>I</em>mpostazioni generali",
"SIDE_USER_PROFILE"		=>	"Profilo <em>U</em>tente",

/* 
 * For: sidebar-pages.php
*/
"SIDE_VIEW_BAK"			=>	"Visualizza la pagina Backup",
"SIDE_WEB_ARCHIVES"		=>	"<em>A</em>rchivi",
"SIDE_PAGE_BAK"			=>	"<em>B</em>ackup",

/* 
 * For: error_checking.php
*/
"ER_PWD_CHANGE"			=>	"Non dimenticare di <a href=\"settings.php#profile\">cambiare la password</a> che è stata generata casualmente ...",
"ER_BAKUP_DELETED"		=>	"Il backup &egrave; stato eliminato per %s",
"ER_REQ_PROC_FAIL"		=>	"Il processo richiesto non &egrave; riuscito",
"ER_YOUR_CHANGES"		=>	"Le modifiche a %s sono state salvate",
"ER_HASBEEN_REST"		=>	"%s &egrave; stato ripristinato",
"ER_HASBEEN_DEL"		=>	"%s &egrave; stato eliminato",
"ER_CANNOT_INDEX"		=>	"Non &egrave; possibile modificare l'URL della pagina index",
"ER_SETTINGS_UPD"		=>	"Le impostazioni sono state aggiornate",
"ER_OLD_RESTORED"		=>	"Le vecchie impostazioni sono state ripristinate",
"ER_NEW_PWD_SENT"		=>	"Una nuova password &egrave; stata inviata all'indirizzo di posta elettronica fornito",
"ER_SENDMAIL_ERR"		=>	"C'&egrave; stato un problema durante l'invio della posta elettronica. Per favore prova ancora",
"ER_FILE_DEL_SUC"		=>	"File eliminato con successo",
"ER_PROBLEM_DEL"		=>	"C'&egrave; stato un problema eliminando il file",
"ER_COMPONENT_SAVE"		=>	"I componenti sono stati salvati",
"ER_COMPONENT_REST"		=>	"I componenti sono stati ripristinati",
"ER_CANCELLED_FAIL"		=>	"<b>Cancellato:</b> L'aggiornamento di questo file &egrave; stato cancellato",

/* 
 * For: changedata.php
*/
"CANNOT_SAVE_EMPTY"		=>	"Non &egrave; possibile salvare una pagina vuota",

/* 
 * For: template_functions.php
*/
"FTYPE_COMPRESSED"		=>	"Compresso", 	//a file-type
"FTYPE_VECTOR"			=>	"Vector", 		//a file-type
"FTYPE_FLASH"			=>	"Flash", 		//a file-type
"FTYPE_VIDEO"			=>	"Video", 		//a file-type
"FTYPE_AUDIO"			=>	"Audio", 		//a file-type
"FTYPE_WEB"				=>	"Web", 			//a file-type
"FTYPE_DOCUMENTS"		=>	"Documenti", 	//a file-type
"FTYPE_SYSTEM"			=>	"Sistema", 		//a file-type
"FTYPE_MISC"			=>	"Misc", 		//a file-type
"IMAGES"				=>	"Immagini",

/* 
 * For: login_functions.php
*/
"FILL_IN_REQ_FIELD"		=>	"Si prega di compilare tutti i campi obbligatori",
"LOGIN_FAILED"			=>	"Accesso fallito. Per favore controlla il tuo Nome Utente e la Password",

/* 
 * For: Date Format
*/
"DATE_FORMAT"			=>	"j M Y" //please keep short


);

?>