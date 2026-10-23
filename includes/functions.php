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

// Check if YouTube channel is live (with caching)
function is_youtube_live() {
    $settings = get_settings();
    $api_key = $settings['youtube_api_key'] ?? '';
    $channel_id = $settings['youtube_channel_id'] ?? '';

    // If no API key or channel ID configured, return false
    if (empty($api_key) || empty($channel_id)) {
        return false;
    }

    // Check cache first (cache for 60 seconds)
    $cache_file = __DIR__ . '/../data/youtube_live_cache.json';
    if (file_exists($cache_file)) {
        $cache = json_decode(file_get_contents($cache_file), true);
        if ($cache && isset($cache['timestamp']) && (time() - $cache['timestamp']) < 60) {
            return $cache['is_live'] ?? false;
        }
    }

    // Query YouTube Data API
    $url = 'https://www.googleapis.com/youtube/v3/search?' . http_build_query([
        'part' => 'snippet',
        'channelId' => $channel_id,
        'eventType' => 'live',
        'type' => 'video',
        'key' => $api_key
    ]);

    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'ignore_errors' => true
        ]
    ]);

    $response = @file_get_contents($url, false, $context);
    $is_live = false;

    if ($response) {
        $data = json_decode($response, true);
        if (isset($data['items']) && count($data['items']) > 0) {
            $is_live = true;
        }
    }

    // Cache the result
    file_put_contents($cache_file, json_encode([
        'is_live' => $is_live,
        'timestamp' => time()
    ]));

    return $is_live;
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

// ============================================
// CALENDAR FUNCTIONS
// ============================================

// Get all locations
function get_locations() {
    return load_data('locations');
}

// Get location by ID
function get_location($id) {
    $locations = get_locations();
    foreach ($locations as $loc) {
        if ($loc['id'] === $id) return $loc;
    }
    return null;
}

// Get recurring services
function get_recurring_services() {
    $data = load_data('recurring_services');
    usort($data, fn($a, $b) => ($a['order'] ?? 99) - ($b['order'] ?? 99));
    return $data;
}

// Get active recurring services
function get_active_recurring_services() {
    $services = get_recurring_services();
    return array_filter($services, fn($s) => $s['active'] ?? true);
}

// Get calendar events (stored modifications and special events)
function get_calendar_events() {
    return load_data('calendar_events');
}

// Save calendar events
function save_calendar_events($events) {
    return save_data('calendar_events', $events);
}

// Generate events for a date range from recurring templates
function generate_calendar_for_range($start_date, $end_date) {
    $recurring = get_active_recurring_services();
    $stored_events = get_calendar_events();
    $events = [];

    // Index stored events by date and recurring_id for quick lookup
    $stored_index = [];
    foreach ($stored_events as $event) {
        $key = $event['date'] . '-' . ($event['recurring_id'] ?? 'special');
        $stored_index[$key] = $event;
    }

    // Generate recurring events for each day in range
    $current = strtotime($start_date);
    $end = strtotime($end_date);

    while ($current <= $end) {
        $date = date('Y-m-d', $current);
        $day_of_week = (int)date('w', $current);

        foreach ($recurring as $service) {
            if ($service['day_of_week'] === $day_of_week) {
                $key = $date . '-' . $service['id'];

                // Check if there's a stored modification/cancellation
                if (isset($stored_index[$key])) {
                    $stored = $stored_index[$key];
                    if (!($stored['is_cancelled'] ?? false)) {
                        $events[] = $stored;
                    }
                } else {
                    // Generate from template
                    $events[] = [
                        'id' => $date . '-' . $service['id'],
                        'date' => $date,
                        'time' => $service['time'],
                        'name' => $service['name'],
                        'location_id' => $service['location_id'],
                        'description' => $service['description'],
                        'recurring_id' => $service['id'],
                        'type' => $service['type'] ?? 'service',
                        'is_modified' => false,
                        'is_cancelled' => false
                    ];
                }
            }
        }

        $current = strtotime('+1 day', $current);
    }

    // Add special (non-recurring) events in the range
    foreach ($stored_events as $event) {
        if (empty($event['recurring_id']) && ($event['is_special'] ?? false)) {
            $event_date = strtotime($event['date']);
            if ($event_date >= strtotime($start_date) && $event_date <= strtotime($end_date)) {
                if (!($event['is_cancelled'] ?? false)) {
                    $events[] = $event;
                }
            }
        }
    }

    // Sort by date and time
    usort($events, function($a, $b) {
        $date_cmp = strcmp($a['date'], $b['date']);
        if ($date_cmp !== 0) return $date_cmp;
        return strcmp($a['time'], $b['time']);
    });

    return $events;
}

// Get events for a specific month
function get_calendar_month($year, $month) {
    $start = sprintf('%04d-%02d-01', $year, $month);
    $end = date('Y-m-t', strtotime($start));
    return generate_calendar_for_range($start, $end);
}

