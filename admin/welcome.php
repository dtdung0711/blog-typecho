<?php
include 'common.php';
include 'header.php';
include 'menu.php';
?>

<div class="main">
    <div class="body container">
        <?php include 'page-title.php'; ?>
        <div class="row typecho-page-main" role="main">
            <div class="col-mb-12">
                <div id="typecho-welcome" class="message">
                    <form action="<?php $options->adminUrl(); ?>" method="get">
                    <h3><?php _e('Chào mừng bạn đến với trang quản trị "%s": ', $options->title); ?></h3>
                    <ol>
                        <li><a class="operate-delete" href="<?php $options->adminUrl('profile.php#change-password'); ?>"><?php _e('Rất khuyến khích bạn thay đổi mật khẩu mặc định của bạn'); ?></a></li>
                        <?php if($user->pass('contributor', true)): ?>
                        <li><a href="<?php $options->adminUrl('write-post.php'); ?>"><?php _e('Viết bài đăng đầu tiên'); ?></a></li>
                        <li><a href="<?php $options->siteUrl(); ?>"><?php $user->pass('administrator', true) ? _e('Xem trang web của tôi') : _e('Xem trang web'); ?></a></li>
                        <?php else: ?>
                        <li><a href="<?php $options->siteUrl(); ?>"><?php _e('Xem trang web'); ?></a></li>
                        <?php endif; ?>
                    </ol>
                    <p><button type="submit" class="btn primary"><?php _e('Hãy cho tôi bắt đầu ngay &raquo;'); ?></button></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include 'copyright.php';
include 'common-js.php';
include 'footer.php';
?>
