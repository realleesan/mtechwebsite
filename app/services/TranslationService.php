<?php
/**
 * TranslationService.php
 *
 * Dịch nội dung động từ DB sang EN / VI / ZH.
 * Dùng Google Translate API không chính thức (không cần key/billing).
 * Kết quả được cache vào file JSON trong backups/translation_cache/
 * để tránh gọi API liên tục.
 *
 * Cache bị xóa tự động khi gọi clearCache() — thường gọi sau khi
 * admin cập nhật dữ liệu trong DB.
 */

class TranslationService
{
    /** Thư mục cache */
    private $cacheDir;

    /** Thời gian cache tối đa (giây) — mặc định 24h */
    private $cacheTTL;

    /** Ngôn ngữ nguồn mặc định */
    private $sourceLang = 'en';

    public function __construct($cacheTTL = 86400)
    {
        $this->cacheDir = __DIR__ . '/../../backups/translation_cache';
        $this->cacheTTL = $cacheTTL;

        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    // ----------------------------------------------------------------
    // PUBLIC API
    // ----------------------------------------------------------------

    /**
     * Dịch một chuỗi văn bản.
     * Trả về bản dịch hoặc chuỗi gốc nếu thất bại.
     *
     * @param string $text   Văn bản cần dịch
     * @param string $target Ngôn ngữ đích: 'vi' | 'zh' | 'en'
     * @param string $source Ngôn ngữ nguồn (mặc định 'en')
     * @return string
     */
    public function translate($text, $target, $source = 'en')
    {
        if (empty(trim($text))) return $text;
        if ($target === $source)  return $text;

        // Kiểm tra cache
        $cached = $this->getCache($text, $target, $source);
        if ($cached !== null) return $cached;

        // Gọi API
        $result = $this->callGoogleTranslate($text, $target, $source);
        if ($result === null) return $text; // fallback về gốc

        // Lưu cache
        $this->setCache($text, $target, $source, $result);

        return $result;
    }

    /**
     * Dịch nhiều chuỗi cùng lúc (batch).
     * Trả về mảng bản dịch theo đúng thứ tự đầu vào.
     *
     * @param array  $texts  Mảng chuỗi cần dịch
     * @param string $target Ngôn ngữ đích
     * @param string $source Ngôn ngữ nguồn
     * @return array
     */
    public function translateBatch(array $texts, $target, $source = 'en')
    {
        if ($target === $source) return $texts;

        $results = [];
        $toTranslate = []; // index => text chưa có cache

        foreach ($texts as $i => $text) {
            if (empty(trim($text))) {
                $results[$i] = $text;
                continue;
            }
            $cached = $this->getCache($text, $target, $source);
            if ($cached !== null) {
                $results[$i] = $cached;
            } else {
                $toTranslate[$i] = $text;
            }
        }

        // Dịch những cái chưa có cache
        foreach ($toTranslate as $i => $text) {
            $translated = $this->callGoogleTranslate($text, $target, $source);
            if ($translated !== null) {
                $this->setCache($text, $target, $source, $translated);
                $results[$i] = $translated;
            } else {
                $results[$i] = $text; // fallback
            }
            // Delay nhỏ để tránh rate limit
            usleep(100000); // 100ms
        }

        ksort($results);
        return $results;
    }

    /**
     * Dịch toàn bộ các field text trong một mảng record DB.
     *
     * @param array  $record  Một row từ DB
     * @param array  $fields  Danh sách field cần dịch, vd: ['name','description']
     * @param string $target  Ngôn ngữ đích
     * @return array Record với các field đã được dịch
     */
    public function translateRecord(array $record, array $fields, $target)
    {
        if ($target === $this->sourceLang) return $record;

        foreach ($fields as $field) {
            if (!empty($record[$field])) {
                $record[$field] = $this->translate($record[$field], $target);
            }
        }
        return $record;
    }

    /**
     * Dịch nhiều records cùng lúc.
     *
     * @param array  $records Mảng rows từ DB
     * @param array  $fields  Danh sách field cần dịch
     * @param string $target  Ngôn ngữ đích
     * @return array
     */
    public function translateRecords(array $records, array $fields, $target)
    {
        if ($target === $this->sourceLang) return $records;

        return array_map(function ($record) use ($fields, $target) {
            return $this->translateRecord($record, $fields, $target);
        }, $records);
    }

    /**
     * Xóa toàn bộ cache dịch.
     * Gọi sau khi admin cập nhật nội dung DB.
     */
    public function clearCache()
    {
        $files = glob($this->cacheDir . '/*.json');
        if ($files) {
            foreach ($files as $file) {
                unlink($file);
            }
        }
    }

    /**
     * Xóa cache của một ngôn ngữ cụ thể.
     *
     * @param string $target Ngôn ngữ cần xóa cache
     */
    public function clearCacheForLang($target)
    {
        $file = $this->cacheDir . '/cache_' . $target . '.json';
        if (file_exists($file)) {
            unlink($file);
        }
    }

    // ----------------------------------------------------------------
    // CACHE HELPERS
    // ----------------------------------------------------------------

    private function getCacheFile($target)
    {
        return $this->cacheDir . '/cache_' . $target . '.json';
    }

    private function loadCacheFile($target)
    {
        $file = $this->getCacheFile($target);
        if (!file_exists($file)) return [];

        // Kiểm tra TTL
        if ((time() - filemtime($file)) > $this->cacheTTL) {
            unlink($file);
            return [];
        }

        $data = json_decode(file_get_contents($file), true);
        return is_array($data) ? $data : [];
    }

    private function saveCacheFile($target, array $data)
    {
        file_put_contents(
            $this->getCacheFile($target),
            json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            LOCK_EX
        );
    }

    private function cacheKey($text, $source)
    {
        return md5($source . '|' . $text);
    }

    private function getCache($text, $target, $source)
    {
        static $loaded = [];
        if (!isset($loaded[$target])) {
            $loaded[$target] = $this->loadCacheFile($target);
        }
        $key = $this->cacheKey($text, $source);
        return isset($loaded[$target][$key]) ? $loaded[$target][$key] : null;
    }

    private function setCache($text, $target, $source, $translated)
    {
        static $loaded = [];
        if (!isset($loaded[$target])) {
            $loaded[$target] = $this->loadCacheFile($target);
        }
        $key = $this->cacheKey($text, $source);
        $loaded[$target][$key] = $translated;
        $this->saveCacheFile($target, $loaded[$target]);
    }

    // ----------------------------------------------------------------
    // GOOGLE TRANSLATE (không chính thức, không cần key)
    // ----------------------------------------------------------------

    private function callGoogleTranslate($text, $target, $source)
    {
        // Map ngôn ngữ sang mã Google Translate
        $langMap = ['vi' => 'vi', 'zh' => 'zh-CN', 'en' => 'en'];
        $tl = $langMap[$target] ?? $target;
        $sl = $langMap[$source] ?? $source;

        $url = 'https://translate.googleapis.com/translate_a/single'
             . '?client=gtx'
             . '&sl=' . urlencode($sl)
             . '&tl=' . urlencode($tl)
             . '&dt=t'
             . '&q=' . urlencode($text);

        $context = stream_context_create([
            'http' => [
                'method'  => 'GET',
                'timeout' => 5,
                'header'  => "User-Agent: Mozilla/5.0\r\n"
            ],
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ]
        ]);

        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            error_log('TranslationService: Cannot reach Google Translate API');
            return null;
        }

        $data = json_decode($response, true);
        if (!isset($data[0]) || !is_array($data[0])) return null;

        // Ghép các đoạn dịch lại
        $translated = '';
        foreach ($data[0] as $part) {
            if (isset($part[0])) {
                $translated .= $part[0];
            }
        }

        return trim($translated) ?: null;
    }
}
