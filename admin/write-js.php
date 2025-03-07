<?php if(!defined('__TYPECHO_ADMIN__')) exit; ?>
<?php \Typecho\Plugin::factory('admin/write-js.php')->write(); ?>
<?php \Widget\Metas\Tag\Cloud::alloc('sort=count&desc=1&limit=200')->to($tags); ?>

<script src="<?php $options->adminStaticUrl('js', 'timepicker.js'); ?>"></script>
<script src="<?php $options->adminStaticUrl('js', 'tokeninput.js'); ?>"></script>
<script>
$(document).ready(function() {
    // 日期时间控件
    $('#date').mask('9999-99-99 99:99').datetimepicker({
    currentText     :   '<?php _e('Bây giờ'); ?>',
    prevText        :   '<?php _e('Tháng trước'); ?>',
    nextText        :   '<?php _e('Tháng sau'); ?>',
    monthNames      :   ['<?php _e('Tháng một'); ?>', '<?php _e('Tháng hai'); ?>', '<?php _e('Tháng ba'); ?>', '<?php _e('Tháng tư'); ?>',
        '<?php _e('Tháng năm'); ?>', '<?php _e('Tháng sáu'); ?>', '<?php _e('Tháng bảy'); ?>', '<?php _e('Tháng tám'); ?>',
        '<?php _e('Tháng chín'); ?>', '<?php _e('Tháng mười'); ?>', '<?php _e('Tháng mười một'); ?>', '<?php _e('Tháng mười hai'); ?>'],
    dayNames        :   ['<?php _e('Chủ Nhật'); ?>', '<?php _e('Thứ Hai'); ?>', '<?php _e('Thứ Ba'); ?>',
        '<?php _e('Thứ Tư'); ?>', '<?php _e('Thứ Năm'); ?>', '<?php _e('Thứ Sáu'); ?>', '<?php _e('Thứ Bảy'); ?>'],
    dayNamesShort   :   ['<?php _e('CN'); ?>', '<?php _e('T2'); ?>', '<?php _e('T3'); ?>', '<?php _e('T4'); ?>',
        '<?php _e('T5'); ?>', '<?php _e('T6'); ?>', '<?php _e('T7'); ?>'],
    dayNamesMin     :   ['<?php _e('CN'); ?>', '<?php _e('T2'); ?>', '<?php _e('T3'); ?>', '<?php _e('T4'); ?>',
        '<?php _e('T5'); ?>', '<?php _e('T6'); ?>', '<?php _e('T7'); ?>'],
    closeText       :   '<?php _e('Hoàn thành'); ?>',
    timeOnlyTitle   :   '<?php _e('Chọn thời gian'); ?>',
    timeText        :   '<?php _e('Thời gian'); ?>',
    hourText        :   '<?php _e('Giờ'); ?>',
    amNames         :   ['<?php _e('Sáng'); ?>', 'A'],
    pmNames         :   ['<?php _e('Chiều'); ?>', 'P'],
    minuteText      :   '<?php _e('Phút'); ?>',
    secondText      :   '<?php _e('Giây'); ?>',

    dateFormat      :   'yy-mm-dd',
    timezone        :   <?php $options->timezone(); ?> / 60,
    hour            :   (new Date()).getHours(),
    minute          :   (new Date()).getMinutes()
});


    // 聚焦
    $('#title').select();

    // text 自动拉伸
    Typecho.editorResize('text', '<?php $security->index('/action/ajax?do=editorResize'); ?>');

    // tag autocomplete 提示
    var tags = $('#tags'), tagsPre = [];
    
    if (tags.length > 0) {
        var items = tags.val().split(','), result = [];
        for (var i = 0; i < items.length; i ++) {
            var tag = items[i];

            if (!tag) {
                continue;
            }

            tagsPre.push({
                id      :   tag,
                tags    :   tag
            });
        }

        tags.tokenInput(<?php 
        $data = array();
        while ($tags->next()) {
            $data[] = array(
                'id'    =>  $tags->name,
                'tags'  =>  $tags->name
            );
        }
        echo json_encode($data);
        ?>, {
            propertyToSearch:   'tags',
            tokenValue      :   'tags',
            searchDelay     :   0,
            preventDuplicates   :   true,
            animateDropdown :   false,
            hintText        :   '<?php _e('Nhập tên thẻ'); ?>',
noResultsText   :   '<?php _e('Thẻ này không tồn tại, nhấn Enter để tạo mới'); ?>',
            prePopulate     :   tagsPre,

            onResult        :   function (result, query, val) {
                if (!query) {
                    return result;
                }

                if (!result) {
                    result = [];
                }

                if (!result[0] || result[0]['id'] != query) {
                    result.unshift({
                        id      :   val,
                        tags    :   val
                    });
                }

                return result.slice(0, 5);
            }
        });

        // tag autocomplete 提示宽度设置
        $('#token-input-tags').focus(function() {
            var t = $('.token-input-dropdown'),
                offset = t.outerWidth() - t.width();
            t.width($('.token-input-list').outerWidth() - offset);
        });
    }

    // 缩略名自适应宽度
    var slug = $('#slug');

    if (slug.length > 0) {
        var wrap = $('<div />').css({
            'position'  :   'relative',
            'display'   :   'inline-block'
        }),
        justifySlug = $('<pre />').css({
            'display'   :   'block',
            'visibility':   'hidden',
            'height'    :   slug.height(),
            'padding'   :   '0 2px',
            'margin'    :   0
        }).insertAfter(slug.wrap(wrap).css({
            'left'      :   0,
            'top'       :   0,
            'minWidth'  :   '5px',
            'position'  :   'absolute',
            'width'     :   '100%'
        })), originalWidth = slug.width();

        function justifySlugWidth() {
            var val = slug.val();
            justifySlug.text(val.length > 0 ? val : '     ');
        }

        slug.bind('input propertychange', justifySlugWidth);
        justifySlugWidth();
    }

    // 原始的插入图片和文件
    Typecho.insertFileToEditor = function (file, url, isImage) {
        var textarea = $('#text'), sel = textarea.getSelection(),
            html = isImage ? '<img src="' + url + '" alt="' + file + '" />'
                : '<a href="' + url + '">' + file + '</a>',
            offset = (sel ? sel.start : 0) + html.length;

        textarea.replaceSelection(html);
        textarea.setSelection(offset, offset);
    };

    var submitted = false, form = $('form[name=write_post],form[name=write_page]').submit(function () {
        submitted = true;
    }), formAction = form.attr('action'),
        idInput = $('input[name=cid]'),
        cid = idInput.val(),
        draft = $('input[name=draft]'),
        draftId = draft.length > 0 ? draft.val() : 0,
        btnSave = $('#btn-save').removeAttr('name').removeAttr('value'),
        btnSubmit = $('#btn-submit').removeAttr('name').removeAttr('value'),
        btnPreview = $('#btn-preview'),
        doAction = $('<input type="hidden" name="do" value="publish" />').appendTo(form),
        locked = false,
        changed = false,
        autoSave = $('<span id="auto-save-message" class="left"></span>').prependTo('.submit'),
        lastSaveTime = null;

    $(':input', form).bind('input change', function (e) {
        var tagName = $(this).prop('tagName');

        if (tagName.match(/(input|textarea)/i) && e.type == 'change') {
            return;
        }

        changed = true;
    });

    form.bind('field', function () {
        changed = true;
    });

    // 发送保存请求
    function saveData(cb) {
        function callback(o) {
            lastSaveTime = o.time;
            cid = o.cid;
            draftId = o.draftId;
            idInput.val(cid);
            autoSave.text('<?php _e('Đã lưu'); ?>' + ' (' + o.time + ')').effect('highlight', 1000);
            locked = false;

            btnSave.removeAttr('disabled');
            btnPreview.removeAttr('disabled');

            if (!!cb) {
                cb(o)
            }
        }

        changed = false;
        btnSave.attr('disabled', 'disabled');
        btnPreview.attr('disabled', 'disabled');
        autoSave.text('<?php _e('Đang lưu'); ?>');

        if (typeof FormData !== 'undefined') {
            var data = new FormData(form.get(0));
            data.append('do', 'save');

            $.ajax({
                url: formAction,
                processData: false,
                contentType: false,
                type: 'POST',
                data: data,
                success: callback
            });
        } else {
            var data = form.serialize() + '&do=save';
            $.post(formAction, data, callback, 'json');
        }
    }

    // 计算夏令时偏移
    var dstOffset = (function () {
        var d = new Date(),
            jan = new Date(d.getFullYear(), 0, 1),
            jul = new Date(d.getFullYear(), 6, 1),
            stdOffset = Math.max(jan.getTimezoneOffset(), jul.getTimezoneOffset());

        return stdOffset - d.getTimezoneOffset();
    })();
    
    if (dstOffset > 0) {
        $('<input name="dst" type="hidden" />').appendTo(form).val(dstOffset);
    }

    // 时区
    $('<input name="timezone" type="hidden" />').appendTo(form).val(- (new Date).getTimezoneOffset() * 60);

    // 自动保存
<?php if ($options->autoSave): ?>
    var autoSaveOnce = !!cid;

    function autoSaveListener () {
        setInterval(function () {
            if (changed && !locked) {
                locked = true;
                saveData();
            }
        }, 10000);
    }

    if (autoSaveOnce) {
        autoSaveListener();
    }

    $('#text').bind('input propertychange', function () {
        if (!locked) {
            autoSave.text('<?php _e('Chưa lưu'); ?>' + (lastSaveTime ? ' (<?php _e('Thời gian lưu lần cuối'); ?>: ' + lastSaveTime + ')' : ''));
        }

        if (!autoSaveOnce) {
            autoSaveOnce = true;
            autoSaveListener();
        }
    });
<?php endif; ?>

    // 自动检测离开页
    $(window).bind('beforeunload', function () {
        if (changed && !submitted) {
            return '<?php _e('Nội dung đã được thay đổi nhưng chưa được lưu, bạn có chắc chắn muốn rời khỏi trang này không?'); ?>';
        }
    });

    // 预览功能
    var isFullScreen = false;

    function previewData(cid) {
        isFullScreen = $(document.body).hasClass('fullscreen');
        $(document.body).addClass('fullscreen preview');

        var frame = $('<iframe frameborder="0" class="preview-frame preview-loading"></iframe>')
            .attr('src', './preview.php?cid=' + cid)
            .attr('sandbox', 'allow-same-origin allow-scripts')
            .appendTo(document.body);

        frame.load(function () {
            frame.removeClass('preview-loading');
        });

        frame.height($(window).height() - 53);
    }

    function cancelPreview() {
        if (submitted) {
            return;
        }

        if (!isFullScreen) {
            $(document.body).removeClass('fullscreen');
        }

        $(document.body).removeClass('preview');
        $('.preview-frame').remove();
    };

    $('#btn-cancel-preview').click(cancelPreview);

    $(window).bind('message', function (e) {
        if (e.originalEvent.data == 'cancelPreview') {
            cancelPreview();
        }
    });

    btnPreview.click(function () {
        if (changed) {
            locked = true;

            if (confirm('<?php _e('Nội dung sau khi chỉnh sửa cần được lưu trước khi xem trước. Bạn có muốn lưu không?'); ?>')) {
                saveData(function (o) {
                    previewData(o.draftId);
                });
            } else {
                locked = false;
            }
        } else if (!!draftId) {
            previewData(draftId);
        } else if (!!cid) {
            previewData(cid);
        }
    });

    btnSave.click(function () {
        doAction.attr('value', 'save');
    });

    btnSubmit.click(function () {
        doAction.attr('value', 'publish');
    });

    // 控制选项和附件的切换
    var fileUploadInit = false;
    $('#edit-secondary .typecho-option-tabs li').click(function() {
        $('#edit-secondary .typecho-option-tabs li').removeClass('active');
        $(this).addClass('active');
        $(this).parents('#edit-secondary').find('.tab-content').addClass('hidden');
        
        var selected_tab = $(this).find('a').attr('href'),
            selected_el = $(selected_tab).removeClass('hidden');

        if (!fileUploadInit) {
            selected_el.trigger('init');
            fileUploadInit = true;
        }

        return false;
    });

    // 高级选项控制
    $('#advance-panel-btn').click(function() {
        $('#advance-panel').toggle();
        return false;
    });

    // 自动隐藏密码框
    $('#visibility').change(function () {
        var val = $(this).val(), password = $('#post-password');

        if ('password' == val) {
            password.removeClass('hidden');
        } else {
            password.addClass('hidden');
        }
    });
    
    // 草稿删除确认
    $('.edit-draft-notice a').click(function () {
        if (confirm('<?php _e('Bạn có chắc chắn muốn xóa bản nháp này không?'); ?>')) {
            window.location.href = $(this).attr('href');
        }

        return false;
    });
});
</script>

