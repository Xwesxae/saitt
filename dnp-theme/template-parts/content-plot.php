<!-- template-parts/content-plot.php -->
<article class="plot-card">
    <?php if (has_post_thumbnail()) : ?>
        <div class="plot-image">
            <?php the_post_thumbnail('plot-thumbnail'); ?>
        </div>
    <?php endif; ?>
    
    <div class="plot-content">
        <h3 class="plot-title"><?php the_title(); ?></h3>
        
        <div class="plot-meta">
            <span class="plot-area"><?php echo get_field('area'); ?> соток</span>
            <span class="plot-number">Участок №<?php echo get_field('plot_number'); ?></span>
        </div>
        
        <div class="plot-price"><?php echo dnp_format_price(get_field('price')); ?></div>
        
        <?php 
        $status = get_field('status');
        $status_info = dnp_get_plot_status($status);
        ?>
        <span class="plot-status <?php echo $status_info['class']; ?>">
            <?php echo $status_info['text']; ?>
        </span>
        
        <div class="plot-actions mt-20">
            <a href="<?php the_permalink(); ?>" class="btn btn-outline w-100">Подробнее</a>
        </div>
    </div>
</article>