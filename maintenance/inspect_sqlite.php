<?php
try {
    $db = new PDO('sqlite:database/database.sqlite');
    // Get all tables
    $res = $db->query("SELECT name FROM sqlite_master WHERE type='table';");
    $tables = [];
    foreach ($res as $row) {
        $tables[] = $row['name'];
    }

    echo "Tables in SQLite Database:\n";
    foreach ($tables as $table) {
        echo "- $table\n";
        $count = $db->query("SELECT COUNT(*) FROM \"$table\"")->fetchColumn();
        echo "  Rows: $count\n";
    }
}
catch (Exception $e) {
    echo $e->getMessage();
}
