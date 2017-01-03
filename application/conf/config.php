<?php
// To change an option please edit between the single quotes below

// Database settings
$db_name = '';
$db_host = '';
$db_username = '';
$db_password = '';

// Set the ACARS frequency you monitor
$acars_frequency = '';

// The location of your ACARS monitoring station (IE: Charlotte, NC)
$location = '';

// The title for your ACARSD server website
$site_title = 'ACARSD server website';

// Change how often the ACARS messages are updated on the front page in seconds
// NOTE: A lower time will put more "stress" on your webserver as well as more requests for data
// Default: 5
$refresh_frequency = '5';

// Save images from Airliners.net to a "cache" folder, this will prevent the image from being
// retrieved for the same aircraft remotely time and time again.
// Default: true
$use_img_cache = true;

// Add your Google Maps API key below, this enables the use of Google Maps for position reports
$google_maps_api = '';

// Google Maps center location based on address
$google_maps_center = 'Charlotte, NC';

// Below you can set how the ACARS messages will appear on the main site.
// Output can contain HTML. If you are unfamiliar with PHP, please examine
// the default output below before changing anything.

// TODO: I will be creating a tool that will allow you to change the output
// without needing to change anything in the config file for ease of use.

/*
 *  Allowable Place holders
 *  
 *  %reg% - Aircraft Registration Number, Example: N123UW
 *  %ac_type% - Aircraft type information, Example: Airbus A320-214
 *  %mode% - ACARS mode, Example: 2
 *  %label% - Message label, Example: H1
 *  %label_info% - Message label information, Example: No Information to transmit
 *  %block_id% - Block ID number
 *  %msg_num% - Message Number
 *  %message% - ACARS message content
 *  %flt_id% - Flight ID, Example: US2066
 *  %flt_path% - Departure and Arrival airport, Example: KCLT-KDFW   NOTE: Flight information is parsed from ACARS messages and saved to the database and may not exist for all, if any flights
 *  %airline% - Airline
 *  %month% - Month of message
 *  %day% - Day of message
 *  %year% - Year of message
 *  %hour% - Hour of message
 *  %minute% - Minute of message
 *  %second% - Second of message
 *  
 */

$output_format = '<span class="maroon">ACARS mode: </span><span class="blue">%mode%</span> <span class="maroon">Aircraft reg: </span><span class="blue">%reg%</span> <span class="green">[%ac_type%]</span>' . "\n";
$output_format .= '<span class="maroon">Message label:</span> <span class="blue">%label%</span> <span class="green">[%label_info%]</span> <span class="maroon">Block id: <span><span class="blue">%block_id%</span> <span class="maroon">Msg no: </span><span class="blue">%msg_num%</span>' . "\n";
$output_format .= '<span class="maroon">Flight ID:</span> <span class="blue">%flt_id%</span> <span class="green">[%airline%]</span>' . "\n";
$output_format .= '<span class="maroon">Message content: </span>' . "\n";
$output_format .= '<span class="blue">%message%</span>' . "\n";
$output_format .= '<span class="black">----------------------------------------------------------[ %month%-%day%-%year% %hour%:%minute%:%second% ]-</span>' . "\n";

