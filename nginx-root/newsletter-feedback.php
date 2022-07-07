<?php

// Load Sprint functions.
require_once "/var/www/ghost/system/secure/sprint-tools.php";
require_once "/var/www/ghost/system/secure/sprint-configuration.php";

// Tell Google to get lost.
header("X-Robots-Tag: noindex, nofollow", true);

// Get feedback value.
if (isset($_GET["feedback"])) {
    $feedback = strtolower($_GET["feedback"]);
} else {
    $feedback = NULL;
}

// Make sure feedback is approved value.
if (!in_array($feedback, ['yes', 'meh', 'no'], TRUE)) {
    header("Location: https://www.sidelinesprint.com/", true, 303);
    exit();
}

// Get campaign name.
if (isset($_GET["utm_campaign"])) {
    $campaign = urldecode($_GET["utm_campaign"]);
} else {
    $campaign = NULL;
}

// Get IP address.
$ip_address = strval($_SERVER["REMOTE_ADDR"]) ?? NULL;

// Get current time.
$timestamp_utc = gmdate("Y-m-d H:i:s");

// Open database connection.
$db_cxn = pg_connect($subscriber_db_cxn_str);

// Insert feedback into table.
$insert_feedback_query = "INSERT INTO newsletter_feedback
                        (campaign, liked_newsletter, ip_address, timestamp_utc)
                        VALUES ($1, $2, $3, $4)";
$insert_feedback_result = pg_query_params(
    $db_cxn,
    $insert_feedback_query,
    array($campaign, $feedback, $ip_address, $timestamp_utc)
);

// If error, send alert.
if ($insert_feedback_result == FALSE) {
    server_alert("Error pushing newsletter feedback to database.");
    header("Location: https://www.sidelinesprint.com/", true, 303);
    exit();
}

// Close DB connection.
pg_close($db_cxn);

// Set flow based on feedback.
if ($feedback == "yes") {

    // Redirect to survey.
    header("Location: -", true, 303);
    exit();

} elseif ($feedback == "meh") {

    // Redirect to survey.
    header("Location: -", true, 303);
    exit();

} elseif ($feedback == "no") {

    // Redirect to survey.
    header("Location: -", true, 303);
    exit();

} else {

    header("Location: https://www.sidelinesprint.com/", true, 303);
    exit();

}
