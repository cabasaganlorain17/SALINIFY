
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
        mp.pair_id,
        mp.word_id,
        w.word,
        mp.match_text
    FROM matching_pairs mp
    JOIN quizzes q
        ON mp.quiz_id = q.quiz_id
    JOIN dialect d
        ON q.dialect_id = d.dialect_id
    JOIN words w
        ON mp.word_id = w.word_id
    WHERE d.dialect_name = ?
      AND q.game_type = 'Matching Type'
    ORDER BY mp.pair_id
";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute([$dialect]);

    $pairs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($pairs, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    error_log("Matching query failed: " . $e->getMessage());
    http_response_code(500);

    echo json_encode([
        "error" => "Unable to retrieve matching pairs."
    ]);
}

?>
