<?php

// Required packages.
require_once "/var/www/ghost/system/secure/sprint-configuration.php";
require_once "/var/www/ghost/system/nginx-root/vendor/autoload.php";
use Hashids\Hashids;
use Postmark\PostmarkClient;

////////////////////////////////////////////////////////////////////////////////

// Wrapper to send email on event.
function server_alert($message) {

  // Define global variables.
  global $postmark_server_alert_stream_id;

  // Define email variables.
  $client = new PostmarkClient($postmark_server_alert_stream_id);
  $from_email = "-";
  $to_email = "-";
  $subject = "Server Alert";
  $html_body = $message;
  $text_body = "Please view the HTML content of this email for the alert.";
  $tag = "server-alerts";
  $track_opens = true;
  $reply_to = NULL;
  $cc = NULL;
  $bcc = NULL;
  $header_array = NULL;
  $attachment_array = NULL;
  $track_links = "None";
  $metadata_array = NULL;
  $message_stream = "server-alerts";

  // Send an email.
  $sendResult = $client->sendEmail(
    $from_email,
    $to_email,
    $subject,
    $html_body,
    $text_body,
    $tag,
    $track_opens,
    $reply_to,
    $cc,
    $bcc,
    $header_array,
    $attachment_array,
    $track_links,
    $metadata_array,
    $message_stream
  );

}

////////////////////////////////////////////////////////////////////////////////

// Parse CURL headers.
function get_headers_from_curl_response($response) {

  // Parse text from headers.
  $headers = array();
  $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));

  // Format each individual line.
  foreach (explode("\r\n", $header_text) as $i => $line) {
    if ($i === 0) {
      $headers["http_code"] = $line;
    } else {
      list($key, $value) = explode(": ", $line);
      $headers[$key] = $value;
    }
  }

  // Return parsed headers.
  return $headers;

}

////////////////////////////////////////////////////////////////////////////////

// Set URL based on query string.
function query_string_passthrough($url, $param_array=NULL) {

  // Parse array to string.
  $query_string = http_build_query($param_array) ?? NULL;

  // Append to URL if not empty.
  if ((!empty($query_string)) && ($query_string != "")) {
    $url = $url . "&" . $query_string;
  }

  // Return url.
  return $url;

}

////////////////////////////////////////////////////////////////////////////////

// Convert empty strings to nulls for database.
function empty_to_null($value) {

  // Quick comparison for strings.
  if (trim($value) === "") {
    return NULL;
  } else {
    return $value;
  }

}

////////////////////////////////////////////////////////////////////////////////

// Clean email address.
function clean_email($email) {

  // Lowercase and remove characters.
  $email = strtolower(htmlspecialchars($email));

  // Remove spaces.
  $email = str_replace(" ", "+", $email);

  // Split at @ symbol.
  $email_array = explode("@", $email);
  $email_name = $email_array[0];
  $email_domain = $email_array[1];

  // Fix issues with Gmail TLD.
  $google_rep_array = array("gmail.con", "gmail.comm", "gmail.coml",
                            "gmail.cm", "gmail.comcom", "gmail.cin",
                            "gamil.com", "gnail.com", "gqmil.com",
                            "gmail.co");
  foreach ($google_rep_array as $google_rep) {
    if ($email_domain == $google_rep) {
      $email_domain = "gmail.com";
    }
  }

  // Fix issues with AOL TLD.
  $aol_rep_array = array("aol.con", "aool.com", "ail.com",
                         "aol.co");
  foreach ($aol_rep_array as $aol_rep) {
    if ($email_domain == $aol_rep) {
      $email_domain = "aol.com";
    }
  }

  // Fix issues with iCloud TLD.
  $icloud_rep_array = array("icloud.con", "icloud.comm", "icloud.vom",
                            "ucloud.com", "icould.com", "icloud.co");
  foreach ($icloud_rep_array as $icloud_rep) {
    if ($email_domain == $icloud_rep) {
      $email_domain = "icloud.com";
    }
  }

  // Fix issues with Mac TLD.
  $mac_rep_array = array("mac.con", "mac.co");
  foreach ($mac_rep_array as $mac_rep) {
    if ($email_domain == $mac_rep) {
      $email_domain = "mac.com";
    }
  }

  // Fix issues with Me TLD.
  $me_rep_array = array("me.con", "me.co");
  foreach ($me_rep_array as $me_rep) {
    if ($email_domain == $me_rep) {
      $email_domain = "me.com";
    }
  }

  // Fix issues with Yahoo TLD.
  $yahoo_rep_array = array("yahoo.con", "yayoo.com", "yahoo.coml",
                           "yhaoo.com", "yahpo.com", "yahoo.co");
  foreach ($yahoo_rep_array as $yahoo_rep) {
    if ($email_domain == $yahoo_rep) {
      $email_domain = "yahoo.com";
    }
  }

  // Fix issues with Hotmail TLD.
  $hotmail_rep_array = array("hotamail.com", "hotmail.con", "hotmail.co");
  foreach ($hotmail_rep_array as $hotmail_rep) {
    if ($email_domain == $hotmail_rep) {
      $email_domain = "hotmail.com";
    }
  }

  // Fix issues with Outlook TLD.
  $outlook_rep_array = array("outlook.con", "outlool.com", "outlook.co");
  foreach ($outlook_rep_array as $outlook_rep) {
    if ($email_domain == $outlook_rep) {
      $email_domain = "outlook.com";
    }
  }

  // Rejoin split portions.
  $email = $email_name . "@" . $email_domain;

  // Make sure actually an email address.
  if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $result = array(
      "is_valid" => TRUE,
      "email" => $email
    );
  } else {
    $result = array(
      "is_valid" => FALSE,
      "email" => $email
    );
  }

  // Return result.
  return $result;

}

