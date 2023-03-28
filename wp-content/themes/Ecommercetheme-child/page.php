<!-- template file for the pages -->
<?php

get_header();

if (have_posts()) :
	while (have_posts()) : the_post(); ?>
	
	<article class="post page">
		
		
		<?php if ( is_page(302) ) { ?>
			<?php get_template_part( 'post' ); ?>

		<?php } else if(!is_page(302)) {?>
		<h2><?php the_title(); ?></h2>
		<?php the_content(); } ?>  
		<!-- the content displays the content of everypage -->

	</article>
	
	<?php endwhile;
	
	else :
		echo '<p>No content found</p>';
	
	endif;
	
get_footer();

?>