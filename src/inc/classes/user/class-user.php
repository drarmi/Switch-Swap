<?php
namespace Omnis\src\inc\classes\user;

class User {
    private static $instance = null;

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
            self::$instance->setup_hooks();
        }
        return self::$instance;
    }

    private function setup_hooks() {
        add_filter('manage_users_columns', [$this, 'add_profile_picture_column']);
        add_action('manage_users_custom_column', [$this, 'display_profile_picture_column'], 10, 3);
    }

    // Добавляем колонку для изображения профиля в таблицу пользователей
    public function add_profile_picture_column($columns) {
        $columns['profile_picture'] = __('Profile Picture', 'omnis_base');
        return $columns;
    }

    // Отображаем изображение профиля в колонке
    public function display_profile_picture_column($value, $column_name, $user_id) {
        if ('profile_picture' === $column_name) {
            $profile_picture = get_user_meta($user_id, 'profile_picture', true);
            return $profile_picture ? '<img src="' . esc_url($profile_picture) . '" width="50" height="50" />' : __('No image', 'omnis_base');
        }
        return $value;
    }
}

// Инициализация класса User
User::get_instance();
