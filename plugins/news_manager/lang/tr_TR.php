<?php

/**
 * News Manager Turkish language file by m[e]s - emresanli.com
 */


$i18n = array(

# general
"PLUGIN_NAME"         =>  "Haber Yönetimi",

# error messages
"ERROR_ENV"           =>  "Gönderilere erişimde bir sorun var. Lütfen /data, /backups klasörünü ve alt klasörlerini <em>CHMOD 777</em> yaparak tekrar deneyin.",
"ERROR_SAVE"          =>  "<b>Hata:</b> Değişiklikleriniz kaydedilemiyor. Lütfen /data, /backups klasörünü ve alt klasörlerini <em>CHMOD 777</em> yaparak tekrar deneyin.",
"ERROR_DELETE"        =>  "<b>Hata:</b> Haber silinemiyor. Lütfen /data, /backups klasörünü ve alt klasörlerini <em>CHMOD 777</em> yaparak tekrar deneyin.",
"ERROR_RESTORE"       =>  "<b>Hata:</b> Yazı geri yüklenemiyor. Lütfen /data ve /backups klasörlerinin ve içlerindeki tüm alt klasörlerin yazdırma seçeneğini <em>CHMOD 777</em> olarak ayarlayın ve tekrar deneyin.",

# success messages
"SUCCESS_SAVE"        =>  "Değişiklikleriniz kaydedildi.",
"SUCCESS_DELETE"      =>  "Haber başarıyla silindi.",
"SUCCESS_RESTORE"     =>  "Yazı geri yüklendi.", 

# other messages
"UPDATE_HTACCESS"     =>  "<b>Uyarı:</b> Büyük olasılıkla <a href=\"%s\">.htaccess</a> dosyasını güncellemeniz gerekiyor.",

# admin button (top-right)
"NEWS_TAB"            =>  "Haberler",
"SETTINGS"            =>  "Ayarlar",
"NEW_POST"            =>  "Yeni Haber Yaz",

# admin panel
"POST_TITLE"          =>  "Haber Başlığı",
"DATE"                =>  "Tarih",
"EDIT_POST"           =>  "Haberi Düzenle",
"VIEW_POST"           =>  "Haberi Görüntüle",
"DELETE_POST"         =>  "Haberi Sil",
"POSTS"               =>  "haber",

# edit settings
"NM_SETTINGS"         =>  "Haber Yönetim Ayarları",
"DOCUMENTATION"       =>  "Daha fazla bilgi için <a href=\"%s\" target=\"_blank\">News Manager</a> sayfasını ziyaret edin.",
"PAGE_URL"            =>  "Haberleri görüntüleme şablonu",
"NO_PAGE_SELECTED"    =>  "Hiçbir sayfa seçilmedi",
"LANGUAGE"            =>  "Haber sayfası dili:",
"SHOW_POSTS_AS"       =>  "Haberler şu şekilde görüntülenecek",
"FULL_TEXT"           =>  "Tam Yazı",
"EXCERPT"             =>  "Kısaltılmış",
"PRETTY_URLS"         =>  "Haberler ve arşivler için düzgün URL kullanılsın",
"PRETTY_URLS_NOTE"    =>  "Bunu etkinleştirirseniz, .htaccess dosyasını da güncellemeniz gerekecek.",
"EXCERPT_LENGTH"      =>  "Kısaltma boyutu (karakter)",
"POSTS_PER_PAGE"      =>  "Haber sayfasındaki girdi sayısı",
"RECENT_POSTS"        =>  "Yan paneldeki haber başlığı sayısı",
"ENABLE_ARCHIVES"     =>  "Arşivleri etkinleştir",
"BY_MONTH"            =>  "Aya göre",
"BY_YEAR"             =>  "Yıla göre",
"READ_MORE_LINK"      =>  "Kısa yazılara \"devamını oku\" bağlantısını ekle",
"ALWAYS"              =>  "Daima",
"NOT_SINGLE"          =>  "Evet, yazıları görüntülerken hariç",
"GO_BACK_LINK"        =>  "Yazıları görüntülerken \"Geri dön\" bağlantısı",
"TITLE_LINK"          =>  "Yazıya başlık bağlantısı",
"BROWSER_BACK"        =>  "Ziyaret edilen önceki sayfa",
"MAIN_NEWS_PAGE"      =>  "Ana Haber Sayfası",
"ENABLE_IMAGES"       =>  "Yazı görsellerini etkinleştir",
"IMAGE_LINKS"         =>  "Yazıya görsel bağlantıları",
"IMAGE_WIDTH"         =>  "Yazı görseli genişliği (piksel)",
"IMAGE_HEIGHT"        =>  "Yazı görseli yüksekliği (piksel)",
"FULL"                =>  "tam",
"IMAGE_CROP"          =>  "Yazı görsellerini genişlik/yükselik oranına uyacak şekilde kırp",
"IMAGE_ALT"           =>  "Yazı görselinin <em>alt</em> etiketine yazı başlığını ekle",
"CUSTOM_SETTINGS"     =>  "Özel ayarlar",

# edit post
"POST_OPTIONS"        =>  "Seçenekler",
"POST_SLUG"           =>  "Kısa Ad / URL",
"POST_TAGS"           =>  "Etiketler (virgülle ayırın)",
"POST_DATE"           =>  "Yayın tarihi (<i>yıl-ay-gün</i>)",
"POST_TIME"           =>  "Yayın zamanı (<i>saat:dakika</i>)",
"POST_PRIVATE"        =>  "Gizli haber",
"POST_IMAGE"          =>  "Resim",
"LAST_SAVED"          =>  "Son Kaydedilme Tarihi",

# validation
"FIELD_IS_REQUIRED"   =>  "Bu alanın girilmesi zorunlu",
"ENTER_VALID_DATE"    =>  "Lütfen geçerli bir tarih girin / Bugün için boş bırakın",
"ENTER_VALID_TIME"    =>  "Lütfen geçerli bir zaman girin / Şimdiki zaman için boş bırakın", 
"ENTER_VALUE_MIN"     =>  "Lütfen %d değerine eşit ya da daha büyük bir değer giriniz",

# date picker - first day of week (0=Sunday, 1=Monday, ... 6=Saturday)
"DAY_OF_WEEK_START"   => "1",

# htaccess
"HTACCESS_HELP"       =>  "Haberler ve arşivler için düzgün URL kullanmak isterseniz, kök dizindeki <code>.htaccess</code> kodlarını aşağıdakilerle değiştirin.",
"GO_BACK_WHEN_DONE"   =>  "Bu sayfayla işiniz bittiğinde, aşağıdaki düğmeye basarak ana panele geri dönebilirsiniz.",

# save/cancel/delete
"SAVE_SETTINGS"       =>  "Ayarları Kaydet",
"SAVE_POST"           =>  "Haberi Gönder",
"FINISHED"            =>  "Bitirdim",
"CANCEL"              =>  "İptal",
"DELETE"              =>  "Sil",
"OR"                  =>  "veya",

# front-end/site
"FOUND"               =>  "Şu haber bulundu:",
"NOT_FOUND"           =>  "Aramanızla ilgili herhangi bir sonuç bulunamadı.",
"NOT_EXIST"           =>  "Böyle bir haber yok.",
"NO_POSTS"            =>  "Henüz bir haber yayınlanmadı.",
"PUBLISHED"           =>  "Gönderim Tarihi:",
"TAGS"                =>  "Etiketler",
"OLDER_POSTS"         =>  "&larr; Eski Haberler",
"NEWER_POSTS"         =>  "Yeni Haberler &rarr;",
"SEARCH"              =>  "Arama",
"GO_BACK"             =>  "&lt;&lt; Geri Dön",
"ELLIPSIS"            =>  " [...] ",
"READ_MORE"           =>  "Devamını Oku",
"AUTHOR"              =>  "Yazar:",
"PREV_TEXT"           =>  "&lt;",
"PREV_TITLE"          =>  "Önceki sayfa",
"NEXT_TEXT"           =>  "&gt;",
"NEXT_TITLE"          =>  "Sonraki sayfa",

# language localization
"LOCALE"              =>  "tr_TR.utf8,tr.utf8,tr_TR.UTF-8,tr.UTF-8,tr_TR,trk,tr",

# date settings - list of available parameters: http://php.net/strftime
"DATE_FORMAT"         =>  "%d.%m.%Y",
"MONTHLY_FORMAT"      =>  "%B %Y",
"YEARLY_FORMAT"       =>  "%Y"

);
