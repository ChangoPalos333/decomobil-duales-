<?php
/**
 * =============================================
 * DECOMOBIL - Configuracion de Base de Datos
 * =============================================
 * 
 * Este archivo contiene la configuracion de conexion a MySQL
 * y funciones utilitarias para la comunicacion con la base de datos.
 * 
 * PATRON DE DISENO: Singleton
 * - Solo se crea UNA conexion a la base de datos
 * - Todas las peticiones usan la misma conexion
 * - Mejora el rendimiento y evita conexiones duplicadas
 * 
 * USO:
 * require_once 'config/database.php';
 * $db = getDB();  // Obtiene la conexion PDO
 * 
 * REQUISITOS:
 * - PHP 7.4+ con extension PDO
 * - MySQL 5.7+ o MariaDB 10.2+
 * - XAMPP con Apache y MySQL corriendo
 */

// =============================================
// CONSTANTES DE CONFIGURACION
// =============================================
// Modifica estos valores segun tu entorno

/**
 * Host de la base de datos
 * En XAMPP local siempre es 'localhost'
 * Para servidor remoto, usa la IP o dominio
 */
define('DB_HOST', 'localhost');

/**
 * Nombre de la base de datos
 * Debe coincidir con el nombre en decomobil.sql
 */
define('DB_NAME', 'decomobil_db');

/**
 * Usuario de MySQL
 * En XAMPP por defecto es 'root'
 * Para produccion, crea un usuario con permisos limitados
 */
define('DB_USER', 'root');

/**
 * Contrasena de MySQL
 * En XAMPP por defecto esta vacia ''
 * IMPORTANTE: En produccion SIEMPRE usa una contrasena fuerte
 */
define('DB_PASS', '');

/**
 * Codificacion de caracteres
 * utf8mb4 soporta emojis y caracteres especiales
 */
define('DB_CHARSET', 'utf8mb4');


// =============================================
// CLASE DATABASE (Patron Singleton)
// =============================================
/**
 * Clase que maneja la conexion a la base de datos
 * Usa el patron Singleton para garantizar una unica conexion
 */
class Database {
    
    /**
     * @var Database|null Instancia unica de la clase
     * Variable estatica que guarda la unica instancia
     */
    private static $instance = null;
    
    /**
     * @var PDO Objeto de conexion PDO a MySQL
     */
    private $connection;

    /**
     * Constructor privado (Singleton)
     * Solo puede ser llamado desde getInstance()
     * Establece la conexion a MySQL usando PDO
     */
    private function __construct() {
        try {
            // DSN (Data Source Name) - cadena de conexion
            // Formato: mysql:host=servidor;dbname=basededatos;charset=codificacion
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            
            // Opciones de configuracion de PDO
            $options = [
                // Lanzar excepciones en caso de error (recomendado)
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                
                // Retornar resultados como array asociativo por defecto
                // Permite acceder a campos como $row['nombre'] en lugar de $row[0]
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                
                // Desactivar emulacion de prepared statements
                // Usa prepared statements nativos de MySQL (mas seguro)
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            // Crear conexion PDO
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch (PDOException $e) {
            // Si hay error de conexion, retornar JSON con el error
            // y terminar la ejecucion
            die(json_encode([
                'success' => false,
                'error' => 'Error de conexion a la base de datos: ' . $e->getMessage()
            ]));
        }
    }

    /**
     * Obtener la instancia unica de Database (Singleton)
     * Si no existe, la crea. Si ya existe, la retorna.
     * 
     * @return Database Instancia unica de la clase
     * 
     * Ejemplo de uso:
     * $db = Database::getInstance()->getConnection();
     */
    public static function getInstance() {
        // Si no existe instancia, crearla
        if (self::$instance === null) {
            self::$instance = new self();
        }
        // Retornar la instancia (nueva o existente)
        return self::$instance;
    }

    /**
     * Obtener el objeto de conexion PDO
     * 
     * @return PDO Conexion activa a la base de datos
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Prevenir clonacion del objeto (Singleton)
     * Lanzaria error si alguien intenta: clone $database;
     */
    private function __clone() {}

    /**
     * Prevenir deserializacion del objeto (Singleton)
     * Evita que se creen instancias via unserialize()
     * 
     * @throws Exception Si se intenta deserializar
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}


// =============================================
// FUNCIONES HELPER (Ayudantes)
// =============================================

/**
 * Obtener conexion a la base de datos
 * Funcion de acceso rapido para no escribir Database::getInstance()->getConnection()
 * 
 * @return PDO Conexion activa a la base de datos
 * 
 * Ejemplo de uso:
 * $db = getDB();
 * $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
 * $stmt->execute([1]);
 * $user = $stmt->fetch();
 */
function getDB() {
    return Database::getInstance()->getConnection();
}

/**
 * Enviar respuesta JSON al cliente
 * Configura los headers necesarios y termina la ejecucion
 * 
 * @param array $data       Datos a enviar (se convertiran a JSON)
 * @param int   $statusCode Codigo HTTP (200=OK, 400=Bad Request, 404=Not Found, etc.)
 * 
 * Ejemplo de uso:
 * jsonResponse(['success' => true, 'data' => $users]);
 * jsonResponse(['success' => false, 'error' => 'No encontrado'], 404);
 */
function jsonResponse($data, $statusCode = 200) {
    // Establecer codigo de estado HTTP
    http_response_code($statusCode);
    
    // Headers para indicar que es JSON
    header('Content-Type: application/json; charset=utf-8');
    
    // Headers CORS (Cross-Origin Resource Sharing)
    // Permiten que el frontend haga peticiones desde otro dominio/puerto
    header('Access-Control-Allow-Origin: *');                    // Permitir cualquier origen
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS'); // Metodos permitidos
    header('Access-Control-Allow-Headers: Content-Type, Authorization');     // Headers permitidos
    
    // Convertir array a JSON y enviarlo
    // JSON_UNESCAPED_UNICODE mantiene los caracteres especiales legibles
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    
    // Terminar ejecucion del script
    exit;
}


// =============================================
// MANEJO DE PREFLIGHT REQUESTS (CORS)
// =============================================
/**
 * Los navegadores envian una peticion OPTIONS antes de peticiones
 * POST/PUT/DELETE desde otro dominio (llamada "preflight").
 * Debemos responder con los headers CORS apropiados.
 */
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Enviar headers CORS
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    // Terminar sin procesar nada mas
    exit(0);
}
?>
