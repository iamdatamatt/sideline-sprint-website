<?php

// Load Sprint functions.
require_once "/var/www/ghost/system/secure/sprint-tools.php";

// Tell Google to get lost.
header("X-Robots-Tag: noindex", true);

// Wrap just in case.
try {

  // Get URL of latest post.
  $curl = curl_init();
  curl_setopt_array($curl, array(
      CURLOPT_URL => "-",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 5,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
  ));
  $response = curl_exec($curl);
  $err = curl_error($curl);
  curl_close($curl);

  // If error, send to generic link; otherwise, send to post.
  if ($err) {
      header("Location: https://www.sidelinesprint.com/home/", true, 303);
      exit();
  } else {
      $post_data = json_decode($response, true);
      if (array_key_exists("posts", $post_data)) {
          if (array_key_exists(0, $post_data["posts"])) {
              if (array_key_exists("url", $post_data["posts"][0])) {
                  $latest_post_url = $post_data["posts"][0]["url"];
                  $latest_post_url = $latest_post_url . "?ref=read_it_first";
                  $redir_url = query_string_passthrough($latest_post_url, $_GET);
                  header("Location: {$redir_url}");
                  exit();
              }
          }
      }

      // Fallback redirect in case keys don't exist.
      header("Location: https://www.sidelinesprint.com/home/", true, 303);
      exit();

  }

} catch (Exception $e) {

  // Fallback redirect in case of error.
  server_alert($e->getMessage());
  header("Location: https://www.sidelinesprint.com/home/", true, 303);
  exit();

}

?>
