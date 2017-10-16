<?php
/*
    WEBlife CMS
    Developed by http://weblife.ua/
*/
defined('WEBlife') or die( 'Restricted access' ); // no direct access

// You can add other locales, will be substituted for all
// qeue up the first successful call to setlocale
// setlocale(LC_ALL, array('en_EN.CP1251', "en_EN", "en", "eng_ENG"));

define('ADMIN_ADD_NEW', 'Add new to level');
define('ADMIN_ADD_NEW_PAGE', 'Add new page');
define('ADMIN_ADD_NEW_PRODUCT', 'product');
define('ADMIN_ADD_NEW_NEWS', 'Add new news');
define('ADMIN_ADD_NEW_STOCK', 'Add new stock');
define('ADMIN_ADD_NEW_BANNER', 'Add new banner');
define('ADMIN_ADD_NEW_SLIDE', 'Add new slide');
define('ADMIN_ADD_NEW_IMAGE', 'Add new image');
define('ADMIN_ADD_NEW_VIDEO', 'Add new video');
define('ADMIN_ADD_NEW_CURRENCY', 'Add new currency');
define('ADMIN_ADD_NEW_BRAND', 'Add new brand');
define('ADMIN_ADD_NEW_ATTR_GROUP', 'Add new group');
define('ADMIN_ADD_NEW_ATTR', 'Add new attribute');
define('ADMIN_ADD_NEW_FILTER', 'Add new filter');
define('ADMIN_ADD_NEW_OPTION', 'Add new option');
define('ADMIN_ADD_NEW_USER', 'Add new user');

define('ADMIN_CREATING_NEW_PAGE', 'Creating new page');
define('ADMIN_CREATING_NEW_PRODUCT', 'Creating new product');
define('ADMIN_CREATING_NEW_NEWS', 'Creating new news');
define('ADMIN_CREATING_NEW_STOCK', 'Creating new stock');
define('ADMIN_CREATING_NEW_BANNER', 'Creating new banner');
define('ADMIN_CREATING_NEW_SLIDE', 'Creating new slide');
define('ADMIN_CREATING_NEW_IMAGE', 'Creating new image');
define('ADMIN_CREATING_NEW_VIDEO', 'Creating new video');
define('ADMIN_CREATING_NEW_CURRENCY', 'Creating new currency');
define('ADMIN_CREATING_NEW_BRAND', 'Creating new brand');
define('ADMIN_CREATING_NEW_ATTR_GROUP', 'Creating new attributes group');
define('ADMIN_CREATING_NEW_ATTR', 'Creating new attribute');
define('ADMIN_CREATING_NEW_FILTER', 'Creating new filter');
define('ADMIN_CREATING_NEW_OPTION', 'Creating new product option');
define('ADMIN_CREATING_NEW_USER', 'Creating new user��');

define('ADMIN_EDIT_CATEGORY_PAGE', 'Edit page');
define('ADMIN_EDIT_PRODUCT', 'Product edit page');
define('ADMIN_EDIT_NEWS', 'News edit page');
define('ADMIN_EDIT_STOCK', 'Stock edit page');
define('ADMIN_EDIT_BANNER', 'Banner edit page');
define('ADMIN_EDIT_SLIDE', 'Slide edit page');
define('ADMIN_EDIT_IMAGE', 'Image edit page');
define('ADMIN_EDIT_VIDEO', 'Video edit page');
define('ADMIN_EDIT_CURRENCY', 'Currency edit page');
define('ADMIN_EDIT_BRAND', 'Brand edit page');
define('ADMIN_EDIT_ATTR_GROUP', 'Attributes group edit page');
define('ADMIN_EDIT_ATTR', 'Attribute edit page');
define('ADMIN_EDIT_FILTER', 'Filter edit page');
define('ADMIN_EDIT_OPTION', 'Product option edit page');
define('ADMIN_EDIT_USER', 'Edit user page');

define('ADMIN_TOGGLE_FIELDS', 'Toggle hidden fields');
define('ADMIN_COPYRIGHT', 'WEBlife Content Management System');
define('ADMIN_AJAX_MODE', 'AJAX Mode!');
define('ADMIN_EDIT_PAGE', 'Editing current page');
define('ADMIN_MAIN_ROOT', 'Root');
define('ADMIN_MAIN_TITLE', 'Main');
define('ADMIN_PATH', 'Path: ');
define('ADMIN_HELLO', 'Hello');
define('ADMIN_LIST_ITEMS', 'List');
define('ADMIN_MODULE_ID_ERROR', 'Module "%s" [%s] is not selected in either category as the executable. For proper operation of the module, you must first select it in the site structure. <a href="/admin.php?module=main ">Back &gt;</a>!');
define('ADMIN_MODULE_TABLE_ERROR', 'DataTable of Module "%s" [%s] does not exist in the database. For proper operation of the module, you must first create a ("%s") Table');

define('PRODUCT_OPTIONS', 'Product options');
define('BANNERS', 'Site Banners');
define('BANNERS_TITLE', 'Edit Banners');
define('BANNERS_PATH_TITLE', 'Banners');
define('BY_LANG', 'Edited by English language');

define('CMS_TITLE', 'WEBlife CMS');
define('CMS_NAME', 'WEBlife Content Management System');

define('ADMINISTRATORS', 'Administrators');
define('USERS', 'Users');
define('CUSTOMERS', 'Customers');
define('CURRENCY', 'Currency');
define('CURRENCY_CHANGE_ERROR', 'Currency did not change!');
define('COEFFICIENT', '�oefficient');
define('CURRENCY_NAME_EXAMPLE', '(Dollar)');
define('CURRENCY_TITLE_EXAMPLE', '(American dollar)');
define('CURRENCY_VIEW_DATA', 'Currency view data');
define('CURRENCY_CHANGE_DISABLE1', 'Deny currency');
define('CURRENCY_CHANGE_DISABLE2', 'already defined!');
define('CURRENCY_ONLY_ONE', 'Only one currency can be equal to 1!');
define('CURRENCY_DENY_SELECT', 'This currency disable, it can\'t be "Default"!');
define('CURRENCY_DENY_TURNOFF', 'This currency is "Default", it can\'t be disable!');
define('CURRENCY_ONLY_ONE_EMPTY', 'Must be 1 currency with course 1');
define('CURRENCY_EMPTY_DEFAULT', 'You must select one currency with cource 1 by "Default" for translation');
define('CURRENCY_SELECT_DEFAULT', 'You must select one currency by "Default" to publish in site');
define('CURRENCY_EMPTY_PUBLISH', 'You must public one currency!');
define('CURRENCY_UNPUBLISHED_DEFAULT_SELECT', 'Not publish currency can\'t be set as');
define('CURRENCY_SETTINGS', 'Settings');
define('RELATED_PRODUCTS', 'Related products');
define('PRODUCT_KITS', 'Product kits');
define('PRODUCT_KIT_PREFIX', 'Kit');

