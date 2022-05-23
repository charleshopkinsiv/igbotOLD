<?php
////////////////////////////////////////////////////////////////////////////////////////
//
//  Instagram Account Limiter
//  Will track account bans
//
////////////////////////////////////////////////////////////////////////////////////////


namespace IgBot\Account;

use IgBot\Task\Task;


class AccountLimiter
{

    private Account $Account;
    private bool $active = true;
    private int $max_hourly_requests;
    private int $max_daily_requests;
    private int $max_daily_messages;
    private Array $BANS = [];
    private Array $ACTIVITY = []; // Store 30 days
    //     "Month" => [
    //         "Day" => [
    //             "Hour" => [
    //                  "type(view,message,comment)" => (int) count
    //              ]
    //         ]
    //     ]
    // ];

    private static $activity_dir = __DIR__ . "/../../data/account_limits/";

    private static array $limits = [
        "view" => [
            "daily" => 100,
            "hourly" => 30
        ],
        "message" => [
            "daily" => 20,
            "hourly" => 4
        ]
    ];

    public function __construct(Account $Account)
    {

        $this->Account                  = $Account;
        $this->max_hourly_requests      = 20;
        $this->max_daily_requests       = 200;
        $this->max_daily_messages       = 15;
        $this->ACTIVITY                 = $this->loadActivity();
    }


    private function loadActivity()
    {

        $activity   = [];
        $file_name  = self::$activity_dir . $this->Account->getUsername();

        if(is_file($file_name))
            $activity = json_decode(file_get_contents($file_name), 1);

        return $activity;
    }


    private function saveActivity()
    {

        $file_name  = self::$activity_dir . $this->Account->getUsername();
        file_put_contents($file_name, json_encode($this->ACTIVITY));
    }


    public function getHourCount($type = "view") : int
    {

        if(isset($ACTIVITY[date('m')][date('h')][date('H')][$type]))
            return $ACTIVITY[date('m')][date('h')][date('H')][$type];
        else
            return 0;
    }

    
    public function getDayCount($type = "view") : int
    {

        if(isset($ACTIVITY[date('m')][date('d')])) {

            $count = 0;

            foreach($ACTIVITY[date('m')][date('d')] as $hour) {

                if(!empty($hour[$type])) {

                    $count += $hour[$type];
                }
            }
        
            return $count;
        }
        else {

            return 0;
        }
    }


    public function leftForHour(Task $Task)
    {

        return $this->getTaskLimits($Task)['hourly'] - $this->getCountForHour($Task);
    }


    public function leftForDay(Task $Task)
    {

        return $this->getTaskLimits($Task)['daily'] - $this->getCountForDay($Task);
    }


    public function getCountForDay(Task $task)
    {

        if(empty($this->ACTIVITY[date('m')][date('d')]))
            return 0;

        $count = 0;

        foreach($this->ACTIVITY[date('m')][date('d')] as $hour) {

            foreach($hour as $type => $hour_count) {

                if($type == self::getLimitType($task))
                    $count += $hour_count;
            }
        }

        return $count;
    }


    public function getCountForHour(Task $task)
    {

        $count = 0;

        if(empty($this->ACTIVITY[date('m')][date('d')][date('H')]))
            return 0;

        foreach($this->ACTIVITY[date('m')][date('d')][date('H')] as $type => $hour_count) {

            if($type == self::getLimitType($task))
                $count += $hour_count;
        }

        return $count;
    }

    
    public function increment($type = "view")
    {

        if(isset($this->ACTIVITY[date('m')][date('d')][date('H')][$type]))
            $this->ACTIVITY[date('m')][date('d')][date('H')][$type]++;
        else
            $this->ACTIVITY[date('m')][date('d')][date('H')][$type] = 1;

        $this->saveActivity();
    }


    public function getTaskLimits(Task $Task)
    {

        return self::$limits[self::getLimitType($Task)];
    }


    public static function getLimitType(Task $Task)
    {

        $limit_type = 'view';

        if($Task->getTaskType() == "Action") {

            switch($Task->getTitle()) {

                case "Send Message":
                    $limit_type = 'message';
                    break;
            }
        }

        return $limit_type;
    }


    public function overLimit(Task $Task) : bool
    {

        if($this->LeftForHour($Task) > 0  
        && $this->leftForDay($Task) > 0)
            return false;
        else
            return true;
    }
}