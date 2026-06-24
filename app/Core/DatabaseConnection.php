<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * ╔══════════════════════════════════════════════════════════════╗
 * ║  SINGLETON PATTERN — DatabaseConnection                     ║
 * ║                                                              ║
 * ║  Đảm bảo chỉ có MỘT instance kết nối database trong        ║
 * ║  toàn bộ vòng đời của ứng dụng (1 HTTP request).            ║
 * ║                                                              ║
 * ║  Lợi ích:                                                    ║
 * ║  • Tránh mở quá nhiều connection đến MySQL                  ║
 * ║  • Tiết kiệm tài nguyên server                               ║
 * ║  • Đảm bảo nhất quán trong cùng một transaction             ║
 * ║                                                              ║
 * ║  Trong Laravel thực tế, Singleton được implement qua         ║
 * ║  ServiceProvider. Class này minh họa pattern thuần PHP      ║
 * ║  để phục vụ tài liệu Chương 5.                              ║
 * ╚══════════════════════════════════════════════════════════════╝
 */
class DatabaseConnection
{
    // Bước 1: Thuộc tính static giữ instance duy nhất
    private static ?DatabaseConnection $instance = null;

    private PDO $connection;

    // Bước 2: Constructor private — không cho new từ bên ngoài
    private function __construct()
    {
        $host   = config('database.connections.mysql.host',     '127.0.0.1');
        $port   = config('database.connections.mysql.port',     '3306');
        $db     = config('database.connections.mysql.database', 'quan_ly_hoc_phi');
        $user   = config('database.connections.mysql.username', 'root');
        $pass   = config('database.connections.mysql.password', '');

        try {
            $this->connection = new PDO(
                "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4",
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            throw new \RuntimeException("Không thể kết nối database: " . $e->getMessage());
        }
    }

    // Bước 3: Clone bị chặn — không cho nhân bản instance
    private function __clone() {}

    // Bước 4: Phương thức static duy nhất để lấy instance
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            // Chỉ tạo mới lần đầu tiên gọi
            self::$instance = new self();
        }

        // Các lần sau trả về instance đã có sẵn
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /**
     * Tiện ích truy vấn nhanh — dùng khi không cần Eloquent.
     */
    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