////////////////////////////////////////////////////////////////////////////////

// Add subscriber to Beehiiv.
function beehiiv_subscribe($email) {

  // Define global variables.
  global $beehiiv_external_embed_id, $beehiiv_publication_id;

  // Wrap in try block to catch errors gracefully.
  try {

    // Push email to Beehiiv. This is the only field we can send at this time.
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => "--",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 10,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => "{\"external_embed_id\":\"{$beehiiv_external_embed_id}\",\"publication_id\":\"{$beehiiv_publication_id}\",\"email\":\"{$email}\"}",
      CURLOPT_HTTPHEADER => array(
        "content-type: application/json"
      ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    // Check for curl error.
    if ($err) {
      throw new Exception("Error pushing to Beehiiv for " . $email);
    }

    // Make sure result was a success.
    $beehiiv_data = json_decode($response, true);
    if ($beehiiv_data["success"]) {
      // Do nothing, as this is what we want.
    } else {
      throw new Exception("Error pushing to Beehiiv for " . $email);
    }

    // Return successful status.
    $subscribe_result = array(
      "status" => "success",
      "email" => $email
    );
    return $subscribe_result;

  } catch (Exception $e) {

    // Return error for issues subscribing.
    $subscribe_result = array(
      "status" => "failure",
      "email" => $email,
      "info" => $e->getMessage()
    );
    return $subscribe_result;

  }

}

////////////////////////////////////////////////////////////////////////////////

// Sign up new user.
function subscribe($email, $ip_address=NULL, $source=NULL, $params=NULL, $referrer_header=NULL, $user_agent=NULL, $base_url=NULL) {

  // Define global variables.
  global $referral_hash_salt, $check_referral_hash_salt, $subscriber_db_cxn_str, $beehiiv_external_embed_id, $beehiiv_publication_id;

  // Wrap in try block to catch errors.
  try {

    // Substring signup parameters.
    if (!empty($params)) {
      $params = empty_to_null(substr($params, 0, 1024));
    }
    if (!empty($referrer_header)) {
      $referrer_header = empty_to_null(substr($referrer_header, 0, 1024));
    }

    // Create hash functions for ID strings.
    $referral_hash = new Hashids($referral_hash_salt);
    $check_referral_hash = new Hashids($check_referral_hash_salt);

    // Create connection to database.
    $db_cxn = pg_connect($subscriber_db_cxn_str);

    // Check if subscriber already exists.
    $exists_query = "SELECT *
                     FROM main_newsletter
                     WHERE email = $1";
    $exists_result = pg_query_params(
      $db_cxn,
      $exists_query,
      array($email)
    );

    // If error, throw exception.
    if ($exists_result == FALSE) {
      $internal_status_code = 901;
      throw new Exception("Error checking database for subscriber " . $email);
    }

    // Adjust flow based on whether subscriber already exists or not.
    if (pg_num_rows($exists_result) == 0) {

      /////////////////////////
      // NEW SUBSCRIBER FLOW //
      /////////////////////////

      // Generate unique IDs for subscriber.
      $unique_id = intval(microtime(TRUE) * 1000) . rand();
      $referral_id = $referral_hash->encode($unique_id);
      $check_referral_id = $check_referral_hash->encode($unique_id);

      // Create remaining fields for subscriber.
      $status = "Active";
      $referral_link = "https://www.sidelinesprint.com/refer?id={$referral_id}";
      $referral_display_link = "sidelinesprint.com/refer?id={$referral_id}";
      $check_referral_link = "https://www.sidelinesprint.com/my-referrals?token={$check_referral_id}";
      $check_referral_display_link = "sidelinesprint.com/my-referrals?token={$check_referral_id}";
      $first_subscribed_timestamp_utc = gmdate("Y-m-d H:i:s");
      $referred_by_id = NULL;
      $referred_by_email = NULL;
      $referral_count = 0;

      // Push subscriber data to subscriber database.
      $add_query = "INSERT INTO main_newsletter
                                (email, status, unique_id, referral_id, check_referral_id,
                                referral_link, referral_display_link, check_referral_link,
                                check_referral_display_link, signup_parameters, signup_source,
                                referred_by_id, referred_by_email, referral_count, first_subscribed_timestamp_utc,
                                signup_ip_address, signup_referrer_header, signup_user_agent, signup_base_url)
                                VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, $15, $16, $17, $18, $19)";
      $add_result = pg_query_params(
        $db_cxn,
        $add_query,
        array(
          $email, $status, $unique_id, $referral_id,
          $check_referral_id, $referral_link,
          $referral_display_link, $check_referral_link,
          $check_referral_display_link, $params,
          $source, $referred_by_id, $referred_by_email,
          $referral_count, $first_subscribed_timestamp_utc, $ip_address,
          $referrer_header, $user_agent, $base_url
        )
      );

      // If error, throw exception.
      if ($add_result == FALSE) {
        $internal_status_code = 906;
        throw new Exception("Error creating subscriber in database for " . $email);
      }

      // Close DB connection.
      pg_close($db_cxn);

      // Push email to Beehiiv. This is the only field we can send at this time.
      $beehiiv_result = beehiiv_subscribe($email);
      if ($beehiiv_result["status"] != "success") {
        $internal_status_code = 909;
        throw new Exception("Error pushing to Beehiiv for " . $email);
      }

      // Return results.
      $subscribe_result = array(
        "status" => "success",
        "http_status_code" => 200,
        "internal_status_code" => 1000,
        "info" => "Successfully added subscriber",
        "email" => $email,
        "unique_id" => $unique_id,
        "referral_id" => $referral_id,
        "check_referral_id" => $check_referral_id
      );
      return $subscribe_result;

    } elseif (pg_num_rows($exists_result) == 1) {

      //////////////////////////////
      // EXISTING SUBSCRIBER FLOW //
      //////////////////////////////

      // Convert results to array.
      $exists_array = pg_fetch_array($exists_result, 0, PGSQL_ASSOC);
      $unique_id = $exists_array["unique_id"];
      $referral_id = $exists_array["referral_id"];
      $check_referral_id = $exists_array["check_referral_id"];

      if ($exists_array["status"] == "Active") {

        ////////////////////////////////////////////////////
        // If they are currently active, adjust behavior. //
        ////////////////////////////////////////////////////

        // Push email to Beehiiv anyways. This is the only field we can send at this time.
        $beehiiv_result = beehiiv_subscribe($email);
        if ($beehiiv_result["status"] != "success") {
          $internal_status_code = 902;
          throw new Exception("Error pushing to Beehiiv for " . $email);
        }

        // Set successful response message.
        $subscribe_result = array(
          "status" => "success",
          "http_status_code" => 200,
          "internal_status_code" => 1000,
          "info" => "Successfully added subscriber",
          "email" => $email,
          "unique_id" => $unique_id,
          "referral_id" => $referral_id,
          "check_referral_id" => $check_referral_id
        );
        return $subscribe_result;

      } elseif ($exists_array["status"] == "Banned") {

        ////////////////////////////////////////////////////
        // If they are currently banned, adjust behavior. //
        ////////////////////////////////////////////////////

        // Set response message.
        $subscribe_result = array(
          "status" => "banned",
          "http_status_code" => 200,
          "internal_status_code" => 602,
          "info" => "Subscriber is banned",
          "email" => $email,
          "unique_id" => $unique_id,
          "referral_id" => $referral_id,
          "check_referral_id" => $check_referral_id
        );
        return $subscribe_result;

      } elseif ($exists_array["status"] == "Unconfirmed") {

        /////////////////////////////////////////////////////////
        // If they are currently unconfirmed, adjust behavior. //
        /////////////////////////////////////////////////////////

        // Set to Active in database.
        $status = "Active";
        $confirmed_timestamp_utc = gmdate("Y-m-d H:i:s");
        $confirm_query = "UPDATE main_newsletter
                          SET status = $1,
                          confirmed_timestamp_utc = $2
                          WHERE email = $3";
        $confirm_result = pg_query_params(
          $db_cxn,
          $confirm_query,
          array(
            $status,
            $confirmed_timestamp_utc,
            $email
          )
        );

        // If error, throw exception.
        if ($confirm_result == FALSE) {
          $internal_status_code = 949;
          throw new Exception("Error confirming subscriber in database for " . $email);
        }

        // Close DB connection.
        pg_close($db_cxn);

        // Push email to Beehiiv. This is the only field we can send at this time.
        $beehiiv_result = beehiiv_subscribe($email);
        if ($beehiiv_result["status"] != "success") {
          $internal_status_code = 907;
          throw new Exception("Error pushing to Beehiiv for " . $email);
        }

        // Return results.
        $subscribe_result = array(
          "status" => "success",
          "http_status_code" => 200,
          "internal_status_code" => 1000,
          "info" => "Successfully added subscriber",
          "email" => $email,
          "unique_id" => $unique_id,
          "referral_id" => $referral_id,
          "check_referral_id" => $check_referral_id
        );
        return $subscribe_result;

      } else {

        ///////////////////////////////////////////////////////////
        // If they are currently anything else, adjust behavior. //
        ///////////////////////////////////////////////////////////

        // Reactivate existing record.
        $status = "Active";
        $resubscribed_timestamp_utc = gmdate("Y-m-d H:i:s");

        // Reactivate subscriber in database.
        $resubscribe_query = "UPDATE main_newsletter
                              SET status = $1,
                              number_times_resubscribed = number_times_resubscribed + 1,
                              resubscribed_timestamp_utc = $2
                              WHERE email = $3";
        $resubscribe_result = pg_query_params(
          $db_cxn,
          $resubscribe_query,
          array(
            $status,
            $resubscribed_timestamp_utc,
            $email
          )
        );

        // If error, throw exception.
        if ($resubscribe_result == FALSE) {
          $internal_status_code = 910;
          throw new Exception("Error reactivating subscriber in database for " . $email);
        }

        // Close DB connection.
        pg_close($db_cxn);

        // Push email to Beehiiv. This is the only field we can send at this time.
        $beehiiv_result = beehiiv_subscribe($email);
        if ($beehiiv_result["status"] != "success") {
          $internal_status_code = 908;
          throw new Exception("Error pushing to Beehiiv for " . $email);
        }

        // Return results. Don't give any referral credit in this case.
        $subscribe_result = array(
          "status" => "success",
          "http_status_code" => 200,
          "internal_status_code" => 1001,
          "info" => "Successfully reactivated subscriber",
          "email" => $email,
          "unique_id" => $unique_id,
          "referral_id" => $referral_id,
          "check_referral_id" => $check_referral_id
        );
        return $subscribe_result;

      }

    } else {

      // Throw error for any irregular response.
      $internal_status_code = 911;
      throw new Exception("Irregular result returned from ID check for " . $email);

    }

  } catch (Exception $e) {

    // Send response for error during signup.
    server_alert($e->getMessage());
    $subscribe_result = array(
      "status" => "failure",
      "http_status_code" => 500,
      "internal_status_code" => 999,
      "info" => $e->getMessage(),
      "email" => $email,
      "unique_id" => NULL,
      "referral_id" => NULL,
      "check_referral_id" => NULL
    );
    return $subscribe_result;

  }

}
