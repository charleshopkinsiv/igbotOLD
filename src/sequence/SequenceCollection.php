<?php

namespace igbot\sequence;

use \core\super\Collection;


class SequenceCollection extends Collection 
{

    public function add(Sequence $Sequence)
    {

        $this->ITEMS[] = $Sequence;
    }

    public function update(Sequence $Sequence)
    {

        foreach($this->ITEMS as $id => $Seq)
            if($Seq->getId() == $Sequence->getId()) {

                $this->ITEMS[$id] = $Seq;
                break;
            }
    }

    public function delete(Sequence $Sequence)
    {

        foreach($this->ITEMS as $id => $Seq)
            if($Seq->getId() == $Sequence->getId()) {

                unset($this->ITEMS[$id]);
                break;
            }
    }
}