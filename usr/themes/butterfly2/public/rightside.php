<div id="rightside">
  <div id="rightside-config-hide" class="">
    <button id="font-plus" type="button" title="Phóng to chữ"><i class="fas fa-plus"></i></button>
    <button id="font-minus" type="button" title="Thu nhỏ chữ"><i class="fas fa-minus"></i></button>
    <?php if ($this->is('post')) : ?>
      <button id="readmode" type="button" title="Chế độ đọc">
        <i class="fas fa-book-open"></i>
      </button>
    <?php endif ?>
    
    <button id="darkmode" type="button" title="Chuyển đổi giữa chế độ sáng và tối">
      <i class="fas fa-adjust">
      </i>
    </button>
    <button id="hide-aside-btn" type="button" title="Chuyển đổi giữa cột đơn và cột kép">
      <i class="fas fa-arrows-alt-h">
      </i>
    </button>
  </div>
  <div id="rightside-config-show">
    <button id="rightside_config" type="button" title="Cài đặt">
      <i class="fas fa-cog fa-spin">
      </i>
    </button>
    <?php if ($this->is('post')) : ?>
      <button class="close" id="mobile-toc-button" type="button" title="Mục lục">
        <i class="fas fa-list-ul">
        </i>
      </button>
    <?php endif ?>
    <?php if ($this->is('post') && $this->allow('comment') || $this->is('page') && $this->allow('comment')) : ?>
      <a id="to_comment" href="#comments" title="Điều hướng đến phần bình luận">
        <i class="fas fa-comments">
        </i>
      </a>
    <?php endif ?>
    <button id="go-up" type="button" title="Lên đầu trang" class="show-percent">
      <span class="scroll-percent"></span>
      <i class="fas fa-arrow-up">
      </i>
    </button>
  </div>
</div>

<?php
/*<button id="translateLink" type="button" title="Chuyển đổi giữa chữ Phồn thể và chữ Tân thể">
      Đổi
    </button>*/
?>