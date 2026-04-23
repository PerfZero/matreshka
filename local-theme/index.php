<?php
/**
 * Basic fallback template for local edits.
 */
get_header();
?>
<main class="page-content">
  <?php get_template_part('template-parts/authors-stories'); ?>
  <?php get_template_part('template-parts/home-hero'); ?>
  <?php get_template_part('template-parts/city-rubrics'); ?>
  <?php
    get_template_part('template-parts/posts-masonry', null, array(
        'section_title' => 'Новости двора:',
        'section_desc'  => 'Что случилось у нас во дворе? Нарочно не придумаешь)',
    ));
  ?>
</main>
<?php get_footer(); ?>
