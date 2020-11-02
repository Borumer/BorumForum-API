<?php 

namespace BorumForum\DBHandlers;

class QuestionHandler extends PostHandler implements Deleteable {
    public function __construct($userApiKey) {
        parent::__construct($userApiKey);
    }

    public function delete($id) {
        
    }

    /**
     * Votes up a question by inserting/updating it into the `user-message-votes` table
     * @param int $id The id of the question that the user is voting up
     */
    public function voteUp($id) {

    }

    /**
     * Votes down a question by inserting/updating it into the `user-message-votes` table
     * @param int $id The id of the question that the user is voting down
     */
    public function voteDown($id) {

    }
}

?>