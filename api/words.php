<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "db.php";

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

$stmt = $conn->prepare($sql);

$stmt->bind_param("s", $dialect);

$stmt->execute();

$result = $stmt->get_result();

$words = [];

while ($row = $result->fetch_assoc()) {
    $words[] = $row;
}

echo json_encode($words, JSON_UNESCAPED_UNICODE);

$stmt->close();
$conn->close();

?>