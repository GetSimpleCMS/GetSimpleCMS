<?php

/**
 * News Manager Greek language file by Kyriakos Tetepoulidis
 */


$i18n = array(

# general
"PLUGIN_NAME"         =>  "News Manager",

# error messages
"ERROR_ENV"           =>  "Υπήρξε ένα σφάλμα στην προσπέλαση του φακέλου των άρθων (/posts) και/ή στο config file. Ορίστε δικαιώματα <em>CHMOD 777</em> στον φάκελο /data, /backups και τους υποφακέλους του και ξαναπροσπαθήστε.",
"ERROR_SAVE"          =>  "<b>Σφάλμα:</b> Δεν ήταν δυνατή η αποθήκευση του άρθρου. Ορίστε δικαιώματα <em>CHMOD 777</em> στον φάκελο /data, /backups και τους υποφακέλους του και ξαναπροσπαθήστε.",
"ERROR_DELETE"        =>  "<b>Σφάλμα:</b> Δεν ήταν δυνατή η διαγραφή του άρθρου. Ορίστε δικαιώματα <em>CHMOD 777</em> στον φάκελο /data, /backups και τους υποφακέλους του και ξαναπροσπαθήστε.",
"ERROR_RESTORE"       =>  "<b>Σφάλμα:</b> Αδύνατη η ανάκτηση του post. ορίστε δικαιώματα <em>CHMOD 777</em> των φακέλων /data, /backups και των υποφακέλων τους και προσπαθήστε ξανά.",

# success messages
"SUCCESS_SAVE"        =>  "Οι αλλαγές έχουν αποθηκευτεί.",
"SUCCESS_DELETE"      =>  "Το άρθρο έχει διαγραφεί.",
"SUCCESS_RESTORE"     =>  "Το άρθρο έχει ανκτηθεί.",

# other messages
"UPDATE_HTACCESS"     =>  "<b>Σημείωση:</b> Πιθανόν πρέπει να ανανεώσετε το αρχείο <a href=\"%s\">.htaccess</a> !",

# admin button (top-right)
"NEWS_TAB"            =>  "News",
"SETTINGS"            =>  "Ρυθμίσεις",
"NEW_POST"            =>  "Δημιουργία Νέου Άρθρου",

# admin panel
"POST_TITLE"          =>  "Τίτλος Άρθρου",
"DATE"                =>  "Ημερομηνία",
"EDIT_POST"           =>  "Επεξεργασία Άρθρου",
"VIEW_POST"           =>  "Εμφάνιση Άρθρου",
"DELETE_POST"         =>  "Διαγραφή Άρθρου",
"POSTS"               =>  "Άρθρο (α)",

# edit settings
"NM_SETTINGS"         =>  "Ρυθμίσεις του News Manager",
"DOCUMENTATION"       =>  "Για περισότερες πληροφορίες των ρυθμίσεων, επισκευθείτε τη <a href=\"%s\" target=\"_blank\">σελίδα documentation</a>.",
"PAGE_URL"            =>  "Σελίδα που θα εμφανίζονται τα Άρθρα",
"NO_PAGE_SELECTED"    =>  "No page selected",
"LANGUAGE"            =>  "Γλώσσα που χρησιμοποιείται στη Σελίδα των Νέων",
"SHOW_POSTS_AS"       =>  "Τα Άρθρα στη σελίδα Άρθρων εμφανίζονται ως",
"FULL_TEXT"           =>  "Πλήρες Κείμενο",
"EXCERPT"             =>  "Περίληψη",
"PRETTY_URLS"         =>  "Χρησιμοποιείστε τα Fancy URLs για τα Άρθρα, Archives, κλπ.",
"PRETTY_URLS_NOTE"    =>  "Αν ενεργοποιήσετε τα Fancy URLs enabled, πρέπει να ενημερώσετε το αρχείο .htaccess μετά την αποθήκευση αυτών των ρυθμίσεων.",
"EXCERPT_LENGTH"      =>  "Μήκος περίληψης (σε χαρακτήρες)",
"POSTS_PER_PAGE"      =>  "Αριθμός άρθρων που θα εμφανίζονται στη σελίδα Άρθρα",
"RECENT_POSTS"        =>  "Αριθμός των Νεώτερων Άρθρων (στη Sidebar)",
"ENABLE_ARCHIVES"     =>  "Enable archives",
"BY_MONTH"            =>  "By month",
"BY_YEAR"             =>  "By year",
"READ_MORE_LINK"      =>  "Add \"read more\" link to excerpts",
"ALWAYS"              =>  "Always",
"NOT_SINGLE"          =>  "Yes, except in single post view",
"GO_BACK_LINK"        =>  "\"Go back\" link in single post view",
"TITLE_LINK"          =>  "Post Title links to Post",
"BROWSER_BACK"        =>  "Previously visited page",
"MAIN_NEWS_PAGE"      =>  "Main News Page",
"ENABLE_IMAGES"       =>  "Enable post images",
"IMAGE_LINKS"         =>  "Link images to posts",
"IMAGE_WIDTH"         =>  "Post image width (pixels)",
"IMAGE_HEIGHT"        =>  "Post image height (pixels)",
"FULL"                =>  "full",
"IMAGE_CROP"          =>  "Crop post images to fit width/height ratio",
"IMAGE_ALT"           =>  "Insert post title in post image <em>alt</em> attribute",
"CUSTOM_SETTINGS"     =>  "Custom settings",

# edit post
"POST_OPTIONS"        =>  "Επιλογές Άρθρου",
"POST_SLUG"           =>  "Slug/URL",
"POST_TAGS"           =>  "Tags (χωρίστε τα tags με κόμμα)",
"POST_DATE"           =>  "Ημερομηνία δημοσίευσης (<i>yyyy-mm-dd</i>)",
"POST_TIME"           =>  "Ώρα δημοσίευσης (<i>hh:mm</i>)",
"POST_PRIVATE"        =>  "Το Άρθρο είναι Ιδιωτικό (κρυφό)",
"POST_IMAGE"          =>  "Image",
"LAST_SAVED"          =>  "Τελευτία Αποθήκευση",

# validation
"FIELD_IS_REQUIRED"   => "Αυτό το πεδίο απαιτείται",
"ENTER_VALID_DATE"    => "Παρακαλώ εισάγετε μία έγκυρη ημερομηνία / Αφήστε κενό για την τρέχουσα ημερομηνία",
"ENTER_VALID_TIME"    => "Παρακαλώ εισάγετε μία έγκυρη ώρα / Αφήστε κενό για την τρέχουσα ώρα",
"ENTER_VALUE_MIN"     => "Παρακαλώ εισάγετε μια τιμή μεγαλύτερη ή ίση του %d",

# date picker - first day of week (0=Sunday, 1=Monday, ... 6=Saturday)
"DAY_OF_WEEK_START"   => "1",

# htaccess
"HTACCESS_HELP"       =>  "Για να ερνεργοποιήσετε τα Fancy URLs για τα Άρθρα, Αrchives, κλπ., αντικαταστήστε τα περιεχόμενα του αρχείου <code>.htaccess</code> με τις γραμμές παρκάτω.",
"GO_BACK_WHEN_DONE"   =>  "Όταν τελειώσετε με αυτή τη σελίδα, πατήστε το κουμπί παρακάτων για να πάτε στο main panel.",

# save/cancel/delete
"SAVE_SETTINGS"       =>  "Αποθήκευση Ρυθμίσεων",
"SAVE_POST"           =>  "Αποθήκευση Άρθρου",
"FINISHED"            =>  "Τελείωσε",
"CANCEL"              =>  "Ακύρωση",
"DELETE"              =>  "Διαγραφή",
"OR"                  =>  "ή",

# front-end/site
"FOUND"               =>  "Το παρακάτω Άρθρο δεν μπορεί να βρεθεί:",
"NOT_FOUND"           =>  "Με συγχωρείτε, η αναζήτησή σας δεν είχε αποτέλεσμα.",
"NOT_EXIST"           =>  "Το ζητούμενο άρθρο δεν υπάρχει.",
"NO_POSTS"            =>  "Δεν έχουν δημοσιευτίε άρθρα ακόμη.",
"PUBLISHED"           =>  "Δημοσιεύτηκε την ",
"TAGS"                =>  "Tags",
"OLDER_POSTS"         =>  "&larr; Παλαιότερα Άρθρα",
"NEWER_POSTS"         =>  "Νεώτερα Άρθρα &rarr;",
"SEARCH"              =>  "Αναζήτηση",
"GO_BACK"             =>  "&lt;&lt; Πίσω στην προηγούμενη σελίδα",
"ELLIPSIS"            =>  " [...] ",
"READ_MORE"           =>  "Read more",
"AUTHOR"              =>  "Author:",
"PREV_TEXT"           =>  "&lt;",
"PREV_TITLE"          =>  "Προηγούμενη σελ.",
"NEXT_TEXT"           =>  "&gt;",
"NEXT_TITLE"          =>  "Επόμενη σελ.",

# language localization
"LOCALE"              =>  "el_GR.utf8,el.utf8,el_GR.UTF-8,el.UTF-8,el_GR,el",

# date settings - list of available parameters: http://php.net/strftime
"DATE_FORMAT"         =>  "%e %b %Y",
"MONTHLY_FORMAT"      =>  "%B %Y",
"YEARLY_FORMAT"       =>  "%Y"

);
