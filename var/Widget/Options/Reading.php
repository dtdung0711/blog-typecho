<?php

namespace Widget\Options;

use Typecho\Db\Exception;
use Typecho\Plugin;
use Typecho\Widget\Helper\Form;
use Widget\Notice;

if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * 文章阅读设置组件
 *
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Reading extends Permalink
{
    /**
     * 执行更新动作
     *
     * @throws Exception
     */
    public function updateReadingSettings()
    {
        /** 验证格式  */
        if ($this->form()->validate()) {
            $this->response->goBack();
        }

        $settings = $this->request->from(
            'postDateFormat',
            'frontPage',
            'frontArchive',
            'pageSize',
            'postsListSize',
            'feedFullText'
        );

        if (
            'page' == $settings['frontPage'] && isset($this->request->frontPagePage) &&
            $this->db->fetchRow($this->db->select('cid')
                ->from('table.contents')->where('type = ?', 'page')
                ->where('status = ?', 'publish')->where('created < ?', $this->options->time)
                ->where('cid = ?', $pageId = intval($this->request->frontPagePage)))
        ) {
            $settings['frontPage'] = 'page:' . $pageId;
        } elseif (
            'file' == $settings['frontPage'] && isset($this->request->frontPageFile) &&
            file_exists(__TYPECHO_ROOT_DIR__ . '/' . __TYPECHO_THEME_DIR__ . '/' . $this->options->theme . '/' .
                ($file = trim($this->request->frontPageFile, " ./\\")))
        ) {
            $settings['frontPage'] = 'file:' . $file;
        } else {
            $settings['frontPage'] = 'recent';
        }

        if ('recent' != $settings['frontPage']) {
            $settings['frontArchive'] = empty($settings['frontArchive']) ? 0 : 1;
            if ($settings['frontArchive']) {
                $routingTable = $this->options->routingTable;
                $routingTable['archive']['url'] = '/' . ltrim($this->encodeRule($this->request->archivePattern), '/');
                $routingTable['archive_page']['url'] = rtrim($routingTable['archive']['url'], '/')
                    . '/page/[page:digital]/';

                if (isset($routingTable[0])) {
                    unset($routingTable[0]);
                }

                $settings['routingTable'] = serialize($routingTable);
            }
        } else {
            $settings['frontArchive'] = 0;
        }

        foreach ($settings as $name => $value) {
            $this->update(['value' => $value], $this->db->sql()->where('name = ?', $name));
        }

        Notice::alloc()->set(_t("Cài đặt đã được lưu"), 'success');
        $this->response->goBack();
    }

    /**
 * Xuất cấu trúc biểu mẫu
 *
 * @return Form
 */
public function form(): Form
{
    /** Xây dựng biểu mẫu */
    $form = new Form($this->security->getIndex('/action/options-reading'), Form::POST_METHOD);

    /** Định dạng ngày bài viết */
    $postDateFormat = new Form\Element\Text(
        'postDateFormat',
        null,
        $this->options->postDateFormat,
        _t('Định dạng ngày bài viết'),
        _t('Định dạng này được sử dụng để chỉ định định dạng mặc định của ngày được hiển thị trong lưu trữ bài viết.') . '<br />'
        . _t('Trong một số chủ đề, định dạng này có thể không hoạt động vì tác giả chủ đề có thể tùy chỉnh định dạng ngày.') . '<br />'
        . _t('Vui lòng tham khảo <a href="https://www.php.net/manual/zh/function.date.php">Cú pháp định dạng ngày của PHP</a>.')
    );
    $postDateFormat->input->setAttribute('class', 'w-40 mono');
    $form->addInput($postDateFormat->addRule('xssCheck', _t('Vui lòng không sử dụng ký tự đặc biệt trong định dạng ngày')));

    // Trang chính
    $frontPageParts = explode(':', $this->options->frontPage);
    $frontPageType = $frontPageParts[0];
    $frontPageValue = count($frontPageParts) > 1 ? $frontPageParts[1] : '';

    $frontPageOptions = [
        'recent' => _t('Hiển thị bài viết mới nhất')
    ];

    $frontPattern = '</label></span><span class="multiline front-archive%class%">'
    . '<input type="checkbox" id="frontArchive" name="frontArchive" value="1"'
    . ($this->options->frontArchive && 'recent' != $frontPageType ? ' checked' : '') . ' />
<label for="frontArchive">' . _t(
    'Đồng thời thay đổi đường dẫn trang danh sách bài viết thành %s',
    '<input type="text" name="archivePattern" class="w-20 mono" value="'
    . htmlspecialchars($this->decodeRule($this->options->routingTable['archive']['url'])) . '" />'
)
. '</label>';

        // Danh sách trang
$pages = $this->db->fetchAll($this->db->select('cid', 'title')
->from('table.contents')->where('type = ?', 'page')
->where('status = ?', 'publish')->where('created < ?', $this->options->time));

if (!empty($pages)) {
$pagesSelect = '<select name="frontPagePage" id="frontPage-frontPagePage">';
foreach ($pages as $page) {
    $selected = '';
    if ('page' == $frontPageType && $page['cid'] == $frontPageValue) {
        $selected = ' selected="true"';
    }

    $pagesSelect .= '<option value="' . $page['cid'] . '"' . $selected
        . '>' . $page['title'] . '</option>';
}
$pagesSelect .= '</select>';
$frontPageOptions['page'] = _t(
    'Sử dụng trang %s làm trang chính',
    '</label>' . $pagesSelect . '<label for="frontPage-frontPagePage">'
);
$selectedFrontPageType = 'page';
}


        // 自定义文件列表
        $files = glob($this->options->themeFile($this->options->theme, '*.php'));
        $filesSelect = '';

        foreach ($files as $file) {
            $info = Plugin::parseInfo($file);
            $file = basename($file);

            if ('index.php' != $file && 'index' == $info['title']) {
                $selected = '';
                if ('file' == $frontPageType && $file == $frontPageValue) {
                    $selected = ' selected="true"';
                }

                $filesSelect .= '<option value="' . $file . '"' . $selected
                    . '>' . $file . '</option>';
            }
        }

        if (!empty($filesSelect)) {
            $frontPageOptions['file'] = _t(
                'Gọi trực tiếp tệp mẫu %s',
                '</label><select name="frontPageFile" id="frontPage-frontPageFile">'
                . $filesSelect . '</select><label for="frontPage-frontPageFile">'
            );            
            $selectedFrontPageType = 'file';
        }

        if (isset($frontPageOptions[$frontPageType]) && 'recent' != $frontPageType && isset($selectedFrontPageType)) {
            $selectedFrontPageType = $frontPageType;
            $frontPattern = str_replace('%class%', '', $frontPattern);
        }

        if (isset($selectedFrontPageType)) {
            $frontPattern = str_replace('%class%', ' hidden', $frontPattern);
            $frontPageOptions[$selectedFrontPageType] .= $frontPattern;
        }

        $frontPage = new Form\Element\Radio('frontPage', $frontPageOptions, $frontPageType, _t('Trang chủ'));
        $form->addInput($frontPage->multiMode());

        /** 文章列表数目 */
        $postsListSize = new Form\Element\Text(
            'postsListSize',
            null,
            $this->options->postsListSize,
            _t('Số lượng bài viết trong danh sách'),
            _t('Số này được sử dụng để chỉ định số lượng bài viết hiển thị trong thanh bên.')
        );        
        $postsListSize->input->setAttribute('class', 'w-20');
        $form->addInput($postsListSize->addRule('isInteger', _t('请填入一个数字')));

        /** Số lượng bài viết trên mỗi trang */
$pageSize = new Form\Element\Text(
    'pageSize',
    null,
    $this->options->pageSize,
    _t('Số lượng bài viết trên mỗi trang'),
    _t('Số này được sử dụng để chỉ định số lượng bài viết hiển thị trên mỗi trang khi xuất bản lưu trữ bài viết.')
);
$pageSize->input->setAttribute('class', 'w-20');
$form->addInput($pageSize->addRule('isInteger', _t('Vui lòng nhập một số nguyên')));

/** Xuất toàn bộ nội dung trong FEED */
$feedFullText = new Form\Element\Radio(
    'feedFullText',
    ['0' => _t('Chỉ xuất bản tóm tắt'), '1' => _t('Xuất bản toàn bộ nội dung')],
    $this->options->feedFullText,
    _t('Xuất bản toàn bộ nội dung trong FEED'),
    _t('Nếu bạn không muốn xuất toàn bộ nội dung bài viết trong FEED, vui lòng chọn tùy chọn "Chỉ xuất bản tóm tắt".') . '<br />'
    . _t('Nội dung tóm tắt được xác định bởi vị trí của dấu phân cách trong bài viết.')
);
$form->addInput($feedFullText);

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
        $this->on($this->request->isPost())->updateReadingSettings();
        $this->response->redirect($this->options->adminUrl);
    }
}