define('FAQ_HEAD', 'Questions/Answers');
define('FAQ_TITLE', 'FAQ');
define('FAQ_LIST_TITLE', 'FAQ List');
define('FAQ_QUESTIONS', 'Questions');
define('FAQ_ANSWERS', 'Answers');
define('FAQ_QUESTION_TITLE', 'Question');
define('FAQ_ANSWER_TITLE', 'Answer');

define('ASK_QUESTION_TITLE', 'Ask question');
define('ASK_QUESTION_BY', 'Question sets');

define('LABLE_SHOW_DIFFERENT', 'Show only different');
define('LABLE_SHOW_ALL', '�������� ���');
define('LABEL_YOUR_DATA', 'Your data');
define('LABEL_YOUR_NAME', 'Your name');
define('LABEL_YOUR_EMAIL', 'Your E-mail');
define('LABEL_YOUR_QUESTION', 'Your question');
define('LABEL_YOUR_ANSWER', 'Your answer');
define('LABEL_YOUR_AGE', 'Your age');
define('LABEL_CHILDREN', 'Children');
define('LABEL_CHILDREN_ABBV', 'Children name');
define('LABEL_CHILDREN_AGE', 'Children age');
define('LABEL_CHILDREN_GENDER', 'Children gender');
define('LABEL_ENTER_ACCESS_CODE', 'Enter access code');
define('LABEL_REQUIRE_INFO_TEXT', 'Required fields');
define('LABEL_SHOW_INFO', 'Publish');
define('LABEL_HIDE_INFO', 'Do not publish');
define('LABEL_SITE_LOCATION', 'On site');
define('LABEL_SUBSCRIBE_TITLE', '%s newsletter');
define('LABEL_SUBSCRIBE', 'Subscribe');
define('LABEL_UNSUBSCRIBE', 'Unsubscribe');
define('LABEL_SMSNOTIFY', 'Receive notifications by SMS');
define('LABEL_HIDDEN_INFO_TITLE', 'Private information');
define('LABEL_RESET', 'Reset');
define('LABEL_EXAMPLE', 'For example');
define('LABEL_TODAY', 'Today');
define('LABEL_YESTERDAY', 'Yesterday');
define('LABEL_PURCHASE', 'Purchase');
define('LABEL_SALES', 'Sales');
define('LABEL_SEARCH_EXAMPLE', 'agricultural production');
define('LABEL_CONTACTS_INFO', 'Contact Information');
define('LABEL_SELECT_CATEGORY', 'Select category');
define('LABEL_SELECT_CURRENCY', 'Select currency');
define('LABEL_SELECT_REGION', 'Select Region');
define('LABEL_SELECT_SETTLEMENT', 'Select City');
define('LABEL_SELECTED_REGION', 'Selected Region');
define('LABEL_SELECTED_SETTLEMENT', 'Selected City');
define('LABEL_AND_UNION', ' and ');
define('LABEL_OR_UNION', ' or ');
define('LABEL_FILTER', 'Filter');
define('LABEL_INSTITUTION', 'Institution');
define('LABEL_SPECIALTY', 'Specialty');
define('LABEL_ADDRESS', 'Address');
define('LABEL_SELECT_DOCTOR', 'Select doctor');
define('LABEL_SELECT_GENDER', 'Select gender');
define('LABEL_COMPANY_LIST', 'See Company List');
define('LABEL_STATIC', 'Static');
define('LABEL_SPECIAL', 'Special');
define('LABEL_CONTACTS', 'Contacts');
define('LABEL_PORTFOLIO', 'Portfolio');
define('LABEL_GALLERY', 'Gallery');
define('LABEL_VIDEO', 'Video');
define('LABEL_WELCOME', 'Welcome');
define('LABEL_CATALOG', 'Catalog');
define('LABEL_PRODUCT_CATALOG', 'Product Catalogue');
define('LABEL_PRICE', 'Price');
define('LABEL_SERVICE', 'Service');
define('LABEL_FLASH_VERSION', 'Flash-version');
define('LABEL_HTML_VERSION', 'HTML-version');
define('LABEL_CLEAR_TEMPLATES', 'Clear templates');
define('LABEL_CLEAR_CACHING', 'Clear cache');
define('LABEL_EXTRA_SYSTEM_SETTINGS', 'Advanced System Settings');
define('LABEL_QUESTION_TO_DO', 'Are you sure');
define('LABEL_REPAIR_DB_TABLES', 'Repair corrupted table');
define('LABEL_OPTIMIZE_DB_TABLES', 'Optimize tables');
define('LABEL_SITE_CURRENCY', 'Site Currency');
define('LABEL_FILE_CURRENCY', 'File Currency');
define('LABEL_ACTION', 'Action');
define('LABEL_ACTIONS', 'Actions');
define('LABEL_DOWNLOAD_FORM', 'Download form');
define('LABEL_DOWNLOADED_FILES', 'Downloaded files');
define('LABEL_UNSAVE_CHANGES', 'Do not want to save changes');
define('LABEL_DELETE_FILE', 'Are you sure you want to delete this file');
define('LABEL_PRODUCTION', 'Production');
define('LABEL_ARTICLE', 'Article');
define('LABEL_BRAND', 'Brand');
define('LABEL_ATTRIBUTE', 'Attribute');
define('LABEL_ATTRIBUTES', 'Attributes');
define('LABEL_RANGE', 'Range');
define('LABEL_MEASUREMENT_UNITS', 'Meas.Units');
define('LABEL_GROUP', 'Group');
define('LABEL_TYPE', 'Type');
define('LABEL_SECTION', 'Section');
define('LABEL_FROM', 'From');
define('LABEL_TO', 'To');
define('LABEL_SELECT', 'Select');
define('LABEL_ALL', 'All');
define('LABEL_BY_KEYWORD', 'By keyword');
define('LABEL_BY_KEY_PHRASE', 'By key phrase');
define('LABEL_FOUND', 'Found');
define('LABEL_PRODUCT_S', 'Product(s)');
define('LABEL_EXPORT', 'Export');
define('LABEL_IMPORT', 'Import');
define('LABEL_IMPORT_FILES', 'Import files');
define('LABEL_DOWNLOAD_EXAMPLE_FILE', 'Download a sample file');
define('LABEL_COUNT_RECORDS', 'Records');
define('LABEL_ITEM_UNACTIVE', 'unactive');
define('LABEL_COPY', 'copy');
define('LABEL_POPULAR', 'popular');
define('LABEL_NEWEST', 'new');
define('LABEL_EDIT', 'edit');
define('LABEL_UPGRADE_FLASH', 'You need to upgrade your flash player!');
define('LABEL_SELECTIONS', 'Selections');
define('LABEL_EMPTY_SELECTIONS', 'Empty. User search to add product!');
define('LABEL_FILTERS_MAIN_LIST', 'Filters (main list)');
define('LABEL_FILTERS_SHORT_LIST', 'Filters SEO settings');
define('LABEL_FILTERS_THIRD_LEVEL_LIST', 'Filters (3-d level menu)');

