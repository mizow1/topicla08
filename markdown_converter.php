<?php
class MarkdownConverter {
    
    public function convertToHtml($markdown) {
        if (empty($markdown)) {
            return '';
        }
        
        // 改行を統一
        $markdown = str_replace(["\r\n", "\r"], "\n", $markdown);
        
        // HTMLエスケープ（XSS対策）
        $markdown = htmlspecialchars($markdown, ENT_QUOTES, 'UTF-8');
        
        // Markdownの変換処理
        $html = $this->processMarkdown($markdown);
        
        return $html;
    }
    
    private function processMarkdown($text) {
        $lines = explode("\n", $text);
        $html = '';
        $inList = false;
        $listLevel = 0;
        
        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            
            // 空行処理
            if (empty($trimmedLine)) {
                if ($inList) {
                    $html .= str_repeat('</ul>', $listLevel);
                    $inList = false;
                    $listLevel = 0;
                }
                $html .= "<br>\n";
                continue;
            }
            
            // 見出し処理 (## → <h2>, ### → <h3>)
            if (preg_match('/^(#{2,6})\s+(.+)$/', $trimmedLine, $matches)) {
                if ($inList) {
                    $html .= str_repeat('</ul>', $listLevel);
                    $inList = false;
                    $listLevel = 0;
                }
                
                $level = strlen($matches[1]);
                $text = trim($matches[2]);
                $html .= "<h{$level}>{$text}</h{$level}>\n";
                continue;
            }
            
            // リスト処理 (- または * で始まる行)
            if (preg_match('/^(\s*)[-*]\s+(.+)$/', $line, $matches)) {
                $indent = strlen($matches[1]);
                $text = $matches[2];
                
                $currentLevel = intval($indent / 2) + 1;
                
                if (!$inList) {
                    $html .= '<ul>';
                    $inList = true;
                    $listLevel = 1;
                } elseif ($currentLevel > $listLevel) {
                    $html .= '<ul>';
                    $listLevel++;
                } elseif ($currentLevel < $listLevel) {
                    $html .= str_repeat('</ul>', $listLevel - $currentLevel);
                    $listLevel = $currentLevel;
                }
                
                $html .= "<li>{$text}</li>\n";
                continue;
            }
            
            // 数字付きリスト処理 (1. で始まる行)
            if (preg_match('/^(\s*)\d+\.\s+(.+)$/', $line, $matches)) {
                if ($inList) {
                    $html .= str_repeat('</ul>', $listLevel);
                    $inList = false;
                    $listLevel = 0;
                }
                
                $text = $matches[2];
                $html .= "<ol><li>{$text}</li></ol>\n";
                continue;
            }
            
            // 強調テキスト処理 (**太字**)
            $line = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $trimmedLine);
            
            // イタリック処理 (*イタリック*)
            $line = preg_replace('/\*([^*]+)\*/', '<em>$1</em>', $line);
            
            // インラインコード処理 (`コード`)
            $line = preg_replace('/`([^`]+)`/', '<code>$1</code>', $line);
            
            // リンク処理 [テキスト](URL)
            $line = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2" target="_blank">$1</a>', $line);
            
            // 通常のテキスト行
            if ($inList) {
                $html .= str_repeat('</ul>', $listLevel);
                $inList = false;
                $listLevel = 0;
            }
            
            $html .= "<p>{$line}</p>\n";
        }
        
        // 最後にリストが開いている場合は閉じる
        if ($inList) {
            $html .= str_repeat('</ul>', $listLevel);
        }
        
        return $html;
    }
}
?>