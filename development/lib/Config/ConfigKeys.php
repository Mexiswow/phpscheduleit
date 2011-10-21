<?php
class ConfigKeys
{
	const ADMIN_EMAIL = 'admin.email';
	const ALLOW_REGISTRATION = 'allow.self.registration';
	const ALLOW_RSS = 'allow.rss';
	const DEFAULT_PAGE_SIZE = 'default.page.size';
	const ENABLE_EMAIL = 'enable.email';
	const LANGUAGE = 'default.language';
	const PASSWORD_PATTERN = 'password.pattern';
	const SCRIPT_URL = 'script.url';
	const SERVER_TIMEZONE = 'server.timezone';
	const VERSION = 'version';
	
	const SCHEDULE_SHOW_INACCESSIBLE_RESOURCES = 'show.inaccessible.resources';
	
	const DATABASE_TYPE = 'type';
	const DATABASE_USER = 'user';
	const DATABASE_PASSWORD = 'password';
	const DATABASE_HOSTSPEC = 'hostspec';
	const DATABASE_NAME = 'name';
	
	const PLUGIN_AUTHENTICATION = 'Authentication';
	const PLUGIN_AUTHORIZATION = 'Authorization';
	const PLUGIN_PERMISSION = 'Permission';

	const RESERVATION_NOTIFY_CREATED = 'notify.created';
	const RESERVATION_NOTIFY_UPDATED = 'notify.updated';
	
	const IMAGE_UPLOAD_DIRECTORY = 'image.upload.directory';
	const IMAGE_UPLOAD_URL = 'image.upload.url';

	const CACHE_TEMPLATES = 'cache.templates';

	const INSTALLATION_PASSWORD = 'install.password';
}

class ConfigSection
{
	const DATABASE = 'database';
	const PLUGINS = 'plugins';
	const RESERVATION = 'reservation';
	const SCHEDULE = 'schedule';
}

?>