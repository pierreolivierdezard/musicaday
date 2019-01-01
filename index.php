<?php
require_once('vendor/autoload.php');

$client = new Google_Client();
$client->setAuthConfigFile('client_secret.json');
$client->setRedirectUri('http://localhost/musicaday/index.php');
$client->setScopes(Google_Service_YouTube::YOUTUBE_READONLY);

$youtube = new Google_Service_YouTube($client);

session_start();

if (isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  $_SESSION['token'] = $client->getAccessToken();
  header('Location: http://localhost/musicaday/index.php');
  die();
}

if (isset($_SESSION['token'])) {
  $client->setAccessToken($_SESSION['token']);
}

if ($client->getAccessToken()) {
  $musicAdayVideos = $youtube->playlistItems->listPlaylistItems(
    'snippet',
    array('playlistId' => 'PLRkM4T9LqmBBZ3BxkS9Z5yrI8G60LFfA6')
  )->items;

  $publishDate = new DateTime('19-01-01');
  $today = new DateTime('now');
  $videoIndex = $publishDate->diff($today)->d;

  $videoId = $musicAdayVideos[$videoIndex]->snippet->resourceId->videoId;

} else {
  ?>
  <h2>Accès Interdit</h2>
  <p>Vous devez <a href="<?= $client->createAuthUrl(); ?>">autoriser</a> l'application à accéder à votre compte youtube</p>
  <?php
}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Music A Day</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Gloria+Hallelujah" rel="stylesheet">
    <script type="text/javascript" src="vendor\twbs\bootstrap\dist\js\bootstrap.min.js"></script>
  </head>
  <body>
    <div class="container-fluid">
      <div class="text-header">
        <h2 class="title">Music A Day</h2>
        <h3 class="subtitle">sounds and images i like</h3>
      </div>
      <div class="frame-video">
          <iframe class="frame-white" src=<?="https://www.youtube.com/embed/$videoId"?> frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
      </div>
    </div>
  </body>
</html>
