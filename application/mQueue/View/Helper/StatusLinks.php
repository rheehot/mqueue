<?php

namespace mQueue\View\Helper;

use Zend_View_Helper_Abstract;
use \mQueue\Model\Status;
use \mQueue\Model\User;

class StatusLinks extends Zend_View_Helper_Abstract
{

    /**
     * Returns the set of links to display a status (the icons used to rate movies)
     * @param \mQueue\Model\Status $status
     * @return string
     */
    public function statusLinks(\mQueue\Model\Status $status)
    {
        $result = '<div class="mqueue_status_links mqueue_status_links_' . $status->getUniqueId() . '">';
        $user = User::getCurrent();

        // Deactivate links if no logged user
        if ($user)
            $tag = 'a';
        else
            $tag = 'span';

        foreach (\mQueue\Model\Status::$ratings as $val => $name) {
            $class = $val . ($status->rating == $val ? ' current' : '');
            $url = $this->view->serverUrl() . $this->view->url(array(
                        'controller' => 'status',
                        'movie' => $status->idMovie,
                        'rating' => ($val == $status->rating && $user && $user->id == $status->idUser) ? 0 : $val
                            ), 'status', true);

            $result .= '<' . $tag . ' class="mqueue_status mqueue_status_' . $class . '"' . ($tag == 'a' ? ' href="' . $url . '"' : '') . ' title="' . $name . '"><span>' . $name . '</span></' . $tag . '>';
        }
        $result .= '<span class="preloader"></span></div>';

        return $result;
    }

}