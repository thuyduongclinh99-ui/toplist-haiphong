#!/bin/bash
# Container start on Render. The disk is wiped on every restart, so each boot:
#   1. lets the official entrypoint copy WordPress and write wp-config.php,
#   2. installs WordPress into Supabase the first time (data persists there),
#   3. starts Apache on $PORT and, on the first boot only, imports the sample
#      content in the background.
set -euo pipefail

PORT="${PORT:-10000}"
SITE_URL="${RENDER_EXTERNAL_URL:-http://localhost:${PORT}}"
cd /var/www/html

wpcli() {
	runuser -u www-data -- wp --path=/var/www/html --url="$SITE_URL" "$@"
}

# Render routes traffic to $PORT.
sed -ri "s/^Listen 80\$/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-enabled/000-default.conf

# Apache's own redirects (e.g. /wp-admin → /wp-admin/) must point at the public
# HTTPS address, not the internal http://host:$PORT behind Render's proxy.
if [ -n "${RENDER_EXTERNAL_HOSTNAME:-}" ]; then
	printf 'ServerName https://%s:443\nUseCanonicalName On\n' "$RENDER_EXTERNAL_HOSTNAME" > /etc/apache2/conf-enabled/render-public-url.conf
fi

docker-entrypoint.sh apache2-setup-only

# Supabase can take a moment to wake up from a pause. Check PostgreSQL
# directly: WordPress itself errors out until it has been installed.
for attempt in $(seq 1 10); do
	if php -r '
		[$host, $port] = array_pad(explode(":", getenv("WORDPRESS_DB_HOST")), 2, "5432");
		$conn = @pg_connect(sprintf("host=%s port=%s dbname=%s user=%s password=%s connect_timeout=5",
			$host, $port, getenv("WORDPRESS_DB_NAME"), getenv("WORDPRESS_DB_USER"), getenv("WORDPRESS_DB_PASSWORD")));
		exit($conn ? 0 : 1);'; then
		break
	fi
	echo "Waiting for the database (attempt ${attempt})..."
	sleep 6
done

if ! wpcli core is-installed >/dev/null 2>&1; then
	echo "Installing WordPress..."
	wpcli core install \
		--title="Toplist Hải Phòng" \
		--admin_user="${WP_ADMIN_USER:?Set WP_ADMIN_USER on Render}" \
		--admin_password="${WP_ADMIN_PASSWORD:?Set WP_ADMIN_PASSWORD on Render}" \
		--admin_email="${WP_ADMIN_EMAIL:?Set WP_ADMIN_EMAIL on Render}" \
		--skip-email
	wpcli theme activate toplist-haiphong
	wpcli plugin activate polylang toplist-demo-content
fi

# Translation files live on the wiped disk, so fetch them on every boot.
wpcli language core install vi --activate >/dev/null 2>&1 || echo "Could not download the Vietnamese language pack."

# Keep the WordPress tables out of Supabase's public REST API; repair images.
wpcli eval-file /var/www/html/wp-content/mu-plugins/supabase/setup.php || true

if [ "$(wpcli option get thp_demo_imported 2>/dev/null || true)" != "1" ]; then
	(
		echo "Importing sample content..."
		wpcli eval 'foreach ( array( "setup", "posts", "images", "menus", "languages" ) as $s ) { echo $s, ": ", thp_demo_run_step_fully( $s ), "\n"; }' \
			&& wpcli eval 'echo "english: ", thp_demo_run_step_fully( "english" ), "\n";' \
			&& wpcli rewrite flush \
			&& wpcli option update thp_demo_imported 1 \
			&& echo "Sample content imported."
	) &
fi

exec docker-entrypoint.sh apache2-foreground
