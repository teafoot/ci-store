<div data-role="navbar">
  <ul>
  	<?php
  		foreach ($top_nav_btns as $btn) {
  			if ($btn["btn_target_url"] == $current_url) {
  				$top_btn_css = 'class="ui-btn-active"';
  			} else {
  				$top_btn_css = "";
  			}

        if ($btn["text"] == "Login") {
          $top_btn_css .= 'rel="external"';
        }
  	?>
    	<li>
    		<a <?= $top_btn_css ?> href="<?= $btn['btn_target_url'] ?>" data-icon="<?= $btn['icon'] ?>"><?= $btn['text'] ?></a>
    	</li>
  	<?php 
  		} 
  	?>
  </ul>
</div>