<?php

/**
 * @file
 * Administrative dashboard for site.
 *
 * Allows clearing of file cache and setting which texts may be displayed.
 */

use GuzzleHttp\Client;

if (isset($_POST['logout'])) {
  unset($_SESSION['auth']);
}

if (isset($_POST['login'])) {
  if (isset($_POST['uname']) && isset($_POST['psw'])) {
    include 'credentials.inc';
    if ($_POST['uname'] == $username && $_POST['psw'] == $password) {
      $_SESSION['auth'] = md5('helloworld');
    }
    else {
      echo '<div class="panel">Incorrect credentials</div>';
    }
  }
}

if (empty($_SESSION['auth'])) {
  echo '<form action="' . $base_url . 'dashboard" method="POST">';
  echo '
    <label><b>Username</b></label>
    <input type="text" placeholder="Enter Username" name="uname" required>
    <label><b>Password</b></label>
    <input type="password" placeholder="Enter Password" name="psw" required><br />
    <button name="login" type="submit">Sign in</button>
  </form>';
  die();
}
elseif ($_SESSION['auth'] != md5('helloworld')) {
  die();
}

if (isset($_POST['allowed'])) {
  $file = fopen("allowed.json", "w");
  fwrite($file, json_encode($_POST['allowed'], JSON_PRETTY_PRINT));
  fclose($file);
  echo '<div class="panel success">Allowed texts updated.</div>';
}

if (isset($_POST['clearcache'])) {
  array_map('unlink', glob('cache/text/*.txt'));
  array_map('unlink', glob('cache/legends/*.txt'));
  unlink('cache/texts.json');
  echo '<div class="panel success">Site cache cleared.</div>';
}

$texts = [];
if (!file_exists('cache/texts.json')) {
  $client = new Client();
  $res = $client->request('GET', $api . '?get_texts=1');
  if ($res->getStatusCode() == '200') {
    $file = fopen("cache/texts.json", "w");
    fwrite($file, $res->getBody());
    fclose($file);
  }
}
$texts = (array) json_decode(file_get_contents('cache/texts.json'));
$allowed = (array) json_decode(file_get_contents('allowed.json'));

if ($texts) {
  foreach ($texts as $id => $values) {
    $checked = '';
    if (in_array($id, $allowed)) {
      $checked = 'checked="checked"';
    }
    $choices[] = '<input type="checkbox" name="allowed[]" value="' . $id . '" ' . $checked . '" /> ' . $values->title . '<br />';
  }
}

if ($choices) {
  echo '<h3>Set texts to display on homepage</h3>';
  echo '<form action="' . $base_url . 'dashboard" method="POST">';
  foreach ($choices as $choice) {
    echo $choice;
  }
  echo '<input type="submit" value="Update allowed texts">';
  echo '</form>';
}

// Clear the site cache.
echo '<hr />';
echo '<form action="' . $base_url . 'dashboard" method="POST">';
echo '<input type="submit" name="clearcache" value="Clear site cache">';
echo '</form>';


// Log out of dashboard.
echo '<hr />';
echo '<form action="' . $base_url . 'dashboard" method="POST">';
echo '<input type="submit" name="logout" value="Sign out">';
echo '</form>';
