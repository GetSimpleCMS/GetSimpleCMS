<?php

/**
 * News Manager Dutch language file by Rogier Koppejan
 */


$i18n = array(

# general
"PLUGIN_NAME"         =>  "News Manager",

# error messages
"ERROR_ENV"           =>  "Er zijn fouten opgetreden bij de toegang tot de data mappen. <em>CHMOD 777</em> de mappen /data, /backups en de onderliggende bestanden en probeer opnieuw.",
"ERROR_SAVE"          =>  "<b>Fout:</b> Kan de wijzigingen niet opslaan. <em>CHMOD 777</em> de mappen /data, /backups en de onderliggende bestanden en probeer opnieuw.",
"ERROR_DELETE"        =>  "<b>Fout:</b> Kan het bericht niet verwijderen. <em>CHMOD 777</em> de mappen /data, /backups en de onderliggende bestanden en probeer opnieuw.",
"ERROR_RESTORE"       =>  "<b>Fout:</b> Kan het bericht niet herstellen. <em>CHMOD 777</em> de mappen /data, /backups en de onderliggende bestanden en probeer opnieuw.",

# success messages
"SUCCESS_SAVE"        =>  "De wijzigingen zijn opgeslagen.",
"SUCCESS_DELETE_POST" =>  "Het bericht is verwijderd.",
"SUCCESS_RESTORE"     =>  "Het bericht is hersteld.",

# other messages
"UPDATE_HTACCESS"     =>  "<b>Let op:</b> Waarschijnlijk moet het <a href=\"%s\">.htaccess</a> bestand aangepast worden!",

# admin button (top-right)
"NEWS_TAB"            =>  "Nieuws",
"SETTINGS"            =>  "Instellingen",
"NEW_POST"            =>  "Nieuw Bericht",

# admin panel
"POST_TITLE"          =>  "Titel",
"DATE"                =>  "Datum",
"EDIT_POST"           =>  "Bewerk Bericht",
"VIEW_POST"           =>  "Bekijk Bericht",
"DELETE_POST"         =>  "Verwijder Bericht",
"POSTS"               =>  "bericht(en)",

# edit settings
"NM_SETTINGS"         =>  "News Manager Instellingen",
"DOCUMENTATION"       =>  "Voor meer informatie over deze instellingen, bezoek de <a href=\"%s\" target=\"_blank\">documentatie pagina</a>.",
"PAGE_URL"            =>  "Berichtenpagina",
"NO_PAGE_SELECTED"    =>  "Geen pagina geselecteerd",
"LANGUAGE"            =>  "Taal die gebruikt wordt op de Nieuwspagina",
"SHOW_POSTS_AS"       =>  "Berichten Weergeven Als",
"FULL_TEXT"           =>  "Volledige Tekst",
"EXCERPT"             =>  "Samenvatting",
"PRETTY_URLS"         =>  "Activeer Fancy URLs voor berichten, archieven, etc.",
"PRETTY_URLS_NOTE"    =>  "Wanneer Fancy URLs geactiveerd zijn, zal je mogelijk het .htaccess bestand moeten bijwerken na het opslaan van deze instellingen.",
"EXCERPT_LENGTH"      =>  "Lengte Van Een Samenvatting (Karakters)",
"POSTS_PER_PAGE"      =>  "Aantal Berichten Op De Nieuwspagina",
"RECENT_POSTS"        =>  "Aantal Recente Berichten (Sidebar)",
"ENABLE_ARCHIVES"     =>  "Archieven activeren",
"BY_MONTH"            =>  "Per maand",
"BY_YEAR"             =>  "Per jaar",
"READ_MORE_LINK"      =>  "Laat een \"lees meer\" link zien bij samenvatting",
"ALWAYS"              =>  "Altijd",
"NOT_SINGLE"          =>  "Ja, behalve bij het tonen van een individueel bericht",
"GO_BACK_LINK"        =>  "\"Ga terug\" link bij een individueel bericht",
"TITLE_LINK"          =>  "Titel als link naar bericht",
"BROWSER_BACK"        =>  "Eerder bekeken pagina",
"MAIN_NEWS_PAGE"      =>  "Hoofd nieuwspagina",
"ENABLE_IMAGES"       =>  "Afbeeldingen aanzetten",
"IMAGE_LINKS"         =>  "Afbeelding als link naar bericht",
"IMAGE_WIDTH"         =>  "Afbeelding breedte (pixels)",
"IMAGE_HEIGHT"        =>  "Afbeelding hoogte (pixels)",
"FULL"                =>  "geheel",
"IMAGE_CROP"          =>  "Afbeeldingen bijsnijden op basis van de breedte/hoogte ratio",
"IMAGE_ALT"           =>  "Voeg berichttitel toe aan afbeelding <em>alt</em> attribuut",
"CUSTOM_SETTINGS"     =>  "Aangepaste instellingen",

# edit post
"POST_OPTIONS"        =>  "Bericht Opties",
"POST_SLUG"           =>  "Slug/URL",
"POST_TAGS"           =>  "Tags (meerdere tags scheiden met komma's)",
"POST_DATE"           =>  "Publicatiedatum (<i>yyyy-mm-dd</i>)",
"POST_TIME"           =>  "Publicatietijd (<i>hh:mm</i>)",
"POST_PRIVATE"        =>  "Bericht Is Priv&#233;",
"POST_IMAGE"          =>  "Afbeelding",
"LAST_SAVED"          =>  "Laatst Bijgewerkt",

# validation
"FIELD_IS_REQUIRED"   => "Dit veld is verplicht",
"ENTER_VALID_DATE"    => "Vul een geldige datum in / Laat leeg voor de huidige datum",
"ENTER_VALID_TIME"    => "Vul een geldige tijd in / Laat leeg voor de huidige tijd",
"ENTER_VALUE_MIN"     => "Vul hier een waarde in groter dan of gelijk aan %d",

# date picker - first day of week (0=Sunday, 1=Monday, ... 6=Saturday)
"DAY_OF_WEEK_START"   => "1",

# htaccess
"HTACCESS_HELP"       =>  "Vervang voor het gebruik van Fancy URLs voor berichten, archieven, etc. de inhoud van het <code>.htaccess</code> bestand met onderstaande regels.",
"GO_BACK_WHEN_DONE"   =>  "Klik op onderstaande knop om terug te gaan naar het berichten paneel.",

# save/cancel/delete
"SAVE_SETTINGS"       =>  "Instellingen Opslaan",
"SAVE_POST"           =>  "Wijzigingen Opslaan",
"FINISHED"            =>  "Gereed",
"CANCEL"              =>  "Annuleren",
"DELETE"              =>  "Verwijderen",
"OR"                  =>  "of",

# front-end/site
"FOUND"               =>  "De volgende berichten zijn gevonden:",
"NOT_FOUND"           =>  "Helaas, er zijn geen berichten gevonden.",
"NOT_EXIST"           =>  "Het opgevraagde bericht bestaat niet.",
"NO_POSTS"            =>  "Er zijn geen berichten gevonden.",
"PUBLISHED"           =>  "Gepubliceerd op",
"TAGS"                =>  "Tags",
"OLDER_POSTS"         =>  "&larr; Oudere Berichten",
"NEWER_POSTS"         =>  "Nieuwere Berichten &rarr;",
"SEARCH"              =>  "Zoek",
"GO_BACK"             =>  "&lt;&lt; Ga terug naar de vorige pagina",
"ELLIPSIS"            =>  " [...] ",
"READ_MORE"           =>  "Lees meer",
"AUTHOR"              =>  "Auteur:",
"PREV_TEXT"           =>  "&lt;",
"PREV_TITLE"          =>  "Vorige pagina",
"NEXT_TEXT"           =>  "&gt;",
"NEXT_TITLE"          =>  "Volgende pagina",

# language localization
"LOCALE"              =>  "nl_NL.utf8,nl.utf8,nl_NL.UTF-8,nl.UTF-8,nl_NL,nl",

# date settings - list of available parameters: http://php.net/strftime
"DATE_FORMAT"         =>  "%e %b %Y",
"MONTHLY_FORMAT"      =>  "%B %Y",
"YEARLY_FORMAT"       =>  "%Y"

);
