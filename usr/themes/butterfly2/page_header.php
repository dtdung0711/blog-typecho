<?php  $this->need('header_com.php'); ?>
<body style="zoom: 1;">
<div id="web_bg"></div>
<div class="page" id="body-wrap">
<?php if (is_array($this->options->beautifyBlock) && in_array('PageShowTopimg',$this->options->beautifyBlock)): ?>
<header class="not-home-page" id="page-header" style="background-image: url(<?php GetRandomThumbnailPost($this); ?>)">
    <div id="page-site-info">
        <h1 id="site-title">
        <?php $this->archiveTitle(array(
            'category'  =>  _t(' %s '),
            'search'    =>  _t('Bài viết chứa từ khóa %s'),
            'tag'       =>  _t('Bài viết trong thẻ %s'),
            'author'    =>  _t('Bài viết được đăng bởi %s')
        ), '', ''); ?>
    <?php if($this->user->hasLogin() && $this->is('page')):?>
    <a style="float: none;"  class="post-edit-link" href="<?php $this->options->adminUrl(); ?>write-page.php?cid=<?php echo $this->cid;?>" title="Chỉnh sửa" target="_blank"><i class="fas fa-pencil-alt"></i></a><?php endif;?>
        </h1>
    </div>
<?php else: ?>        
<header class="not-top-img" id="page-header">        
<?php endif; ?>
<?php  $this->need('public/nav.php'); ?>
</header>