<?php
////////////////////////////////////////////////////////////
//
//  Save a sequence collection for each account
//
////////////////////////////////////////////////////////////

namespace igbot\sequence;


class SequenceDataMapper
{

    private static $sequence_file = __DIR__ . "/../../data/sequences.bin";

    private SequenceCollection $Collection;

    public function __construct()
    {

        

    }


    public function insert(Sequence $Sequence)
    {

        $Collection = $this->fetchAll();
        $Collection->add($Sequence);
        $this->saveCollection($Collection);
    }


    public function update(Sequence $UpdatedSequence)
    {

        $Collection = $this->fetchAll();
        foreach($Collection as $Sequence) {
            if($Sequence->getId() == $UpdatedSequence->getId()) {

                $Collection->update($UpdatedSequence);
                $this->saveCollection($Collection);
                break;
            }
        }
    }


    public function fetchAll() : SequenceCollection
    {

        

        if(empty($this->SequenceCollection))
            if(is_file(self::$sequence_file))
                $this->SequenceCollection = unserialize(file_get_contents(self::$sequence_file));
            else
                $this->SequenceCollection = new SequenceCollection;

        return $this->SequenceCollection;
    }


    public function saveCollection(SequenceCollection $Collection)
    {

        file_put_contents(self::$sequence_file, serialize($Collection));
    }


    public function nextId()
    {

        $largest_id = 0;
        foreach($this->fetchAll() as $Sequence)
            if($Sequence->getId() > $largest_id)
                $largest_id = $Sequence->getId();

        return $largest_id + 1;
    }
}