<?php
/**
 * Local theme bootstrap.
 */
function local_theme_setup(): void
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('menus');
    add_theme_support('custom-logo', array(
        'height'      => 80,
        'width'       => 320,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    register_nav_menus(array(
        'primary' => 'Primary Menu',
    ));
}
add_action('after_setup_theme', 'local_theme_setup');

/**
 * Theme assets.
 */
function local_theme_assets(): void
{
    $style_path = get_theme_file_path('style.css');
    $script_path = get_theme_file_path('theme.js');
    $style_version = file_exists($style_path) ? (string) filemtime($style_path) : wp_get_theme()->get('Version');
    $script_version = file_exists($script_path) ? (string) filemtime($script_path) : wp_get_theme()->get('Version');

    wp_enqueue_style(
        'local-theme-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400&family=Montserrat:wght@400;500;600;700&family=Russo+One&display=swap',
        array(),
        null
    );

    wp_enqueue_style(
        'local-theme-style',
        get_stylesheet_uri(),
        array('local-theme-fonts'),
        $style_version
    );

    wp_enqueue_script(
        'local-theme-ui',
        get_template_directory_uri() . '/theme.js',
        array(),
        $script_version,
        true
    );
}
add_action('wp_enqueue_scripts', 'local_theme_assets');

/**
 * Category icon meta key.
 */
function local_theme_category_icon_meta_key(): string
{
    return 'local_theme_category_icon_id';
}

/**
 * Category menu order meta key.
 */
function local_theme_category_menu_order_meta_key(): string
{
    return 'local_theme_category_menu_order';
}

/**
 * Get category icon URL by term id.
 */
function local_theme_get_category_icon_url(int $term_id, string $size = 'thumbnail'): string
{
    $icon_id = (int) get_term_meta($term_id, local_theme_category_icon_meta_key(), true);
    if ($icon_id <= 0) {
        return '';
    }

    $url = wp_get_attachment_image_url($icon_id, $size);
    return $url ? $url : '';
}

/**
 * Add category icon field on "Add category" screen.
 */
function local_theme_category_add_icon_field(): void
{
    ?>
    <div class="form-field term-group">
        <label for="local_theme_category_icon_id">Иконка рубрики</label>
        <div class="local-theme-category-icon-control">
            <input type="hidden" id="local_theme_category_icon_id" name="local_theme_category_icon_id" value="">
            <div class="local-theme-category-icon-preview"></div>
            <p>
                <button type="button" class="button js-local-theme-icon-upload">Выбрать иконку</button>
                <button type="button" class="button js-local-theme-icon-remove">Удалить</button>
            </p>
        </div>
        <p class="description">Используется в выпадающем меню "Рубрики" в шапке.</p>
        <?php wp_nonce_field('local_theme_save_category_icon', 'local_theme_category_icon_nonce'); ?>
    </div>
    <div class="form-field term-group">
        <label for="local_theme_category_menu_order">Порядок в меню</label>
        <input
            type="number"
            id="local_theme_category_menu_order"
            name="local_theme_category_menu_order"
            value=""
            min="0"
            step="1"
        >
        <p class="description">Меньше число = выше в списке рубрик. Пусто = в конец.</p>
    </div>
    <?php
}
add_action('category_add_form_fields', 'local_theme_category_add_icon_field');

/**
 * Add category icon field on "Edit category" screen.
 */
function local_theme_category_edit_icon_field(WP_Term $term): void
{
    $icon_id = (int) get_term_meta($term->term_id, local_theme_category_icon_meta_key(), true);
    $icon_url = $icon_id > 0 ? wp_get_attachment_image_url($icon_id, 'thumbnail') : '';
    $menu_order_raw = get_term_meta($term->term_id, local_theme_category_menu_order_meta_key(), true);
    $menu_order = '' === (string) $menu_order_raw ? '' : (string) absint((string) $menu_order_raw);
    ?>
    <tr class="form-field term-group-wrap">
        <th scope="row"><label for="local_theme_category_icon_id">Иконка рубрики</label></th>
        <td>
            <div class="local-theme-category-icon-control">
                <input type="hidden" id="local_theme_category_icon_id" name="local_theme_category_icon_id" value="<?php echo esc_attr((string) $icon_id); ?>">
                <div class="local-theme-category-icon-preview">
                    <?php if ($icon_url) : ?>
                        <img src="<?php echo esc_url($icon_url); ?>" alt="" style="max-width:48px;height:auto;display:block;">
                    <?php endif; ?>
                </div>
                <p>
                    <button type="button" class="button js-local-theme-icon-upload">Выбрать иконку</button>
                    <button type="button" class="button js-local-theme-icon-remove">Удалить</button>
                </p>
            </div>
            <p class="description">Используется в выпадающем меню "Рубрики" в шапке.</p>
            <?php wp_nonce_field('local_theme_save_category_icon', 'local_theme_category_icon_nonce'); ?>
        </td>
    </tr>
    <tr class="form-field term-group-wrap">
        <th scope="row"><label for="local_theme_category_menu_order">Порядок в меню</label></th>
        <td>
            <input
                type="number"
                id="local_theme_category_menu_order"
                name="local_theme_category_menu_order"
                value="<?php echo esc_attr($menu_order); ?>"
                min="0"
                step="1"
            >
            <p class="description">Меньше число = выше в списке рубрик. Пусто = в конец.</p>
        </td>
    </tr>
    <?php
}
add_action('category_edit_form_fields', 'local_theme_category_edit_icon_field');

/**
 * Save category icon term meta.
 */
function local_theme_save_category_icon(int $term_id): void
{
    if (!current_user_can('manage_categories')) {
        return;
    }

    if (
        !isset($_POST['local_theme_category_icon_nonce']) ||
        !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['local_theme_category_icon_nonce'])), 'local_theme_save_category_icon')
    ) {
        return;
    }

    $icon_id = isset($_POST['local_theme_category_icon_id'])
        ? absint(wp_unslash($_POST['local_theme_category_icon_id']))
        : 0;

    if ($icon_id > 0) {
        update_term_meta($term_id, local_theme_category_icon_meta_key(), $icon_id);
    } else {
        delete_term_meta($term_id, local_theme_category_icon_meta_key());
    }

    $menu_order_raw = isset($_POST['local_theme_category_menu_order'])
        ? trim((string) wp_unslash($_POST['local_theme_category_menu_order']))
        : '';

    if ('' === $menu_order_raw) {
        delete_term_meta($term_id, local_theme_category_menu_order_meta_key());
        return;
    }

    update_term_meta($term_id, local_theme_category_menu_order_meta_key(), absint($menu_order_raw));
}
add_action('created_category', 'local_theme_save_category_icon');
add_action('edited_category', 'local_theme_save_category_icon');

