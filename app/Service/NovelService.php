<?php
namespace App\Service;
use App\Model\Novel;
use App\Model\NovelDay;
use App\Model\NovelWeek;
use App\Model\NovelMonth;

class NovelService
{

    public static function ifNovelDayEmptySetIt($novel_day, $novel, $today) {
        if(!empty($novel_day)) return $novel_day;
        $novel_day = new NovelDay();
        $novel_day->novel_id = $novel->id;
        $novel_day->date = $today;
        $novel_day->category_id = $novel->novel_categories_id;
        return $novel_day;
    }


    public static function ifNovelWeekEmptySetIt($this_novel_week, $week, $this_year, $novel) {
        if(!empty($this_novel_week)) return $this_novel_week;
        $this_novel_week = new NovelWeek();
        $this_novel_week->week = $week;
        $this_novel_week->year = $this_year;
        $this_novel_week->novel_id = $novel->id;
        $this_novel_week->category_id = $novel->novel_categories_id;
        return $this_novel_week;
    }

    public static function ifNovelMonthEmptySetIt($this_novel_month, $month, $this_year, $novel) {
        if(!empty($this_novel_month)) return $this_novel_month;
        $this_novel_month = new NovelMonth();
        $this_novel_month->month = $month;
        $this_novel_month->year = $this_year;
        $this_novel_month->novel_id = $novel->id;
        $this_novel_month->category_id = $novel->novel_categories_id;
        return $this_novel_month;
    }

}