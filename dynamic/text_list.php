<?php

use GuzzleHttp\Client;

$texts = [];
$api = 'http://gendered-api.local/api';
$cached = FALSE;

if (file_exists('cache/texts.json')) {
  $modified = filemtime('cache/texts.json');
  // If right now is less than 30 minutes past the file creation, consider the
  // cache valid.
  if (time() - 60 * 30 < $modified) {
    $cached = TRUE;
  }
}
if (!$cached) {
  $client = new Client();
  $res = $client->request('GET', $api . '?get_texts=1');
  if ($res->getStatusCode() == '200') {
    $file = fopen("cache/texts.json", "w");
    fwrite($file, $res->getBody());
    fclose($file);
  }
}
$texts = (array) json_decode(file_get_contents('cache/texts.json'));

if ($texts) {
  foreach ($texts as $id => $title) {
    $links[] = '<a href="/selection/' . $id . '">' . $title . '</a>';
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
