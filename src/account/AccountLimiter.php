<?php
////////////////////////////////////////////////////////////////////////////////////////
//
//  Instagram Account Limiter
//  Will track account bans
//
////////////////////////////////////////////////////////////////////////////////////////


namespace igbot\account;


class AccountLimiter
{

    private Account $Account;
    private bool $active = true;
    private int $max_hourly_requests;
    private int $max_daily_requests;
    private Array $BANS = [];
    private Array $ACTIVITY = []; // Store 30 days
    //     "Month" => [
    //         "Day" => [
    //             "Hour" => "Count"
    //         ]
    //     ]
    // ];

    public function __construct(Account $Account)
    {

        $this->Account = $Account;
        $this->max_hourly_requests      = 20;
        $this->max_daily_requests       = 200;
    }

    public function getHourCount() : int
    {

        if(isset($ACTIVITY[date('m')][date('h')][date('H')]))
            return $ACTIVITY[date('m')][date('h')][date('H')];
        else
            return 0;
    }

    public function getDayCount() : int
    {

        if(isset($ACTIVITY[date('m')][date('d')]))
            return $ACTIVITY[date('m')][date('d')];
        else
            return 0;
    }
    
    public function increment()
    {
        if(isset($this->ACTIVITY[date('m')][date('d')][date('H')]))
            $this->ACTIVITY[date('m')][date('d')][date('H')]++;
        else
            $this->ACTIVITY[date('m')][date('d')][date('H')] = 1;
    }

    public function overLimit() : bool
    {

        if($this->getHourCount() >= $this->max_hourly_requests 
        ||$this->getDayCount() >= $this->max_daily_requests)
            return true;
        else
            return false;
    }
}