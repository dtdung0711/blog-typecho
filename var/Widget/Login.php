<?php

namespace Widget;

use Typecho\Cookie;
use Typecho\Validate;
use Widget\Base\Users;

if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * Đăng nhập component
 *
 * @category typecho
 * @package Widget
 * @copyright Bản quyền © 2008 Nhóm Typecho (http://www.typecho.org)
 * @license Giấy phép Công cộng GNU phiên bản 2.0
 */
class Login extends Users implements ActionInterface
{
    /**
     * Hàm khởi tạo
     *
     * @access public
     * @return void
     */
    public function action()
    {
        // Bảo vệ
        $this->security->protect();

        /** Nếu đã đăng nhập */
        if ($this->user->hasLogin()) {
            /** Chuyển hướng trực tiếp */
            $this->response->redirect($this->options->index);
        }

        /** Khởi tạo đối tượng xác nhận */
        $validator = new Validate();
        $validator->addRule('name', 'required', _t('Vui lòng nhập tên người dùng'));
        $validator->addRule('password', 'required', _t('Vui lòng nhập mật khẩu'));
        $expire = 30 * 24 * 3600;

        /** Trạng thái ghi nhớ mật khẩu */
        if ($this->request->remember) {
            Cookie::set('__typecho_remember_remember', 1, $expire);
        } elseif (Cookie::get('__typecho_remember_remember')) {
            Cookie::delete('__typecho_remember_remember');
        }

        /** Xử lý ngoại lệ xác nhận */
        if ($error = $validator->run($this->request->from('name', 'password'))) {
            Cookie::set('__typecho_remember_name', $this->request->name);

            /** Thiết lập thông báo */
            Notice::alloc()->set($error);
            $this->response->goBack();
        }

        /** Bắt đầu xác thực người dùng **/
        $valid = $this->user->login(
            $this->request->name,
            $this->request->password,
            false,
            1 == $this->request->remember ? $expire : 0
        );

        /** So sánh mật khẩu */
        if (!$valid) {
            /** Ngăn chặn việc thử tìm kiếm, chờ 3 giây */
            sleep(3);

            self::pluginHandle()->loginFail(
                $this->user,
                $this->request->name,
                $this->request->password,
                1 == $this->request->remember
            );

            Cookie::set('__typecho_remember_name', $this->request->name);
            Notice::alloc()->set(_t('Tên người dùng hoặc mật khẩu không hợp lệ'), 'error');
            $this->response->goBack('?referer=' . urlencode($this->request->referer));
        }

        self::pluginHandle()->loginSucceed(
            $this->user,
            $this->request->name,
            $this->request->password,
            1 == $this->request->remember
        );

        /** Chuyển hướng đến địa chỉ sau xác thực */
        if (!empty($this->request->referer)) {
            /** Sửa lỗi #952 & xác nhận URL chuyển hướng */
            if (
                0 === strpos($this->request->referer, $this->options->adminUrl)
                || 0 === strpos($this->request->referer, $this->options->siteUrl)
            ) {
                $this->response->redirect($this->request->referer);
            }
        } elseif (!$this->user->pass('contributor', true)) {
            /** Không cho phép người dùng thông thường trực tiếp truy cập vào trang quản trị */
            $this->response->redirect($this->options->profileUrl);
        }

        $this->response->redirect($this->options->adminUrl);
    }
}
