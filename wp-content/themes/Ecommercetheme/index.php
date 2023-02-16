<?php
if(have_posts()):
    while(have_posts()): the_post();?>
<h2> Title should go here</h2>
<?php
endwhile;

else :

    echo '<p> No Content Found</p>';

endif;

?>