define('SITE_LANGUAGE', 'Language');
define('SITE_FOUND', 'Found');
define('CATALOG_SEARCH', 'Catalog search');
define('SITE_SEARCH', 'Site Search');
define('SITE_SEARCH_ENTER', 'Enter the keyphrase');
define('SITE_SEARCH_RESULTS', 'Search Results');
define('SITE_CONTACTS', 'Contact');
define('SITE_COUNT_RECORDS', 'All Items = ');
define('SITE_PAGE', 'Page');
define('SITE_PAGES', 'Pages');
define('SITE_PAGER_ALL', 'Show All');
define('SITE_PAGER_FIRST', 'First');
define('SITE_PAGER_LAST', 'Last');
define('SITE_PAGER_PREV', 'Previous');
define('SITE_PAGER_NEXT', 'Next');
define('SITE_PREV', 'Previous');
define('SITE_NEXT', 'Next');
define('SITE_BACK', 'Back');
define('SITE_FORWARD', 'Forward');
define('NO_CONTENT', 'No content');

define('FEEDBACK', 'Feedback');
define('COMMENTS', 'Comments');
define('POLLS', 'Polls');
define('QUOTES', 'Quotes');
define('ADD_COMMENT', 'Leave a comment');
define('NO_COMMENTS', 'No comments');

define('NO_RESULTS', 'Nothing found!');
define('FOUND', 'Pages found');
define('FOUND_ERROR', 'Not enough characters for the search! The search phrase must contain 3 or more characters!');

define('ACCESS_CODE', 'Access code');
define('ANNOUNCEMENTS', 'Announcements');
define('ARTICLES', 'Articles');
define('AUCTIONS', 'Auctions');
define('CATALOG', 'Catalog');
define('CATALOGS', 'Catalogs');
define('CLIENTS', 'Clients');
define('PAGES', 'Pages');
define('MAIN_PAGE', 'Home');
define('HOMESLIDER', 'Slider');
define('BRANDS', 'Brands');
define('ATTRIBUTES', 'Attributes');
define('ATTRIBUTE_GROUPS', 'Attribute groups');
define('FILTERS', 'Filters');
define('NEWS', 'News');
define('VIDEOS', 'Video');
define('ORDERS', 'Orders');
define('STOCKS', 'Stock');
define('BLOGNEWS', 'Blogs');
define('PATIENTS', 'Patients');
define('PORTFOLIO', 'Portfolio');
define('PRICES', 'Prices');
define('PRODUCTS', 'Products');
define('PRODUCTS_COMPARE', 'compare');
define('SELECT_PRODUCTS_TO_COMPARE', 'Select products to compare');
define('TAGS_MENU', 'Edit Submenu');
define('STATIC_BLOCKS', 'Static Blocks');
define('CATALOG_IMPORT', 'Catalog Import');
define('CATALOG_EXPORT', 'Catalog Export');
define('CATALOG_IMPORT_EXPORT', 'Catalog Import/Export');
define('CATALOG_ADD_ATTRIBUTES', 'Add attributes');
define('CATALOG_ATTRIBUTES_SELECT_GROUP', 'Select attributes group');

define('COMM_NAME', 'Name');
define('COMM_MSG', 'Message');
define('COMM_CODE', 'Confirmation code');
define('COMM_SEND', 'Send');
define('COMM_WRONG', 'Please fill out the form correctly');
define('COMM_OK', 'Thank you. Your message has been sent and will be posted after verification administrator');
define('COMM_TIME', 'Time');

define('GALLERY', 'Gallery');
define('GALLERIES', 'Galleries');

define('MEMBERS_MESSAGES', 'Messages');
define('MEMBERS_MESSAGES_ADD', 'Add new post');
define('MEMBERS_MESSAGES_BODY', 'Text of letter');
define('MEMBERS_MESSAGES_UNSENDER', 'The sender is not selected');
define('MEMBERS_MESSAGES_UNRECEIVER', 'The recipient is selected');
define('MEMBERS_MESSAGES_EMPTY', 'In this section, no messages!');

define('MODULE_NOT_INIT_ERROR', 'This module not yet assigned a no one category in the main category tree. The module will work uncorrect!');

define('MENU_TYPE_NOTDEFINED', 'Menu is not selected');
define('MENU_TYPE_MAIN', 'Main Menu');
define('MENU_TYPE_TOP', 'Top Menu');
define('MENU_TYPE_LEFT', 'Left side Menu');
define('MENU_TYPE_BOTTOM', 'Footer Menu');
define('MENU_TYPE_RIGHT', 'Right side Menu');
define('MENU_TYPE_CATALOG', 'Catalog Menu');
define('MENU_TYPE_USER', 'User Menu');
define('MENU_TYPE_SYSTEM', 'System Menu');
define('MENU_TYPE_OTHER', 'Other Menu');

define('TOPLINK_PREVIEW_SITE', 'View');
define('TOPLINK_LOGOUT', 'Exit');
define('NOT_FOUND', 'Page not found');

define('TITLE_SETTINGS', 'Edit Site Settings');
define('TITLE_TAGS_MENU', 'Edit Tag Menu');
define('TITLE_STATIC_BLOCKS', 'Edit menu Tag');
define('TITLE_EDIT_PRIVATE_INFO', 'Edit personal data');
define('TITLE_EDIT_PAGE', 'Editing');

