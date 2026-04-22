<?php
/**
 * Plugin Name: Matrezhka City Importer (MU)
 * Description: CSV import tool for "city" taxonomy terms.
 * Author: Matrezhka Team
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register the city import page in admin tools.
 */
function matrezhka_register_city_import_page()
{
    add_management_page(
        'Импорт городов',
        'Импорт городов',
        'manage_categories',
        'matrezhka-city-import',
        'matrezhka_render_city_import_page'
    );
}
add_action('admin_menu', 'matrezhka_register_city_import_page');

/**
 * Add a quick import button on city taxonomy screen.
 */
function matrezhka_city_import_quick_button()
{
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || 'edit-city' !== $screen->id) {
        return;
    }

    $import_url = admin_url('tools.php?page=matrezhka-city-import');
    echo '<div class="notice notice-info is-dismissible"><p>';
    echo 'Нужно загрузить города из CSV? ';
    echo '<a class="button button-primary" href="' . esc_url($import_url) . '">Открыть импорт</a>';
    echo '</p></div>';
}
add_action('admin_notices', 'matrezhka_city_import_quick_button');

/**
 * Detect CSV delimiter by first line.
 *
 * @param string $line CSV first line.
 *
 * @return string
 */
function matrezhka_detect_csv_delimiter($line)
{
    $delimiters = array(',', ';', "\t", '|');
    $best = ',';
    $max_parts = 0;

    foreach ($delimiters as $delimiter) {
        $parts = str_getcsv($line, $delimiter);
        $count = is_array($parts) ? count($parts) : 0;
        if ($count > $max_parts) {
            $max_parts = $count;
            $best = $delimiter;
        }
    }

    return $best;
}

/**
 * Parse and import cities from uploaded CSV file.
 *
 * @param string $tmp_path Uploaded file temp path.
 *
 * @return array<string, mixed>
 */
function matrezhka_import_cities_from_csv($tmp_path)
{
    $result = array(
        'created' => 0,
        'skipped' => 0,
        'errors' => 0,
        'messages' => array(),
    );

    $handle = fopen($tmp_path, 'r');
    if (false === $handle) {
        $result['errors']++;
        $result['messages'][] = 'Не удалось открыть CSV-файл.';
        return $result;
    }

    $first_line = fgets($handle);
    if (false === $first_line) {
        fclose($handle);
        $result['messages'][] = 'CSV-файл пустой.';
        return $result;
    }

    $delimiter = matrezhka_detect_csv_delimiter($first_line);
    rewind($handle);

    $row_index = 0;
    while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
        $row_index++;

        if (!is_array($row)) {
            continue;
        }

        $row = array_map(
            static function ($value) {
                $value = (string) $value;
                $value = preg_replace('/^\xEF\xBB\xBF/u', '', $value);
                return trim($value);
            },
            $row
        );

        if (0 === count(array_filter($row, static function ($value) {
            return '' !== $value;
        }))) {
            continue;
        }

        if (function_exists('mb_strtolower')) {
            $col1 = isset($row[0]) ? mb_strtolower($row[0]) : '';
            $col2 = isset($row[1]) ? mb_strtolower($row[1]) : '';
        } else {
            $col1 = isset($row[0]) ? strtolower($row[0]) : '';
            $col2 = isset($row[1]) ? strtolower($row[1]) : '';
        }
        $is_header = in_array($col1, array('city', 'name', 'город', 'название'), true)
            || in_array($col2, array('slug', 'слаг', 'код'), true);
        if (1 === $row_index && $is_header) {
            continue;
        }

        $name = isset($row[0]) ? trim((string) $row[0]) : '';
        $slug = isset($row[1]) ? sanitize_title((string) $row[1]) : '';

        if ('' === $name) {
            $result['skipped']++;
            continue;
        }

        $existing_by_name = get_term_by('name', $name, 'city');
        $existing_by_slug = '' !== $slug ? get_term_by('slug', $slug, 'city') : false;

        if (($existing_by_name && !is_wp_error($existing_by_name)) || ($existing_by_slug && !is_wp_error($existing_by_slug))) {
            $result['skipped']++;
            continue;
        }

        $args = array();
        if ('' !== $slug) {
            $args['slug'] = $slug;
        }

        $insert = wp_insert_term($name, 'city', $args);
        if (is_wp_error($insert)) {
            $result['errors']++;
            $result['messages'][] = sprintf('Ошибка в строке %d: %s', (int) $row_index, $insert->get_error_message());
            continue;
        }

        $result['created']++;
    }

    fclose($handle);
    return $result;
}

