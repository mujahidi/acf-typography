#!/usr/bin/env bash
# Google Fonts cache tests (3.3.0 steps A5, A7, B3), run against a local WordPress dev site.
#
# Part 1 runs tests/wp-eval/font-cache-unit.php inside WordPress (lock, newer result, scheduling, weights).
# Part 2 loads real admin and front-end pages and counts Google API calls per scenario, from the
# ACFT_HTTP lines that the HTTP mock writes to the site's debug log.
#
# The site needs: this plugin active, WP_DEBUG and WP_DEBUG_LOG on, no page cache, and
# tests/fixtures/mu-plugins/acft-test-http-mock.php and acft-test-constants.php copied to its
# mu-plugins (acft-test-options-page.php too, with ACF Pro, for check 1f).
# Settings come from the environment or tests/.env; see tests/.env.example.
# It changes site options while it runs and restores them at the end. Dev only; never ship.
#
# Usage: bash tests/run-font-cache-tests.sh [--restore]
#   --restore  only put back the site options saved by a run that did not finish
# shellcheck disable=SC2016 # the PHP passed to wp eval is single-quoted on purpose
set -u
cd "$(dirname "${BASH_SOURCE[0]}")/.." || exit 1

# settings: environment first, then tests/.env, then defaults
if [ -f tests/.env ]; then
	env_before=$(declare -p WP_CLI CACHE_FLUSH SITE_URL 2>/dev/null)
	# shellcheck source=/dev/null
	. tests/.env
	eval "$env_before"
fi
WP_CLI="${WP_CLI:-wp}"
CACHE_FLUSH="${CACHE_FLUSH:-$WP_CLI cache flush}"

SCRATCH="$(mktemp -d)"
trap 'rm -rf "$SCRATCH"' EXIT
COOKIE_HDR="$SCRATCH/cookie-header.txt"
PASS=0; FAIL=0; SKIP=0

wpcli() { bash -c "$WP_CLI \"\$@\"" wp-cli "$@"; }
wpe()   { wpcli eval "$1" 2>/dev/null; }
flush() { bash -c "$CACHE_FLUSH" >/dev/null 2>&1; }
# the debug log is read through WP-CLI, so it works when WordPress runs in a container or VM
LOGPHP='$l = ini_get("error_log"); clearstatcache();'
off()   { wpe "$LOGPHP"' echo is_file( $l ) ? filesize( $l ) : 0;'; }
newlog(){ wpe "$LOGPHP"' echo (string) @file_get_contents( $l, false, null, '"$1"' );'; }
calls() { wpe "$LOGPHP"' echo substr_count( (string) @file_get_contents( $l, false, null, '"$1"' ), "ACFT_HTTP" );'; }
admin() { curl -sS -o "$SCRATCH/last.html" -H "@$COOKIE_HDR" "$SITE_URL$1"; }
front() { curl -sS -o "$SCRATCH/last.html" "$SITE_URL$1"; }
check() { # name expected actual
	if [ "$2" = "$3" ]; then PASS=$((PASS+1)); echo "PASS  $1  ($3)"; else FAIL=$((FAIL+1)); echo "FAIL  $1  expected=$2 actual=$3"; fi
}
skip()  { SKIP=$((SKIP+1)); echo "SKIP  $1  ($2)"; }
# state helpers: cache summary as "count|has_error|fetched_recent"
state() { wpe '$c=acft_get_google_fonts_cache(); echo count($c["families"]),"|",($c["error"]!==""?"err":"noerr"),"|",($c["fetched"]>time()-120?"fresh":"old");'; }
# set settings key without firing the save hooks (so setup never fetches)
setkey() { wpe 'remove_all_actions("update_option_acft_settings"); remove_all_actions("add_option_acft_settings"); remove_all_filters("pre_update_option_acft_settings"); update_option("acft_settings", array("google_key"=>"'"$1"'"));'; }
# constants for the site, as if set in wp-config.php; WP-Cron stays off so background fetches never land in a count
consts() { wpe 'update_option("acft_test_constants", array("DISABLE_WP_CRON"=>true'"${1:+, $1}"'));'; }
scheduled() { wpe 'echo wp_next_scheduled("acft_refresh_google_fonts_event") ? "yes" : "no";'; }
unsched() { wpe 'wp_clear_scheduled_hook("acft_refresh_google_fonts_event");'; }

