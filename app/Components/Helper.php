<?php

namespace App\Components;

use App\BranchGeneralReport;
use App\ExamDates;
use App\Models\Balance;
use App\Models\Group;
use App\Models\GroupStudent;
use App\Models\Staff;
use App\Student;
use Illuminate\Support\Facades\DB;

class Helper
{

    public static function getAplesinLink()
    {
        return config('apelsin.payment_link');
    }

    public static function convert_time_to_minutes($time)
    {
        $time = explode(':', $time);
        return ($time[0] * 60) + ($time[1]);
    }

    public function count_lesson($group_type, $end_date, $exact = array())
    {
        $d = date("Y-m-d");
        $k = 0;
        if ($group_type == "mwf"):
            while ($d <= $end_date) {
                $timestamp = strtotime($d);
                $day = date('D', $timestamp);
                if (($day == 'Mon') || ($day == 'Wed') || ($day == 'Fri')) {
                    $k++;
                }

                $d = date('Y-m-d', strtotime($d . ' +1 day'));
            }
        endif;
        if ($group_type == "tts"):
            while ($d <= $end_date) {
                $timestamp = strtotime($d);
                $day = date('D', $timestamp);
                if (($day == 'Tue') || ($day == 'Thu') || ($day == 'Sat')) {
                    $k++;
                }

                $d = date('Y-m-d', strtotime($d . ' +1 day'));
            }
        endif;
        if ($group_type == "ed"):
            while ($d <= $end_date) {
                $timestamp = strtotime($d);
                $day = date('D', $timestamp);
                if ($day != 'Sun') {
                    $k++;
                }

                $d = date('Y-m-d', strtotime($d . ' +1 day'));
            }
        endif;
        if ($group_type == "ss"):
            while ($d <= $end_date) {
                $timestamp = strtotime($d);
                $day = date('D', $timestamp);
                if (($day == 'Sat') || ($day == 'Sun')) {
                    $k++;
                }

                $d = date('Y-m-d', strtotime($d . ' +1 day'));
            }
        endif;
        if ($group_type == "other"):
            while ($d <= $end_date) {
                $timestamp = strtotime($d);
                $day = date('D', $timestamp);
                if (in_array($day, $exact)) {
                    $k++;
                }

                $d = date('Y-m-d', strtotime($d . ' +1 day'));
            }
        endif;
        return $k;
    }

