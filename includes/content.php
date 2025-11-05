<?php

declare(strict_types=1);

const SITE_CONTENT_PATH = __DIR__ . '/../data/site-content.json';

/**
 * Load the editable site content from disk.
 */
function load_site_content(): array
{
    if (!file_exists(SITE_CONTENT_PATH)) {
        return [];
    }

    $contents = file_get_contents(SITE_CONTENT_PATH);

    if ($contents === false) {
        return [];
    }

    $data = json_decode($contents, true);

    return is_array($data) ? $data : [];
}

/**
 * Persist the updated site content to disk.
 */
function save_site_content(array $content): bool
{
    $encoded = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    if ($encoded === false) {
        return false;
    }

    return file_put_contents(SITE_CONTENT_PATH, $encoded . PHP_EOL) !== false;
}
