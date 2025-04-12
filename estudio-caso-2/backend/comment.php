<?php
// Incluye el archivo de conexión a la base de datos
require('db.php');

/**
 * Crea un nuevo comentario en la base de datos
 * @param int $userId ID del usuario que crea el comentario
 * @param int $taskId ID de la tarea asociada
 * @param string $description Contenido del comentario
 * @return int ID del comentario creado o 0 si falla
 */
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
        return $pdo->lastInsertId(); // Retorna el ID del nuevo comentario
    } catch (Exception $e) {
        return 0; // Retorna 0 si hay error
    }
}

/**
 * Obtiene todos los comentarios de una tarea específica
 * @param int $taskId ID de la tarea
 * @return array Lista de comentarios o array vacío si falla
 */
function getCommentsByTask($taskId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM comments WHERE task_id = :task_id");
        $stmt->execute(['task_id' => $taskId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retorna todos los resultados
    } catch (Exception $e) {
        return []; // Retorna array vacío si hay error
    }
}

/**
 * Actualiza el contenido de un comentario existente
 * @param int $commentId ID del comentario a actualizar
 * @param int $userId ID del usuario (para validar ownership)
 * @param string $description Nuevo contenido del comentario
 * @return bool True si se actualizó, False si falló
 */
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
        return $stmt->rowCount() > 0; // Retorna true si se afectó alguna fila
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Elimina un comentario específico
 * @param int $commentId ID del comentario a eliminar
 * @param int $userId ID del usuario (para validar ownership)
 * @return bool True si se eliminó, False si falló
 */
function deleteComment($commentId, $userId) {
    global $pdo;
    try {
        $sql = "DELETE FROM comments WHERE id = :id AND user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $commentId,
            'user_id' => $userId
        ]);
        return $stmt->rowCount() > 0; // Retorna true si se eliminó alguna fila
    } catch (Exception $e) {
        return false;
    }
}

// Configuración inicial del API
$method = $_SERVER['REQUEST_METHOD']; // Obtiene el método HTTP (GET, POST, etc)
header('Content-Type: application/json'); // Establece el tipo de respuesta como JSON
session_start(); // Inicia la sesión para validar autenticación

// Validación de sesión activa
if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    echo json_encode(["error" => "Sesion no activa"]);
    exit;
}

$userId = $_SESSION["user_id"]; // ID del usuario autenticado

// Manejo de las diferentes solicitudes HTTP
try {
    switch ($method) {
        case 'GET':
            // Obtener comentarios de una tarea específica
            if (isset($_GET['task_id'])) {
                $comments = getCommentsByTask($_GET['task_id']);
                echo json_encode($comments);
            } else {
                http_response_code(400);
                echo json_encode(["error" => "Se requiere task_id"]);
            }
            break;
            
        case 'POST':
            // Crear un nuevo comentario
            $input = json_decode(file_get_contents('php://input'), true);
            if (isset($input['task_id'], $input['description'])) {
                $commentId = createComment($userId, $input['task_id'], $input['description']);
                if ($commentId > 0) {
                    http_response_code(201); // Código 201: Created
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
            // Actualizar un comentario existente
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
            // Eliminar un comentario
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
            // Método HTTP no soportado
            http_response_code(405);
            echo json_encode(["error" => "Método no permitido"]);
    }
} catch (Exception $e) {
    // Error interno del servidor
    http_response_code(500);
    echo json_encode(["error" => "Error interno del servidor"]);
}