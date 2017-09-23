<?php

use GuzzleHttp\Client;

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
  foreach ($texts as $id => $title) {
    if (in_array($id, $allowed)) {
      $links[] = '<a href="/selection/' . $id . '">' . $title . '</a>';
    }
  }
}

if ($links) {
  echo '<h3>Available Texts</h3>';
  echo '<table>';
  echo '<thead><th>Title</th></thead>';
  foreach ($links as $link) {
    echo '<tr><td>' . $link . '</td></tr>';
  }
  echo '</table>';
}
