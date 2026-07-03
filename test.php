<?php
echo "Start<br>";

require_once 'config.php';

echo "Config loaded<br>";

echo csrf_token();

echo "<br>Done";