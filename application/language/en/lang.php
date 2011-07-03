<?php
/**
 * This file is part of MySQLDumper released under the GNU/GPL 2 license
  * http://www.mysqldumper.net
 *
 * @package       MySQLDumper
 * @subpackage    Language
 * @version       $Rev$
 * @author        $Author$
  */
$lang=array(
    'L_ACTION' => "Action",
    'L_ACTIVATED' => "activated",
    'L_ACTUALLY_INSERTED_RECORDS' => "Up to now <b>%s</b> records were"
    ." successfully added.",
    'L_ACTUALLY_INSERTED_RECORDS_OF' => "Up to now  <b>%s</b> of <b>%s</b>"
    ." records were successfully added.",
    'L_ADD' => "Add",
    'L_ADDED' => "added",
    'L_ADD_DB_MANUALLY' => "Add database manually",
    'L_ADD_RECIPIENT' => "Add recipient",
    'L_ALL' => "all",
    'L_ANALYZE' => "Analyze",
    'L_ANALYZING_TABLE' => "Now data of the table '<b>%s</b>' is"
    ." being analyzed.",
    'L_ASKDBCOPY' => "Do you  want to copy database `%s` to"
    ." database `%s`?",
    'L_ASKDBDELETE' => "Do you want to delete the Database"
    ." `%s` with the content?",
    'L_ASKDBEMPTY' => "Do you want to empty the Database `%s`"
    ." ?",
    'L_ASKDELETEFIELD' => "Do you want to delete the Field?",
    'L_ASKDELETERECORD' => "Are you sure to delete this record?",
    'L_ASKDELETETABLE' => "Should the table `%s` be deleted?",
    'L_ASKTABLEEMPTY' => "Should the table `%s` be emptied?",
    'L_ASKTABLEEMPTYKEYS' => "Should the table `%s` be emptied and"
    ." the Indices reset?",
    'L_ATTACHED_AS_FILE' => "attached as file",
    'L_ATTACH_BACKUP' => "Attach backup",
    'L_AUTHENTICATE' => "Login information",
    'L_AUTHORIZE' => "Authorize",
    'L_AUTODELETE' => "Delete backups automatically",
    'L_BACK' => "back",
    'L_BACKUPFILESANZAHL' => "In the Backup directory there are",
    'L_BACKUPS' => "Backups",
    'L_BACKUP_DBS' => "DBs to backup",
    'L_BACKUP_TABLE_DONE' => "Dumping of table `%s` finished. %s"
    ." records have been saved.",
    'L_BACK_TO_OVERVIEW' => "Database Overview",
    'L_CALL' => "Call",
    'L_CANCEL' => "Cancel",
    'L_CANT_CREATE_DIR' => "Couldn' t create dir '%s'. 
Please"
    ." create it using your FTP program.",
    'L_CHANGE' => "change",
    'L_CHANGEDIR' => "Changing to Directory",
    'L_CHANGEDIRERROR' => "Couldn`t change directory!",
    'L_CHARSET' => "Charset",
    'L_CHECK' => "Check",
    'L_CHECK_DIRS' => "Check my directories",
    'L_CHOOSE_CHARSET' => "MySQLDumper couldn't detect the"
    ." encoding of the backup file"
    ." automatically.
<br />You must choose"
    ." the charset with which this backup was"
    ." saved.
<br />If you discover any"
    ." problems with some characters after"
    ." restoring, you can repeat the"
    ." backup-progress and then choose"
    ." another character set.
<br />Good"
    ." luck. ;)",
    'L_CHOOSE_DB' => "Select Database",
    'L_CLEAR_DATABASE' => "Clear database",
    'L_CLOSE' => "Close",
    'L_COLLATION' => "Collation",
    'L_COMMAND' => "Command",
    'L_COMMAND_AFTER_BACKUP' => "Command after backup",
    'L_COMMAND_BEFORE_BACKUP' => "Command before backup",
    'L_COMMENT' => "Comment",
    'L_COMPRESSED' => "compressed (gz)",
    'L_CONFBASIC' => "Basic Parameter",
    'L_CONFIG' => "Configuration",
    'L_CONFIGFILE' => "Config File",
    'L_CONFIGFILES' => "Configuration Files",
    'L_CONFIGURATIONS' => "Configurations",
    'L_CONFIG_AUTODELETE' => "Autodelete",
    'L_CONFIG_CRONPERL' => "Crondump Settings for Perl script",
    'L_CONFIG_EMAIL' => "Email Notification",
    'L_CONFIG_FTP' => "FTP Transfer of Backup file",
    'L_CONFIG_HEADLINE' => "Configuration",
    'L_CONFIG_INTERFACE' => "Interface",
    'L_CONFIG_LOADED' => "Configuration \"%s\" has been imported"
    ." successfully.",
    'L_CONFIRM_CONFIGFILE_DELETE' => "Really delete the configuration file"
    ." %s?",
    'L_CONFIRM_DELETE_FILE' => "Should the file '%s' really be"
    ." deleted?",
    'L_CONFIRM_DELETE_TABLES' => "Really delete the selected tables?",
    'L_CONFIRM_DROP_DATABASES' => "Should the selected databases really"
    ." be deleted?

Attention: all data will"
    ." be deleted! Maybe you should create a"
    ." backup first.",
    'L_CONFIRM_RECIPIENT_DELETE' => "Should the recipient \"%s\" really be"
    ." deleted?",
    'L_CONFIRM_TRUNCATE_DATABASES' => "Should all tables of the selected"
    ." databases really be"
    ." deleted?

Attention: all data will be"
    ." deleted! Maybe you want to create a"
    ." backup first.",
    'L_CONFIRM_TRUNCATE_TABLES' => "Really empty the selected tables?",
    'L_CONNECT' => "connect",
    'L_CONNECTIONPARS' => "Connection Parameter",
    'L_CONNECTTOMYSQL' => "Connect to MySQL",
    'L_CONTINUE_MULTIPART_RESTORE' => "Continue Multipart-Restore with next"
    ." file '%s'.",
    'L_CONVERTED_FILES' => "Converted Files",
    'L_CONVERTER' => "Backup Converter",
    'L_CONVERTING' => "Converting",
    'L_CONVERT_FILE' => "File to be converted",
    'L_CONVERT_FILENAME' => "Name of destination file (without"
    ." extension)",
    'L_CONVERT_FILEREAD' => "Read file '%s'",
    'L_CONVERT_FINISHED' => "Conversion finished, '%s' was written"
    ." successfully.",
    'L_CONVERT_START' => "Start Conversion",
    'L_CONVERT_TITLE' => "Convert Dump to MSD Format",
    'L_CONVERT_WRONG_PARAMETERS' => "Wrong parameters!  Conversion is not"
    ." possible.",
    'L_CREATE' => "Create",
    'L_CREATEAUTOINDEX' => "Create Auto-Index",
    'L_CREATED' => "Created",
    'L_CREATEDIRS' => "Create Directories",
    'L_CREATE_CONFIGFILE' => "Create a new configuration file",
    'L_CREATE_DATABASE' => "Create new database",
    'L_CREATE_TABLE_SAVED' => "Definition of table `%s` saved.",
    'L_CREDITS' => "Credits / Help",
    'L_CRONSCRIPT' => "Cronscript",
    'L_CRON_COMMENT' => "Enter Comment",
    'L_CRON_COMPLETELOG' => "Log complete output",
    'L_CRON_EXECPATH' => "Path of Perl scripts",
    'L_CRON_EXTENDER' => "File extension",
    'L_CRON_PRINTOUT' => "Print output on screen.",
    'L_CSVOPTIONS' => "CSV Options",
    'L_CSV_EOL' => "Seperate lines with",
    'L_CSV_ERRORCREATETABLE' => "Error while creating table `%s` !",
    'L_CSV_FIELDCOUNT_NOMATCH' => "The count of fields doesn't match with"
    ." that of the data to import (%d instead"
    ." of %d).",
    'L_CSV_FIELDSENCLOSED' => "Fields enclosed by",
    'L_CSV_FIELDSEPERATE' => "Fields separated with",
    'L_CSV_FIELDSESCAPE' => "Fields escaped with",
    'L_CSV_FIELDSLINES' => "%d fields recognized, totally %d lines",
    'L_CSV_FILEOPEN' => "Open CSV file",
    'L_CSV_NAMEFIRSTLINE' => "Field names in first line",
    'L_CSV_NODATA' => "No data found for import!",
    'L_CSV_NULL' => "Replace NULL with",
    'L_DATABASES_OF_USER' => "Databases of user",
    'L_DATASIZE' => "Size of data",
    'L_DATASIZE_INFO' => "This is the size of the records - not"
    ." the size of the backup file",
    'L_DAY' => "Day",
    'L_DAYS' => "Days",
    'L_DB' => "Database",
    'L_DBCONNECTION' => "Database Connection",
    'L_DBPARAMETER' => "Database Parameters",
    'L_DBS' => "Databases",
    'L_DB_ADAPTER' => "DB-Adapter",
    'L_DB_BACKUPPARS' => "Database Backup Parameter",
    'L_DB_HOST' => "Hostname",
    'L_DB_IN_LIST' => "The database '%s' couldn't be added"
    ." because it is allready existing.",
    'L_DB_NAME' => "Database name",
    'L_DB_PASS' => "Password",
    'L_DB_SELECT_ERROR' => "<br />Error:<br />Selection of"
    ." database <b>",
    'L_DB_SELECT_ERROR2' => "</b> failed!",
    'L_DB_USER' => "User",
    'L_DEFAULT_CHARACTER_SET_NAME' => "Default character set",
    'L_DEFAULT_CHARSET' => "Default character set",
    'L_DEFAULT_COLLATION_NAME' => "Default collation",
    'L_DELETE' => "Delete",
    'L_DELETE_DATABASE' => "Delete database",
    'L_DELETE_FILE_ERROR' => "Error deleting file \"%s\"!",
    'L_DELETE_FILE_SUCCESS' => "File \"%s\" was deleted successfully.",
    'L_DELETE_HTACCESS' => "Remove directory protection (delete"
    ." .htaccess)",
    'L_DESELECTALL' => "Deselect all",
    'L_DIR' => "Directory",
    'L_DISABLEDFUNCTIONS' => "Disabled Functions",
    'L_DO' => "Execute",
    'L_DOCRONBUTTON' => "Run the Perl Cron script",
    'L_DONE' => "Done!",
    'L_DONT_ATTACH_BACKUP' => "Don't attach backup",
    'L_DOPERLTEST' => "Test Perl Modules",
    'L_DOSIMPLETEST' => "Test Perl",
    'L_DOWNLOAD_FILE' => "Download file",
    'L_DO_NOW' => "operate now",
    'L_DUMP' => "Backup",
    'L_DUMP_ENDERGEBNIS' => "The file contains <b>%s</b> tables"
    ." with <b>%s</b> records.<br />",
    'L_DUMP_FILENAME' => "Backup File",
    'L_DUMP_HEADLINE' => "Create backup...",
    'L_DUMP_NOTABLES' => "No tables found in database `%s`",
    'L_DUMP_OF_DB_FINISHED' => "Dumping of database `%s` done",
    'L_DURATION' => "Duration",
    'L_EDIT' => "edit",
    'L_EHRESTORE_CONTINUE' => "continue and log errors",
    'L_EHRESTORE_STOP' => "stop",
    'L_EMAIL' => "E-Mail",
    'L_EMAILBODY_ATTACH' => "The Attachment contains the backup of"
    ." your MySQL-Database.<br />Backup of"
    ." Database `%s`
<br /><br />Following"
    ." File was created:<br /><br />%s <br"
    ." /><br />Kind regards<br /><br"
    ." />MySQLDumper<br />",
    'L_EMAILBODY_FOOTER' => "`<br /><br />Kind regards<br /><br"
    ." />MySQLDumper<br />",
    'L_EMAILBODY_MP_ATTACH' => "A Multipart Backup was created.<br"
    ." />The Backup files are attached to"
    ." separate emails.<br />Backup of"
    ." Database `%s`
<br /><br />Following"
    ." Files were created:<br /><br />%s <br"
    ." /><br />Kind regards<br /><br"
    ." />MySQLDumper<br />",
    'L_EMAILBODY_MP_NOATTACH' => "A Multipart Backup was created.<br"
    ." />The Backup files are not attached to"
    ." this email!<br />Backup of Database"
    ." `%s`
<br /><br />Following Files were"
    ." created:<br /><br />%s
<br /><br"
    ." />Kind regards<br /><br"
    ." />MySQLDumper<br />",
    'L_EMAILBODY_NOATTACH' => "Files are not attached to this"
    ." email!<br />Backup of Database"
    ." `%s`
<br /><br />Following File was"
    ." created:<br /><br />%s
<br /><br"
    ." />Kind regards<br /><br"
    ." />MySQLDumper<br />",
    'L_EMAILBODY_TOOBIG' => "The Backup file exceeded the maximum"
    ." size of %s and was not attached to"
    ." this email.<br />Backup of Database"
    ." `%s`
<br /><br />Following File was"
    ." created:<br /><br />%s
<br /><br"
    ." />Kind regards<br /><br"
    ." />MySQLDumper<br />",
    'L_EMAIL_ADDRESS' => "E-Mail-Address",
    'L_EMAIL_CC' => "CC-Receiver",
    'L_EMAIL_MAXSIZE' => "Maximum size of attachment",
    'L_EMAIL_ONLY_ATTACHMENT' => "... attachment only.",
    'L_EMAIL_RECIPIENT' => "Receiver",
    'L_EMAIL_SENDER' => "Sender address of the email",
    'L_EMAIL_START' => "Starting to send e-mail",
    'L_EMAIL_WAS_SEND' => "Email was successfully sent to",
    'L_EMPTY' => "Empty",
    'L_EMPTYKEYS' => "empty and reset indexes",
    'L_EMPTYTABLEBEFORE' => "Empty table before",
    'L_EMPTY_DB_BEFORE_RESTORE' => "Delete tables before restoring",
    'L_ENCODING' => "encoding",
    'L_ENCRYPTION_TYPE' => "Kind of encrypting",
    'L_ENGINE' => "Engine",
    'L_ENTER_DB_INFO' => "First click the button \"Connect to"
    ." MySQL\". Only if no database could be"
    ." detected you need to provide a"
    ." database name here.",
    'L_ENTRY' => "Entry",
    'L_ERROR' => "Error",
    'L_ERRORHANDLING_RESTORE' => "Error Handling while restoring",
    'L_ERROR_CONFIGFILE_NAME' => "Filename \"%s\" contains invalid"
    ." characters.",
    'L_ERROR_DELETING_CONFIGFILE' => "Error: couldn't delete configuration"
    ." file %s!",
    'L_ERROR_LOADING_CONFIGFILE' => "Couldn't load configfile \"%s\".",
    'L_ERROR_LOG' => "Error Log",
    'L_ERROR_MULTIPART_RESTORE' => "Multipart-Restore: couldn't finde the"
    ." next file '%s'!",
    'L_ESTIMATED_END' => "Estimated end",
    'L_EXCEL2003' => "Excel from 2003",
    'L_EXISTS' => "Exists",
    'L_EXPORT' => "Export",
    'L_EXPORTFINISHED' => "Export finished.",
    'L_EXPORTLINES' => "<strong>%s</strong> lines exported",
    'L_EXPORTOPTIONS' => "Export Options",
    'L_EXTENDEDPARS' => "Extended Parameter",
    'L_FADE_IN_OUT' => "Display on/off",
    'L_FATAL_ERROR_DUMP' => "Fatal error: the CREATE-Statement of"
    ." table '%s' in database '%s' couldn't"
    ." be read!",
    'L_FIELDS' => "Fields",
    'L_FIELDS_OF_TABLE' => "Fields of table",
    'L_FILE' => "File",
    'L_FILES' => "Files",
    'L_FILESIZE' => "File size",
    'L_FILE_MANAGE' => "File Administration",
    'L_FILE_OPEN_ERROR' => "Error: could not open file.",
    'L_FILE_SAVED_SUCCESSFULLY' => "The file has been saved successfully.",
    'L_FILE_SAVED_UNSUCCESSFULLY' => "The file couldn't be saved!",
    'L_FILE_UPLOAD_SUCCESSFULL' => "The file '%s' was uploaded"
    ." successfully.",
    'L_FILTER_BY' => "Filter by",
    'L_FM_ALERTRESTORE1' => "Should the database",
    'L_FM_ALERTRESTORE2' => "be restored with the records from the"
    ." file",
    'L_FM_ALERTRESTORE3' => "?",
    'L_FM_ALL_BU' => "All Backups",
    'L_FM_ANZ_BU' => "Backups",
    'L_FM_ASKDELETE1' => "Should the file(s)",
    'L_FM_ASKDELETE2' => "really be deleted?",
    'L_FM_ASKDELETE3' => "Do you want autodelete to be executed"
    ." with configured rules now?",
    'L_FM_ASKDELETE4' => "Do you want to delete all backup"
    ." files?",
    'L_FM_ASKDELETE5' => "Do you want to delete all backup files"
    ." with",
    'L_FM_ASKDELETE5_2' => "* ?",
    'L_FM_AUTODEL1' => "Autodelete: the following files were"
    ." deleted because of maximum files"
    ." setting:",
    'L_FM_CHOOSE_ENCODING' => "Choose encoding of backup file",
    'L_FM_COMMENT' => "Enter Comment",
    'L_FM_DELETE' => "Delete",
    'L_FM_DELETE1' => "The file",
    'L_FM_DELETE2' => "was deleted successfully.",
    'L_FM_DELETE3' => "couldn't be deleted!",
    'L_FM_DELETEALL' => "Delete all backup files",
    'L_FM_DELETEALLFILTER' => "Delete all with",
    'L_FM_DELETEAUTO' => "Run autodelete manually",
    'L_FM_DUMPSETTINGS' => "Backup Configuration",
    'L_FM_DUMP_HEADER' => "Backup",
    'L_FM_FILEDATE' => "File date",
    'L_FM_FILES1' => "Database Backups",
    'L_FM_FILESIZE' => "File size",
    'L_FM_FILEUPLOAD' => "Upload file",
    'L_FM_FREESPACE' => "Free Space on Server",
    'L_FM_LAST_BU' => "Last Backup",
    'L_FM_NOFILE' => "You didn't choose a file!",
    'L_FM_NOFILESFOUND' => "No file found.",
    'L_FM_RECORDS' => "Records",
    'L_FM_RESTORE' => "Restore",
    'L_FM_RESTORE_HEADER' => "Restore of Database"
    ." `<strong>%s</strong>`",
    'L_FM_SELECTTABLES' => "Select tables",
    'L_FM_STARTDUMP' => "Start New Backup",
    'L_FM_TABLES' => "Tables",
    'L_FM_TOTALSIZE' => "Total Size",
    'L_FM_UPLOADFAILED' => "The upload has failed!",
    'L_FM_UPLOADFILEEXISTS' => "A file with the same name already"
    ." exists !",
    'L_FM_UPLOADFILEREQUEST' => "please choose a file.",
    'L_FM_UPLOADMOVEERROR' => "Couldn't move selected file to the"
    ." upload directory.",
    'L_FM_UPLOADNOTALLOWED1' => "This file type is not supported.",
    'L_FM_UPLOADNOTALLOWED2' => "Valid types are: *.gz and *.sql-files",
    'L_FOUND_DB' => "found db",
    'L_FROMFILE' => "from file",
    'L_FROMTEXTBOX' => "from text box",
    'L_FTP' => "FTP",
    'L_FTP_ADD_CONNECTION' => "Add connection",
    'L_FTP_CHOOSE_MODE' => "FTP Transfer Mode",
    'L_FTP_CONFIRM_DELETE' => "Should this FTP-Connection really be"
    ." deleted?",
    'L_FTP_CONNECTION' => "FTP-Connection",
    'L_FTP_CONNECTION_CLOSED' => "FTP-Connection closed",
    'L_FTP_CONNECTION_DELETE' => "Delete connection",
    'L_FTP_CONNECTION_ERROR' => "The connection to server '%s' using"
    ." port %s couldn't be established",
    'L_FTP_CONNECTION_SUCCESS' => "The connection to server '%s' using"
    ." port %s was established successfully",
    'L_FTP_DIR' => "Upload directory",
    'L_FTP_FILE_TRANSFER_ERROR' => "Transfer of file '%s' was faulty",
    'L_FTP_FILE_TRANSFER_SUCCESS' => "The file '%s' was transferred"
    ." successfully",
    'L_FTP_LOGIN_ERROR' => "Login as user '%s' was denied",
    'L_FTP_LOGIN_SUCCESS' => "Login as user '%s' was successfull",
    'L_FTP_OK' => "Connection successful.",
    'L_FTP_PASS' => "Password",
    'L_FTP_PASSIVE' => "use passive mode",
    'L_FTP_PASV_ERROR' => "Switching to passive mode was"
    ." unsuccessful",
    'L_FTP_PASV_SUCCESS' => "Switching to passive mode was"
    ." successfull",
    'L_FTP_PORT' => "Port",
    'L_FTP_SEND_TO' => "to <strong>%s</strong><br /> into"
    ." <strong>%s</strong>",
    'L_FTP_SERVER' => "Server",
    'L_FTP_SSL' => "Secure SSL FTP connection",
    'L_FTP_START' => "Starting FTP transfer",
    'L_FTP_TIMEOUT' => "Connection Timeout",
    'L_FTP_TRANSFER' => "FTP Transfer",
    'L_FTP_USER' => "User",
    'L_FTP_USESSL' => "use SSL Connection",
    'L_GENERAL' => "General",
    'L_GZIP' => "GZip compression",
    'L_GZIP_COMPRESSION' => "GZip Compression",
    'L_HOME' => "Home",
    'L_HOUR' => "Hour",
    'L_HOURS' => "Hours",
    'L_HTACC_ACTIVATE_REWRITE_ENGINE' => "Activate rewrite",
    'L_HTACC_ADD_HANDLER' => "Add handler",
    'L_HTACC_CONFIRM_DELETE' => "Should the directory protection be"
    ." written now ?",
    'L_HTACC_CONTENT' => "Contents of file",
    'L_HTACC_CREATE' => "Create directory protection",
    'L_HTACC_CREATED' => "The directory protection was created.",
    'L_HTACC_CREATE_ERROR' => "There was an error while creating the"
    ." directory protection !<br />Please"
    ." create the 2 files manually with the"
    ." following content",
    'L_HTACC_CRYPT' => "Crypt 8 Chars max (Linux and"
    ." Unix-Systems)",
    'L_HTACC_DENY_ALLOW' => "Deny / Allow",
    'L_HTACC_DIR_LISTING' => "Directory Listing",
    'L_HTACC_EDIT' => "Edit .htaccess",
    'L_HTACC_ERROR_DOC' => "Error Document",
    'L_HTACC_EXAMPLES' => "More examples and documentation",
    'L_HTACC_EXISTS' => "It already exists an directory"
    ." protection. If you create a new one,"
    ." the older one will be overwritten !",
    'L_HTACC_MAKE_EXECUTABLE' => "Make executable",
    'L_HTACC_MD5' => "MD5 (Linux and Unix-Systems)",
    'L_HTACC_NO_ENCRYPTION' => "plain text, no cryption (Windows)",
    'L_HTACC_NO_USERNAME' => "You have to enter a name!",
    'L_HTACC_PROPOSED' => "Urgently recommended",
    'L_HTACC_REDIRECT' => "Redirect",
    'L_HTACC_SCRIPT_EXEC' => "Execute script",
    'L_HTACC_SHA1' => "SHA1 (all Systems)",
    'L_HTACC_WARNING' => "Attention! The .htaccess directly"
    ." affects the browser's behavior.<br"
    ." />With incorrect content, these pages"
    ." may no longer be accessible.",
    'L_IMPORT' => "Import",
    'L_IMPORTIEREN' => "Import",
    'L_IMPORTOPTIONS' => "Import Options",
    'L_IMPORTSOURCE' => "Import Source",
    'L_IMPORTTABLE' => "Import in Table",
    'L_IMPORT_NOTABLE' => "No table was selected for import!",
    'L_IN' => "in",
    'L_INFO_ACTDB' => "Selected Database",
    'L_INFO_DATABASES' => "Accessable database(s)",
    'L_INFO_DBEMPTY' => "The database is empty !",
    'L_INFO_FSOCKOPEN_DISABLED' => "On this server the PHP command"
    ." fsockopen() is disabled by the"
    ." server's configuration. Because of"
    ." this the automatic download of"
    ." language packs is not possible. To"
    ." bypass this, you can download packages"
    ." manually, extract them locally and"
    ." upload them to the directory"
    ." \"language\" of your MySQLDumper"
    ." installation. Afterwards the new"
    ." language pack is available on this"
    ." site.",
    'L_INFO_LASTUPDATE' => "Last update",
    'L_INFO_LOCATION' => "Your location is",
    'L_INFO_NODB' => "database does not exist.",
    'L_INFO_NOPROCESSES' => "no running processes",
    'L_INFO_NOSTATUS' => "no status available",
    'L_INFO_NOVARS' => "no variables available",
    'L_INFO_OPTIMIZED' => "optimized",
    'L_INFO_RECORDS' => "Records",
    'L_INFO_SIZE' => "Size",
    'L_INFO_SUM' => "Total",
    'L_INSTALL' => "Installation",
    'L_INSTALLED' => "Installed",
    'L_INSTALL_HELP_PORT' => "(empty = Default Port)",
    'L_INSTALL_HELP_SOCKET' => "(empty = Default Socket)",
    'L_IS_WRITABLE' => "Is writable",
    'L_KILL_PROCESS' => "Stop process",
    'L_LANGUAGE' => "Language",
    'L_LANGUAGE_NAME' => "English",
    'L_LASTBACKUP' => "Last Backup",
    'L_LOAD' => "Load default settings",
    'L_LOAD_DATABASE' => "Reload databases",
    'L_LOAD_FILE' => "Load file",
    'L_LOG' => "Log",
    'L_LOGFILENOTWRITABLE' => "Can't write to logfile!",
    'L_LOGFILES' => "Logfiles",
    'L_LOGGED_IN' => "Logged in",
    'L_LOGIN' => "Login",
    'L_LOGIN_AUTOLOGIN' => "Automatic login",
    'L_LOGIN_INVALID_USER' => "Unknown combination of username and"
    ." password.",
    'L_LOGOUT' => "Log out",
    'L_LOG_CREATED' => "Log file created.",
    'L_LOG_DELETE' => "delete Log",
    'L_LOG_MAXSIZE' => "Maximum size of log files",
    'L_LOG_NOT_READABLE' => "The log file '%s' does not exist or is"
    ." not readable.",
    'L_MAILERROR' => "Sending of email failed!",
    'L_MAILPROGRAM' => "Mail program",
    'L_MAXSIZE' => "Max. Size",
    'L_MAX_BACKUP_FILES_EACH2' => "For each database",
    'L_MAX_EXECUTION_TIME' => "Max execution time",
    'L_MAX_UPLOAD_SIZE' => "Maximum file size",
    'L_MAX_UPLOAD_SIZE_INFO' => "If your Dumpfile is bigger than the"
    ." above mentioned limit, you must upload"
    ." it via FTP into the directory"
    ." \"work/backup\". 
After that you can"
    ." choose it to begin a restore progress.",
    'L_MEMORY' => "Memory",
    'L_MENU_HIDE' => "Hide menu",
    'L_MENU_SHOW' => "Show menu",
    'L_MESSAGE' => "Message",
    'L_MESSAGE_TYPE' => "Message type",
    'L_MINUTE' => "Minute",
    'L_MINUTES' => "Minutes",
    'L_MOBILE_OFF' => "Off",
    'L_MOBILE_ON' => "On",
    'L_MODE_EASY' => "Easy",
    'L_MODE_EXPERT' => "Expert",
    'L_MSD_INFO' => "MySQLDumper-Information",
    'L_MSD_MODE' => "MySQLDumper-Mode",
    'L_MSD_VERSION' => "MySQLDumper-Version",
    'L_MULTIDUMP' => "Multidump",
    'L_MULTIDUMP_FINISHED' => "Backup of <b>%d</b> Databases done",
    'L_MULTIPART_ACTUAL_PART' => "Actual Part",
    'L_MULTIPART_SIZE' => "maximum File size",
    'L_MULTI_PART' => "Multipart Backup",
    'L_MYSQLVARS' => "MySQL Variables",
    'L_MYSQL_CLIENT_VERSION' => "MySQL-Client",
    'L_MYSQL_CONNECTION_ENCODING' => "Standard encoding of MySQL-Server",
    'L_MYSQL_DATA' => "MySQL-Data",
    'L_MYSQL_VERSION' => "MySQL-Version",
    'L_MYSQL_VERSION_TOO_OLD' => "We are sorry: the installed"
    ." MySQL-Version %s is too old and can"
    ." not be used together with this version"
    ." of MySQLDumper. Please update your"
    ." MySQL-Version to at least version"
    ." %s.
As an alternative you could"
    ." install MySQLDumper version 1.24,"
    ." which is able to work together with"
    ." older MySQL-Versions. But you will"
    ." lose some of the new functions of"
    ." MySQLDumper in that case.",
    'L_NAME' => "Name",
    'L_NEW' => "new",
    'L_NEWTABLE' => "New table",
    'L_NEXT_AUTO_INCREMENT' => "Next automatic index",
    'L_NEXT_AUTO_INCREMENT_SHORT' => "n. auto index",
    'L_NO' => "no",
    'L_NOFTPPOSSIBLE' => "You don't have FTP functions !",
    'L_NOGZPOSSIBLE' => "Because Zlib is not installed, you"
    ." cannot use GZip-Functions!",
    'L_NONE' => "none",
    'L_NOREVERSE' => "Oldest entry first",
    'L_NOTAVAIL' => "<em>not available</em>",
    'L_NOTICE' => "Notice",
    'L_NOTICES' => "Notices",
    'L_NOT_ACTIVATED' => "not activated",
    'L_NOT_SUPPORTED' => "This backup doesn't support this"
    ." function.",
    'L_NO_DB_FOUND' => "I couldn't find any databases"
    ." automatically!
Please unhide the"
    ." connection parameters, and enter the"
    ." name of your database manually.",
    'L_NO_DB_FOUND_INFO' => "The connection to the database was"
    ." successfully established.<br />
Your"
    ." userdata is valid and was accepted by"
    ." the MySQL-Server.<br />
But"
    ." MySQLDumper was not able to find any"
    ." database.<br />
The automatic"
    ." detection via script is blocked on"
    ." some servers.<br />
You must enter"
    ." your database name manually after the"
    ." installation is finished.
Click on"
    ." \"configuration\" \"Connection"
    ." Parameter - display\" and enter the"
    ." database name there.",
    'L_NO_DB_SELECTED' => "No database selected.",
    'L_NO_ENTRIES' => "Table \"<b>%s</b>\" is empty and"
    ." doesn't have any entry.",
    'L_NO_MSD_BACKUPFILE' => "Backups of other scripts",
    'L_NO_NAME_GIVEN' => "You didn't enter a name.",
    'L_NR_TABLES_OPTIMIZED' => "%s tables have been optimized.",
    'L_NUMBER_OF_FILES_FORM' => "Delete by number of files per database",
    'L_OF' => "of",
    'L_OK' => "OK",
    'L_OPTIMIZE' => "Optimize",
    'L_OPTIMIZE_TABLES' => "Optimize Tables before Backup",
    'L_OPTIMIZE_TABLE_ERR' => "Error optimizing table `%s`.",
    'L_OPTIMIZE_TABLE_SUCC' => "Optimized table `%s` successfully.",
    'L_OS' => "Operating system",
    'L_OVERHEAD' => "Overhead",
    'L_PAGE' => "Page",
    'L_PAGE_REFRESHS' => "Pageviews",
    'L_PASS' => "Password",
    'L_PASSWORD' => "Password",
    'L_PASSWORDS_UNEQUAL' => "The Passwords are not identical or"
    ." empty !",
    'L_PASSWORD_REPEAT' => "Password (repeat)",
    'L_PASSWORD_STRENGTH' => "Password strength",
    'L_PERLOUTPUT1' => "Entry in crondump.pl for"
    ." absolute_path_of_configdir",
    'L_PERLOUTPUT2' => "URL for the browser or for external"
    ." Cron job",
    'L_PERLOUTPUT3' => "Commandline in the Shell or for the"
    ." Crontab",
    'L_PERL_COMPLETELOG' => "Perl-Complete-Log",
    'L_PERL_LOG' => "Perl-Log",
    'L_PHPBUG' => "Bug in zlib ! No Compression possible!",
    'L_PHPMAIL' => "PHP-Function mail()",
    'L_PHP_EXTENSIONS' => "PHP-Extensions",
    'L_PHP_VERSION' => "PHP-Version",
    'L_PHP_VERSION_TOO_OLD' => "We are sorry: the installed"
    ." PHP-Version is too old. MySQLDumper"
    ." needs a PHP-Version of %s or higher."
    ." This server has a PHP-Version of %s"
    ." which is too old. You need to update"
    ." your PHP-Version before you can"
    ." install and use MySQLDumper.",
    'L_POP3_PORT' => "POP3-Port",
    'L_POP3_SERVER' => "POP3-Server",
    'L_PORT' => "Port",
    'L_POSITION_BC' => "bottom center",
    'L_POSITION_BL' => "bottom left",
    'L_POSITION_BR' => "bottom right",
    'L_POSITION_MC' => "center center",
    'L_POSITION_ML' => "middle left",
    'L_POSITION_MR' => "middle right",
    'L_POSITION_NOTIFICATIONS' => "Position of notification window",
    'L_POSITION_TC' => "top center",
    'L_POSITION_TL' => "top left",
    'L_POSITION_TR' => "top right",
    'L_PREFIX' => "Prefix",
    'L_PRIMARYKEYS_CHANGED' => "Primary keys changed",
    'L_PRIMARYKEYS_CHANGINGERROR' => "Error changing primary keys",
    'L_PRIMARYKEYS_SAVE' => "Save primary keys",
    'L_PRIMARYKEY_CONFIRMDELETE' => "Really delete primary key?",
    'L_PRIMARYKEY_DELETED' => "Primary key deleted",
    'L_PRIMARYKEY_FIELD' => "Primary key field",
    'L_PRIMARYKEY_NOTFOUND' => "Primary key not found",
    'L_PROCESSKILL1' => "The script tries to kill process",
    'L_PROCESSKILL2' => ".",
    'L_PROCESSKILL3' => "The script tries since",
    'L_PROCESSKILL4' => "sec. to kill the process",
    'L_PROCESS_ID' => "Process ID",
    'L_PROGRESS_FILE' => "Progress file",
    'L_PROGRESS_OVER_ALL' => "Overall Progress",
    'L_PROGRESS_TABLE' => "Progress of table",
    'L_PROVIDER' => "Provider",
    'L_PROZESSE' => "Processes",
    'L_RECHTE' => "Permissions",
    'L_RECORDS' => "Records",
    'L_RECORDS_INSERTED' => "<b>%s</b> records inserted.",
    'L_RECORDS_OF_TABLE' => "Records of table",
    'L_RECORDS_PER_PAGECALL' => "Records per pagecall",
    'L_REFRESHTIME' => "Refresh time",
    'L_REFRESHTIME_PROCESSLIST' => "Refreshing time of the process list",
    'L_REGISTRATION_DESCRIPTION' => "Please enter the administrator account"
    ." now. You will login into MySQLDumper"
    ." with this user. Note the dates now"
    ." given good reason.

You can choose"
    ." your username and password free."
    ." Please make sure to choose the safest"
    ." possible combination of user name and"
    ." password to protect access to"
    ." MySQLDumper against unauthorized"
    ." access best!",
    'L_RELOAD' => "Reload",
    'L_REMOVE' => "Remove",
    'L_REPAIR' => "Repair",
    'L_RESET' => "Reset",
    'L_RESET_SEARCHWORDS' => "reset search words",
    'L_RESTORE' => "Restore",
    'L_RESTORE_COMPLETE' => "<b>%s</b> tables created.",
    'L_RESTORE_DB' => "Database '<b>%s</b>' on '<b>%s</b>'.",
    'L_RESTORE_DB_COMPLETE_IN' => "Restoring of database '%s' finished in"
    ." %s.",
    'L_RESTORE_OF_TABLES' => "Choose tables to be restored",
    'L_RESTORE_TABLE' => "Restoring of table '%s'",
    'L_RESTORE_TABLES_COMPLETED' => "Up to now <b>%d</b> of <b>%d</b>"
    ." tables were created.",
    'L_RESTORE_TABLES_COMPLETED0' => "Up to now <b>%d</b> tables were"
    ." created.",
    'L_REVERSE' => "Last entry first",
    'L_SAFEMODEDESC' => "Because PHP is running in safe_mode"
    ." you need to create the following"
    ." directories manually using your"
    ." FTP-Programm:",
    'L_SAVE' => "Save",
    'L_SAVEANDCONTINUE' => "Save and continue installation",
    'L_SAVE_ERROR' => "Error - unable to save configuration!",
    'L_SAVE_SUCCESS' => "Configuration was saved succesfully"
    ." into configuration file \"%s\".",
    'L_SAVING_DATA_TO_FILE' => "Saving data of database '%s' to file"
    ." '%s'",
    'L_SAVING_DATA_TO_MULTIPART_FILE' => "Maximum filesize reached: proceeding"
    ." with file '%s'",
    'L_SAVING_DB_FORM' => "Database",
    'L_SAVING_TABLE' => "Saving table",
    'L_SEARCH_ACCESS_KEYS' => "Browse: forward=ALT+V, backwards=ALT+C",
    'L_SEARCH_IN_TABLE' => "Search in table",
    'L_SEARCH_NO_RESULTS' => "The search for \"<b>%s</b>\" in table"
    ." \"<b>%s</b>\" doesn't bring any hits!",
    'L_SEARCH_OPTIONS' => "Search options",
    'L_SEARCH_OPTIONS_AND' => "a column must contain all search words"
    ." (AND-search)",
    'L_SEARCH_OPTIONS_CONCAT' => "a row must contain all of the search"
    ." words but they can be in any column"
    ." (could take some time)",
    'L_SEARCH_OPTIONS_OR' => "a column must have one of the search"
    ." words (OR-search)",
    'L_SEARCH_RESULTS' => "The search for \"<b>%s</b>\" in table"
    ." \"<b>%s</b>\" brings the following"
    ." results",
    'L_SECOND' => "Second",
    'L_SECONDS' => "Seconds",
    'L_SELECT' => "Select",
    'L_SELECTALL' => "Select All",
    'L_SELECTED_FILE' => "Selected file",
    'L_SELECT_FILE' => "Select file",
    'L_SELECT_LANGUAGE' => "Select language",
    'L_SENDMAIL' => "Sendmail",
    'L_SENDRESULTASFILE' => "send result as file",
    'L_SEND_MAIL_FORM' => "Send email report",
    'L_SERVER' => "Server",
    'L_SERVERCAPTION' => "Display Server",
    'L_SETPRIMARYKEYSFOR' => "Set new primary keys for table",
    'L_SHOWING_ENTRY_X_TO_Y_OF_Z' => "Showing entry %s to %s of %s",
    'L_SHOWRESULT' => "show result",
    'L_SHOW_TABLES' => "Show tables",
    'L_SMTP' => "SMTP",
    'L_SMTP_HOST' => "SMTP-Host",
    'L_SMTP_PORT' => "SMTP-Port",
    'L_SOCKET' => "Socket",
    'L_SPEED' => "Speed",
    'L_SQLBOX' => "SQL-Box",
    'L_SQLBOXHEIGHT' => "Height of SQL-Box",
    'L_SQLLIB_ACTIVATEBOARD' => "activate Board",
    'L_SQLLIB_BOARDS' => "Boards",
    'L_SQLLIB_DEACTIVATEBOARD' => "deactivate Board",
    'L_SQLLIB_GENERALFUNCTIONS' => "general functions",
    'L_SQLLIB_RESETAUTO' => "reset auto-increment",
    'L_SQLLIMIT' => "Count of records each page",
    'L_SQL_ACTIONS' => "Actions",
    'L_SQL_AFTER' => "after",
    'L_SQL_ALLOWDUPS' => "Duplicates allowed",
    'L_SQL_ATPOSITION' => "insert at position",
    'L_SQL_ATTRIBUTES' => "Attributes",
    'L_SQL_BACKDBOVERVIEW' => "Back to Overview",
    'L_SQL_BEFEHLNEU' => "New command",
    'L_SQL_BEFEHLSAVED1' => "SQL Command",
    'L_SQL_BEFEHLSAVED2' => "was added",
    'L_SQL_BEFEHLSAVED3' => "was saved",
    'L_SQL_BEFEHLSAVED4' => "was moved up",
    'L_SQL_BEFEHLSAVED5' => "was deleted",
    'L_SQL_BROWSER' => "SQL-Browser",
    'L_SQL_CARDINALITY' => "Cardinality",
    'L_SQL_CHANGED' => "was changed.",
    'L_SQL_CHANGEFIELD' => "change field",
    'L_SQL_CHOOSEACTION' => "Choose action",
    'L_SQL_COLLATENOTMATCH' => "Charset and Collation don't fit"
    ." together!",
    'L_SQL_COLUMNS' => "Columns",
    'L_SQL_COMMANDS' => "SQL Commands",
    'L_SQL_COMMANDS_IN' => "lines in",
    'L_SQL_COMMANDS_IN2' => "sec. parsed.",
    'L_SQL_COPYDATADB' => "Copy complete Database to",
    'L_SQL_COPYSDB' => "Copy Structure of Database",
    'L_SQL_COPYTABLE' => "copy table",
    'L_SQL_CREATED' => "was created.",
    'L_SQL_CREATEINDEX' => "create new index",
    'L_SQL_CREATETABLE' => "create table",
    'L_SQL_DATAVIEW' => "Data View",
    'L_SQL_DBCOPY' => "The Content of Database `%s` was"
    ." copied in Database `%s`.",
    'L_SQL_DBSCOPY' => "The Structure of Database `%s` was"
    ." copied in Database `%s`.",
    'L_SQL_DELETED' => "was deleted",
    'L_SQL_DELETEDB' => "Delete Database",
    'L_SQL_DESTTABLE_EXISTS' => "Destination Table exists !",
    'L_SQL_EDIT' => "edit",
    'L_SQL_EDITFIELD' => "Edit field",
    'L_SQL_EDIT_TABLESTRUCTURE' => "Edit table structure",
    'L_SQL_EMPTYDB' => "Empty Database",
    'L_SQL_ERROR1' => "Error in Query:",
    'L_SQL_ERROR2' => "MySQL says:",
    'L_SQL_EXEC' => "Execute SQL Statement",
    'L_SQL_EXPORT' => "Export from Database `%s`",
    'L_SQL_FIELDDELETE1' => "The Field",
    'L_SQL_FIELDNAMENOTVALID' => "Error: No valid fieldname",
    'L_SQL_FIRST' => "first",
    'L_SQL_IMEXPORT' => "Import-Export",
    'L_SQL_IMPORT' => "Import in Database `%s`",
    'L_SQL_INDEXES' => "Indices",
    'L_SQL_INSERTFIELD' => "insert field",
    'L_SQL_INSERTNEWFIELD' => "insert new field",
    'L_SQL_LIBRARY' => "SQL Library",
    'L_SQL_NAMEDEST_MISSING' => "Name of Destination is missing !",
    'L_SQL_NEWFIELD' => "New field",
    'L_SQL_NODATA' => "no records",
    'L_SQL_NODEST_COPY' => "No Copy without Destination !",
    'L_SQL_NOFIELDDELETE' => "Delete is not possible because Tables"
    ." must contain at least one field.",
    'L_SQL_NOTABLESINDB' => "No tables found in Database",
    'L_SQL_NOTABLESSELECTED' => "No tables selected !",
    'L_SQL_OPENFILE' => "Open SQL-File",
    'L_SQL_OPENFILE_BUTTON' => "Upload",
    'L_SQL_OUT1' => "Executed",
    'L_SQL_OUT2' => "Commands",
    'L_SQL_OUT3' => "It had",
    'L_SQL_OUT4' => "Comments",
    'L_SQL_OUT5' => "Because the output contains more than"
    ." 5000 lines it isn't displayed.",
    'L_SQL_OUTPUT' => "SQL Output",
    'L_SQL_QUERYENTRY' => "The Query contains",
    'L_SQL_RECORDDELETED' => "Record was deleted",
    'L_SQL_RECORDEDIT' => "edit record",
    'L_SQL_RECORDINSERTED' => "Record was added",
    'L_SQL_RECORDNEW' => "new record",
    'L_SQL_RECORDUPDATED' => "Record was updated",
    'L_SQL_RENAMEDB' => "Rename Database",
    'L_SQL_RENAMEDTO' => "was renamed to",
    'L_SQL_SCOPY' => "Table structure of `%s` was copied in"
    ." Table `%s`.",
    'L_SQL_SEARCH' => "Search",
    'L_SQL_SEARCHWORDS' => "Searchword(s)",
    'L_SQL_SELECTTABLE' => "select table",
    'L_SQL_SERVER' => "SQL-Server",
    'L_SQL_SHOWDATATABLE' => "Show Data of Table",
    'L_SQL_STRUCTUREDATA' => "Structure and Data",
    'L_SQL_STRUCTUREONLY' => "Only Structure",
    'L_SQL_TABLEEMPTIED' => "Table `%s` was deleted.",
    'L_SQL_TABLEEMPTIEDKEYS' => "Table `%s` was deleted and the indices"
    ." were reset.",
    'L_SQL_TABLEINDEXES' => "Indexes of table",
    'L_SQL_TABLENEW' => "Edit Tables",
    'L_SQL_TABLENOINDEXES' => "No Indexes in Table",
    'L_SQL_TABLENONAME' => "Table needs a name!",
    'L_SQL_TABLESOFDB' => "Tables of Database",
    'L_SQL_TABLEVIEW' => "Table View",
    'L_SQL_TBLNAMEEMPTY' => "Table name can't be empty!",
    'L_SQL_TBLPROPSOF' => "Table properties of",
    'L_SQL_TCOPY' => "Table `%s` was copied with data in"
    ." Table `%s`.",
    'L_SQL_UPLOADEDFILE' => "loaded file:",
    'L_SQL_VIEW_COMPACT' => "View: compact",
    'L_SQL_VIEW_STANDARD' => "View: standard",
    'L_SQL_VONINS' => "from totally",
    'L_SQL_WARNING' => "The execution of SQL Statements can"
    ." manipulate data. TAKE CARE! The"
    ." Authors don't accept any liability for"
    ." damaged or lost data.",
    'L_SQL_WASCREATED' => "was created",
    'L_SQL_WASEMPTIED' => "was emptied",
    'L_STARTDUMP' => "Start Backup",
    'L_START_RESTORE_DB_FILE' => "Starting restore of database '%s' from"
    ." file '%s'.",
    'L_START_SQL_SEARCH' => "start search",
    'L_STATUS' => "State",
    'L_STEP' => "Step",
    'L_SUCCESS_CONFIGFILE_CREATED' => "Configuration file \"%s\" has"
    ." successfully been created.",
    'L_SUCCESS_DELETING_CONFIGFILE' => "The configuration file \"%s\" has"
    ." successfully been deleted.",
    'L_TABLE' => "Table",
    'L_TABLES' => "Tables",
    'L_TABLESELECTION' => "Table selection",
    'L_TABLE_CREATE_SUCC' => "The table '%s' has been created"
    ." successfully.",
    'L_TABLE_TYPE' => "Table Type",
    'L_TESTCONNECTION' => "Test Connection",
    'L_THEME' => "Theme",
    'L_TIME' => "Time",
    'L_TIMESTAMP' => "Timestamp",
    'L_TITLE_INDEX' => "Index",
    'L_TITLE_KEY_FULLTEXT' => "Fulltext key",
    'L_TITLE_KEY_PRIMARY' => "Primary key",
    'L_TITLE_KEY_UNIQUE' => "Unique key",
    'L_TITLE_MYSQL_HELP' => "MySQL documentation",
    'L_TITLE_NOKEY' => "No key",
    'L_TITLE_SEARCH' => "Search",
    'L_TITLE_SHOW_DATA' => "Show data",
    'L_TITLE_UPLOAD' => "Upload SQL file",
    'L_TO' => "to",
    'L_TOOLS' => "Tools",
    'L_TOOLS_TOOLBOX' => "Select Database / Datebase functions /"
    ." Import - Export",
    'L_UNIT_KB' => "KiloByte",
    'L_UNIT_MB' => "MegaByte",
    'L_UNIT_PIXEL' => "Pixel",
    'L_UNKNOWN' => "unknown",
    'L_UNKNOWN_SQLCOMMAND' => "unknown SQL-Command",
    'L_UPDATE' => "Update",
    'L_UPDATE_CONNECTION_FAILED' => "Update failed because connection to"
    ." server '%s' could not be established.",
    'L_UPDATE_ERROR_RESPONSE' => "Update failed, server returned: '%s'",
    'L_UPTO' => "up to",
    'L_USERNAME' => "Username",
    'L_USE_SSL' => "Use SSL",
    'L_VALUE' => "Value",
    'L_VERSIONSINFORMATIONEN' => "Version Information",
    'L_VIEW' => "view",
    'L_VISIT_HOMEPAGE' => "Visit Homepage",
    'L_VOM' => "from",
    'L_WITH' => "with",
    'L_WITHATTACH' => "with attach",
    'L_WITHOUTATTACH' => "without attach",
    'L_WITHPRAEFIX' => "with prefix",
    'L_WRONGCONNECTIONPARS' => "Connection parameters wrong or"
    ." missing!",
    'L_WRONG_CONNECTIONPARS' => "Connection parameters are wrong !",
    'L_WRONG_RIGHTS' => "The file or the directory '%s' is not"
    ." writable for me. The rights (chmod)"
    ." are not set properly or it has the"
    ." wrong owner.<br /><br />Please set the"
    ." correct attributes using your FTP"
    ." program. The file or the directory"
    ." needs to be set to %s.",
    'L_YES' => "yes",
    'L_ZEND_FRAMEWORK_VERSION' => "Zend Framework Version",
    'L_ZEND_ID_ACCESS_NOT_A_DIRECTORY' => "The given filename '%value%' isn't a"
    ." directory.",
    'L_ZEND_ID_ACCESS_NOT_A_FILE' => "The given filename '%value%' isn't a"
    ." file.",
    'L_ZEND_ID_ACCESS_NOT_A_LINK' => "The given target '%value%' is not a"
    ." link.",
    'L_ZEND_ID_ACCESS_NOT_EXECUTABLE' => "The file or directory '%value%' isn't"
    ." executable.",
    'L_ZEND_ID_ACCESS_NOT_EXISTS' => "The file or directory '%value%'"
    ." doesn't exists.",
    'L_ZEND_ID_ACCESS_NOT_READABLE' => "The file or directory '%value%' isn't"
    ." readable.",
    'L_ZEND_ID_ACCESS_NOT_UPLOADED' => "The given file '%value%' isn't an"
    ." uploaded file.",
    'L_ZEND_ID_ACCESS_NOT_WRITABLE' => "The file or directory '%value%' isn't"
    ." writable.",
    'L_ZEND_ID_DIGITS_INVALID' => "Invalid type given. String, integer or"
    ." float expected.",
    'L_ZEND_ID_DIGITS_STRING_EMPTY' => "Value is an empty string.",
    'L_ZEND_ID_EMAIL_ADDRESS_DOT_ATOM' => "The email address can not be matched"
    ." against dot-atom format.",
    'L_ZEND_ID_EMAIL_ADDRESS_INVALID' => "Invalid type given. String expected.",
    'L_ZEND_ID_EMAIL_ADDRESS_INVALID_FORMAT' => "The email address format is invalid.",
    'L_ZEND_ID_EMAIL_ADDRESS_INVALID_HOSTNAME' => "The hostname is invalid.",
    'L_ZEND_ID_EMAIL_ADDRESS_INVALID_LOCAL_PART' => "The local part of the email address"
    ." (<local-part>@<domain>.<tld>) is"
    ." invalid.",
    'L_ZEND_ID_EMAIL_ADDRESS_INVALID_MX_RECORD' => "There is noch valid MX record for this"
    ." email address.",
    'L_ZEND_ID_EMAIL_ADDRESS_INVALID_SEGMENT' => "The hostname is located in a not"
    ." routable network segment. The email"
    ." address should not be resolved from"
    ." public network.",
    'L_ZEND_ID_EMAIL_ADDRESS_LENGTH_EXCEEDED' => "The email address is too long. The"
    ." maximum length is 320 chars.",
    'L_ZEND_ID_EMAIL_ADDRESS_QUOTED_STRING' => "The email addess can not be matched"
    ." against quoted-string format.",
    'L_ZEND_ID_IS_EMPTY' => "Value is required and can't be empty.",
    'L_ZEND_ID_MISSING_TOKEN' => "No token was provided to match"
    ." against.",
    'L_ZEND_ID_NOT_DIGITS' => "Only digits are allowed.",
    'L_ZEND_ID_NOT_EMPTY_INVALID' => "Invalid type given. String, integer,"
    ." float, boolean or array expected.",
    'L_ZEND_ID_NOT_SAME' => "The two given tokens do not match.",
);
return $lang;