define('ERROR_CURRENT_PASSWORD', "Your password is wrong. <br/>Please, insert right password and confirmation again.");
define('ERROR_CONFIRMED_PASSWORD', "Your password was not confirmed properly. <br/>Please, insert password and confirmation again.");
define('ERROR_SAVE_DATA', "You changed data didn't update. Try again or contact with your site administrator");
define('ERROR_SAVE_DETAILS_DATA', 'Your additional change data were not updated.<br/>Try again or contact the administrator of your site!');
define('ERROR_LIST_FIELDS_EMPTY', ' - is empty');
define('ERROR_LIST_FIELDS_REQUIRED', 'At least one field must be filled!');

define('TOPLINK_CURRENCY', 'Currency');
define('TOPLINK_BANNERS', 'Banners');
define('TOPLINK_SETTINGS', 'Settings');
define('TOPLINK_MYSQLDUMPER', 'Dump DB');
define('TOPLINK_USERS', 'Users');
define('TOPLINK_COMMENTUSERS', 'Commentators');
define('TOPLINK_MYCOMMENTUSERS', 'My commentators');
define('TOPLINK_USER', 'My Profile');

define('SETTINGS_WEBSITE', 'Site Settings');
define('SETTINGS_OWNER', 'Owner');
define('SETTINGS_TITLE', 'Site Settings');
define('SETTINGS_WEBSITE_NAME', 'Site Name');
define('SETTINGS_WEBSITE_SLOGAN', 'Site Slogan');
define('SETTINGS_WEBSITE_LOGO', 'logo');
define('SETTINGS_COPYRIGHT', 'Copyright');
define('SETTINGS_WEBSITE_URL', 'Url map');
define('SETTINGS_FIRST_NAME', 'Name');
define('SETTINGS_LAST_NAME', 'Name of owner');
define('SETTINGS_EMAIL', 'Owner E-mail');
define('SETTINGS_PHONE', 'Phone');
define('SETTINGS_ADDRESS', 'Owner Address');
define('SETTINGS_MENU', 'Code submenu (write a new menu item with a new line. Unpointed)');
define('SETTINGS_SITE_EMAIL', 'General email site address (used as the return address/es, separated by commas');
define('SETTINGS_NOTIFY_EMAIL', 'Email for notifications (notification will be sent at this address/es, separated by commas)');

define('USER_EXIT', 'Exit');
define('USER_HELLO', 'Hello');
define('USER_REGISTER_TITLE', 'User Registration');
define('USER_LOGIN_TITLE', 'User Authorization');
define('USER_LOGOUT_TITLE', 'Close the user account');
define('USER_RECOVERY_TITLE', 'Recover password');
define('USER_PROFILE', 'My Profile');
define('USER_PROFILE_EDIT', 'Edit Profile');
define('USER_PROFILE_TITLE', 'Profile');
define('USER_ACCESS_CODE', 'User access code');
define('USER_REGISTER_ACCESS_CODE', 'Register access code');
define('USER_SHOW_DATA', 'Show user data');
define('USER_TO_ACTIVATE_LIST', 'Activation users list');

define('USERS_TITLE', 'Users');
define('USERS_MAIN', 'Home');
define('USERS_CREATE', 'Create User');
define('USERS_ADDNEW_TITLE', 'Creating new user');
define('USERS_EDIT_TITLE', 'Edit User');
define('USERS_ACTIVATION_VIEW', 'View to activate');

define('USERS_ENABLED', 'Active');
define('USERS_EDIT', 'Edit');
define('USERS_DELETE', 'Delete');
define('USERS_DENIED', 'Forbidden');
define('USERS_COUNT', 'Total Users');
define('USERS_LIST', 'All Users list');
define('USERS_COPY_PASS_BEFORE_SAVE', 'Copy pass before save!!!');
define('USERS_PASS_SET', 'set');
define('USERS_PASS_NOT_SET', 'not set');

define('USERS_ACCESS', 'Access');
define('USERS_ACCESS_ADMINISTRATOR', 'Admin');
define('USERS_ACCESS_MODERATOR', 'Moderator');
define('USERS_ACCESS_USER', 'User');

define('USERS_ID', 'id');
define('USERS_NAME', 'Name');
define('USERS_SNAME', 'Surname');
define('USERS_FNAME', 'First name');
define('USERS_MNAME', 'Middle name');
define('USERS_MAIL', 'E-mail');
define('USERS_NETWORK', 'Network');
define('USERS_PHONE', 'Phone');
define('USERS_LOGIN', 'Login');
define('USERS_PASS', 'Password');
define('USERS_CONFPASS', 'Confirm password');
define('USERS_PHOTO', 'Photo');
define('USERS_REGION', 'Region');
define('USERS_CITY', 'City');
define('USERS_CURRENT_PASS', 'Current password');
define('USERS_NEW_PASS', 'New password');
define('USERS_MORE_INFO', 'More info');
define('USERS_SUBSCRIBE', 'Subscribe');
define('USERS_FULL_NAME', 'Full name');
define('USERS_OLD_PASS', 'Old pass');
define('USERS_AUTO_PASS', 'Auto pass');
define('USERS_CONFIRM_MAIL', 'E-mail confirmed');
define('USERS_EMPTY_FILES', 'no files');

define('USERS_ACTIVE', 'Active');
define('USERS_NOACTIVE', 'Not active');
define('USERS_DESCR', 'Short description');

define('USERS_ERROR_UPDATE_ENABLED', 'Could not edit the record');
define('USERS_ERROR_DELETE', 'Failed to delete record');
define('USERS_ERROR_PASSWORDS', 'Please enter the Password and Confirm password!');
define('USERS_ERROR_PASSWORDS_CONFIRM', 'Your New Password and Confirm New Password does not match!');
define('USERS_ERROR_NOCONFPASS', 'Your password was not confirmed properly. <br/>Please, insert password and confirmation again.');
define('USERS_ERROR_INSERT', 'Please insert');
define('USERS_ERROR_UPDATE', 'Error Update');
define('USERS_ERROR_INPUT_LOGIN', 'Please enter login. Login must be greater than 3 Latin characters [a-zA-Z0-9_-]!');
define('USERS_ERROR_LOGIN', 'Access denied! Possible cause of error in the login or password!');
define('USERS_ERROR_MAX_WRONG_PASS', 'You no longer valid input wrong password!');
define('USERS_CHANGE_CURR_PASS', 'To modify data, please enter the Current password!');
define('USERS_EMPTY_FIELDS', 'Fill all require fields!');
define('USERS_CONFIRM_CHANGE_PASS', 'Are you copy pass and want to change it?');
define('USERS_COPY_PASS_FIRST', 'First you need to generate new pass and copy it to buffer!');
define('USERS_GENERATE_PASS_ERROR', 'Error generation pass!');

