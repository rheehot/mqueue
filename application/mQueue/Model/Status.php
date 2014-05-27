<?php

namespace mQueue\Model;

use Zend_Date;
use mQueue\Model\Status as DefaultModelStatus;

/**
 * A status (link between movie and user with a rating)
 */
class Status extends AbstractModel
{

    const Nothing = 0;
    const Need = 1;
    const Bad = 2;
    const Ok = 3;
    const Excellent = 4;
    const Favorite = 5;

    /**
     * array of ratings names indexed by the rating value
     * @var array $ratings
     */
    public static $ratings = null;

    /**
     * Returns the unique ID for this status to be used in HTML
     * @return string
     */
    public function getUniqueId()
    {
        return $this->idMovie . '_' . $this->idUser;
    }

    /**
     * Returns the name
     * @return string
     */
    public function getName()
    {
        if ($this->rating == 0)
            return _tr('Not rated');
        return \mQueue\Model\Status::$ratings[$this->rating];
    }

    /**
     * Returns the date of last udpate
     * @return Zend_Date
     */
    public function getDateUpdate()
    {
        return new Zend_Date($this->dateUpdate, Zend_Date::ISO_8601);
    }

}

// Defines ratings names
\mQueue\Model\Status::$ratings = array(
    DefaultModelStatus::Need => _tr('Need'),
    DefaultModelStatus::Bad => _tr('Bad'),
    DefaultModelStatus::Ok => _tr('Ok'),
    DefaultModelStatus::Excellent => _tr('Excellent'),
    DefaultModelStatus::Favorite => _tr('Favorite'));