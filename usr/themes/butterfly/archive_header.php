<?php  $this->need('header_com.php'); ?>
<style>#body-wrap {min-height: 0;}</style>
<div id="web_bg"></div>
<?php require_once('public/rightside.php');?>
<div class="page" id="body-wrap">
<header class="not-home-page" id="page-header"  style="background-image: url(<?php if ($this->is('page')){GetRandomThumbnailPost($this);}?>)">
         <div id="page-site-info"><h1 id="site-title"><?php $this->archiveTitle(array(
            'category'  =>  _t(' %s '),
            'search'    =>  _t('Bài viết chứa từ khóa %s'),
            'tag'       =>  _t('Bài viết dưới thẻ %s'),
            'author'    =>  _t('Bài viết được đăng bởi %s')
        ), '', ''); ?></h1></div>
<nav id="nav" class="show" >
    <span id="blog_name">
        <a id="site-name" href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title() ?></a>
    </span>
        <div id="menus">
        <div id="search-button">
            <a class="site-page social-icon search">
            <i class="fas fa-search fa-fw"></i>
            <?php if (is_array($this->options->beautifyBlock) && in_array('showNoAlertSearch',$this->options->beautifyBlock)): ?>
                <form method="post" action="<?php $this->options->siteUrl(); ?>" role="search" id="dSearch">
                    <input type="text" placeholder="Tìm kiếm" id="dSearchIn" name="s" required="required">
                </form>
            <?php else: ?>
                <span> Tìm kiếm</span>
            <?php endif ?>
            </a> 
        </div>
        <div id="toggle-menu"><a class="site-page"><i class="fas fa-bars fa-fw"></i></a></div>
                <div class="menus_items">
                    <div class="menus_item">
                       <a class="site-page" href="<?php $this->options->siteUrl(); ?>">
                       <li class="fa-fw fas fa-home"></li><?php _e('Trang chủ'); ?></a>
                    </div>
                    <?php $this->widget('Widget_Contents_Page_List')->to($pages); ?>
                    <?php while($pages->next()): ?>
                    <div class="menus_item">
                    <a class="site-page" href="<?php $pages->permalink(); ?>" title="<?php $pages->title(); ?>">
                        <?php if($this->is($pages->title == "Liên kết bạn bè")){
                            echo"<i class='fa-fw fas fa-link'></i>";
                        }
                        elseif($this->is($pages->title == "Giới thiệu")){
                             echo"<li class='fa-fw fas fa-user'></li>";
                        }
                        elseif($this->is($pages->title=="Lời nhắn")){
                             echo"<i class='fa-fw fas fa-comment-dots'></i>";
                        }
                        elseif($this->is($pages->title=="Lưu trữ")){
                             echo"<i class='fa-fw fas fa-archive'></i>";
                        }
                        elseif($this->is($pages->title=="Thẻ")){
                             echo"<i class='fa-fw fas fa-tags'></i>";
                        }
                        elseif($this->is($pages->title=="Danh mục")){
                             echo"<i class='fa-fw fas fa-folder-open'></i>";
                        }elseif($this->is($pages->title=="Bảng lời nhắn")){
                             echo"<i class='fa-fw fa fa-comment-dots'></i>";
                        }else{
                            echo"<i class='fa-fw fa fa-coffee'></i>";
                        }                        
                        ?>  
                        <?php $pages->title(); ?>
                        </a>
                    </div>  
                <?php endwhile; ?>
            </div>
            </div>
        </div> 
    </nav>
</header>