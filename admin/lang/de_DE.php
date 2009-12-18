<?php

/****************************************************
*
* @File: 		de_DE.php
* @Package:		GetSimple
* @Subject:		German language file
* @Date:		07 Sept 2009
* @Revision:	12 Oct 2009
* @Version:		GetSimple 1.6
* @Status:		1.1
* @Translators: hakan http://uysal-consulting.de/ & wizzy - http://is-wizard.com/	
*
*****************************************************/

$i18n = array(

/* 
 * For: install.php
*/
"PHPVER_ERROR"				=>	"<b>Abbruch:</b> PHP 5.1.3 oder höher wird benötigt. Sie haben ",
"SIMPLEXML_ERROR"			=>	"<b>Abbruch:</b> <em>SimpleXML</em> ist nicht installiert",
"CURL_WARNING"				=>	"<b>Warnung:</b> <em>cURL</em> ist nicht installiert",
"TZ_WARNING"				=>	"<b>Warnung:</b> <em>date_default_timezone_set</em> ist nicht gesetzt",
"WEBSITENAME_ERROR"			=>	"<b>Fehler:</b> Es gibt ein Problem mit dem Titel Ihrer Website",
"WEBSITEURL_ERROR"			=>	"<b>Fehler:</b> Es gibt ein Problem mit der URL Ihrer Website",
"USERNAME_ERROR"			=>	"<b>Fehler:</b> Benutzername war nicht gesetzt",
"EMAIL_ERROR"				=>	"<b>Fehler:</b> Es gibt ein Problem mit Ihrer E-Mail Adresse",
"CHMOD_ERROR"				=>	"<b>Abbruch:</b> Die Konfigurationsdatei kann nicht geschrieben werden. Führen Sie <em>CHMOD 777</em> auf den Ordnern /data/ und /backups/ aus und versuchen es erneut",
"EMAIL_COMPLETE"			=>	"Einrichtung vollständig",
"EMAIL_USERNAME"			=>	"Ihr Benutzername lautet",
"EMAIL_PASSWORD"			=>	"Ihr neues Passwort ist",
"EMAIL_LOGIN"				=>	"Melden Sie sich hier an",
"EMAIL_THANKYOU"			=>	"Danke für den Einsatz von",
"NOTE_REGISTRATION"			=>	"Ihre Zugangsdaten wurden versendet an",
"NOTE_REGERROR"				=>	"<b>Fehler:</b> Ihre Zugangsdaten konnten nicht per E-Mail versendet werden. Notieren Sie sich daher folgendes Passwort",
"NOTE_USERNAME"				=>	"Ihr Benutzername lautet",
"NOTE_PASSWORD"				=>	"und Ihr Passwort ist",
"INSTALLATION"				=>	"Installation",
"LABEL_WEBSITE"				=>	"Website Name",
"LABEL_BASEURL"				=>	"Website Basis URL",
"LABEL_SUGGESTION"			=>	"Unser Vorschlag ist",
"LABEL_USERNAME"			=>	"Benutzername",
"LABEL_EMAIL"				=>	"E-Mail Adresse",
"LABEL_INSTALL"				=>	"Installation starten",

/* 
 * For: pages.php
*/
"MENUITEM_SUBTITLE"			=>	"Menüeintrag",
"HOMEPAGE_SUBTITLE"			=>	"Startseite",
"PRIVATE_SUBTITLE"			=>	"Privat",
"EDITPAGE_TITLE"			=>	"Seite bearbeiten",
"VIEWPAGE_TITLE"			=>	"Seite anzeigen",
"DELETEPAGE_TITLE"			=>	"Seite löschen",
"PAGE_MANAGEMENT"			=>	"Seitenverwaltung", 
"TOGGLE_STATUS"				=>	"Editor umschalten", // 'g' is the accesskey identifier
"TOTAL_PAGES"				=>	"Seiten",
"ALL_PAGES"					=>	"Alle Seiten",

/* 
 * For: edit.php
*/
"PAGE_NOTEXIST"				=>	"Die angeforderte Seite existiert nicht",
"BTN_SAVEPAGE"				=>	"Seite speichern",
"BTN_SAVEUPDATES"			=>	"Änderungen speichern",
"DEFAULT_TEMPLATE"			=>	"Standardvorlage",
"NONE"						=>	"None",   
"PAGE"						=>	"Seite",
"NEW_PAGE"					=>	"Neue Seite",
"PAGE_EDIT_MODE"			=>	"Seite bearbeiten",
"CREATE_NEW_PAGE"			=>	"Neue Seite erstellen",
"VIEW"						=>	"<em>V</em>orschau", // 'v' is the accesskey identifier
"PAGE_OPTIONS"				=>	"<em>O</em>ptionen", // 'o' is the accesskey identifier
"TOGGLE_EDITOR"				=>	"Editor/HTML", // 'g' is the accesskey identifier 
"SLUG_URL"					=>	"Seitenname/URL", 
"TAG_KEYWORDS"				=>	"Tags &amp; Schlagworte", 
"PARENT_PAGE"				=>	"Übergeordnete Seite",
"TEMPLATE"					=>	"Vorlage",
"KEEP_PRIVATE"				=>	"Nicht veröffentlichen",
"ADD_TO_MENU"				=>	"Im Menü anzeigen",
"PRIORITY"					=>	"Priorität",
"MENU_TEXT"					=>	"Menübezeichnung",
"LABEL_PAGEBODY"			=>	"Seiteninhalt",
"CANCEL"					=>	"Abbrechen",
"BACKUP_AVAILABLE"			=>	"Sicherung verfügbar",
"MAX_FILE_SIZE"				=>	"Maximale Dateigröße",
"LAST_SAVED"				=>	"Zuletzt gespeichert",
"FILE_UPLOAD"				=>	"Datei hochladen",
"OR"						=>	"oder",

/* 
 * For: upload.php
*/
"ERROR_UPLOAD"				=>	"Es gab ein Problem beim Hochladen der Datei",
"FILE_SUCCESS_MSG"			=>	"Erfolg! Die Datei befindet sich",
"FILE_MANAGEMENT"			=>	"Dateiverwaltung",
"UPLOADED_FILES"			=>	"Hochgeladene Dateien",
"SHOW_ALL"					=>	"Alle anzeigen",
"VIEW_FILE"					=>	"Datei anzeigen",
"DELETE_FILE"				=>	"Datei löschen",
"TOTAL_FILES"				=>	"Dateien",

/* 
 * For: logout.php
*/
"LOGGED_OUT"				=>	"abgemeldet",
"MSG_LOGGEDOUT"				=>	"Sie sind nun abgemeldet.",
"MSG_PLEASE"				=>	"Bitte melden Sie sich erneut an um auf Ihren Account zuzugreifen", 

/* 
 * For: index.php
*/
"LOGIN"						=>	"Login",
"USERNAME"					=>	"Benutzername",
"PASSWORD"					=>	"Passwort",
"FORGOT_PWD"				=>	"Passwort vergessen?",
"CONTROL_PANEL"				=>	"Konsole", 
"LOGIN_REQUIREMENT"			=>	"Anmeldevoraussetzungen",
"WARN_JS_COOKIES"			=>	"Bitte aktivieren Sie Cookies und JavaScript in Ihrem Browser, um eine fehlerfreie Darstellung der Seite zu ermöglichen.", 
"WARN_IE6"					=>	"Internet Explorer 6 sollte funktionieren, wird aber nicht empfohlen",

/* 
 * For: navigation.php
*/
"CURRENT_MENU" 				=> 	"Aktuelle Menüstruktur",
"NO_MENU_PAGES" 			=> 	"Es sind keine Seiten für die Anzeige im Menü eingerichtet.",

/* 
 * For: theme-edit.php
*/
"TEMPLATE_FILE" 			=> 	"Die Vorlage aus der Datei <b>%s</b> wurde erfolgreich hochgeladen",
"THEME_MANAGEMENT" 			=> 	"Vorlagenverwaltung", // wizzy
"EDIT_THEME" 				=> 	"Vorlage bearbeiten",
"EDITING_FILE" 				=> 	"Datei bearbeiten",
"BTN_SAVECHANGES" 			=> 	"Änderungen speichern",
"EDIT" 						=> 	"bearbeiten",

/* 
 * For: support.php
*/
"SETTINGS_UPDATED"			=> 	"Ihre Einstellungen wurden aktualisiert",
"UNDO" 						=> 	"Rückgängig",
"SUPPORT" 					=> 	"Support",
"SETTINGS" 					=> 	"Einstellungen",
"ERROR" 					=> 	"Fehler",
"BTN_SAVESETTINGS" 			=> 	"Einstellungen speichern",
"EMAIL_ON_404" 				=> 	"Administrator per E-Mail benachrichtigen bei 'Seite nicht gefunden' (404) Fehlern.",
"VIEW_404" 					=> 	"Zeige 'Seite nicht gefunden' (404) Fehler an",
"VIEW_FAILED_LOGIN"			=> 	"Zeige die Anzahl der fehlgeschlagenen Anmeldeversuche an",
"VIEW_CONTACT_FORM"			=> 	"Zeige abgesendete Kontaktformulareinträge an",
"VIEW_TICKETS" 				=> 	"Zeige die erstellten Tickets an",

/* 
 * For: log.php
*/
"MSG_HAS_BEEN_CLR" 			=> 	" wurde bereinigt",
"LOGS" 						=> 	"Logs",
"VIEWING" 					=> 	"Anzeige der",
"LOG_FILE" 					=> 	"Logdatei ",
"CLEAR_ALL_DATA" 			=> 	"Wirklich alle Einträge löschen",
"CLEAR_THIS_LOG" 			=> 	"Einträge löschen", // 'c' is the accesskey identifier
"LOG_FILE_ENTRY" 			=> 	"Log Eintrag",
"THIS_COMPUTER"				=>	"Dieser Computer",

/* 
 * For: backup-edit.php
*/
"BAK_MANAGEMENT"			=>	"Backupverwaltung",
"ASK_CANCEL"				=>	"<em></em>Abbrechen", // 'c' is the accesskey identifier
"ASK_RESTORE"				=>	"<em></em>Wiederherstellen", // 'r' is the accesskey identifier
"ASK_DELETE"				=>	"<em></em>Löschen", // 'd' is the accesskey identifier
"BACKUP_OF"					=>	"Backup von",
"PAGE_TITLE"				=>	"Seitentitel",
"YES"						=>	"Ja",
"NO"						=>	"Nein",
"DATE"						=>	"Datum",

/* 
 * For: components.php
*/
"COMPONENTS"				=>	"Komponenten",
"DELETE_COMPONENT"			=>	"Komponente löschen",
"EDIT"						=>	"Bearbeiten",
"ADD_COMPONENT"				=>	"<em></em>Komponente hinzufügen", // 'a' is the accesskey identifier
"SAVE_COMPONENTS"			=>	"Komponente speichern",

/* 
 * For: sitemap.php
*/
"SITEMAP_CREATED"			=>	"Die Sitemap wurde erzeugt! Ausserdem wurden 4 Suchmaschinen auf die Aktualisierung hingewiesen",
"SITEMAP_ERRORPING"			=>	"Die Sitemap wurde erzeugt, jedoch trat ein Fehler beim Benachrichtigen der Suchenmaschinen auf",
"SITEMAP_ERROR"				=>	"Die Sitemap konnte nicht erzeugt werden",
"SITEMAP_WAIT"				=>	"<b>Bitte warten Sie:</b> Die Sitemap wird gerade erzeugt",

/* 
 * For: theme.php
*/
"THEME_CHANGED"				=>	"Ihre Vorlage wurde erfolgreich verändert",
"CHOOSE_THEME"				=>	"Wählen Sie Ihre Vorlage aus",
"ACTIVATE_THEME"			=>	"Vorlage aktivieren",
"THEME_SCREENSHOT"			=>	"Screenshot der Vorlage",
"THEME_PATH"				=>	"Pfad der aktuellen Vorlage",

/* 
 * For: resetpassword.php
*/
"RESET_PASSWORD"			=>	"Passwort zurücksetzen",
"YOUR_NEW"					=>	"Ihr neues",
"PASSWORD_IS"				=>	"Passwort lautet",
"ATTEMPT"					=>	"Versuch",
"MSG_PLEASE_EMAIL"			=>	"Bitte geben Sie Ihre E-Mail-Adresse ein. Sie erhalten ein neues Passwort per E-Mail.",
"SEND_NEW_PWD"				=>	"Neues Passwort senden",

/* 
 * For: settings.php
*/
"GENERAL_SETTINGS"			=>	"Allgemeine Einstellungen",
"WEBSITE_SETTINGS"			=>	"Website Einstellungen",
"LOCAL_TIMEZONE"			=>	"Lokale Zeitzone",
"LANGUAGE"					=>	"Sprache",
"USE_FANCY_URLS"			=>	"<b>Suchmaschinenfreundliche URLs aktivieren</b> - Das Modul mod_rewrite muss auf Ihrem Server aktiviert sein. (Ist das nicht der Fall, kann der Server die URL nicht richtig verarbeiten und es kommt zu Fehler 404 - Seite nicht gefunden!)",
"ENABLE_HTML_ED"			=>	"<b>HTML Editor aktivieren</b>",
"USER_SETTINGS"				=>	"Anmeldeeinstellungen",
"WARN_EMAILINVALID"			=>	"Achtung: Die Syntax der angegebenen E-Mail Adresse sieht nicht gültig aus",
"ONLY_NEW_PASSWORD"			=>	"Geben Sie hier nur ein Passwort an, wenn Sie Ihres ändern möchten",
"NEW_PASSWORD"				=>	"Neues Passwort",
"CONFIRM_PASSWORD"			=>	"Passwort wiederholen",
"PASSWORD_NO_MATCH"			=>	"Die Passwörter stimmen nicht überein",

/* 
 * For: contactform.php
*/
"MSG_CAPTCHA_FAILED"		=>	"Captcha fehlgeschlagen, wir denken Sie sind ein Spambot",
"CONTACT_FORM_SUB"			=>	"Contact Form Submission",
"FROM"						=>	"von",
"MSG_CONTACTSUC"			=>	"Ihre E-Mail wurde erfolgreich versendet",
"MSG_CONTACTERR"			=>	"Beim versenden Ihrer E-Mail ist ein Fehler aufgetreten",

/* 
 * For: 404-mailer.php
*/
"404_ENCOUNTERED"			=>	"Auto: 404 Error Encountered on",
"404_AUTO_MSG"				=>	"Dies ist eine automatische Nachricht Ihrer Website",
"PAGE_CANNOT_FOUND"			=>	"Ein 'Seite nicht gefunden (404)' Fehler ist aufgetreten bei",
"DOMAIN"					=>	"Domäne",
"DETAILS"					=>	"Details",
"WHEN"						=>	"Wann",
"WHO"						=>	"Wer",
"FAILED_PAGE"				=>	"Fehlgeschlagene Seite",
"REFERRER"					=>	"Referrer",
"BROWSER"					=>	"Browser",

/* 
 * For: health-check.php
*/
"WEB_HEALTH_CHECK"			=>	"Website Gesundheitscheck",
"VERSION"					=>	"Version",
"UPG_NEEDED"				=>	"Aktualisierung wird benötigt zu",
"CANNOT_CHECK"				=>	"Es kann nicht geprüft werden.<br> Ihre Version ist",
"LATEST_VERSION"			=>	"Die aktuellste Version wurde installiert",
"SERVER_SETUP"				=>	"Server Konfigutation",
"OR_GREATER_REQ"			=>	"oder höher wird benötigt",
"OK"						=>	"OK",
"INSTALLED"					=>	"installiert",
"NOT_INSTALLED"				=>	"nicht installiert",
"WARNING"					=>	"Warnung",
"DATA_FILE_CHECK"			=>	"Integritätstest der Datendatei",
"DIR_PERMISSIONS"			=>	"Verzeichnisrechte",
"EXISTANCE"					=>	"%s Existance",
"MISSING_FILE"				=>	"fehlende Datei",
"BAD_FILE"					=>	"fehlerhafte Datei",
"NO_FILE"					=>	"keine Datei",
"GOOD_D_FILE"				=>	"Deny Datei - ok",
"GOOD_A_FILE"				=>	"Allow Datei - ok",
"CANNOT_DEL_FILE"			=>	"Die Datei kann nicht gelöscht werden",
"DOWNLOAD"					=>	"Download",
"WRITABLE"					=>	"beschreibbar",
"NOT_WRITABLE"				=>	"nicht beschreibbar",

/* 
 * For: footer.php
*/
"POWERED_BY"				=>	"Powered by",
"PRODUCTION"				=>	"Produktion",
"SUBMIT_TICKET"				=>	"Ticket erstellen",

/* 
 * For: backups.php
*/
"PAGE_BACKUPS"				=>	"Seiten Backups", 
"ASK_DELETE_ALL"			=>	"Alle löschen", // 'd' is the accesskey identifier
"DELETE_ALL_BAK"			=>	"Wirklich alle Backups löschen?", 
"TOTAL_BACKUPS"				=>	"Backups insgesamt", 

/* 
 * For: archive.php
*/
"SUCC_WEB_ARCHIVE"			=>	"Website wurde erfolgreich archiviert!",
"SUCC_WEB_ARC_DEL"			=>	"Website Archiv erfolgreich gelöscht",
"WEBSITE_ARCHIVES"			=>	"Website Archiv",
"ARCHIVE_DELETED"			=>	"Archiv erfolgreich gelöscht",
"CREATE_NEW_ARC"			=>	"Neues Archiv erstellen",
"ASK_CREATE_ARC"			=>	"<em></em>Neues Archiv erstellen",
"CREATE_ARC_WAIT"			=>	"<b>Bitte warten:</b> Website Archiv wird gerade erstellt ...",
"DOWNLOAD_ARCHIVES"			=>	"Archiv herunterladen",
"DELETE_ARCHIVE"			=>	"Archiv löschen",
"TOTAL_ARCHIVES"			=>	"Archive",

/* 
 * For: include-nav.php
*/
"WELCOME"					=>	"Willkommen", // used as 'Welcome USERNAME!'
"TAB_PAGES"					=>	"<em>S</em>eiten",
"TAB_FILES"					=>	"<em>D</em>ateien",
"TAB_THEME"					=>	"<em>V</em>orlagen",
"TAB_BACKUPS"				=>	"<em>B</em>ackups",
"TAB_SETTINGS"				=>	"<em>E</em>instellungen",
"TAB_SUPPORT"				=>	"Supp<em>o</em>rt",
"TAB_LOGOUT"				=>	"<em>A</em>bmelden",

/* 
 * For: sidebar-files.php
*/
"BROWSE_COMPUTER"			=>	"Durchsuchen Sie Ihren Computer",
"UPLOAD"					=>	"Hochladen",

/* 
 * For: sidebar-support.php
*/
"SIDE_SUPPORT_LOG"			=>	"Supp<em>o</em>rt Einstellungen &amp; Logs",
"SIDE_VIEW_LOG"				=>	"Log anzeigen",
"SIDE_HEALTH_CHK"			=>	"Website <em>G</em>esundheitscheck",
"SIDE_SUBMIT_TICKET"		=>	"Tic<em>k</em>et anlegen",
"SIDE_DOCUMENTATION"		=>	"<em>D</em>okumentation",

/* 
 * For: sidebar-theme.php
*/
"SIDE_VIEW_SITEMAP"			=>	"<em>Z</em>eige Sitemap",
"SIDE_GEN_SITEMAP"			=>	"<em>E</em>rzeuge Sitemap",
"SIDE_COMPONENTS"			=>	"<em>B</em>earbeite Komponenten",
"SIDE_EDIT_THEME"			=>	"Bearbeite Vorlage", 
"SIDE_CHOOSE_THEME"			=>	"Vorlage auswählen",

/* 
 * For: sidebar-pages.php
*/
"SIDE_CREATE_NEW"			=>	"<em>N</em>eue Seite erstellen",
"SIDE_VIEW_PAGES"			=>	"Alle <em>S</em>eiten anzeigen",

/* 
 * For: sidebar-pages.php
*/
"SIDE_GEN_SETTINGS"			=>	"Allgemeine <em>E</em>instellungen",
"SIDE_USER_PROFILE"			=>	"<em>B</em>enutzer Profil",

/* 
 * For: sidebar-pages.php
*/
"SIDE_VIEW_BAK"				=>	"Seiten Backups", 
"SIDE_WEB_ARCHIVES"			=>	"<em>W</em>ebsite Archive", 
"SIDE_PAGE_BAK"				=>	"<em>S</em>eiten Backups", 

/* 
 * For: error_checking.php
*/
"ER_PWD_CHANGE"				=>	"Vergessen Sie nicht Ihr Passwort vom derzeit zufällig generierten auf <a href=\"settings.php#profile\">ein neues zu ändern</a>",
"ER_BAKUP_DELETED"			=>	"Die Sicherung für %s wurde gelöscht", 
"ER_REQ_PROC_FAIL"			=>	"Der angeforderte Prozess ist fehlgeschlagen",
"ER_YOUR_CHANGES"			=>	"Ihre Änderungen zu %s wurden gespeichert",
"ER_HASBEEN_REST"			=>	"%s wurde wiederhergestellt",
"ER_HASBEEN_DEL"			=>	"%s wurde gelöscht",
"ER_CANNOT_INDEX"			=>	"Sie können nicht die URL der Startseite verändern",
"ER_SETTINGS_UPD"			=>	"Ihre Einstellungen wurden gespeichert",
"ER_OLD_RESTORED"			=>	"Ihre alten Einstellungen wurden wiederhergestellt",
"ER_NEW_PWD_SENT"			=>	"Ein neues Passwort wurde an die angegebene E-Mail Adresse versendet",
"ER_SENDMAIL_ERR"			=>	"Es ist ein Fehler beim Versenden der E-Mail aufgetreten. Bitte versuchen Sie es erneut",
"ER_FILE_DEL_SUC"			=>	"Datei wurde erfolgreich gelöscht",
"ER_PROBLEM_DEL"			=>	"Es trat ein Fehler beim Löschen der Datei auf",
"ER_COMPONENT_SAVE"			=>	"Ihre Komponenten wurden gespeichert",
"ER_COMPONENT_REST"			=>	"Ihre Komponenten wurden wiederhergestellt",
"ER_CANCELLED_FAIL"			=>	"<b>Abgebrochen:</b> Die Aktualisierungen in der Datei wurden nicht gespeichert",

/* 
 * For: changedata.php
*/
"CANNOT_SAVE_EMPTY"			=>	"Sie können keine leere Seite speichern",

/* 
 * For: template_functions.php
*/
"FTYPE_COMPRESSED"			=>	"Compressed", //a file-type
"FTYPE_VECTOR"				=>	"Vector", //a file-type
"FTYPE_FLASH"				=>	"Flash", //a file-type
"FTYPE_VIDEO"				=>	"Video", //a file-type
"FTYPE_AUDIO"				=>	"Audio", //a file-type
"FTYPE_WEB"					=>	"Web", //a file-type
"FTYPE_DOCUMENTS"			=>	"Dokumente", //a file-type
"FTYPE_SYSTEM"				=>	"System", //a file-type
"FTYPE_MISC"				=>	"Sonstiges", //a file-type
"IMAGES"					=>	"Bilder",

/* 
 * For: login_functions.php
*/
"FILL_IN_REQ_FIELD"			=>	"Bitte füllen Sie alle benötigten Pflichtfelder aus",
"LOGIN_FAILED"				=>	"Anmeldung fehlgeschlagen. Bitte prüfen Sie Ihren Benutzernamen und das Passwort.",

/* 
 * For: Date Format
*/
"DATE_FORMAT"				=>	"d.m.Y" //please keep short


);

?>