<?php
/****************************************************
*
* @File: 			pt_BR.php
* @Package:			GetSimple
* @Subject:			Brazilian portuguese language file
* @Date:			08 Sept 2009
* @Revision:		09 Sept 2009
* @Version:			GetSimple 1.6
* @Status:			Final
* @Traductors: 	    guhemama, lantonioli	
*
*****************************************************/

$i18n = array(

/* 
 * For: install.php
*/
"PHPVER_ERROR"      =>  "<b>Impossível continuar:</b> é necessário ter o PHP versão 5.1.3 ou superior, você deve ",
"SIMPLEXML_ERROR"   =>  "<b>Impossível continuar:</b> <em>SimpleXML</em> não está instalado",
"CURL_WARNING"      =>  "<b>Atenção:</b> <em>cURL</em> não est&aacute; instalado",
"TZ_WARNING"        =>  "<b>Atenção:</b> <em>date_default_timezone_set</em> não encontrado",
"WEBSITENAME_ERROR" =>  "<b>Erro:</b> há um problema com o título de seu website",
"WEBSITEURL_ERROR"  =>  "<b>Erro:</b> há um problema com a URL de seu website",
"USERNAME_ERROR"    =>  "<b>Erro:</b> nome de usuário não definido",
"EMAIL_ERROR"       =>  "<b>Erro:</b> há um problema com seu endereço de email",
"CHMOD_ERROR"       =>  "<b>Impossível continuar:</b> Não é possível gravar o arquivo de configuração. Aplique <em>CHMOD 777</em> nos diretórios /data/ e /backups/ e tente novamente",
"EMAIL_COMPLETE"    =>  "Instalação completa",
"EMAIL_USERNAME"    =>  "Seu nome de usuário é",
"EMAIL_PASSWORD"    =>  "Sua senha é",
"EMAIL_LOGIN"       =>  "Faça o login aqui",
"EMAIL_THANKYOU"    =>  "Obrigado por usar",
"NOTE_REGISTRATION" =>  "Suas informações de registro foram enviadas para",
"NOTE_REGERROR"     =>  "<b>Erro:</b> houve um problema ao enviar as informações de registro via email. Por favor anote a senha exibida abaixo",
"NOTE_USERNAME"     =>  "Seu nome de usuário é",
"NOTE_PASSWORD"     =>  "e sua senha é",
"INSTALLATION"      =>  "Instalação",
"LABEL_WEBSITE"     =>  "Nome do website",
"LABEL_BASEURL"     =>  "URL base do website",
"LABEL_SUGGESTION"  =>  "Nossa sugestão é",
"LABEL_USERNAME"    =>  "Nome de Usuário",
"LABEL_EMAIL"       =>  "Endereço de e-mail",
"LABEL_INSTALL"     =>  "Instalar agora!",

/* 
 * For: pages.php
*/
"MENUITEM_SUBTITLE" =>  "item do menu",
"HOMEPAGE_SUBTITLE" =>  "página inicial",
"PRIVATE_SUBTITLE"  =>  "privada",
"EDITPAGE_TITLE"    =>  "Editar página",
"VIEWPAGE_TITLE"    =>  "Visualizar página",
"DELETEPAGE_TITLE"  =>  "Excluir página",
"PAGE_MANAGEMENT"   =>  "Gerenciamento de Páginas",
"TOGGLE_STATUS"     =>  "Alternar status",
"TOTAL_PAGES"       =>  "páginas no total",
"ALL_PAGES"         =>  "Todas as páginas",

/* 
 * For: edit.php
*/
"PAGE_NOTEXIST"     =>  "A página requisitada não existe",
"BTN_SAVEPAGE"      =>  "Salvar página",
"BTN_SAVEUPDATES"   =>  "Salvar atualização",
"DEFAULT_TEMPLATE"  =>  "Tema padrão",
"NONE"              =>  "Nenhum",
"PAGE"              =>  "Página",
"NEW_PAGE"          =>  "Nova página",
"PAGE_EDIT_MODE"    =>  "Modo de edição de página",
"CREATE_NEW_PAGE"   =>  "Criar nova página",
"VIEW"              =>  "<em>V</em>isualizar", // 'v' es the accesskey identifier
"PAGE_OPTIONS"      =>  "<em>O</em>pções de página", // 'o' es the accesskey identifier
"TOGGLE_EDITOR"     =>  "Ativar/<em>d</em>esativar editor", // 'g' es the accesskey identifier
"SLUG_URL"          =>  "URL amigável/Link da página", //how to translate slug?
"TAG_KEYWORDS"      =>  "Tags &amp; palavras-chave",
"PARENT_PAGE"       =>  "Página-mãe",
"TEMPLATE"          =>  "Temas",
"KEEP_PRIVATE"      =>  "Manter privada?",
"ADD_TO_MENU"       =>  "Adicionar ao menu",
"PRIORITY"          =>  "Prioridade",
"MENU_TEXT"         =>  "Texto do menu",
"LABEL_PAGEBODY"    =>  "Conteúdo da página",
"CANCEL"            =>  "Cancelar",
"BACKUP_AVAILABLE"  =>  "Backup disponível",
"MAX_FILE_SIZE"     =>  "Tamanho máximo de arquivo",
"LAST_SAVED"        =>  "Salvo pela última vez",
"FILE_UPLOAD"       =>  "Envio de arquivos",
"OR"                =>  "ou",

/* 
 * For: upload.php
*/
"ERROR_UPLOAD"      =>  "Houve um problema no envio do arquivo",
"FILE_SUCCESS_MSG"  =>  "Envio realizado com successo! Localização do arquivo",
"FILE_MANAGEMENT"   =>  "Gerenciamento de Arquivos",
"UPLOADED_FILES"    =>  "Arquivos enviados",
"SHOW_ALL"          =>  "Mostrar todos",
"VIEW_FILE"         =>  "Visualizar arquivo",
"DELETE_FILE"       =>  "Apagar arquivo",
"TOTAL_FILES"       =>  "arquivos no total",

/* 
 * For: logout.php
*/
"LOGGED_OUT"        =>  "Até logo!",
"MSG_LOGGEDOUT"     =>  "Você fez o logout.",
"MSG_PLEASE"        =>  "Por favor, faça login novamente caso queira modificar o website",

/* 
 * For: index.php
*/
"LOGIN"             =>  "Login",
"USERNAME"          =>  "Nome de usuário",
"PASSWORD"          =>  "Senha",
"FORGOT_PWD"        =>  "Esqueceu a senha?",
"CONTROL_PANEL"     =>  "Painel de controle",
"LOGIN_REQUIREMENT" =>  "Requerimentos para acesso",
"WARN_JS_COOKIES"   =>  "Os cookies e o Javascript devem estar habilitados em seu navegador para que o website funcione corretamente",
"WARN_IE6"          =>  "Pode ser que o Internet Explorer 6 funcione, mas não há suporte para ele",

/* 
 * For: navigation.php
*/
"CURRENT_MENU"      =>  "Menu atual",
"NO_MENU_PAGES"     =>  "Não há páginas configuradas para aparecer no menu principal",

/* 
 * For: theme-edit.php
*/
"TEMPLATE_FILE"     =>  "O arquivo de tema <b>%s</b> foi atualizado com sucesso",
"THEME_MANAGEMENT"  =>  "Gerenciamento de Temas",
"EDIT_THEME"        =>  "Editar Tema",
"EDITING_FILE"      =>  "Editando arquivo",
"BTN_SAVECHANGES"   =>  "Salvar modificações",
"EDIT"              =>  "Editar",

/* 
 * For: support.php
*/
"SETTINGS_UPDATED"  =>  "Suas configurações foram atualizadas",
"UNDO"              =>  "Desfazer",
"SUPPORT"           =>  "Suporte",
"SETTINGS"          =>  "Configurações",
"ERROR"             =>  "Erro",
"BTN_SAVESETTINGS"  =>  "Salvar configurações",
"EMAIL_ON_404"      =>  "Enviar email ao administrador quando ocorrerem erros 404",
"VIEW_404"          =>  "Visualizar erros 404",
"VIEW_FAILED_LOGIN" =>  "Visualizar tentativas fracassadas de acesso",
"VIEW_CONTACT_FORM" =>  "Visualizar envios do formulário de contato",
"VIEW_TICKETS"      =>  "Visualizar tickets enviados",

/* 
 * For: log.php
*/
"MSG_HAS_BEEN_CLR"  =>  "foi apagado",
"LOGS"              =>  "Logs",
"VIEWING"           =>  "Visualizando",
"LOG_FILE"          =>  "Arquivo de Log",
"CLEAR_ALL_DATA"    =>  "Apagar todos os dados de",
"CLEAR_THIS_LOG"    =>  "Apagar este Log (<em>C</em>)", // 'c' is the accesskey identifier
"LOG_FILE_ENTRY"    =>  "ENTRADA DO LOG",
"THIS_COMPUTER"     =>  "Este computador",

/* 
 * For: backup-edit.php
*/
"BAK_MANAGEMENT"    =>  "Gerenciamento de Backups",
"ASK_CANCEL"        =>  "<em>C</em>ancelar", // 'c' is the accesskey identifier
"ASK_RESTORE"       =>  "<em>R</em>estaurar", // 'r' is the accesskey identifier
"ASK_DELETE"        =>  "<em>D</em>eletar", // 'd' is the accesskey identifier
"BACKUP_OF"         =>  "Backup de",
"PAGE_TITLE"        =>  "Título da página",
"YES"               =>  "Sim",
"NO"                =>  "Não",
"DATE"              =>  "Data",

/* 
 * For: components.php
*/
"COMPONENTS"        =>  "Componentes",
"DELETE_COMPONENT"  =>  "Apagar componente",
"EDIT"              =>  "Editar",
"ADD_COMPONENT"     =>  "<em>A</em>dicionar componente", // 'a' is the accesskey identifier
"SAVE_COMPONENTS"   =>  "Salvar componentes",

/* 
 * For: sitemap.php
*/
"SITEMAP_CREATED"   =>  "Mapa do website criado! Também enviamos ping desta atualização para quatro websites de busca",
"SITEMAP_ERRORPING" =>  "Mapa do website criado, no entando ocorreu um erro ao enviar ping desta atualização para um ou mais websites de busca",
"SITEMAP_ERROR"     =>  "O mapa do website não pode ser criado",
"SITEMAP_WAIT"      =>  "<b>Por favor, aguarde:</b> criando mapa do website",

/* 
 * For: theme.php
*/
"THEME_CHANGED"     =>  "Seu tema foi modificado com sucesso",
"CHOOSE_THEME"      =>  "Escolher Tema",
"ACTIVATE_THEME"    =>  "Ativar tema",
"THEME_SCREENSHOT"  =>  "Imagem do tema",
"THEME_PATH"        =>  "Local do Tema",

/* 
 * For: resetpassword.php
*/
"RESET_PASSWORD"    =>  "Resetar senha",
"YOUR_NEW"          =>  "Sua nova",
"PASSWORD_IS"       =>  "senha é",
"ATTEMPT"           =>  "Tentativa",
"MSG_PLEASE_EMAIL"  =>  "Por favor, digite o e-mail registrado no sistema e uma nova senha será enviada para você",
"SEND_NEW_PWD"      =>  "Enviar nova senha",

/* 
 * For: settings.php
*/
"GENERAL_SETTINGS"  =>  "Configurações Gerais",
"WEBSITE_SETTINGS"  =>  "Configurações do Website",
"LOCAL_TIMEZONE"    =>  "Fuso-horário",
"LANGUAGE"          =>  "Idioma",
"USE_FANCY_URLS"    =>  "<b>Usar URLs amigáveis</b> - É necessário que seu serviço de hospedagem esteja com mod_rewrite habilitado",
"ENABLE_HTML_ED"    =>  "<b>Habilitar o editor HTML</b>",
"USER_SETTINGS"     =>  "Configurações de acesso do usuário",
"WARN_EMAILINVALID" =>  "Advertência: este e-mail não parece ser válido!",
"ONLY_NEW_PASSWORD" =>  "Só digite algo abaixo caso você deseje modificar",
"NEW_PASSWORD"      =>  "Nova senha",
"CONFIRM_PASSWORD"  =>  "Confirme a senha",
"PASSWORD_NO_MATCH" =>  "As senhas não conferem",

/* 
 * For: contactform.php
*/
"MSG_CAPTCHA_FAILED" =>  "Erro de Captcha! Assim vamos pensar que você é um spambot!",
"CONTACT_FORM_SUB"  =>  "Envio de formulário de contato",
"FROM"              =>  "de",
"MSG_CONTACTSUC"    =>  "Sua mensagem foi enviada com sucesso",
"MSG_CONTACTERR"    =>  "Houve um erro ao enviar sua mensagem",

/* 
 * For: 404-mailer.php
*/
"404_ENCOUNTERED"   =>  "Auto: Erro 404 encontrado em",
"404_AUTO_MSG"      =>  "Esta é uma mensagem automática do seu website",
"PAGE_CANNOT_FOUND" =>  "Um erro de 'Página Não-encontrada' foi encontrado no",
"DOMAIN"            =>  "domínio",
"DETAILS"           =>  "DETALHES",
"WHEN"              =>  "Quando",
"WHO"               =>  "Quem",
"FAILED_PAGE"       =>  "Página com erro",
"REFERRER"          =>  "Referrer",
"BROWSER"           =>  "Navegador",

/* 
 * For: health-check.php
*/
"WEB_HEALTH_CHECK"  =>  "Check-up do website",
"VERSION"           =>  "- versão",
"UPG_NEEDED"        =>  "Atualização necessária para",
"CANNOT_CHECK"      =>  "Impossível checar. Sua versão é",
"LATEST_VERSION"    =>  "Última versão instalada",
"SERVER_SETUP"      =>  "Configuração do Servidor",
"OR_GREATER_REQ"    =>  "ou superior é necessária",
"OK"                =>  "OK",
"INSTALLED"         =>  "Instalado",
"NOT_INSTALLED"     =>  "Não instalado",
"WARNING"           =>  "Aviso",
"DATA_FILE_CHECK"   =>  "Check-up dos arquivos de dados",
"DIR_PERMISSIONS"   =>  "Permissões de diretório",
"EXISTANCE"         =>  "%s - existência",
"MISSING_FILE"      =>  "Arquivo perdido",
"BAD_FILE"          =>  "Arquivo corrompido",
"NO_FILE"           =>  "Sem arquivo",
"GOOD_D_FILE"       =>  "'Negação' correta",
"GOOD_A_FILE"       =>  "'Permissão' correta",
"CANNOT_DEL_FILE"   =>  "Impossível apagar arquivo",
"DOWNLOAD"          =>  "Download",
"WRITABLE"          =>  "Gravável",
"NOT_WRITABLE"      =>  "Não-gravável",

/* 
 * For: footer.php
*/
"POWERED_BY"        =>  "Powered by",
"PRODUCTION"        =>  "Production",
"SUBMIT_TICKET"     =>  "Enviar ticket",

/* 
 * For: backups.php
*/
"PAGE_BACKUPS"      =>  "Backups de Página",
"ASK_DELETE_ALL"    =>  "<em>D</em>eletar tudo",
"DELETE_ALL_BAK"    =>  "Apagar todos os backups?",
"TOTAL_BACKUPS"     =>  "backups no total",

/* 
 * For: archive.php
*/
"SUCC_WEB_ARCHIVE"  =>  "Um arquivamento do website foi realizado com sucesso!",
"SUCC_WEB_ARC_DEL"  =>  "Arquivo do website apagado com sucesso",
"WEBSITE_ARCHIVES"  =>  "Arquivos do Website",
"ARCHIVE_DELETED"   =>  "Arquivo apagado com sucesso",
"CREATE_NEW_ARC"    =>  "Criar um novo arquivo",
"ASK_CREATE_ARC"    =>  "<em>C</em>riar um novo arquivo agora",
"CREATE_ARC_WAIT"   =>  "<b>Por favor, aguarde:</b> criando arquivo do website...",
"DOWNLOAD_ARCHIVES" =>  "Fazer download do arquivo",
"DELETE_ARCHIVE"    =>  "Apagar arquivo",
"TOTAL_ARCHIVES"    =>  "arquivos no total",

/* 
 * For: include-nav.php
*/
"WELCOME"           =>  "Bem-vindo(a)", // used as 'Welcome USERNAME!'
"TAB_PAGES"         =>  "<em>P</em>áginas",
"TAB_FILES"         =>  "Arquivos <em>F</em>",
"TAB_THEME"         =>  "<em>T</em>emas",
"TAB_BACKUPS"       =>  "<em>B</em>ackups",
"TAB_SETTINGS"      =>  "Configuraçõe<em>s</em>",
"TAB_SUPPORT"       =>  "Sup<em>o</em>rte",
"TAB_LOGOUT"        =>  "<em>L</em>ogout",

/* 
 * For: sidebar-files.php
*/
"BROWSE_COMPUTER"   =>  "Procurar no computador",
"UPLOAD"            =>  "Enviar",

/* 
 * For: sidebar-support.php
*/
"SIDE_SUPPORT_LOG"   =>  "Configurações de Sup<em>o</em>rte",
"SIDE_VIEW_LOG"      =>  "Visualizar Log",
"SIDE_HEALTH_CHK"    =>  "C<em>h</>ecar saúde do website",
"SIDE_SUBMIT_TICKET" =>  "Enviar tic<em>k</em>et",
"SIDE_DOCUMENTATION" =>  "<em>D</em>ocumentação",

/* 
 * For: sidebar-theme.php
*/
"SIDE_VIEW_SITEMAP" =>  "<em>V</em>isualizar mapa do website",
"SIDE_GEN_SITEMAP"  =>  "<em>G</em>erar mapa do website",
"SIDE_COMPONENTS"   =>  "<em>E</em>ditar componentes",
"SIDE_EDIT_THEME"   =>  "Editar <em>t</em>ema",
"SIDE_CHOOSE_THEME" =>  "Escolher <em>t</em>ema",

/* 
 * For: sidebar-pages.php
*/
"SIDE_CREATE_NEW"   =>  "<em>C</em>riar nova página",
"SIDE_VIEW_PAGES"   =>  "Visualizar todas as <em>p</em>áginas",

/* 
 * For: sidebar-pages.php
*/
"SIDE_GEN_SETTINGS" =>  "Configuraçõe<em>s</em> Gerais",
"SIDE_USER_PROFILE" =>  "Perfil de <em>U</em>suário",

/* 
 * For: sidebar-pages.php
*/
"SIDE_VIEW_BAK"     =>  "Visualizar backup da página",
"SIDE_WEB_ARCHIVES" =>  "Arquivos do <em>w</em>ebsite",
"SIDE_PAGE_BAK"     =>  "<em>B</em>ackups de página",

/* 
 * For: error_checking.php
*/
"ER_PWD_CHANGE"     =>  "Não esqueça de <a href=\"settings.php#profile\">mudar sua senha</a> para algo diferente da que foi automaticamente gerada agora...",
"ER_BAKUP_DELETED"  =>  "O backup de %s foi apagado",
"ER_REQ_PROC_FAIL"  =>  "O processo requisitado falhou",
"ER_YOUR_CHANGES"   =>  "Suas modificações de %s foram salvas",
"ER_HASBEEN_REST"   =>  "%s foi restaurado",
"ER_HASBEEN_DEL"    =>  "%s foi apagado",
"ER_CANNOT_INDEX"   =>  "Você não pode mudar a URL da página inicial",
"ER_SETTINGS_UPD"   =>  "Suas configurações não foram atualizadas",
"ER_OLD_RESTORED"   =>  "Suas configurações anteriores foram restauradas",
"ER_NEW_PWD_SENT"   =>  "Uma nova senha foi enviada para o e-mail fornecido",
"ER_SENDMAIL_ERR"   =>  "Houve um problema ao enviar o e-mail. Por favor, tente novamente",
"ER_FILE_DEL_SUC"   =>  "Arquivo apagado com sucesso",
"ER_PROBLEM_DEL"    =>  "Houve um problema ao apagar o arquivo",
"ER_COMPONENT_SAVE" =>  "Seus componentes foram salvos",
"ER_COMPONENT_REST" =>  "Seus componentes foram restaurados",
"ER_CANCELLED_FAIL" =>  "<b>Cancelado:</b> a atualização deste arquivo foi cancelada",

/* 
 * For: changedata.php
*/
"CANNOT_SAVE_EMPTY" =>  "Você não pode salvar uma página em branco",

/* 
 * For: template_functions.php
*/
"FTYPE_COMPRESSED"  =>  "Comprimido", //a file-type
"FTYPE_VECTOR"      =>  "Vetor", //a file-type
"FTYPE_FLASH"       =>  "Flash", //a file-type
"FTYPE_VIDEO"       =>  "Vídeo", //a file-type
"FTYPE_AUDIO"       =>  "Áudio", //a file-type
"FTYPE_WEB"         =>  "Web", //a file-type
"FTYPE_DOCUMENTS"   =>  "Documentos", //a file-type
"FTYPE_SYSTEM"      =>  "Sistema", //a file-type
"FTYPE_MISC"        =>  "Miscelânea", //a file-type
"IMAGES"            =>  "Imagens",

/* 
 * For: login_functions.php
*/
"FILL_IN_REQ_FIELD" =>  "Por favor, preencha todos os campos obrigatórios",
"LOGIN_FAILED"      =>  "Não foi possível efetuar o login. Por favor, cheque seu nome de usuário e senha",

/* 
 * For: Date Format
*/
"DATE_FORMAT"       =>  "d/m/Y" //please keep short


);

?>