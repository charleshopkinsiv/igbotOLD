<?php



namespace IgBot\Scrapers\Routine;


use \CharlesHopkinsIV\Core\Super\Mapper;


class ScrapeRoutineMapper extends Mapper 
{

    private $routines_file = __DIR__ . "/../../../data/scraperoutines.json";
    private $ROUTINES;

    public function fetchAll()
    {

        if(empty($this->ROUTINES)) {

            if(file_exists($this->routines_file))
                $this->ROUTINES = json_decode(file_get_contents($this->routines_file), 1);
            if(empty($this->ROUTINES)) $this->ROUTINES = [];

            foreach($this->ROUTINES as $key => $acct) {

                $this->ROUTINES[$key] = new ScrapeRoutine(
                    $acct['id'],
                    $acct['account'],
                    $acct['type'],
                    $acct['details'],
                    $acct['frequency'],
                    $acct['sequence'],
                    $acct['status'],
                );
            }

        }

        return $this->ROUTINES;
    }


    public function insert(ScrapeRoutine $Routine)
    {

        if(file_exists($this->routines_file))
            $ROUTINES = json_decode(file_get_contents($this->routines_file) ,1);
        if(empty($ROUTINES))
            $ROUTINES = [];


        $ROUTINES[] = [
            'id'            => $this->lastId() + 1,
            'account'       => $Routine->getAccount(),
            'type'          => $Routine->getType(),
            'details'       => $Routine->getDetails(),
            'frequency'     => $Routine->getFrequency(),
            'sequence'      => $Routine->getSequence(),
            'status'        => $Routine->getStatus()
        ];

        file_put_contents($this->routines_file, json_encode($ROUTINES));
    }


    public function update(ScrapeRoutine $Routine)
    {

        $ROUTINES = json_decode(file_get_contents($this->routines_file) ,1);

        // Remove past instance of this routine
        foreach($ROUTINES as $key => $ROUTINE) {

            if($ROUTINE['id'] == $Routine->getId()) {

                unset($ROUTINES[$key]);
            }
        }


        // Add new routine and save
        $ROUTINES[] = [
            'id'            => $Routine->getId() + 1,
            'account'       => $Routine->getAccount(),
            'type'          => $Routine->getType(),
            'details'       => $Routine->getDetails(),
            'frequency'     => $Routine->getFrequency(),
            'sequence'      => $Routine->getSequence(),
            'status'        => $Routine->getStatus()
        ];

        file_put_contents($this->routines_file, json_encode($ROUTINES));
    }


    public function deleteById(int $id)
    {

        $ROUTINES = json_decode(file_get_contents($this->routines_file) ,1);

        // Remove past instance of this routine
        foreach($ROUTINES as $key => $ROUTINE) {

            if($ROUTINE['id'] == $id) {

                unset($ROUTINES[$key]);
            }
        }

        file_put_contents($this->routines_file, json_encode($ROUTINES));
    }


    public function lastId()
    {

        $largest_id = 0;

        foreach($this->fetchAll() as $Routine) {

            if($Routine->getId() > $largest_id)
                $largest_id = $Routine->getId();
        }

        return $largest_id;
    }
}