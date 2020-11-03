<?php 

namespace BorumForum\DBHandlers;

/**
 * Interface for creatable and deleteable objects
 */
interface PerpetuallyTemporary {
    public function create($data);

    public function delete($id);
}

?>