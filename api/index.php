<?php
// ============================================================
//  SARC — API REST completa
//  Archivo: sarc/api/index.php
// ============================================================

// ── CONFIGURA AQUÍ TUS CREDENCIALES MySQL ───────────────────
define('DB_HOST', 'localhost');
define('DB_NAME', 'sarc_db');
define('DB_USER', 'root');   // ← tu usuario MySQL
define('DB_PASS', '');        // ← tu contraseña MySQL (vacía en XAMPP por defecto)
// ────────────────────────────────────────────────────────────

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

// ── CONEXIÓN PDO ─────────────────────────────────────────────
function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
                DB_USER, DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                 PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
            );
        } catch (PDOException $e) {
            json_out(['ok'=>false,'error'=>'Error de conexión a MySQL: '.$e->getMessage()], 500);
        }
    }
    return $pdo;
}

// ── HELPERS ───────────────────────────────────────────────────
function json_out(array $d, int $code=200): void {
    http_response_code($code);
    echo json_encode($d, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    exit;
}
function req_body(): array {
    return json_decode(file_get_contents('php://input'), true) ?? [];
}
function need(array $d, array $keys): void {
    foreach ($keys as $k)
        if (!isset($d[$k]) || $d[$k]==='')
            json_out(['ok'=>false,'error'=>"Campo requerido: $k"], 422);
}

// ── ROUTER ────────────────────────────────────────────────────
$uri    = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$parts  = explode('/', $uri);
// eliminar segmentos de carpeta hasta llegar al recurso
$known  = ['auth','usuarios','asesores','clientes','rutas','satisfacciones','visitas','dashboard'];
while (count($parts) && !in_array($parts[0], $known)) array_shift($parts);

$res    = $parts[0] ?? '';
$id     = isset($parts[1]) && is_numeric($parts[1]) ? (int)$parts[1] : null;
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($res) {
        case 'auth':            r_auth($method);                break;
        case 'usuarios':        r_usuarios($method, $id);       break;
        case 'asesores':        r_asesores($method, $id);       break;
        case 'clientes':        r_clientes($method, $id);       break;
        case 'rutas':           r_rutas($method, $id);          break;
        case 'satisfacciones':  r_satisfacciones($method, $id); break;
        case 'visitas':          r_visitas($method, $id);        break;
        case 'dashboard':       r_dashboard($method);           break;
        default: json_out(['ok'=>false,'error'=>'Endpoint no encontrado'], 404);
    }
} catch (PDOException $e) {
    json_out(['ok'=>false,'error'=>'Error BD: '.$e->getMessage()], 500);
} catch (Exception $e) {
    json_out(['ok'=>false,'error'=>$e->getMessage()], 500);
}

// ============================================================
//  AUTH — POST /auth
// ============================================================
function r_auth(string $m): void {
    if ($m!=='POST') json_out(['ok'=>false,'error'=>'Solo POST'],405);
    $d = req_body();
    need($d,['username','password']);
    $s = db()->prepare("SELECT * FROM usuarios WHERE username=? AND estado='activo' LIMIT 1");
    $s->execute([$d['username']]);
    $u = $s->fetch();
    if (!$u || !password_verify($d['password'], $u['password_hash']))
        json_out(['ok'=>false,'error'=>'Usuario o contraseña incorrectos'],401);
    db()->prepare("UPDATE usuarios SET ultimo_acceso=NOW() WHERE id=?")->execute([$u['id']]);
    unset($u['password_hash']);
    json_out(['ok'=>true,'data'=>$u]);
}

