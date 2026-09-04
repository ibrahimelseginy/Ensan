<?php
// Generate a hash using Laravel's default bcrypt settings
$hash = password_hash('password', PASSWORD_BCRYPT);
echo $hash;
