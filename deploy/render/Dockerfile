# Toplist Hải Phòng on Render (free web service) with Supabase PostgreSQL.
#
# WordPress stays on the 6.5 branch because PG4WP (the PostgreSQL layer) is
# tested up to 6.5; Polylang 3.8.x is the last line that supports 6.5.
# Build context is the repository root (see render.yaml).

FROM wordpress:6.5.5-php8.2-apache

ARG WP_VERSION=6.5.13
ARG WP_SHA1=9037c70c7fd7e52c8dbc4e75c0d54052cd1ee05d
ARG POLYLANG_VERSION=3.8.11
ARG PG4WP_VERSION=3.4.1
ARG WPCLI_VERSION=2.11.0
ARG WPCLI_SHA512=adb12146bab8d829621efed41124dcd0012f9027f47e0228be7080296167566070e4a026a09c3989907840b21de94b7a35f3bfbd5f827c12f27c5803546d1bba

# PostgreSQL client extension for PG4WP, plus OS security updates.
RUN apt-get update \
	&& apt-get install -y --no-install-recommends libpq-dev unzip \
	&& docker-php-ext-install pgsql \
	&& apt-get upgrade -y \
	&& rm -rf /var/lib/apt/lists/*

# Latest security release of the 6.5 branch over the image's 6.5.5 core
# (wp-config-docker.php from the image is kept).
RUN curl -fsSL -o /tmp/wordpress.tar.gz "https://wordpress.org/wordpress-${WP_VERSION}.tar.gz" \
	&& echo "${WP_SHA1} */tmp/wordpress.tar.gz" | sha1sum -c - \
	&& tar -xzf /tmp/wordpress.tar.gz -C /usr/src/ \
	&& rm /tmp/wordpress.tar.gz \
	&& rm -rf /usr/src/wordpress/wp-content/plugins/akismet /usr/src/wordpress/wp-content/plugins/hello.php

# PG4WP: the pg4wp folder and its db.php drop-in in wp-content.
RUN curl -fsSL -o /tmp/pg4wp.zip "https://github.com/kevinoid/postgresql-for-wordpress/archive/refs/tags/v${PG4WP_VERSION}.zip" \
	&& unzip -q /tmp/pg4wp.zip -d /tmp/pg4wp \
	&& cp -r "/tmp/pg4wp/postgresql-for-wordpress-${PG4WP_VERSION}/pg4wp" /usr/src/wordpress/wp-content/pg4wp \
	&& cp /usr/src/wordpress/wp-content/pg4wp/db.php /usr/src/wordpress/wp-content/db.php \
	&& rm -rf /tmp/pg4wp /tmp/pg4wp.zip

# Polylang (installed here: plugins added from wp-admin would vanish on restart).
RUN curl -fsSL -o /tmp/polylang.zip "https://downloads.wordpress.org/plugin/polylang.${POLYLANG_VERSION}.zip" \
	&& unzip -q /tmp/polylang.zip -d /usr/src/wordpress/wp-content/plugins/ \
	&& rm /tmp/polylang.zip

# WP-CLI for the first-boot install and data import.
RUN curl -fsSL -o /usr/local/bin/wp "https://github.com/wp-cli/wp-cli/releases/download/v${WPCLI_VERSION}/wp-cli-${WPCLI_VERSION}.phar" \
	&& echo "${WPCLI_SHA512}  /usr/local/bin/wp" | sha512sum -c - \
	&& chmod +x /usr/local/bin/wp

COPY theme/toplist-haiphong /usr/src/wordpress/wp-content/themes/toplist-haiphong
COPY plugins/toplist-demo-content /usr/src/wordpress/wp-content/plugins/toplist-demo-content
COPY deploy/render/mu-plugins /usr/src/wordpress/wp-content/mu-plugins
COPY deploy/render/render-entrypoint.sh /usr/local/bin/render-entrypoint.sh
COPY deploy/render/apache2-setup-only /usr/local/bin/apache2-setup-only

RUN chown -R www-data:www-data /usr/src/wordpress \
	&& chmod +x /usr/local/bin/render-entrypoint.sh /usr/local/bin/apache2-setup-only

ENTRYPOINT ["render-entrypoint.sh"]
