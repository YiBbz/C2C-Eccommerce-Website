<?php
class Service {
    private $conn;
    private $table = "services";

    public $id;
    public $seller_id;
    public $category_id;
    public $title;
    public $description;
    public $price;
    public $delivery_time;
    public $image;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . "
                SET
                    seller_id = :seller_id,
                    category_id = :category_id,
                    title = :title,
                    description = :description,
                    price = :price,
                    delivery_time = :delivery_time,
                    image = :image,
                    status = :status";

        $stmt = $this->conn->prepare($query);

        // Sanitize and bind parameters
        $stmt->bindParam(":seller_id", $this->seller_id);
        $stmt->bindParam(":category_id", $this->category_id);
        $stmt->bindParam(":title", htmlspecialchars(strip_tags($this->title)));
        $stmt->bindParam(":description", htmlspecialchars(strip_tags($this->description)));
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":delivery_time", $this->delivery_time);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":status", $this->status);

        return $stmt->execute();
    }

    public function read($category_id = null) {
        $query = "SELECT 
                    s.*, u.username as seller_name, c.name as category_name,
                    (SELECT AVG(rating) FROM reviews r WHERE r.service_id = s.id) as average_rating
                FROM 
                    " . $this->table . " s
                    LEFT JOIN users u ON s.seller_id = u.id
                    LEFT JOIN categories c ON s.category_id = c.id
                WHERE 
                    s.status = 'active'";

        if($category_id) {
            $query .= " AND s.category_id = :category_id";
        }

        $stmt = $this->conn->prepare($query);
        
        if($category_id) {
            $stmt->bindParam(":category_id", $category_id);
        }

        $stmt->execute();
        return $stmt;
    }
}
