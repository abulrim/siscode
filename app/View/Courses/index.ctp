<section class="cil">
</section>


<?php echo $this->HtmlLogic->startTemplate(array('id' => 'course-input-tmpl'));?>
	<select name="subject_id">
		<?php foreach($subjects as $subject): ?>
			<option value="<?php echo $subject['Subject']['id']; ?>"><?php echo $subject['Subject']['name']; ?></option>
		<?php endforeach; ?>
	</select>
	<input type="text" name="number" />
	<input type="text" name="crn" />
<?php echo $this->HtmlLogic->endTemplate();?>