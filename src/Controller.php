<?php
/**
 * Created by PhpStorm.
 * User: subtext
 * Date: 2/1/18
 * Time: 1:03 PM
 */

namespace Subtext\Garbage;


class Controller
{

    /**
     * @param string $request
     */
    public function execute(string $request)
    {
        echo "Bandit is a good and loving puppy!<br />";
        echo $request;
    }

}
