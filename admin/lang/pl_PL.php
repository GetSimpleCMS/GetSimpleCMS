<?php
/****************************************************
*
* @File: 		pl_PL.php
* @Package:		GetSimple
* @Subject:		Polish language file
* @Date:		02 Sept 2009
* @Revision:	02 Sept 2009
* @Version:		GetSimple 1.6
* @Status:		1.0
* @Translator: 	Krzysztof Kotowicz - www.kotos.net	
*
*****************************************************/

$i18n = array(

/* 
 * For: install.php
*/
"PHPVER_ERROR"			=>	"<b>Kontynuacja niemożliwa:</b> wymagany jest PHP 5.1.3, lub nowszy, ty masz",
"SIMPLEXML_ERROR"		=>	"<b>Kontynuacja niemożliwa:</b> <em>SimpleXML</em> jest niezainstalowany",
"CURL_WARNING"			=>	"<b>Ostrzeżenie:</b> <em>cURL</em> Niezainstalowany",
"TZ_WARNING"				=>	"<b>Ostrzeżenie:</b> <em>date_default_timezone_set</em> jest zagubiony",
"WEBSITENAME_ERROR"	=>	"<b>Błąd:</b> Jest problem z tytułem twojej strony",
"WEBSITEURL_ERROR"	=>	"<b>Błąd:</b> Jest problem z URLem strony",
"USERNAME_ERROR"		=>	"<b>Błąd:</b> Użytkownik nie został ustawiony",
"EMAIL_ERROR"				=>	"<b>Błąd:</b> Jest problem z Twoim adresem e-mail",
"CHMOD_ERROR"				=>	"<b>Kontynuacja niemożliwa:</b> Nie można zapisac pliku konfiguracyjnego. Ustaw <em>CHMOD 777</em> na folder /data/ oraz /backups/ i spróbuj ponownie",
"EMAIL_COMPLETE"		=>	"Ustawienia zakończone",
"EMAIL_USERNAME"		=>	"Twoje imię",
"EMAIL_PASSWORD"		=>	"Twoje nowe hasło",
"EMAIL_LOGIN"				=>	"Logowanie",
"EMAIL_THANKYOU"		=>	"Dziękujemy za używanie",
"NOTE_REGISTRATION"	=>	"Twoje dane rejestracji zostały wysłane",
"NOTE_REGERROR"			=>	"<b>Błąd:</b> Jest problem z wysłaniem informacji rejestracyjnych na e-mail. Proszę zapisać poniższe hasło",
"NOTE_USERNAME"			=>	"Nazwa użytkownika to",
"NOTE_PASSWORD"			=>	"hasło to",
"INSTALLATION"			=>	"Instalacja",
"LABEL_WEBSITE"			=>	"Nazwa strony",
"LABEL_BASEURL"			=>	"URL strony głównej",
"LABEL_SUGGESTION"	=>	"Nasza sugestja to",
"LABEL_USERNAME"		=>	"Użytkownik",
"LABEL_EMAIL"				=>	"Adres e-mail",
"LABEL_INSTALL"			=>	"Instaluj!",

/* 
 * For: pages.php
*/
"MENUITEM_SUBTITLE"	=>	"wyświetlane w menu",
"HOMEPAGE_SUBTITLE"	=>	"Strona główna",
"PRIVATE_SUBTITLE"	=>	"prywatny",
"EDITPAGE_TITLE"		=>	"Edytuj stronę",
"VIEWPAGE_TITLE"		=>	"Zobacz stronę",
"DELETEPAGE_TITLE"	=>	"Usuń stronę",
"PAGE_MANAGEMENT"		=>	"Zarządzanie stronami",
"TOGGLE_STATUS"			=>	"Status",
"TOTAL_PAGES"				=>	"- wszystkich stron",
"ALL_PAGES"					=>	"Wszystkie strony",

/* 
 * For: edit.php
*/
"PAGE_NOTEXIST"			=>	"Strona nie istnieje",
"BTN_SAVEPAGE"			=>	"Zapisz stronę",
"BTN_SAVEUPDATES"		=>	"Zapisz aktualizację",
"DEFAULT_TEMPLATE"	=>	"Domyślny szablon",
"NONE"							=>	"Nie",
"PAGE"							=>	"Strona",
"NEW_PAGE"					=>	"Nowa strona",
"PAGE_EDIT_MODE"		=>	"Tryb edycji strony",
"CREATE_NEW_PAGE"		=>	"Nowa strona",
"VIEW"							=>	"Podgląd", // 'v' is the accesskey identifier
"PAGE_OPTIONS"			=>	"Opcje strony", // 'o' is the accesskey identifier
"TOGGLE_EDITOR"			=>	"Wyłacz edytor", // 'g' is the accesskey identifier
"SLUG_URL"					=>	"plik/URL",
"TAG_KEYWORDS"			=>	"Tagi i słowa kluczowe",
"PARENT_PAGE"				=>	"Podstrona",
"TEMPLATE"					=>	"Szablon",
"KEEP_PRIVATE"			=>	"Prywatna?",
"ADD_TO_MENU"				=>	"Dodaj do menu",
"PRIORITY"					=>	"Kolejność",
"MENU_TEXT"					=>	"Tekst w menu",
"LABEL_PAGEBODY"		=>	"Body strony",
"CANCEL"						=>	"Zrezygnuj",
"BACKUP_AVAILABLE"	=>	"Dostępna kopia",
"MAX_FILE_SIZE"			=>	"Maks. rozmiar pliku",
"LAST_SAVED"				=>	"Ostanio zapisane",
"FILE_UPLOAD"				=>	"Załadowane pliki",
"OR"								=>	"lub",

/* 
 * For: upload.php
*/
"ERROR_UPLOAD"			=>	"Problem z załadowaniem pliku",
"FILE_SUCCESS_MSG"	=>	"Sukces! Plik załadowany",
"FILE_MANAGEMENT"		=>	"Zarządzanie plikami",
"UPLOADED_FILES"		=>	"Załadowane pliki",
"SHOW_ALL"					=>	"Pokaż wszystkie",
"VIEW_FILE"					=>	"Zobacz plik",
"DELETE_FILE"				=>	"Usuń plik",
"TOTAL_FILES"				=>	"- wszystkich plików",

/* 
 * For: logout.php
*/
"LOGGED_OUT"				=>	"Wylogowany",
"MSG_LOGGEDOUT"			=>	"Aktualnie jesteś wylogowany.",
"MSG_PLEASE"				=>	"Proszę zaloguj sie ponownie, aby mieć dostęp do konta", 

/* 
 * For: index.php
*/
"LOGIN"							=>	"Zaloguj się",
"USERNAME"					=>	"Użytkownik",
"PASSWORD"					=>	"Hasło",
"FORGOT_PWD"				=>	"Zpomniałeś hasło?",
"CONTROL_PANEL"			=>	"Panel kontrolny",
"LOGIN_REQUIREMENT"	=>	"Dostęp do panelu administracyjnego strony wymaga zalogowania się",
"WARN_JS_COOKIES"		=>	"Ciasteczka i javaskrypt muszą być w Twojej przeglądarce włączone, aby wszystko działało poprawnie",
"WARN_IE6"					=>	"Internet Explorer 6 może działać, ale nie jest wspomagany. Już czas! Zmień przeglądarke na nowszą",

/* 
 * For: navigation.php
*/
"CURRENT_MENU" 			=> 	"Obecne menu",
"NO_MENU_PAGES" 		=> 	"Nie ma stron ustawionych, aby pojawiać się w menu głównym",

/* 
 * For: theme-edit.php
*/
"TEMPLATE_FILE" 		=> 	"Pliki szablonu <b>%s</b> pomyślnie zaktualizowano!",
"THEME_MANAGEMENT" 	=> 	"Zarządzanie szablonem",
"EDIT_THEME" 				=> 	"Edytuj szablon",
"EDITING_FILE" 			=> 	"Edytowanie pliku",
"BTN_SAVECHANGES" 	=> 	"Zapisz zmiany",
"EDIT" 							=> 	"Edytuj",

/* 
 * For: support.php
*/
"SETTINGS_UPDATED"	=> 	"Ustawienia zostały zaktualizowane",
"UNDO" 							=> 	"Przywróć",
"SUPPORT" 					=> 	"Wsparcie",
"SETTINGS" 					=> 	"Ustawienia",
"ERROR" 						=> 	"Błąd",
"BTN_SAVESETTINGS" 	=> 	"Zapisz ustawienia",
"EMAIL_ON_404" 			=> 	" Email do administratora spowodu błędu 404",
"VIEW_404" 					=> 	"Zobacz błędy 404",
"VIEW_FAILED_LOGIN"	=> 	"Zobacz nieudane próby logowania",
"VIEW_CONTACT_FORM"	=> 	"Zobacz wysłane z formularza kontaktowego",
"VIEW_TICKETS" 			=> 	"Zobacz twoje otwarte listy",

/* 
 * For: log.php
*/
"MSG_HAS_BEEN_CLR" 	=> 	"zostało wyczyszczone",
"LOGS" 							=> 	"Logi",
"VIEWING" 					=> 	"Podgląd",
"LOG_FILE" 					=> 	"Log pliku",
"CLEAR_ALL_DATA" 		=> 	"Wyczyść wszystkie dane z",
"CLEAR_THIS_LOG" 		=> 	"Wyczyść ten log", // 'c' is the accesskey identifier
"LOG_FILE_ENTRY" 		=> 	"WPIS LOGU",
"THIS_COMPUTER"			=>	"Ten komputer",

/* 
 * For: backup-edit.php
*/
"BAK_MANAGEMENT"		=>	"Zarządzanie kopią zapasową i archiwum",
"ASK_CANCEL"				=>	"Zrezygnuj", // 'c' is the accesskey identifier
"ASK_RESTORE"				=>	"Przywróć", // 'r' is the accesskey identifier
"ASK_DELETE"				=>	"Usuń", // 'd' is the accesskey identifier
"BACKUP_OF"					=>	"Kopia",
"PAGE_TITLE"				=>	"Tytuł strony",
"YES"								=>	"Tak",
"NO"								=>	"Nie",
"DATE"							=>	"Data",

/* 
 * For: components.php
*/
"COMPONENTS"				=>	"Komponenty",
"DELETE_COMPONENT"	=>	"Usuń komponent",
"EDIT"							=>	"Edytuj",
"ADD_COMPONENT"			=>	"Dodaj komponent", // 'a' is the accesskey identifier
"SAVE_COMPONENTS"		=>	"Zapisz komponent",

/* 
 * For: sitemap.php
*/
"SITEMAP_CREATED"		=>	"Mapa strony utworzona!",
"SITEMAP_ERRORPING"	=>	"Mapa strony została utworzona, ale wystąpił błąd pingowania raz lub więcej do systemów wyszukiwania",
"SITEMAP_ERROR"			=>	"Mapa strony nie mogła zostać utworzona",
"SITEMAP_WAIT"			=>	"<b>Proszę czekać:</b> Tworzę mapę witryny",

/* 
 * For: theme.php
*/
"THEME_CHANGED"			=>	"Twój szablon został zmieniony",
"CHOOSE_THEME"			=>	"Wybierz szablon",
"ACTIVATE_THEME"		=>	"Aktywuj szablon",
"THEME_SCREENSHOT"	=>	"Obrazek szablonu",
"THEME_PATH"				=>	"Ścieżka do aktualnego szablonu",

/* 
 * For: resetpassword.php
*/
"RESET_PASSWORD"		=>	"Resetuj hasło",
"YOUR_NEW"					=>	"Twóje nowe",
"PASSWORD_IS"				=>	"hasło to",
"ATTEMPT"						=>	"Próba",
"MSG_PLEASE_EMAIL"	=>	" Proszę wprowadzić adres e-mail zarejestrowany w tym systemie, nowe hasło zostanie wysłane",
"SEND_NEW_PWD"			=>	"Wyślij nowe hasło",

/* 
 * For: settings.php
*/
"GENERAL_SETTINGS"	=>	"Ustawienia główne",
"WEBSITE_SETTINGS"	=>	"Ustawienia strony",
"LOCAL_TIMEZONE"		=>	"Czas lokalny",
"LANGUAGE"					=>	"Język",
"USE_FANCY_URLS"		=>	"<b>Użyj przyjaznych URLi</b> - Wymaga włączonego mod_rewrite twojego hostingu",
"ENABLE_HTML_ED"		=>	"<b>Włącz edytor HTML</b>",
"USER_SETTINGS"			=>	"Ustawienia logowania użytkownika",
"WARN_EMAILINVALID"	=>	"Ostrzeżenie: E-mail jest niepoprawny!",
"ONLY_NEW_PASSWORD"	=>	"Wprowadz poniżej hasło aby zmienić na nowe",
"NEW_PASSWORD"			=>	"Nowe hasło",
"CONFIRM_PASSWORD"	=>	"Potwierdź hasło",
"PASSWORD_NO_MATCH"	=>	"Hasła nie pasują do siebie",

/* 
 * For: contactform.php
*/
"MSG_CAPTCHA_FAILED"=>	"Captcha niepoprawna, sądzimy że jesteś spam botem",
"CONTACT_FORM_SUB"	=>	"Zgłoszenie z formularza kontaktowego",
"FROM"							=>	"od",
"MSG_CONTACTSUC"		=>	"Twój e-mail został pomyślnie wysłany",
"MSG_CONTACTERR"		=>	"Podczas wysyłania e-maila wystąpił błąd",

/* 
 * For: 404-mailer.php
*/
"404_ENCOUNTERED"		=>	"Auto: 404",
"404_AUTO_MSG"			=>	"To jest automatyczna wiadomość z Twojej strony",
"PAGE_CANNOT_FOUND"	=>	"'strona nie znaleziona' wystapił błąd",
"DOMAIN"						=>	"domena",
"DETAILS"						=>	"SZCZEGÓŁY",
"WHEN"							=>	"Kiedy",
"WHO"								=>	"Kto",
"FAILED_PAGE"				=>	"Niepoprawna strona",
"REFERRER"					=>	"Polecający",
"BROWSER"						=>	"Przeglądarka",

/* 
 * For: health-check.php
*/
"WEB_HEALTH_CHECK"	=>	"Stan działania strony",
"VERSION"						=>	"Wersja",
"UPG_NEEDED"				=>	"Potrzebna aktualizacja do",
"CANNOT_CHECK"			=>	"Nie można sprawdzić. Twoja wersja to",
"LATEST_VERSION"		=>	"Zainstalowana najnowsza wersja",
"SERVER_SETUP"			=>	"Ustawienia serwera",
"OR_GREATER_REQ"		=>	"lub nowsza jest wymagana",
"OK"								=>	"OK",
"INSTALLED"					=>	"Zainstalowana",
"NOT_INSTALLED"			=>	"Nie zainstalowana",
"WARNING"						=>	"Ostrzeżenie",
"DATA_FILE_CHECK"		=>	"Sprawdznie integralności plików danych",
"DIR_PERMISSIONS"		=>	"Prawa dostępu do katalogu",
"EXISTANCE"					=>	"%s - poprawność",
"MISSING_FILE"			=>	"Zagubiony plik",
"BAD_FILE"					=>	"Zły plik",
"NO_FILE"						=>	"Brak pliku",
"GOOD_D_FILE"				=>	"Good 'Deny' file",
"GOOD_A_FILE"				=>	"Good 'Allow' file",
"CANNOT_DEL_FILE"		=>	"Nie można usunąć pliku",
"DOWNLOAD"					=>	"Ściągnij",
"WRITABLE"					=>	"Zapisywalne",
"NOT_WRITABLE"			=>	"Nie zapisywalne",

/* 
 * For: footer.php
*/
"POWERED_BY"				=>	"Powered by",
"PRODUCTION"				=>	"Produkcja",
"SUBMIT_TICKET"			=>	"Zgłoś list (ticket)",

/* 
 * For: backups.php
*/
"PAGE_BACKUPS"			=>	"Kopia zapasowa stron",
"ASK_DELETE_ALL"		=>	"Usuń wszystkie",
"DELETE_ALL_BAK"		=>	"Usunąć wszystkie kopie zapasowe?",
"TOTAL_BACKUPS"			=>	"- wszystkich kopii zapasowych",

/* 
 * For: archive.php
*/
"SUCC_WEB_ARCHIVE"	=>	"Sukces. Archiwizacja strony www przebiegła poprawnie!",
"SUCC_WEB_ARC_DEL"	=>	"Archiwum strony usunięte",
"WEBSITE_ARCHIVES"	=>	"Archiwa strony www",
"ARCHIVE_DELETED"		=>	"Archiwum usunięte z sukcesem",
"CREATE_NEW_ARC"		=>	"Utwórz nowe archiwum",
"ASK_CREATE_ARC"		=>	"Utwórz TERAZ nowe archiwum",
"CREATE_ARC_WAIT"		=>	"<b>Proszę czekać:</b> Tworzę archiwum strony...",
"DOWNLOAD_ARCHIVES"	=>	"Ściągnij archiwum",
"DELETE_ARCHIVE"		=>	"Usuń archiwum",
"TOTAL_ARCHIVES"		=>	"- wszystkich archiwów",

/* 
 * For: include-nav.php
*/
"WELCOME"						=>	"Witamy", // used as 'Welcome USERNAME!'
"TAB_PAGES"					=>	"Strony",
"TAB_FILES"					=>	"Pliki",
"TAB_THEME"					=>	"Szablony",
"TAB_BACKUPS"				=>	"Kopia zapasowa i archiwum",
"TAB_SETTINGS"			=>	"Ustawienia",
"TAB_SUPPORT"				=>	"Wsparcie",
"TAB_LOGOUT"				=>	"Wyloguj",

/* 
 * For: sidebar-files.php
*/
"BROWSE_COMPUTER"		=>	"Pobierz z komputera",
"UPLOAD"						=>	"Załaduj",

/* 
 * For: sidebar-support.php
*/
"SIDE_SUPPORT_LOG"	=>	"Wsparcie ustawień i logów",
"SIDE_VIEW_LOG"			=>	"zobacz log",
"SIDE_HEALTH_CHK"		=>	"Stan działania strony",
"SIDE_SUBMIT_TICKET"=>	"Zgłoś list (ticket)",
"SIDE_DOCUMENTATION"=>	"Dokumentacja",

/* 
 * For: sidebar-theme.php
*/
"SIDE_VIEW_SITEMAP"	=>	"Zobacz mapę strony",
"SIDE_GEN_SITEMAP"	=>	"Generuj mapę strony",
"SIDE_COMPONENTS"		=>	"Edytuj komponenty",
"SIDE_EDIT_THEME"		=>	"Edytuj szablon",
"SIDE_CHOOSE_THEME"	=>	"Wybierz szablon",

/* 
 * For: sidebar-pages.php
*/
"SIDE_CREATE_NEW"		=>	"Utwórz nową stronę",
"SIDE_VIEW_PAGES"		=>	"Zobacz wszystkie strony",

/* 
 * For: sidebar-pages.php
*/
"SIDE_GEN_SETTINGS"	=>	"Ustawienia główne",
"SIDE_USER_PROFILE"	=>	"Profil użytkownika",

/* 
 * For: sidebar-pages.php
*/
"SIDE_VIEW_BAK"			=>	"Zobacz kopie zapasowe strony",
"SIDE_WEB_ARCHIVES"	=>	"Archiwum strony",
"SIDE_PAGE_BAK"			=>	"Kopia zapasowa stron",

/* 
 * For: error_checking.php
*/
"ER_PWD_CHANGE"			=>	"Nie zapomnij <a href=\"settings.php\">zmienić Twoje hasło</a> z losowego na takie co zapamiętasz...",
"ER_BAKUP_DELETED"	=>	"Kopia bezpieczeństwa została skasowana dla %s",
"ER_REQ_PROC_FAIL"	=>	"Niestety, zadanie niewykonalne",
"ER_YOUR_CHANGES"		=>	"Twoje zmiany %s zostały zapisane",
"ER_HASBEEN_REST"		=>	"%s zostało przywrócone",
"ER_HASBEEN_DEL"		=>	"%s zostało usunięte",
"ER_CANNOT_INDEX"		=>	"Nie można zmienić URL strony głównej (index)",
"ER_SETTINGS_UPD"		=>	"Twoje ustaienia zostały zaktualizowane",
"ER_OLD_RESTORED"		=>	"Twoje stare ustawienia zostały przywrócone",
"ER_NEW_PWD_SENT"		=>	"Nowe hasło zostało wysłane na e-mail podany w konfiguracji",
"ER_SENDMAIL_ERR"		=>	"Jest problem z wysłaniem e-maila. Proszę spróbuj ponownie",
"ER_FILE_DEL_SUC"		=>	"Plik pomyślnie skasowany",
"ER_PROBLEM_DEL"		=>	"Jest problem z usunięciem pliku",
"ER_COMPONENT_SAVE"	=>	"Twoje komponenty zostały zapisane",
"ER_COMPONENT_REST"	=>	"Twoje komponenty zostały przywrócone",
"ER_CANCELLED_FAIL"	=>	"<b>Rezygnacja:</b> Aktualizacja tego pliku została odwołana",

/* 
 * For: changedata.php
*/
"CANNOT_SAVE_EMPTY"	=>	"Nie można zapisać pustej strony",

/* 
 * For: template_functions.php
*/
"FTYPE_COMPRESSED"	=>	"Skompresowany", //a file-type
"FTYPE_VECTOR"			=>	"Wektorowy", //a file-type
"FTYPE_FLASH"				=>	"Flash", //a file-type
"FTYPE_VIDEO"				=>	"Video", //a file-type
"FTYPE_AUDIO"				=>	"Audio", //a file-type
"FTYPE_WEB"					=>	"Web", //a file-type
"FTYPE_DOCUMENTS"		=>	"Dokumenty", //a file-type
"FTYPE_SYSTEM"			=>	"System", //a file-type
"FTYPE_MISC"				=>	"Różne", //a file-type
"IMAGES"						=>	"Obrazki",

/* 
 * For: login_functions.php
*/
"FILL_IN_REQ_FIELD"	=>	"Proszę wypełnić wszystkie wymagane pola",
"LOGIN_FAILED"			=>	"Zły login lub hasło. Proszę ponownie sprawdzić Twój login i hasło"


);

?>