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
</main>
<?php get_footer(); ?>
