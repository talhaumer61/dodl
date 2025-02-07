<?php
function deleteDirectory($dir) {
    if (!is_dir($dir)) {
        return false;
    }
    
    $files = array_diff(scandir($dir), array('.', '..'));

    foreach ($files as $file) {
        $path = $dir . DIRECTORY_SEPARATOR . $file;

        if (is_dir($path)) {
            // Recursive call to delete subdirectories and files inside
            deleteDirectory($path);
        } else {
            // Delete the file
            unlink($path);
        }
    }

    // Remove the directory itself
    return rmdir($dir);
}

// REPLACE DEL_ME WITH ACTUAL DIRECTORY
$directoryToDelete = './del_me';

if (deleteDirectory($directoryToDelete)) {
    echo "Directory deleted successfully.";
} else {
    echo "Directory Not Found.";
}

?>