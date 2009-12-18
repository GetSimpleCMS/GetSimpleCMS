<?php
/****************************************************
*
* @File: 				fr_FR.php
* @Package:			GetSimple
* @Subject:			French language file
* @Date:				01 Sept 2009
* @Revision:		01 Sept 2009
* @Version:			GetSimple 1.6
* @Status:			Final
* @Traductors: 	Brian Pierson,
*              	  and
*								Patrick Lefevre, <patrick[dot]lefevre[at]gmail[dot]com> 	
*
*****************************************************/

$i18n = array(

/* 
 * For: install.php
*/
"PHPVER_ERROR"          =>    "<b>L'installation ne peut pas se poursuivre :</b> une version de PHP supérieure à 5.1.3 est requise - version installée : ",
"SIMPLEXML_ERROR"       =>    "<b>L'installation ne peut pas se poursuivre :</b> <em>SimpleXML</em> n'est pas installé",
"CURL_WARNING"          =>    "<b>Avertissement :</b> <em>cURL</em> n'est pas installé",
"TZ_WARNING"            =>    "<b>Avertissement :</b> le paramètre <em>date_default_timezone_set</em> n'est pas renseigné",
"WEBSITENAME_ERROR"     =>    "<b>Erreur :</b> le nom du site saisi n'est pas correct",
"WEBSITEURL_ERROR"      =>    "<b>Erreur :</b> l'URL fournie n'est pas correcte",
"USERNAME_ERROR"        =>    "<b>Erreur :</b> le nom d'utilisateur n'est pas renseigné",
"EMAIL_ERROR"           =>    "<b>Erreur :</b> l'adresse e-mail fournie n'est pas valide",
"CHMOD_ERROR"           =>    "<b>L'installation ne peut pas se poursuivre :</b> l'écriture du fichier de configuration est impossible. Faites un <em>CHMOD 777</em> sur les dossiers <strong>/data/</strong> <em>et</em> <strong>/backups/</strong> puis réessayez d'installer GetSimple",
"EMAIL_COMPLETE"        =>    "L'installation s'est déroulée avec succès",
"EMAIL_USERNAME"        =>    "Votre nom d'utilisateur est",
"EMAIL_PASSWORD"        =>    "Votre nouveau mot de passe est",
"EMAIL_LOGIN"           =>    "Connectez-vous ici",
"EMAIL_THANKYOU"        =>    "Merci d'utiliser",
"NOTE_REGISTRATION"     =>    "Les informations de votre enregistrement vous ont été envoyées à l'adresse",
"NOTE_REGERROR"         =>    "<b>Erreur :</b> Envoi par e-mail des informations liées à votre enregistrement est impossible. Prenez bien note du mot de passe inscrit ci-dessous",
"NOTE_USERNAME"         =>    "Votre nom d'utilisateur est",
"NOTE_PASSWORD"         =>    "et votre mot de passe est",
"INSTALLATION"          =>    "Installation",
"LABEL_WEBSITE"         =>    "Nom du site",
"LABEL_BASEURL"         =>    "URL de la racine du site",
"LABEL_SUGGESTION"      =>    "Suggestion",
"LABEL_USERNAME"        =>    "Nom d'utilisateur",
"LABEL_EMAIL"           =>    "Adresse e-mail",
"LABEL_INSTALL"         =>    "Installer !",

/* 
 * For: pages.php
*/
"MENUITEM_SUBTITLE"     =>    "élément du menu",
"HOMEPAGE_SUBTITLE"     =>    "page d'accueil",
"PRIVATE_SUBTITLE"      =>    "privée",
"EDITPAGE_TITLE"        =>    "Modifier la page",
"VIEWPAGE_TITLE"        =>    "Visualiser la page",
"DELETEPAGE_TITLE"      =>    "Supprimer la page",
"PAGE_MANAGEMENT"       =>    "Gestion des pages",
"TOGGLE_STATUS"         =>    "Afficher les marquages",
"TOTAL_PAGES"           =>    "page(s) au total",
"ALL_PAGES"             =>    "Toutes les pages",

/* 
 * For: edit.php
*/
"PAGE_NOTEXIST"         =>    "La page demandée n'existe pas",
"BTN_SAVEPAGE"          =>    "Enregistrer la page",
"BTN_SAVEUPDATES"       =>    "Enregistrer les modifications",
"DEFAULT_TEMPLATE"      =>    "Thème par défaut",
"NONE"                  =>    "Aucun",
"PAGE"                  =>    "Page",
"NEW_PAGE"              =>    "Nouvelle Page",
"PAGE_EDIT_MODE"        =>    "Édition des pages",
"CREATE_NEW_PAGE"       =>    "Créer une nouvelle page",
"VIEW"                  =>    "<em>V</em>isualiser", // 'v' is the accesskey identifier
"PAGE_OPTIONS"          =>    "<em>O</em>ptions", // 'o' is the accesskey identifier
"TOGGLE_EDITOR"         =>    "Éditeur WYSIWY<em>G</em>", // 'g' is the accesskey identifier
"SLUG_URL"              =>    "Identifiant/URL",
"TAG_KEYWORDS"          =>    "Marquages &amp; Mots-clés",
"PARENT_PAGE"           =>    "Page parent",
"TEMPLATE"              =>    "Thème",
"KEEP_PRIVATE"          =>    "Garder privée ?",
"ADD_TO_MENU"           =>    "Ajouter au menu",
"PRIORITY"              =>    "Priorité",
"MENU_TEXT"             =>    "Texte du menu",
"LABEL_PAGEBODY"        =>    "Corps de la page",
"CANCEL"                =>    "Annuler",
"BACKUP_AVAILABLE"      =>    "Sauvegardes disponibles",
"MAX_FILE_SIZE"         =>    "Taille maximale du fichier",
"LAST_SAVED"            =>    "Dernier enregistrement",
"FILE_UPLOAD"           =>    "Uploader un fichier",
"OR"                    =>    "ou",

/* 
 * For: upload.php
*/
"ERROR_UPLOAD"          =>    "Une erreur est survenue lors de l'importation du fichier",
"FILE_SUCCESS_MSG"      =>    "Adresse du fichier",
"FILE_MANAGEMENT"       =>    "Gestion des fichiers",
"UPLOADED_FILES"        =>    "Fichiers uploadés",
"SHOW_ALL"              =>    "Voir tous les fichiers",
"VIEW_FILE"             =>    "Voir le fichier",
"DELETE_FILE"           =>    "Supprimer le fichier",
"TOTAL_FILES"           =>    "fichiers au total",

/* 
 * For: logout.php
*/
"LOGGED_OUT"            =>    "Quitter",
"MSG_LOGGEDOUT"         =>    "Vous êtes désormais déconnecté",
"MSG_PLEASE"            =>    "Reconnectez-vous si vous devez accéder à votre compte", 

/* 
 * For: index.php
*/
"LOGIN"                 =>    "Connexion",
"USERNAME"              =>    "Nom d'utilisateur",
"PASSWORD"              =>    "Mot de passe",
"FORGOT_PWD"            =>    "Mot de passe oublié ?",
"CONTROL_PANEL"         =>    "Console d'administration",
"LOGIN_REQUIREMENT"     =>    "Configuration requise",
"WARN_JS_COOKIES"       =>    "Les cookies et javascript doivent être activés dans votre navigateur pour que tout fonctionne correctement",
"WARN_IE6"              =>    "Internet Explorer 6 n'est pas supporté.",

/* 
 * For: navigation.php
*/
"CURRENT_MENU"          =>     "Menu actuel",
"NO_MENU_PAGES"         =>     "Il n'y a pas de page à afficher dans le menu",

/* 
 * For: theme-edit.php
*/
"TEMPLATE_FILE"         =>     "Le fichier du thème <b>%s</b> a été uploadé avec succès !",
"THEME_MANAGEMENT"      =>     "Gestion des thèmes",
"EDIT_THEME"            =>     "Modifier le thème",
"EDITING_FILE"          =>     "Modification du fichier",
"BTN_SAVECHANGES"       =>     "Enregistrer",
"EDIT"                  =>     "Modifier",

/* 
 * For: support.php
*/
"SETTINGS_UPDATED"      =>     "Vos paramètres ont été mis à jour",
"UNDO"                  =>     "Annuler le dernier changement",
"SUPPORT"               =>     "Support",
"SETTINGS"              =>     "Paramètres de configuration",
"ERROR"                 =>     "Erreur",
"BTN_SAVESETTINGS"      =>     "Enregistrer les modifications",
"EMAIL_ON_404"          =>     "E-mail à notifier lors d'erreurs 404",
"VIEW_404"              =>     "Visualiser les erreurs 404",
"VIEW_FAILED_LOGIN"     =>     "Visualiser les erreurs de connexion",
"VIEW_CONTACT_FORM"     =>     "Voir le formulaire de contact",
"VIEW_TICKETS"          =>     "Visualiser les tickets soumis",

/* 
 * For: log.php
*/
"MSG_HAS_BEEN_CLR"      =>     "a été effacé",
"LOGS"                  =>     "Logs",
"VIEWING"               =>     "Visualisation",
"LOG_FILE"              =>     "Fichier de log",
"CLEAR_ALL_DATA"        =>     "Purger toutes les données de",
"CLEAR_THIS_LOG"        =>     "Effa<em>c</em>er ces Logs", // 'c' is the accesskey identifier
"LOG_FILE_ENTRY"        =>     "LOG FILE ENTRY",
"THIS_COMPUTER"         =>     "Cet ordinateur",

/* 
 * For: backup-edit.php
*/
"BAK_MANAGEMENT"        =>    "Gestion des sauvegardes",
"ASK_CANCEL"            =>    "<em>A</em>nnuler", // 'c' is the accesskey identifier
"ASK_RESTORE"           =>    "<em>R</em>estaurer", // 'r' is the accesskey identifier
"ASK_DELETE"            =>    "<em>S</em>upprimer", // 'd' is the accesskey identifier
"BACKUP_OF"             =>    "Sauvegarde de",
"PAGE_TITLE"            =>    "Titre de la page",
"YES"                   =>    "Oui",
"NO"                    =>    "Non",
"DATE"                  =>    "Date",

/* 
 * For: components.php
*/
"COMPONENTS"            =>    "Composants",
"DELETE_COMPONENT"      =>    "Supprimer le composant",
"EDIT"                  =>    "Modifier",
"ADD_COMPONENT"         =>    "<em>A</em>jouter un composant", // 'a' is the accesskey identifier
"SAVE_COMPONENTS"       =>    "Enregistrer les composants",

/* 
 * For: sitemap.php
*/
"SITEMAP_CREATED"       =>    "Sitemap créé ! 4 moteurs de recherche en ont été par ailleurs informés",
"SITEMAP_ERRORPING"     =>    "Sitemap crée, mais il y a eu une erreur lors de sa soumission à un (ou plusieurs) moteur(s) de recherche ",
"SITEMAP_ERROR"         =>    "Votre sitemap n'a pas pu être généré",
"SITEMAP_WAIT"          =>    "<b>Patientez s'il vous plaît :</b> votre sitemap est en tain d'être généré...",

/* 
 * For: theme.php
*/
"THEME_CHANGED"         =>    "Le thème a été changé avec succès",
"CHOOSE_THEME"          =>    "Choix du thème",
"ACTIVATE_THEME"        =>    "Activer le thème",
"THEME_SCREENSHOT"      =>    "Aperçu du thème",
"THEME_PATH"            =>    "Chemin du thème sélectionné",

/* 
 * For: resetpassword.php
*/
"RESET_PASSWORD"        =>    "Regénérer un nouveau mot de passe",
"YOUR_NEW"              =>    "Votre nouveau",
"PASSWORD_IS"           =>    "mot de passe est",
"ATTEMPT"               =>    "Tentative",
"MSG_PLEASE_EMAIL"      =>    "Saisissez, s'il vous plaît, l'adresse e-mail avec laquelle vous vous êtes enregistré, et un nouveau mot de passe vous sera envoyé",
"SEND_NEW_PWD"          =>    "Envoyer le nouveau mot de passe",

/* 
 * For: settings.php
*/
"GENERAL_SETTINGS"      =>    "Paramètres généraux",
"WEBSITE_SETTINGS"      =>    "Configuration du site",
"LOCAL_TIMEZONE"        =>    "Fuseau horaire",
"LANGUAGE"              =>    "Langue",
"USE_FANCY_URLS"        =>    "<b>Utiliser les URL simplifiées</b> - Nécessite que le module mod_rewrite soit activé",
"ENABLE_HTML_ED"        =>    "<b>Activer l'éditeur HTML</b>",
"USER_SETTINGS"         =>    "Paramètres du compte de l'utilisateur",
"WARN_EMAILINVALID"     =>    "Attention : Cette adresse ne semble pas valide !",
"ONLY_NEW_PASSWORD"     =>    "Ne renseignez ce champ que si vous désirez changer de mot de passe",
"NEW_PASSWORD"          =>    "Nouveau mot de passe",
"CONFIRM_PASSWORD"      =>    "Ressaisissez le mot de passe",
"PASSWORD_NO_MATCH"     =>    "Les mots de passe saisis ne correspondent pas",


/* 
 * For: contactform.php
*/"MSG_CAPTCHA_FAILED"  =>    "Ce que vous avez saisi ne correspond pas à l'image",
"CONTACT_FORM_SUB"      =>    "Soumission du formulaire de contact",
"FROM"                  =>    "De",
"MSG_CONTACTSUC"        =>    "Votre e-mail a été envoyé avec succès",
"MSG_CONTACTERR"        =>    "Une erreur est survenue lors de l'envoi du message",

/* 
 * For: 404-mailer.php
*/
"404_ENCOUNTERED"       =>    "Auto : une erreur 404 est survenue sur",
"404_AUTO_MSG"          =>    "Ceci est un message automatique de votre site web",
"PAGE_CANNOT_FOUND"     =>    "Une erreur de type 'page not found' a été rencontrée sur le",
"DOMAIN"                =>    "domaine",
"DETAILS"               =>    "DETAILS",
"WHEN"                  =>    "Quand",
"WHO"                   =>    "Qui",
"FAILED_PAGE"           =>    "Page concernée",
"REFERRER"              =>    "Référence",
"BROWSER"               =>    "Navigateur",

/* 
 * For: health-check.php
*/
"WEB_HEALTH_CHECK"      =>    "Vérifier l'état de santé du site",
"VERSION"               =>    " - Version",
"UPG_NEEDED"            =>    "Mise à jour requise vers la version",
"CANNOT_CHECK"          =>    "Vérification impossible. Votre version :",
"LATEST_VERSION"        =>    "Dernière version disponible installée",
"SERVER_SETUP"          =>    "Configuration du serveur",
"OR_GREATER_REQ"        =>    "ou supérieure est requise",
"OK"                    =>    "OK",
"INSTALLED"             =>    "Installé",
"NOT_INSTALLED"         =>    "Pas installé",
"WARNING"               =>    "Avertissement",
"DATA_FILE_CHECK"       =>    "Vérification de l'intégrité des fichiers",
"DIR_PERMISSIONS"       =>    "Droits des répertoires",
"EXISTANCE"             =>    "Existence du fichier %s",
"MISSING_FILE"          =>    "Fichier manquant",
"BAD_FILE"              =>    "Mauvais fichier",
"NO_FILE"               =>    "Pas de fichier",
"GOOD_D_FILE"           =>    "'Deny' file -> OK",
"GOOD_A_FILE"           =>    "'Allow' file -> OK",
"CANNOT_DEL_FILE"       =>    "Suppression du fichier impossible",
"DOWNLOAD"              =>    "Télécharger",
"WRITABLE"              =>    "Inscriptible",
"NOT_WRITABLE"          =>    "Non inscriptible",

/* 
 * For: footer.php
*/
"POWERED_BY"            =>    "Propulsé par",
"PRODUCTION"            =>    "Production",
"SUBMIT_TICKET"         =>    "Soumettre un ticket",

/* 
 * For: backups.php
*/
"PAGE_BACKUPS"          =>    "Sauvegardes",
"ASK_DELETE_ALL"        =>    "Tout <em>S</em>upprimer",
"DELETE_ALL_BAK"        =>    "Supprimer toutes les sauvegardes ?",
"TOTAL_BACKUPS"         =>    "sauvegarde(s) au total",

/* 
 * For: archive.php
*/
"SUCC_WEB_ARCHIVE"      =>    "Site archivé avec succès !",
"SUCC_WEB_ARC_DEL"      =>    "Archive du site supprimée avec succès",
"WEBSITE_ARCHIVES"      =>    "Archives du site",
"ARCHIVE_DELETED"       =>    "Archive supprimée avec succès",
"CREATE_NEW_ARC"        =>    "Créer une nouvelle archive",
"ASK_CREATE_ARC"        =>    "<em>C</em>réer une nouvelle archive maintenant",
"CREATE_ARC_WAIT"       =>    "<b>Attendez s'il vous plaît :</b> Création de l'archive en cours...",
"DOWNLOAD_ARCHIVES"     =>    "Télécharger l'archive",
"DELETE_ARCHIVE"        =>    "Supprimer l'archive",
"TOTAL_ARCHIVES"        =>    "archive(s) au total",

/* 
 * For: include-nav.php
*/
"WELCOME"               =>    "Bienvenue", // used as 'Welcome USERNAME!'
"TAB_PAGES"             =>    "<em>P</em>ages",
"TAB_FILES"             =>    "<em>F</em>ichiers",
"TAB_THEME"             =>    "Thèmes",
"TAB_BACKUPS"           =>    "<em>S</em>auvegardes",
"TAB_SETTINGS"          =>    "<em>C</em>onfiguration",
"TAB_SUPPORT"           =>    "Supp<em>o</em>rt",
"TAB_LOGOUT"            =>    "<em>D</em>éconnexion",

/* 
 * For: sidebar-files.php
*/
"BROWSE_COMPUTER"       =>    "Parcourir...",
"UPLOAD"                =>    "Uploader",

/* 
 * For: sidebar-support.php
*/
"SIDE_SUPPORT_LOG"      =>    "Supp<em>o</em>rt - Paramètres &amp; Logs",
"SIDE_VIEW_LOG"         =>    "Voir les Log",
"SIDE_HEALTH_CHK"       =>    "Contrôles de Santé du site",
"SIDE_SUBMIT_TICKET"    =>    "Soumettre un Tic<em>k</em>et",
"SIDE_DOCUMENTATION"    =>    "- <em>D</em>ocumentation",

/* 
 * For: sidebar-theme.php
*/
"SIDE_VIEW_SITEMAP"     =>    "<em>V</em>oir le Sitemap",
"SIDE_GEN_SITEMAP"      =>    "<em>G</em>énérer le Sitemap",
"SIDE_COMPONENTS"       =>    "<em>M</em>odifier les composants",
"SIDE_EDIT_THEME"       =>    "Modifier le <em>t</em>hème",
"SIDE_CHOOSE_THEME"     =>    "<em>C</em>hoisissez un thème",

/* 
 * For: sidebar-pages.php
*/
"SIDE_CREATE_NEW"       =>    "<em>C</em>réer une nouvelle page",
"SIDE_VIEW_PAGES"       =>    "Voir toutes les <em>p</em>ages",

/* 
 * For: sidebar-pages.php
*/
"SIDE_GEN_SETTINGS"     =>    "<em>P</em>aramètres généraux",
"SIDE_USER_PROFILE"     =>    "Profil de l'<em>U</em>tilisateur",

/* 
 * For: sidebar-pages.php
*/
"SIDE_VIEW_BAK"         =>    "Voir les sauvegardes de la page",
"SIDE_WEB_ARCHIVES"     =>    "<em>A</em>rchives du site",
"SIDE_PAGE_BAK"         =>    "<em>S</em>auvegardes de la page",

/* 
 * For: error_checking.php
*/
"ER_PWD_CHANGE"         =>    "N'oubliez pas de <a href=\"settings.php#profile\">changer votre mot de passe</a>. Il sera plus facile à mémoriser que celui généré aléatoirement...",
"ER_BAKUP_DELETED"      =>    "La sauvegarde de %s a été supprimée",
"ER_REQ_PROC_FAIL"      =>    "Erreur dans le traitement de la requête",
"ER_YOUR_CHANGES"       =>    "Les modifications du fichier %s ont été enregistrées",
"ER_HASBEEN_REST"       =>    "%s a été restauré",
"ER_HASBEEN_DEL"        =>    "%s a été supprimé",
"ER_CANNOT_INDEX"       =>    "Vous ne pouvez pas modifier l'URL de la page d'accueil",
"ER_SETTINGS_UPD"       =>    "Vos paramètres ont été enregistrés",
"ER_OLD_RESTORED"       =>    "Vos anciens paramètres ont été restaurés",
"ER_NEW_PWD_SENT"       =>    "Un nouveau mot de passe a été envoyé à l'adresse fournie",
"ER_SENDMAIL_ERR"       =>    "Il y a eu un problème lors de l'envoi du message, ressayez plus tard s'il vous plaît",
"ER_FILE_DEL_SUC"       =>    "Fichier supprimé avec succès",
"ER_PROBLEM_DEL"        =>    "Une erreur est survenue lors de la suppression du fichier",
"ER_COMPONENT_SAVE"     =>    "Vos composants ont été enregistrés",
"ER_COMPONENT_REST"     =>    "Vos composants ont été restaurés",
"ER_CANCELLED_FAIL"     =>    "<b>Annulé :</b> La mise à jour du fichier a été annulée",

/* 
 * For: changedata.php
*/
"CANNOT_SAVE_EMPTY"     =>    "Vous ne pouvez pas enregistrer une page vide",

/* 
 * For: template_functions.php
*/
"FTYPE_COMPRESSED"      =>    "Compressé", //a file-type
"FTYPE_VECTOR"          =>    "Vectoriel", //a file-type
"FTYPE_FLASH"           =>    "Flash", //a file-type
"FTYPE_VIDEO"           =>    "Vidéo", //a file-type
"FTYPE_AUDIO"           =>    "Audio", //a file-type
"FTYPE_WEB"             =>    "Web", //a file-type
"FTYPE_DOCUMENTS"       =>    "Documents", //a file-type
"FTYPE_SYSTEM"          =>    "Système", //a file-type
"FTYPE_MISC"            =>    "Divers", //a file-type
"IMAGES"                =>    "Images",

/* 
 * For: login_functions.php
*/
"FILL_IN_REQ_FIELD"     =>   "Remplissez, s'il vous plaît, tous les champs obligatoires",
"LOGIN_FAILED"          =>   "Échec de la connexion. Vérifiez, s'il vous plaît, votre nom d'utilisateur et votre mot de passe",


/* 
 * For: MISC
*/
"DATE_FORMAT"			=>	"d/m/Y" //please keep short

);

?>