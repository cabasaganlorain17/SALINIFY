
<?php

header("Content-Type: application/json; charset=UTF-8");

require_once "db.php";

$dialect = $_GET["dialect"] ?? "";
$type = $_GET["type"] ?? "";

if (empty($dialect) || empty($type)) {
    echo json_encode([
        "error" => "Please provide a dialect and quiz type."
    ]);
    exit;
}

$sql = "
    SELECT
        q.question_id,
        q.question_text,
        q.hint,
        o.option_id,
        o.option_text,
        o.is_correct
    FROM quizzes z
    JOIN dialect d
        ON z.dialect_id = d.dialect_id
    JOIN questions q
        ON z.quiz_id = q.quiz_id
    JOIN options o
        ON q.question_id = o.question_id
    WHERE d.dialect_name = ?
      AND z.game_type = ?
    ORDER BY q.question_id, o.option_id
";

try {
    $stmt = $conn->prepare($sql);

    $stmt->execute([$dialect, $type]);

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $questions = [];

    foreach ($rows as $row) {
        $questionId = $row["question_id"];

        if (!isset($questions[$questionId])) {
            $questions[$questionId] = [
                "question_id" => $questionId,
                "question" => $row["question_text"],
                "hint" => $row["hint"],
                "options" => []
            ];
        }

        $questions[$questionId]["options"][] = [
            "option_id" => $row["option_id"],
            "text" => $row["option_text"],
            "is_correct" => $row["is_correct"]
        ];
    }

    echo json_encode(
        array_values($questions),
        JSON_UNESCAPED_UNICODE
    );

} catch (PDOException $e) {
    error_log("Quiz query failed: " . $e->getMessage());

    http_response_code(500);

    echo json_encode([
        "error" => "Unable to retrieve quiz questions."
    ]);
}

?>