// ============================================================
//  USUARIOS — GET / POST / PUT / DELETE
// ============================================================
function r_usuarios(string $m, ?int $id): void {
    $db = db();

    if ($m==='GET') {
        if ($id) {
            $s=$db->prepare("SELECT id,username,nombre,email,rol,estado,fecha_registro FROM usuarios WHERE id=?");
            $s->execute([$id]); $r=$s->fetch();
            $r ? json_out(['ok'=>true,'data'=>$r]) : json_out(['ok'=>false,'error'=>'No encontrado'],404);
        }
        $rows=$db->query("SELECT id,username,nombre,email,rol,estado,fecha_registro FROM usuarios ORDER BY id")->fetchAll();
        json_out(['ok'=>true,'data'=>$rows]);
    }

    if ($m==='POST') {
        $d=req_body(); need($d,['username','nombre','email','password','rol']);
        $db->prepare("INSERT INTO usuarios(username,password_hash,nombre,email,rol,estado) VALUES(?,?,?,?,?,?)")
           ->execute([$d['username'],password_hash($d['password'],PASSWORD_BCRYPT),
                      $d['nombre'],$d['email'],$d['rol'],$d['estado']??'activo']);
        json_out(['ok'=>true,'id'=>(int)$db->lastInsertId(),'message'=>'Usuario creado'],201);
    }

    if ($m==='PUT') {
        if (!$id) json_out(['ok'=>false,'error'=>'ID requerido'],400);
        $d=req_body(); $f=[];$p=[];
        foreach(['username','nombre','email','rol','estado'] as $c)
            if (isset($d[$c])){ $f[]="$c=?"; $p[]=$d[$c]; }
        if (!empty($d['password'])){ $f[]="password_hash=?"; $p[]=password_hash($d['password'],PASSWORD_BCRYPT); }
        if (empty($f)) json_out(['ok'=>false,'error'=>'Sin campos'],422);
        $p[]=$id;
        $db->prepare("UPDATE usuarios SET ".implode(',',$f)." WHERE id=?")->execute($p);
        json_out(['ok'=>true,'message'=>'Usuario actualizado']);
    }

    if ($m==='DELETE') {
        if (!$id) json_out(['ok'=>false,'error'=>'ID requerido'],400);
        $db->prepare("DELETE FROM usuarios WHERE id=?")->execute([$id]);
        json_out(['ok'=>true,'message'=>'Usuario eliminado']);
    }
    json_out(['ok'=>false,'error'=>'Método no permitido'],405);
}

// ============================================================
//  ASESORES — GET / POST / PUT / DELETE
// ============================================================
function r_asesores(string $m, ?int $id): void {
    $db = db();

    if ($m==='GET') {
        if ($id) {
            $s=$db->prepare("SELECT * FROM asesores WHERE id=?");
            $s->execute([$id]); $r=$s->fetch();
            $r ? json_out(['ok'=>true,'data'=>$r]) : json_out(['ok'=>false,'error'=>'No encontrado'],404);
        }
        $where=''; $p=[];
        if (!empty($_GET['estado'])){ $where="WHERE estado=?"; $p[]=$_GET['estado']; }
        $s=$db->prepare("SELECT * FROM asesores $where ORDER BY nombre");
        $s->execute($p);
        json_out(['ok'=>true,'data'=>$s->fetchAll()]);
    }

    if ($m==='POST') {
        $d=req_body(); need($d,['nombre','email','tipo']);
        $db->prepare("INSERT INTO asesores(nombre,email,telefono,tipo,zona,estado,notas) VALUES(?,?,?,?,?,?,?)")
           ->execute([$d['nombre'],$d['email'],$d['telefono']??'',$d['tipo'],
                      $d['zona']??'',$d['estado']??'activo',$d['notas']??'']);
        json_out(['ok'=>true,'id'=>(int)$db->lastInsertId(),'message'=>'Asesor creado'],201);
    }

    if ($m==='PUT') {
        if (!$id) json_out(['ok'=>false,'error'=>'ID requerido'],400);
        $d=req_body(); $f=[];$p=[];
        foreach(['nombre','email','telefono','tipo','zona','estado','notas'] as $c)
            if (array_key_exists($c,$d)){ $f[]="$c=?"; $p[]=$d[$c]??''; }
        if (empty($f)) json_out(['ok'=>false,'error'=>'Sin campos'],422);
        $p[]=$id;
        $db->prepare("UPDATE asesores SET ".implode(',',$f)." WHERE id=?")->execute($p);
        json_out(['ok'=>true,'message'=>'Asesor actualizado']);
    }

    if ($m==='DELETE') {
        if (!$id) json_out(['ok'=>false,'error'=>'ID requerido'],400);
        $cnt=$db->prepare("SELECT COUNT(*) FROM rutas WHERE asesor_id=?");
        $cnt->execute([$id]);
        if ((int)$cnt->fetchColumn()>0)
            json_out(['ok'=>false,'error'=>'El asesor tiene rutas asignadas y no puede eliminarse.'],409);
        $db->prepare("DELETE FROM asesores WHERE id=?")->execute([$id]);
        json_out(['ok'=>true,'message'=>'Asesor eliminado']);
    }
    json_out(['ok'=>false,'error'=>'Método no permitido'],405);
}