/**
 * Add "menu order" column to categories table.
 */
function local_theme_category_columns(array $columns): array
{
    $columns['local_theme_category_menu_order'] = 'Порядок меню';
    return $columns;
}
add_filter('manage_edit-category_columns', 'local_theme_category_columns');

/**
 * Render category "menu order" column.
 */
function local_theme_category_column_value(string $content, string $column_name, int $term_id): string
{
    if ('local_theme_category_menu_order' !== $column_name) {
        return $content;
    }

    $value = get_term_meta($term_id, local_theme_category_menu_order_meta_key(), true);
    if ('' === (string) $value) {
        return '—';
    }

    return (string) absint((string) $value);
}
add_filter('manage_category_custom_column', 'local_theme_category_column_value', 10, 3);

/**
 * Enqueue admin scripts for category icon picker.
 */
function local_theme_category_icon_admin_assets(string $hook): void
{
    if ('edit-tags.php' !== $hook && 'term.php' !== $hook) {
        return;
    }

    $taxonomy = isset($_GET['taxonomy']) ? sanitize_key(wp_unslash($_GET['taxonomy'])) : '';
    if ('category' !== $taxonomy) {
        return;
    }

    wp_enqueue_media();
    wp_register_script('local-theme-category-icon-admin', '', array('jquery'), wp_get_theme()->get('Version'), true);
    wp_enqueue_script('local-theme-category-icon-admin');
    wp_add_inline_script(
        'local-theme-category-icon-admin',
        <<<'JS'
jQuery(function ($) {
  const previewMarkup = (url) => '<img src="' + url + '" alt="" style="max-width:48px;height:auto;display:block;">';

  $(document).on('click', '.js-local-theme-icon-upload', function (event) {
    event.preventDefault();

    const $control = $(this).closest('.local-theme-category-icon-control');
    const $input = $control.find('input[name="local_theme_category_icon_id"]');
    const $preview = $control.find('.local-theme-category-icon-preview');

    const frame = wp.media({
      title: 'Выберите иконку рубрики',
      button: { text: 'Использовать иконку' },
      multiple: false
    });

    frame.on('select', function () {
      const attachment = frame.state().get('selection').first().toJSON();
      const url = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

      $input.val(attachment.id);
      $preview.html(previewMarkup(url));
    });

    frame.open();
  });

  $(document).on('click', '.js-local-theme-icon-remove', function (event) {
    event.preventDefault();

    const $control = $(this).closest('.local-theme-category-icon-control');
    $control.find('input[name="local_theme_category_icon_id"]').val('');
    $control.find('.local-theme-category-icon-preview').empty();
  });
});
JS
    );
}
add_action('admin_enqueue_scripts', 'local_theme_category_icon_admin_assets');

