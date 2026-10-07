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

$stmt = $conn->prepare($sql);

$stmt->bind_param("ss", $dialect, $type);

$stmt->execute();

$result = $stmt->get_result();

$questions = [];

while ($row = $result->fetch_assoc()) {

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

$questions = array_values($questions);

echo json_encode($questions, JSON_UNESCAPED_UNICODE);

$stmt->close();
$conn->close();

?>