define('USERS_ADD_SUCCESS', 'New Account has been added to Database');
define('USERS_EDIT_SUCCESS', 'Account has been modified');

define('DATABASE_SUCCESS', 'Data successfully saved! ');
define('DATABASE_UPDATE_SUCCESS', 'Data successfully updated!');
define('DATABASE_UPDATE_ERROR', 'Data has not been updated!');
define('ERROR_PLEASE_INSERT', 'Do not fill in the following blanks:');
define('CONFIRM_USER_ACTIVATION', 'You sure you want to activate this user? After activation site resources will be enable for him!');
define('ACTIVATE', 'Activate');
define('CONFIRM_CLOSE_VIEW', 'Close detail view?');
define('CLOSE_VIEW', 'Close view');

define('BUTTON_SAVE', 'Save');
define('BUTTON_CANCEL', 'Cancel');
define('BUTTON_CHECK', 'Check');
define('BUTTON_CONFIRM', 'Confirm');
define('BUTTON_CLEAR', 'Clear');
define('BUTTON_CLOSE', 'Close');
define('BUTTON_EDIT', 'Edit');
define('BUTTON_EXIT', 'Exit');
define('BUTTON_RELOAD', 'Reload');
define('BUTTON_UPDATE', 'Update');
define('BUTTON_CREATE', 'Create');
define('BUTTON_ADD', 'Add');
define('BUTTON_MORE', 'More');
define('BUTTON_SEARCH', 'Search');
define('BUTTON_SELECT', 'Select');
define('BUTTON_DOWNLOAD', 'Download');
define('BUTTON_SEND', 'Send');
define('BUTTON_FEEDBACK', 'Feedback');
define('BUTTON_REGISTER', 'Register');
define('BUTTON_DETAIL', 'Detail');
define('BUTTON_ENTER', 'Enter');
define('BUTTON_COPY', 'Copy');
define('BUTTON_SAVE_ADD', 'Save and add new');
define('BUTTON_APPLY', 'Apply');
define('BUTTON_DELETE', 'Delete');

define('HEAD_SHOW_IN_CART', 'Show in cart');
define('HEAD_SHORT_TITLE', 'Short title');
define('HEAD_GROUP', 'Group');
define('HEAD_LINK_ADD_ITEM', 'Add new page');
define('HEAD_LINK_ADD_NEWS', 'Submit News');
define('HEAD_LINK_AJAX_MANAGERS', 'Ajax managers');
define('HEAD_LINK_SORTBY', 'Sort by');
define('HEAD_LINK_SORT_NAME', 'name');
define('HEAD_LINK_SORT_CODE', 'code');
define('HEAD_LINK_SORT_TITLE', 'title');
define('HEAD_LINK_SORTDATEADD', 'date');
define('HEAD_LINK_SORT_PRICE', 'price');
define('HEAD_LINK_SORT_TYPE', 'type');
define('HEAD_LINK_SORT_DEFAULT', 'default');
define('HEAD_LINK_RECEIVED_LETTERS', 'Show received a letter');
define('HEAD_LINK_SENT_LETTERS', 'Show/Sent Mail');

define('HEAD_ID', 'ID');
define('HEAD_NAME', 'Name');
define('HEAD_COMPLETED', 'Completed');
define('HEAD_EMAIL', 'Email');
define('HEAD_PRODUCT', 'Product');
define('HEAD_SORT', 'Order');
define('HEAD_CATEGORY', 'Category');
define('HEAD_CREATED', 'Created');
define('HEAD_CODE', 'Code');
define('HEAD_CURRENCY_CODE', 'Code');
define('HEAD_PRICE', 'Price');
define('HEAD_PRICES', 'Prices');
define('HEAD_POPULAR', 'Popular');
define('HEAD_STOCK', 'Stock');
define('HEAD_NEWEST', 'New');
define('HEAD_DOCTOR', 'Doctor');
define('HEAD_PATIENT', 'Patient');
define('HEAD_CLIENT', 'Client');
define('HEAD_CONTACTS', 'Contacts');
define('HEAD_TIME', 'Time');
define('HEAD_DATE', 'Date');
define('HEAD_CREATED_DATE', 'Created Date<br/>(yyyy-mm-dd)');
define('HEAD_CREATED_TIME', 'Created Time<br/>(hh:mm:ss)');
define('HEAD_DATE_ADDED', 'Added <br/>(d.m.y h:m:s)');
define('HEAD_DATE_CHANGED', 'Changed <br/>(d.m.y h:m:s)');
define('HEAD_DATE_ADDED_SQL', 'Added <br/>(y.m.d h:m:s)');
define('HEAD_DATE_CHANGED_SQL', 'Changed <br/>(y.m.d h:m:s)');
define('HEAD_ONFRONTPAGE', 'On the<br/>Main');
define('HEAD_PUBLICATION', 'Published');
define('HEAD_ARCHIVE', 'Archive');
define('HEAD_EDIT', 'Edit');
define('HEAD_DELETE', 'Delete');
define('HEAD_USERNAME', 'User');
define('HEAD_TITLE', 'Title');
define('HEAD_OWNER', 'Owner');
define('HEAD_SENDER', 'Sender');
define('HEAD_RECEIVER', 'Recipient');
define('HEAD_LIMIT', 'Limit');
define('HEAD_POSITION', 'Position');
define('HEAD_MODULE', 'Module');
define('HEAD_QUANTITY', 'Quantity');
define('HEAD_UNITS', 'Units');
define('HEAD_NOTE', 'Note');
define('HEAD_TARGET', 'Link Target');
define('HEAD_HITS', 'Hits');
define('HEAD_COUNT_HITS', 'Count hits');
define('HEAD_MAX_HITS', 'Max count hits');
define('HEAD_CLICKS', 'Clicks');
define('HEAD_COUNT_CLICKS', 'Count clicks');
define('HEAD_MAX_CLICKS', 'Max count clicks');
define('HEAD_XPOS', 'Horizontal position (px)');
define('HEAD_YPOS', 'Vertical position (px)');
define('HEAD_DEFAULT', 'By default');
define('HEAD_DEFAULT_4_CALC', 'By default<br/>to recounts');
define('HEAD_DEFAULT_4_SHOW', 'By default<br/>to display');
define('HEAD_COEFFICIENT', 'Coefficient');
define('HEAD_NOMINAL', 'Nominal');
define('HEAD_SIGN', 'Sign');
define('HEAD_RATE', 'Course');
define('HEAD_DECIMALS', 'Decimals sign');
define('HEAD_DECIMALS_POINT', 'Decimal point');
define('HEAD_THOUSAND_SEPARATOR', 'Thousands separator');
define('HEAD_TEMPLATE', 'Template');
define('HEAD_EXAMPLE', 'Example');
define('HEAD_URL', 'Url');
define('HEAD_IMAGE', 'Image');
define('HEAD_SHOW', 'Show');
define('HEAD_FILENAME', 'File');
define('HEAD_TYPE', 'Type');
define('HEAD_GENDER', 'Gender');
define('HEAD_WEIGHT', 'Weight');
define('HEAD_PRIORITY', 'Priority');
define('HEAD_SIGNIFICANCE', 'Significance');
define('HEAD_MORE_OPTIONS', 'More options');
define('HEAD_AVAILABLE_ON_PAGES', 'Available on pages');

