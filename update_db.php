<?php
include('includes/config.php');

// Add visibility column if it doesn't exist
$result = @$conn->query('ALTER TABLE noticeboard ADD COLUMN visibility VARCHAR(20) DEFAULT "all"');
echo "Database update completed.\n";
echo "You can delete this file after confirming.\n";
?>
