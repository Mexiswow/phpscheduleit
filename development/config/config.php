<?php
error_reporting(E_ALL & ~E_NOTICE);

$conf['settings']['server.timezone'] = 'America/Chicago';   // look up here http://php.net/manual/en/timezones.php
$conf['settings']['allow.self.registration'] = 'true';  // this is email self registration on home page
$conf['settings']['admin.email'] = 'admin@example.com'; // notification email to admin
$conf['settings']['default.page.size'] = '50';  // number of records per page
$conf['settings']['enable.email'] = 'true';
$conf['settings']['default.language'] = 'en_US';    // find your language in phpScheduleIt/lang directory
$conf['settings']['allow.rss'] = 'true';
$conf['settings']['version'] = '2.0.0';
$conf['settings']['script.url'] = 'localhost';   // http://localhost/';
$conf['settings']['password.pattern'] = '/^[^\s]{6,}$/i';
$conf['settings']['schedule']['show.inaccessible.resources'] = 'true';
$conf['settings']['database']['type'] = 'mysql';
$conf['settings']['database']['user'] = 'schedule_user';
$conf['settings']['database']['password'] = 'password';
$conf['settings']['database']['hostspec'] = '127.0.0.1';
$conf['settings']['database']['name'] = 'phpscheduleit2';
$conf['settings']['reservation']['notify.created'] = 'true';    // notifying a booking event
$conf['settings']['phpmailer']['mailer'] = 'mail';
$conf['settings']['phpmailer']['smtp.host'] = '';   // 'smtp.university.ca'
$conf['settings']['phpmailer']['smtp.port'] = '';   // '25'
$conf['settings']['phpmailer']['smtp.secure'] = ''; // 'ssl'
$conf['settings']['phpmailer']['smtp.auth'] = '';   // 'Password'
$conf['settings']['phpmailer']['smtp.username'] = '';   // 'username'
$conf['settings']['phpmailer']['smtp.password'] = '';   // 'password'
$conf['settings']['image.upload.directory'] = 'Web/uploads/images'; // from root directory, ie public_html/phpscheduleit/Web/uploads/images should be set to Web/uploads/images
$conf['settings']['image.upload.url'] = 'uploads/images';   // from script.url
$conf['settings']['cache.templates'] = 'true';
$conf['settings']['plugins']['Authentication'] = '';
$conf['settings']['plugins']['Authorization'] = '';
$conf['settings']['plugins']['Permission'] = '';
$conf['settings']['install.password'] = ''; // any password
?>