define('HEAD_FILES_MANAGER', 'Files manager');
define('HEAD_ATTRIBUTE_MANAGER', 'Attribute manager');
define('HEAD_FILTERS_MANAGER', 'Filters manager');
define('HEAD_META_TEMPLATES', 'Metadata templates');
define('HEAD_PAGE_TYPE', 'Page Type');
define('HEAD_MENU_TYPES', 'Menu types');
define('HEAD_PUBLISH_PAGE', 'Publish page');
define('HEAD_REDIRECT_LINK', 'for this page if you want, you can select internal link for redirect');
define('HEAD_SWITCH', 'switch');
define('HEAD_EXTERNAL_LINK', 'or external link for redirect');
define('HEAD_CONTENT', 'Full Content');
define('HEAD_PARENT', 'Parent');
define('HEAD_PAGE_ACCESS', 'Page Access');
define('HEAD_PAGE_IMAGE', 'Page Image');
define('HEAD_WIDTH', 'Width');
define('HEAD_HEIGHT', 'Height');
define('HEAD_META_NAME', 'Meta Name');
define('HEAD_META_CONTENT', 'Meta Content');
define('HEAD_SEO_NAME', 'SEO Name');
define('HEAD_SEO_CONTENT', 'SEO Conent');
define('HEAD_KEYWORDS', 'Keywords');
define('HEAD_DESCRIPTION', 'Description');
define('HEAD_ROBOTS', 'Robots');
define('HEAD_SEO_TITLE', 'SEO Title');
define('HEAD_SEO_PATH', 'SEO Path');
define('HEAD_SEO_TEXT', 'SEO Text');
define('HEAD_LAST_UPDATE', 'Last update');
define('HEAD_REDIRECT', 'Redirect');
define('HEAD_SUB_PAGES', 'Sub pages');
define('HEAD_APPLY_REORDER', 'Apply reorder');
define('HEAD_DENIED', '--');
define('HEAD_SELECT_REDIRECT_LINK', 'Select link to redirect');
define('HEAD_SWITCH_TEXT_EDITOR', 'Turn on/off text editor');
define('HEAD_SHOW_HIDE', 'Show/Hide');
define('HEAD_ROOT_LEVEL', 'Root level');
define('HEAD_INACTIVE', 'notact');
define('HEAD_MODULE_NOT_SELECT', 'module not select');
define('HEAD_ALL_CHILD', 'all child');
define('HEAD_APPLY_TO_ALL_CHILD', 'Aplly to all child category!');
define('HEAD_FILE', 'File');
define('HEAD_NOT_SELECT', 'not select');
define('HEAD_GENERATE', 'Generate');
define('HEAD_COPY', 'Copy');
define('HEAD_NO_PUBLISH', 'not publish');
define('HEAD_PUBLISH', 'publish');
define('HEAD_ADD_VIEW_SUB_PAGES', 'Add/View SubPages');
define('HEAD_CLEAR', 'Clear');
define('HEAD_BANNER_IMAGE', 'Banner image');
define('HEAD_BANNER_TEXT', 'Banner text');
define('HEAD_SELECT_FROM_LIST', 'Select from list');
define('HEAD_META_DATA', 'META data');
define('HEAD_SEO_DATA', 'SEO data');
define('HEAD_PAGE_IMAGE_PREVIEW', 'Img preview');
define('HEAD_TITLE_REDIRECT', 'Redirect');
define('HEAD_SHORT_CONTENT', 'Short content');
define('HEAD_ATTACH_FILE', 'Attach Page File');
define('HEAD_ATTACH_FILES', 'Attach Page Files');
define('HEAD_ITEMS', 'count');
define('HEAD_PRODUCT_CODE', 'Product code');
define('HEAD_PRODUCT_PRICE', 'Product price');
define('HEAD_PRODUCT_CPRICE', 'Product price (kit)');
define('HEAD_PRODUCT_DISCOUNT', 'Discount (%)');
define('HEAD_OPEN_MANAGER', 'Open manager');
define('HEAD_FILES_MANAGER_ACCESS', '<b>"File manager"</b> will be accsess after create user');
define('HEAD_IMAGE_PARAMS', 'Image params');
define('HEAD_PAGE_SETTINGS', 'Page settings');

define('ALERT_EMPTY_PAGE_TITLE', 'Empty page title!');
define('STATUS_SEND', 'Sending...');

define('OPTION_YES', 'Yes');
define('OPTION_NO', 'No');

define('TOTAL_PAGES', 'Total');
define('PAGER_PAGE', 'Pages');

