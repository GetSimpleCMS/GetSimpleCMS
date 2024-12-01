<?php

/**
 * News Manager Finnish language file by Samuli Vuorinen
 */


$i18n = array(

# general
"PLUGIN_NAME" => "Uutismanageri",

# error messages
"ERROR_ENV" => "Data-kansiota käyttäessä tapahtui virhe. Aja hakemistoille /data, /backups ja niiden alihakemistoille käsky <em>CHMOD 777</em> ja yritä uudestaan.",
"ERROR_SAVE" => "<b>Virhe.</b> Aja hakemistoille /data, /backups ja niiden alihakemistoille käsky <em>CHMOD 777</em> ja yritä uudestaan.",
"ERROR_DELETE" => "<b>Virhe.</b> Uutisen poistaminen ei onnistunut. Aja hakemistoille /data, /backups ja niiden alihakemistoille käsky <em>CHMOD 777</em> ja yritä uudestaan.",
"ERROR_RESTORE" => "<b>Virhe.</b> Uutisen palauttaminen epäonnistui. Aja hakemistoille /data, /backups ja niiden alihakemistoille käsky <em>CHMOD 777</em> ja yritä uudestaan.",

# success messages
"SUCCESS_SAVE" => "Muutoksesi on tallennettu.",
"SUCCESS_DELETE" => "Uutinen on poistettu.",
"SUCCESS_RESTORE" => "Uutinen on palautettu.",

# other messages
"UPDATE_HTACCESS" => "<b>Huomio:</b> Sinun täytyy todennäköisesti päivittää <a href=\"%s\">.htaccess</a>-tiedostosi!",

# admin button (top-right)
"NEWS_TAB" =>  "Uutiset",
"SETTINGS" => "Asetukset",
"NEW_POST" => "Kirjoita uusi uutinen",

# admin panel
"POST_TITLE" => "Otsikko",
"DATE" => "Päiväys",
"EDIT_POST" => "Muokkaa viestiä",
"VIEW_POST" => "Näytä uutinen",
"DELETE_POST" => "Poista uutinen",
"POSTS" => "uutiset",

# edit settings
"NM_SETTINGS" => "Uutismanagerin asetukset",
"DOCUMENTATION" => "Jos haluat lisätietoa näistä asetuksista, käy <a href=\"%s\" target=\"_blank\">dokumentaatiosivulla.</a>",
"PAGE_URL" => "Sivulla jolla uutiset näytetään",
"NO_PAGE_SELECTED" => "Ei valittua sivua",
"LANGUAGE" => "Uutissivun kieli",
"SHOW_POSTS_AS" => "Uutisten näyttö uutissivulla",
"FULL_TEXT" => "Koko teksti",
"EXCERPT" => "Tekstiote",
"PRETTY_URLS" => "Käytä muotoiltuja URL-osoitteita uutisille, arkistolle, jne.",
"PRETTY_URLS_NOTE" => "Jos URL-muotoilu on päällä, saatat joutua päivittämään .htaccess-tiedostoa näiden asetusten tallentamisen jälkeen.",
"EXCERPT_LENGTH" => "Tekstiotteen pituus (merkkiä)",
"POSTS_PER_PAGE" => "Uutisten määrä uutissivulla",
"RECENT_POSTS" => "Uusimpien uutisten määrä (sivupalkissa)",
"ENABLE_ARCHIVES" => "Arkistointi",
"BY_MONTH" => "Kuukauden mukaan",
"BY_YEAR" => "Vuoden mukaan",
"READ_MORE_LINK" => "Lisää \"lue lisää\" -linkki tekstiotteisiin",
"ALWAYS" => "Aina",
"NOT_SINGLE" => "Kyllä, paitsi yksittäisen uutisen näkymässä",
"GO_BACK_LINK" => "\"Palaa takaisin\" -linkki yksittäisen uutisen näkymässä",
"TITLE_LINK" => "Otsikossa linkki uutiseen",
"BROWSER_BACK" => "Viimeksi vierailtu sivu",
"MAIN_NEWS_PAGE" => "Pääuutissivulla",
"ENABLE_IMAGES" => "Näytä uutisessa kuvia",
"IMAGE_LINKS" => "Linkkaa kuvat uutiseen",
"IMAGE_WIDTH" => "Kuvan leveys (pikseliä)",
"IMAGE_HEIGHT" => "Kuvan korkeus (pikseliä)",
"FULL" => "täysi",
"IMAGE_CROP" => "Rajaa uutisen kuvat jotta haluttu kuvasuhde säilyy",
"IMAGE_ALT" => "Lisää uutisen otsikko kuvan <em>alt</em>-attribuuttiin.",
"CUSTOM_SETTINGS" => "Mukautetut asetukset",

# edit post
"POST_OPTIONS" => "Uutisen asetukset",
"POST_SLUG" => "Polku/URL",
"POST_TAGS" => "Tägit (erottele tägit pilkulla)",
"POST_DATE" => "Julkaisupäivämäärä (<i>vvvv-kk-pp</i>)",
"POST_TIME" => "Julkaisuaika (<i>tt:mm</i>)",
"POST_PRIVATE" => "Uutinen on yksityinen",
"POST_IMAGE" => "Kuva",
"LAST_SAVED" => "Viimeksi tallennettu",

# validation
"FIELD_IS_REQUIRED" => "Tämä kenttä on pakollinen",
"ENTER_VALID_DATE" => "Anna kunnollinen päivämäärä / Jätä tyhjäksi, jolloin käytetään tämänhetkistä päivämäärää",
"ENTER_VALID_TIME" => "Anna kunnollinen kellonaika / Jätä tyhjäksi, jolloin käytetään tämänhetkistä kellonaikaa",
"ENTER_VALUE_MIN" => "Anna arvo joka on suurempi tai yhtä suuri kuin %d",

# date picker - first day of week (0=Sunday, 1=Monday, ... 6=Saturday)
"DAY_OF_WEEK_START"   => "1",

# htaccess
"HTACCESS_HELP" => "Voidaksi käyttää uutisissa, arkistossa jne. muotoiltuja URL-osoitteita, korvaa <code>.htaccess</code>-tiedoston sisältö allaolevilla riveillä.",
"GO_BACK_WHEN_DONE" => "Kun olet valmis, klikkaa allaolevaa nappia palataksesi pääpaneeliin",

# save/cancel/delete
"SAVE_SETTINGS" => "Tallenna asetukset",
"SAVE_POST" => "Tallenna uutinen",
"FINISHED" => "Valmis",
"CANCEL" => "Peruuta",
"DELETE" => "Poista",
"OR" => "tai",

# front-end/site
"FOUND" => "Seuraavat uutiset löytyivät:",
"NOT_FOUND" => "Pahoittelut, hakusi ei tuottanut yhtään tulosta.",
"NOT_EXIST" => "Pyydettyä uutista ei ole olemassa.",
"NO_POSTS" => "Uutisia ei löytynyt.",
"PUBLISHED" => "Julkaistu",
"TAGS" => "Tags",
"OLDER_POSTS" => "&larr; Vanhemmat uutiset",
"NEWER_POSTS" => "Uudemmat uutiset &rarr;",
"SEARCH" => "Etsi",
"GO_BACK" => "&lt;&lt; Palaa edelliselle sivulle",
"ELLIPSIS" => " [...] ",
"READ_MORE" => "Lue lisää",
"AUTHOR" => "Kirjoittaja:",
"PREV_TEXT" => "&lt;",
"PREV_TITLE" => "Edellinen sivu",
"NEXT_TEXT" => "&gt;",
"NEXT_TITLE" => "Seuraava sivu",

# language localization
"LOCALE" => "fi_FI.utf8,fi.utf8,fi_FI.UTF-8,fi.UTF-8,fi_FI,fin,fi",

# date settings - list of available parameters: http://php.net/strftime
"DATE_FORMAT" => "%d.%m.%Y",
"MONTHLY_FORMAT" => "%B %Y",
"YEARLY_FORMAT" => "%Y"

);
