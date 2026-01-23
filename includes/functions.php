<?php
/**
 * All Saints of Lincolnshire - Helper Functions
 */

// Load JSON data file
function load_data($filename) {
    $path = __DIR__ . '/../data/' . $filename . '.json';
    if (file_exists($path)) {
        $json = file_get_contents($path);
        return json_decode($json, true) ?: [];
    }
    return [];
}

// Save JSON data file
function save_data($filename, $data) {
    $path = __DIR__ . '/../data/' . $filename . '.json';
    return file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Get settings
function get_settings() {
    return load_data('settings');
}

// Get services
function get_services() {
    $data = load_data('services');
    usort($data, fn($a, $b) => ($a['order'] ?? 99) - ($b['order'] ?? 99));
    return $data;
}

// Get upcoming events
function get_upcoming_events($limit = 3) {
    $events = load_data('events');
    $now = time();

    // Filter future events
    $upcoming = array_filter($events, fn($e) => strtotime($e['date']) >= $now);

    // Sort by date
    usort($upcoming, fn($a, $b) => strtotime($a['date']) - strtotime($b['date']));

    return array_slice($upcoming, 0, $limit);
}

// Get all events
function get_all_events() {
    $events = load_data('events');
    usort($events, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));
    return $events;
}

// Get recent news
function get_recent_news($limit = 4) {
    $news = load_data('news');
    usort($news, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));
    return array_slice($news, 0, $limit);
}

// Get all news
function get_all_news() {
    $news = load_data('news');
    usort($news, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));
    return $news;
}

// Get clergy
function get_clergy() {
    $clergy = load_data('clergy');
    $active = array_filter($clergy, fn($c) => ($c['active'] ?? true));
    usort($active, fn($a, $b) => ($a['order'] ?? 99) - ($b['order'] ?? 99));
    return $active;
}

// Format date
function format_date($date, $format = 'j F Y') {
    return date($format, strtotime($date));
}

// Generate slug from title
function generate_slug($title) {
    $slug = strtolower($title);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

// Escape HTML
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
