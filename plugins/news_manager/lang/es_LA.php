<?php

/**
 * News Manager, archivo de idioma español por Joaquin Díaz
 */


$i18n = array(

# general
"PLUGIN_NAME"         =>  "News Manager",

# error messages
"ERROR_ENV"           =>  "Se ha producido un error al acceder a la carpeta de posts y/o el archivo de configuración. Dar permisos <em>CHMOD 777</em> a la carpetas /data, /backups y sus subcarpetas y vuelva a intentarlo.",
"ERROR_SAVE"          =>  "<b>Error:</b> No se puede guardar los cambios. Dar Permiso <em>CHMOD 777</em> a la carpetas /data, /backups y sus subcarpetas y vuelva a intentarlo.",
"ERROR_DELETE"        =>  "<b>Error:</b> No se puede eliminar el post. Dar Permiso <em>CHMOD 777</em> a la carpetas /data, /backups y sus subcarpetas y vuelva a intentarlo.",
"ERROR_RESTORE"       =>  "<b>Error:</b> Unable to restore the post. <em>CHMOD 777</em> the folders /data, /backups and their sub-folders and retry.",

# success messages
"SUCCESS_SAVE"        =>  "Los cambios han sido guardados.",
"SUCCESS_DELETE"      =>  "El post ha sido eliminado.",
"SUCCESS_RESTORE"     =>  "The post has been restored.",

# other messages
"UPDATE_HTACCESS"     =>  "<b>Nota:</b> Es probable que tenga que actualizar el archivo <a href=\"load.php?id=news_manager&amp;htaccess\">.htaccess</a>",

# admin button (top-right)
"SETTINGS"            =>  "Configuración",
"NEW_POST"            =>  "Crear Post",

# admin panel
"POST_TITLE"          =>  "Titulo del Post",
"DATE"                =>  "Fecha",
"EDIT_POST"           =>  "Editar Post",
"VIEW_POST"           =>  "Ver Post",
"DELETE_POST"         =>  "Eliminar Post",
"POSTS"               =>  "post(s)",

# edit settings
"NM_SETTINGS"         =>  "Configuración Gestor de Noticias",
"DOCUMENTATION"       =>  "Para obtener más información sobre estas opciones, visite la pagina de <a href=\"http://www.cyberiada.org/cnb/news-manager/\" target=\"_blank\">documentación</a>.",
"PAGE_URL"            =>  "Página para mostrar posts",
"LANGUAGE"            =>  "Idioma utilizado en la página de noticias",
"SHOW_POSTS_AS"       =>  "Posts en la página de noticias se muestran como",
"FULL_TEXT"           =>  "Texto completo",
"EXCERPT"             =>  "Extracto",
"PRETTY_URLS"         =>  "Usar Fancy URL para posts, archivos, etc",
"PRETTY_URLS_NOTE"    =>  "Si tiene activado Fancy URL, es posible que deba actualizar el archivo .htaccess después de guardar esta configuración.",
"EXCERPT_LENGTH"      =>  "Caracteres de extracto",
"POSTS_PER_PAGE"      =>  "Número de posts en página de noticias",
"RECENT_POSTS"        =>  "Número de post recientes (en la barra lateral)",

# edit post
"POST_OPTIONS"        =>  "Opciones del Post",
"POST_SLUG"           =>  "URL",
"POST_TAGS"           =>  "Tags (tags separados con comas)",
"POST_DATE"           =>  "Fecha de Publicación (<i>aaaa-mm-dd</i>)",
"POST_TIME"           =>  "Hora de Publicación (<i>hh:mm</i>)",
"POST_PRIVATE"        =>  "El post es privado",
"LAST_SAVED"          =>  "Último Guardado",

# htaccess
"HTACCESS_HELP"       =>  "Para activar Fancy URL para los posts, archivos, etc, reemplace el contenido de el <code>.htaccess</code> con las siguientes líneas.",
"GO_BACK_WHEN_DONE"   =>  "Cuando haya terminado con esta página, haga clic en el botón de abajo para volver al panel principal.",

# save/cancel/delete
"SAVE_SETTINGS"       =>  "Guardar configuración",
"SAVE_POST"           =>  "Guardar Post",
"FINISHED"            =>  "Terminado",
"CANCEL"              =>  "Cancelar",
"DELETE"              =>  "Eliminar",
"OR"                  =>  "o",

# front-end/site
"FOUND"               =>  "Los siguientes posts han sido encontrados:",
"NOT_FOUND"           =>  "Lo sentimos, no se ha encontrado lo que busca.",
"NOT_EXIST"           =>  "El post no existe.",
"NO_POSTS"            =>  "No hay post publicado todavia.",
"PUBLISHED"           =>  "Publicado el",
"TAGS"                =>  "Tags",
"OLDER_POSTS"         =>  "&larr; anterior",
"NEWER_POSTS"         =>  "siguiente &rarr;",
"SEARCH"              =>  "Buscar",
"GO_BACK"             =>  "&lt;&lt; Volver a la página anterior",
"ELLIPSIS"            =>  " [...]",

# language localization
"LOCALE"              =>  "es_ES.utf8,es.utf8,es_ES.UTF-8,es.UTF-8,es_ES,esp",

# date settings
"DATE_FORMAT"         =>  "%b %e, %Y"

);

?>