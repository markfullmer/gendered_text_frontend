<h2>Test</h2>

<?php
/**
 * @file
 * Allow users to test prepared texts with a given legend.
 */

use GuzzleHttp\Client;

// Some default text.
$text = 'One midday when, after an absence of two hours, {{ Arabella }} came into the room, {{ she(Arabella) }} beheld the chair empty. Down {{ she(Arabella) }} flopped on the bed, and sitting, meditated. "Now where the devil is my {{ man(Jude) }} gone to!" {{ she(Arabella) }} said.

[ [Arabella/Arthur/Aspen:male] [Julie/Jude/Juen:female] ]';

if (isset($_POST['text'])) {
  $text = $_POST['text'];
}

echo '
<div class="container test">
  <form action="' . $base_url . 'test" method="POST">
    <div class="row">
      <div class="six columns">
        <label for="text">Text to be genderized. See <a href="/instructions">instructions.</a></label>
        <textarea class="u-full-width textbox" placeholder="Place words here..." name="text">' . $text . '</textarea>
      </div>
      <div class="six columns"><input type="submit" name="json" value="Genderize" />';
if (isset($_POST['text'])) {
  $client = new Client();
  $res = $client->request('POST', $api, [
    'query' => [
      'test' => $_POST['text'],
    ],
  ]);
  if ($res->getStatusCode() == '200') {
    $test = ($res->getBody());
  }
  echo '<p>' . $test . '</p>';
  echo '<div class="panel"><p>Changing the default gender value in the legend (the value after the colon) will dynamically change the gender in the text.';
}
echo '
      </div>
    </div>
  </form>
</div>';