    public function recalculation_for_replaced()
    {
        $grstd_query = "
			SELECT
				gs.group_id,
				gs.student_id,
				gs.`status`,
				gs.balance,
				gs.lessons_left,
				gs.exception_sum,
				gs.exception_status,
				gt.teacher_id,
				g.`id` as group_id,
				g.`name` AS group_name,
				g.`days`,
				g.`exact_days`,
				g.level_id,
				g.branch_id,
				g.fee as course_price
			FROM t_group_student gs
				LEFT JOIN t_groups g
					ON
						gs.group_id = g.id
				LEFT JOIN t_levels l
					ON
						g.level_id = l.id
				LEFT JOIN t_group_teacher gt
					ON
						gt.group_id = g.id
				LEFT JOIN t_students s
					ON
						gs.student_id = s.id
			WHERE
				`gs`.`status` IN ('a')
				 AND
					 (
						`s`.`grant` = 0
						OR `s`.`grant` IS NULL
					)
				 AND gs.group_id  IN(
					SELECT
						group_id
					FROM t_replaced_lessons
					WHERE replaced_date = CURDATE()
				 )
				 AND g.`status` = 'a'

		";

        $grstd = DB::select($grstd_query);

        // Writing track for executing queries: track for select
        $track_s_query = "
			INSERT INTO `t_recalculation_jobs` (`type`,`table_name`,`rows_number`,`branch_id`,`created_at`)
			VALUES('select','t_group_student, t_groups, t_canceled_lessons, t_students', :rows_number, :branch_id, now())
		";
        DB::insert($track_s_query,
            [
                'rows_number' => count($grstd),
                'branch_id' => 0,
            ]
        );

        $count_balance = 0;
        $count_student = 0;
        foreach ($grstd as $gs) {
            if (($gs->exception_sum !== null) && ($gs->exception_status == 'a')) {
                $payment_per_month = $gs->exception_sum;
            } else {
                $payment_per_month = $gs->course_price;
            }
            // $payment_per_month = ($gs->exception_sum !== null) ? $gs->exception_sum : $gs->course_price;
            $group = Group::find($gs->group_id);
            $one_lesson_price = $payment_per_month / $group->getLessonCount();
            // taking money from student's balance
            $balance = Balance::create([
                'student_id' => $gs->student_id,
                'debit' => 0,
                'credit' => $one_lesson_price,
                'teacher_id' => $gs->teacher_id,
                'group_id' => $gs->group_id,
                'level_id' => $gs->level_id,
                'branch_id' => $gs->branch_id,
                'details' => [
                    "product" => [
                        'group_id' => $gs->group_id,
                        'lesson_count' => 1,
                    ],
                ],
            ]);
            if ($balance) {
                $count_balance++;
            }

            $student = Student::find($gs->student_id);
            $student->balance -= $one_lesson_price;
            if ($student->save()) {
                $count_student++;
            }

        }

        // Writing track for executing queries: track for update t_group_student
        $track_u_query = "
			INSERT INTO `t_recalculation_jobs` (`type`,`table_name`,`rows_number`,`branch_id`,`created_at`)
			VALUES('insert','t_balance', :rows_number, :branch_id, now())
		";
        DB::insert($track_u_query,
            [
                'rows_number' => $count_balance,
                'branch_id' => 0,
            ]
        );

        // Writing track for executing queries: track for insert t_debts count
        $track_i_query = "
			INSERT INTO `t_recalculation_jobs` (`type`,`table_name`,`rows_number`,`branch_id`,`created_at`)
			VALUES('update','t_students', :rows_number, :branch_id, now())
		";
        DB::insert($track_i_query,
            [
                'rows_number' => $count_student,
                'branch_id' => 0,
            ]
        );

    }
    public function recalculation($branch_id, $is_online = 0)
    {
        // Get all holidays from DB
        $holidays = DB::select("SELECT `holiday` FROM t_holidays");

        // If today is holiday, so we don't need to recalculate
        foreach ($holidays as $h) {
            if ($h->holiday == date('m-d')) {
                return false;
            }
        }

        // Define if today is odd days or even days, if today is Sunday we won't recalculate
        if (date('D') == 'Mon' || date('D') == 'Wed' || date('D') == 'Fri') {
            $days = 'mwf';
        } elseif (date('D') == 'Tue' || date('D') == 'Thu' || date('D') == 'Sat') {
            $days = 'tts';
        } else {
            return false;
        }

        // Getting students with groups for recalculate
        $grstd_query = "
	            SELECT
	                gs.group_id,
	                gs.student_id,
	                gs.`status`,
	                gs.balance,
	                gs.lessons_left,
	                gs.exception_sum,
	                gs.exception_status,
					gt.teacher_id,
					g.`id` as group_id,
	                g.`name` AS group_name,
	                g.`days`,
	                g.`exact_days`,
					g.level_id,
	                g.branch_id,
					g.fee as course_price
	            FROM t_group_student gs
	                LEFT JOIN t_groups g
	                    ON
	                        gs.group_id = g.id
					LEFT JOIN t_levels l
						ON
							g.level_id = l.id
					LEFT JOIN t_group_teacher gt
						ON
							gt.group_id = g.id
							AND
								CASE
									WHEN l.teaches_2_teachers = 1 THEN gt.teaching_days = :teaching_days
									ELSE gt.teaching_days IS NULL
								END
					LEFT JOIN t_students s
						ON
							gs.student_id = s.id
	            WHERE
	                `gs`.`status` IN ('a')
					 AND
					 	(
							`s`.`grant` = 0
							OR `s`.`grant` IS NULL
						)
					 AND gs.group_id NOT IN(
	                    SELECT
	                        group_id
	                    FROM t_canceled_lessons
	                    WHERE canceled_date = CURDATE()
	                 )
	                 AND g.branch_id = :branch_id
	                 AND g.`is_online` = :is_online
	                 AND g.`status` = 'a'
	                 AND (
                 		g.days IN(:days,'ed')
                 		OR
                 		(
                 			g.days='other' AND JSON_CONTAINS(g.exact_days,:today)
                 		)
                 	)
	        ";

        $today = date('D');

        $grstd = DB::select($grstd_query,
            [
                'branch_id' => $branch_id,
                'is_online' => $is_online,
                'teaching_days' => $days,
                'days' => $days,
                'today' => '["' . $today . '"]',
            ]
        );

        // Writing track for executing queries: track for select
        $track_s_query = "
	        	INSERT INTO `t_recalculation_jobs` (`type`,`table_name`,`rows_number`,`branch_id`,`created_at`)
	        	VALUES('select','t_group_student, t_groups, t_canceled_lessons, t_students', :rows_number, :branch_id, now())
	        ";
        DB::insert($track_s_query,
            [
                'rows_number' => count($grstd),
                'branch_id' => $branch_id,
            ]
        );

        $count_balance = 0;
        $count_student = 0;
        foreach ($grstd as $gs) {
            if (($gs->exception_sum !== null) && ($gs->exception_status == 'a')) {
                $payment_per_month = $gs->exception_sum;
            } else {
                $payment_per_month = $gs->course_price;
            }
            // $payment_per_month = ($gs->exception_sum !== null) ? $gs->exception_sum : $gs->course_price;
            $group = Group::find($gs->group_id);
            $one_lesson_price = $payment_per_month / $group->getLessonCount();
            // taking money from student's balance
            $balance = Balance::create([
                'student_id' => $gs->student_id,
                'debit' => 0,
                'credit' => $one_lesson_price,
                'teacher_id' => $gs->teacher_id,
                'group_id' => $gs->group_id,
                'level_id' => $gs->level_id,
                'branch_id' => $gs->branch_id,
                'details' => [
                    "product" => [
                        'group_id' => $gs->group_id,
                        'lesson_count' => 1,
                    ],
                ],
            ]);
            if ($balance) {
                $count_balance++;
            }

            $student = Student::find($gs->student_id);
            $student->balance -= $one_lesson_price;
            if ($student->save()) {
                $count_student++;
            }

        }

        // Writing track for executing queries: track for update t_group_student
        $track_u_query = "
	        	INSERT INTO `t_recalculation_jobs` (`type`,`table_name`,`rows_number`,`branch_id`,`created_at`)
	        	VALUES('insert','t_balance', :rows_number, :branch_id, now())
	        ";
        DB::insert($track_u_query,
            [
                'rows_number' => $count_balance,
                'branch_id' => $branch_id,
            ]
        );

        // Writing track for executing queries: track for insert t_debts count
        $track_i_query = "
	        	INSERT INTO `t_recalculation_jobs` (`type`,`table_name`,`rows_number`,`branch_id`,`created_at`)
	        	VALUES('update','t_students', :rows_number, :branch_id, now())
	        ";
        DB::insert($track_i_query,
            [
                'rows_number' => $count_student,
                'branch_id' => $branch_id,
            ]
        );
    }

