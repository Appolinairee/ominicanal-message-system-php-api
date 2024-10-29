<?php

class Message
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function create($conversationId, $senderType, $content)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO messages (conversation_id, sender_type, content) VALUES (:conversation_id, :sender_type, :content)");
            $stmt->bindParam(':conversation_id', $conversationId);
            $stmt->bindParam(':sender_type', $senderType);
            $stmt->bindParam(':content', $content);
            $stmt->execute();

            return ['ok' => true, 'message' => 'Message envoyé avec succès.'];
        } catch (PDOException $e) {
            return [
                'ok' => false,
                'error' => 'Erreur de base de données : ' . $e->getMessage()
            ];
        }
    }

    public function getByConversationId($conversationId)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM messages WHERE conversation_id = :conversation_id ORDER BY created_at ASC");
            $stmt->bindParam(':conversation_id', $conversationId);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [
                'ok' => false,
                'error' => 'Erreur de base de données : ' . $e->getMessage()
            ];
        }
    }

    public function updateReadStatus($messageId, $status)
    {
        try {
            $stmt = $this->db->prepare("UPDATE messages SET read_status = :read_status WHERE id = :id");
            $stmt->bindParam(':read_status', $status);
            $stmt->bindParam(':id', $messageId);
            $stmt->execute();

            return ['ok' => true, 'message' => 'Statut de lecture mis à jour avec succès.'];
        } catch (PDOException $e) {
            return [
                'ok' => false,
                'error' => 'Erreur de base de données : ' . $e->getMessage()
            ];
        }
    }
}