// Forms
define('FEEDBACK_FIRST_NAME', "Name");
define('FEEDBACK_LAST_NAME', 'Surname');
define('FEEDBACK_DATE_OF_BIRTH', 'Birthday');
define('FEEDBACK_CONTACT_EMAIL', 'Card Email');
define('FEEDBACK_COUNTRY', 'Country');
define('FEEDBACK_CITY', 'City');
define('FEEDBACK_STREET', 'Street');
define('FEEDBACK_HOUSE', 'House');
define('FEEDBACK_APARTMENT', 'Apartment');
define('FEEDBACK_APT', 'Apt');
define('FEEDBACK_STATE', 'Region');
define('FEEDBACK_TEL', 'Phone');
define('FEEDBACK_FAX', 'Fax');
define('FEEDBACK_EMAIL', 'E-mail');
define('FEEDBACK_ADDRESS', 'Address');
define('FEEDBACK_CODE', 'Code');
define('FEEDBACK_CODE_CASE', 'Insensitive symbols');
define('FEEDBACK_CONFIRMATION_CODE', 'Confirmation Code');
define('FEEDBACK_STRING_TEXT', 'Message');
define('FEEDBACK_FILLING', "Required");
define('FEEDBACK_STRING_SEND', 'Send');
define('FEEDBACK_ALERT_ERROR', 'Please fill out all required fields!');
define('FEEDBACK_STRING_SEND_ERROR', 'Send your request failed!');
define('FEEDBACK_MESSAGE_SEND_ERROR', 'Your message could not be send');
define('FEEDBACK_ERROR_INPUT_STRING', 'Enter:');
define('FEEDBACK_ERROR_INPUT_EMAIL', 'Please enter a valid e-mail');
define('FEEDBACK_STRING_SEND_EMAIL', 'Thank you, your message has been sent');
define('FEEDBACK_HEADER_SEND_LINK', 'Sending this article link...');
define('FEEDBACK_RECIPIENT_NAME', "Recipient name");
define('FEEDBACK_EMAIL_ERROR', 'Please enter a valid email address!');
define('FEEDBACK_EMAIL_MULTI_ERROR', 'You cannot enter more than one address email. Or you have entered an incorrect email address!');
define('FEEDBACK_EMAIL_RESEND_ERROR', 'Error. You trying to send the same data again!');

define('ORDER_FIRST_NAME', "First Name");
define('ORDER_COMMENT', 'Comment');
define('ORDER_COUNTRY', 'Country');
define('ORDER_CITY', 'City');
define('ORDER_STREET', 'Street');
define('ORDER_HOUSE', 'House');
define('ORDER_APARTMENT', 'Apartment');
define('ORDER_APT', 'Ap.');
define('ORDER_STATE', 'State');
define('ORDER_TEL', 'Phone');
define('ORDER_FAX', 'Fax');
define('ORDER_EMAIL', 'E-mail');
define('ORDER_ADDRESS', 'Address');
define('ORDER_CODE', 'Code');
define('ORDER_CODE_CASE', 'Register free symbols');
define('ORDER_CONFIRMATION_CODE', 'Confirmation code');
define('ORDER_STRING_TEXT', 'Message');
define('ORDER_FILLING', "Required filed");
define('ORDER_STRING_SEND', 'Send');
define('ORDER_ALERT_ERROR', 'Fill all required fields!');
define('ORDER_STRING_SEND_ERROR', 'Error! Your query not send');
define('ORDER_MESSAGE_SEND_ERROR', 'Your massege was not send');
define('ORDER_ERROR_INPUT_STRING', 'Please type:');
define('ORDER_ERROR_INPUT_EMAIL', 'Please type correct email');
define('ORDER_STRING_COMPLETE', 'Your order is delivered!');
define('ORDER_HEADER_SEND_LINK', 'Sending link');
define('ORDER_EMAIL_ERROR', 'Please type correct email!');
define('ORDER_EMAIL_MULTI_ERROR', 'You can not type more than one email. Or your email is incorrect!');
define('ORDER_EMAIL_RESEND_ERROR', 'Error. You try to send the same data!');
define('ORDER_FILL_REQUIRED_FIELD', 'You must type the "%s" field');
define('NEW_ORDER_COMPLETED', 'You� order �%s is send');
define('NEW_ORDER_NUMBER', 'New order �%s');
define('ORDER_CONFIRM_LETTER_NOT_SEND', '�� ����������');
define('ORDER_CONFIRM_LETTER_SEND', '����������');
define('ORDER_CONFIRM_LETTER_ERROR', '<font color="red">���������� ���������, ��� ��� ����� �� ��������</font>');
define('ORDER_STATUS_CHANGED', '������� ������ ��');
define('ORDER_PAYMENT_CHANGED', '������� ������ ������ ��');
define('ORDER_SEND_CONFIRM', '�������� �����������');
define('ORDER_CONFIRMATION_SUBJECT', '������������� ������');

define('FEEDBACK_MESSAGE_FROM', 'Message from "%s" form');
define('FEEDBACK_CONFIRM_REQUIRED_FIELD', 'Confirm "%s" field!');
define('FEEDBACK_FILL_REQUIRED_FIELD', 'Fill "%s" field!');
define('FEEDBACK_FILL_REQUIRED_FIELD_CORRECT', 'Fill in the correct field "%s"!');
define('FEEDBACK_FILL_OUT_FORM', 'Employment applicant form');
define('FEEDBACK_SEND_FORM', 'Submit');
define('FEEDBACK_PERSONAL_DATA', 'Personal data');
define('FEEDBACK_SKYPE', 'Skype');
define('FEEDBACK_YEAR', 'Year');
define('FEEDBACK_MONTH', 'Month');
define('FEEDBACK_DAY', 'Date');
define('FEEDBACK_COMPANY_NAME', 'Company name');
define('FEEDBACK_COMPANY_ADD_MORE', 'Add one more company');
define('FEEDBACK_MORE_INFO', 'Additional information');
define('FEEDBACK_SELECT_EMPTY', '---');
define('FEEDBACK_GENDER', 'Gender');
define('FEEDBACK_AGE', 'Age');

define('COMPILE_SYSTEM_ERROR', 'An error occurred while generating "%s"!');
define('ACCESS_SYSTEM_ERROR', 'Access error');
define('ACCESS_PAGE_ERROR', 'Access to this page is closed.');
define('ENTER_SYSTEM_ERROR', 'An error occurred in the system');
define('ENTER_INPUT_ERROR', 'Entered "%s" is incorrect!');
define('TRY_AGAIN_ACTION', 'Try again');
define('TRY_AGAIN_TITLE', 'Please try again');
define('TRY_AGAIN_AFTER_TITLE', 'Please try again after');
define('GENDER_NO_METTER', 'No matter');
define('GENDER_FEMALE', 'Female');
define('GENDER_MALE', 'Male');
define('AGE_FROM', 'From');
define('AGE_TO', 'To');

