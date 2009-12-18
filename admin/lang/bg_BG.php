<?php
/****************************************************
*
* @File: 				bg_BG.php
* @Package:			GetSimple
* @Subject:			BG Bulgarian language file
* @Date:				18 Sept 2009
* @Revision:		22 Sept 2009
* @Version:			GetSimple 1.7
* @Status:			Final
* @Traductors: 	Evtimii Mihailov (admin@loading.biz)
*
*****************************************************/

$i18n = array(

/* 
 * For: install.php
*/
"PHPVER_ERROR"			=>	"<b>Не можете да провължите:</b> PHP 5.1.3 или по-нов е необходим, Вие разполагате ",
"SIMPLEXML_ERROR"		=>	"<b>Не можете да провължите:</b> <em>SimpleXML</em> не е инсталиран",
"CURL_WARNING"			=>	"<b>Внимание:</b> <em>cURL</em> Не е инсталиран",
"TZ_WARNING"				=>	"<b>Внимание:</b> <em>date_default_timezone_set</em> липсва",
"WEBSITENAME_ERROR"	=>	"<b>Грешка:</b> Възникна проблем със заглавието на Вашия уебсайт",
"WEBSITEURL_ERROR"	=>	"<b>Грешка:</b> Възникна проблем с URL-а на Вашия уебсайт",
"USERNAME_ERROR"		=>	"<b>Грешка:</b> Потребителското име не беше зададено",
"EMAIL_ERROR"				=>	"<b>Грешка:</b> Възникна проблем с Вашия e-mail адрес",
"CHMOD_ERROR"				=>	"<b>Не можете да провължите:</b> Не може да се записва на конфигурационния файл. <em>CHMOD 777</em> на папките /data/ и /backups/ и опитайте отново",
"EMAIL_COMPLETE"		=>	"Настройката завърши",
"EMAIL_USERNAME"		=>	"Вашето потребителско име е",
"EMAIL_PASSWORD"		=>	"Вашата нова парола е",
"EMAIL_LOGIN"				=>	"Влезте тук",
"EMAIL_THANKYOU"		=>	"Благодарим Ви, че използвате",
"NOTE_REGISTRATION"	=>	"Вашата регистрационна информация беше изпратена на",
"NOTE_REGERROR"			=>	"<b>Грешка:</b> Възникна проблем при изпращането на регистрационната информация чрез e-mail. Моля запишете паролата посочена по-долу",
"NOTE_USERNAME"			=>	"Вашето потребителско име е ",
"NOTE_PASSWORD"			=>	"и вашата парола",
"INSTALLATION"			=>	"Инсталация",
"LABEL_WEBSITE"			=>	"Име на сайта",
"LABEL_BASEURL"			=>	"Базов URL адрес на сайта",
"LABEL_SUGGESTION"	=>	"Нашата препоръка е ",
"LABEL_USERNAME"		=>	"Име на потребител",
"LABEL_EMAIL"				=>	"Email Адрес",
"LABEL_INSTALL"			=>	"Инсталирай сега!",

/* 
 * For: pages.php
*/
"MENUITEM_SUBTITLE"	=>	"елемент от менюто",
"HOMEPAGE_SUBTITLE"	=>	"начална страница",
"PRIVATE_SUBTITLE"	=>	"лично",
"EDITPAGE_TITLE"		=>	"Редактирай Страница",
"VIEWPAGE_TITLE"		=>	"Прегледай Страница",
"DELETEPAGE_TITLE"	=>	"Изтрий Страница",
"PAGE_MANAGEMENT"		=>	"Управление на Страниците",
"TOGGLE_STATUS"			=>	"промяна на статус",
"TOTAL_PAGES"				=>	"страници(а) общо",
"ALL_PAGES"					=>	"Всички страници",

/* 
 * For: edit.php
*/
"PAGE_NOTEXIST"			=>	"Поисканата страница не съществува",
"BTN_SAVEPAGE"			=>	"Запази Страница",
"BTN_SAVEUPDATES"		=>	"Запиши Актуализация",
"DEFAULT_TEMPLATE"	=>	"Template по подразбиране",
"NONE"							=>	"Няма",
"PAGE"							=>	"Страница",
"NEW_PAGE"					=>	"Нова Страница",
"PAGE_EDIT_MODE"		=>	"Режим на Редактиране на Страницата",
"CREATE_NEW_PAGE"		=>	"Създай Нова Страница",
"VIEW"							=>	"<em>И</em>зглед", // 'v' is the accesskey identifier
"PAGE_OPTIONS"			=>	"<em>О</em>пции", // 'o' is the accesskey identifier
"TOGGLE_EDITOR"			=>	"Пр<em>е</em>включи Редактор", // 'g' is the accesskey identifier
"SLUG_URL"					=>	"Кратък URL",
"TAG_KEYWORDS"			=>	"Тагове &amp; Ключови думи",
"PARENT_PAGE"				=>	"Предходна страница",
"TEMPLATE"					=>	"Template",
"KEEP_PRIVATE"			=>	"Запази лично?",
"ADD_TO_MENU"				=>	"Добави към Менюто",
"PRIORITY"					=>	"Приоритет",
"MENU_TEXT"					=>	"Меню Текст",
"LABEL_PAGEBODY"		=>	"Тяло на Страницата",
"CANCEL"						=>	"Отказ",
"BACKUP_AVAILABLE"	=>	"Наличие на резервно копие",
"MAX_FILE_SIZE"			=>	"Максимална големина на файла",
"LAST_SAVED"				=>	"Последно запазен",
"FILE_UPLOAD"				=>	"Качване на Файл",
"OR"								=>	"или",

/* 
 * For: upload.php
*/
"ERROR_UPLOAD"			=>	"Възникна проблем при качването на файла",
"FILE_SUCCESS_MSG"	=>	"Поздравления! Местоположение на Файла",
"FILE_MANAGEMENT"		=>	"Управление на Файлове",
"UPLOADED_FILES"		=>	"Качени файлове",
"SHOW_ALL"					=>	"Покажи Всички",
"VIEW_FILE"					=>	"Прегледай Файл",
"DELETE_FILE"				=>	"Изтрий Файл",
"TOTAL_FILES"				=>	"Общо файлове",

/* 
 * For: logout.php
*/
"LOGGED_OUT"				=>	"Излязохте",
"MSG_LOGGEDOUT"			=>	"Вие вече излязохте от системата.",
"MSG_PLEASE"				=>	"Моля влезте отново ако отново имате нужда от достъп до своя акаунт.", 

/* 
 * For: index.php
*/
"LOGIN"							=>	"Вход",
"USERNAME"					=>	"Потребителско име",
"PASSWORD"					=>	"Парола",
"FORGOT_PWD"				=>	"Забравена парола?",
"CONTROL_PANEL"			=>	"Контролен Панел",
"LOGIN_REQUIREMENT"	=>	"Изисквания за вход",
"WARN_JS_COOKIES"		=>	"Cookies и javascript трябва да са активирани във Вашия браузър за нормална работа",
"WARN_IE6"					=>	"Internet Explorer 6 може да работи, но не се поддържа",

/* 
 * For: navigation.php
*/
"CURRENT_MENU" 			=> 	"Текущо Меню",
"NO_MENU_PAGES" 		=> 	"Няма страници, които са настроени да се появяват в главното меню",

/* 
 * For: theme-edit.php
*/
"TEMPLATE_FILE" 		=> 	"Template файла<b>%s</b> беше обновен успешно!",
"THEME_MANAGEMENT" 	=> 	"Управление на темата",
"EDIT_THEME" 				=> 	"Редактирай Тема",
"EDITING_FILE" 			=> 	"Редактиране на Файла",
"BTN_SAVECHANGES" 	=> 	"Запази промените",
"EDIT" 							=> 	"Редактирай",

/* 
 * For: support.php
*/
"SETTINGS_UPDATED"	=> 	"Вашите настройки бяха обновени",
"UNDO" 							=> 	"Отмени",
"SUPPORT" 					=> 	"Поддръжка",
"SETTINGS" 					=> 	"Настройки",
"ERROR" 						=> 	"Грешка",
"BTN_SAVESETTINGS" 	=> 	"Запази Настройки",
"EMAIL_ON_404" 			=> 	"Изпращай email на администратора при грешка 404",
"VIEW_404" 					=> 	"Преглед на грешки 404",
"VIEW_FAILED_LOGIN"	=> 	"Преглед на неуспешни опити за вход в системата",
"VIEW_CONTACT_FORM"	=> 	"Преглед на форма за обратна връзка",
"VIEW_TICKETS" 			=> 	"Преглед на Вашите изпратени билети",

/* 
 * For: log.php
*/
"MSG_HAS_BEEN_CLR" 	=> 	" беше изчистено",
"LOGS" 							=> 	"Логове",
"VIEWING" 					=> 	"Преглед на",
"LOG_FILE" 					=> 	"лог файл",
"CLEAR_ALL_DATA" 		=> 	"Изчисти всички данни от",
"CLEAR_THIS_LOG" 		=> 	"<em>И</em>зчисти Този Лог", // 'c' is the accesskey identifier
"LOG_FILE_ENTRY" 		=> 	"Запис в лог файл",
"THIS_COMPUTER"			=>	"Този компютър",

/* 
 * For: backup-edit.php
*/
"BAK_MANAGEMENT"		=>	"Backup Управление",
"ASK_CANCEL"				=>	"<em>О</em>тказ", // 'c' is the accesskey identifier
"ASK_RESTORE"				=>	"<em>В</em>ъзстанови", // 'r' is the accesskey identifier
"ASK_DELETE"				=>	"<em>И</em>зтрий", // 'd' is the accesskey identifier
"BACKUP_OF"					=>	"Резервен фаил на",
"PAGE_TITLE"				=>	"Заглавие на Страница",
"YES"								=>	"Да",
"NO"								=>	"Не",
"DATE"							=>	"Дата",

/* 
 * For: components.php
*/
"COMPONENTS"				=>	"Компоненти",
"DELETE_COMPONENT"	=>	"Изтрий Компонент",
"EDIT"							=>	"Редактирай",
"ADD_COMPONENT"			=>	"<em>Д</em>обави Компонент", // 'a' is the accesskey identifier
"SAVE_COMPONENTS"		=>	"Запиши Компонентите",

/* 
 * For: sitemap.php
*/
"SITEMAP_CREATED"		=>	"Картата на сайта създадена! Също така, пингнахме успешно 4 търсещи машини за обновлението!",
"SITEMAP_ERRORPING"	=>	"Картата на сайта създадена, но възникна грешка при пингването на една или повече от търсещите машини",
"SITEMAP_ERROR"			=>	"Вашата карта на сайта не може да бъде генерирана",
"SITEMAP_WAIT"			=>	"<b>Моля изчакайте:</b> Създаване на карта на сайта",

/* 
 * For: theme.php
*/
"THEME_CHANGED"			=>	"Вашата тема беше променена успешно",
"CHOOSE_THEME"			=>	"Избери Тема",
"ACTIVATE_THEME"		=>	"Активирай Тема",
"THEME_SCREENSHOT"	=>	"Скрийншот на Темата",
"THEME_PATH"				=>	"Път до настоящата тема",

/* 
 * For: resetpassword.php
*/
"RESET_PASSWORD"		=>	"Рестартирай Парола",
"YOUR_NEW"					=>	"Вашата нова",
"PASSWORD_IS"				=>	"парола е",
"ATTEMPT"						=>	"Опит",
"MSG_PLEASE_EMAIL"	=>	"Моля въведете email адреса регистриран в тази система и ще Ви изпратим нова парола",
"SEND_NEW_PWD"			=>	"Изпрати нова парола",

/* 
 * For: settings.php
*/
"GENERAL_SETTINGS"	=>	"Общи Настройки",
"WEBSITE_SETTINGS"	=>	"Настройки на уебсайта",
"LOCAL_TIMEZONE"		=>	"Локална часова зона",
"LANGUAGE"					=>	"Език",
"USE_FANCY_URLS"		=>	"<b>Използвай Fancy URLs</b> - изисква сървърът Ви да е с включен mod_rewritе",
"ENABLE_HTML_ED"		=>	"<b>Включи HTML редактора</b>",
"USER_SETTINGS"			=>	"Потребителски настройки",
"WARN_EMAILINVALID"	=>	"ВНИМАНИЕ: Този email адрес изглежда не е валиден!",
"ONLY_NEW_PASSWORD"	=>	"Посочете парола само ако желаете да промените текущата",
"NEW_PASSWORD"			=>	"Нова Парола",
"CONFIRM_PASSWORD"	=>	"Потвърди Парола",
"PASSWORD_NO_MATCH"	=>	"Паролите не съвпадат",

/* 
 * For: contactform.php
*/
"MSG_CAPTCHA_FAILED"=>	"Captcha failed, предполагаме, че сте спам бот",
"CONTACT_FORM_SUB"	=>	"Contact Form Submission",
"FROM"							=>	"from",
"MSG_CONTACTSUC"		=>	"Вашият email беше изпратен успешно",
"MSG_CONTACTERR"		=>	"Възникна грешка при изпращането на Вашият email",

/* 
 * For: 404-mailer.php
*/
"404_ENCOUNTERED"		=>	"Auto: 404 Error Encountered on",
"404_AUTO_MSG"			=>	"Това е автоматично съобщение от Вашия уебсайт",
"PAGE_CANNOT_FOUND"	=>	"Възникна грешка 'страницата не беше намерена' ",
"DOMAIN"						=>	"домейн",
"DETAILS"						=>	"ДЕТАЙЛИ",
"WHEN"							=>	"Кога",
"WHO"								=>	"Кой",
"FAILED_PAGE"				=>	"Неуспешна Страница",
"REFERRER"					=>	"Referrer",
"BROWSER"						=>	"Браузър",

/* 
 * For: health-check.php
*/
"WEB_HEALTH_CHECK"	=>	"Проверка на здравето на сайта",
"VERSION"						=>	"Версия",
"UPG_NEEDED"				=>	"Необходимо е обновяване до",
"CANNOT_CHECK"			=>	"Невъзможност за проверка. Вашата версия е",
"LATEST_VERSION"		=>	"Инсталирана е последната налична версия",
"SERVER_SETUP"			=>	"Настройка на Сървъра",
"OR_GREATER_REQ"		=>	"или по-добра е необходима",
"OK"								=>	"ОК",
"INSTALLED"					=>	"Инсталиран",
"NOT_INSTALLED"			=>	"Не е инсталиран",
"WARNING"						=>	"Внимание",
"DATA_FILE_CHECK"		=>	"Проверка на цялостта на информационните файлове",
"DIR_PERMISSIONS"		=>	"Directory Permissions",
"EXISTANCE"					=>	"%s Наличие",
"MISSING_FILE"			=>	"Липсващ файл",
"BAD_FILE"					=>	"Повреден Файл",
"NO_FILE"						=>	"Няма файл",
"GOOD_D_FILE"				=>	"Good 'Deny' file",
"GOOD_A_FILE"				=>	"Good 'Allow' file",
"CANNOT_DEL_FILE"		=>	"Невъзможност да се изтрие файла",
"DOWNLOAD"					=>	"Сваляне",
"WRITABLE"					=>	"С разрешение за запис",
"NOT_WRITABLE"			=>	"Без разрешение за запис",

/* 
 * For: footer.php
*/
"POWERED_BY"				=>	"Задвижван от",
"PRODUCTION"				=>	"Production",
"SUBMIT_TICKET"			=>	"Submit Ticket",

/* 
 * For: backups.php
*/
"PAGE_BACKUPS"			=>	"Резервни копия на страници",
"ASK_DELETE_ALL"		=>	"<em>И</em>зтрий Всички",
"DELETE_ALL_BAK"		=>	"Изтрий всички резервни копия?",
"TOTAL_BACKUPS"			=>	"резервни(о) копия(е) общо",

/* 
 * For: archive.php
*/
"SUCC_WEB_ARCHIVE"	=>	"Успешен уебсайт архив!",
"SUCC_WEB_ARC_DEL"	=>	"Архива на сайта беше изтрит успешно",
"WEBSITE_ARCHIVES"	=>	"Архиви на уебсайта",
"ARCHIVE_DELETED"		=>	"Архива беше изтрит успешно",
"CREATE_NEW_ARC"		=>	"Създайте Нов Архив",
"ASK_CREATE_ARC"		=>	"<em>С</em>ъздайте Нов Архив Сега",
"CREATE_ARC_WAIT"		=>	"<b>Моля изчакайте:</b> Създаване на архив на сайта...",
"DOWNLOAD_ARCHIVES"	=>	"Свали Архив",
"DELETE_ARCHIVE"		=>	"Изтрий Архив",
"TOTAL_ARCHIVES"		=>	"архив(и) общо",

/* 
 * For: include-nav.php
*/
"WELCOME"						=>	"Добре дошъл(а)", // used as 'Welcome USERNAME!'
"TAB_PAGES"					=>	"<em>С</em>траници",
"TAB_FILES"					=>	"<em>Ф</em>айлове",
"TAB_THEME"					=>	"<em>Т</em>ема",
"TAB_BACKUPS"				=>	"<em>Р</em>езервни копия",
"TAB_SETTINGS"			=>	"<em>Н</em>астройки",
"TAB_SUPPORT"				=>	"Подд<em>р</em>ъжка",
"TAB_LOGOUT"				=>	"<em>И</em>зход",

/* 
 * For: sidebar-files.php
*/
"BROWSE_COMPUTER"		=>	"Избор от Вашия компютър",
"UPLOAD"						=>	"Качване",

/* 
 * For: sidebar-support.php
*/
"SIDE_SUPPORT_LOG"	=>	"Подд<em>р</em>ъжка Настройки &amp; Логове",
"SIDE_VIEW_LOG"			=>	"Преглед на Логовете",
"SIDE_HEALTH_CHK"		=>	"Уебсайт <em>Т</em>ехническа Проверка",
"SIDE_SUBMIT_TICKET"=>	"Submit Tic<em>k</em>et",
"SIDE_DOCUMENTATION"=>	"<em>Д</em>окументация",

/* 
 * For: sidebar-theme.php
*/
"SIDE_VIEW_SITEMAP"	=>	"<em>П</em>регледай Карта на Сайта",
"SIDE_GEN_SITEMAP"	=>	"<em>Г</em>енерирай Карта на Сайта",
"SIDE_COMPONENTS"		=>	"<em>Р</em>едактирай Компоненти",
"SIDE_EDIT_THEME"		=>	"Редактирай <em>Т</em>емата",
"SIDE_CHOOSE_THEME"	=>	"Изберете <em>Т</em>ема",

/* 
 * For: sidebar-pages.php
*/
"SIDE_CREATE_NEW"		=>	"<em>С</em>ъздайте Нова Страница",
"SIDE_VIEW_PAGES"		=>	"Изглед на всички <em>С</em>траници",

/* 
 * For: sidebar-pages.php
*/
"SIDE_GEN_SETTINGS"	=>	"Общи <em>Н</em>астройки",
"SIDE_USER_PROFILE"	=>	"<em>П</em>отребителски Профил",

/* 
 * For: sidebar-pages.php
*/
"SIDE_VIEW_BAK"			=>	"Преглед на резервните копия на страници",
"SIDE_WEB_ARCHIVES"	=>	"<em>У</em>ебсайт Архиви",
"SIDE_PAGE_BAK"			=>	"Резервни копия на <em>С</em>траници",

/* 
 * For: error_checking.php
*/
"ER_PWD_CHANGE"			=>	"Не забравяйте да <a href=\"settings.php#profile\">промените паролата си</a> от случайно генерираната, която имате сега...",
"ER_BAKUP_DELETED"	=>	"Резервното копие беше изтрито за %s",
"ER_REQ_PROC_FAIL"	=>	"Поисканият процес не беше изпълнен",
"ER_YOUR_CHANGES"		=>	"Вашите промени на %s бяха запазени",
"ER_HASBEEN_REST"		=>	"%s беше възстановен",
"ER_HASBEEN_DEL"		=>	"%s беше изтрит",
"ER_CANNOT_INDEX"		=>	"Не можете да промените URL на началната страница",
"ER_SETTINGS_UPD"		=>	"Вашите настройки бяха обновени",
"ER_OLD_RESTORED"		=>	"Вашите стари настройки бяха възстановени",
"ER_NEW_PWD_SENT"		=>	"На посочения от Вас email адрес беше изпратена нова парола",
"ER_SENDMAIL_ERR"		=>	"Възникна проблем при изпращането на email-а. Моля опитайте отново",
"ER_FILE_DEL_SUC"		=>	"Файлът беше изтрит успешно",
"ER_PROBLEM_DEL"		=>	"Възникна проблем при изтриването на файла",
"ER_COMPONENT_SAVE"	=>	"Вашите компоненти бяха запазени",
"ER_COMPONENT_REST"	=>	"Вашите компоненти бяха възстановени",
"ER_CANCELLED_FAIL"	=>	"<b>Отказ:</b> Промяната на този файл беше отменена",

/* 
 * For: changedata.php
*/
"CANNOT_SAVE_EMPTY"	=>	"Не можете да запазите празна страница",

/* 
 * For: template_functions.php
*/
"FTYPE_COMPRESSED"	=>	"Компресиран", //a file-type
"FTYPE_VECTOR"			=>	"Векторен", //a file-type
"FTYPE_FLASH"				=>	"Flash", //a file-type
"FTYPE_VIDEO"				=>	"Видео", //a file-type
"FTYPE_AUDIO"				=>	"Аудио", //a file-type
"FTYPE_WEB"					=>	"Web", //a file-type
"FTYPE_DOCUMENTS"		=>	"Документи", //a file-type
"FTYPE_SYSTEM"			=>	"Състемен", //a file-type
"FTYPE_MISC"				=>	"Друго", //a file-type
"IMAGES"						=>	"Изображения",

/* 
 * For: login_functions.php
*/
"FILL_IN_REQ_FIELD"	=>	"Моля попълнете всички полета които се изискват",
"LOGIN_FAILED"			=>	"Невалитен вход. Моля проверете отново своето Потребителско име и Парола",

/* 
 * For: Date Format
*/
"DATE_FORMAT"				=>	"M j, Y" //please keep short


);

?>