<?php
namespace  Marbobley\EntitiesVisitorBundle;

final class VisitorCounter{

    public function incrementCounter(int $i) : int
    {
        $i++;
        return $i;
    }
}