/**
 * User local avatar meta key.
 */
function local_theme_user_avatar_meta_key(): string
{
    return 'local_theme_user_avatar_id';
}

/**
 * Get local avatar attachment ID for user.
 */
function local_theme_get_user_avatar_id(int $user_id): int
{
    return (int) get_user_meta($user_id, local_theme_user_avatar_meta_key(), true);
}

/**
 * Resolve user ID from get_avatar() input.
 *
 * @param mixed $id_or_email
 */
function local_theme_resolve_user_id_from_avatar($id_or_email): int
{
    if (is_numeric($id_or_email)) {
        return (int) $id_or_email;
    }

    if ($id_or_email instanceof WP_User) {
        return (int) $id_or_email->ID;
    }

    if ($id_or_email instanceof WP_Comment) {
        if (!empty($id_or_email->user_id)) {
            return (int) $id_or_email->user_id;
        }

        if (!empty($id_or_email->comment_author_email)) {
            $user = get_user_by('email', (string) $id_or_email->comment_author_email);
            return $user instanceof WP_User ? (int) $user->ID : 0;
        }
    }

    if (is_object($id_or_email) && isset($id_or_email->user_id) && is_numeric($id_or_email->user_id)) {
        return (int) $id_or_email->user_id;
    }

    if (is_string($id_or_email) && is_email($id_or_email)) {
        $user = get_user_by('email', $id_or_email);
        return $user instanceof WP_User ? (int) $user->ID : 0;
    }

    return 0;
}

/**
 * Render local avatar field on profile screens.
 */
function local_theme_user_avatar_profile_field(WP_User $user): void
{
    if (!current_user_can('upload_files')) {
        return;
    }

    $avatar_id = local_theme_get_user_avatar_id((int) $user->ID);
    $avatar_url = $avatar_id > 0 ? wp_get_attachment_image_url($avatar_id, 'thumbnail') : '';
    ?>
    <h2>Локальный аватар</h2>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th><label for="local_theme_user_avatar_id">Фото профиля</label></th>
                <td>
                    <input type="hidden" id="local_theme_user_avatar_id" name="local_theme_user_avatar_id" value="<?php echo esc_attr((string) $avatar_id); ?>">
                    <div class="local-theme-user-avatar-preview" style="margin-bottom:8px;">
                        <?php if ($avatar_url) : ?>
                            <img src="<?php echo esc_url($avatar_url); ?>" alt="" style="width:96px;height:96px;object-fit:cover;border-radius:50%;display:block;">
                        <?php endif; ?>
                    </div>
                    <p>
                        <button type="button" class="button js-local-theme-user-avatar-upload">Выбрать фото</button>
                        <button type="button" class="button js-local-theme-user-avatar-remove">Удалить</button>
                    </p>
                    <p class="description">Это фото будет использоваться в блоке «Истории авторов» и в get_avatar().</p>
                    <?php wp_nonce_field('local_theme_save_user_avatar', 'local_theme_user_avatar_nonce'); ?>
                </td>
            </tr>
            <tr>
                <th><label for="local_theme_author_status">Статус автора</label></th>
                <td>
                    <input
                        type="text"
                        id="local_theme_author_status"
                        name="local_theme_author_status"
                        value="<?php echo esc_attr((string) get_user_meta($user->ID, 'local_theme_author_status', true)); ?>"
                        class="regular-text"
                    >
                    <p class="description">Пример: Начальный, Продвинутый.</p>
                </td>
            </tr>
            <tr>
                <th><label for="local_theme_author_age">Возраст</label></th>
                <td>
                    <input
                        type="number"
                        id="local_theme_author_age"
                        name="local_theme_author_age"
                        min="0"
                        max="120"
                        value="<?php echo esc_attr((string) get_user_meta($user->ID, 'local_theme_author_age', true)); ?>"
                        class="small-text"
                    >
                </td>
            </tr>
            <tr>
                <th><label for="local_theme_author_city">Город</label></th>
                <td>
                    <input
                        type="text"
                        id="local_theme_author_city"
                        name="local_theme_author_city"
                        value="<?php echo esc_attr((string) get_user_meta($user->ID, 'local_theme_author_city', true)); ?>"
                        class="regular-text"
                    >
                </td>
            </tr>
            <tr>
                <th><label for="local_theme_author_hobbies">Увлечения</label></th>
                <td>
                    <textarea
                        id="local_theme_author_hobbies"
                        name="local_theme_author_hobbies"
                        rows="3"
                        class="large-text"
                    ><?php echo esc_textarea((string) get_user_meta($user->ID, 'local_theme_author_hobbies', true)); ?></textarea>
                    <p class="description">Любой текст. Можно через запятую или строками.</p>
                </td>
            </tr>
            <tr>
                <th><label for="local_theme_author_contact_link">Ссылка «Написать автору»</label></th>
                <td>
                    <input
                        type="url"
                        id="local_theme_author_contact_link"
                        name="local_theme_author_contact_link"
                        value="<?php echo esc_attr((string) get_user_meta($user->ID, 'local_theme_author_contact_link', true)); ?>"
                        class="regular-text"
                        placeholder="https://t.me/username"
                    >
                    <p class="description">Если пусто, будет использован email автора.</p>
                </td>
            </tr>
        </tbody>
    </table>
    <?php
}
add_action('show_user_profile', 'local_theme_user_avatar_profile_field');
add_action('edit_user_profile', 'local_theme_user_avatar_profile_field');

