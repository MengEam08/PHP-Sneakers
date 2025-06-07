<?php
session_start();
session_destroy();
header("Location: /PHP-Sneakers/index.php?p=login");
exit();
