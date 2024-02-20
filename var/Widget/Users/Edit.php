<?php

namespace Widget\Users;

use Typecho\Common;
use Typecho\Widget\Exception;
use Typecho\Widget\Helper\Form;
use Utils\PasswordHash;
use Widget\ActionInterface;
use Widget\Base\Users;
use Widget\Notice;

if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * 编辑用户组件
 *
 * @link typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Edit extends Users implements ActionInterface
{
    /**
     * 执行函数
     *
     * @return void
     * @throws Exception|\Typecho\Db\Exception
     */
    public function execute()
    {
        /** 管理员以上权限 */
        $this->user->pass('administrator');

        /** 更新模式 */
        if (($this->request->uid && 'delete' != $this->request->do) || 'update' == $this->request->do) {
            $this->db->fetchRow($this->select()
                ->where('uid = ?', $this->request->uid)->limit(1), [$this, 'push']);

            if (!$this->have()) {
                throw new Exception(_t('用户不存在'), 404);
            }
        }
    }

    /**
     * 获取菜单标题
     *
     * @return string
     */
    public function getMenuTitle(): string
    {
        return _t('编辑用户 %s', $this->name);
    }

    /**
     * 判断用户是否存在
     *
     * @param integer $uid 用户主键
     * @return boolean
     * @throws \Typecho\Db\Exception
     */
    public function userExists(int $uid): bool
    {
        $user = $this->db->fetchRow($this->db->select()
            ->from('table.users')
            ->where('uid = ?', $uid)->limit(1));

        return !empty($user);
    }

    /**
     * 增加用户
     *
     * @throws \Typecho\Db\Exception
     */
    public function insertUser()
    {
        if ($this->form('insert')->validate()) {
            $this->response->goBack();
        }

        $hasher = new PasswordHash(8, true);

        /** 取出数据 */
        $user = $this->request->from('name', 'mail', 'screenName', 'password', 'url', 'group');
        $user['screenName'] = empty($user['screenName']) ? $user['name'] : $user['screenName'];
        $user['password'] = $hasher->hashPassword($user['password']);
        $user['created'] = $this->options->time;

        /** 插入数据 */
        $user['uid'] = $this->insert($user);

        /** 设置高亮 */
        Notice::alloc()->highlight('user-' . $user['uid']);

        /** 提示信息 */
        Notice::alloc()->set(_t('Người dùng %s đã được thêm', $user['screenName']), 'success');

        /** 转向原页 */
        $this->response->redirect(Common::url('manage-users.php', $this->options->adminUrl));
    }

   /**
 * Tạo biểu mẫu
 *
 * @access public
 * @param string|null $action Hành động của biểu mẫu
 * @return Form
 */
public function form(?string $action = null): Form
{
    /** Xây dựng biểu mẫu */
    $form = new Form($this->security->getIndex('/action/users-edit'), Form::POST_METHOD);

    /** Tên người dùng */
    $name = new Form\Element\Text('name', null, null, _t('Tên người dùng') . ' *', _t('Tên này sẽ được sử dụng khi người dùng đăng nhập.')
        . '<br />' . _t('Vui lòng không trùng với tên người dùng hiện có trong hệ thống.'));
    $form->addInput($name);

    /** Địa chỉ email */
    $mail = new Form\Element\Text('mail', null, null, _t('Địa chỉ email') . ' *', _t('Địa chỉ email sẽ là phương tiện liên lạc chính của người dùng.')
        . '<br />' . _t('Vui lòng không trùng với địa chỉ email hiện có trong hệ thống.'));
    $form->addInput($mail);

    /** Bút danh của người dùng */
    $screenName = new Form\Element\Text('screenName', null, null, _t('Bút danh'), _t('Bút danh có thể khác với tên người dùng, dùng để hiển thị trên giao diện.')
        . '<br />' . _t('Nếu bạn để trống trường này, hệ thống sẽ sử dụng tên người dùng mặc định.'));
    $form->addInput($screenName);

    /** Mật khẩu của người dùng */
    $password = new Form\Element\Password('password', null, null, _t('Mật khẩu'), _t('Gán một mật khẩu cho người dùng.')
        . '<br />' . _t('Đề nghị sử dụng kết hợp ký tự đặc biệt, chữ cái và số để tăng cường bảo mật.'));
    $password->input->setAttribute('class', 'w-60');
    $form->addInput($password);

    /** Xác nhận mật khẩu của người dùng */
    $confirm = new Form\Element\Password('confirm', null, null, _t('Xác nhận mật khẩu'), _t('Vui lòng xác nhận mật khẩu của bạn, phải trùng với mật khẩu đã nhập ở trên.'));
    $confirm->input->setAttribute('class', 'w-60');
    $form->addInput($confirm);

    /** Địa chỉ trang cá nhân của người dùng */
    $url = new Form\Element\Text('url', null, null, _t('Địa chỉ trang cá nhân'), _t('Địa chỉ trang cá nhân của người dùng, vui lòng bắt đầu với <code>http://</code>.'));
    $form->addInput($url);

    /** Nhóm của người dùng */
    $group = new Form\Element\Select(
        'group',
        [
            'subscriber'  => _t('Người theo dõi'),
            'contributor' => _t('Người đóng góp'),
            'editor' => _t('Biên tập viên'),
            'administrator' => _t('Quản trị viên')
        ],
        null,
        _t('Nhóm người dùng'),
        _t('Các nhóm người dùng khác nhau có các quyền khác nhau.') . '<br />' . _t('Xem bảng phân quyền cụ thể tại <a href="https://docs.typecho.org/develop/acl">đây</a>.')
    );
    $form->addInput($group);

    /** Hành động của người dùng */
    $do = new Form\Element\Hidden('do');
    $form->addInput($do);

    /** Khóa chính của người dùng */
    $uid = new Form\Element\Hidden('uid');
    $form->addInput($uid);

    /** Nút gửi */
    $submit = new Form\Element\Submit();
    $submit->input->setAttribute('class', 'btn primary');
    $form->addItem($submit);

    if (null != $this->request->uid) {
        $submit->value(_t('Chỉnh sửa người dùng'));
        $name->value($this->name);
        $screenName->value($this->screenName);
        $url->value($this->url);
        $mail->value($this->mail);
        $group->value($this->group);
        $do->value('update');
        $uid->value($this->uid);
        $_action = 'update';
    } else {
        $submit->value(_t('Thêm người dùng'));
        $do->value('insert');
        $_action = 'insert';
    }

    if (empty($action)) {
        $action = $_action;
    }

    /** Thêm các quy tắc cho biểu mẫu */
    if ('insert' == $action || 'update' == $action) {
        $screenName->addRule([$this, 'screenNameExists'], _t('Bút danh đã tồn tại'));
        $screenName->addRule('xssCheck', _t('Vui lòng không sử dụng ký tự đặc biệt trong bút danh'));
        $url->addRule('url', _t('Định dạng địa chỉ trang cá nhân không đúng'));
        $mail->addRule('required', _t('Vui lòng nhập địa chỉ email'));
        $mail->addRule([$this, 'mailExists'], _t('Địa chỉ email đã tồn tại'));
        $mail->addRule('email', _t('Định dạng địa chỉ email không đúng'));
        $password->addRule('minLength', _t('Để đảm bảo an toàn tài khoản, vui lòng nhập ít nhất sáu ký tự'), 6);
        $confirm->addRule('confirm', _t('Hai mật khẩu nhập không khớp'), 'password');
    }

    if ('insert' == $action) {
        $name->addRule('required', _t('Vui lòng nhập tên người dùng'));
        $name->addRule('xssCheck', _t('Vui lòng không sử dụng ký tự đặc biệt trong tên người dùng'));
        $name->addRule([$this, 'nameExists'], _t('Tên người dùng đã tồn tại'));
        $password->label(_t('Mật khẩu') . ' *');
        $confirm->label(_t('Xác nhận mật khẩu') . ' *');
        $password->addRule('required', _t('Vui lòng nhập mật khẩu'));
    }

    if ('update' == $action) {
        $name->input->setAttribute('disabled', 'disabled');
        $uid->addRule('required', _t('Khóa chính người dùng không tồn tại'));
        $uid->addRule([$this, 'userExists'], _t('Người dùng không tồn tại'));
    }

    return $form;
}

    /**
 * Cập nhật người dùng
 *
 * @throws \Typecho\Db\Exception
 */
public function updateUser()
{
    if ($this->form('update')->validate()) {
        $this->response->goBack();
    }

    /** Lấy dữ liệu */
    $user = $this->request->from('mail', 'screenName', 'password', 'url', 'group');
    $user['screenName'] = empty($user['screenName']) ? $user['name'] : $user['screenName'];
    if (empty($user['password'])) {
        unset($user['password']);
    } else {
        $hasher = new PasswordHash(8, true);
        $user['password'] = $hasher->hashPassword($user['password']);
    }

    /** Cập nhật dữ liệu */
    $this->update($user, $this->db->sql()->where('uid = ?', $this->request->uid));

    /** Đặt điểm nổi bật */
    Notice::alloc()->highlight('user-' . $this->request->uid);

    /** Thiết lập thông báo */
    Notice::alloc()->set(_t('Người dùng %s đã được cập nhật', $user['screenName']), 'success');

    /** Chuyển hướng về trang gốc */
    $this->response->redirect(Common::url('manage-users.php?' .
        $this->getPageOffsetQuery($this->request->uid), $this->options->adminUrl));
}

/**
 * Lấy chuỗi truy vấn trang dịch chuyển
 *
 * @param integer $uid ID của người dùng
 * @return string
 * @throws \Typecho\Db\Exception
 */
protected function getPageOffsetQuery(int $uid): string
{
    return 'page=' . $this->getPageOffset('uid', $uid);
}

/**
 * Xóa người dùng
 *
 * @throws \Typecho\Db\Exception
 */
public function deleteUser()
{
    $users = $this->request->filter('int')->getArray('uid');
    $masterUserId = $this->db->fetchObject($this->db->select(['MIN(uid)' => 'num'])->from('table.users'))->num;
    $deleteCount = 0;

    foreach ($users as $user) {
        if ($masterUserId == $user || $user == $this->user->uid) {
            continue;
        }

        if ($this->delete($this->db->sql()->where('uid = ?', $user))) {
            $deleteCount++;
        }
    }

    /** Thiết lập thông báo */
    Notice::alloc()->set(
        $deleteCount > 0 ? _t('Người dùng đã được xóa') : _t('Không có người dùng nào được xóa'),
        $deleteCount > 0 ? 'success' : 'notice'
    );

    /** Chuyển hướng về trang gốc */
    $this->response->redirect(Common::url('manage-users.php', $this->options->adminUrl));
}

/**
 * Hàm nhập
 *
 * @access public
 * @return void
 */
public function action()
{
    $this->user->pass('administrator');
    $this->security->protect();
    $this->on($this->request->is('do=insert'))->insertUser();
    $this->on($this->request->is('do=update'))->updateUser();
    $this->on($this->request->is('do=delete'))->deleteUser();
    $this->response->redirect($this->options->adminUrl);
}
}
