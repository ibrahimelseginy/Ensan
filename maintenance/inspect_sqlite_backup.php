<?php
try {
    $db = new PDO('sqlite:database/database.sqlite.backup');
    // Get all tables
    $res = $db->query("SELECT name FROM sqlite_master WHERE type='table';");
    $tables = [];
    foreach ($res as $row) {
        $tables[] = $row['name'];
    }

    echo "Tables in SQLite Database Backup:\n";
    foreach ($tables as $table) {
        $count = $db->query("SELECT COUNT(*) FROM \"$table\"")->fetchColumn();
        if ($count > 0) {
            echo "- $table ($count rows)\n";
        }
    }
}
catch (Exception $e) {
    echo $e->getMessage();
}
