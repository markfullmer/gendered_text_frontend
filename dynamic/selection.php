<?php

use GuzzleHttp\Client;

$legend = [];
$genders = ['male', 'female', 'non-binary'];
$choices = '';

if (isset($_GET['selection'])) {
  $selection = $_GET['selection'];
  $cache_file = 'cache/legends/' . $selection . '.txt';
}
if (!file_exists($cache_file)) {
  $client = new Client();
  $res = $client->request('GET', $api . '?text=' . $selection);
  if ($res->getStatusCode() == '200') {
    $file = fopen($cache_file, 'w');
    fwrite($file, $res->getBody());
    fclose($file);
  }
}
$legend = (array) json_decode(file_get_contents($cache_file, 'w'));

if (!empty($legend)) {
  foreach ($legend as $names) {
    $choices .= '<label for="characters[' . $names . ']">Gender for "' . $names . '": ';
    $choices .= '<select id="characters[' . $names . ']" name="characters[' . $names . ']">';
    foreach ($genders as $gender) {
      $choices .= '<option value="' . $gender . '">' . ucfirst($gender) . '</option>';
    }
    $choices .= '</select>';
  }

  echo '<h3>Choose Genders</h3>';
  echo '<div class="row"><div class="two-thirds column">';
  echo '<form action="' . $base_url . 'read" method="POST">';
  echo '<input type="hidden" name="text" value="' . $selection . '" />';
  echo '<span class="button" id="randomize" onclick="randomize()">Randomize</span>';
  echo $choices;
  echo '</div><div class="row"><div class="one-third column">';
  echo '<br /><input class="button button-primary" type="submit" name="submit" value="Read the text" /></div>';
  echo '</form>';
}
