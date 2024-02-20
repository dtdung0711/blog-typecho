<?php

namespace Widget;

use Typecho\Common;
use Typecho\Cookie;
use Typecho\Db\Exception;
use Typecho\Validate;
use Utils\PasswordHash;
use Widget\Base\Users;

if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * Component đăng ký
 *
 * @author qining
 * @category typecho
 * @package Widget
 */
class Register extends Users implements ActionInterface
{
    /**
     * Hàm khởi tạo
     *
     * @throws Exception
     */
    public function action()
    {
        // Bảo vệ
        $this->security->protect();

        /** Nếu đã đăng nhập hoặc không cho phép đăng ký */
        if ($this->user->hasLogin() || !$this->options->allowRegister) {
            /** Chuyển hướng trực tiếp */
            $this->response->redirect($this->options->index);
        }

        /** Khởi tạo đối tượng xác nhận */
        $validator = new Validate();
        $validator->addRule('name', 'required', _t('Phải nhập tên người dùng'));
        $validator->addRule('name', 'minLength', _t('Tên người dùng phải chứa ít nhất 2 ký tự'), 2);
        $validator->addRule('name', 'maxLength', _t('Tên người dùng chỉ có thể chứa tối đa 32 ký tự'), 32);
        $validator->addRule('name', 'xssCheck', _t('Vui lòng không sử dụng ký tự đặc biệt trong tên người dùng'));
        $validator->addRule('name', [$this, 'nameExists'], _t('Tên người dùng đã tồn tại'));
        $validator->addRule('mail', 'required', _t('Phải nhập địa chỉ email'));
        $validator->addRule('mail', [$this, 'mailExists'], _t('Địa chỉ email đã tồn tại'));
        $validator->addRule('mail', 'email', _t('Địa chỉ email không hợp lệ'));
        $validator->addRule('mail', 'maxLength', _t('Địa chỉ email chỉ có thể chứa tối đa 64 ký tự'), 64);

        /** Nếu yêu cầu chứa mật khẩu */
        if (array_key_exists('password', $_REQUEST)) {
            $validator->addRule('password', 'required', _t('Phải nhập mật khẩu'));
            $validator->addRule('password', 'minLength', _t('Để đảm bảo an toàn cho tài khoản, mật khẩu phải chứa ít nhất 6 ký tự'), 6);
            $validator->addRule('password', 'maxLength', _t('Để dễ nhớ, mật khẩu không được quá 18 ký tự'), 18);
            $validator->addRule('confirm', 'confirm', _t('Hai lần nhập mật khẩu không khớp'), 'password');
        }

        /** Nếu có lỗi trong quá trình xác nhận */
        if ($error = $validator->run($this->request->from('name', 'password', 'mail', 'confirm'))) {
            Cookie::set('__typecho_remember_name', $this->request->name);
            Cookie::set('__typecho_remember_mail', $this->request->mail);

            /** Thiết lập thông báo */
            Notice::alloc()->set($error);
            $this->response->goBack();
        }

        $hasher = new PasswordHash(8, true);
        $generatedPassword = Common::randString(7);

        $dataStruct = [
            'name' => $this->request->name,
            'mail' => $this->request->mail,
            'screenName' => $this->request->name,
            'password' => $hasher->hashPassword($generatedPassword),
            'created' => $this->options->time,
            'group' => 'subscriber'
        ];

        $dataStruct = self::pluginHandle()->register($dataStruct);

        $insertId = $this->insert($dataStruct);
        $this->db->fetchRow($this->select()->where('uid = ?', $insertId)
            ->limit(1), [$this, 'push']);

        self::pluginHandle()->finishRegister($this);

        $this->user->login($this->request->name, $generatedPassword);

        Cookie::delete('__typecho_first_run');
        Cookie::delete('__typecho_remember_name');
        Cookie::delete('__typecho_remember_mail');

        Notice::alloc()->set(
            _t(
                'Người dùng <strong>%s</strong> đã đăng ký thành công, mật khẩu là <strong>%s</strong>',
                $this->screenName,
                $generatedPassword
            ),
            'success'
        );
        $this->response->redirect($this->options->adminUrl);
    }
}
