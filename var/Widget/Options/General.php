<?php

namespace Widget\Options;

use Typecho\Db\Exception;
use Typecho\I18n\GetText;
use Typecho\Widget\Helper\Form;
use Widget\ActionInterface;
use Widget\Base\Options;
use Widget\Notice;

if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * 基本设置组件
 *
 * @author qining
 * @category typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class General extends Options implements ActionInterface
{
    /**
     * 检查是否在语言列表中
     *
     * @param string $lang
     * @return bool
     */
    public function checkLang(string $lang): bool
    {
        $langs = self::getLangs();
        return isset($langs[$lang]);
    }

    /**
     * 获取语言列表
     *
     * @return array
     */
    public static function getLangs(): array
    {
        $dir = defined('__TYPECHO_LANG_DIR__') ? __TYPECHO_LANG_DIR__ : __TYPECHO_ROOT_DIR__ . '/usr/langs';
        $files = glob($dir . '/*.mo');
        $langs = ['zh_CN' => '简体中文'];

        if (!empty($files)) {
            foreach ($files as $file) {
                $getText = new GetText($file, false);
                [$name] = explode('.', basename($file));
                $title = $getText->translate('lang', $count);
                $langs[$name] = $count > - 1 ? $title : $name;
            }

            ksort($langs);
        }

        return $langs;
    }

    /**
     * 过滤掉可执行的后缀名
     *
     * @param string $ext
     * @return boolean
     */
    public function removeShell(string $ext): bool
    {
        return !preg_match("/^(php|php4|php5|sh|asp|jsp|rb|py|pl|dll|exe|bat)$/i", $ext);
    }

    /**
     * 执行更新动作
     *
     * @throws Exception
     */
    public function updateGeneralSettings()
    {
        /** 验证格式 */
        if ($this->form()->validate()) {
            $this->response->goBack();
        }

        $settings = $this->request->from(
            'title',
            'description',
            'keywords',
            'allowRegister',
            'allowXmlRpc',
            'lang',
            'timezone'
        );
        $settings['attachmentTypes'] = $this->request->getArray('attachmentTypes');

        if (!defined('__TYPECHO_SITE_URL__')) {
            $settings['siteUrl'] = rtrim($this->request->siteUrl, '/');
        }

        $attachmentTypes = [];
        if ($this->isEnableByCheckbox($settings['attachmentTypes'], '@image@')) {
            $attachmentTypes[] = '@image@';
        }

        if ($this->isEnableByCheckbox($settings['attachmentTypes'], '@media@')) {
            $attachmentTypes[] = '@media@';
        }

        if ($this->isEnableByCheckbox($settings['attachmentTypes'], '@doc@')) {
            $attachmentTypes[] = '@doc@';
        }

        $attachmentTypesOther = $this->request->filter('trim', 'strtolower')->attachmentTypesOther;
        if ($this->isEnableByCheckbox($settings['attachmentTypes'], '@other@') && !empty($attachmentTypesOther)) {
            $types = implode(
                ',',
                array_filter(array_map('trim', explode(',', $attachmentTypesOther)), [$this, 'removeShell'])
            );

            if (!empty($types)) {
                $attachmentTypes[] = $types;
            }
        }

        $settings['attachmentTypes'] = implode(',', $attachmentTypes);
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
        $form = new Form($this->security->getIndex('/action/options-general'), Form::POST_METHOD);

        /** 站点名称 */
        $title = new Form\Element\Text('title', null, $this->options->title, _t('Tên trang web'), _t('Tên của trang web sẽ hiển thị trong tiêu đề của trang.'));
$title->input->setAttribute('class', 'w-100');
$form->addInput($title->addRule('required', _t('Vui lòng nhập tên trang web'))
    ->addRule('xssCheck', _t('Vui lòng không sử dụng ký tự đặc biệt trong tên trang web')));


        /** 站点地址 */
        if (!defined('__TYPECHO_SITE_URL__')) {
            $siteUrl = new Form\Element\Text(
                'siteUrl',
                null,
                $this->options->originalSiteUrl,
                _t('Địa chỉ trang web'),
                _t('Địa chỉ trang web chủ yếu được sử dụng để tạo liên kết cố định cho nội dung.')
                . ($this->options->originalSiteUrl == $this->options->rootUrl ?
                    '' : '</p><p class="message notice mono">'
                    . _t('Địa chỉ hiện tại <strong>%s</strong> không khớp với giá trị được thiết lập trên', $this->options->rootUrl))
            );
            $siteUrl->input->setAttribute('class', 'w-100 mono');
            $form->addInput($siteUrl->addRule('required', _t('Vui lòng nhập địa chỉ trang web'))
                ->addRule('url', _t('Vui lòng nhập một địa chỉ URL hợp lệ')));
        }        


        /** 站点描述 */
        $description = new Form\Element\Text(
            'description',
            null,
            $this->options->description,
            _t('Mô tả trang web'),
            _t('Mô tả trang web sẽ được hiển thị trong phần đầu của mã trang web.')
        );
        $form->addInput($description->addRule('xssCheck', _t('Vui lòng không sử dụng ký tự đặc biệt trong mô tả trang web')));
        
        /** Từ khóa */
        $keywords = new Form\Element\Text(
            'keywords',
            null,
            $this->options->keywords,
            _t('Từ khóa'),
            _t('Vui lòng phân tách nhiều từ khóa bằng dấu phẩy ",".')
        );
        $form->addInput($keywords->addRule('xssCheck', _t('Vui lòng không sử dụng ký tự đặc biệt trong từ khóa')));
        
        /** Đăng ký */
        $allowRegister = new Form\Element\Radio(
            'allowRegister',
            ['0' => _t('Không cho phép'), '1' => _t('Cho phép')],
            $this->options->allowRegister,
            _t('Cho phép đăng ký'),
            _t('Cho phép khách truy cập đăng ký vào trang web của bạn, người dùng đăng ký mặc định không có quyền ghi.')
        );
        $form->addInput($allowRegister);
        
        /** Giao diện XMLRPC */
        $allowXmlRpc = new Form\Element\Radio(
            'allowXmlRpc',
            ['0' => _t('Tắt'), '1' => _t('Chỉ tắt giao diện Pingback'), '2' => _t('Mở')],
            $this->options->allowXmlRpc,
            _t('Giao diện XMLRPC')
        );
        $form->addInput($allowXmlRpc);
        
        /** Mục ngôn ngữ */
        // hack Quét ngôn ngữ
        _t('lang');
        
        $langs = self::getLangs();
        
        if (count($langs) > 1) {
            $lang = new Form\Element\Select('lang', $langs, $this->options->lang, _t('Ngôn ngữ'));
            $form->addInput($lang->addRule([$this, 'checkLang'], _t('Gói ngôn ngữ được chọn không tồn tại')));
        }
        
        /** Múi giờ */
$timezoneList = [
    "0"      => _t('Giờ chuẩn Greenwich (GMT)'),
    "3600"   => _t('Giờ trung Âu - Amsterdam, Hà Lan, Pháp (GMT +1)'),
    "7200"   => _t('Giờ Đông Âu - Bucharest, Síp, Hy Lạp (GMT +2)'),
    "10800"  => _t('Giờ Moscow - Iraq, Ethiopia, Madagascar (GMT +3)'),
    "14400"  => _t('Giờ Tbilisi - Oman, Mauritania, Reunion (GMT +4)'),
    "18000"  => _t('Giờ New Delhi - Pakistan, Maldives (GMT +5)'),
    "21600"  => _t('Giờ Colombo - Bangladesh (GMT +6)'),
    "25200"  => _t('Giờ Bangkok, Jakarta - Campuchia, Indonesia, Lào (GMT +7)'),
    "28800"  => _t('Giờ Bắc Kinh - Hồng Kông, Singapore, Việt Nam (GMT +8)'),
    "32400"  => _t('Giờ Tokyo, Pyongyang - Micronesia, Palau (GMT +9)'),
    "36000"  => _t('Giờ Sydney, Guam - Tasmania, Papua New Guinea (GMT +10)'),
    "39600"  => _t('Quần đảo Solomon - Các đảo Cocos (GMT +11)'),
    "43200"  => _t('Giờ Wellington - New Zealand, Fiji (GMT +12)'),
    "-3600"  => _t('Quần đảo Faeroe - Azores, Guinea Bissau (GMT -1)'),
    "-7200"  => _t('Giờ Trung Đại - Greenland (GMT -2)'),
    "-10800" => _t('Buenos Aires - Uruguay, French Guiana (GMT -3)'),
    "-14400" => _t('Giờ Santiago, Brasília - Venezuela, Bolivia (GMT -4)'),
    "-18000" => _t('New York, Ottawa - Cuba, Colombia, Jamaica (GMT -5)'),
    "-21600" => _t('Giờ Mexico - Honduras, Guatemala, Costa Rica (GMT -6)'),
    "-25200" => _t('Giờ Denver, USA (GMT -7)'),
    "-28800" => _t('Giờ San Francisco, USA (GMT -8)'),
    "-32400" => _t('Giờ Alaska (GMT -9)'),
    "-36000" => _t('Quần đảo Hawaii (GMT -10)'),
    "-39600" => _t('Quần đảo Samoa (GMT -11)'),
    "-43200" => _t('Quần đảo Aniwetok (GMT -12)')
];

        $timezone = new Form\Element\Select('timezone', $timezoneList, $this->options->timezone, _t('时区'));
        $form->addInput($timezone);

        /** 扩展名 */
        $attachmentTypesOptionsResult = (null != trim($this->options->attachmentTypes)) ?
            array_map('trim', explode(',', $this->options->attachmentTypes)) : [];
        $attachmentTypesOptionsValue = [];

        if (in_array('@image@', $attachmentTypesOptionsResult)) {
            $attachmentTypesOptionsValue[] = '@image@';
        }

        if (in_array('@media@', $attachmentTypesOptionsResult)) {
            $attachmentTypesOptionsValue[] = '@media@';
        }

        if (in_array('@doc@', $attachmentTypesOptionsResult)) {
            $attachmentTypesOptionsValue[] = '@doc@';
        }

        $attachmentTypesOther = array_diff($attachmentTypesOptionsResult, $attachmentTypesOptionsValue);
        $attachmentTypesOtherValue = '';
        if (!empty($attachmentTypesOther)) {
            $attachmentTypesOptionsValue[] = '@other@';
            $attachmentTypesOtherValue = implode(',', $attachmentTypesOther);
        }

        $attachmentTypesOptions = [
            '@image@' => _t('Hình ảnh') . ' <code>(gif jpg jpeg png tiff bmp webp avif)</code>',
            '@media@' => _t('Tập tin đa phương tiện') . ' <code>(mp3 mp4 mov wmv wma rmvb rm avi flv ogg oga ogv)</code>',
            '@doc@'   => _t('Tập tin văn bản thông thường') . ' <code>(txt doc docx xls xlsx ppt pptx zip rar pdf)</code>',
            '@other@' => _t(
                'Định dạng khác %s',
                ' <input type="text" class="w-50 text-s mono" name="attachmentTypesOther" value="'
                . htmlspecialchars($attachmentTypesOtherValue) . '" />'
            ),
        ];
        
        $attachmentTypes = new Form\Element\Checkbox(
            'attachmentTypes',
            $attachmentTypesOptions,
            $attachmentTypesOptionsValue,
            _t('Loại tập tin được phép tải lên'),
            _t('Sử dụng dấu phẩy "," để phân tách các định dạng, ví dụ: %s', '<code>cpp, h, mak</code>')
        );
        $form->addInput($attachmentTypes->multiMode());
        
        /** Nút gửi */
        $submit = new Form\Element\Submit('submit', null, _t('Lưu cài đặt'));
        $submit->input->setAttribute('class', 'btn primary');
        $form->addItem($submit);
        
        return $form;        
    }

    /**
     * 绑定动作
     */
    public function action()
    {
        $this->user->pass('administrator');
        $this->security->protect();
        $this->on($this->request->isPost())->updateGeneralSettings();
        $this->response->redirect($this->options->adminUrl);
    }
}
