
<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/db.php";

$dialect = $_GET["dialect"] ?? "";

if (empty($dialect)) {
    echo json_encode([
        "error" => "Please provide a dialect."
    ]);
    exit;
}

$sql = "
    SELECT 
        w.*,
        c.category_name,
        d.dialect_name
    FROM words w
    JOIN categories c 
        ON w.category_id = c.category_id
    JOIN dialect d 
        ON w.dialect_id = d.dialect_id
    WHERE d.dialect_name = ?
";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute([$dialect]);

    $words = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($words, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    error_log("Words query failed: " . $e->getMessage());
    http_response_code(500);

    echo json_encode([
        "error" => "Unable to retrieve words."
    ]);
}

?>
