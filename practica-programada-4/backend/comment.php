<?php
require('db.php');

function createComment($userId, $taskId, $description) {
    global $pdo;
    try {
        $sql = "INSERT INTO comments (user_id, task_id, description) VALUES (:user_id, :task_id, :description)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'task_id' => $taskId,
            'description' => $description
        ]);
        return $pdo->lastInsertId();
    } catch (Exception $e) {
        return 0;
    }
}

function getCommentsByTask($taskId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM comments WHERE task_id = :task_id");
        $stmt->execute(['task_id' => $taskId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function updateComment($commentId, $userId, $description) {
    global $pdo;
    try {
        $sql = "UPDATE comments SET description = :description WHERE id = :id AND user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'description' => $description,
            'id' => $commentId,
            'user_id' => $userId
        ]);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}

function deleteComment($commentId, $userId) {
    global $pdo;
    try {
        $sql = "DELETE FROM comments WHERE id = :id AND user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $commentId,
            'user_id' => $userId
        ]);
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}

$method = $_SERVER['REQUEST_METHOD'];
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(["error" => "Sesión no activa"]);
    exit;
}

$userId = $_SESSION["user_id"];

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['task_id'])) {
                $comments = getCommentsByTask($_GET['task_id']);
                echo json_encode($comments);
            } else {
                http_response_code(400);
                echo json_encode(["error" => "Se requiere task_id"]);
            }
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            if (isset($input['task_id'], $input['description'])) {
                $commentId = createComment($userId, $input['task_id'], $input['description']);
                if ($commentId > 0) {
                    http_response_code(201);
                    echo json_encode(["message" => "Comentario creado"]);
                } else {
                    http_response_code(500);
                    echo json_encode(["error" => "Error creando comentario"]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["error" => "Datos incompletos"]);
            }
            break;
            
        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true);
            if (isset($_GET['id'], $input['description'])) {
                $success = updateComment($_GET['id'], $userId, $input['description']);
                if ($success) {
                    echo json_encode(["message" => "Comentario actualizado"]);
                } else {
                    http_response_code(404);
                    echo json_encode(["error" => "Comentario no encontrado"]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["error" => "Datos inválidos"]);
            }
            break;
            
        case 'DELETE':
            if (isset($_GET['id'])) {
                $success = deleteComment($_GET['id'], $userId);
                if ($success) {
                    echo json_encode(["message" => "Comentario eliminado"]);
                } else {
                    http_response_code(404);
                    echo json_encode(["error" => "Comentario no encontrado"]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["error" => "ID requerido"]);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(["error" => "Método no permitido"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error interno del servidor"]);
}