    public function branch_general_report($branch_id, $from_date, $till_date)
    {
        $staff = Staff::whereHas('branches', function ($q) use ($branch_id) {
            $q->where('branches.id', $branch_id);
        })
            ->where(['teaches' => true])
            ->get();

        foreach ($staff as $employee) {

            $query = "
					SELECT
						gs.*,
						staff.`name`,
						stud.`name`
					FROM
						t_group_student gs
					LEFT JOIN t_groups g ON g.id = gs.group_id
					LEFT JOIN t_students stud ON stud.id = gs.student_id
					LEFT JOIN t_group_teacher gt ON gs.group_id = gt.group_id
					LEFT JOIN t_staff staff ON staff.id = gt.teacher_id
					WHERE
						gt.teacher_id = :teacher_id
						AND gs.`status` = 'a'
						AND g.`status` = 'a'
						AND g.`branch_id` = :branch_id
				";

            $active_students = DB::select($query, [
                'teacher_id' => $employee->id,
                'branch_id' => $branch_id,
            ]);

            $query = "
					SELECT
						DISTINCT t.student_id,
						gs.*,
						staff.`name`,
						stud.`name`
					FROM
						t_group_student gs
					LEFT JOIN t_groups g ON g.id = gs.group_id
					LEFT JOIN t_students stud ON stud.id = gs.student_id
					LEFT JOIN t_group_teacher gt ON gs.group_id = gt.group_id
					LEFT JOIN t_staff staff ON staff.id = gt.teacher_id
					LEFT JOIN t_tracks t ON t.student_id = gs.student_id
					WHERE
						gt.teacher_id = :teacher_id
						AND gs.`status` = 'ar'
						AND t.`status` = 'a'
						AND g.`branch_id` = :branch_id
						AND t.`created_at` BETWEEN :from_date AND :till_date
				";

            $finished_students = DB::select($query, [
                'teacher_id' => $employee->id,
                'branch_id' => $branch_id,
                'from_date' => $from_date,
                'till_date' => $till_date,
            ]);

            $query = "
					SELECT
						gt.*,
						g.*
					FROM t_group_teacher gt
					LEFT JOIN t_groups g ON gt.group_id = g.id
					WHERE
						gt.teacher_id = :teacher_id
						AND g.branch_id = :branch_id
						AND g.`status` = 'a'
						AND g.group_started_date BETWEEN :from_date AND :till_date
				";

            $new_groups = DB::select($query, [
                'teacher_id' => $employee->id,
                'branch_id' => $branch_id,
                'from_date' => $from_date,
                'till_date' => $till_date,
            ]);

            $query = "
					SELECT
						gt.*,
						g.*
					FROM t_group_teacher gt
					LEFT JOIN t_groups g ON gt.group_id = g.id
					WHERE
						gt.teacher_id = :teacher_id
						AND g.branch_id = :branch_id
						AND g.`type` = 'individual'
						AND g.`status` = 'a'
						AND g.group_started_date BETWEEN :from_date AND :till_date
				";

            $individual = DB::select($query, [
                'teacher_id' => $employee->id,
                'branch_id' => $branch_id,
                'from_date' => $from_date,
                'till_date' => $till_date,
            ]);

            $query = "
					SELECT
						gt.*,
						g.*
					FROM t_group_teacher gt
					LEFT JOIN t_groups g ON gt.group_id = g.id
					WHERE
						gt.teacher_id = :teacher_id
						AND g.branch_id = :branch_id
						AND g.`type` = 'standard'
						AND g.`status` = 'a'
				";

            $active_groups = DB::select($query, [
                'teacher_id' => $employee->id,
                'branch_id' => $branch_id,
            ]);

            $query = "
					SELECT
						gt.*,
						g.*
					FROM t_group_teacher gt
					LEFT JOIN t_groups g ON gt.group_id = g.id
					WHERE
						gt.teacher_id = :teacher_id
						AND g.branch_id = :branch_id
						AND g.group_end_date BETWEEN :from_date AND :till_date
				";

            $closed_groups = DB::select($query, [
                'teacher_id' => $employee->id,
                'branch_id' => $branch_id,
                'from_date' => $from_date,
                'till_date' => $till_date,
            ]);

            $timestamp = strtotime($from_date);

            BranchGeneralReport::create([
                'staff_id' => $employee->id,
                'active_students' => count($active_students),
                'finished_students' => count($finished_students),
                'new_groups' => count($new_groups),
                'individual' => count($individual),
                'active_groups' => count($active_groups),
                'closed_groups' => count($closed_groups),
                'period' => date('Y-m', $timestamp),
                'branch_id' => $branch_id,
            ]);

        }
    }