restore() {
	wpe '$b = get_option("acft_test_backup");
		if ( ! is_array( $b ) ) { echo "nothing to restore"; return; }
		remove_all_actions("update_option_acft_settings"); remove_all_actions("add_option_acft_settings"); remove_all_filters("pre_update_option_acft_settings");
		foreach ( array("acft_settings"=>"settings", "acft_google_fonts"=>"fonts", "acft_test_http_mock"=>"mock", "acft_test_constants"=>"consts", "options_site_typo"=>"site_typo", "_options_site_typo"=>"site_typo_ref") as $o => $k ) {
			false === $b[$k] ? delete_option($o) : update_option($o, $b[$k], "acft_google_fonts" === $o ? false : null);
		}
		wp_clear_scheduled_hook("acft_refresh_google_fonts_event"); acft_google_fonts_lock(false); delete_option("acft_test_backup");
		echo "site options restored";'
	flush
}

if [ "${1:-}" = "--restore" ]; then restore; echo; exit 0; fi

# --- preflight ---------------------------------------------------------------
PRE=$(wpcli eval 'echo "plugin=", function_exists("acft_refresh_google_fonts") ? "ok" : "missing", "\n";
	echo "log=", WP_DEBUG ? ini_get("error_log") : "", "\n";
	echo "home=", untrailingslashit(home_url()), "\n";
	echo "optpage=", function_exists("acf_get_field") && acf_get_field("field_acft_opt_typo") ? 1 : 0, "\n";
	echo "backup=", false === get_option("acft_test_backup") ? "none" : "left", "\n";
	foreach ( array("acft-test-http-mock.php", "acft-test-constants.php") as $f ) echo "md5:$f=", is_file(WPMU_PLUGIN_DIR."/$f") ? md5_file(WPMU_PLUGIN_DIR."/$f") : "missing", "\n";' 2>"$SCRATCH/pre.err")
val() { printf '%s\n' "$PRE" | sed -n "s/^$1=//p"; }
die() { echo "Cannot run: $1" >&2; exit 2; }

[ "$(val plugin)" = ok ] || { tail -5 "$SCRATCH/pre.err" >&2; die "WP-CLI failed or the plugin is not active (WP_CLI=\"$WP_CLI\"). See tests/.env.example."; }
[ -n "$(val log)" ] || die "turn on WP_DEBUG and WP_DEBUG_LOG in the site's wp-config.php."
[ "$(val backup)" = none ] || die "a previous run did not finish. Run: bash tests/run-font-cache-tests.sh --restore"
for f in acft-test-http-mock.php acft-test-constants.php; do
	[ "$(val "md5:$f")" = "$(md5sum "tests/fixtures/mu-plugins/$f" | cut -d' ' -f1)" ] || die "copy tests/fixtures/mu-plugins/$f to the site's mu-plugins folder (missing or out of date)."
done
SITE_URL="${SITE_URL:-$(val home)}"
SITE_URL="${SITE_URL%/}"
code=$(curl -sS -o /dev/null -w '%{http_code}' "$SITE_URL/") || true
[ "$code" = 200 ] || die "$SITE_URL/ answered HTTP $code. Set SITE_URL in tests/.env."
echo "Site: $SITE_URL   Debug log: $(val log)"

# --- setup -------------------------------------------------------------------
START=$(off)
umask 077
wpe '$u=get_users(array("role"=>"administrator","number"=>1,"fields"=>"ID")); $id=(int)$u[0]; $e=time()+3*HOUR_IN_SECONDS;
	echo "Cookie: ",AUTH_COOKIE,"=",wp_generate_auth_cookie($id,$e,"auth"),"; ",SECURE_AUTH_COOKIE,"=",wp_generate_auth_cookie($id,$e,"secure_auth"),"; ",LOGGED_IN_COOKIE,"=",wp_generate_auth_cookie($id,$e,"logged_in");' > "$COOKIE_HDR"
umask 022
wpe 'update_option("acft_test_backup", array("settings"=>get_option("acft_settings"), "fonts"=>get_option("acft_google_fonts"), "mock"=>get_option("acft_test_http_mock"), "consts"=>get_option("acft_test_constants"), "site_typo"=>get_option("options_site_typo"), "site_typo_ref"=>get_option("_options_site_typo")), false);'
trap 'echo; echo "Interrupted: restoring the site options"; restore; echo; rm -rf "$SCRATCH"; exit 130' INT TERM
consts
unsched
setkey test-key-A
wpe 'update_option("acft_test_http_mock","ok");'
flush

# --- part 1: function checks ---------------------------------------------------
echo; echo "Part 1: tests/wp-eval/font-cache-unit.php"
wpcli eval-file - < tests/wp-eval/font-cache-unit.php 2>/dev/null | tee "$SCRATCH/unit.txt" | grep -v '^PASS'
UNIT=$(sed -n 's/^RESULT: \([0-9]*\) passed, \([0-9]*\) failed$/\1 \2/p' "$SCRATCH/unit.txt")
if [ -n "$UNIT" ]; then read -r up uf <<< "$UNIT"; PASS=$((PASS+up)); FAIL=$((FAIL+uf)); else FAIL=$((FAIL+1)); echo "FAIL  font-cache-unit.php did not finish"; fi
flush

echo; echo "Part 2: page loads"
# 1. upgrade from 3.2.3: option absent
wpe 'delete_option("acft_google_fonts");'; unsched; flush
O=$(off); front /; front '/?s=acft'; front /no-such-page-xyz/
check "1a upgrade: front end makes no call" 0 "$(calls "$O")"
check "1b upgrade: front end schedules a background fetch instead" yes "$(scheduled)"
unsched; flush
O=$(off); admin /wp-admin/
check "1c upgrade: first admin load makes 1 call" 1 "$(calls "$O")"
check "1d upgrade: list saved" "3|noerr|fresh" "$(state)"
check "1e option not autoloaded" "off" "$(wpe 'global $wpdb; $a=$wpdb->get_var("SELECT autoload FROM $wpdb->options WHERE option_name=\"acft_google_fonts\""); echo in_array($a,array("no","off","auto-off"),true)?"off":$a;')"
if [ "$(val optpage)" = 1 ]; then
	wpe 'update_field("field_acft_opt_typo", array("font_family"=>"Roboto","font_weight"=>"300"), "option");'
	flush; admin '/wp-admin/admin.php?page=acft-test-options'
	check "1f saved Roboto still selected on options page" 1 "$(grep -Eo '<option[^>]*"Roboto"[^>]*selected|<option[^>]*selected[^>]*"Roboto"' "$SCRATCH/last.html" | head -1 | wc -l)"
