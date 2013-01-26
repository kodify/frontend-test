<?php

namespace Kodify\AdminBundle\Extension;

class TimeAgoExtension extends \Twig_Extension {

    public function getFilters() {
        return array(
            'timeAgo'  => new \Twig_Filter_Method($this, 'timeAgo'),
        );
    }

    public function timeAgo(\DateTime $value)
    {
        $time = time() - $value->getTimestamp();

        $tokens = array (
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );

        foreach ($tokens as $unit => $text) {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
        }
    }

    public function getName()
    {
        return 'time_ago_extension';
    }

}