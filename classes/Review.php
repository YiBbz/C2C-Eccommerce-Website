<?php
class Review {
    private $conn;
    private $table = "reviews";

    public $id;
    public $order_id;
    public $service_id;
    public $reviewer_id;
    public $rating;
    public $comment;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . "
                SET
                    order_id = :order_id,
                    service_id = :service_id,
                    reviewer_id = :reviewer_id,
                    rating = :rating,
                    comment = :comment";

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(":order_id", $this->order_id);
        $stmt->bindParam(":service_id", $this->service_id);
        $stmt->bindParam(":reviewer_id", $this->reviewer_id);
        $stmt->bindParam(":rating", $this->rating);
        $stmt->bindParam(":comment", htmlspecialchars(strip_tags($this->comment)));

        return $stmt->execute();
    }

    public function getServiceReviews($service_id) {
        $query = "SELECT 
                    r.*, u.username as reviewer_name
                FROM 
                    " . $this->table . " r
                    LEFT JOIN users u ON r.reviewer_id = u.id
                WHERE 
                    r.service_id = :service_id
                ORDER BY 
                    r.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":service_id", $service_id);
        $stmt->execute();

        return $stmt;
    }
}
