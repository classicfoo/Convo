<?php
require_once __DIR__ . '/layout.php';

logout_user();
header('Location: /login.php');
exit;
