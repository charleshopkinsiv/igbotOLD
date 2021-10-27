<?php



namespace igbot\scrapers\routine;


use \core\super\Mapper;


class ScrapeRoutineMapper extends Mapper 
{

    private $stub_file = __DIR__ . "/../../../../../data/stubs/scraperoutines.json";
    private $ROUTINES;

    public function fetchAll()
    {

        if(empty($this->ROUTINES)) {

            $this->ROUTINES = json_decode(file_get_contents($this->stub_file), 1);
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

        $ROUTINES = json_decode(file_get_contents($this->stub_file) ,1);

        $ROUTINES[] = [
            'id'            => $this->lastId() + 1,
            'account'       => $Routine->getAccount(),
            'type'          => $Routine->getType(),
            'details'       => $Routine->getDetails(),
            'frequency'     => $Routine->getFrequency(),
            'sequence'      => $Routine->getSequence(),
            'status'        => $Routine->getStatus()
        ];

        file_put_contents($this->stub_file, json_encode($ROUTINES));
    }


    public function update(ScrapeRoutine $Routine)
    {

        $ROUTINES = json_decode(file_get_contents($this->stub_file) ,1);

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

        file_put_contents($this->stub_file, json_encode($ROUTINES));
    }


    public function lastId()
    {

        $largest_id = 0;

        foreach($this->fetchAll() as $Routine) {

            if($Routine->lastId() > $largest_id)
                $largest_id = $Routine->lastId();
        }

        return $largest_id;
    }
}