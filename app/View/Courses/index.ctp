<div id="logo"></div>
<div class="clear"></div>

<div class="wrapper">
	<section class="cil">
		<div class="cil-courses">
		</div>
		<input type="button" id="add-course" name="addCourse" value="Add course"/>
		<input type="button" class="cil-submit" value="Submit" />
	</section>

	<section class="schedule">
		<div class="schedule-header">
			<ul class="weekdays">
				<li></li>
				<li>Monday</li>
				<li>Tuesday</li>
				<li>Wednesday</li>
				<li>Thursday</li>
				<li>Friday</li>
				<li>Saturday</li>
			</ul>
		</div>
		<div class="column">
			<ul class="time-slots">
				<li></li>
				<li>7:00am - 8:00am</li>
				<li>8:00am - 9:00am</li>
				<li>9:00am - 10:00am</li>
				<li>10:00am - 11:00am</li>
				<li>11:00am - 12:00pm</li>
				<li>12:00pm - 1:00pm</li>
				<li>1:00pm - 2:00pm</li>
				<li>2:00pm - 3:00pm</li>
				<li>3:00pm - 4:00pm</li>
				<li>4:00pm - 5:00pm</li>
				<li>5:00pm - 6:00pm</li>
				<li>6:00pm - 7:00pm</li>
			</ul>
		</div>
		<div class="column day-1"></div>
		<div class="column day-2"></div>
		<div class="column day-3"></div>
		<div class="column day-4"></div>
		<div class="column day-5"></div>
		<div class="column last  day-6"></div>

		<ul class="schedule-list">
		</ul>
	</section>
</div>
<?php echo $this->HtmlLogic->startTemplate(array('id' => 'course-input-tmpl'));?>
	<select name="subject_id" class="course-subject">
		<?php foreach($subjects as $subject): ?>
			<option {{#equal subject_id <?php echo $subject['Subject']['id'];?>}}selected{{/equal}} value="<?php echo $subject['Subject']['id']; ?>"><?php echo $subject['Subject']['name']; ?></option>
		<?php endforeach; ?>
	</select>
	<input type="text" class="course-number" name="number" value="{{number}}" placeholder="Number" />
	<input type="text" class="course-crn" name="crn" value="{{crn}}" placeholder="CRN" />
	<span name="remove" class="remove-course"><i class="remove-course-btn"></i></span>
<?php echo $this->HtmlLogic->endTemplate();?>

	
<?php echo $this->HtmlLogic->startTemplate(array('id' => 'course-slot-view-tmpl'));?>
	Title: {{Course.title}}, CRN: {{Course.crn}}, start time: {{start_time}}, end time: {{end_time}}
<?php echo $this->HtmlLogic->endTemplate();?>
	