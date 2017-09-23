<?php

/**
 * @file
 * Main "engine" file. Should not need to be customized.
 */

session_start();
require 'vendor/autoload.php';
$base_url = 'http://' . $_SERVER['SERVER_NAME'] . '/';
$api = 'https://gendered-text.markfullmer.com/api';

// Contains the leading HTML head text, mostly used for search engines.
include 'templates/header.php';
// Contains the navigation bar.
include 'templates/navigation.php';
// Contains the main page text.
?>
<article>
  <div class="container">
    <div class="row">
      <div class="twelve columns">
<?php
$pages = ['about', 'participate'];
$found = FALSE;
$dynamic = ['selection', 'read', 'export', 'dashboard'];
if (isset($_GET['page']) && in_array($_GET['page'], $pages)) {
  $body = file_get_contents('templates/' . $_GET['page'] . '.html');
  echo $body;
  $found = TRUE;
}
else {
  foreach ($dynamic as $page) {
    if (isset($_GET[$page])) {
      include 'dynamic/' . $page . '.php';
      $found = TRUE;
      break;
    }
  }
}
if (!$found) {
  include 'dynamic/text_list.php';
}

?>
      </div>
    </div>
  </div>
</article>

<?php
// Contains the footer text.
include 'templates/footer.php';
