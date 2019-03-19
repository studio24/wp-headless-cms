<?php
/**
 * Plugin Name:     S24 Wp Headless Cms
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     PLUGIN DESCRIPTION HERE
 * Author:          YOUR NAME HERE
 * Author URI:      YOUR SITE HERE
 * Text Domain:     s24-wp-headless-cms
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         S24_Wp_Headless_Cms
 */


require_once __DIR__ . '/vendor/autoload.php';

/**
 * Add meta box for Headless CMS options
 * - View page
 * - Display environment
 * - Clear cache
 *
 * Need to set folllowing WP config constant:
 * S24_FRONTEND_APP_ENV - environment
 * S24_FRONTEND_URL - frontend URL
 * S24_CLEAR_CACHE_TOKEN - secret token used to help authenticate clear cache request
 *
 * @see https://codex.wordpress.org/Plugin_API/Action_Reference/add_meta_boxes
 */
add_action("add_meta_boxes", function(){
	add_meta_box("s24-wp-headless-cms", "Headless CMS", function() {
		$url = get_permalink();

		// Set frontend URL (without trailing slash)
		$frontendUrl = rtrim(getenv('S24_FRONTEND_URL'), '/');

		$urlParts = parse_url($url);
		$path = $urlParts['path'];
		$link = $frontendUrl . $path;

		// Detect environment
		$env = getenv('S24_FRONTEND_APP_ENV');
		if (empty($env)) {
			$env = 'unknown';
		}
		switch ($env) {
			case 'prod':
				$colour = '#3F3';
				break;
			case 'test':
			case 'pre-prod':
			case 'dev':
			case 'staging':
			case 'uat':
				$colour = '#FF0';
				break;
			default:
				$colour = '#CCC';
				break;
		}
		$env = ucfirst($env);

		// Detect post type
		$type = get_post_type();

		// URLs are correct in WordPress for the following post types
		$correctUrls = ['post', 'page'];

		if (in_array($type, $correctUrls)) {

			echo <<<EOD

<p>Environment: <strong style="padding: 0.3em 0.6em; background-color: $colour">$env</strong></span></p>

<p><a href="$link">View page on front-end website</a></p>

<p><a href="TODO?path=$path">Clear cache</a></p>

EOD;

		} else {

			echo <<<EOD

<p>Environment: <strong style="padding: 0.3em 0.6em; background-color: $colour">$env</strong></span></p>

EOD;

		}

	}, null, "side", "high", null);
});


/**
 * AJAX action to send clear cache request to frontend
 *
 * @see https://codex.wordpress.org/Plugin_API/Action_Reference/wp_ajax_(action)
 * @see https://developer.wordpress.org/plugins/javascript/ajax/
 */
add_action( 'wp_ajax_s24_headless_cms_clear_cache', function(){

	$baseUrl = rtrim(getenv('S24_FRONTEND_URL'), '/');
	$url = filter_var($_POST['url'], FILTER_SANITIZE_URL);
	$token = getenv('S24_CLEAR_CACHE_TOKEN');

	if (empty($url) || empty($baseUrl) || empty($token)) {
		return;
	}

	$url = $baseUrl . '/clear-cache/' . $url;

	$client = new GuzzleHttp\Client();
	$res = $client->request('GET', $url, [
		'headers' => ['X-Cache-Token' => $token]
	]);

	switch ($res->getStatusCode()) {
		case 200:
			// Cache successfully cleared
			// @todo return 200 "Cache successfully cleared"
			break;
		case 401:
			// Request not authorised
			// @todo return 401 "Not authorised to clear the cache" $res->getReasonPhrase()
			break;
		case 404:
			// Cache key not found
			// @todo return 404 "Cache does not need to be cleared for this post" $res->getReasonPhrase()
			break;
		default:
			// Some other error
			// @todo return $res->getStatusCode() "Error" $res->getReasonPhrase()
	}

	wp_die();
});


/**
 * @todo Add custom JS
 *
 * @see https://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
 */
add_action('admin_enqueue_scripts', function($hook){
	if ( 'post.php' != $hook ) {
		return;
	}
	wp_enqueue_script( 'css_clear_cache_admin_js', plugins_url('ccs-custom') . '/js/headless-cms.js' );
});


