<?php

/*****************************************************************
 * News Manager Czech language file by Tomáš Janeček / TeeJay    *
 *                                     http://tomasjanecek.cz    *                                                             *
 *****************************************************************/                                      
 
$i18n = array(

# general
"PLUGIN_NAME"         =>  "Správce článků",

# error messages
"ERROR_ENV"           =>  "Chyba v přístupu do adresářů dat. Nastavte <em>CHMOD 777</em> na složky /data, /backups a jejich podsložky a akci opakujte.",
"ERROR_SAVE"          =>  "<b>Error:</b> Nelze uložit vaše změny. Nastavte <em>CHMOD 777</em> na složky /data, /backups a jejich podsložky a akci opakujte.",
"ERROR_DELETE"        =>  "<b>Error:</b> Článek nelze smazat. Nastavte <em>CHMOD 777</em> na složky /data, /backups a jejich podsložky a akci opakujte.",
"ERROR_RESTORE"       =>  "<b>Error:</b> Článek nelze obnovit. Nastavte <em>CHMOD 777</em> na složky /data, /backups a jejich podsložky a akci opakujte.",

# success messages
"SUCCESS_SAVE"        =>  "Vaše změny byly uloženy.",
"SUCCESS_DELETE"      =>  "Článek byl smazán.",
"SUCCESS_RESTORE"     =>  "článek byl obnoven.",

# other messages
"UPDATE_HTACCESS"     =>  "<b>Poznámka:</b> Pravděpodobně musíte zaktualizovat soubor <a href=\"%s\">.htaccess</a>!",

# admin button (top-right)
"NEWS_TAB"            =>  "Články",
"SETTINGS"            =>  "Nastavení",
"NEW_POST"            =>  "Napsat článek",

# admin panel
"POST_TITLE"          =>  "Nadpis článku",
"DATE"                =>  "Datum",
"EDIT_POST"           =>  "Upravit článek",
"VIEW_POST"           =>  "Náhled článku",
"DELETE_POST"         =>  "Smazat článek",
"POSTS"               =>  "příspěvek/ky",

# edit settings
"NM_SETTINGS"         =>  "Nastavení Správce novinek",
"DOCUMENTATION"       =>  "Pro více informaci o těchto nastaveních navštivte <a href=\"%s\" target=\"_blank\">documentation page</a>.",
"PAGE_URL"            =>  "Stránka pro výpis článků",
"NO_PAGE_SELECTED"    =>  "Nebyla vybrána žádná stránka",
"LANGUAGE"            =>  "Jazyk použitý na stránkách článků",
"SHOW_POSTS_AS"       =>  "Články na stránce s novinkami jsou zobrazeny jako",
"FULL_TEXT"           =>  "Celý text",
"EXCERPT"             =>  "Úvodní text",
"PRETTY_URLS"         =>  "Použít Fancy URLs na články, archivy, atd.",
"PRETTY_URLS_NOTE"    =>  "Máte-li Fancy URLs povolené, možna budete muset zaktualizovat váš .htaccess soubor po uložení těchto nastavení.",
"EXCERPT_LENGTH"      =>  "Délka úvodního textu (počet znaků)",
"POSTS_PER_PAGE"      =>  "Počet článků na stránce s články",
"RECENT_POSTS"        =>  "Počet nedávných článků (v postranní liště)",
"ENABLE_ARCHIVES"     =>  "Povolit archivy",
"BY_MONTH"            =>  "Podle měsíce",
"BY_YEAR"             =>  "Podle roku",
"READ_MORE_LINK"      =>  "Přidat odkaz \"Číst dál...\" k úvodníkům",
"ALWAYS"              =>  "Vždy",
"NOT_SINGLE"          =>  "Kromě zobrazení celého článku",
"GO_BACK_LINK"        =>  "Odkaz \"Zpět\" pod jednotlivými články",
"TITLE_LINK"          =>  "Odkaz na článek v nadpise článku",
"BROWSER_BACK"        =>  "Předchozí navštívená stránka",
"MAIN_NEWS_PAGE"      =>  "Stránka s výpisem článků",
"ENABLE_IMAGES"       =>  "Povolit obrázky článků",
"IMAGE_LINKS"         =>  "Odkaz na články v obrázku",
"IMAGE_WIDTH"         =>  "Šírka obrázků článků (v pixelech)",
"IMAGE_HEIGHT"        =>  "Výška obrázků článků (v pixelech)",
"FULL"                =>  "celý",
"IMAGE_CROP"          =>  "Oříznou obrázky článků pro zachování poměru šířka/výška",
"IMAGE_ALT"           =>  "Vložit text nadpisu článku do <em>alt</em> atributu obrázku článku",
"CUSTOM_SETTINGS"     =>  "Vlastní nastavení",

# edit post
"POST_OPTIONS"        =>  "Nastavení článku",
"POST_SLUG"           =>  "Slug/URL",
"POST_TAGS"           =>  "Tagy (jednotlivé tagy oddělujte čárkou)",
"POST_DATE"           =>  "Datum publikování (<i>yyyy-mm-dd</i>)",
"POST_TIME"           =>  "Čas publikování (<i>hh:mm</i>)",
"POST_PRIVATE"        =>  "Článek je soukromý",
"POST_IMAGE"          =>  "Obrázek",
"LAST_SAVED"          =>  "Naposledy uloženo",

# validation
"FIELD_IS_REQUIRED"   => "Toto pole je nutné vyplnit",
"ENTER_VALID_DATE"    => "Prosím zadejte správné datum / Nechte prázdné pro dnešní datum",
"ENTER_VALID_TIME"    => "Prosím zadejte správný čas / Nechte prázdné pro aktuální čas",
"ENTER_VALUE_MIN"     => "Prosím zadejte hodnotu větší nebo rovnu %d",

# date picker - first day of week (0=Sunday, 1=Monday, ... 6=Saturday)
"DAY_OF_WEEK_START"   => "1",

# htaccess
"HTACCESS_HELP"       =>  "Abyste povolili Fancy URLs pro články, archivy, atd., nahraďte obsah vašeho <code>.htaccess</code> souboru řádky níže.",
"GO_BACK_WHEN_DONE"   =>  "Až budete s touto stránkou hotovi, klikněte na tlačítko níže pro navrácení na hlavní panel.",

# save/cancel/delete
"SAVE_SETTINGS"       =>  "Uložit nastavení",
"SAVE_POST"           =>  "Uložit článek",
"FINISHED"            =>  "Hotovo",
"CANCEL"              =>  "Zrušit",
"DELETE"              =>  "Smazat",
"OR"                  =>  "nebo",

# front-end/site
"FOUND"               =>  "Byly nalezeny následující články:",
"NOT_FOUND"           =>  "Omlouváme se, ale vaše vyhledávání nevede k žádným výsledkům.",
"NOT_EXIST"           =>  "Požadovaný článek neexistuje.",
"NO_POSTS"            =>  "Nenalezeny žádné články.",
"PUBLISHED"           =>  "Publikováno",
"TAGS"                =>  "Tagy",
"OLDER_POSTS"         =>  "&larr; Starší články",
"NEWER_POSTS"         =>  "Novější články &rarr;",
"SEARCH"              =>  "Hledat",
"GO_BACK"             =>  "&lt;&lt; Zpět na předchozí stránku",
"ELLIPSIS"            =>  " [...] ",
"READ_MORE"           =>  "Číst dál...",
"AUTHOR"              =>  "Autor:",
"PREV_TEXT"           =>  "&lt;",
"PREV_TITLE"          =>  "Předchozí stránka",
"NEXT_TEXT"           =>  "&gt;",
"NEXT_TITLE"          =>  "Další stránka",

# language localization
"LOCALE"              =>  "cs_CZ.utf8,cs.utf8,cs_CZ.UTF-8,cs.UTF-8,cs_CZ,cs",

# date settings - list of available parameters: http://php.net/strftime
"DATE_FORMAT"         =>  "%d.%m.%Y - %H:%M",
"MONTHLY_FORMAT"      =>  "%B %Y",
"YEARLY_FORMAT"       =>  "%Y"

);
