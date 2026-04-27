<?php get_header(); ?>
<main class="page-content">

  <?php get_template_part('template-parts/authors-stories', null, array(
      'current_author_id' => (int) get_the_author_meta('ID'),
  )); ?>

  <article class="single-post">
    <div class="single-post__card">
      <h1 class="single-post__title"><?php the_title(); ?></h1>
      <div class="single-post__content"><?php the_content(); ?></div>
    </div>
  </article>

</main>
<?php get_footer(); ?>
