<?php if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
} ?>
<?php $this->need('page_header.php'); ?>
<main class="layout" id="content-inner">
    <div class="recent-posts category_ui" id="recent-posts">
        <?php if ($this->have()): ?>
    	<?php while ($this->next()): ?>
           <div class="recent-post-item">
		   <?php if(noCover($this)): ?>  
        		<wehao class="post_cover">
             		<a href="<?php $this->permalink() ?>">
                	<img class="post-bg" data-lazy-src="<?php echo get_ArticleThumbnail($this);?>" src="<?php echo GetLazyLoad() ?>" onerror="this.onerror=null;this.src='<?php $this->options->themeUrl('img/404.jpg'); ?>'"></a>
        		</wehao>
    		<?php endif ?>
              <div class="recent-post-info<?php echo noCover($this) ? '' : ' no-cover'; ?>">
			    <a  class="article-title" href="<?php $this->permalink(); ?>"><?php $this->title(); ?></a>
			    <div class="article-meta-wrap">
			         <?php $this->sticky(); ?>
			   <span class="post-meta-date" style="display:none;">
			        <i class="far fa-calendar-alt"></i>
				    <?php _e('Xuất bản vào '); ?> <?php $this->date(); ?>
				    <time class="post-meta-date-created" datetime="<?php $this->date('c'); ?>">
				    </time>
				</span>
				<i class="fas fa-history"></i>
				<span class="article-meta-label"><?php _e('Cập nhật vào'); ?></span>
				  <?php echo date('Y-m-d', $this->modified); ?>
				<time class="post-meta-date-updated" datetime="<?php echo date('Y-m-d', $this->modified); ?>" title="<?php _e('Cập nhật vào '); ?>">
				</time>
				<span class="article-meta">
				    <span class="article-meta__separator">|</span>
				    <i class="fas fa-inbox article-meta__icon"></i>
				  <span class="post-meta-date">
				    <?php _e('Danh mục: '); ?>
				    <?php $this->category(' '); ?>
				</span>
				    	<span class="article-meta__separator">|</span>
			     <span class="post-meta-date" itemprop="author">
				    <?php _e('Tác giả: '); ?>
				<a itemprop="name" href="<?php $this->author->permalink(); ?>" rel="author">
				    <?php $this->author(); ?>
				    </a>
				</span>
					<span class="article-meta__separator">|</span>
				<i class="fas fa-comments"></i>
			     <span class="post-meta-date" itemprop="interactionCount">
				    <a itemprop="discussionUrl" href="<?php $this->permalink(); ?>#comments">
				        <?php $this->commentsNum('0 bình luận', '1 bình luận', '%d bình luận'); ?>
				    </a>
				</span>
				</span>
			    </div>
			    <div class="content">
			    <?php if ($this->fields->excerpt && $this->fields->excerpt != '') {
    echo $this->fields->excerpt;
} else {
    echo $this->excerpt(130);
}
                   echo '<br><br><a href="',$this->permalink(),'" title="',$this->title(),'">Đọc thêm...</a>';
                    ?>
                   </div>
            </div>
        </div>
    	<?php endwhile; ?>
        <?php else: ?>
            <article class="post">
                <h2 class="post-title"><?php _e('Không tìm thấy nội dung'); ?></h2>
            </article>
        <?php endif; ?>
<nav id="pagination">
 <?php $this->pageNav('<i class="fas fa-chevron-left fa-fw"></i>', '<i class="fas fa-chevron-right fa-fw"></i>', 1, '...', ['wrapTag' => 'div', 'wrapClass' => 'pagination', 'itemTag' => '', 'prevClass' => 'extend prev', 'nextClass' => 'extend next', 'currentClass' => 'page-number current']); ?>
</nav>
    </div><!-- end #main -->
	<?php $this->need('sidebar.php'); ?>
	</main>
<script type="text/javascript" src="<?php $this->options->themeUrl('js/wehao.js?v1.4'); ?>"></script>
<?php $this->need('footer.php'); ?>
