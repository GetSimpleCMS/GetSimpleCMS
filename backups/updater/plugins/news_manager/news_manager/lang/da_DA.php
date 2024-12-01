<?php

/**
 * News Manager Danish language file by Christian Sand
 */


$i18n = array(

# general
"PLUGIN_NAME"         =>  "News Manager",

# error messages
"ERROR_ENV"           =>  "Det var ikke muligt at tilgå datamappen. <em>CHMOD 777</em> mapperne /data, /backups og deres undermapper, og forsøg igen.",
"ERROR_SAVE"          =>  "<b>Fejl:</b> Kan ikke gemme ændringer. <em>CHMOD 777</em> mapperne /data, /backups og deres undermapper, og forsøg igen.",
"ERROR_DELETE"        =>  "<b>Fejl:</b> Kan ikke slette posten. <em>CHMOD 777</em> mapperne /data, /backups og deres undermapper, og forsøg igen..",
"ERROR_RESTORE"       =>  "<b>Fejl:</b> Kan ikke genoprette posten. <em>CHMOD 777</em> mapperne /data, /backups og deres undermapper, og forsøg igen.",

# success messages
"SUCCESS_SAVE"        =>  "Ændringer blev gemt.",
"SUCCESS_DELETE"      =>  "Posten blev slettet.",
"SUCCESS_RESTORE"     =>  "Posten blev genoprettet.",

# other messages
"UPDATE_HTACCESS"     =>  "<b>Fejl:</b> Opdater venligst <a href=\"%s\">.htaccess</a>-filen!",

# admin button (top-right)
"NEWS_TAB"            =>  "Nyheder",
"SETTINGS"            =>  "Indstillinger",
"NEW_POST"            =>  "Opret nyhedspost",

# admin panel
"POST_TITLE"          =>  "Titel",
"DATE"                =>  "Dato",
"EDIT_POST"           =>  "Rediger nyhedspost",
"VIEW_POST"           =>  "Vis nyhedspost",
"DELETE_POST"         =>  "Slet nyhedspost",
"POSTS"               =>  "Nyhedspost(er)",

# edit settings
"NM_SETTINGS"         =>  "Indstillinger",
"DOCUMENTATION"       =>  "For flere oplysninger, besøg siden: <a href=\"%s\" target=\"_blank\">Dokumentation</a>.",
"PAGE_URL"            =>  "Side med nyhedsposter",
"NO_PAGE_SELECTED"    =>  "Ingen sider valgt",
"LANGUAGE"            =>  "Sprog på nyhedsside:",
"SHOW_POSTS_AS"       =>  "Nyhedsposter vises",
"FULL_TEXT"           =>  "i fuld længde",
"EXCERPT"             =>  "som uddrag",
"PRETTY_URLS"         =>  "Benyt forkortet URL til nyhedsposter, -arkiver, etc.",
"PRETTY_URLS_NOTE"    =>  "Hvis forkortet URL er aktiveret, skal .htaccess-filen muligvis opdateres, efter disse indstillinger er gemt.",
"EXCERPT_LENGTH"      =>  "Længde på uddrag (antal karakterer)",
"POSTS_PER_PAGE"      =>  "Antal nyhedsposter på nyhedsside",
"RECENT_POSTS"        =>  "Antal seneste nyhedsposter (på sidemenu)",
"ENABLE_ARCHIVES"     =>  "Slå arkivering til",
"BY_MONTH"            =>  "Efter måned",
"BY_YEAR"             =>  "Efter år",
"READ_MORE_LINK"      =>  "Tilføj \" mere\" link til nyhedsuddrag",
"ALWAYS"              =>  "Altid",
"NOT_SINGLE"          =>  "Ja, undtagen ved enkelt nyhedsindlæg",
"GO_BACK_LINK"        =>  "\"Gå tilbage\" link ved visning af enkelt nyhedsindlæg",
"TITLE_LINK"          =>  "Opret linkoverskrift på nyhedsindlæg",
"BROWSER_BACK"        =>  "Foregående side",
"MAIN_NEWS_PAGE"      =>  "Nyhedsoversigt",
"ENABLE_IMAGES"       =>  "Vis billeder ved nyhedsindlæg",
"IMAGE_LINKS"         =>  "Billedlink til nyhedsindlæg",
"IMAGE_WIDTH"         =>  "Bredde (pixels)",
"IMAGE_HEIGHT"        =>  "Højde (pixels)",
"FULL"                =>  "fuld",
"IMAGE_CROP"          =>  "Beskær billeder",
"IMAGE_ALT"           =>  "Indsæt overskrift på nyhedsbillede <em>alt</em> attribut",
"CUSTOM_SETTINGS"     =>  "Brugerdefinerede indstillinger",

# edit post
"POST_OPTIONS"        =>  "Opsætning af nyhedspost",
"POST_SLUG"           =>  "Slug/URL",
"POST_TAGS"           =>  "Etiketter (adskilt med komma)",
"POST_DATE"           =>  "Udgivelsesdato (<i>yyyy-mm-dd</i>)",
"POST_TIME"           =>  "Udgivelsestidspunkt (<i>hh:mm</i>)",
"POST_PRIVATE"        =>  "Vent med at udgive nyhedsposten",
"POST_IMAGE"          =>  "Billede",
"LAST_SAVED"          =>  "Seneste udgave",

# validation
"FIELD_IS_REQUIRED"   => "Udfyldning af felt påkrævet",
"ENTER_VALID_DATE"    => "Indtast en gyldig dato / Udfyld ikke for aktuel dato",
"ENTER_VALID_TIME"    => "Indtast et gyldigt tidspunkt / Udfyld ikke for aktuelt tidspunkt",
"ENTER_VALUE_MIN"     => "Angiv en værdi der mindst er %d",

# date picker - first day of week (0=Sunday, 1=Monday, ... 6=Saturday)
"DAY_OF_WEEK_START"   => "1",

# htaccess
"HTACCESS_HELP"       =>  "Aktiver forkortet URL for nyhedsposter, -arkiver, etc. ved at erstatte indholdet af <code>.htaccess</code>-filen med linjerne herunder.",
"GO_BACK_WHEN_DONE"   =>  "Klik på knappen her under for at vende tilbage til hovedmenuen.",

# save/cancel/delete
"SAVE_SETTINGS"       =>  "Gem indstillinger",
"SAVE_POST"           =>  "Gem nyhedspost",
"FINISHED"            =>  "Afsluttet",
"CANCEL"              =>  "Annuller",
"DELETE"              =>  "Slet",
"OR"                  =>  "eller",

# front-end/site
"FOUND"               =>  "Følgende nyhedsposter blev fundet:",
"NOT_FOUND"           =>  "Søgningen gav ingen resultater.",
"NOT_EXIST"           =>  "Nyhedsposten findes ikke.",
"NO_POSTS"            =>  "Ingen nyhedsposter fundet.",
"PUBLISHED"           =>  "Udgivet den",
"TAGS"                =>  "Tags",
"OLDER_POSTS"         =>  "&larr; Ældre nyhedsposter",
"NEWER_POSTS"         =>  "Nye nyhedsposter &rarr;",
"SEARCH"              =>  "Søg",
"GO_BACK"             =>  "&lt;&lt; Forrige side",
"ELLIPSIS"            =>  " [...] ",
"READ_MORE"           =>  "Læs mere",
"AUTHOR"              =>  "Forfatter:",
"PREV_TEXT"           =>  "&lt;",
"PREV_TITLE"          =>  "Forrige side",
"NEXT_TEXT"           =>  "&gt;",
"NEXT_TITLE"          =>  "Næste side",

# language localization
"LOCALE"              =>  "da_DA.utf8,da.utf8,da_DA.UTF-8,da.UTF-8,da_DA,dan,da",

# date settings - list of available parameters: http://php.net/strftime
"DATE_FORMAT"         =>  "%b %e, %Y",
"MONTHLY_FORMAT"      =>  "%B %Y",
"YEARLY_FORMAT"       =>  "%Y"

);
