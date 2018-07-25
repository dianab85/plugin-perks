<?php 
$dynam_perk_img_path = get_post_meta( $post->ID, 'dynam_perk_img_path', true);
$dynam_perk_img_seo = get_post_meta( $post->ID, 'dynam_perk_img_seo', true);
$dynam_perk_title_text = get_post_meta( $post->ID, 'dynam_perk_title_text', true);
$dynam_perk_description_text = get_post_meta( $post->ID, 'dynam_perk_description_text', true);
$dynam_perk_rewards_program = get_post_meta( $post->ID, 'dynam_perk_rewards_program', true);
$dynam_perk_link_status = 'internal-active';

if(empty($dynam_perk_img_seo)){
	$dynam_perk_img_seo = $dynam_perk_title_text;
}
?>
<div class="input-sect">
	<label class="no-padd">Image</label>
	<span class="help-text">
		<i class="icon-info"></i>
		<span>
			Accepted image formats: .png and .jpg
		</span>
	</span>
	<div class="img-upload">
		<div class="img-preview">
			<?php if(!empty($dynam_perk_img_path)){
				echo "<img src='" . $dynam_perk_img_path . "' />" ;
			} ?>
		</div>
		<div class="upload">
			<input type='button' class='button button-upload' value='Upload a File'/>
		    <input type='button' class='button button-clear' value='Clear'/>
			<div class="input-props">
				<div class="input-sect">
					<label>Image Path</label>
		    		<input type='text' class='img-path' id="dynam_perk_img_path" name="dynam_perk_img_path" value='<?php echo $dynam_perk_img_path; ?>' />
				</div>
				<div class="input-sect">
					<label>Image Alt Text</label>
		    		<input type='text' class='img-path' id="dynam_perk_img_seo" name="dynam_perk_img_seo" value='<?php echo $dynam_perk_img_seo; ?>' />
		    		<span class="help-text">
		    			<i class="icon-info"></i>
		    			<span>
		    				This text will be used for the title & Alt text for the attached image.
		    			</span>
		    		</span>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="input-sect">
	<label>Place a checkmark if this is the <strong>Rewards Program Perk</strong></label>
	<input type="checkbox" id="dynam_perk_rewards_program" name="dynam_perk_rewards_program" <?php echo $dynam_perk_rewards_program; ?>>
</div>

<div class="input-sect">
	<label>Perk Name</label>
	<input type="text" class="med" id="dynam_perk_title_text" name="dynam_perk_title_text" value="<?php echo $dynam_perk_title_text; ?>">
</div>

<div class="input-sect">
	<label>Perk Description</label>
	<textarea type="text" rows=5 class="med" id="dynam_perk_description_text" name="dynam_perk_description_text"><?php echo $dynam_perk_description_text; ?></textarea>
</div>

