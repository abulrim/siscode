<script type="text/x-handlebars">
	<div id="logo"><a href="<?php echo $this->webroot; ?>"></a></div>
	<div class="clear"></div>

	<div class="wrapper">
		{{outlet}}

		<?php if (Configure::read('debug') == 0): ?>
			<div class="social-media">
				<div class="social fb">
					<div class="fb-like" data-href="http://www.facebook.com/siscode.me" data-send="false" data-layout="button_count" data-width="300" data-show-faces="false" data-font="arial"></div>
				</div>
				<div class="social twitter">
					<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://siscode.me" data-text="Build your uni schedule on" data-via="siscodeme">Tweet</a>
				</div>
				Last updated 30 April 2013 (Fall 2013-2014)
			</div>
		<?php endif; ?>
	</div>
</script>

<script type="text/x-handlebars" data-template-name="schedule">
	{{outlet}}
	{{render "footer"}}
</script>

<script type="text/x-handlebars" data-template-name="components/course-input">
	<div class="cil-inputs-wrapper">
		<div class="cil-inputs">
			<div class="cil-course-floats cil-subject-wrapper">
				{{chosen-select selectClass="cil-course-subject" content=subjects optionLabelPath="content.name" optionValuePath="content.id" value=model.subject}}
			</div>
			<div class="cil-course-floats cil-number-wrapper">
				{{chosen-select selectClass="cil-course-subject" content=numbers value=model.number}}
			</div>
			<div class="cil-course-floats cil-crn-wrapper">
				{{input class="cil-course-crn course-input" value=model.crn placeholder="CRN"}}
			</div>
			<div class="clear"></div>
		</div>
		<div class="cil-remove-course" {{action "removeCourse"}}><i class="cil-remove-course-btn"></i></div>
	</div>
</script>

<script type="text/x-handlebars" data-template-name="components/chosen-select">
	{{view Em.Select class=selectClass content=content optionLabelPath=optionLabelPath optionValuePath=optionValuePath value=value selection=selection}}
</script>

<script type="text/x-handlebars" data-template-name="inputs">
	<div class="l-input">
		<section class="cil">
			<div class="cil-main">
				<div class="cil-institutions">
					{{chosen-select selectClass="cil-institution" content=institutions optionLabelPath="content.name" selection=selectedInstitution}}
				</div>
				<div class="cil-courses">
					{{#each course in courses}}
						{{course-input model=course subjects=subjects}}
					{{/each}}
				</div>
				{{#if selectedInstitution}}
					<button class="cil-add-course" {{action "addCourse"}}><i class="cil-add-course-icon"></i>Add course</button>
				{{/if}}
				<section class="cil-filters">
					<h2><i class="cil-filter-btn"></i>Filter by day:</h2>
					<table class="cil-filters-table">
						<tr>
							{{#each weekdays}}
								<td>
									{{input id=label type="checkbox" checked=checked}}
									<label {{bind-attr for="label"}}>{{label}}</label>
								</td>
							{{/each}}
						</tr>
					</table>
				</section>
				<button class="cil-submit" {{action "submit"}}>Submit</button>
			</div>
			<div class="cil-expand-arrow"><i class="cil-expand-arrow-down"></i></div>
		</section>
	</div>
</script>

<script type="text/x-handlebars" data-template-name="calendar">

	{{render "inputs"}}

	<div class="l-results">
		<section class="schedule">
			<div class="schedule-loader"></div>
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
			{{outlet}}
		</section>
	</div>
</script>

<script type="text/x-handlebars" data-template-name="courses">
	{{#unless maxPage}}
		<div class="schedule-empty-error">
			{{errorMessage}}
		</div>
	{{/unless}}

	{{#each day in days}}
		<div class="schedule-column schedule-day">
			{{#each slot in day}}
				{{course-slot model=slot}}
			{{/each}}
		</div>
	{{/each}}
	<div class="paginator paginator-top">
		{{course-paginator maxPage=maxPage page=page}}
	</div>
	<div class="paginator">
		{{course-paginator maxPage=maxPage page=page}}
	</div>
</script>

<script type="text/x-handlebars" data-template-name="footer">
	<div {{bind-attr class=":foot-bar isOpen:toggled"}}>
		<div class="foot-bar-top">
			<div class="foot-bar-toggle-wrapper" {{action "toggleOpen"}}>
				<div class="foot-bar-toggle">
					<i class="foot-bar-arrow"></i>
				</div>
			</div>
			<div class="foot-bar-add" {{action "addNew"}}>
				<i class="foot-bar-add-icon"></i>
			</div>
		</div>
		<div class="foot-bar-content">
			<ul class="foot-bar-combination">
				{{#each savedCombinations}}
					<li>
						{{footer-input model=this}}
					</li>
				{{/each}}
			</ul>
		</div>
	</div>
</script>

<script type="text/x-handlebars" data-template-name="components/footer-input">
	{{#unless isEdit}}
		<div>
			<a class="foot-bar-combination-name" name="foot-bar-combination-name">{{model.name}}</a>
		</div>
	{{else}}
		<div>
			{{input value=model.name class="foot-bar-edit-input"}}
		</div>
	{{/unless}}
	<div class="foot-bar-edit" {{action "edit"}}><i class="foot-bar-edit-icon"></i></div>
	<div class="foot-bar-remove" {{action "remove" this}}><i class="foot-bar-remove-icon"></i></div>
	<div class="clear"></div>
</script>

<script type="text/x-handlebars" data-template-name="components/course-slot">
	<div class="resp-long">{{model.startTime}} - {{model.endTime}}</div>
	{{model.course.code}} {{model.course.number}}
	<div class="tooltip-content">
		<div class="tooltip-title">{{model.course.title}}</div>
		<div class="tooltip-body">
			CRN: {{model.course.crn}}
			<br>Instructor: {{model.instructor}}
			<div class="resp-small">{{model.startTime}} - {{model.endTime}}</div>
		</div>
	</div>
</script>

<script type="text/x-handlebars" data-template-name="components/course-paginator">
	<div {{bind-attr class=":paginator-next showNext::paginator-disabled"}} {{action "fetch" "next"}}><i class="paginator-arrow-right"></i></div>
	<div {{bind-attr class=":paginator-previous showPrevious::paginator-disabled"}}  {{action "fetch" "previous"}}><i class="paginator-arrow-left"></i></div>
	<div class="paginator-text">{{page}}/{{maxPage}}</div>
	<div class="clear"></div>
</script>

<script>
	window.config = {
		institutions: <?php echo json_encode($institutions); ?>,
		webroot: "<?php echo $this->webroot; ?>"
	}
</script>
