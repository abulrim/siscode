<div id="logo"></div>
<div class="clear"></div>

<div class="wrapper">
	<section class="cil">
		<div class="cil-courses">
		</div>
		<button id="add-course" name="addCourse"><i class="add-course-icon"></i>Add course</button>
		<section class="filters">
			<h2><i class="filter-btn"></i>Filter by day:</h2>
			<table class="filters-table">
				<tr><td>M</td><td>T</td><td>W</td><td>R</td><td>F</td><td>S</td></tr>
				<tr>
					<td><input type="checkbox" name="filter[1]" checked="checked" value="1" /></td>
					<td><input type="checkbox" name="filter[2]" checked="checked" value="2" /></td>
					<td><input type="checkbox" name="filter[3]" checked="checked" value="3" /></td>
					<td><input type="checkbox" name="filter[4]" checked="checked" value="4" /></td>
					<td><input type="checkbox" name="filter[5]" checked="checked" value="5" /></td>
					<td><input type="checkbox" name="filter[6]" checked="checked" value="6" /></td>
				</tr>
			</table>
		</section>
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
		<div class="column day-1 schedule-day"></div>
		<div class="column day-2 schedule-day"></div>
		<div class="column day-3 schedule-day"></div>
		<div class="column day-4 schedule-day"></div>
		<div class="column day-5 schedule-day"></div>
		<div class="column last day-6 schedule-day"></div>
		<div class="paginator top"></div>
		<div class="paginator"></div>
	</section>
</div>
<?php echo $this->HtmlLogic->startTemplate(array('id' => 'course-input-tmpl'));?>
	<div class="course-floats">
		<select name="subject_id" class="course-subject" data-placeholder="Select a Subject">
			<option value=''></option>
			<?php foreach($subjects as $subject): ?>
				<option {{#equal subject_id <?php echo $subject['Subject']['id'];?>}}selected{{/equal}} value="<?php echo $subject['Subject']['id']; ?>"><?php echo $subject['Subject']['name']; ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="course-floats"><input type="text" class="course-number course-input" name="number" value="{{number}}" placeholder="Number" /></div>
	<div class="course-floats"><input type="text" class="course-crn course-input" name="crn" value="{{crn}}" placeholder="CRN" /></div>
	<div class="course-floats"><span name="remove" class="remove-course"><i class="remove-course-btn"></i></span></div>
	<div class="clear"></div>
<?php echo $this->HtmlLogic->endTemplate();?>

	
<?php echo $this->HtmlLogic->startTemplate(array('id' => 'course-slot-view-tmpl'));?>
	Title: {{Course.title}}, CRN: {{Course.crn}}, start time: {{start_time}}, end time: {{end_time}}
<?php echo $this->HtmlLogic->endTemplate();?>
		
<?php echo $this->HtmlLogic->startTemplate(array('id' => 'pagination-view-tmpl'));?>
	<div class="next"><i class="arrow-right"></i></div>
	<div class="previous"><i class="arrow-left"></i></div>
	<div class="paginator-text">{{page}}/{{maxPage}}</div>
	<div class="clear"></div>
<?php echo $this->HtmlLogic->endTemplate();?>
	