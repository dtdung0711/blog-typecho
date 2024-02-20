<?php

namespace Widget\Options;

use Typecho\Db\Exception;
use Typecho\Widget\Helper\Form;
use Widget\ActionInterface;
use Widget\Base\Options;
use Widget\Notice;

if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * 评论设置组件
 *
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Discussion extends Options implements ActionInterface
{
    /**
     * 执行更新动作
     *
     * @throws Exception
     */
    public function updateDiscussionSettings()
    {
        /** 验证格式 */
        if ($this->form()->validate()) {
            $this->response->goBack();
        }

        $settings = $this->request->from(
            'commentDateFormat',
            'commentsListSize',
            'commentsPageSize',
            'commentsPageDisplay',
            'commentsAvatar',
            'commentsOrder',
            'commentsMaxNestingLevels',
            'commentsUrlNofollow',
            'commentsPostTimeout',
            'commentsUniqueIpInterval',
            'commentsWhitelist',
            'commentsRequireMail',
            'commentsAvatarRating',
            'commentsPostTimeout',
            'commentsPostInterval',
            'commentsRequireModeration',
            'commentsRequireURL',
            'commentsHTMLTagAllowed',
            'commentsStopWords',
            'commentsIpBlackList'
        );
        $settings['commentsShow'] = $this->request->getArray('commentsShow');
        $settings['commentsPost'] = $this->request->getArray('commentsPost');

        $settings['commentsShowCommentOnly'] = $this->isEnableByCheckbox(
            $settings['commentsShow'],
            'commentsShowCommentOnly'
        );
        $settings['commentsMarkdown'] = $this->isEnableByCheckbox($settings['commentsShow'], 'commentsMarkdown');
        $settings['commentsShowUrl'] = $this->isEnableByCheckbox($settings['commentsShow'], 'commentsShowUrl');
        $settings['commentsUrlNofollow'] = $this->isEnableByCheckbox($settings['commentsShow'], 'commentsUrlNofollow');
        $settings['commentsAvatar'] = $this->isEnableByCheckbox($settings['commentsShow'], 'commentsAvatar');
        $settings['commentsPageBreak'] = $this->isEnableByCheckbox($settings['commentsShow'], 'commentsPageBreak');
        $settings['commentsThreaded'] = $this->isEnableByCheckbox($settings['commentsShow'], 'commentsThreaded');

        $settings['commentsPageSize'] = intval($settings['commentsPageSize']);
        $settings['commentsMaxNestingLevels'] = min(7, max(2, intval($settings['commentsMaxNestingLevels'])));
        $settings['commentsPageDisplay'] = ('first' == $settings['commentsPageDisplay']) ? 'first' : 'last';
        $settings['commentsOrder'] = ('DESC' == $settings['commentsOrder']) ? 'DESC' : 'ASC';
        $settings['commentsAvatarRating'] = in_array($settings['commentsAvatarRating'], ['G', 'PG', 'R', 'X'])
            ? $settings['commentsAvatarRating'] : 'G';

        $settings['commentsRequireModeration'] = $this->isEnableByCheckbox(
            $settings['commentsPost'],
            'commentsRequireModeration'
        );
        $settings['commentsWhitelist'] = $this->isEnableByCheckbox($settings['commentsPost'], 'commentsWhitelist');
        $settings['commentsRequireMail'] = $this->isEnableByCheckbox($settings['commentsPost'], 'commentsRequireMail');
        $settings['commentsRequireURL'] = $this->isEnableByCheckbox($settings['commentsPost'], 'commentsRequireURL');
        $settings['commentsCheckReferer'] = $this->isEnableByCheckbox(
            $settings['commentsPost'],
            'commentsCheckReferer'
        );
        $settings['commentsAntiSpam'] = $this->isEnableByCheckbox($settings['commentsPost'], 'commentsAntiSpam');
        $settings['commentsAutoClose'] = $this->isEnableByCheckbox($settings['commentsPost'], 'commentsAutoClose');
        $settings['commentsPostIntervalEnable'] = $this->isEnableByCheckbox(
            $settings['commentsPost'],
            'commentsPostIntervalEnable'
        );

        $settings['commentsPostTimeout'] = intval($settings['commentsPostTimeout']) * 24 * 3600;
        $settings['commentsPostInterval'] = round($settings['commentsPostInterval'], 1) * 60;

        unset($settings['commentsShow']);
        unset($settings['commentsPost']);

        foreach ($settings as $name => $value) {
            $this->update(['value' => $value], $this->db->sql()->where('name = ?', $name));
        }

        Notice::alloc()->set(_t("Cài đặt đã được lưu"), 'success');
        $this->response->goBack();
    }

    /**
     * 输出表单结构
     *
     * @return Form
     */
    public function form(): Form
    {
        /** 构建表格 */
        $form = new Form($this->security->getIndex('/action/options-discussion'), Form::POST_METHOD);

        /** 评论日期格式 */
        $commentDateFormat = new Form\Element\Text(
            'commentDateFormat',
            null,
            $this->options->commentDateFormat,
            _t('Định dạng ngày của bình luận'),
            _t('Đây là một định dạng mặc định, khi bạn gọi phương thức hiển thị ngày bình luận trong mẫu, nếu không có định dạng ngày cụ thể, nó sẽ xuất theo định dạng này.') . '<br />' . _t('Xem cách viết cụ thể tại <a href="https://www.php.net/manual/zh/function.date.php">Hướng dẫn về Định dạng ngày PHP</a>.')
        );
        $commentDateFormat->input->setAttribute('class', 'w-40 mono');
        $form->addInput($commentDateFormat);
        
        /** Số lượng danh sách bình luận */
        $commentsListSize = new Form\Element\Text(
            'commentsListSize',
            null,
            $this->options->commentsListSize,
            _t('Số lượng danh sách bình luận'),
            _t('Số này được sử dụng để chỉ định số lượng danh sách bình luận hiển thị trong thanh bên.')
        );
        $commentsListSize->input->setAttribute('class', 'w-20');
        $form->addInput($commentsListSize->addRule('isInteger', _t('Vui lòng nhập một số nguyên')));
        
        $commentsShowOptions = [
            'commentsShowCommentOnly' => _t('Chỉ hiển thị bình luận, không hiển thị Pingback và Trackback'),
            'commentsMarkdown'        => _t('Sử dụng cú pháp Markdown trong bình luận'),
            'commentsShowUrl'         => _t('Tự động thêm liên kết trang cá nhân của người bình luận khi hiển thị tên của họ'),
            'commentsUrlNofollow'     => _t('Sử dụng thuộc tính <a href="https://en.wikipedia.org/wiki/Nofollow">nofollow</a> cho liên kết trang cá nhân của người bình luận'),
            'commentsAvatar'          => _t('Bật dịch vụ ảnh đại diện <a href="https://gravatar.com">Gravatar</a>, hiển thị ảnh đại diện với hạng mục đánh giá tối đa %s',
                '</label><select id="commentsShow-commentsAvatarRating" name="commentsAvatarRating">
            <option value="G"' . ('G' == $this->options->commentsAvatarRating ? ' selected="true"' : '') . '>' . _t('G - Phổ biến') . '</option>
            <option value="PG"' . ('PG' == $this->options->commentsAvatarRating ? ' selected="true"' : '') . '>' . _t('PG - Trên 13 tuổi') . '</option>
            <option value="R"' . ('R' == $this->options->commentsAvatarRating ? ' selected="true"' : '') . '>' . _t('R - Trên 17 tuổi') . '</option>
            <option value="X"' . ('X' == $this->options->commentsAvatarRating ? ' selected="true"' : '') . '>' . _t('X - Giới hạn') . '</option></select>
            <label for="commentsShow-commentsAvatarRating">'),
            'commentsPageBreak'       => _t('Bật chế độ phân trang, và mỗi trang hiển thị %s bình luận, %s được sử dụng là mặc định khi liệt kê',
                '</label><input type="text" value="' . $this->options->commentsPageSize
                . '" class="text num text-s" id="commentsShow-commentsPageSize" name="commentsPageSize" /><label for="commentsShow-commentsPageSize">',
                '</label><select id="commentsShow-commentsPageDisplay" name="commentsPageDisplay">
            <option value="first"' . ('first' == $this->options->commentsPageDisplay ? ' selected="true"' : '') . '>' . _t('Trang đầu tiên') . '</option>
            <option value="last"' . ('last' == $this->options->commentsPageDisplay ? ' selected="true"' : '') . '>' . _t('Trang cuối cùng') . '</option></select>'
                . '<label for="commentsShow-commentsPageDisplay">'),
            'commentsThreaded'        => _t('Bật trả lời bình luận, với %s lớp là mức tối đa cho mỗi bình luận',
                    '</label><input name="commentsMaxNestingLevels" type="text" class="text num text-s" value="' . $this->options->commentsMaxNestingLevels . '" id="commentsShow-commentsMaxNestingLevels" />
            <label for="commentsShow-commentsMaxNestingLevels">') . '</label></span><span class="multiline">'
                . _t('Hiển thị bình luận của %s trước', '<select id="commentsShow-commentsOrder" name="commentsOrder">
            <option value="DESC"' . ('DESC' == $this->options->commentsOrder ? ' selected="true"' : '') . '>' . _t('Mới nhất') . '</option>
            <option value="ASC"' . ('ASC' == $this->options->commentsOrder ? ' selected="true"' : '') . '>' . _t('Cũ nhất') . '</option></select><label for="commentsShow-commentsOrder">')
        ];        

        $commentsShowOptionsValue = [];
        if ($this->options->commentsShowCommentOnly) {
            $commentsShowOptionsValue[] = 'commentsShowCommentOnly';
        }

        if ($this->options->commentsMarkdown) {
            $commentsShowOptionsValue[] = 'commentsMarkdown';
        }

        if ($this->options->commentsShowUrl) {
            $commentsShowOptionsValue[] = 'commentsShowUrl';
        }

        if ($this->options->commentsUrlNofollow) {
            $commentsShowOptionsValue[] = 'commentsUrlNofollow';
        }

        if ($this->options->commentsAvatar) {
            $commentsShowOptionsValue[] = 'commentsAvatar';
        }

        if ($this->options->commentsPageBreak) {
            $commentsShowOptionsValue[] = 'commentsPageBreak';
        }

        if ($this->options->commentsThreaded) {
            $commentsShowOptionsValue[] = 'commentsThreaded';
        }

        $commentsShow = new Form\Element\Checkbox(
            'commentsShow',
            $commentsShowOptions,
            $commentsShowOptionsValue,
            _t('Hiển thị bình luận')
        );
        $form->addInput($commentsShow->multiMode());

        /** 评论提交 */
        $commentsPostOptions = [
            'commentsRequireModeration'  => _t('Tất cả bình luận phải được kiểm duyệt trước khi xuất hiện'),
            'commentsWhitelist'          => _t('Người bình luận phải từng có bình luận được phê duyệt trước đó'),
            'commentsRequireMail'        => _t('Bắt buộc phải nhập địa chỉ email'),
            'commentsRequireURL'         => _t('Bắt buộc phải nhập URL'),
            'commentsCheckReferer'       => _t('Kiểm tra xem URL nguồn của bình luận có khớp với liên kết bài viết hay không'),
            'commentsAntiSpam'           => _t('Bật chế độ chống spam'),
            'commentsAutoClose'          => _t('Tự động đóng bình luận sau %s ngày từ khi bài viết được đăng',
                '</label><input name="commentsPostTimeout" type="text" class="text num text-s" value="' . intval($this->options->commentsPostTimeout / (24 * 3600)) . '" id="commentsPost-commentsPostTimeout" />
            <label for="commentsPost-commentsPostTimeout">'),
            'commentsPostIntervalEnable' => _t('Thời gian giới hạn giữa các lần bình luận từ cùng một IP là %s phút',
                '</label><input name="commentsPostInterval" type="text" class="text num text-s" value="' . round($this->options->commentsPostInterval / (60), 1) . '" id="commentsPost-commentsPostInterval" />
            <label for="commentsPost-commentsPostInterval">')
        ];        

        $commentsPostOptionsValue = [];
        if ($this->options->commentsRequireModeration) {
            $commentsPostOptionsValue[] = 'commentsRequireModeration';
        }

        if ($this->options->commentsWhitelist) {
            $commentsPostOptionsValue[] = 'commentsWhitelist';
        }

        if ($this->options->commentsRequireMail) {
            $commentsPostOptionsValue[] = 'commentsRequireMail';
        }

        if ($this->options->commentsRequireURL) {
            $commentsPostOptionsValue[] = 'commentsRequireURL';
        }

        if ($this->options->commentsCheckReferer) {
            $commentsPostOptionsValue[] = 'commentsCheckReferer';
        }

        if ($this->options->commentsAntiSpam) {
            $commentsPostOptionsValue[] = 'commentsAntiSpam';
        }

        if ($this->options->commentsAutoClose) {
            $commentsPostOptionsValue[] = 'commentsAutoClose';
        }

        if ($this->options->commentsPostIntervalEnable) {
            $commentsPostOptionsValue[] = 'commentsPostIntervalEnable';
        }

        $commentsPost = new Form\Element\Checkbox(
            'commentsPost',
            $commentsPostOptions,
            $commentsPostOptionsValue,
            _t('Gửi bình luận')
        );
        $form->addInput($commentsPost->multiMode());
        
        /** Cho phép sử dụng thẻ và thuộc tính HTML */
        $commentsHTMLTagAllowed = new Form\Element\Textarea(
            'commentsHTMLTagAllowed',
            null,
            $this->options->commentsHTMLTagAllowed,
            _t('Cho phép sử dụng thẻ và thuộc tính HTML'),
            _t('Mặc định, bình luận của người dùng không được phép sử dụng bất kỳ thẻ HTML nào. Bạn có thể chỉ định các thẻ HTML được phép sử dụng ở đây.') . '<br />'
            . _t('Ví dụ: %s', '<code>&lt;a href=&quot;&quot;&gt; &lt;img src=&quot;&quot;&gt; &lt;blockquote&gt;</code>')
        );
        $commentsHTMLTagAllowed->input->setAttribute('class', 'mono');
        $form->addInput($commentsHTMLTagAllowed);
        
        /** Nút gửi */
        $submit = new Form\Element\Submit('submit', null, _t('Lưu cài đặt'));
        $submit->input->setAttribute('class', 'btn primary');
        $form->addItem($submit);        

        return $form;
    }

    /**
     * 绑定动作
     *
     * @access public
     * @return void
     */
    public function action()
    {
        $this->user->pass('administrator');
        $this->security->protect();
        $this->on($this->request->isPost())->updateDiscussionSettings();
        $this->response->redirect($this->options->adminUrl);
    }
}