else
	skip "1f saved Roboto still selected on options page" "needs ACF Pro and acft-test-options-page.php"
fi

# 2. fresh cache: no refetch
flush; O=$(off); admin /wp-admin/; admin /wp-admin/edit.php
check "2 fresh cache: no call" 0 "$(calls "$O")"

# 3. stale (8 days) -> refetch
wpe '$c=get_option("acft_google_fonts"); $c["fetched"]=time()-8*DAY_IN_SECONDS; update_option("acft_google_fonts",$c,false);'; flush
O=$(off); admin /wp-admin/
check "3 stale list: 1 call" 1 "$(calls "$O")"

# 4. key changed -> refetch
setkey test-key-B; flush
O=$(off); admin /wp-admin/
check "4 key changed: 1 call" 1 "$(calls "$O")"

# 5. failure with no list: error saved, then 1h backoff
wpe 'delete_option("acft_google_fonts"); update_option("acft_test_http_mock","error400");'; flush
O=$(off); admin /wp-admin/
check "5a error400: 1 call" 1 "$(calls "$O")"
check "5b error stored, no list" "0|err|old" "$(state)"
flush; O=$(off); admin /wp-admin/; admin '/wp-admin/options-general.php?page=acf-typography-field'
check "5c backoff: no call within the hour" 0 "$(calls "$O")"
check "5d settings page shows Google's message" 1 "$(grep -c 'Google Fonts could not be loaded: API key not valid' "$SCRATCH/last.html")"

