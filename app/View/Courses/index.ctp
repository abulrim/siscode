<section class="cil">
	<div class="cil-courses">
	</div>
	<input type="button" id="add-course" name="addCourse" value="Add course" />
	<input type="button" class="cil-submit" value="Submit" />
</section>

<section class="schedule">
	<ul class="schedule-list">
	</ul>
</section>

<?php echo $this->HtmlLogic->startTemplate(array('id' => 'course-input-tmpl'));?>
	<select name="subject_id">
		<?php foreach($subjects as $subject): ?>
			<option {{#equal subject_id <?php echo $subject['Subject']['id'];?>}}selected{{/equal}} value="<?php echo $subject['Subject']['id']; ?>"><?php echo $subject['Subject']['name']; ?></option>
		<?php endforeach; ?>
	</select>
	<input type="text" name="number" value="{{number}}"/>
	<input type="text" name="crn" value="{{crn}}"/>
	<input type="button" name="remove" class="remove-course" value="Remove" />
<?php echo $this->HtmlLogic->endTemplate();?>

	
<?php echo $this->HtmlLogic->startTemplate(array('id' => 'course-slot-view-tmpl'));?>
	Title: {{Course.title}}, CRN: {{Course.crn}}, start time: {{start_time}}, end time: {{end_time}}
<?php echo $this->HtmlLogic->endTemplate();?>