// ============================================================
//  CLIENTES — GET / POST / PUT / DELETE
// ============================================================
function r_clientes(string $m, ?int $id): void {
    $db = db();

    if ($m==='GET') {
        if ($id) {
            $s=$db->prepare("SELECT * FROM clientes WHERE id=?");
            $s->execute([$id]); $r=$s->fetch();
            $r ? json_out(['ok'=>true,'data'=>$r]) : json_out(['ok'=>false,'error'=>'No encontrado'],404);
        }
        $cond=[];$p=[];
        if (!empty($_GET['tipo'])){ $cond[]="tipo=?"; $p[]=$_GET['tipo']; }
        if (!empty($_GET['estado'])){ $cond[]="estado=?"; $p[]=$_GET['estado']; }
        if (!empty($_GET['q'])){
            $cond[]="(nombre LIKE ? OR contacto LIKE ?)";
            $like='%'.$_GET['q'].'%'; $p[]=$like; $p[]=$like;
        }
        $where=$cond?'WHERE '.implode(' AND ',$cond):'';
        $s=$db->prepare("SELECT * FROM clientes $where ORDER BY nombre");
        $s->execute($p);
        json_out(['ok'=>true,'data'=>$s->fetchAll()]);
    }

    if ($m==='POST') {
        $d=req_body(); need($d,['nombre','tipo']);
        $db->prepare("INSERT INTO clientes(nombre,nit,tipo,contacto,email,telefono,direccion,ciudad,zona,estado,notas) VALUES(?,?,?,?,?,?,?,?,?,?,?)")
           ->execute([$d['nombre'],$d['nit']??'',$d['tipo'],$d['contacto']??'',$d['email']??'',
                      $d['telefono']??'',$d['direccion']??'',$d['ciudad']??'',
                      $d['zona']??'',$d['estado']??'activo',$d['notas']??'']);
        json_out(['ok'=>true,'id'=>(int)$db->lastInsertId(),'message'=>'Cliente creado'],201);
    }

    if ($m==='PUT') {
        if (!$id) json_out(['ok'=>false,'error'=>'ID requerido'],400);
        $d=req_body(); $f=[];$p=[];
        foreach(['nombre','nit','tipo','contacto','email','telefono','direccion','ciudad','zona','estado','notas'] as $c)
            if (array_key_exists($c,$d)){ $f[]="$c=?"; $p[]=$d[$c]??''; }
        if (empty($f)) json_out(['ok'=>false,'error'=>'Sin campos'],422);
        $p[]=$id;
        $db->prepare("UPDATE clientes SET ".implode(',',$f)." WHERE id=?")->execute($p);
        json_out(['ok'=>true,'message'=>'Cliente actualizado']);
    }

    if ($m==='DELETE') {
        if (!$id) json_out(['ok'=>false,'error'=>'ID requerido'],400);
        $db->prepare("DELETE FROM clientes WHERE id=?")->execute([$id]);
        json_out(['ok'=>true,'message'=>'Cliente eliminado']);
    }
    json_out(['ok'=>false,'error'=>'Método no permitido'],405);
}

