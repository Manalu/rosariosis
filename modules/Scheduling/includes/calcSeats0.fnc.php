<?php
//this function calculates the number of available seats in a course period

//used in MassSchedule.php, Schedule.php, Scheduler.php & UnfilledRequests.php

function calcSeats0($period,$date='')
{
	$mp = $period['MARKING_PERIOD_ID'];

	$seats = DBGet(DBQuery("SELECT 
		max((SELECT count(1) 
		FROM SCHEDULE ss JOIN STUDENT_ENROLLMENT sem ON (sem.STUDENT_ID=ss.STUDENT_ID AND sem.SYEAR=ss.SYEAR) 
		WHERE ss.COURSE_PERIOD_ID='".$period['COURSE_PERIOD_ID']."' 
		AND (ss.MARKING_PERIOD_ID='".$mp."' OR ss.MARKING_PERIOD_ID IN (".GetAllMP(GetMP($mp,'MP'),$mp).")) 
		AND (ac.SCHOOL_DATE>=ss.START_DATE AND (ss.END_DATE IS NULL OR ac.SCHOOL_DATE<=ss.END_DATE)) 
		AND (ac.SCHOOL_DATE>=sem.START_DATE AND (sem.END_DATE IS NULL OR ac.SCHOOL_DATE<=sem.END_DATE)))) AS FILLED_SEATS 
	FROM ATTENDANCE_CALENDAR ac 
	WHERE ac.CALENDAR_ID='".$period['CALENDAR_ID']."' 
	AND ac.SCHOOL_DATE BETWEEN ".($date?"'".$date."'":db_case(array("(CURRENT_DATE>'".GetMP($mp,'END_DATE')."')",'TRUE',"'".GetMP($mp,'START_DATE')."'",'CURRENT_DATE')))." AND '".GetMP($mp,'END_DATE')."'"));
	return $seats[1]['FILLED_SEATS'];
}
