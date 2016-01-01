<?php
/**
 * Created by PhpStorm.
 * User: bratfisch
 * Date: 29.12.2015
 * Time: 14:39
 */

namespace Simplified\Core;

interface ContainerInterface {
    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     * @return mixed Entry.
     */
    public function get($id);

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return boolean
     */
    public function has($id);
}