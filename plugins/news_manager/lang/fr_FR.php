<?php

/**
 * Traduction française de News Manager par Sébastien Colmant et Emmanuel Simond
 */


$i18n = array(

# general
"PLUGIN_NAME"         =>  "News Manager",

# error messages
"ERROR_ENV"           =>  "Il y a eu une erreur en accédant au dossier des billets et/ou au fichier de configuration. <em>CHMOD 777</em> sur les dossiers /data, /backups et leurs sous-dossiers puis réessayez.",
"ERROR_SAVE"          =>  "<b>Erreur:</b> Impossible d'enregistrer vos modifications. <em>CHMOD 777</em> sur les dossiers /data, /backups et leurs sous-dossiers puis réessayez.",
"ERROR_DELETE"        =>  "<b>Erreur:</b> Impossible d'effacer le billet. <em>CHMOD 777</em> sur les dossiers /data, /backups et leurs sous-dossiers puis réessayez.",
"ERROR_RESTORE"       =>  "<b>Error:</b> Impossible de récupérer le billet. <em>CHMOD 777</em> sur les dossiers /data, /backups et leurs sous-dossiers puis réessayez.",

# success messages
"SUCCESS_SAVE"        =>  "Vos modifications ont bien été enregistrées.",
"SUCCESS_DELETE"      =>  "Le billet a été supprimé.",
"SUCCESS_RESTORE"     =>  "Le billet a bien été récupéré.",

# other messages
"UPDATE_HTACCESS"     =>  "<b>Avertissement:</b> Vous devez probablement mettre à jour votre fichier <a href=\"%s\">.htaccess</a>!",

# admin button (top-right)
"NEWS_TAB"            =>  "News",
"SETTINGS"            =>  "Paramètres",
"NEW_POST"            =>  "Créer un Nouveau Billet",

# admin panel
"POST_TITLE"          =>  "Titre du Billet",
"DATE"                =>  "Date",
"EDIT_POST"           =>  "Modifier le Billet",
"VIEW_POST"           =>  "Voir le Billet",
"DELETE_POST"         =>  "Supprimer le Billet",
"POSTS"               =>  "Billets",

# edit settings
"NM_SETTINGS"         =>  "Paramètres de News Manager",
"DOCUMENTATION"       =>  "Pour plus d'informations sur ces paramètres, visitez la <a href=\"%s\" target=\"_blank\">page de documentation (en anglais)</a>.",
"PAGE_URL"            =>  "URL de la page des News",
"NO_PAGE_SELECTED"    =>  "Aucune page sélectionnée",
"LANGUAGE"            =>  "Langue à utiliser pour la page des News",
"SHOW_POSTS_AS"       =>  "Afficher les billets de la page des News sous forme de",
"FULL_TEXT"           =>  "Texte Complet",
"EXCERPT"             =>  "Extrait",
"PRETTY_URLS"         =>  "Utiliser les URLs simplifiées pour les messages, les archives, etc...",
"PRETTY_URLS_NOTE"    =>  "Si vous utilisez les URLs simplifiées, vous pourriez avoir à mettre à jour votre fichier <code>.htaccess</code> après l'enregistrement de ces paramètres.",
"EXCERPT_LENGTH"      =>  "Longueur des Extraits (en nb. de caractères)",
"POSTS_PER_PAGE"      =>  "Nombre de Billets à afficher sur la page des News",
"RECENT_POSTS"        =>  "Nombre de Billets récents (dans la barre latérale)",
"ENABLE_ARCHIVES"     =>  "Activer les archives",
"BY_MONTH"            =>  "Par mois",
"BY_YEAR"             =>  "Par année",
"READ_MORE_LINK"      =>  "Ajouter un lien \"Lire la suite\" à la fin des extraits",
"ALWAYS"              =>  "Toujours",
"NOT_SINGLE"          =>  "Oui, sauf dans la page du billet",
"GO_BACK_LINK"        =>  "Ajouter un lien \"Retourner à la page précédente\" dans la page d'un billet",
"TITLE_LINK"          =>  "Lien vers le billet dans le titre",
"BROWSER_BACK"        =>  "Retour en arrière vers la page précédemment visitée",
"MAIN_NEWS_PAGE"      =>  "Lien vers la page principale des News",
"ENABLE_IMAGES"       =>  "Activer les images dans les billets",
"IMAGE_LINKS"         =>  "Créer un lien de l'image vers le contenu du billet",
"IMAGE_WIDTH"         =>  "Largeur des images (en pixels)",
"IMAGE_HEIGHT"        =>  "Hauteur des images (en pixels)",
"FULL"                =>  "taille originale",
"IMAGE_CROP"          =>  "Recadrer les images des billets pour les adapter au rapport largeur/hauteur",
"IMAGE_ALT"           =>  "Utiliser le titre du billet pour l'attribut <em>alt</em> des images",
"CUSTOM_SETTINGS"     =>  "Réglages personnalisés supplémentaires",

# edit post
"POST_OPTIONS"        =>  "Options du Billet",
"POST_SLUG"           =>  "Slug/URL",
"POST_TAGS"           =>  "Étiquettes (séparez les étiquettes par des virgules)",
"POST_DATE"           =>  "Date de publication (<i>yyyy-mm-dd</i>)",
"POST_TIME"           =>  "Heure de publication (<i>hh:mm</i>)",
"POST_PRIVATE"        =>  "Billet Privé",
"POST_IMAGE"          =>  "Image",
"LAST_SAVED"          =>  "Dernière sauvegarde",

# validation
"FIELD_IS_REQUIRED"   => "Ce champ est obligatoire",
"ENTER_VALID_DATE"    => "Veuillez svp entrer une date valide / Laissez ce champ vide pour utiliser la date du jour",
"ENTER_VALID_TIME"    => "Veuillez svp entrer une heure valide / Laissez ce champ vide pour utiliser l'heure actuelle",
"ENTER_VALUE_MIN"     => "Veuillez fournir une valeur supérieure ou égale à %d",

# date picker - first day of week (0=Sunday, 1=Monday, ... 6=Saturday)
"DAY_OF_WEEK_START"   => "1",

# htaccess
"HTACCESS_HELP"       =>  "Pour activer la redirection d'URLs pour les billets, archives, etc, remplacez le contenu de votre fichier <code>.htaccess</code> par les lignes ci-dessous:",
"GO_BACK_WHEN_DONE"   =>  "Lorsque vous avez terminé avec cette page, cliquez sur le bouton ci-dessous pour retourner au panneau principal.",

# save/cancel/delete
"SAVE_SETTINGS"       =>  "Enregistrer les Paramètres",
"SAVE_POST"           =>  "Enregistrer le Billet",
"FINISHED"            =>  "Terminé",
"CANCEL"              =>  "Annuler",
"DELETE"              =>  "Supprimer",
"OR"                  =>  "ou",

# front-end/site
"FOUND"               =>  "Les billets suivants ont été trouvés:",
"NOT_FOUND"           =>  "Désolé, votre recherche n'a pas retourné de résultat.",
"NOT_EXIST"           =>  "Le billet demandé n'existe pas.",
"NO_POSTS"            =>  "Aucun billet n'a encore été publié.",
"PUBLISHED"           =>  "Publié le",
"TAGS"                =>  "Étiquettes",
"TAGS_TITLE"          =>  "Voir tous les billets avec cette étiquette",
"OLDER_POSTS"         =>  "&larr; Billets précédents",
"NEWER_POSTS"         =>  "Billets suivants &rarr;",
"SEARCH"              =>  "Recherche",
"GO_BACK"             =>  "&lt;&lt; Retourner à la page précédente",
"ELLIPSIS"            =>  " [...] ",
"READ_MORE"           =>  "Lire la suite",
"AUTHOR"              =>  "Auteur:",
"PREV_TEXT"           =>  "&lt;",
"PREV_TITLE"          =>  "Page précédente",
"NEXT_TEXT"           =>  "&gt;",
"NEXT_TITLE"          =>  "Page suivante",

# language localization
"LOCALE"              =>  "fr_FR.utf8,fr.utf8,fr_FR.UTF-8,fr.UTF-8,fr_FR,fra,fr",

# date settings. voir http://php.net/fr/strftime pour la liste des paramètres possibles
"DATE_FORMAT"         =>  "%e %B %Y",
"MONTHLY_FORMAT"      =>  "%B %Y",
"YEARLY_FORMAT"       =>  "%Y"

);
