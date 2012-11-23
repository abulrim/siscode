<a href="<?php echo $this->webroot; ?>"><div id="logo"></div></a>
<div class="clear"></div>

<div class="wrapper">
	<div class="l-input">
		<section class="cil">
			<div class="cil-main">
				<div class="cil-courses">
				</div>
				<button class="cil-add-course" name="addCourse"><i class="cil-add-course-icon"></i>Add course</button>
				<section class="cil-filters">
					<h2><i class="cil-filter-btn"></i>Filter by day:</h2>
					<table class="cil-filters-table">
						<tr>
							<td>
								<input type="checkbox" id="filter1" name="filter[1]" checked="checked" value="1" />
								<label for="filter1" onclick="">M</label>
							</td>
							<td>
								<input type="checkbox" id="filter2" name="filter[2]" checked="checked" value="2" />
								<label for="filter2" onclick="">T</label>
							</td>
							<td>
								<input type="checkbox" id="filter3" name="filter[3]" checked="checked" value="3" />
								<label for="filter3" onclick="">W</label>
							</td>
							<td>
								<input type="checkbox" id="filter4" name="filter[4]" checked="checked" value="4" />
								<label for="filter4" onclick="">R</label>
							</td>
							<td>
								<input type="checkbox" id="filter5" name="filter[5]" checked="checked" value="5" />
								<label for="filter5" onclick="">F</label>
							</td>
							<td>
								<input type="checkbox" id="filter6" name="filter[6]" checked="checked" value="6" />
								<label for="filter6" onclick="">S</label>
							</td>
						</tr>
					</table>
				</section>
				<input type="button" class="cil-submit" value="Submit" />
			</div>
			<div class="cil-expand-arrow"><i class="cil-expand-arrow-down"></i></div>
		</section>
	</div>
	
	<div class="l-results">
		<section class="schedule">
			<div class="schedule-loader"></div>
			<div class="schedule-empty-error">No results! Take a break this semester, work at McDonalds instead</div>
			<div class="schedule-header">
				<ul class="schedule-weekdays">
					<div class="resp-long">
						<li></li>
						<li>Monday</li>
						<li>Tuesday</li>
						<li>Wednesday</li>
						<li>Thursday</li>
						<li>Friday</li>
						<li>Saturday</li>
					</div>
					<div class="resp-small">
						<li></li>
						<li>Mon</li>
						<li>Tue</li>
						<li>Wed</li>
						<li>Thu</li>
						<li>Fri</li>
						<li>Sat</li>
					</div>
				</ul>
			</div>
			<div class="schedule-column">
				<ul class="schedule-time-slots">
					<div class="resp-long">
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
					</div>
					<div class="resp-small">
						<li></li>
						<li>7-8</li>
						<li>8-9</li>
						<li>9-10</li>
						<li>10-11</li>
						<li>11-12</li>
						<li>12-13</li>
						<li>13-14</li>
						<li>14-15</li>
						<li>15-16</li>
						<li>16-17</li>
						<li>17-18</li>
						<li>18-19</li>
					</div>
				</ul>
			</div>
			<div class="schedule-column day-1 schedule-day"></div>
			<div class="schedule-column day-2 schedule-day"></div>
			<div class="schedule-column day-3 schedule-day"></div>
			<div class="schedule-column day-4 schedule-day"></div>
			<div class="schedule-column day-5 schedule-day"></div>
			<div class="schedule-column last day-6 schedule-day"></div>
			<div class="paginator paginator-top"></div>
			<div class="paginator"></div>
		</section>
	</div>
	
</div>
<div class="foot-bar">
	<div class="foot-bar-top">
		<div class="foot-bar-toggle-wrapper">
			<div class="foot-bar-toggle">
				<i class="foot-bar-arrow"></i>
			</div>
		</div>
		<div class="foot-bar-add">
			<i class="foot-bar-add-icon"></i>
		</div>
	</div>
	<div class="foot-bar-content">
		<ul class="foot-bar-combination">
		</ul>
	</div>
</div>
<?php echo $this->HtmlLogic->startTemplate(array('id' => 'foot-bar-combination-tmpl')); ?>
	<div class="foot-bar-combination-name-wrapper">
		<div>
			<a class="foot-bar-combination-name" href="<?php echo $this->webroot . 'c/'; ?>{{url}}" name="foot-bar-combination-name">{{name}}</a>
		</div>
		<div>
			<input type="text" value="{{name}}" class="foot-bar-edit-input" />
		</div>
	</div>
	<div class="foot-bar-edit"><i class="foot-bar-edit-icon"></i></div>
	<div class="foot-bar-remove"><i class="foot-bar-remove-icon"></i></div>
	<div class="clear"></div>
<?php echo $this->HtmlLogic->endTemplate(); ?>
	
<?php echo $this->HtmlLogic->startTemplate(array('id' => 'course-input-tmpl'));?>
	<div class="cil-inputs-wrapper">
		<div class="cil-inputs">
			<div class="cil-course-floats cil-subject-wrapper">
				<select name="subject_id" class="cil-course-subject" data-placeholder="Select a Subject">
					<option value=''></option>
					{{#each subjects}}
						<option value="{{this.id}}">{{this.name}}</option>
					{{/each}}
				</select>
			</div>
			<div class="cil-course-floats cil-number-wrapper">
				<select name="number" class="cil-course-number" data-placeholder="Number">
				</select>
			</div>
			<div class="cil-course-floats cil-crn-wrapper"><input type="text" class="cil-course-crn course-input" name="crn" value="{{crn}}" placeholder="CRN" /></div>
			<div class="clear"></div>
		</div>
	</div>
	<div name="remove" class="cil-remove-course"><i class="cil-remove-course-btn"></i></div>
	<div class="clear"></div>
<?php echo $this->HtmlLogic->endTemplate(); ?>
	
<?php echo $this->HtmlLogic->startTemplate(array('id' => 'course-input-numbers')); ?>
	<option value=''></option>
	{{#each numbers}}
		<option value="{{this}}">{{this}}</option>
	{{/each}}
<?php echo $this->HtmlLogic->endTemplate(); ?>
	
<?php echo $this->HtmlLogic->startTemplate(array('id' => 'course-slot-view-tmpl'));?>
	<div class="schedule-course-slot-wrapper">
		<div class="resp-long">{{start_time}} - {{end_time}}</div>
		{{Course.subject_code}} {{Course.number}}
		<div class="tooltip-content">
			<div class="tooltip-title">{{Course.title}}</div>
			<div class="tooltip-body">
				CRN: {{Course.crn}}
				<br>Instructor: {{Instructor.name}}
				<div class="resp-small">{{start_time}} - {{end_time}}</div>
			</div>
		</div>
	</div>
<?php echo $this->HtmlLogic->endTemplate();?>
		
<?php echo $this->HtmlLogic->startTemplate(array('id' => 'pagination-view-tmpl'));?>
	<div class="paginator-next"><i class="paginator-arrow-right"></i></div>
	<div class="paginator-previous"><i class="paginator-arrow-left"></i></div>
	<div class="paginator-text">{{page}}/{{maxPage}}</div>
	<div class="clear"></div>
<?php echo $this->HtmlLogic->endTemplate();?>

<div id="subjects-data" data-subjects='<?php echo json_encode($subjects); ?>'></div>