// Get events for a specific date
function get_events_for_date($date) {
    return generate_calendar_for_range($date, $date);
}

// Get upcoming events (for homepage)
function get_upcoming_calendar_events($limit = 6) {
    $today = date('Y-m-d');
    $end = date('Y-m-d', strtotime('+12 months'));
    $events = generate_calendar_for_range($today, $end);

    // Filter to only future events (from today onwards)
    $future_events = array_filter($events, function($e) use ($today) {
        return $e['date'] >= $today;
    });

    return array_slice(array_values($future_events), 0, $limit);
}

// Get upcoming special events (for homepage - excludes recurring services)
function get_upcoming_special_events($limit = 3) {
    $today = date('Y-m-d');
    $stored_events = get_calendar_events();

    // Filter for special events that are in the future and not cancelled
    $special = array_filter($stored_events, function($event) use ($today) {
        return ($event['is_special'] ?? false)
            && $event['date'] >= $today
            && !($event['is_cancelled'] ?? false);
    });

    // Sort by date and time
    usort($special, function($a, $b) {
        $date_cmp = strcmp($a['date'], $b['date']);
        if ($date_cmp !== 0) return $date_cmp;
        return strcmp($a['time'], $b['time']);
    });

    return array_slice($special, 0, $limit);
}

// Save a single calendar event (modification or special)
function save_calendar_event($event) {
    $events = get_calendar_events();

    // Find existing event by ID
    $found = false;
    foreach ($events as $i => $e) {
        if ($e['id'] === $event['id']) {
            $events[$i] = $event;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $events[] = $event;
    }

    return save_calendar_events($events);
}

// Delete a calendar event by ID
function delete_calendar_event($id) {
    $events = get_calendar_events();
    $events = array_filter($events, fn($e) => $e['id'] !== $id);
    return save_calendar_events(array_values($events));
}

// Cancel a recurring event instance
function cancel_event_instance($date, $recurring_id) {
    $event = [
        'id' => $date . '-' . $recurring_id,
        'date' => $date,
        'recurring_id' => $recurring_id,
        'is_cancelled' => true
    ];
    return save_calendar_event($event);
}

// Format time for display (24h to 12h)
function format_time($time) {
    return date('g:ia', strtotime($time));
}

// Get day name from number
function get_day_name($day_num) {
    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    return $days[$day_num] ?? '';
}

// Truncate text to a maximum number of characters
function truncate_text($text, $max_chars = 150) {
    $text = trim($text ?? '');
    if (strlen($text) <= $max_chars) {
        return $text;
    }
    $truncated = substr($text, 0, $max_chars);
    // Cut at last word boundary
    $last_space = strrpos($truncated, ' ');
    if ($last_space !== false) {
        $truncated = substr($truncated, 0, $last_space);
    }
    return $truncated . '...';
}

// Convert image to WebP format
function convert_to_webp($source_path, $quality = 80) {
    if (!function_exists('imagewebp')) {
        return false; // GD WebP support not available
    }

    $ext = strtolower(pathinfo($source_path, PATHINFO_EXTENSION));
    $webp_path = preg_replace('/\.(jpg|jpeg|png|gif)$/i', '.webp', $source_path);

    // Create image resource based on type
    switch ($ext) {
        case 'jpg':
        case 'jpeg':
            $image = @imagecreatefromjpeg($source_path);
            break;
        case 'png':
            $image = @imagecreatefrompng($source_path);
            // Preserve transparency
            if ($image) {
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
            }
            break;
        case 'gif':
            $image = @imagecreatefromgif($source_path);
            break;
        default:
            return false;
    }

    if (!$image) {
        return false;
    }

    // Convert to WebP
    $result = imagewebp($image, $webp_path, $quality);
    unset($image); // imagedestroy() is a no-op since PHP 8.0 and deprecated in 8.5

    return $result ? $webp_path : false;
}

// Get WebP path for an image (returns original if WebP doesn't exist)
function get_webp_path($original_path) {
    $webp_path = preg_replace('/\.(jpg|jpeg|png|gif)$/i', '.webp', $original_path);
    $full_webp_path = $_SERVER['DOCUMENT_ROOT'] . $webp_path;

    if (file_exists($full_webp_path)) {
        return $webp_path;
    }
    return null;
}

// Output picture element with WebP support
function picture_element($src, $alt = '', $class = '', $loading = 'lazy') {
    $webp_src = get_webp_path($src);
    $loading_attr = $loading ? ' loading="' . e($loading) . '"' : '';

    if ($webp_src) {
        return '<picture>
            <source srcset="' . e($webp_src) . '" type="image/webp">
            <img src="' . e($src) . '" alt="' . e($alt) . '" class="' . e($class) . '"' . $loading_attr . '>
        </picture>';
    }

    return '<img src="' . e($src) . '" alt="' . e($alt) . '" class="' . e($class) . '"' . $loading_attr . '>';
}
