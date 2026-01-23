<?php

function compact_files($dir)
{
    if (!is_dir($dir))
        return;

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($files as $file) {
        if ($file->isDir())
            continue;

        $pathname = $file->getPathname();

        // Skip ignored
        if (
            strpos($pathname, 'vendor') !== false ||
            strpos($pathname, '.git') !== false ||
            strpos($pathname, '.gemini') !== false
        ) {
            continue;
        }

        if ($pathname === __FILE__)
            continue;

        $ext = $file->getExtension();
        if (!in_array($ext, ['php', 'js', 'css', 'sql', 'html']))
            continue;

        $content = file_get_contents($pathname);
        if ($content === false)
            continue;

        // Strategy: Split, Trim Right, Filter Empty, Join
        $lines = explode("\n", $content);
        $newLines = [];

        foreach ($lines as $line) {
            // Trim right side (whitespace)
            $trimmed = rtrim($line);

            // Check if line is empty (contains only whitespace)
            if (trim($trimmed) !== '') {
                $newLines[] = $trimmed;
            }
        }

        // Normalize PHP tag spacing lightly if needed?
        // No, keep it strictly compact logic.

        $newContent = implode("\n", $newLines);

        if ($newContent !== $content) {
            echo "Compacting: $pathname\n";
            file_put_contents($pathname, $newContent);
        }
    }
}

echo "Starting aggressive code compaction...\n";
compact_files(__DIR__);
echo "Done.\n";
