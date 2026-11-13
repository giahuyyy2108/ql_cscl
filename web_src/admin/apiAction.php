<?php
require_once('web_src/bean/CtChiSoPeer.php');

/** API chi doc cho he thong tich hop, dung API key thay cho session. */
class apiAction
{
    public function getCtChiSo()
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');

        if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'GET') {
            header('Allow: GET');
            return $this->respond(405, array('success' => false, 'message' => 'Chi ho tro GET'));
        }

        // Dat key tren Apache/OS, khong hard-code vao source hoac URL.
        $configuredKey = getenv('QL_CSCL_CT_CHISO_API_KEY');
        // XAMPP may be running before Apache loads its new configuration.
        // Read the same key from an Apache config file outside the web root.
        if (!is_string($configuredKey) || strlen($configuredKey) < 32) {
            $keyFile = dirname(__DIR__, 4) . '/apache/conf/extra/ql_cscl_api_key.conf';
            if (is_readable($keyFile)) {
                $keyFileContent = file_get_contents($keyFile);
                if (is_string($keyFileContent)
                    && preg_match('/^\s*SetEnv\s+QL_CSCL_CT_CHISO_API_KEY\s+"([A-Za-z0-9_-]{32,})"\s*$/m', $keyFileContent, $matches)) {
                    $configuredKey = $matches[1];
                }
            }
        }
        if (!is_string($configuredKey) || strlen($configuredKey) < 32) {
            return $this->respond(503, array('success' => false, 'message' => 'API chua duoc cau hinh'));
        }

        $providedKey = isset($_SERVER['HTTP_X_API_KEY']) ? trim((string) $_SERVER['HTTP_X_API_KEY']) : '';
        if ($providedKey === '') {
            $authorization = isset($_SERVER['HTTP_AUTHORIZATION'])
                ? (string) $_SERVER['HTTP_AUTHORIZATION']
                : (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) ? (string) $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] : '');
            if ($authorization === '' && function_exists('getallheaders')) {
                foreach (getallheaders() as $name => $value) {
                    if (strcasecmp($name, 'Authorization') === 0) {
                        $authorization = (string) $value;
                        break;
                    }
                }
            }
            if (preg_match('/^Bearer\s+(.+)$/i', trim($authorization), $matches)) {
                $providedKey = trim($matches[1]);
            }
        }
        if ($providedKey === '' || !hash_equals($configuredKey, $providedKey)) {
            header('WWW-Authenticate: Bearer realm="ql_cscl"');
            return $this->respond(401, array('success' => false, 'message' => 'API key khong hop le'));
        }

        $maChiSo = isset($_GET['ma_chi_so'])
            ? filter_var($_GET['ma_chi_so'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)))
            : false;
        if ($maChiSo === false) {
            return $this->respond(400, array('success' => false, 'message' => 'ma_chi_so phai la so nguyen duong'));
        }

        try {
            $peer = new CtChiSoPeer();
            $data = $peer->getToanBoTheoMaChiSo($maChiSo);
            return $this->respond(200, array(
                'success' => true,
                'ma_chi_so' => $maChiSo,
                'total' => count($data),
                'data' => $data
            ));
        } catch (Throwable $e) {
            error_log('api.getCtChiSo: ' . $e->getMessage());
            return $this->respond(500, array('success' => false, 'message' => 'Loi truy van du lieu'));
        }
    }

    private function respond($status, $data)
    {
        http_response_code($status);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        return null;
    }
}
