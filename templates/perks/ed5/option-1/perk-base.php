<div class="perks-opt1">
	<?php 
		$count = 1;
	?>
	<?php while($dynamic_perks->have_posts()) : $dynamic_perks->the_post(); 			
		//Image
		$dynam_perk_img_path = get_post_meta( get_the_id(), 'dynam_perk_img_path', true);
		$dynam_perk_img_seo = get_post_meta( get_the_id(), 'dynam_perk_img_seo', true);
		//Perk name and description
		$dynam_perk_title_text = get_post_meta( get_the_id(), 'dynam_perk_title_text', true);
		$dynam_perk_description_text = get_post_meta( get_the_id(), 'dynam_perk_description_text', true);
		//rewards program
		$dynam_perk_rewards_program = get_post_meta( get_the_id(), 'dynam_perk_rewards_program', true);
		if($dynam_perk_rewards_program === "on"){
			if($count%4 === 0) {
				$perk_item_class = "perk perk--end-row perk--reward";
			}elseif(($count-1)%4 === 0) {
				$perk_item_class = "perk perk--start-row perk--reward";
			}else{
				$perk_item_class = "perk perk--reward";
			}
		}else {
			if($count%4 === 0) {
				$perk_item_class = "perk perk--end-row";
			}elseif(($count-1)%4 === 0) {
				$perk_item_class = "perk perk--start-row";
			}else{
				$perk_item_class = "perk";
			}
		}		
	?>
	<div class="<?php echo $perk_item_class; ?>" >
		<?php if(!empty($dynam_perk_img_path)){ ?>
			<div class="perk__image-wrap">
				<img height="60" class="perk__image-tag" src="<?php echo $dynam_perk_img_path; ?>" alt="<?php echo $dynam_perk_img_seo; ?>" title="<?php echo $dynam_perk_img_seo; ?>" />
			</div>		
		<?php } ?>
		<div class="perk__text-wrap">
			<p id="perk__name" class="perk__name"><?php echo $dynam_perk_title_text; ?></p>
			<p class="perk__description">
				<?php echo $dynam_perk_description_text; ?>
			</p>
		</div>
	</div>
	<?php 
		$count++;
	?>
	<?php endwhile; wp_reset_query(); ?>
</div>