define('FILES_UPLOAD', 'File Download with the action');
define('FILES_UPLOAD_ERROR_JAVASCRIPT', 'You have a problem with your javascript!');
define('FILES_UPLOAD_FILE_TITLE', 'Choose the file');
define('FILES_UPLOAD_MULTIPLE_SELECT_FILES', 'Select File');
define('FILES_UPLOADIFY_ADDED', 'Uploaded files');
define('FILES_UPLOADIFY_CLEAR', 'Clear');
define('FILES_UPLOADIFY_DOWNLOAD', 'Download');

define('WEBLIFE_CREATED_BY', 'Creating web sites Kiev');
define('WEBLIFE_COPYRIGHT', 'Web sites creating by WebLife&trade;');
define('WEBLIFE_PROJECT_COPYRIGHT', 'Creating Web Projects WebLife&trade;');
define('WEBLIFE_COPYRIGHT_SIMPLE', 'Creating a Personal Page');
define('WEBLIFE_DEVELOPED', 'The site was developed');

define('WORK_AREA', 'Work area');
define('WORK_AREA_NO_CONTENT', 'Page under construction!');
define('URL_GALLERY_BACK', 'Back to the gallery');
define('URL_NEWS_BACK', 'Back to news list');
define('URL_BACK_TO_LIST', 'Back to list');
define('URL_TO_SITE', 'Go to site');
define('URL_TO_SECTION', 'Go to section');
define('URL_TO_EXIT', 'Exit');
define('URL_LINK_FULL_SCREEN', 'Full screen');
define('URL_LINK_NORMAL_SCREEN', 'Normal screen');
define('URL_LINK_HTML_VERSION', 'HTML version');
define('SORT_BY', 'Sort by');
define('SORT_BY_DATE', 'Date');
define('SORT_BY_EXT', 'Type');
define('SORT_BY_NAME', 'Name');
define('SORT_BY_SIZE', 'Size');
define('STR_FILE_SIZE_KB', 'KB');
define('STR_FILE_SIZE_MB', 'MB');

define('CATALOG_REPRICE', 'Price replacement');
define('CATALOG_REPRICE_TITLE', 'Global price replacement');
define('CATALOG_REPRICE_FROM', 'Replace price with');
define('CATALOG_REPRICE_TO', 'Replaced price by');
define('CATALOG_REPRICE_UPDATED', 'It was updated objects: <u>%s</u>!');
define('CATALOG_REPRICE_CHANGES', 'Replaced the price <u>%s</u> by price <u>%s</u> !');
define('CATALOG_REPRICE_WARNING', 'This operation is globally change these prices. Cancellation can not be! Be careful!');

define('RECOVERY_TITLE', 'Recover password');
define('RECOVERY_CODE', 'Code');
define('RECOVERY_SUBJECT_CODE', 'Password recovery, confirmation');
define('RECOVERY_SUBJECT_PASS', 'Password recovery, password');
define('RECOVERY_SEND_CODE', 'You e-mailed with Code to restore password. Follow the instructions which are described in it!');
define('RECOVERY_SEND_PASS', 'You e-mailed with a new password. Follow the instructions which are described in it!');
define('RECOVERY_SEARCH_ERROR', 'User unfinded by your entered data! Check them out correctly.');
define('RECOVERY_ENTER_CODE', 'Please, enter the confirmation code sent to your E-mail');
define('RECOVERY_ENTER_CODE_ERROR', 'Wrong code or validity of the code has expired!');

define('REGISTER_STEP_NUMBER', 'Step �');
define('REGISTER_CODE', 'Code');
define('REGISTER_FILL_FIELD', 'Please fill out the form fields');
define('REGISTER_LOGIN_UNIQUE_ERROR', 'You entered username %s already busy with somebody, come up with a new login');
define('REGISTER_EMAIL_UNIQUE_ERROR', 'You entered e-mail %s is already registered. Use the password recovery.');
define('REGISTER_SUBJECT_CONFIRM', 'Confirm User Registration');
define('REGISTER_SUBJECT_SUCCSESS', 'Successful registration');
define('REGISTER_SEND_CONFIRMCODE', 'You e-mailed to confirm registration. Follow the instructions which are described in it!');
define('REGISTER_ENTER_CONFIRMCODE', 'Please, enter the confirmation code sent to your registered E-mail');
define('REGISTER_CONFIRM_ERROR_DATA', 'Someone changed your access data. Please contact the site administrator to manually confirm your registration!');
define('REGISTER_ENTER_CODE_ERROR', 'Invalid confirmation code registration!');
define('REGISTER_SEND_SUCCSESS', 'Your data is successfully saved and sent!');
define('REGISTER_SUCCSESS_TEXT', 'Registration is successful. <br/>For work, you will need to login');

define('BANNER_TITLE_EMPTY', 'Title is empty!');
define('BANNER_POSITION_EMPTY', 'Select position!');
define('BANNER_MODULE_EMPTY', 'Select module!');
define('WELCOME_TO_ADMIN', 'Welcome to admin zone!');

define('CONFIRM_COPY', 'Copy this page?');
define('CONFIRM_EMPTY', 'Confirm clear?');
define('CONFIRM_NOT_SAVE', 'Do not save changes?');
define('CONFIRM_DELETE', 'Are tou shure? Page and all related info will be deleted from all languages!');
define('CONFIRM_CHANGE_MENU_TYPE', 'Change menu type?');
define('CONFIRM_CHANGE_PAGE_TYPE', 'Change page type?');
define('CONFIRM_DELETE_CAT', 'Are tou shure? Page, all related info and child categories will be deleted from all languages!');

define('SELECTIONS', 'Selections');
define('COMPARE_LIST', '������ ���������');
define('SHOPPING_LIST', '������ �������');
define('ADD_TO_WISHLIST', '� ������ �������');
define('IN_WISHLIST', '� ������ �������');
define('WISHLIST_REMOVE_ALL', '������� ������ �������');

define('ADD_TO_COMPARE', '� ������ ���������');
define('IN_COMPARE', '� ������ ���������');
define('SITE_CURRENCY', '���');
define('BUY', '������');
define('IN_CART', '��� � �������');

define('LABEL_ON', '��������');
define('SELECTED_FILTERS', 'Selected filters');
define('REMOVE_ALL_FILTERS', 'Remove all filters');