# 6. failure keeps the old list
wpe 'update_option("acft_google_fonts", array("families"=>array("Roboto","Lato"),"fetched"=>time()-8*DAY_IN_SECONDS,"attempted"=>0,"error"=>"","key_hash"=>md5("test-key-B")), false); update_option("acft_test_http_mock","wp_error");'; flush
O=$(off); admin /wp-admin/
check "6a wp_error on stale list: 1 call" 1 "$(calls "$O")"
check "6b old list kept, error set" "2|err|old" "$(state)"

# 7. backoff expires after an hour
wpe '$c=get_option("acft_google_fonts"); $c["attempted"]=time()-2*HOUR_IN_SECONDS; update_option("acft_google_fonts",$c,false); update_option("acft_test_http_mock","ok");'; flush
O=$(off); admin /wp-admin/
check "7a retry after 1h: 1 call" 1 "$(calls "$O")"
check "7b recovered, error cleared" "3|noerr|fresh" "$(state)"

# 8. no key anywhere: no call
setkey ""; wpe 'delete_option("acft_google_fonts");'; flush
O=$(off); admin /wp-admin/; front /
check "8 no key: no call" 0 "$(calls "$O")"

# 9. ACFT_GOOGLE_API_KEY constant only (no saved key)
consts '"ACFT_GOOGLE_API_KEY"=>"const-key-1"'; unsched; flush
O=$(off); front /
check "9a constant, front end: no call" 0 "$(calls "$O")"
check "9b constant, front end schedules a background fetch" yes "$(scheduled)"
unsched; flush
O=$(off); admin /wp-admin/
check "9c constant, admin: 1 call" 1 "$(calls "$O")"
check "9d cache keyed to the constant" "match" "$(wpe '$c=get_option("acft_google_fonts"); echo $c["key_hash"]===md5("const-key-1")?"match":"nomatch";')"
consts '"ACFT_GOOGLE_API_KEY"=>"const-key-2"'; flush
O=$(off); admin /wp-admin/
check "9e constant changed in wp-config: 1 call" 1 "$(calls "$O")"

# 10. constant beats saved key
setkey saved-key; flush
O=$(off); admin /wp-admin/
check "10 constant + saved key: constant wins, no refetch" 0 "$(calls "$O")"

# 11. legacy YOUR_API_KEY defined by the site
consts '"YOUR_API_KEY"=>"legacy-key"'; wpe 'delete_option("acft_google_fonts");'; setkey ""; flush
O=$(off); admin /wp-admin/
check "11a legacy constant: 1 call" 1 "$(calls "$O")"
check "11b cache keyed to legacy constant" "match" "$(wpe '$c=get_option("acft_google_fonts"); echo $c["key_hash"]===md5("legacy-key")?"match":"nomatch";')"
check "11c deprecation notice logged" 1 "$(newlog "$O" | grep -c 'YOUR_API_KEY constant is deprecated' | awk '{print ($1>0)?1:0}')"
consts

# 12. saving settings fetches right away (add + update hooks, and an unchanged save)
wpe 'delete_option("acft_settings"); delete_option("acft_google_fonts");'
O=$(off); wpe 'add_option("acft_settings", array("google_key"=>"save-key-1"));'
check "12a add_option save: 1 call" 1 "$(calls "$O")"
O=$(off); wpe 'update_option("acft_settings", array("google_key"=>"save-key-2"));'
check "12b update_option save: 1 call" 1 "$(calls "$O")"
check "12c key hash follows the save" "match" "$(wpe '$c=get_option("acft_google_fonts"); echo $c["key_hash"]===md5("save-key-2")?"match":"nomatch";')"
wpe 'update_option("acft_test_http_mock","error400"); acft_refresh_google_fonts(true); update_option("acft_test_http_mock","ok");'
check "12d setup: fetch failed, error stored" "3|err|fresh" "$(state)"
O=$(off); wpe 'update_option("acft_settings", get_option("acft_settings"));'
check "12e saving the same key again: 1 call" 1 "$(calls "$O")"
check "12f error cleared after the unchanged save" "3|noerr|fresh" "$(state)"

