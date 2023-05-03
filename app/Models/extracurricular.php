<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class extracurricular extends Model
{
    use HasFactory;
    // protected $fillable = [''];
    protected $guarded = ['kdExtracurricular'];
    protected $with = [
        'members',
        'schedules',
        'latest_schedule',
        'leader'
    ];

    public function scopeFilter($query, array $filters){
        $query->when($filters['search'] ?? false, fn($query, $search) =>
            $query->where(fn($query) =>
                $query->where('name', 'like', '%' . $search . '%')
            // )->orwhereHas('latest_schedule', fn($query) =>
                // $query->where('location', 'like', '%' . $search . '%')->first()
                    // ->orWhere('date', 'like', '%' . $search . '%')
                    // ->orWhere('timeStart', 'like', '%' . $search . '%')
                    // ->orWhere('timeEnd', 'like', '%' . $search . '%')
            )->orwhereHas('leader', fn($query) =>
                $query->whereHas('userXmas', fn($query) =>
                    $query->where('name', 'like', '%' . $search . '%')
            ))
        );

        if((isset($filters['Physique']) && isset($filters['NonPhysique'])) === false){
            $query->when($filters['Physique'] ?? false, fn($query) =>
                $query->where('category', 'like', 'Physique')
            );

            $query->when($filters['NonPhysique'] ?? false, fn($query) =>
                $query->where('category', 'like', 'Non-Physique')
            );
        }

        // SELECT *
        // FROM
        //     (SELECT  `extracurriculars`.kdextracurricular,`extracurriculars`.name, DATE_FORMAT(MAX(`schedules`.`date`), '%a') AS `date_max`
        //     FROM  `schedules`
        //     JOIN `extracurriculars` ON `extracurriculars`.`kdExtracurricular` = `schedules`.`kdExtracurricular`
        //     GROUP BY `extracurriculars`.kdextracurricular DESC) AS `sched_max`
        // WHERE `date_max` = 'mon';
        // $data_sched = DB::table("extracurriculars")->select("*", DB::raw("(SELECT  *, DATE_FORMAT(MAX(`schedules`.`date`), '%a') AS `date_max`
        //                                                     FROM  `schedules`
        //                                                     JOIN `extracurriculars` ON `extracurriculars`.`kdExtracurricular` = `schedules`.`kdExtracurricular`
        //                                                     GROUP BY `extracurriculars`.kdextracurricular DESC) AS `sched_max`"))
        //                                         ->where('date_max', '=', 'mon');
        $day = [];
        if((isset($filters['Mon']) || isset($filters['Tue']) || isset($filters['Wed']) || isset($filters['Thu']) || isset($filters['Fri']) || isset($filters['Sat']) || isset($filters['Sun'])) === true){
            if(isset($filters['Mon']) === true){
                $day[] = ['Mon'];
            }
            if(isset($filters['Tue']) === true){
                $day[] = ['Tue'];
            }
            if(isset($filters['Wed']) === true){
                $day[] = ['Wed'];
            }
            if(isset($filters['Thu']) === true){
                $day[] = ['Thu'];
            }
            if(isset($filters['Fri']) === true){
                $day[] = ['Fri'];
            }
            if(isset($filters['Sat']) === true){
                $day[] = ['Sat'];
            }
            if(isset($filters['Sun']) === true){
                $day[] = ['Sun'];
            }
            $query->whereIn('kdExtracurricular', fn($query) =>
                $query->select('kdExtracurricular')
                    ->from(fn($query) =>
                            $query->select(DB::raw(" `extracurriculars`.kdextracurricular, DATE_FORMAT(MAX(schedules.date), '%a') AS date_max"))
                                ->from('schedules')
                                ->JOIN('extracurriculars', 'extracurriculars.kdExtracurricular', '=', 'schedules.kdExtracurricular')
                                ->groupBy('extracurriculars.kdExtracurricular')
                    )->whereIn('date_max', $day)->reorder('kdExtracurricular')
            );
        }

        // if((isset($filters['Mon']) && isset($filters['Tue']) && isset($filters['Wed']) && isset($filters['Thu']) && isset($filters['Fri']) && isset($filters['Sat']) && isset($filters['Sun'])) === false){
        //     $query->when($filters['Mon'] ?? false, fn($query) =>
        //         $query->whereIn('kdExtracurricular', fn($query) =>
        //             $query->select('kdExtracurricular')
        //                 ->from(fn($query) =>
        //                         $query->select(DB::raw(" `extracurriculars`.kdextracurricular, DATE_FORMAT(MAX(schedules.date), '%a') AS date_max"))
        //                             ->from('schedules')
        //                             ->JOIN('extracurriculars', 'extracurriculars.kdExtracurricular', '=', 'schedules.kdExtracurricular')
        //                             ->groupBy('extracurriculars.kdExtracurricular')
        //                 )->where('date_max', 'IN', 'Mon')->reorder('kdExtracurricular')
        //         )
        //     );
        //     $query->when($filters['Tue'] ?? false, fn($query) =>
        //         $query->whereIn('kdExtracurricular', fn($query) =>
        //             $query->select('kdExtracurricular')
        //                 ->from(fn($query) =>
        //                         $query->select(DB::raw(" `extracurriculars`.kdextracurricular, DATE_FORMAT(MAX(schedules.date), '%a') AS date_max"))
        //                             ->from('schedules')
        //                             ->JOIN('extracurriculars', 'extracurriculars.kdExtracurricular', '=', 'schedules.kdExtracurricular')
        //                             ->groupBy('extracurriculars.kdExtracurricular')
        //                 )->whereRaw("`date_max` = 'Tue'")->reorder('kdExtracurricular')
        //         )
        //     );
        //     $query->when($filters['Wed'] ?? false, fn($query) =>
        //         $query->whereIn('kdExtracurricular', fn($query) =>
        //             $query->select('kdExtracurricular')
        //                 ->from(fn($query) =>
        //                         $query->select(DB::raw(" `extracurriculars`.kdextracurricular, DATE_FORMAT(MAX(schedules.date), '%a') AS date_max"))
        //                             ->from('schedules')
        //                             ->JOIN('extracurriculars', 'extracurriculars.kdExtracurricular', '=', 'schedules.kdExtracurricular')
        //                             ->groupBy('extracurriculars.kdExtracurricular')
        //                 )->whereRaw("`date_max` = 'Wed'")->reorder('kdExtracurricular')
        //         )
        //     );
        //     $query->when($filters['Thu'] ?? false, fn($query) =>
        //         $query->whereIn('kdExtracurricular', fn($query) =>
        //             $query->select('kdExtracurricular')
        //                 ->from(fn($query) =>
        //                         $query->select(DB::raw(" `extracurriculars`.kdextracurricular, DATE_FORMAT(MAX(schedules.date), '%a') AS date_max"))
        //                             ->from('schedules')
        //                             ->JOIN('extracurriculars', 'extracurriculars.kdExtracurricular', '=', 'schedules.kdExtracurricular')
        //                             ->groupBy('extracurriculars.kdExtracurricular')
        //                 )->whereRaw("`date_max` = 'Thu'")->reorder('kdExtracurricular')
        //         )
        //     );
        //     $query->when($filters['Fri'] ?? false, fn($query) =>
        //         $query->whereIn('kdExtracurricular', fn($query) =>
        //             $query->select('kdExtracurricular')
        //                 ->from(fn($query) =>
        //                         $query->select(DB::raw(" `extracurriculars`.kdextracurricular, DATE_FORMAT(MAX(schedules.date), '%a') AS date_max"))
        //                             ->from('schedules')
        //                             ->JOIN('extracurriculars', 'extracurriculars.kdExtracurricular', '=', 'schedules.kdExtracurricular')
        //                             ->groupBy('extracurriculars.kdExtracurricular')
        //                 )->whereRaw("`date_max` = 'Fri'")->reorder('kdExtracurricular')
        //         )
        //     );
        //     $query->when($filters['Sat'] ?? false, fn($query) =>
        //         $query->whereIn('kdExtracurricular', fn($query) =>
        //             $query->select('kdExtracurricular')
        //                 ->from(fn($query) =>
        //                         $query->select(DB::raw(" `extracurriculars`.kdextracurricular, DATE_FORMAT(MAX(schedules.date), '%a') AS date_max"))
        //                             ->from('schedules')
        //                             ->JOIN('extracurriculars', 'extracurriculars.kdExtracurricular', '=', 'schedules.kdExtracurricular')
        //                             ->groupBy('extracurriculars.kdExtracurricular')
        //                 )->whereRaw("`date_max` = 'Sat'")->reorder('kdExtracurricular')
        //         )
        //     );
        //     $query->when($filters['Sun'] ?? false, fn($query) =>
        //         $query->whereIn('kdExtracurricular', fn($query) =>
        //             $query->select('kdExtracurricular')
        //                 ->from(fn($query) =>
        //                         $query->select(DB::raw(" `extracurriculars`.kdextracurricular, DATE_FORMAT(MAX(schedules.date), '%a') AS date_max"))
        //                             ->from('schedules')
        //                             ->JOIN('extracurriculars', 'extracurriculars.kdExtracurricular', '=', 'schedules.kdExtracurricular')
        //                             ->groupBy('extracurriculars.kdExtracurricular')
        //                 )->whereRaw("`date_max` = 'Sun'")->reorder('kdExtracurricular')
        //         )
        //     );
        // } elseif((isset($filters['Mon']) && isset($filters['Tue']) && isset($filters['Wed']) && isset($filters['Thu']) && isset($filters['Fri']) && isset($filters['Sat']) && isset($filters['Sun'])) === false){

        // }
    }

    public function members(){
        return $this->hasMany(member::class, 'kdExtracurricular', 'kdExtracurricular');
    }

    public function leader(){
        return $this->hasOne(member::class, 'kdExtracurricular', 'kdExtracurricular')->where('kdState', '=', 2);
        // return $this->hasOneThrough(userXmas::class, member::class, 'kdExtracurricular', 'NIP', 'kdExtracurricular', 'kdMember')->where('kdState', '=', 2);
    }

    public function documentations(){
        return $this->hasMany(documentation::class, 'kdExtracurricular', 'kdExtracurricular');
    }

    public function schedules(){
        return $this->hasMany(schedule::class, 'kdExtracurricular', 'kdExtracurricular')->orderBy('date', 'DESC');
    }

    public function latest_schedule(){
        return $this->hasOne(schedule::class, 'kdExtracurricular', 'kdExtracurricular')->latest('date')->latest('timeStart');
        // return $this->schedules()->one()->latestOfMany();
    }
}
