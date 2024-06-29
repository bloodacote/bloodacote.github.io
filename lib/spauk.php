<?php
// SPAuk engine (demo) by bloodacote 
// Compiled time: 2024-06-23 17:13:44 
$root_dir = $_SERVER["DOCUMENT_ROOT"];$method = $_SERVER["REQUEST_METHOD"];$input = file_get_contents("php://input");$input = json_decode($input, true);$output = [];function set_error($code, $text) {http_response_code($code);$error_output = ["code" => $code,"error" => $text];$error_output = json_encode($error_output);echo $error_output;exit();}function check_user_input($key, $type = null, $min = null, $max = null) {global $input;if (!isset($input[$key])) {set_error(418, "no-input__" . $key);}$user_input = $input[$key];if (gettype($user_input) != $type AND $type != null) {set_error(418, "wrong-type__" . $key);}if (gettype($user_input) == "boolean") {$input_len = 0 + intval($input[$key]);}if (gettype($user_input) == "integer") {$input_len = $user_input;}if (gettype($user_input) == "string") {$input_len = mb_strlen($user_input);}if ($input_len < $min AND $min != null) {set_error(418, "too-short__" . $key);}if ($input_len > $max AND $max != null) {set_error(418, "too-long__" . $key);}}function default_user_input($key, $type = null, $default = null, $min = null, $max = null) {global $input;if (!isset($input[$key])) {$input[$key] = $default;} else {check_user_input($key, $type, $min, $max);}}function check_method($need_method) {global $method;if (mb_strtoupper($need_method) != mb_strtoupper($method)) {set_error(418, "wrong-method");}}function get_input($key) {global $input;return $input[$key];}function set_output($key, $val) {global $output;if ($key == null) {$output = $val;} else {$output[$key] = $val;}}function send_output_data() {global $output;header("Content-type: application/json");$output = json_encode($output);echo $output;}function db_connect($host, $user, $pass, $db_name) {$dsn = "mysql:host=$host;dbname=$db_name";$opts = [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,PDO::ATTR_ERRMODE      => PDO::ERRMODE_EXCEPTION];try {$pdo = new PDO($dsn, $user, $pass, $opts);return $pdo;} catch (Exception $err) {set_error(418, "db-fail-connect");}}function db_query($db, $query, $data = []) {$result = $db -> prepare($query);$result -> execute($data);}function db_fetch_one($db, $query, $data = []) {$result = $db -> prepare($query);$result -> execute($data);$data = $result -> fetch();return $data;}function db_fetch_all($db, $query, $data = []) {foreach ($data as $key => $val) {if (gettype($data[$key]) == "integer") {$query = str_replace(":$key", $val, $query);unset($data[$key]);}}$result = $db -> prepare($query);$result -> execute($data);$data = $result -> fetchAll();return $data;}function db_fetch_count($db, $query, $data = []) {$result = $db -> prepare($query);$result -> execute($data);$data = $result -> fetch();return $data["COUNT(*)"];}function db_get_last_id($db) {$data = $db -> lastInsertId();return $data;}function hash64_encode($str) {return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($str));}function hash64_decode($str) {return base64_decode(str_replace(['-', '_'], ['+', '/'], $str));}function jwt_gen($payload, $secret_key) {$header = array("typ" => "JWT","alg" => "HS256");$base_header = hash64_encode(json_encode($header));$base_payload = hash64_encode(json_encode($payload));$signature = hash_hmac("sha256", $base_header .'.'. $base_payload, $secret_key, true);$base_signature = hash64_encode($signature);$token = $base_header .'.'. $base_payload .'.'. $base_signature;return $token;}function jwt_degen($token, $secret_key) {if ($token == "") {set_error(500, "no-token");}$token_parts = explode(".", $token);if (count($token_parts) != 3) {set_error(500, "wrong-token-format");  }  $base_header = $token_parts[0];$base_payload = $token_parts[1];$base_signature = $token_parts[2];$header = hash64_decode($base_header);$payload = hash64_decode($base_payload);$signature = hash64_decode($base_signature);$new_signature = hash_hmac("sha256", $base_header .'.'. $base_payload, $secret_key, true);$is_valid = hash_equals($signature, $new_signature);if ($is_valid != 1) {$is_valid = 0;}$output = array("valid" => $is_valid,"data" => json_decode($payload, true));return $output;}function passhash_crypt($text) {$text = password_hash($text, PASSWORD_DEFAULT);return $text;}function passhash_check($text, $hashed_text) {$result = password_verify($text, $hashed_text);return $result;}function load_url($url, $method, $data = null) {$ch = curl_init();curl_setopt($ch, CURLOPT_URL, $url);curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));curl_setopt($ch, CURLOPT_POSTFIELDS, $data);curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);$response = curl_exec($ch);if (curl_error($ch)) {set_error(500, "curl-error: " . curl_error($ch));}  curl_close($ch);return $response;}function load_api($url, $method, $data = null) {$response = load_url($url, $method, $data = null);$response = json_decode($response, true);return $response;}?>