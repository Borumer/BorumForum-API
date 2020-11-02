<?php 

namespace BorumForum\DBHandlers;

class VoteHandler extends UserKnownHandler {
    /**
     * @var int The id of the question or answer getting voted on
     */
    private $postId;

    /**
     * Constructor for VoteHandler
     * @param string $userApiKey The API key of the user
     * @param int $postId The id of the post
     */
    public function __construct($userApiKey, $postId) {
        parent::__construct($userApiKey);
        $this->postId = $postId;
    }

    public function voteDown($id) {

    }

    public function voteUp($id) {

    }

    /**
     * Find the current number of votes before any changes occur
    */
	function getVotes() {
		$result = mysqli_query($this->conn, "SELECT SUM(vote) FROM `user-message-votes` WHERE message_id = " . $this->postId); 
		$rows = mysqli_fetch_array($result, MYSQLI_NUM);
		return $rows[0];
	}
}

?>