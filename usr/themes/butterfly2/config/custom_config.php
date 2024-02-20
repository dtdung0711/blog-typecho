<?php
function themeConfig($form)
{
    ?>
    <link rel="stylesheet" href="<?php Helper::options()->themeUrl('css/themedash.css?v1.5.3'); ?>">
    <div class='set_toc'>
        <div class='mtoc'>
            <a href='#themeBackup'>Sao lưu và Khôi phục chủ đề</a>
<a href='#cids'>Đặt bài viết nổi bật và phần công cộng</a>
<a href='#pjax'>Thiết lập pjax</a>
<a href='#friends'>Thiết lập liên kết bạn bè</a>
<a href='#reward'>Chức năng thưởng</a>
<a href='#aside'>Thiết lập hiển thị thanh bên</a>
<a href='#beautifyBlock'>Tùy chọn làm đẹp</a>
<a href='#ShowLive2D'>Thiết lập Live2D</a>
<a href='#otherCustom'>Nội dung tùy chỉnh khác</a>
<a href='#CustomColor'>Màu sắc tùy chỉnh</a>
<a href='#NULL' id='point'>Quay lại điểm neo khi lưu cài đặt lần trước</a>
        </div>
    </div>
    <form class="protected" action="?butterflybf" method="post" id="themeBackup">
        <input type="submit" name="type" class="btn btn-s" value="Sao lưu dữ liệu chủ đề" />&nbsp;&nbsp;<input type="submit" name="type"
            class="btn btn-s" value="Khôi phục dữ liệu chủ đề" />&nbsp;&nbsp;<input type="submit" name="type" class="btn btn-s"
            value="Xóa dữ liệu sao lưu" />
    </form>
    <script src='https://cdn.staticfile.org/jquery/1.10.2/jquery.min.js'></script>
    <script src="<?php Helper::options()->themeUrl('js/themecustom.js?v1.5.3'); ?>"></script>
    <script src='https://static.wehao.org/postdomai.js'></script>
    <?php
    $sticky_cids = new Typecho_Widget_Helper_Form_Element_Text('sticky_cids', NULL, NULL, 'Các cid bài viết nổi bật', '<div style="font-family:arial; background:#E8EFD1; padding:8px">Nhập theo thứ tự, vui lòng sử dụng dấu phẩy hoặc khoảng trắng nửa chiều để phân tách cid</div>');
    $sticky_cids->setAttribute('id', 'cids');
    $form->addInput($sticky_cids);

    $slide_cids = new Typecho_Widget_Helper_Form_Element_Text('slide_cids', NULL, NULL, 'Các cid bài viết trong trang chủ', 'Tự động bật, nhập cách như trên, <b style="color:red">Lưu ý: Nhập sai cid có thể gây lỗi trang</b>');
    $form->addInput($slide_cids);

    $StaticFile = new Typecho_Widget_Helper_Form_Element_Select(
        'StaticFile',
        array(
            'CDN' => 'Tải tài nguyên tĩnh từ CDN (mặc định)',
            'local' => 'Tải tài nguyên tĩnh từ máy chủ local',
        ),
        'CDN',
        'Cách tải tài nguyên tĩnh cho blog',
        'Giới thiệu: Bật khi máy chủ mạng không có hoặc CDN bị lỗi<br>
         Tải tài nguyên tĩnh của blog như js, css, hình ảnh từ máy chủ (có thể tăng mức tiêu thụ băng thông máy chủ một chút)<br>
         Lưu ý: Bạn cần <a href="https://github.com/wehaox/Typecho-Butterfly/releases/download/1.7.7/static-23.11.zip">tải xuống</a> phiên bản tài nguyên tĩnh tương ứng và giải nén trực tiếp vào thư mục gốc của chủ đề<br>
         Tệp này được sử dụng chung với tùy chọn CDN tùy chỉnh bên dưới'
    );
    $form->addInput($StaticFile->multiMode());

    $CDNURL = new Typecho_Widget_Helper_Form_Element_Text(
    'CDNURL',
    NULL,
    NULL,
    'URL CDN Tùy chỉnh (do @origami-tech cung cấp)',
    'Bạn cần chọn cách tải tài nguyên tĩnh của blog là từ CDN để tùy chọn này có hiệu lực và <b>local load > URL CDN tùy chỉnh > nguồn jsdelivr</b><br>
    Lưu ý: Bạn cần <a href="https://github.com/wehaox/Typecho-Butterfly/releases/download/1.7.7/static-23.11.zip">tải xuống</a> và giải nén tài nguyên tĩnh vào CDN trước<br>
    Quy tắc điền liên kết: Điền vào thư mục cha của thư mục static, không cần dấu / cuối cùng, ví dụ: https://pub-gcdn.starsdust.cn/libs/butterfly '
);
    $form->addInput($CDNURL);

    $jsdelivrLink = new Typecho_Widget_Helper_Form_Element_Select(
    'jsdelivrLink',
    array(
        'cdn.jsdelivr.net' => 'Nguồn mặc định của jsdelivr',
        'gcore.jsdelivr.net' => 'Nguồn gcore',
        'fastly.jsdelivr.net' => 'Nguồn fastly',
        'raw.fastgit.org' => 'Nguồn fastgit',
    ),
    'gcore.jsdelivr.net',
    'Chuyển đổi nguồn CDN được cung cấp bởi jsdelivr (mặc định sử dụng nguồn gcore)',
    'Cần mở cách tải tài nguyên tĩnh từ CDN ở phía trên'
);
    $form->addInput($jsdelivrLink->multiMode());

    $NewTabLink = new Typecho_Widget_Helper_Form_Element_Select(
    'NewTabLink',
    array(
        'on' => 'Bật (mặc định)',
        'off' => 'Tắt',
    ),
    'on',
    'Có bật chế độ mở liên kết ngoài trong tab mới hay không',
    'Giới thiệu: Liên kết không thuộc trang web sẽ mở trong tab mới'
);
    $form->addInput($NewTabLink->multiMode());

    $showFramework = new Typecho_Widget_Helper_Form_Element_Select(
    'showFramework',
    array(
        'on' => 'Bật (mặc định)',
        'off' => 'Tắt',
    ),
    'on',
    'Có hiển thị khung và chủ đề của blog ở phía dưới không',
    'Giới thiệu: Nếu bạn là người mới và muốn tự chỉnh sửa tên chủ đề, việc đó có thể gây ra thông báo vi phạm bản quyền. Bạn có thể tắt chức năng này và chúng tôi hy vọng bạn <b>tôn trọng chủ đề này</b>'
);
    $form->addInput($showFramework->multiMode());

    $Defend = new Typecho_Widget_Helper_Form_Element_Select(
    'Defend',
    array('off' => 'Tắt (mặc định)', 'on' => 'Bật'),
    'off',
    'Có bật chế độ bảo trì trang web hoặc truy cập bằng mật khẩu không',
    'Giới thiệu: Nếu bạn để trống mật khẩu ở phía dưới, trang web sẽ hiển thị chế độ bảo trì, ngược lại nó sẽ yêu cầu nhập mật khẩu để truy cập, người dùng đăng nhập sẽ không bị hạn chế'
);
$form->addInput($Defend->multiMode());

$ThemePassword = new Typecho_Widget_Helper_Form_Element_Text('ThemePassword', NULL, NULL, _t('Mật khẩu truy cập toàn bộ trang web (không bắt buộc)'), _t('Nhập mật khẩu để truy cập trang web, <b>cần phải bật chế độ bảo trì trang web hoặc truy cập bằng mật khẩu ở phía trên</b>'));
$form->addInput($ThemePassword);

$NoQQ = new Typecho_Widget_Helper_Form_Element_Select(
    'NoQQ',
    array('off' => 'Tắt (mặc định)', 'on' => 'Bật'),
    'off',
    'Có cấm truy cập trang web bằng ứng dụng di động QQ không',
    'Giới thiệu: Cấm truy cập từ ứng dụng QQ trên điện thoại di động'
);
    $form->addInput($NoQQ->multiMode());

    $SiteLogo = new Typecho_Widget_Helper_Form_Element_Text('SiteLogo', NULL, NULL, _t('Thiết lập tên trang web là logo hình ảnh (không bắt buộc)'), _t('Khi bạn thiết lập mục này, tên trang web sẽ không hiển thị ở góc trên bên trái của thanh điều hướng, sử dụng định dạng png'));
$form->addInput($SiteLogo);

$Sitefavicon = new Typecho_Widget_Helper_Form_Element_Text('Sitefavicon', NULL, NULL, _t('Biểu tượng trang web'), _t('Biểu tượng trang web, sử dụng định dạng png, kích thước khuyến nghị không vượt quá 64x64'));
$form->addInput($Sitefavicon);

$logoUrl = new Typecho_Widget_Helper_Form_Element_Text('logoUrl', NULL, _t('#null'), _t('Hình ảnh của tác giả'), _t('Nhập địa chỉ hình ảnh vào đây, nó sẽ hiển thị ở phần tác giả bên phải của thanh bên'));
$form->addInput($logoUrl);

$author_description = new Typecho_Widget_Helper_Form_Element_Text('author_description', NULL, _t('Mô tả của tác giả'), _t('Mô tả của tác giả'), _t('Nhập mô tả của trang web vào đây, nó sẽ hiển thị ở thông tin tác giả bên phải của thanh bên'));
$form->addInput($author_description);

$author_site_description = new Typecho_Widget_Helper_Form_Element_Text('author_site_description', NULL, _t('Trang web cá nhân'), _t('Mô tả liên kết của tác giả'), _t('Mô tả liên kết của tác giả'));
$form->addInput($author_site_description);

$author_site = new Typecho_Widget_Helper_Form_Element_Text('author_site', NULL, _t('#null'), _t('Liên kết của tác giả'), _t('Nhập liên kết của tác giả vào đây, nó sẽ hiển thị trên trang web cá nhân của tác giả bên phải của thanh bên'));
$form->addInput($author_site);

$author_bottom = new Typecho_Widget_Helper_Form_Element_Textarea('author_bottom', NULL, _t(''), _t('Nội dung dưới cùng của thông tin tác giả bên cạnh (không bắt buộc)'), _t('Nhập mã HTML vào đây, nó sẽ hiển thị dưới cùng của thông tin tác giả bên phải của thanh bên'));
$form->addInput($author_bottom);

$announcement = new Typecho_Widget_Helper_Form_Element_Textarea('announcement', NULL, _t('Đây là thông báo<br>'), _t('Thông báo'), _t('Nhập thông báo vào đây, nó sẽ hiển thị ở thông báo bên phải của thanh bên, sử dụng cú pháp HTML'));
$form->addInput($announcement);

$AD = new Typecho_Widget_Helper_Form_Element_Textarea('AD', NULL, NULL, _t('Quảng cáo (do @yzl3014 cung cấp)'), _t('Nhập quảng cáo vào đây, sau đó nó sẽ tự động hiển thị dưới phần thông báo trong thanh bên, hỗ trợ HTML'));
$form->addInput($AD);

$headerimg = new Typecho_Widget_Helper_Form_Element_Text('headerimg', NULL, _t('https://s2.loli.net/2023/01/18/bIJTVaR3MLPzcZ7.jpg'), _t('Hình ảnh banner trang chủ'), _t('Nhập đường dẫn hình ảnh của trang chủ vào đây'));
$form->addInput($headerimg);

$buildtime = new Typecho_Widget_Helper_Form_Element_Text('buildtime', NULL, _t('2021/04/05'), _t('Ngày xây dựng trang web'), _t('Nhập ngày xây dựng trang web theo định dạng trong ô nhập'));
$form->addInput($buildtime);

$outoftime = new Typecho_Widget_Helper_Form_Element_Text('outoftime', NULL, _t('15'), _t('Thông báo bài viết hết hạn'), _t('Thiết lập số ngày tối đa để thông báo bài viết hết hạn, mặc định là 15 ngày, bạn có thể thiết lập riêng cho từng bài viết trong quản lý bài viết nếu cần'));
$form->addInput($outoftime);

$archivelink = new Typecho_Widget_Helper_Form_Element_Text('archivelink', NULL, _t('#null'), _t('Liên kết của bài viết (lưu trữ)'), _t('Cần tạo trang độc lập và nhập liên kết thủ công vào đây'));
$form->addInput($archivelink);

$tagslink = new Typecho_Widget_Helper_Form_Element_Text('tagslink', NULL, _t('#null'), _t('Liên kết của thẻ'), _t('Cần tạo trang độc lập và nhập liên kết thủ công vào đây'));
$form->addInput($tagslink);

$categorylink = new Typecho_Widget_Helper_Form_Element_Text('categorylink', NULL, _t('#null'), _t('Liên kết của danh mục'), _t('Cần tạo trang độc lập và nhập liên kết thủ công vào đây'));
$form->addInput($categorylink);

    $CloseComments = new Typecho_Widget_Helper_Form_Element_Select(
    'CloseComments',
    array(
        'off' => 'Tắt (mặc định)',
        'on' => 'Bật',
    ),
    'off',
    'Đóng bình luận toàn bộ trang web',
    'Giới thiệu: Khi bật, tất cả bài viết sẽ không thể bình luận'
);
$form->addInput($CloseComments->multiMode());

$EnableCommentsLogin = new Typecho_Widget_Helper_Form_Element_Select(
    'EnableCommentsLogin',
    array(
        'off' => 'Tắt (mặc định)',
        'on' => 'Bật',
    ),
    'off',
    'Bật đăng nhập để bình luận cho người dùng',
    'Giới thiệu: Khi bật, nút đăng nhập sẽ được hiển thị trong khu vực bình luận'
);
    $form->addInput($EnableCommentsLogin->multiMode());

    $ShowRelatedPosts = new Typecho_Widget_Helper_Form_Element_Select(
    'ShowRelatedPosts',
    array(
        'off' => 'Tắt (mặc định)',
        'on' => 'Bật',
    ),
    'off',
    'Hiển thị bài viết liên quan trong nội dung bài viết',
    'Giới thiệu: Khi bật, các bài viết liên quan sẽ được hiển thị sau nội dung bài viết (dựa trên các thẻ bài viết, không phải mỗi bài viết đều hiển thị)'
);
$form->addInput($ShowRelatedPosts->multiMode());

$RelatedPostsNum = new Typecho_Widget_Helper_Form_Element_Select(
    'RelatedPostsNum',
    array(
        '3' => '3 bài viết (mặc định)',
        '6' => '6 bài viết',
    ),
    '3',
    'Số lượng bài viết liên quan hiển thị',
    'Giới thiệu: Hiển thị tối đa 3 hoặc 6 bài viết liên quan'
);
$form->addInput($RelatedPostsNum->multiMode());

$DefaultEncoding = new Typecho_Widget_Helper_Form_Element_Select(
    'DefaultEncoding',
    array(
        '2' => 'Tiếng Trung giản thể (mặc định)',
        '1' => 'Tiếng Trung phồn thể',
    ),
    '2',
    'Phông chữ mặc định cho blog',
    'Giới thiệu: Nếu bạn viết bài bằng tiếng Trung phồn thể, hãy chọn phông chữ này'
);
$form->addInput($DefaultEncoding->multiMode());


    $themeFontSize = new Typecho_Widget_Helper_Form_Element_Text('themeFontSize', NULL, _t(''), _t('Kích cỡ phông chữ mặc định'), _t('Nhập giá trị pixel, ví dụ 14px'));
$form->addInput($themeFontSize);

$GravatarSelect = new Typecho_Widget_Helper_Form_Element_Select(
    'GravatarSelect',
    array(
        "https://gravatar.loli.net/avatar/" => 'loli (mặc định)',
        'https://gravatar.helingqi.com/wavatar/' => '禾令奇',
        "https://sdn.geekzu.org/avatar/" => '极客族',
        "https://cdn.sep.cc/avatar/" => '九月的风',
        "https://gravatar.com/avatar/" => 'Nguồn chính thức (bị chặn)',
        "https://cravatar.cn/avatar/" => 'Nguồn chính thức Trung Quốc (đề xuất)',
    ),
    'loli',
    'Chọn nguồn Gravatar',
    'Giới thiệu: Chọn nguồn Gravatar cho các hình đại diện trong phần bình luận'
);
$GravatarSelect->setAttribute('id', 'gravatarlist');
$form->addInput($GravatarSelect->multiMode());

$baidustatistics = new Typecho_Widget_Helper_Form_Element_Text('baidustatistics', NULL, _t(''), _t('Thống kê trên Baidu'), _t('Chỉ cần phần https://hm.baidu.com/hm.js?xxxxxxxxxxxxxxxxxx'));
$form->addInput($baidustatistics);

$googleadsense = new Typecho_Widget_Helper_Form_Element_Text('googleadsense', NULL, _t(''), _t('Quảng cáo Google (chức năng thử nghiệm)'), _t('Nhập phần sau client, ví dụ ca-pub-xxxxx'));
$form->addInput($googleadsense);

$EnablePjax = new Typecho_Widget_Helper_Form_Element_Select(
    'EnablePjax',
    array(
        'off' => 'Tắt (mặc định)',
        'on' => 'Bật',
    ),
    'off',
    'Bật PJAX',
    'Giới thiệu: Load trang mà không làm mới lại, tăng tốc độ tải trang hiệu quả<br>
     Vui lòng xem trước <a href="https://blog.wehaox.com/archives/typecho-butterfly.html#cl-13">tài liệu sử dụng</a>'
);
$EnablePjax->setAttribute('id', 'pjax');
$form->addInput($EnablePjax->multiMode());

$PjaxCallBack = new Typecho_Widget_Helper_Form_Element_Textarea(
    'PjaxCallBack',
    NULL,
    NULL,
    'Hàm gọi lại PJAX (không bắt buộc)',
    'Dùng để giải quyết vấn đề mất tính năng của một số đoạn mã JS do bật PJAX (Nhập mã JS vào đây)'
);
$form->addInput($PjaxCallBack);

    /* Cài đặt liên kết bạn bè */
$friendset = new Typecho_Widget_Helper_Form_Element_Select(
    'friendset',
    array(
        '0' => 'Chế độ chủ đề',
        '1' => 'Chế độ plugin',
    ),
    '0',
    'Sử dụng plugin Link để cài đặt liên kết bạn bè (Cần tải về <a href="https://static.wehao.org/Links.zip">tại đây</a>)',
    'Giới thiệu: Dành cho người mới và người dùng không biết về lập trình, mặc định là đọc từ chủ đề để tránh lỗi'
);
$friendset->setAttribute('id', 'friends');
$form->addInput($friendset);

    $Friends = new Typecho_Widget_Helper_Form_Element_Textarea(
    'Friends',
    NULL,
    NULL,
    'Liên kết bạn bè (không bắt buộc)',
    'Giới thiệu: Dùng để nhập các liên kết bạn bè <br />
     Lưu ý: Cần tạo trang độc lập cho liên kết bạn bè, trường này mới có hiệu lực <br />
     Định dạng: Tên blog || Địa chỉ blog || Hình đại diện blog || Giới thiệu blog <br />
     Khác: Mỗi dòng một liên kết bạn bè'
);
$form->addInput($Friends);

$LazyLoad = new Typecho_Widget_Helper_Form_Element_Text(
    'LazyLoad',
    NULL,
    NULL,
    'Hình ảnh tải chậm toàn cầu (không bắt buộc)',
    'Giới thiệu: Dùng để thay đổi hình ảnh tải chậm toàn cầu <br />
     Định dạng: base64 hoặc URL của hình ảnh'
);
$form->addInput($LazyLoad);


$ShowGlobalReward = new Typecho_Widget_Helper_Form_Element_Select(
    'ShowGlobalReward',
    array(
        'off' => 'Tắt (mặc định)',
        'show' => 'Bật tính năng thưởng',
    ),
    'off',
    'Bật tính năng thưởng toàn cầu cho bài viết',
    'Giới thiệu: Bật tính năng này sẽ hiển thị các tùy chọn thưởng cho tất cả các bài viết'
);
$ShowGlobalReward->setAttribute('id', 'reward');
$form->addInput($ShowGlobalReward->multiMode());

    /* Cài đặt thưởng */
$RewardInfo = new Typecho_Widget_Helper_Form_Element_Textarea(
    'RewardInfo',
    NULL,
    _t('WeChat || https://cdn.jsdelivr.net/gh/wehaox/CDN@main/reward/wechat.jpg
Alipay || https://cdn.jsdelivr.net/gh/wehaox/CDN@main/reward/alipay.jpg'),
    'Thông tin thưởng (không bắt buộc)',
    'Lưu ý: Cần bật tính năng thưởng để trường này hiển thị <br />
     Định dạng: Tên thưởng || URL hình ảnh <br />Mỗi dòng một mục thưởng'
);
$form->addInput($RewardInfo);

    // Cài đặt hiển thị thanh bên
$sidebarBlock = new Typecho_Widget_Helper_Form_Element_Checkbox(
    'sidebarBlock',
    array(
        'ShowAuthorInfo' => _t('Hiển thị thông tin tác giả'),
        'ShowAnnounce' => _t('Hiển thị thông báo'),
        'ShowRecentPosts' => _t('Hiển thị bài viết gần đây'),
        'ShowRecentComments' => _t('Hiển thị bình luận gần đây'),
        'ShowCategory' => _t('Hiển thị danh mục'),
        'ShowTag' => _t('Hiển thị thẻ'),
        'ShowArchive' => _t('Hiển thị lưu trữ'),
        'ShowWebinfo' => _t('Hiển thị thông tin trang web'),
        'ShowMobileSide' => _t('Hiển thị thanh bên trên di động')
    ),
    array('ShowAuthorInfo', 'ShowAnnounce', 'ShowRecentPosts', 'ShowCategory', 'ShowTag', 'ShowArchive', 'ShowWebinfo'),
    _t('Hiển thị thanh bên')
);
$sidebarBlock->setAttribute('id', 'aside');
$form->addInput($sidebarBlock->multiMode());

// Hiển thị số người trực tuyến
$ShowOnlinePeople = new Typecho_Widget_Helper_Form_Element_Select(
    'ShowOnlinePeople',
    array(
        'on' => 'Bật',
        'off' => 'Tắt (mặc định)',
    ),
    'off',
    'Hiển thị số người trực tuyến',
    'Giới thiệu: Thống kê số người trực tuyến trong mô-đun tin tức trang web bên thanh bên, tránh lỗi 500 với một số máy chủ ảo không thể kích hoạt'
);
$form->addInput($ShowOnlinePeople->multiMode());

$sidderArchiveNum = new Typecho_Widget_Helper_Form_Element_Text('sidderArchiveNum', NULL, _t('5'), _t('Số dòng hiển thị trong lưu trữ bên thanh'), _t('Mặc định là 5'));
$form->addInput($sidderArchiveNum);

    // Cài đặt thanh bên của bài viết
$PostSidebarBlock = new Typecho_Widget_Helper_Form_Element_Checkbox(
    'PostSidebarBlock',
    array(
        'ShowAuthorInfo' => _t('Hiển thị thông tin tác giả'),
        'ShowAnnounce' => _t('Hiển thị thông báo'),
        'ShowRecentPosts' => _t('Hiển thị bài viết gần đây'),
        'ShowWebinfo' => _t('Hiển thị thông tin trang web'),
        'ShowOther' => _t('Hiển thị các mục khác'),
        'ShowWeiboHot' => _t('Hiển thị các từ khóa hot trên Weibo')
    ),
    array('ShowAuthorInfo', 'ShowAnnounce', 'ShowRecentPosts', 'ShowWebinfo'),
    _t('Hiển thị bên thanh bên trong bài viết'),
    _t('Giải thích: Cài đặt riêng cho thanh bên trong bài viết')
);
$form->addInput($PostSidebarBlock->multiMode());

// Cài đặt làm đẹp
$beautifyBlock = new Typecho_Widget_Helper_Form_Element_Checkbox(
    'beautifyBlock',
    array(
        'ShowBeautifyChange' => _t('Bật tính năng làm đẹp (dựa trên bản thân của Butterfly)'),
        'ShowColorTags' => _t('Bật thẻ màu sắc'),
        'ShowTopimg' => _t('Hiển thị hình ảnh ở đầu trang chủ'),
        'PostShowTopimg' => _t('Hiển thị hình ảnh đầu bài viết'),
        'PageShowTopimg' => _t('Hiển thị hình ảnh đầu trang độc lập'),
        'showLineNumber' => _t('Hiển thị số dòng trong khối mã'),
        'showSnackbar' => _t('Hiển thị hộp thoại chuyển đổi chủ đề và tiếng Trung và tiếng Hoa'),
        'showLazyloadBlur' => _t('Bật hiệu ứng mờ khi tải chậm'),
        'showButterflyClock' => _t('Bật hiển thị đồng hồ bên thanh bên (cần điền khóa và thời tiết vàng vào dưới)'),
        'showNoAlertSearch' => _t('Bật hộp tìm kiếm không cảnh báo'),
    ),
    array('ShowTopimg', 'PostShowTopimg', 'PageShowTopimg', 'showLineNumber', 'showSnackbar', 'showLazyloadBlur', 'showNoAlertSearch'),
    _t('Cài đặt làm đẹp')
);
    $beautifyBlock->setAttribute('id', 'beautifyBlock');
    $form->addInput($beautifyBlock->multiMode());

    // Vị trí bìa
$coverPosition = new Typecho_Widget_Helper_Form_Element_Select(
    'coverPosition',
    array(
        'left' => 'Bên trái',
        'cross' => 'Chéo (mặc định)',
        'right' => 'Bên phải',
    ),
    'cross',
    'Vị trí hiển thị bìa danh sách bài viết trang chủ',
    'Cá nhân vẫn cảm thấy chéo là tốt nhất'
);
$form->addInput($coverPosition->multiMode());

// Key thời tiết và đồng hồ
$qweather_key = new Typecho_Widget_Helper_Form_Element_Text('qweather_key', NULL, null, _t('Key thời tiết và đồng hồ'), _t('<a href="https://github.com/anzhiyu-c/hexo-butterfly-clock-anzhiyu/#安装">Nhận key theo hướng dẫn</a>'));
$gaud_map_key = new Typecho_Widget_Helper_Form_Element_Text('gaud_map_key', NULL, null, _t('Key dịch vụ web bản đồ Gaud Clock'), _t('Key được sử dụng trong thanh bên để hiển thị đồng hồ, tương tự như trên'));
$form->addInput($qweather_key);
$form->addInput($gaud_map_key); 

    // Hiển thị Live2D
$ShowLive2D = new Typecho_Widget_Helper_Form_Element_Select(
    'ShowLive2D',
    array(
        'off' => 'Tắt (mặc định)',
        "on" => 'Bật (mặc định GitHub)'
    ),
    'off',
    'Bật mô hình nhân vật Live2D (chỉ hiển thị theo mặc định GitHub và không hiển thị trên điện thoại di động)',
    'Giới thiệu: Sau khi kích hoạt, một nhân vật nhỏ sẽ xuất hiện ở góc dưới bên phải, tính năng này sử dụng cuộc gọi từ xa và không tốn nhiều tài nguyên'
);
$ShowLive2D->setAttribute('id', 'ShowLive2D');
$form->addInput($ShowLive2D->multiMode());

// Vị trí hộp thoại chuyển đổi
$SnackbarPosition = new Typecho_Widget_Helper_Form_Element_Select(
    'SnackbarPosition',
    array(
        'top-left' => 'Bên trái trên (mặc định)',
        'top-center' => 'Giữa trên',
        'top-right' => 'Bên phải trên',
        'bottom-left' => 'Bên trái dưới',
        'bottom-center' => 'Giữa dưới',
        'bottom-right' => 'Bên phải dưới',
    ),
    'top-left',
    'Vị trí của hộp thoại chuyển đổi chủ đề và tiếng Trung và tiếng Hoa',
    'Chọn một trong số các tùy chọn, cần bật hộp thoại chuyển đổi chủ đề và tiếng Trung và tiếng Hoa '
);
$form->addInput($SnackbarPosition->multiMode());

// Hiệu ứng chuột
$CursorEffects = new Typecho_Widget_Helper_Form_Element_Select(
    'CursorEffects',
    array(
        'off' => 'Tắt (mặc định)',
        'heart' => 'Hiệu ứng nhấp chuột: Trái tim',
        'fireworks' => 'Hiệu ứng pháo hoa',
    ),
    'off',
    'Chọn hiệu ứng nhấp chuột',
    'Giới thiệu: Dùng để chuyển đổi hiệu ứng nhấp chuột '
);
$form->addInput($CursorEffects->multiMode());

// Tiêu đề phụ tùy chỉnh
$CustomSubtitle = new Typecho_Widget_Helper_Form_Element_Text(
    'CustomSubtitle',
    NULL,
    NULL,
    'Tiêu đề phụ/trang chủ tùy chỉnh (không bắt buộc)',
    'Giới thiệu: Không điền sẽ sử dụng API một câu '
);
$form->addInput($CustomSubtitle);

$SubtitleLoop = new Typecho_Widget_Helper_Form_Element_Select(
    'SubtitleLoop',
    array(
        'true' => 'Bật vòng lặp (mặc định)',
        "false" => 'Tắt vòng lặp'
    ),
    'true',
    'Vòng lặp gõ phím tiêu đề phụ',
    'Giới thiệu: Bật vòng lặp gõ phím tiêu đề phụ ở trang chủ'
);
$form->addInput($SubtitleLoop->multiMode());

$EnableAutoHeaderLink = new Typecho_Widget_Helper_Form_Element_Select(
    'EnableAutoHeaderLink',
    array(
        'on' => 'Bật (mặc định)',
        "off" => 'Tắt'
    ),
    'on',
    'Tự động tạo liên kết trang chủ cho thanh đầu trang',
    'Giới thiệu: Nếu bạn muốn tùy chỉnh liên kết thanh đầu trang, bạn có thể chọn tắt mục này'
);
$form->addInput($EnableAutoHeaderLink->multiMode());

    // Liên kết đầu trang tùy chỉnh
$CustomHeaderLink = new Typecho_Widget_Helper_Form_Element_Textarea(
    'CustomHeaderLink',
    NULL,
    NULL,
    'Liên kết thanh đầu trang tùy chỉnh',
    'Giới thiệu: Hiện đang sử dụng định dạng HTML <b style="color:red">Lưu ý rằng nếu bạn tùy chỉnh hoàn toàn liên kết, hãy tắt mục trên</b>'
);
$CustomHeaderLink->setAttribute('id', 'otherCustom');
$form->addInput($CustomHeaderLink);

// Người dùng được xác thực tùy chỉnh
$CustomAuthenticated = new Typecho_Widget_Helper_Form_Element_Textarea(
    'CustomAuthenticated',
    NULL,
    NULL,
    'Người dùng được xác thực tùy chỉnh',
    'Giới thiệu: Tiêu đề chuyên biệt cho người dùng được xác thực trong phần bình luận <br>
     Định dạng: Email||Tiêu đề chứng nhận Ví dụ:<br> xxx@xxx.com||Chứng chỉ xxx<br>
    (Mỗi dòng một)'
);
$form->addInput($CustomAuthenticated);

    // 自定义css和js
    $CustomCSS = new Typecho_Widget_Helper_Form_Element_Textarea(
        'CustomCSS',
        NULL,
        NULL,
        'CSS tùy chỉnh (không bắt buộc)',
    'Giới thiệu: Vui lòng điền nội dung CSS tùy chỉnh, không cần thẻ style.'
    );
    $form->addInput($CustomCSS);

    $CustomScript = new Typecho_Widget_Helper_Form_Element_Textarea(
    'CustomScript',
    NULL,
    NULL,
    'Mã JS tùy chỉnh (không bắt buộc, vui lòng đọc phần giới thiệu dưới đây)',
    'Giới thiệu: Vui lòng điền nội dung JS tùy chỉnh, không cần thẻ <script>.<br />
     Không chuyên nghiệp xin đừng điền!'
);
$form->addInput($CustomScript);

$CustomHead = new Typecho_Widget_Helper_Form_Element_Textarea(
    'CustomHead',
    NULL,
    NULL,
    'Nội dung trong thẻ head tùy chỉnh',
    'Giới thiệu: Điền các thẻ <link> như cdn, mã số thống kê Baidu, vv'
);
$form->addInput($CustomHead);

$CustomBodyEnd = new Typecho_Widget_Helper_Form_Element_Textarea(
    'CustomBodyEnd',
    NULL,
    NULL,
    'Nội dung cuối thẻ body tùy chỉnh',
    'Giới thiệu: Điền các thẻ như <script></script> từ các CDN, vv'
);
$form->addInput($CustomBodyEnd);

$Customfooter = new Typecho_Widget_Helper_Form_Element_Textarea(
    'Customfooter',
    NULL,
    NULL,
    'Nội dung cuối trang (Footer) tùy chỉnh',
    'Giới thiệu: Thông tin cuối trang web, như số đăng ký, vv (có thể sử dụng html)'
);
$form->addInput($Customfooter);

$themeColor = new Typecho_Widget_Helper_Form_Element_Text('themeColor', NULL, 
_t('#eee'), _t('Màu chủ đề'), _t('Chủ yếu dành cho trình duyệt có thanh trạng thái ngập trong, mặc định là #eee'));
$themeColor->setAttribute('id', 'CustomColor');
$form->addInput($themeColor);

// Tùy chọn chế độ tối
$darkModeSelect = new Typecho_Widget_Helper_Form_Element_Select(
    'darkModeSelect',
    array(
        "1" => 'Luôn sáng',
        '2' => 'Theo hệ thống (mặc định)',
        '3' => 'Theo hệ thống và tự động tối theo thời gian',
        '4' => 'Luôn tối',
    ),
    '2',
    'Chế độ tối',
    'Giới thiệu: Nếu người dùng đã cài đặt chế độ màu trong góc dưới bên trái, mục này sẽ không hoạt động'
);
$form->addInput($darkModeSelect->multiMode());

$darkTime = new Typecho_Widget_Helper_Form_Element_Text('darkTime', NULL, 
_t('7-20'), _t('Thời gian tối tự động'), _t('Mặc định là 7-20, định dạng 24 giờ, nhập theo định dạng (7-20)'));
$form->addInput($darkTime);

// Tùy chỉnh màu sắc    
$EnableCustomColor = new Typecho_Widget_Helper_Form_Element_Select(
    'EnableCustomColor',
    array(
        "false" => 'Tắt (mặc định)',
        'true' => 'Bật'
    ),
    'false',
    'Bật tùy chỉnh màu chủ đề (chức năng thử nghiệm)',
    'Giới thiệu: Cần bật mục này dưới đây về màu sắc tùy chỉnh và mục bắt buộc về màu sắc'
);
$form->addInput($EnableCustomColor->multiMode());

$CustomColorMain = new Typecho_Widget_Helper_Form_Element_Text(
    'CustomColorMain',
    NULL,
    _t('#49b1f5'),
    'Màu chính tùy chỉnh của chủ đề',
    'Giới thiệu: Sử dụng định dạng hex hoặc tên màu, như #fff, trắng'
);
$form->addInput($CustomColorMain);

$CustomColorButtonBG = new Typecho_Widget_Helper_Form_Element_Text(
    'CustomColorButtonBG',
    NULL,
    _t('#49b1f5'),
    'Màu nền của nút tùy chỉnh',
    'Giới thiệu: Tương tự như trên'
);
$form->addInput($CustomColorButtonBG);

$CustomColorButtonHover = new Typecho_Widget_Helper_Form_Element_Text(
    'CustomColorButtonHover',
    NULL,
    _t('#ff7242'),
    'Màu khi di chuột qua nút tùy chỉnh',
    'Giới thiệu: Tương tự như trên'
);
$form->addInput($CustomColorButtonHover);

    // Tùy chọn màu sắc tùy chỉnh
$CustomColorSelection = new Typecho_Widget_Helper_Form_Element_Text(
    'CustomColorSelection',
    NULL,
    _t('#00c4b6'),
    'Màu sắc chọn văn bản tùy chỉnh',
    'Thông tin: Giống như các tùy chọn màu sắc ở trên'
);
$form->addInput($CustomColorSelection);
// Kết thúc tùy chọn màu sắc tùy chỉnh

// Cài đặt Captcha cho khu vực bình luận
$siteKey = new Typecho_Widget_Helper_Form_Element_Text(
    'siteKey',
    NULL,
    null,
    'Mã Site Key cho reCAPTCHA v2 trong khu vực bình luận:',
    '<a href="https://www.google.com/recaptcha/admin/create">Nhấn vào đây để lấy mã</a>'
);

$secretKey = new Typecho_Widget_Helper_Form_Element_Text('secretKey', NULL, null, _t('Mã Serect Key cho reCAPTCHA v2:'), _t('Nhập mã vào hai vị trí để kích hoạt reCAPTCHA trong khu vực bình luận'));
$form->addInput($siteKey);
$form->addInput($secretKey);

// Cài đặt Captcha hCaptcha cho khu vực bình luận
$hcaptchaSecretKey = new Typecho_Widget_Helper_Form_Element_Text(
    'hcaptchaSecretKey',
    NULL,
    null,
    '<hr> hCaptcha cho khu vực bình luận <br> Mã bí mật (sietkey) - Sử dụng nó như là mã bí mật để kiểm tra mã thông báo người dùng:',
    '<a href="https://dashboard.hcaptcha.com/welcome">Nhấn vào đây để lấy mã</a>'
);

$hcaptchaAPIKey = new Typecho_Widget_Helper_Form_Element_Text('hcaptchaAPIKey', NULL, null, _t('Mã API:'), _t('Nhập mã vào hai vị trí để kích hoạt hCaptcha trong khu vực bình luận'));

$form->addInput($hcaptchaSecretKey);
$form->addInput($hcaptchaAPIKey);




    $db = Typecho_Db::get();
    $sjdq = $db->fetchRow($db->select()->from('table.options')->where('name = ?', 'theme:butterfly'));
    $ysj = $sjdq['value'];
    if (isset($_POST['type'])) {
        if ($_POST["type"] == "Sao lưu dữ liệu chủ đề") {
            if ($db->fetchRow($db->select()->from('table.options')->where('name = ?', 'theme:butterflybf'))) {
                $update = $db->update('table.options')->rows(array('value' => $ysj))->where('name = ?', 'theme:butterflybf');
                $updateRows = $db->query($update);
                echo '<div class="tongzhi">Sao lưu đã được cập nhật. Vui lòng chờ đợi tự động làm mới! Nếu không thấy, vui lòng nhấp vào';

                ?>
<a href="<?php Helper::options()->adminUrl('options-theme.php'); ?>">đây</a></div>
                <script
                    language="JavaScript">window.setTimeout("location=\'<?php Helper::options()->adminUrl('options-theme.php'); ?>\'", 2500);</script>
                <?php
            } else {
                if ($ysj) {
                    $insert = $db->insert('table.options')->rows(array('name' => 'theme:butterflybf', 'user' => '0', 'value' => $ysj));
                    $insertId = $db->query($insert);
echo '<div class="tongzhi">Sao lưu đã hoàn tất. Vui lòng đợi để tự động làm mới! Nếu không đợi được, vui lòng nhấp vào';
                    ?>
                    <a href="<?php Helper::options()->adminUrl('options-theme.php'); ?>">đây</a></div>
                    <script
                        language="JavaScript">window.setTimeout("location=\'<?php Helper::options()->adminUrl('options-theme.php'); ?>\'", 2500);</script>
                    <?php
                }
            }
        }
        if ($_POST["type"] == "Khôi phục dữ liệu chủ đề") {
            if ($db->fetchRow($db->select()->from('table.options')->where('name = ?', 'theme:butterflybf'))) {
                $sjdub = $db->fetchRow($db->select()->from('table.options')->where('name = ?', 'theme:butterflybf'));
                $bsj = $sjdub['value'];
                $update = $db->update('table.options')->rows(array('value' => $bsj))->where('name = ?', 'theme:butterfly');
                $updateRows = $db->query($update);
echo '<div class="tongzhi">Phát hiện dữ liệu sao lưu chủ đề, đã khôi phục hoàn tất. Vui lòng đợi để tự động làm mới! Nếu không đợi được, vui lòng nhấp vào';
                ?>
                <a href="<?php Helper::options()->adminUrl('options-theme.php'); ?>">đây</a></div>
                <script
                    language="JavaScript">window.setTimeout("location=\'<?php Helper::options()->adminUrl('options-theme.php'); ?>\'", 2000);</script>
                <?php
            } else {
echo '<div class="tongzhi">Không có dữ liệu sao lưu chủ đề, không thể khôi phục!</div>';
            }
        }
        if ($_POST["type"] == "Xóa dữ liệu sao lưu") {
            if ($db->fetchRow($db->select()->from('table.options')->where('name = ?', 'theme:butterflybf'))) {
                $delete = $db->delete('table.options')->where('name = ?', 'theme:butterflybf');
                $deletedRows = $db->query($delete);
echo '<div class="tongzhi">Xóa thành công, vui lòng đợi để làm mới tự động, nếu không thì nhấp vào';
                ?>
                <a href="<?php Helper::options()->adminUrl('options-theme.php'); ?>">đây</a></div>
                <script
                    language="JavaScript">window.setTimeout("location=\'<?php Helper::options()->adminUrl('options-theme.php'); ?>\'", 2500);</script>
                <?php
            } else {
echo '<div class="tongzhi">Không cần xóa! Sao lưu không tồn tại!!!</div>';
            }
        }
    }
    // 结束
}

function themeFields($layout)
{
    $thumb = new Typecho_Widget_Helper_Form_Element_Text(
        'thumb',
        NULL,
        NULL,
        'Tùy chỉnh hình thu nhỏ bài viết',
        'Khi điền: Hình thu nhỏ bài viết sẽ hiển thị theo hình bạn điền vào <br>Không điền sẽ sử dụng hình mặc định'
    );
    $layout->addItem($thumb);

    $summaryContent = new Typecho_Widget_Helper_Form_Element_Textarea(
        'summaryContent',
        NULL,
        NULL,
        'Tùy chỉnh trích dẫn bài viết',
        'Không thích trích dẫn tự động? Hãy tùy chỉnh theo ý muốn!'
    );
    $layout->addItem($summaryContent);

    $desc = new Typecho_Widget_Helper_Form_Element_Text(
        'desc',
        NULL,
        NULL,
        'Mô tả SEO',
        'Dùng để điền mô tả SEO cho bài viết hoặc trang độc lập, nếu không điền thì không có mô tả'
    );
    $layout->addItem($desc);

    $keywords = new Typecho_Widget_Helper_Form_Element_Text(
        'keywords',
        NULL,
        NULL,
        'Từ khóa SEO',
        'Dùng để điền từ khóa SEO cho bài viết hoặc trang độc lập, nếu không điền thì không có từ khóa'
    );
    $layout->addItem($keywords);


    $showTimeWarning = new Typecho_Widget_Helper_Form_Element_Select(
        'showTimeWarning',
        array(
            'on' => 'Bật (mặc định)',
            'off' => 'Tắt'
        ),
        'on',
        'Có bật cảnh báo bài viết hết hạn không',
        'Dùng để thiết lập cảnh báo hết hạn cho bài viết hiện tại <br /> <b>Chỉ có tác dụng trong bài viết, không cần thay đổi trong trang độc lập</b> <br />'
    );
    $layout->addItem($showTimeWarning);

    $ShowReward = new Typecho_Widget_Helper_Form_Element_Select(
        'ShowReward',
        array(
            'off' => 'Tắt (mặc định)',
            'show' => 'Bật tiền thưởng',
        ),
        'off',
        'Có bật tiền thưởng bài viết không',
        'Giới thiệu: Bật tính năng này cần thêm hình ảnh mã QR trong cài đặt chủ đề'
    );
    $layout->addItem($ShowReward);
    $ShowToc = new Typecho_Widget_Helper_Form_Element_Select(
        'ShowToc',
        array(
            'show' => 'Bật (mặc định)',
            'off' => 'Tắt',
        ),
        'show',
        'Có hiển thị mục lục bài viết không',
        'Giới thiệu: Có thể một số bài viết không cần chức năng mục lục, mặc định là bật, thường không cần phải thiết lập'
    );
    $layout->addItem($ShowToc);

    $CopyRight = new Typecho_Widget_Helper_Form_Element_Select(
        'CopyRight',
        array(
            'on' => 'CC BY-NC-SA 4.0 (mặc định)',
            'off' => 'Không cho phép sao chép',
        ),
        'on',
        'Chú thích bản quyền bài viết',
        'Giới thiệu: Mặc định là CC BY-NC-SA 4.0'
    );
    $layout->addItem($CopyRight);

    $NoCover = new Typecho_Widget_Helper_Form_Element_Select(
        'NoCover',
        array(
            'on' => 'Hiển thị ảnh bìa',
            'off' => 'Không hiển thị ảnh bìa',
        ),
        'on',
        'Trang chủ có hiển thị ảnh bìa không',
        'Giới thiệu: Bài viết này có vẻ không cần ảnh bìa'
    );
    $layout->addItem($NoCover);
}

function themeInit($archive)
{
    if (Helper::options()->EnablePjax == "on") {
        Helper::options()->commentsAntiSpam = false;
    }
    if ($archive->is('single')) {
        $archive->content = createCatalog($archive->content);
        $archive->content = ParseCode($archive->content);
    }
    $loginStatus = $archive->widget('Widget_User')->hasLogin();
    if (Helper::options()->siteKey !== "" && Helper::options()->secretKey !== "" && !$loginStatus) {
        comments_filter($archive);
    }
    if (Helper::options()->hcaptchaSecretKey !== "" && Helper::options()->hcaptchaAPIKey !== "" && !$loginStatus) {
        hcaptcha_filter($archive);
    }
    if ($archive->is('index')) {
        // echo '<script src="'..'"></script>';        
    }
}
?>