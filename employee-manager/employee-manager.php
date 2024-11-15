<?php
/*
Plugin Name: Employee Manager (Nhân Viên)
Description: A plugin to manage employee data using an existing nhan_vien database table.
Version: 1.0
Author: Your Name
*/

if (!defined('ABSPATH')) {
    exit;
}

class EmployeeManager
{
    public $table_name = 'nhan_vien';

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_menu_pages'));
    }

    public static function activate()
    {
        global $wpdb;

        $table_name = 'nhan_vien';

        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            wp_die(
                'Lỗi: Bảng "nhan_vien" không tồn tại trong cơ sở dữ liệu. Vui lòng kiểm tra lại.',
                'Lỗi Kích Hoạt Plugin',
                array('back_link' => true)
            );
        }
    }

    public static function deactivate() {}

    public function add_menu_pages()
    {
        add_menu_page(
            'Quản lý nhân viên',
            'Nhân Viên',
            'manage_options',
            'employee-manager',
            array($this, 'list_employees'),
            'dashicons-businessperson',
            6
        );
        add_submenu_page(
            'employee-manager',
            'Thêm Nhân Viên Mới',
            'Thêm Mới',
            'manage_options',
            'add-new-employee',
            array($this, 'add_employee')
        );
    }

    public function list_employees()
    {
        global $wpdb;

        $employees = $wpdb->get_results("SELECT * FROM $this->table_name");
        echo '<div class="wrap"><h1>Danh sách nhân viên</h1>';
        echo '<table class="widefat fixed" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Mã nhân viên</th>
                    <th>Chức vụ</th>
                    <th>Tên</th>
                    <th>Ngày sinh</th>
                    <th>SĐT</th>
                    <th>Email</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($employees as $employee) {
            echo "<tr>
                <td>{$employee->id_nhan_vien}</td>
                <td>{$employee->ma_nhan_vien}</td>
                <td>{$employee->id_chuc_vu}</td>
                <td>{$employee->ten_nhan_vien}</td>
                <td>{$employee->ngay_sinh}</td>
                <td>{$employee->sdt}</td>
                <td>{$employee->email}</td>
                <td>{$employee->status_id}</td>
            </tr>";
        }
        echo '</tbody></table></div>';
    }

    public function add_employee()
    {
        if (isset($_POST['submit_employee'])) {
            global $wpdb;

            $wpdb->insert(
                $this->table_name,
                array(
                    'ma_nhan_vien' => sanitize_text_field($_POST['ma_nhan_vien']),
                    'id_chuc_vu' => intval($_POST['id_chuc_vu']),
                    'ten_nhan_vien' => sanitize_text_field($_POST['ten_nhan_vien']),
                    'ngay_sinh' => sanitize_text_field($_POST['ngay_sinh']),
                    'sdt' => sanitize_text_field($_POST['sdt']),
                    'email' => sanitize_email($_POST['email']),
                    'status_id' => intval($_POST['status_id'])
                )
            );
            dd($_POST['ma_nhan_vien'], $_POST['id_chuc_vu']);
            echo '<div class="updated"><p>Thêm nhân viên thành công!</p></div>';
        }

        echo '<h2>Thêm Nhân Viên Mới</h2>
        <form method="POST" action="">
            <table>
                <tr><td>Mã nhân viên:</td><td><input type="text" name="ma_nhan_vien" required></td></tr>
                <tr><td>Chức vụ ID:</td><td><input type="number" name="id_chuc_vu"></td></tr>
                <tr><td>Tên nhân viên:</td><td><input type="text" name="ten_nhan_vien" required></td></tr>
                <tr><td>Ngày sinh:</td><td><input type="date" name="ngay_sinh"></td></tr>
                <tr><td>Số điện thoại:</td><td><input type="text" name="sdt"></td></tr>
                <tr><td>Email:</td><td><input type="email" name="email"></td></tr>
                <tr><td>Trạng thái:</td><td><input type="number" name="status_id"></td></tr>
            </table>
            <input type="submit" name="submit_employee" value="Thêm Nhân Viên" class="button button-primary">
        </form>';
    }
}

function dd(...$input)
{
    echo "<pre style='text-align: left !important; background-color: #f5f5f5; padding: 10px; border: 1px solid #ccc; border-radius: 5px; over-flow:auto'>";
    if (count($input) > 1) {
        foreach ($input as $index => $item) {
            $random_color = '#' . substr(md5(rand()), 0, 6);
            echo "<strong style='color:{$random_color}'>Input {$index}:</strong><br>";
            print_r($item);
            echo "<br><br>";
        }
    } else {
        //Kiem tra có phai la cau sql khong
        $is_sql = false;
        $sqlKeywords = ['SELECT', 'INSERT', 'UPDATE', 'DELETE', 'FROM', 'WHERE', 'AND', 'OR', 'JOIN', 'GROUP BY', 'ORDER BY', 'LIMIT'];
        if (is_string($input[0])) {
            foreach ($sqlKeywords as $keyword) {
                if (stripos($input[0], $keyword) !== false) {
                    $is_sql = true;
                }
            }

            if ($is_sql == true) {
                $keywords = ['SELECT', 'FROM', 'LEFT JOIN', 'INNER JOIN', 'RIGHT JOIN', 'WHERE', 'GROUP BY', 'ORDER BY', 'LIMIT'];
                foreach ($keywords as $keyword) {
                    $input[0] = str_replace($keyword, "\n<strong>{$keyword}</strong>", $input[0]);
                }
                echo $input[0];
            } else {
                print_r($input[0]);
            }
        } else {
            print_r($input[0]);
        }
    }
    echo "</pre>";
    die;
}

new EmployeeManager();
