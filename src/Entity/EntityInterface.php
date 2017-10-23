<?php
/**
 * Created by PhpStorm.
 * User: alexeydorofeev
 * Date: 20/10/2017
 * Time: 08:22
 */

namespace Repository\Entity;

interface EntityInterface
{
    /**
     * @param mixed $id
     * @return EntityInterface
     */
    public function setId($id);

    /**
     * @return mixed
     */
    public function getId();
}
