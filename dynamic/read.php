<?php

use GuzzleHttp\Client;

if (isset($_POST['characters'])) {
  $texts = (array) json_decode(file_get_contents('cache/texts.json'));
  $id = $_POST['text'];
  $title = urlencode($texts[$id]);

  $hash = md5($_POST['text'] . serialize($_POST['characters']));
  $export = '<a class="button button-primary" href="' . $base_url . '/dynamic/export.php?export=' . $hash . '&title=' . $title . '">Export to eBook</a>';
  $cache_file = 'cache/text/' . $hash . '.txt';
  if (file_exists($cache_file)) {
    $text = file_get_contents($cache_file);
  }
  else {
    $client = new Client();
    $res = $client->request('POST', $api, [
      'query' => [
        'text' => $_POST['text'],
        'characters' => $_POST['characters'],
      ],
    ]);
    if ($res->getStatusCode() == '200') {
      $text = ($res->getBody());
      $file = fopen($cache_file, 'w');
      fwrite($file, $text);
      fclose($file);
    }
  }
  echo $export;
  echo $text;
}
