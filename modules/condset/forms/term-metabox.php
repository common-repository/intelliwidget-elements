<div id="intelliwidget_main_meta_box_<?php echo $term->term_taxonomy_id; ?>" class="main-meta-box postbox">
  <h2><?php echo IntelliWidgetMainStrings::get_label( 'metabox_title' ); ?></h2>
  <div class="inside">
    <input type="hidden" name="term_taxonomy_id" id="term_taxonomy_id" value="<?php echo $term->term_taxonomy_id; ?>" />
    <?php
      iwctl()->render_profiles( $term->term_taxonomy_id, $term->taxonomy ); ?>
  </div>
</div>