    public static function getSubdomain($referer)
    {
        $referer = str_replace(['http://', 'https://'], ['', ''], $referer);
        $part = explode('.', $referer);
        if (count($part) == 3 && $part[0] != 'www') {
            return $part[0];
        } elseif (count($part) >= 4 && $part[0] == 'www') {
            return $part[1];
        }

        return '';
    }
    public function recalculation_temp($branch_id, $date_h, $days_type, $is_online = 0)
    {
        // Get all holidays from DB
        $holidays = DB::select("SELECT `holiday` FROM t_holidays");

        // If today is holiday, so we don't need to recalculate
        foreach ($holidays as $h) {
            if ($h->holiday == $date_h) {
                return false;
            }
        }

        // Define if today is odd days or even days, if today is Sunday we won't recalculate
        // if(date('D')=='Mon' || date('D')=='Wed' || date('D')=='Fri'){
        //     $days = 'mwf';
        // }
        // elseif(date('D')=='Tue' || date('D')=='Thu' || date('D')=='Sat'){
        //     $days = 'tts';
        // }
        // else{
        //     return false;
        // }

        // Getting students with groups for recalculate
        $grstd_query = "
					SELECT
						gs.group_id,
						gs.student_id,
						gs.`status`,
						gs.balance,
						gs.lessons_left,
						gs.exception_sum,
						gs.exception_status,
				g.`id` as group_id,
						g.`name` AS group_name,
						g.`days`,
						g.`exact_days`,
				g.fee as course_price
					FROM t_group_student gs
						LEFT JOIN t_groups g
							ON
								gs.group_id = g.id
				LEFT JOIN t_students s
				  ON
					gs.student_id = s.id
					WHERE
						`gs`.`status` IN ('a','p1')
				 AND
				   (
					`s`.`grant` = 0
					OR `s`.`grant` IS NULL
				  )
				 AND gs.group_id NOT IN(
							SELECT
								group_id
							FROM t_canceled_lessons
							WHERE canceled_date = '2022-01-25'
						 )
						 AND g.branch_id = :branch_id
						 AND g.`is_online` = :is_online
						 AND g.`status` = 'a'
						 AND (
						   g.days IN(:days,'ed')
						 )
				";

        $today = date('D');

        $grstd = DB::select($grstd_query,
            [
                'branch_id' => $branch_id,
                'is_online' => $is_online,
                'days' => $days_type,
                // 'today' => '["Tue"]'
            ]
        );

        // Writing track for executing queries: track for select
        $track_s_query = "
				  INSERT INTO `t_recalculation_jobs` (`type`,`table_name`,`rows_number`,`branch_id`,`created_at`)
				  VALUES('select','t_group_student, t_groups, t_canceled_lessons, t_students', :rows_number, :branch_id, now())
				";
        DB::insert($track_s_query,
            [
                'rows_number' => count($grstd),
                'branch_id' => $branch_id,
            ]
        );
        $count_balance = 0;
        $count_student = 0;
        foreach ($grstd as $gs) {
            if (($gs->exception_sum !== null) && ($gs->exception_status == 'a')) {
                $payment_per_month = $gs->exception_sum;
            } else {
                $payment_per_month = $gs->course_price;
            }
            // $payment_per_month = ($gs->exception_sum !== null) ? $gs->exception_sum : $gs->course_price;
            $group = Group::find($gs->group_id);
            $one_lesson_price = $payment_per_month / $group->getLessonCount();
            // taking money from student's balance
            $balance = Balance::create([
                'student_id' => $gs->student_id,
                'debit' => 0,
                'credit' => $one_lesson_price,
                'details' => [
                    "product" => [
                        'group_id' => $gs->group_id,
                        'lesson_count' => 1,
                    ],
                ],
            ]);
            if ($balance) {
                $count_balance++;
            }

            $student = Student::find($gs->student_id);
            $student->balance -= $one_lesson_price;
            if ($student->save()) {
                $count_student++;
            }

        }

        // Writing track for executing queries: track for update t_group_student
        $track_u_query = "
				  INSERT INTO `t_recalculation_jobs` (`type`,`table_name`,`rows_number`,`branch_id`,`created_at`)
				  VALUES('insert','t_balance', :rows_number, :branch_id, now())
				";
        DB::insert($track_u_query,
            [
                'rows_number' => $count_balance,
                'branch_id' => $branch_id,
            ]
        );

        // Writing track for executing queries: track for insert t_debts count
        $track_i_query = "
				  INSERT INTO `t_recalculation_jobs` (`type`,`table_name`,`rows_number`,`branch_id`,`created_at`)
				  VALUES('update','t_students', :rows_number, :branch_id, now())
				";
        DB::insert($track_i_query,
            [
                'rows_number' => $count_student,
                'branch_id' => $branch_id,
            ]
        );
    }

    public function getStudentsCountByGroups($groups, $status)
    {
        return GroupStudent::whereIn('group_id', $groups)->where('status', $status)->count();
    }

    public function getLessonPrice($group_id,$student_id)
    {
        $gs=GroupStudent::where("group_id",$group_id)->where("student_id",$student_id)->first();
        $group = Group::find($gs->group_id);
        if (($gs->exception_sum !== null) && ($gs->exception_status == 'a')) {
            $payment_per_month = $gs->exception_sum;
        } else {
            $payment_per_month = $group->fee;
        }
        // $payment_per_month = ($gs->exception_sum !== null) ? $gs->exception_sum : $gs->course_price;

        $one_lesson_price = $payment_per_month / $group->getLessonCountForRecalculate();
        return $one_lesson_price;
    }

}
