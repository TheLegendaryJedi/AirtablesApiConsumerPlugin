<?php
/*
Plugin Name: Airtables API consumer
Plugin URI: 
Description: Consumes and shows Airtables tables
Version: 1.0
Author: Gonçalo Atanásio
Author URI: 
Text Domain:
License: GPLv3
*/

// Register the shortcode
add_shortcode( 'airtablesAPI', 'api_airtables_consumer');
// Register the css
add_action( 'wp_enqueue_scripts', 'api_airtables_consumer_style');


/**
 * The main function to create the shortcode results
 * storing or retreiving the data from cache
 * @param  [type] $atts    [description]
 * @param  [type] $content [description]
 * @return [type]          [description]
 */


if ( ! function_exists('write_log')) {
    function write_log ( $log )  {
       if ( is_array( $log ) || is_object( $log ) ) {
          error_log( print_r( $log, true ) );
       } else {
          error_log( $log );
       }
    }
 }

 function api_airtables_consumer( $atts, $content = null )
{
    // Get the attributes
    extract( shortcode_atts ( array (
        'table' => 'none',
        'key' => 'none'
    ), $atts ));

    if ($table != null && $key != null)
    {
        // Generate the API results for the tags
        //$latestRelease = api_get_github_api_latest_release('https://api.github.com/repos/PX4/Firmware/releases/latest' . '?client_id=891e6e071147aebaf6c8&client_secret=7081e3f921a1a8f9bac79f0f1dc9d635a5b02671', $token);
        // Get the response of creating the shortcode
        //$response = api_format_github_repo_latest_release($latestRelease);
        // Return the response
        return $response;
    }
    else
    {
        return 'The table and key attributes are required, enter [airtablesAPI table="anTable" key="aKey"] to use this shortcode.';
    }
}

/**
 * A function to register the stylesheet
 * @return [type] [description]
 */
function api_airtables_consumer_style()
{
    wp_register_style( 'api_airtables_consumer_style', plugins_url('airtablesApiConsumer.css', __FILE__), array(), rand(111,9999), 'all');
    wp_enqueue_style( 'api_airtables_consumer_style' );
}

/**
 * A function to retrieve the repository information via
 * the GitHub API.
 * @param  $author The author of the GitHub repository
 * @param  $repo   The name of the GitHub repository
 * @param  $token  The API token used to access the GitHub API
 * @return         A decoded array of information about the GitHub repository
 */
 function api_get_github_api_latest_release($url, $token)
 {
    write_log('url');
    write_log($url);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
    curl_setopt($ch, CURLOPT_USERAGENT, 'Agent smith');
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $output = curl_exec($ch);
    curl_close($ch);
    $result = json_decode(trim($output), true);
    return $result;
 }

 function api_format_github_repo_latest_release($latestRelease)
 {
    $dateTimeString = explode("T", $latestRelease['published_at']);
    $myDateTime = DateTime::createFromFormat('Y-m-d', $dateTimeString[0]);
    $date = date_format($myDateTime,'F d, Y');

    $string = '
    <h1 class="vc_custom_heading headerCustom HeroHeadText">
        <a href="https://github.com/PX4/Firmware/releases" target=" _blank">
            PX4 Latest Stable Release ' . $latestRelease['tag_name'] .
        '</a>
    </h1>' .
    '<p class="vc_custom_heading HeroHeadDate">'
        . $date .
    '</p>';
    return $string;
 }