// ============================================================
//  RUTAS — GET / POST / PUT / DELETE
// ============================================================
function r_rutas(string $m, ?int $id): void {
    $db = db();

    if ($m==='GET') {
        if ($id) {
            $s=$db->prepare("SELECT r.*, a.nombre AS asesor_nombre FROM rutas r JOIN asesores a ON a.id=r.asesor_id WHERE r.id=?");
            $s->execute([$id]); $r=$s->fetch();
            if (!$r) json_out(['ok'=>false,'error'=>'No encontrada'],404);
            $r['clientes_ids']=json_decode($r['clientes_ids']??'[]',true);
            json_out(['ok'=>true,'data'=>$r]);
        }
        $cond=[];$p=[];
        if (!empty($_GET['asesor_id'])){ $cond[]="r.asesor_id=?"; $p[]=(int)$_GET['asesor_id']; }
        if (!empty($_GET['estado'])){ $cond[]="r.estado=?"; $p[]=$_GET['estado']; }
        if (!empty($_GET['fecha'])){ $cond[]="r.fecha=?"; $p[]=$_GET['fecha']; }
        $where=$cond?'WHERE '.implode(' AND ',$cond):'';
        $s=$db->prepare("SELECT r.*, a.nombre AS asesor_nombre FROM rutas r JOIN asesores a ON a.id=r.asesor_id $where ORDER BY r.fecha DESC, r.id DESC");
        $s->execute($p);
        $rows=$s->fetchAll();
        foreach ($rows as &$row) $row['clientes_ids']=json_decode($row['clientes_ids']??'[]',true);
        json_out(['ok'=>true,'data'=>$rows]);
    }

    if ($m==='POST') {
        $d=req_body(); need($d,['asesor_id','fecha','hora_inicio','zona','estado']);
        $db->prepare("INSERT INTO rutas(asesor_id,fecha,hora_inicio,hora_fin,zona,estado,vehiculo,km_inicial,km_final,clientes_ids,observaciones) VALUES(?,?,?,?,?,?,?,?,?,?,?)")
           ->execute([(int)$d['asesor_id'],$d['fecha'],$d['hora_inicio'],$d['hora_fin']??'',
                      $d['zona'],$d['estado'],$d['vehiculo']??'',
                      (int)($d['km_inicial']??0),(int)($d['km_final']??0),
                      json_encode($d['clientes_ids']??[]),$d['observaciones']??'']);
        json_out(['ok'=>true,'id'=>(int)$db->lastInsertId(),'message'=>'Ruta creada'],201);
    }

    if ($m==='PUT') {
        if (!$id) json_out(['ok'=>false,'error'=>'ID requerido'],400);
        $d=req_body(); $f=[];$p=[];
        foreach(['asesor_id','fecha','hora_inicio','hora_fin','zona','estado','vehiculo','km_inicial','km_final','observaciones'] as $c)
            if (array_key_exists($c,$d)){ $f[]="$c=?"; $p[]=$d[$c]; }
        if (isset($d['clientes_ids'])){ $f[]="clientes_ids=?"; $p[]=json_encode($d['clientes_ids']); }
        if (empty($f)) json_out(['ok'=>false,'error'=>'Sin campos'],422);
        $p[]=$id;
        $db->prepare("UPDATE rutas SET ".implode(',',$f)." WHERE id=?")->execute($p);
        json_out(['ok'=>true,'message'=>'Ruta actualizada']);
    }

    if ($m==='DELETE') {
        if (!$id) json_out(['ok'=>false,'error'=>'ID requerido'],400);
        $s=$db->prepare("SELECT estado FROM rutas WHERE id=?"); $s->execute([$id]); $r=$s->fetch();
        if (!$r) json_out(['ok'=>false,'error'=>'No encontrada'],404);
        if ($r['estado']==='completada')
            json_out(['ok'=>false,'error'=>'No se pueden eliminar rutas completadas'],409);
        $db->prepare("DELETE FROM rutas WHERE id=?")->execute([$id]);
        json_out(['ok'=>true,'message'=>'Ruta eliminada']);
    }
    json_out(['ok'=>false,'error'=>'Método no permitido'],405);
}