# 13. admin-ajax (heartbeat, logged in and logged out) never fetches
wpe '$c=get_option("acft_google_fonts"); $c["fetched"]=0; update_option("acft_google_fonts",$c,false);'; unsched; flush
O=$(off)
curl -sS -o /dev/null -H "@$COOKIE_HDR" -d 'action=heartbeat&interval=60' "$SITE_URL/wp-admin/admin-ajax.php"
curl -sS -o /dev/null -d 'action=heartbeat&interval=60' "$SITE_URL/wp-admin/admin-ajax.php"
curl -sS -o /dev/null -d 'action=nopriv_whatever' "$SITE_URL/wp-admin/admin-ajax.php"
check "13a admin-ajax with stale list: no call" 0 "$(calls "$O")"
check "13b admin-ajax schedules a background fetch instead" yes "$(scheduled)"
unsched

# 14. deprecated acft_update_gf_json_file() still works
O=$(off); R=$(wpe '$u=get_users(array("role"=>"administrator","number"=>1,"fields"=>"ID")); wp_set_current_user((int)$u[0]); acft_update_gf_json_file("ignored"); echo "ran";')
check "14a old function does not fatal" ran "$R"
check "14b old function refreshes: 1 call" 1 "$(calls "$O")"
check "14c deprecation notice logged" 1 "$(newlog "$O" | grep -c 'acft_update_gf_json_file' | awk '{print ($1>0)?1:0}')"

# 15. JSON file gone and never recreated
check "15 google_fonts.json absent" absent "$(wpe 'echo file_exists(dirname((new ReflectionClass("acf_plugin_Typography"))->getFileName())."/google_fonts.json")?"present":"absent";')"

# 16. other failed responses keep the old list and save the reason
wpe 'update_option("acft_google_fonts", array("families"=>array("Roboto","Lato"),"fetched"=>time()-8*DAY_IN_SECONDS,"attempted"=>0,"error"=>"","key_hash"=>md5(acft_get_google_api_key())), false); update_option("acft_test_http_mock","bad_body");'; flush
O=$(off); admin /wp-admin/
check "16a 200 without a font list: 1 call" 1 "$(calls "$O")"
check "16b old list kept, error set" "2|err|old" "$(state)"
check "16c error text" "Unexpected response from the Google Fonts API." "$(wpe 'echo acft_get_google_fonts_cache()["error"];')"
wpe '$c=get_option("acft_google_fonts"); $c["attempted"]=time()-2*HOUR_IN_SECONDS; update_option("acft_google_fonts",$c,false); update_option("acft_test_http_mock","error503_html");'; flush
O=$(off); admin /wp-admin/; admin '/wp-admin/options-general.php?page=acf-typography-field'
check "16d 503 with an HTML body: 1 call" 1 "$(calls "$O")"
check "16e old list kept, error set" "2|err|old" "$(state)"
check "16f settings page shows the HTTP status" 1 "$(grep -c 'Google Fonts could not be loaded: HTTP 503' "$SCRATCH/last.html")"

# --- restore -------------------------------------------------------------------
trap 'rm -rf "$SCRATCH"' INT TERM
echo; restore; echo
echo; echo "Other new debug.log lines (excluding ACFT_HTTP and expected deprecation notices):"; newlog "$START" | grep -v ACFT_HTTP | grep -v "YOUR_API_KEY constant is deprecated" | grep -v "acft_update_gf_json_file" | sed "s/^/  /" | head -30
echo; echo "RESULT: $PASS passed, $FAIL failed, $SKIP skipped"
[ "$FAIL" -eq 0 ]
