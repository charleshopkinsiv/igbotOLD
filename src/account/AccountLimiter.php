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
    private Boolean $active = true;
    private Array $BANS = [];
    private Array $ACTIVITY = [ // Store 30 days
        "Month" => [
            "Day" => [
                "Hour" => "Count"
            ]
        ]
    ];

    public function __construct(Account $Account)
    {

        $this->Account = $Account;
    }
    
}