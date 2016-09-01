#!/bin/bash

set -e

: ${MEDIAWIKI_SITE_NAME:=MediaWiki}
: ${MEDIAWIKI_SITE_LANG:=en}
: ${MEDIAWIKI_ADMIN_USER:=admin}
: ${MEDIAWIKI_ADMIN_PASS:=rosebud}
: ${MEDIAWIKI_DB_HOST:=mysql}
: ${MEDIAWIKI_DB_PORT:=3306}
: ${MEDIAWIKI_DB_NAME:=mediawiki}
: ${MEDIAWIKI_DB_TYPE:=mysql}
: ${MEDIAWIKI_ENABLE_SSL:=false}
: ${MEDIAWIKI_UPDATE:=false}



# Wait for the DB to come up
while [ `/bin/nc $MEDIAWIKI_DB_HOST $MEDIAWIKI_DB_PORT < /dev/null > /dev/null; echo $?` != 0 ]; do
    echo "Waiting for database to come up at $MEDIAWIKI_DB_HOST:$MEDIAWIKI_DB_PORT..."
    sleep 1
done

export MEDIAWIKI_DB_TYPE MEDIAWIKI_DB_HOST MEDIAWIKI_DB_USER MEDIAWIKI_DB_PASSWORD MEDIAWIKI_DB_NAME

TERM=dumb php -- <<'EOPHP'
<?php
// database might not exist, so let's try creating it (just to be safe)
if (getenv('MEDIAWIKI_DB_TYPE') == 'mysql') {
	$mysql = new mysqli(getenv('MEDIAWIKI_DB_HOST'), getenv('MEDIAWIKI_DB_USER'), getenv('MEDIAWIKI_DB_PASSWORD'), '', (int) getenv('MEDIAWIKI_DB_PORT'));
	if ($mysql->connect_error) {
		file_put_contents('php://stderr', 'MySQL Connection Error: (' . $mysql->connect_errno . ') ' . $mysql->connect_error . "\n");
		exit(1);
	}
	if (!$mysql->query('CREATE DATABASE IF NOT EXISTS `' . $mysql->real_escape_string(getenv('MEDIAWIKI_DB_NAME')) . '`')) {
		file_put_contents('php://stderr', 'MySQL "CREATE DATABASE" Error: ' . $mysql->error . "\n");
		$mysql->close();
		exit(1);
	}
	$mysql->close();
}
EOPHP

cd /var/www/html
# FIXME: Keep php files out of the doc root.
echo "Checking config"
# If there is no LocalSettings.php, create one using maintenance/install.php

if [ ! -e "/conf/CustomSettings.php" ]; then
	cp /conf_default/CustomSettings.php /conf/CustomSettings.php
fi

if [ ! -e "/conf/composer.local.json" ]; then
	cp /conf_default/composer.local.json /conf/composer.local.json
fi
ln -sf /conf/composer.local.json composer.local.json

if [ -e "/conf/LocalSettings.php" ]; then
	ln -sf /conf/LocalSettings.php LocalSettings.php
fi

if [ ! -e "LocalSettings.php" -a ! -z "$MEDIAWIKI_SITE_SERVER" ]; then
	echo "Creating config"
	php maintenance/install.php \
		--confpath /conf \
		--dbname "$MEDIAWIKI_DB_NAME" \
		--dbport "$MEDIAWIKI_DB_PORT" \
		--dbserver "$MEDIAWIKI_DB_HOST" \
		--dbtype "$MEDIAWIKI_DB_TYPE" \
		--dbuser "$MEDIAWIKI_DB_USER" \
		--dbpass "$MEDIAWIKI_DB_PASSWORD" \
		--installdbuser "$MEDIAWIKI_DB_USER" \
		--installdbpass "$MEDIAWIKI_DB_PASSWORD" \
		--server "$MEDIAWIKI_SITE_SERVER" \
		--scriptpath "" \
		--lang "$MEDIAWIKI_SITE_LANG" \
		--pass "$MEDIAWIKI_ADMIN_PASS" \
		"$MEDIAWIKI_SITE_NAME" \
		"$MEDIAWIKI_ADMIN_USER"

        ln -sf /conf/LocalSettings.php LocalSettings.php
		# Append inclusion of /compose_conf/CustomSettings.php
        echo "@include('/conf/CustomSettings.php');" >> LocalSettings.php
fi

curl -sS https://getcomposer.org/installer | php
php composer.phar install --no-dev

# If LocalSettings.php exists, then attempt to run the update.php maintenance
# script. If already up to date, it won't do anything, otherwise it will
# migrate the database if necessary on container startup. It also will
# verify the database connection is working.
if [ -e "LocalSettings.php" -a $MEDIAWIKI_UPDATE = true ]; then
	echo >&2 'info: Running maintenance/update.php';
	php maintenance/update.php --quick --conf ./LocalSettings.php
fi

# Ensure images folder exists
mkdir -p images

# Fix file ownership and permissions
chown -R www-data: .
chmod 755 images

exec "$@"