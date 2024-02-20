<nav id="nav" class="show" >
         <span id="blog-info">
            <a href="<?php $this->options->siteUrl(); ?>">
                <?php if(!empty($this->options->SiteLogo)) : ?>
                <img src="<?php $this->options->SiteLogo() ?>" width="95px" />
                <?php else :?>
                <span class="site-name"><?php $this->options->title() ?></span>
                <?php endif ?>
            </a>
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
                            <li class="fa-fw fas fa-home"></li><?php _e(' Trang chủ'); ?></a>
                    </div>
                    <?php if($this->options->EnableAutoHeaderLink === 'on') : ?>
                        <?php $this->widget('Widget_Contents_Page_List')->to($pages); ?>
                        <?php while($pages->next()): ?>
                        <div class="menus_item">
                            <a class="site-page" href="<?php $pages->permalink(); ?>" title="<?php $pages->title(); ?>">
                            <?php switch ($pages->title){
                               case "Liên kết":
                                echo "<i class='fa-fw fas fa-link'></i>";
                                break;
                            case "Giới thiệu":
                                echo "<li class='fa-fw fas fa-user'></li>";
                                break;
                            case "Bình luận":
                                echo "<i class='fa-fw fas fa-comment-dots'></i>";
                                break;
                            case "Lưu trữ":
                                echo "<i class='fa-fw fas fa-archive'></i>";
                                break;
                            case "Thẻ":
                                echo "<i class='fa-fw fas fa-tags'></i>";
                                break;
                            case "Danh mục":
                                echo "<i class='fa-fw fas fa-folder-open'></i>";
                                break;
                            case "Bảng tin nhắn":
                                echo "<i class='fa-fw fa fa-comment-dots'></i>";
                                break;
                            default:
                                echo "<i class='fa-fw fa fa-coffee'></i>";                            
                            }?>
                            <span><?php $pages->title(); ?></span>
                            </a>
                       </div>
                    <?php endwhile; ?>
                    <?php endif; ?>
                    <?php $this->options->CustomHeaderLink() ?>
                </div>
            </div>
    </nav>