/**
 * Save local avatar user meta.
 */
function local_theme_save_user_avatar(int $user_id): void
{
    if (!current_user_can('edit_user', $user_id)) {
        return;
    }

    if (
        !isset($_POST['local_theme_user_avatar_nonce']) ||
        !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['local_theme_user_avatar_nonce'])), 'local_theme_save_user_avatar')
    ) {
        return;
    }

    $avatar_id = isset($_POST['local_theme_user_avatar_id'])
        ? absint(wp_unslash($_POST['local_theme_user_avatar_id']))
        : 0;

    if ($avatar_id > 0) {
        update_user_meta($user_id, local_theme_user_avatar_meta_key(), $avatar_id);
    } else {
        delete_user_meta($user_id, local_theme_user_avatar_meta_key());
    }

    $status = isset($_POST['local_theme_author_status']) ? sanitize_text_field(wp_unslash($_POST['local_theme_author_status'])) : '';
    $age = isset($_POST['local_theme_author_age']) ? absint(wp_unslash($_POST['local_theme_author_age'])) : 0;
    $city = isset($_POST['local_theme_author_city']) ? sanitize_text_field(wp_unslash($_POST['local_theme_author_city'])) : '';
    $hobbies = isset($_POST['local_theme_author_hobbies']) ? sanitize_textarea_field(wp_unslash($_POST['local_theme_author_hobbies'])) : '';
    $contact_link = isset($_POST['local_theme_author_contact_link']) ? esc_url_raw(wp_unslash($_POST['local_theme_author_contact_link'])) : '';

    if ('' !== $status) {
        update_user_meta($user_id, 'local_theme_author_status', $status);
    } else {
        delete_user_meta($user_id, 'local_theme_author_status');
    }

    if ($age > 0) {
        update_user_meta($user_id, 'local_theme_author_age', (string) $age);
    } else {
        delete_user_meta($user_id, 'local_theme_author_age');
    }

    if ('' !== $city) {
        update_user_meta($user_id, 'local_theme_author_city', $city);
    } else {
        delete_user_meta($user_id, 'local_theme_author_city');
    }

    if ('' !== $hobbies) {
        update_user_meta($user_id, 'local_theme_author_hobbies', $hobbies);
    } else {
        delete_user_meta($user_id, 'local_theme_author_hobbies');
    }

    if ('' !== $contact_link) {
        update_user_meta($user_id, 'local_theme_author_contact_link', $contact_link);
    } else {
        delete_user_meta($user_id, 'local_theme_author_contact_link');
    }
}
add_action('personal_options_update', 'local_theme_save_user_avatar');
add_action('edit_user_profile_update', 'local_theme_save_user_avatar');

