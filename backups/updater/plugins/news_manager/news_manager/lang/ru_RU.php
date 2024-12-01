<?php

/**
 * News Manager Russian language file - initial translation by reff http://reff.pp.ua/
   Updated thanks to Oleg06
 */


$i18n = array(

# general
"PLUGIN_NAME"         =>  "Редактор новостей",

# error messages
"ERROR_ENV"           =>  "Произошла ошибка доступа к папке записей и/или к конфигурационному файлу. Установите права <em>CHMOD 777</em> по отношению к папке /data, /backups и вложенным в нее подпапкам и повторите попытку.",
"ERROR_SAVE"          =>  "<b>Ошибка:</b> Невозможно сохранить запись. Установите права <em>CHMOD 777</em> по отношению к папке /data, /backups и вложенным в нее подпапкам и повторите попытку.",
"ERROR_DELETE"        =>  "<b>Ошибка:</b> Невозможно удалить запись. Установите права <em>CHMOD 777</em> по отношению к папке /data, /backups и вложенным в нее подпапкам и повторите попытку.",
"ERROR_RESTORE"       =>  "<b>Ошибка:</b> Не удается восстановить запись. Установите права <em>CHMOD 777</em> для папок /data, /backups и вложенные в них папки и повторите.",

# success messages
"SUCCESS_SAVE"        =>  "Запись была успешно сохранена.",
"SUCCESS_DELETE"      =>  "Запись была успешно удалена.",
"SUCCESS_RESTORE"     =>  "Сообщение было восстановлено.",

# other messages
"UPDATE_HTACCESS"     =>  "<b>Внимание:</b> Скопируйте код <a href=\"%s\">.htaccess</a> и замените его вручную!",

# admin button (top-right)
"NEWS_TAB"            =>  "Новости",
"SETTINGS"            =>  "Настройки",
"NEW_POST"            =>  "Создать новую запись",

# admin panel
"POST_TITLE"          =>  "Заголовок записи",
"DATE"                =>  "Дата",
"EDIT_POST"           =>  "Редактировать запись",
"VIEW_POST"           =>  "Просмотреть запись",
"DELETE_POST"         =>  "Удалить запись",
"POSTS"               =>  "всего записей",

# edit settings
"NM_SETTINGS"         =>  "Настройки редактора новостей",
"DOCUMENTATION"       =>  "Дополнительную информацию о настройках плагина можно найти на странице <a href=\"%s\" target=\"_blank\">документации</a>",
"PAGE_URL"            =>  "Адрес страницы новостной ленты",
"NO_PAGE_SELECTED"    =>  "Страница не выбрана",
"LANGUAGE"            =>  "Предпочитаемый язык",
"SHOW_POSTS_AS"       =>  "Режим отображения новостей",
"FULL_TEXT"           =>  "Полный текст",
"EXCERPT"             =>  "Отрывок",
"PRETTY_URLS"         =>  "Использовать ЧПУ (<a href='http://ru.wikipedia.org/wiki/%D0%A7%D0%9F%D0%A3_(%D0%98%D0%BD%D1%82%D0%B5%D1%80%D0%BD%D0%B5%D1%82)'>человеко-понятный</a> URL)",
"PRETTY_URLS_NOTE"    =>  "Если используется ЧПУ, измените вручную файл .htaccess",
"EXCERPT_LENGTH"      =>  "Длина отрывка новости (количество символов)",
"POSTS_PER_PAGE"      =>  "Количество новостей на странице новостной ленты",
"RECENT_POSTS"        =>  "Количество последних новостных записей в виджете боковой панели",
"ENABLE_ARCHIVES"     =>  "Включить архивы",
"BY_MONTH"            =>  "По месяцам",
"BY_YEAR"             =>  "По годам",
"READ_MORE_LINK"      =>  "Добавить ссылку \"Читать дальше\" к отрывку",
"ALWAYS"              =>  "Всегда",
"NOT_SINGLE"          =>  "Только в отрывке",
"GO_BACK_LINK"        =>  "Показывать ссылку \"Назад\" в конце полной новости",
"TITLE_LINK"          =>  "Ссылка на полную новость в заголовке",
"BROWSER_BACK"        =>  "На ранее посещенную страницу",
"MAIN_NEWS_PAGE"      =>  "На гланую страницу новостей",
"ENABLE_IMAGES"       =>  "Включать изображения в отрывки",
"IMAGE_LINKS"         =>  "Ссылка на изображение",
"IMAGE_WIDTH"         =>  "Ширина изображения (пиксели)",
"IMAGE_HEIGHT"        =>  "Высота изоюражения (пиксели)",
"FULL"                =>  "полный",
"IMAGE_CROP"          =>  "Обрезать изображение согласно размерам",
"IMAGE_ALT"           =>  "Включить заголовок новости в атрибут <em>alt</em>",
"CUSTOM_SETTINGS"     =>  "Пользовательские настройки",

# edit post
"POST_OPTIONS"        =>  "Настройки записи",
"POST_SLUG"           =>  "Slug/URL",
"POST_TAGS"           =>  "Тэг(и) - через запятую",
"POST_DATE"           =>  "Дата публикации (<i>гггг-мм-дд</i>)",
"POST_TIME"           =>  "Время публикации (<i>чч:мм</i>)",
"POST_PRIVATE"        =>  "Скрытая запись (черновик)",
"POST_IMAGE"          =>  "Изображение",
"LAST_SAVED"          =>  "Дата последнего сохранения",

# validation
"FIELD_IS_REQUIRED"   => "Это поле обязательно",
"ENTER_VALID_DATE"    => "Пожалуйста, укажите желаемую дату, либо оставьте пустым для отображения теущей даты",
"ENTER_VALID_TIME"    => "Пожалуйста, укажите желаемое время, либо оставьте пустым для отображения теущего времени",
"ENTER_VALUE_MIN"     => "Пожалуйста, введите число, большее или равное %d",

# date picker - first day of week (0=Sunday, 1=Monday, ... 6=Saturday)
"DAY_OF_WEEK_START"   => "1",

# htaccess
"HTACCESS_HELP"       =>  "Для включения ЧПУ (<a href='http://ru.wikipedia.org/wiki/%D0%A7%D0%9F%D0%A3_(%D0%98%D0%BD%D1%82%D0%B5%D1%80%D0%BD%D0%B5%D1%82)'>человеко-понятный</a> URL) замените содержимое файла <code>.htaccess</code> следующим текстом.",
"GO_BACK_WHEN_DONE"   =>  "Сохранить и вернуться.",

# save/cancel/delete
"SAVE_SETTINGS"       =>  "Сохранить настройки",
"SAVE_POST"           =>  "Сохранить новость",
"FINISHED"            =>  "Готово",
"CANCEL"              =>  "Отмена",
"DELETE"              =>  "Удалить",
"OR"                  =>  "или",

# front-end/site
"FOUND"               =>  "Были найдены следующие записи:",
"NOT_FOUND"           =>  "Ничего не найдено.",
"NOT_EXIST"           =>  "Запрошенная запись не найдена.",
"NO_POSTS"            =>  "Нет опубликованных записей.",
"PUBLISHED"           =>  "Опубликовано:",
"TAGS"                =>  "Теги",
"OLDER_POSTS"         =>  "&larr; Назад",
"NEWER_POSTS"         =>  "Вперед &rarr;",
"SEARCH"              =>  "Найти",
"GO_BACK"             =>  "&lt;&lt; Вернуться на предыдущую страницу",
"ELLIPSIS"            =>  " [...] ",
"READ_MORE"           =>  "Читать дальше",
"AUTHOR"              =>  "Автор:",
"PREV_TEXT"           =>  "&lt;",
"PREV_TITLE"          =>  "Предыдущая страница",
"NEXT_TEXT"           =>  "&gt;",
"NEXT_TITLE"          =>  "Следующая страница",

# language localization
"LOCALE"              =>  "ru_RU.utf8,ru.utf8,ru_RU.UTF-8,ru.UTF-8,ru_RU,ru",

# date settings - list of available parameters: http://php.net/strftime
"DATE_FORMAT"         =>  "%e %EB %Y",
"MONTHLY_FORMAT"      =>  "%B %Y",
"YEARLY_FORMAT"       =>  "%Y",

# custom month names
"MONTHLIST"           =>  "Январь,Февраль,Март,Апрель,Май,Июнь,Июль,Август,Сентябрь,Октябрь,Ноябрь,Декабрь",
"MONTHLIST_ALT"       =>  "января,февраля,марта,апреля,мая,июня,июля,августа,сентября,октября,ноября,декабря",

);
