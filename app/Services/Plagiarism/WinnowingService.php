<?php

namespace App\Services\Plagiarism;

class WinnowingService
{
    /**
     * Làm sạch văn bản tiếng Việt để tăng khả năng bắt copy-paste (chống lách bằng dấu câu/khoảng trắng).
     * Giữ nguyên dấu tiếng Việt để không bị trùng lặp sai giữa các từ khác nghĩa (ví dụ: 'mía' và 'mỉa').
     */
    public function preprocessText(string $text): string
    {
        // 1. Loại bỏ các tags HTML nếu có
        $text = strip_tags($text);
        
        // 2. Decode các entity HTML
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // 3. Chuyển về chữ thường
        $text = mb_strtolower($text, 'UTF-8');
        
        // 4. Loại bỏ TOÀN BỘ các ký tự không phải chữ cái hoặc chữ số (dấu câu, khoảng trắng, v.v.)
        // \p{L} = Any letter in any language (bao gồm tiếng Việt)
        // \p{N} = Any number
        $text = preg_replace('/[^\p{L}\p{N}]/u', '', $text);
        
        return $text;
    }

    /**
     * Cắt chuỗi thành các K-Grams (nhóm K ký tự liền kề).
     * Mặc định K = 7 (khuyên dùng cho tiếng Việt/tiếng Anh để chống nhiễu).
     */
    public function generateKGrams(string $text, int $k = 7): array
    {
        $grams = [];
        $length = mb_strlen($text, 'UTF-8');
        
        if ($length < $k) {
            return [$text]; // Trả về nguyên chuỗi nếu văn bản quá ngắn
        }
        
        for ($i = 0; $i <= $length - $k; $i++) {
            $grams[] = mb_substr($text, $i, $k, 'UTF-8');
        }
        
        return $grams;
    }

    /**
     * Tính toán Rolling Hash cho danh sách K-Grams.
     * Sử dụng thuật toán hàm crc32() có sẵn của PHP (viết bằng C) cho tốc độ siêu nhanh.
     */
    public function rollingHash(array $grams): array
    {
        $hashes = [];
        foreach ($grams as $gram) {
            // crc32 trả về số nguyên 32-bit (có thể âm trên hệ điều hành 32bit).
            // Ta dùng `sprintf("%u", ...)` để lấy unsigned int, ép về int thuần tuý nếu 64-bit.
            $unsignedCrc32 = sprintf("%u", crc32($gram));
            // Cast thành (int) vì PHP 64-bit có giới hạn int khổng lồ, dư sức chứa unsigned 32-bit
            $hashes[] = (int)$unsignedCrc32; 
        }
        return $hashes;
    }

    /**
     * Thuật toán Winnowing: Lấy Min-Hash trong mỗi Cửa Sổ (Window).
     * @param int $windowSize Mặc định W = 10 (cứ mỗi 10 hash liên tiếp, chọn 1 đại diện nhỏ nhất).
     * Điều này giúp giảm 90% lượng fingerprint phải lưu trữ nhưng vẫn đảm bảo tính chính xác.
     * @return array [vị_trí => mã_hash]
     */
    public function winnow(array $hashes, int $windowSize = 10): array
    {
        $fingerprints = [];
        $hashesCount = count($hashes);
        
        if ($hashesCount == 0) return [];
        if ($hashesCount <= $windowSize) {
            $minIdx = 0;
            for ($i = 1; $i < $hashesCount; $i++) {
                if ($hashes[$i] <= $hashes[$minIdx]) {
                    $minIdx = $i;
                }
            }
            return [$minIdx => $hashes[$minIdx]];
        }

        $minHash = PHP_INT_MAX;
        $minIdx = -1;

        for ($i = 0; $i <= $hashesCount - $windowSize; $i++) {
            // Nếu hash nhỏ nhất cũ đã rớt ra khỏi cửa sổ hiện tại, tìm lại từ đầu trong cửa sổ
            if ($minIdx < $i) {
                $minHash = PHP_INT_MAX;
                for ($j = $i; $j < $i + $windowSize; $j++) {
                    // Chiến thuật: lấy hash nhỏ nhất ở vị trí XA NHẤT bên PHẢI (rightmost)
                    // để nó tồn tại trong cửa sổ lâu nhất có thể ở các vòng lặp sau.
                    if ($hashes[$j] <= $minHash) {
                        $minHash = $hashes[$j];
                        $minIdx = $j;
                    }
                }
                $fingerprints[$minIdx] = $minHash;
            } else {
                // Nếu hash nhỏ nhất vẫn còn trong cửa sổ, ta chỉ việc so sánh với phần tử mới nhất vừa lọt vào cửa sổ
                $newElementIdx = $i + $windowSize - 1;
                if ($hashes[$newElementIdx] <= $minHash) {
                    $minHash = $hashes[$newElementIdx];
                    $minIdx = $newElementIdx;
                    $fingerprints[$minIdx] = $minHash;
                }
            }
        }
        
        return $fingerprints;
    }

    /**
     * Hàm tiện ích tích hợp từ A-Z: 
     * Đưa vào 1 đoạn văn bản thô -> Trả về mảng Fingerprints tinh gọn.
     */
    public function getFingerprints(string $text, int $k = 7, int $windowSize = 10): array
    {
        $preprocessed = $this->preprocessText($text);
        if (empty($preprocessed)) return [];

        $grams = $this->generateKGrams($preprocessed, $k);
        $hashes = $this->rollingHash($grams);
        
        return $this->winnow($hashes, $windowSize);
    }

    /**
     * Tính toán chỉ số tương đồng Jaccard Similarity.
     */
    public function computeJaccardSimilarity(array $fingerprints1, array $fingerprints2): float
    {
        // Chuyển về dạng danh sách mảng giá trị hash
        $set1 = array_values($fingerprints1);
        $set2 = array_values($fingerprints2);

        $intersection = count(array_intersect($set1, $set2));
        $union = count(array_unique(array_merge($set1, $set2)));

        if ($union == 0) return 0;
        return $intersection / $union;
    }
}