// ============================================================
//  SATISFACCIONES — GET / POST / PUT / DELETE
// ============================================================
function r_satisfacciones(string $m, ?int $id): void {
    $db = db();
    $join = "SELECT s.*, c.nombre AS cliente_nombre, a.nombre AS asesor_nombre
             FROM satisfacciones s
             JOIN clientes c ON c.id=s.cliente_id
             JOIN rutas    r ON r.id=s.ruta_id
             JOIN asesores a ON a.id=r.asesor_id";

    if ($m==='GET') {
        if ($id) {
            $s=$db->prepare("$join WHERE s.id=?"); $s->execute([$id]); $r=$s->fetch();
            $r ? json_out(['ok'=>true,'data'=>$r]) : json_out(['ok'=>false,'error'=>'No encontrado'],404);
        }
        $rows=$db->query("$join ORDER BY s.fecha DESC, s.id DESC")->fetchAll();
        json_out(['ok'=>true,'data'=>$rows]);
    }

    if ($m==='POST') {
        $d=req_body(); need($d,['ruta_id','cliente_id','calificacion','fecha']);
        $db->prepare("INSERT INTO satisfacciones(ruta_id,cliente_id,calificacion,fecha,comentarios) VALUES(?,?,?,?,?)")
           ->execute([(int)$d['ruta_id'],(int)$d['cliente_id'],(int)$d['calificacion'],$d['fecha'],$d['comentarios']??'']);
        json_out(['ok'=>true,'id'=>(int)$db->lastInsertId(),'message'=>'Satisfacción registrada'],201);
    }

    if ($m==='PUT') {
        if (!$id) json_out(['ok'=>false,'error'=>'ID requerido'],400);
        $d=req_body(); $f=[];$p=[];
        foreach(['calificacion','fecha','comentarios'] as $c)
            if (array_key_exists($c,$d)){ $f[]="$c=?"; $p[]=$d[$c]; }
        if (empty($f)) json_out(['ok'=>false,'error'=>'Sin campos'],422);
        $p[]=$id;
        $db->prepare("UPDATE satisfacciones SET ".implode(',',$f)." WHERE id=?")->execute($p);
        json_out(['ok'=>true,'message'=>'Satisfacción actualizada']);
    }

    if ($m==='DELETE') {
        if (!$id) json_out(['ok'=>false,'error'=>'ID requerido'],400);
        $db->prepare("DELETE FROM satisfacciones WHERE id=?")->execute([$id]);
        json_out(['ok'=>true,'message'=>'Satisfacción eliminada']);
    }
    json_out(['ok'=>false,'error'=>'Método no permitido'],405);
}
// ============================================================
//  VISITAS GPS — GET / POST / PUT
// ============================================================
function r_visitas(string $m, ?int $id): void {
    $db = db();

    if ($m === 'GET') {
        if ($id) {
            // Visitas de una ruta específica
            $s = $db->prepare(
                "SELECT v.*, c.nombre AS cliente_nombre, c.tipo AS cliente_tipo,
                        c.direccion AS cliente_direccion, c.ciudad AS cliente_ciudad
                 FROM visitas_gps v
                 JOIN clientes c ON c.id = v.cliente_id
                 WHERE v.ruta_id = ?
                 ORDER BY v.fecha_registro ASC"
            );
            $s->execute([$id]);
            json_out(['ok' => true, 'data' => $s->fetchAll()]);
        }
        // Todas las visitas recientes (últimas 100)
        $rows = $db->query(
            "SELECT v.*, c.nombre AS cliente_nombre,
                    a.nombre AS asesor_nombre, r.zona, r.fecha AS ruta_fecha
             FROM visitas_gps v
             JOIN clientes c  ON c.id = v.cliente_id
             JOIN rutas r     ON r.id = v.ruta_id
             JOIN asesores a  ON a.id = r.asesor_id
             ORDER BY v.fecha_registro DESC LIMIT 100"
        )->fetchAll();
        json_out(['ok' => true, 'data' => $rows]);
    }

    if ($m === 'POST') {
        $d = req_body();
        need($d, ['ruta_id', 'cliente_id', 'hora_entrada', 'resultado']);
        $db->prepare(
            "INSERT INTO visitas_gps
             (ruta_id, cliente_id, hora_entrada, hora_salida, duracion_min,
              lat_entrada, lng_entrada, lat_salida, lng_salida, resultado, notas)
             VALUES (?,?,?,?,?,?,?,?,?,?,?)
             ON DUPLICATE KEY UPDATE
             hora_salida=VALUES(hora_salida), duracion_min=VALUES(duracion_min),
             lat_salida=VALUES(lat_salida), lng_salida=VALUES(lng_salida),
             resultado=VALUES(resultado), notas=VALUES(notas)"
        )->execute([
            (int)$d['ruta_id'], (int)$d['cliente_id'],
            $d['hora_entrada'], $d['hora_salida'] ?? '',
            (int)($d['duracion_min'] ?? 0),
            $d['lat_entrada'] ?? null, $d['lng_entrada'] ?? null,
            $d['lat_salida']  ?? null, $d['lng_salida']  ?? null,
            $d['resultado'], $d['notas'] ?? ''
        ]);
        json_out(['ok' => true, 'id' => (int)$db->lastInsertId(), 'message' => 'Visita registrada'], 201);
    }

    if ($m === 'PUT') {
        if (!$id) json_out(['ok' => false, 'error' => 'ID requerido'], 400);
        $d = req_body(); $f = []; $p = [];
        foreach (['hora_salida','duracion_min','lat_salida','lng_salida','resultado','notas'] as $c)
            if (array_key_exists($c, $d)) { $f[] = "$c=?"; $p[] = $d[$c]; }
        if (empty($f)) json_out(['ok' => false, 'error' => 'Sin campos'], 422);
        $p[] = $id;
        $db->prepare("UPDATE visitas_gps SET " . implode(',', $f) . " WHERE id=?")->execute($p);
        json_out(['ok' => true, 'message' => 'Visita actualizada']);
    }

    if ($m === 'DELETE') {
        if (!$id) json_out(['ok' => false, 'error' => 'ID requerido'], 400);
        $db->prepare("DELETE FROM visitas_gps WHERE id=?")->execute([$id]);
        json_out(['ok' => true, 'message' => 'Visita eliminada']);
    }
    json_out(['ok' => false, 'error' => 'Método no permitido'], 405);
}

