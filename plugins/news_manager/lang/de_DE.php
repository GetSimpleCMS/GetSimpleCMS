<?php

/**
 * News Manager German language file by Markus Weimar
 */


$i18n = array(

# general
"PLUGIN_NAME"         =>  "News Manager",

# error messages
"ERROR_ENV"           =>  "<b>Fehler:</b> Der Zugriff auf die Daten-Ordner wurde verweigert. Passen Sie bitte die Dateirechte der Ordner /data, /backups sowie deren Unterordner an (<em>chmod 777</em>) und versuchen Sie es erneut.",
"ERROR_SAVE"          =>  "<b>Fehler:</b> Die Änderungen konnten nicht gespeichert werden. Passen Sie bitte die Dateirechte der Ordner /data, /backups sowie deren Unterordner an (<em>chmod 777</em>) und versuchen Sie es erneut.",
"ERROR_DELETE"        =>  "<b>Fehler:</b> Der Beitrag konnte nicht gelöscht werden. Passen Sie bitte die Dateirechte der Ordner /data, /backups sowie deren Unterordner an (<em>chmod 777</em>) und versuchen Sie es erneut.",
"ERROR_RESTORE"       =>  "<b>Fehler:</b> Der Beitrag konnte nicht wiederhergestellt werden. Passen Sie bitte die Dateirechte für die Ordner /data, /backups sowie deren Unterordner an (<em>chmod 777</em>) und versuchen Sie es erneut.",

# success messages
"SUCCESS_SAVE"        =>  "Änderungen gespeichert.",
"SUCCESS_DELETE"      =>  "Beitrag gelöscht.",
"SUCCESS_RESTORE"     =>  "Beitrag wiederhergestellt.",

# other messages
"UPDATE_HTACCESS"     =>  "<b>Hinweis:</b> Sie müssen wahrscheinlich die Datei <a href=\"%s\">.htaccess</a> aktualisieren!",

# admin button (top-right)
"NEWS_TAB"            =>  "Beiträge",
"SETTINGS"            =>  "Einstellungen",
"NEW_POST"            =>  "Neuen Beitrag erstellen",

# admin panel
"POST_TITLE"          =>  "Beitragstitel",
"DATE"                =>  "Datum",
"EDIT_POST"           =>  "Beitrag bearbeiten",
"VIEW_POST"           =>  "Beitrag ansehen",
"DELETE_POST"         =>  "Beitrag löschen",
"POSTS"               =>  "Beiträge",

# edit settings
"NM_SETTINGS"         =>  "News Manager-Einstellungen",
"DOCUMENTATION"       =>  "Mehr Informationen zu den Einstellungen erhalten Sie in der <a href=\"%s\" target=\"_blank\">Dokumentation</a>.",
"PAGE_URL"            =>  "Beiträge auf folgender Seite anzeigen",
"NO_PAGE_SELECTED"    =>  "Keine Seite ausgewählt",
"LANGUAGE"            =>  "Sprache auf der News-Seite",
"SHOW_POSTS_AS"       =>  "Beiträge auf der News-Seite anzeigen als",
"FULL_TEXT"           =>  "Volltext",
"EXCERPT"             =>  "Auszug",
"PRETTY_URLS"         =>  "Fancy-URLs aktivieren für Beiträge, Archive etc.",
"PRETTY_URLS_NOTE"    =>  "Wenn Fancy-URLs aktiviert sind, müssen Sie möglicherweise die Datei .htaccess nach dem Speichern dieser Einstellungen aktualisieren.",
"EXCERPT_LENGTH"      =>  "Länge des Auszugs (Zeichen)",
"POSTS_PER_PAGE"      =>  "Anzahl der Beiträge auf der News-Seite",
"RECENT_POSTS"        =>  "Anzahl der aktuellen Beiträge (in der Seitenleiste)",
"ENABLE_ARCHIVES"     =>  "Archivierung aktivieren",
"BY_MONTH"            =>  "Monatlich",
"BY_YEAR"             =>  "Jährlich",
"READ_MORE_LINK"      =>  "Weiterlesen-Link im Auszug einfügen",
"ALWAYS"              =>  "Immer",
"NOT_SINGLE"          =>  "Ja, mit Ausnahme der Einzelbeitrag-Anzeige",
"GO_BACK_LINK"        =>  "Zurück-Link in der Einzelbeitrag-Anzeige",
"TITLE_LINK"          =>  "Beitragstitel mit dem Beitrag verlinken",
"BROWSER_BACK"        =>  "Zuletzt besuchte Seite",
"MAIN_NEWS_PAGE"      =>  "News-Hauptseite",
"ENABLE_IMAGES"       =>  "Beitragsbilder aktivieren",
"IMAGE_LINKS"         =>  "Grafiken mit dem Beitrag verlinken",
"IMAGE_WIDTH"         =>  "Breite des Beitragsbilds (Pixel)",
"IMAGE_HEIGHT"        =>  "Höhe des Beitragsbilds (Pixel)",
"FULL"                =>  "Volle Größe",
"IMAGE_CROP"          =>  "Beitragsbilder proportional beschneiden",
"IMAGE_ALT"           =>  "Beitragstitel im Alternativtext des Beitragsbilds einfügen",
"CUSTOM_SETTINGS"     =>  "Benutzerdefinierte Einstellungen",

# edit post
"POST_OPTIONS"        =>  "Beitragsoptionen",
"POST_SLUG"           =>  "Slug/URL",
"POST_TAGS"           =>  "Tags (kommagetrennt)",
"POST_DATE"           =>  "Datum der Veröffentlichung (<i>yyyy-mm-dd</i>)",
"POST_TIME"           =>  "Zeit der Veröffentlichung (<i>hh:mm</i>)",
"POST_PRIVATE"        =>  "Beitrag ist privat",
"POST_IMAGE"          =>  "Beitragsbild",
"LAST_SAVED"          =>  "Zuletzt gespeichert",

# validation
"FIELD_IS_REQUIRED"   =>  "Dieses Feld muss ausgefüllt werden",
"ENTER_VALID_DATE"    =>  "Geben Sie bitte ein gültiges Datum ein oder lassen Sie das Feld leer, um das aktuelle Datum zu verwenden",
"ENTER_VALID_TIME"    =>  "Geben Sie bitte eine gültige Zeit ein oder lassen Sie das Feld leer, um die aktuelle Zeit zu verwenden",
"ENTER_VALUE_MIN"     =>  "Geben Sie bitte einen Wert größer oder gleich %d ein",

# date picker - first day of week (0=Sunday, 1=Monday, ... 6=Saturday)
"DAY_OF_WEEK_START"   => "1",

# htaccess
"HTACCESS_HELP"       =>  "Um Fancy-URLs für Beiträge, Archive etc. zu aktivieren, ersetzen Sie den Inhalt der Datei <code>.htaccess</code> durch die unten stehenden Zeilen.",
"GO_BACK_WHEN_DONE"   =>  "Klicken Sie anschließend auf die Schaltfläche unten, um zur vorigen Seite zurückzukehren.",

# save/cancel/delete
"SAVE_SETTINGS"       =>  "Einstellungen speichern",
"SAVE_POST"           =>  "Beitrag speichern",
"FINISHED"            =>  "Fertig",
"CANCEL"              =>  "Abbrechen",
"DELETE"              =>  "Löschen",
"OR"                  =>  "oder",

# front-end/site
"FOUND"               =>  "Folgende Beiträge wurden gefunden:",
"NOT_FOUND"           =>  "Zu Ihrer Suche wurden keine Ergebnisse gefunden.",
"NOT_EXIST"           =>  "Der gewünschte Beitrag existiert nicht.",
"NO_POSTS"            =>  "Keine Beiträge gefunden.",
"PUBLISHED"           =>  "Veröffentlicht am",
"TAGS"                =>  "Tags",
"OLDER_POSTS"         =>  "&larr; Ältere Beiträge",
"NEWER_POSTS"         =>  "Neuere Beiträge &rarr;",
"SEARCH"              =>  "Suche",
"GO_BACK"             =>  "&lt;&lt; Zur vorigen Seite",
"ELLIPSIS"            =>  " [...] ",
"READ_MORE"           =>  "Weiterlesen",
"AUTHOR"              =>  "Autor:",
"PREV_TEXT"           =>  "&lt;",
"PREV_TITLE"          =>  "Vorige Seite",
"NEXT_TEXT"           =>  "&gt;",
"NEXT_TITLE"          =>  "Nächste Seite",

# language localization
"LOCALE"              =>  "de_DE.utf8,de.utf8,de_DE.UTF-8,de.UTF-8,de_DE,deu,de",

# date settings - list of available parameters: http://php.net/strftime
"DATE_FORMAT"         =>  "%d.%m.%Y",
"MONTHLY_FORMAT"      =>  "%B %Y",
"YEARLY_FORMAT"       =>  "%Y"

);