/**
 * Load media uploader script on user profile screens.
 */
function local_theme_user_avatar_admin_assets(string $hook): void
{
    if ('profile.php' !== $hook && 'user-edit.php' !== $hook) {
        return;
    }

    wp_enqueue_media();
    wp_register_script('local-theme-user-avatar-admin', '', array('jquery'), wp_get_theme()->get('Version'), true);
    wp_enqueue_script('local-theme-user-avatar-admin');
    wp_add_inline_script(
        'local-theme-user-avatar-admin',
        <<<'JS'
jQuery(function ($) {
  const previewMarkup = (url) => '<img src="' + url + '" alt="" style="width:96px;height:96px;object-fit:cover;border-radius:50%;display:block;">';

  $(document).on('click', '.js-local-theme-user-avatar-upload', function (event) {
    event.preventDefault();

    const $td = $(this).closest('td');
    const $input = $td.find('input[name="local_theme_user_avatar_id"]');
    const $preview = $td.find('.local-theme-user-avatar-preview');

    const frame = wp.media({
      title: 'Выберите фото профиля',
      button: { text: 'Использовать фото' },
      multiple: false
    });

    frame.on('select', function () {
      const attachment = frame.state().get('selection').first().toJSON();
      const url = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
      $input.val(attachment.id);
      $preview.html(previewMarkup(url));
    });

    frame.open();
  });

  $(document).on('click', '.js-local-theme-user-avatar-remove', function (event) {
    event.preventDefault();
    const $td = $(this).closest('td');
    $td.find('input[name="local_theme_user_avatar_id"]').val('');
    $td.find('.local-theme-user-avatar-preview').empty();
  });
});
JS
    );
}
add_action('admin_enqueue_scripts', 'local_theme_user_avatar_admin_assets');

/**
 * Override get_avatar_data with local avatar when available.
 *
 * @param array<string, mixed> $args
 * @return array<string, mixed>
 */
function local_theme_pre_get_avatar_data(array $args, $id_or_email): array
{
    $user_id = local_theme_resolve_user_id_from_avatar($id_or_email);
    if ($user_id <= 0) {
        return $args;
    }

    $avatar_id = local_theme_get_user_avatar_id($user_id);
    if ($avatar_id <= 0) {
        return $args;
    }

    $size = isset($args['size']) ? (int) $args['size'] : 96;
    $src = wp_get_attachment_image_src($avatar_id, array($size, $size));
    if (!is_array($src) || empty($src[0])) {
        return $args;
    }

    $args['url'] = $src[0];
    $args['found_avatar'] = true;
    $args['width'] = $size;
    $args['height'] = $size;
    return $args;
}
add_filter('pre_get_avatar_data', 'local_theme_pre_get_avatar_data', 10, 2);

/**
 * Get public author display name.
 */
function local_theme_get_author_public_name(int $user_id): string
{
    $user = get_user_by('ID', $user_id);
    if (!$user instanceof WP_User) {
        return '';
    }

    $first_name = trim((string) get_user_meta($user_id, 'first_name', true));
    $last_name = trim((string) get_user_meta($user_id, 'last_name', true));
    $nickname = trim((string) get_user_meta($user_id, 'nickname', true));
    $display_name = trim((string) $user->display_name);
    $full_name = trim($first_name . ' ' . $last_name);

    if ('' !== $full_name) {
        return $full_name;
    }

    if ('' !== $nickname && !is_email($nickname)) {
        return $nickname;
    }

    if ('' !== $display_name && !is_email($display_name)) {
        return $display_name;
    }

    return (string) $user->user_login;
}

/**
 * Get avatar URL for author.
 */
function local_theme_get_author_avatar_url(int $user_id, int $size = 160): string
{
    $avatar_data = get_avatar_data($user_id, array(
        'size' => $size,
        'default' => '404',
    ));

    if (!empty($avatar_data['found_avatar']) && !empty($avatar_data['url'])) {
        return (string) $avatar_data['url'];
    }

    return '';
}

/**
 * Get users for "Истории авторов" block.
 *
 * @return array<int, WP_User>
 */
function local_theme_get_story_authors(int $limit = 20): array
{
    $users = get_users(array(
        'role__in' => array('matre_author', 'author'),
        'orderby' => 'display_name',
        'order' => 'ASC',
        'number' => $limit,
    ));

    return is_array($users) ? $users : array();
}