// ============================================================
//  DASHBOARD — Estadísticas generales
// ============================================================
function r_dashboard(string $m): void {
    if ($m!=='GET') json_out(['ok'=>false,'error'=>'Solo GET'],405);
    $db=db(); $s=[];
    foreach([
        'total_asesores'    =>"SELECT COUNT(*) FROM asesores",
        'asesores_activos'  =>"SELECT COUNT(*) FROM asesores WHERE estado='activo'",
        'total_clientes'    =>"SELECT COUNT(*) FROM clientes",
        'clientes_activos'  =>"SELECT COUNT(*) FROM clientes WHERE estado='activo'",
        'total_rutas'       =>"SELECT COUNT(*) FROM rutas",
        'rutas_completadas' =>"SELECT COUNT(*) FROM rutas WHERE estado='completada'",
        'rutas_pendientes'  =>"SELECT COUNT(*) FROM rutas WHERE estado IN('planificada','en-progreso')",
    ] as $k=>$sql) $s[$k]=(int)$db->query($sql)->fetchColumn();
    $s['promedio_satisfaccion']=round((float)$db->query("SELECT COALESCE(AVG(calificacion),0) FROM satisfacciones")->fetchColumn(),1);
    // Visitas GPS de hoy
    $s['visitas_hoy']   = (int)$db->query("SELECT COUNT(*) FROM visitas_gps WHERE DATE(fecha_registro)=CURDATE()")->fetchColumn();
    $s['visitas_exitosas_hoy'] = (int)$db->query("SELECT COUNT(*) FROM visitas_gps WHERE DATE(fecha_registro)=CURDATE() AND resultado='exitosa'")->fetchColumn();
    $s['rutas_por_estado']=$db->query("SELECT estado, COUNT(*) as total FROM rutas GROUP BY estado")->fetchAll();
    $s['top_asesores']=$db->query("SELECT a.nombre, COUNT(r.id) as rutas FROM asesores a JOIN rutas r ON r.asesor_id=a.id WHERE r.estado='completada' GROUP BY a.id ORDER BY rutas DESC LIMIT 5")->fetchAll();
    json_out(['ok'=>true,'data'=>$s]);
}
