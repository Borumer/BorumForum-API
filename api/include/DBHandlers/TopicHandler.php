<?php 

namespace BorumForum\DBHandlers;

class TopicHandler extends UserKnownHandler implements Deleteable {
    public function __construct($userApiKey) {
        parent::__construct($userApiKey);
    }

    /**
     * @param int $id A natural number within 11 bytes representing the id of the topocq
     */
    public function delete($id) {
        $this->executeQuery("DELETE FROM topics WHERE id = $id");

        if (mysqli_affected_rows($this->conn) == 1) {
            return [
                "statusCode" => 200
            ];
        } else {
            return [
                "statusCode" => 404,
                "error" => [
                    "message" => "That topic does not exist"
                ]
            ];
        }
    }

    public function follow($id) {

    }

    public function ignore($id) {

    }
}

?>