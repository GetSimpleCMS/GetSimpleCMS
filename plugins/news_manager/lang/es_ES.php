<?php

/**
 * News Manager Spanish-Spain language file by Carlos Navarro
 */


$i18n = array(

# general
"PLUGIN_NAME"         =>  "News Manager",

# error messages
"ERROR_ENV"           =>  "Se ha producido un error al acceder a las carpetas de datos. Aplicar permisos <em>CHMOD 777</em> a las carpetas /data, /backups y sus subcarpetas, y reintentar.",
"ERROR_SAVE"          =>  "<b>Error:</b> No se ha podido guardar los cambios. Aplicar permisos <em>CHMOD 777</em> a las carpetas /data, /backups y sus subcarpetas, y reintentar.",
"ERROR_DELETE"        =>  "<b>Error:</b> No se ha podido eliminar la entrada. Aplicar permisos <em>CHMOD 777</em> a las carpetas /data, /backups y sus subcarpetas, y reintentar.",
"ERROR_RESTORE"       =>  "<b>Error:</b> No se ha podido restablecer la entrada. Aplicar permisos <em>CHMOD 777</em> a las carpetas /data, /backups y sus subcarpetas, y reintentar.",

# success messages
"SUCCESS_SAVE"        =>  "Se han guardado los cambios.",
"SUCCESS_DELETE"      =>  "La entrada ha sido eliminada.",
"SUCCESS_RESTORE"     =>  "La entrada ha sido restablecida.",

# other messages
"UPDATE_HTACCESS"     =>  "<b>Nota:</b> Probablemente se ha de modificar el archivo <a href=\"%s\">.htaccess</a> del sitio.",

# admin button (top-right)
"NEWS_TAB"            =>  "Noticias",
"SETTINGS"            =>  "Configuración",
"NEW_POST"            =>  "Entrada nueva",

# admin panel
"POST_TITLE"          =>  "Título de la entrada",
"DATE"                =>  "Fecha",
"EDIT_POST"           =>  "Editar entrada",
"VIEW_POST"           =>  "Ver entrada",
"DELETE_POST"         =>  "Eliminar entrada",
"POSTS"               =>  "entrada(s)",

# edit settings
"NM_SETTINGS"         =>  "Configuración de News Manager",
"DOCUMENTATION"       =>  "Para más información sobre la configuración, visitar la <a href=\"%s\" target=\"_blank\">página de documentación</a>.",
"PAGE_URL"            =>  "Página para mostrar entradas",
"NO_PAGE_SELECTED"    =>  "No se ha seleccionado ninguna página",
"LANGUAGE"            =>  "Idioma utilizado en la página de noticias",
"SHOW_POSTS_AS"       =>  "Mostrar las entradas como",
"FULL_TEXT"           =>  "Entrada completa",
"EXCERPT"             =>  "Extracto",
"PRETTY_URLS"         =>  "Usar URLs amigables para entradas, archivos, etc.",
"PRETTY_URLS_NOTE"    =>  "Si se activan las URLs amigables, además será necesario actualizar el archivo .htaccess ...",
"EXCERPT_LENGTH"      =>  "Longitud del extracto (en caracteres)",
"POSTS_PER_PAGE"      =>  "Número de entradas en la página de noticias",
"RECENT_POSTS"        =>  "Número de entradas recientes (en barra lateral)",
"ENABLE_ARCHIVES"     =>  "Habilitar archivos",
"BY_MONTH"            =>  "Por meses",
"BY_YEAR"             =>  "Por años",
"READ_MORE_LINK"      =>  "Añadir enlace \"leer más\" al final del extracto",
"ALWAYS"              =>  "Siempre",
"NOT_SINGLE"          =>  "Sí, excepto en entradas individuales",
"GO_BACK_LINK"        =>  "Enlace \"Volver...\" en entradas individuales",
"TITLE_LINK"          =>  "Enlace a la entrada en el título",
"BROWSER_BACK"        =>  "Página visitada anteriormente",
"MAIN_NEWS_PAGE"      =>  "Página principal de noticias",
"ENABLE_IMAGES"       =>  "Habilitar imágenes destacadas",
"IMAGE_LINKS"         =>  "Imágenes con enlace a la entrada",
"IMAGE_WIDTH"         =>  "Anchura de imagen (pixels)",
"IMAGE_HEIGHT"        =>  "Altura de imagen (pixels)",
"FULL"                =>  "completa",
"IMAGE_CROP"          =>  "Recortar imagen adaptándola al ancho / alto",
"IMAGE_ALT"           =>  "Insertar título en atributo <em>alt</em> de imagen",
"CUSTOM_SETTINGS"     =>  "Configuración personalizada",

# edit post
"POST_OPTIONS"        =>  "Opciones de la entrada",
"POST_SLUG"           =>  "Identificador (slug/URL)",
"POST_TAGS"           =>  "Etiquetas (separadas por comas)",
"POST_DATE"           =>  "Fecha de publicación (<i>aaaa-mm-dd</i>)",
"POST_TIME"           =>  "Hora de publicación (<i>hh:mm</i>)",
"POST_PRIVATE"        =>  "Entrada privada",
"POST_IMAGE"          =>  "Imagen",
"LAST_SAVED"          =>  "Guardada por última vez",

# validation
"FIELD_IS_REQUIRED"   => "Este campo es obligatorio",
"ENTER_VALID_DATE"    => "Introducir una fecha válida / Dejar vacío para fecha actual",
"ENTER_VALID_TIME"    => "Introducir una hora válida / Dejar vacío para hora actual",
"ENTER_VALUE_MIN"     => "Introducir un valor mayor o igual que %d",

# date picker - first day of week (0=Sunday, 1=Monday, ... 6=Saturday)
"DAY_OF_WEEK_START"   => "1",

# htaccess
"HTACCESS_HELP"       =>  "Para activar las URLs amigables para entradas, archivos, etc., reemplazar el contenido del archivo <code>.htaccess</code> por las líneas siguientes:",
"GO_BACK_WHEN_DONE"   =>  "Hacer clic en el botón de abajo para volver al panel principal.",

# save/cancel/delete
"SAVE_SETTINGS"       =>  "Guardar cambios",
"SAVE_POST"           =>  "Guardar entrada",
"FINISHED"            =>  "Finalizado",
"CANCEL"              =>  "Cancelar",
"DELETE"              =>  "Eliminar",
"OR"                  =>  "ó",

# front-end/site
"FOUND"               =>  "Se han encontrado las siguientes entradas:",
"NOT_FOUND"           =>  "La búsqueda no ha devuelto resultados.",
"NOT_EXIST"           =>  "La entrada solicitada no existe.",
"NO_POSTS"            =>  "No se han encontrado entradas.",
"PUBLISHED"           =>  "Publicada el",
"TAGS"                =>  "Etiquetas",
"OLDER_POSTS"         =>  "&larr; Más antiguas",
"NEWER_POSTS"         =>  "Más recientes &rarr;",
"SEARCH"              =>  "Buscar",
"GO_BACK"             =>  "&lt;&lt; Volver a la página anterior",
"ELLIPSIS"            =>  " [...] ",
"READ_MORE"           =>  "Leer más",
"AUTHOR"              =>  "Autor:",
"PREV_TEXT"           =>  "&lt;",
"PREV_TITLE"          =>  "Página anterior",
"NEXT_TEXT"           =>  "&gt;",
"NEXT_TITLE"          =>  "Página siguiente",

# language localization
"LOCALE"              =>  "es_ES.utf8,es.utf8,es_ES.UTF-8,es.UTF-8,es_ES,esp",

# date settings - list of available parameters: http://php.net/strftime
"DATE_FORMAT"         =>  "%d.%m.%Y",
"MONTHLY_FORMAT"      =>  "%B %Y",
"YEARLY_FORMAT"       =>  "%Y"

);
