<?php 

namespace BorumForum\DBHandlers;

class CommentHandler extends UserKnownHandler implements PerpetuallyTemporary {
    function __construct($userApiKey) {
        parent::__construct($userApiKey);
    }

    public function create($data) {

    }

    public function delete($id) {
        if (!is_numeric($id)) {
            return [
                "statusCode" => 400,
                "error" => [
                    "message" => "id must be numeric"
                ]
            ];
        }

        $this->executeQuery("DELETE FROM comments WHERE id = $id AND user_id = " . $this->userId . " LIMIT 1");

        if (mysqli_affected_rows($this->conn) == 1) {
            return [
                "statusCode" => 200
            ];
        } else {
            return [
                "statusCode" => 404,
                "error" => [
                    "message" => "That comment does not exist or you did not make that comment"
                ]
            ];
        }
    }

    public function edit($id, $newBody) {

    }
}

?>