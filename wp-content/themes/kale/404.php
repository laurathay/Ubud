<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package kale  
 *
 */
?>
<?php get_header(); ?>

<!-- Two Columns -->
<div class="row two-columns">
    <!-- Main Column -->
    <div class="main-column col-md-12" role="main">
        
        <!-- Page Content -->
        <div class="error-404">
            <h1 class="entry-title"><?php _e('404', 'kale'); ?></h1>
            <p><?php _e('Page Not Found', 'kale'); ?></p>
        </div>
        <!-- /Page Content -->
        
    </div>
    <!-- /Main Column -->


</div>
<!-- /Two Columns -->

<hr />

<?php get_footer(); ?>