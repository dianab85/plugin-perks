<div class="perks-opt2">	
    <?php 
		$count = 1;
	?>
	<?php while($dynamic_perks->have_posts()) : $dynamic_perks->the_post(); 			
		//Image
		$dynam_perk_img_path = get_post_meta( get_the_id(), 'dynam_perk_img_path', true);
		$dynam_perk_img_seo = get_post_meta( get_the_id(), 'dynam_perk_img_seo', true);
		//Perk name and description
        $dynam_perk_description_text = get_post_meta( get_the_id(), 'dynam_perk_description_text', true);	
        if($count%4 === 0) {
			$perk_item_class = "perk perk--end-row";
		}elseif(($count-1)%4 === 0) {
			$perk_item_class = "perk perk--start-row";
		}else{
			$perk_item_class = "perk";
		}	
	?>
	<div class="<?php echo $perk_item_class; ?>" >
        <?php if(!empty($dynam_perk_img_path)){ ?>
            <div class="perk__image-wrap"  style="background: url(<?php echo $dynam_perk_img_path; ?>) no-repeat center; background-size: cover;"></div>      
        <?php } ?>	
        <div class="perk__description">
            <?php echo $dynam_perk_description_text; ?>
        </div>	
	</div>
    <?php 
		$count++;
	?>
	<?php endwhile; wp_reset_query(); ?>
</div>