<?php
class Order {
    private $conn;
    private $table = "orders";

    public $id;
    public $service_id;
    public $buyer_id;
    public $seller_id;
    public $status;
    public $price;
    public $requirements;
    public $delivery_date;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . "
                SET
                    service_id = :service_id,
                    buyer_id = :buyer_id,
                    seller_id = :seller_id,
                    price = :price,
                    requirements = :requirements,
                    delivery_date = DATE_ADD(CURRENT_DATE, INTERVAL :delivery_time DAY)";

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(":service_id", $this->service_id);
        $stmt->bindParam(":buyer_id", $this->buyer_id);
        $stmt->bindParam(":seller_id", $this->seller_id);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":requirements", htmlspecialchars(strip_tags($this->requirements)));
        $stmt->bindParam(":delivery_time", $this->delivery_time);

        return $stmt->execute();
    }

    public function getUserOrders($user_id, $user_type) {
        $query = "SELECT 
                    o.*, s.title as service_title,
                    CASE 
                        WHEN :user_type = 'buyer' THEN seller.username
                        ELSE buyer.username
                    END as other_party
                FROM 
                    " . $this->table . " o
                    LEFT JOIN services s ON o.service_id = s.id
                    LEFT JOIN users buyer ON o.buyer_id = buyer.id
                    LEFT JOIN users seller ON o.seller_id = seller.id
                WHERE 
                    " . ($user_type == 'buyer' ? 'o.buyer_id' : 'o.seller_id') . " = :user_id
                ORDER BY 
                    o.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":user_type", $user_type);
        $stmt->execute();

        return $stmt;
    }
}
