<html>
  <head></head>
  <body>
    <h2>My Bookmarks</h2>
    <?php
    // include class file
    include_once 'Services/Delicious.php';
    
    // initialize object
    $sdObj = new Services_Delicious('oanure', 'del1c10us');
    
    // get recent posts from del.icio.us
    // print as bulleted list
    $posts = $sdObj->getRecentPosts();
    echo "<ul>\n";
    foreach ($posts as $p) {
      echo "  <li>\n";
      echo "    <a href=\"" . $p['href'] . "\">" . $p['description'] . "</a><br />\n";
      echo "    <span style=\"color:red\">" . implode(' ', $p['tag']) . "</span><br />\n";
	  echo "  </li>\n";
    }
    echo "</ul>\n";
    ?>
  </body>
</html>