/**
 * Render city import admin page.
 */
function matrezhka_render_city_import_page()
{
    if (!current_user_can('manage_categories')) {
        wp_die('Недостаточно прав.');
    }

    if (!taxonomy_exists('city')) {
        echo '<div class="wrap"><h1>Импорт городов</h1>';
        echo '<div class="notice notice-error"><p>Таксономия <code>city</code> не найдена.</p></div></div>';
        return;
    }

    $report = null;
    $request_method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
    if ('POST' === $request_method) {
        check_admin_referer('matrezhka_city_import_csv', 'matrezhka_city_import_nonce');

        if (!isset($_FILES['cities_csv']) || !is_array($_FILES['cities_csv'])) {
            $report = array(
                'created' => 0,
                'skipped' => 0,
                'errors' => 1,
                'messages' => array('Файл не загружен.'),
            );
        } else {
            $file = $_FILES['cities_csv'];
            $error_code = isset($file['error']) ? (int) $file['error'] : UPLOAD_ERR_NO_FILE;
            if (UPLOAD_ERR_OK !== $error_code) {
                $report = array(
                    'created' => 0,
                    'skipped' => 0,
                    'errors' => 1,
                    'messages' => array('Ошибка загрузки файла. Код: ' . $error_code),
                );
            } else {
                $tmp_name = isset($file['tmp_name']) ? (string) $file['tmp_name'] : '';
                $filename = isset($file['name']) ? (string) $file['name'] : '';
                $ext = strtolower((string) pathinfo($filename, PATHINFO_EXTENSION));
                if (!in_array($ext, array('csv', 'txt'), true)) {
                    $report = array(
                        'created' => 0,
                        'skipped' => 0,
                        'errors' => 1,
                        'messages' => array('Поддерживаются только файлы CSV/TXT.'),
                    );
                } else {
                    $report = matrezhka_import_cities_from_csv($tmp_name);
                }
            }
        }
    }

    ?>
    <div class="wrap">
        <h1>Импорт городов (CSV)</h1>
        <p>Загружайте файл с колонками <code>name,slug</code> или с одной колонкой <code>city</code>.</p>
        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('matrezhka_city_import_csv', 'matrezhka_city_import_nonce'); ?>
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row"><label for="cities_csv">CSV-файл городов</label></th>
                        <td>
                            <input type="file" id="cities_csv" name="cities_csv" accept=".csv,.txt" required>
                            <p class="description">Пример: <code>city</code> в первой колонке, как в вашем <code>cities.csv</code>.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php submit_button('Загрузить города'); ?>
        </form>

        <?php if (is_array($report)) : ?>
            <hr>
            <h2>Результат импорта</h2>
            <ul>
                <li>Создано: <strong><?php echo (int) $report['created']; ?></strong></li>
                <li>Пропущено: <strong><?php echo (int) $report['skipped']; ?></strong></li>
                <li>Ошибок: <strong><?php echo (int) $report['errors']; ?></strong></li>
            </ul>
            <?php if (!empty($report['messages']) && is_array($report['messages'])) : ?>
                <div class="notice notice-warning">
                    <p><strong>Сообщения:</strong></p>
                    <ul style="list-style: disc; padding-left: 20px;">
                        <?php foreach ($report['messages'] as $message) : ?>
                            <li><?php echo esc_html((string) $message); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php
}
