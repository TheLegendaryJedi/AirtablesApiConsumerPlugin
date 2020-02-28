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
 */

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
        $result = api_get_values($table, $key);
        // Get the response of creating the shortcode
        $response = format_response($result);
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
 */
function api_airtables_consumer_style()
{
    wp_register_style( 'api_airtables_consumer_style', plugins_url('airtablesApiConsumer.css', __FILE__), array(), rand(111,9999), 'all');
    wp_enqueue_style( 'api_airtables_consumer_style' );
}

/**
 * A function to retrieve the repository information via
 * the API.
 * @param  $table The name of the table
 * @param  $key   Your key
 *
 */
 function api_get_values($table, $key)
 {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.airtable.com/v0/app0umLTMyA6xbTDK/' . $table . '?api_key=' . $key);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $output = curl_exec($ch);
    curl_close($ch);
    //$result = json_decode(trim($output), true);
    $result = json_decode($output);
    //print_r($result->records);

    return $result;
 }

 /**
  * This function needs to me custumized for each table.
  * TODO: make it dynamic for every case
 */
 function format_response($result)
 {
    $html_content = "";
    if (! empty($result->records)) {
        $html_content = '<div class="airtable_cards et_pb_row et_pb_row_1 et_pb_row_3col">';
        foreach ($result->records as $record) {
            if (isset($record->fields->Maintainer)) {
                $name = $record->fields->Name;
                //print_r($name);
                $maintainer = $record->fields->Maintainer;
                //print_r($maintainer);
                if($maintainer){
					$component = $record->fields->Component;
					//print_r($component);
					$github = $record->fields->Github;
					//print_r($github);
					$img = $record->fields->Photo[0]->url;
					//print_r($img);
                    $html_content .= '<div class="et_pb_column et_pb_column_1_3">'
                                    .'<img class="air_image" src="' . $img . '" height="275" width="275">'
                                    .'<h3>' . $name . '</h3>'
                                    .'<h3>' . $component . '</h3>'
                                    .'<a href="' . $github . '">GitHub</a>'
                                    .'</div>';
                }
            }
        }
        $html_content .= '</div>';
    }
    return $html_content;
 }