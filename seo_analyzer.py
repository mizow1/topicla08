import os
import requests
from bs4 import BeautifulSoup
from anthropic import Anthropic
from urllib.parse import urljoin, urlparse
from dotenv import load_dotenv
import json

load_dotenv()

class SEOAnalyzer:
    def __init__(self):
        self.client = Anthropic(api_key=os.getenv('CLAUDE_API_KEY'))
        
    def fetch_url_content(self, url):
        """URLからコンテンツを取得してHTMLを解析"""
        try:
            headers = {
                'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            }
            response = requests.get(url, headers=headers, timeout=10)
            response.raise_for_status()
            return response.text
        except requests.RequestException as e:
            raise Exception(f"URL取得エラー: {e}")
    
    def extract_page_info(self, html_content, url):
        """HTMLからSEOに関連する情報を抽出"""
        soup = BeautifulSoup(html_content, 'html.parser')
        
        page_info = {
            'url': url,
            'title': soup.title.string.strip() if soup.title else '',
            'meta_description': '',
            'h1_tags': [],
            'h2_tags': [],
            'h3_tags': [],
            'meta_keywords': '',
            'images_without_alt': 0,
            'total_images': 0,
            'internal_links': 0,
            'external_links': 0,
            'canonical_url': '',
            'meta_robots': '',
            'og_tags': {},
            'twitter_tags': {},
            'json_ld': [],
            'content_length': 0
        }
        
        # メタ情報の取得
        meta_desc = soup.find('meta', attrs={'name': 'description'})
        if meta_desc:
            page_info['meta_description'] = meta_desc.get('content', '')
            
        meta_keywords = soup.find('meta', attrs={'name': 'keywords'})
        if meta_keywords:
            page_info['meta_keywords'] = meta_keywords.get('content', '')
            
        meta_robots = soup.find('meta', attrs={'name': 'robots'})
        if meta_robots:
            page_info['meta_robots'] = meta_robots.get('content', '')
            
        # カノニカルURL
        canonical = soup.find('link', attrs={'rel': 'canonical'})
        if canonical:
            page_info['canonical_url'] = canonical.get('href', '')
        
        # 見出しタグの取得
        page_info['h1_tags'] = [h1.get_text().strip() for h1 in soup.find_all('h1')]
        page_info['h2_tags'] = [h2.get_text().strip() for h2 in soup.find_all('h2')]
        page_info['h3_tags'] = [h3.get_text().strip() for h3 in soup.find_all('h3')]
        
        # 画像の分析
        images = soup.find_all('img')
        page_info['total_images'] = len(images)
        page_info['images_without_alt'] = sum(1 for img in images if not img.get('alt'))
        
        # リンクの分析
        links = soup.find_all('a', href=True)
        domain = urlparse(url).netloc
        
        for link in links:
            href = link.get('href')
            if href.startswith('http'):
                if domain in href:
                    page_info['internal_links'] += 1
                else:
                    page_info['external_links'] += 1
            elif href.startswith('/'):
                page_info['internal_links'] += 1
        
        # OGタグ
        og_tags = soup.find_all('meta', property=lambda x: x and x.startswith('og:'))
        for tag in og_tags:
            prop = tag.get('property')
            content = tag.get('content')
            if prop and content:
                page_info['og_tags'][prop] = content
                
        # Twitterタグ
        twitter_tags = soup.find_all('meta', attrs={'name': lambda x: x and x.startswith('twitter:')})
        for tag in twitter_tags:
            name = tag.get('name')
            content = tag.get('content')
            if name and content:
                page_info['twitter_tags'][name] = content
        
        # JSON-LD構造化データ
        scripts = soup.find_all('script', type='application/ld+json')
        for script in scripts:
            try:
                page_info['json_ld'].append(json.loads(script.string))
            except:
                pass
        
        # コンテンツ長
        content_text = soup.get_text()
        page_info['content_length'] = len(content_text.strip())
        
        return page_info
    
    def analyze_with_claude(self, page_info):
        """Claude APIを使用してSEO分析と改善案を生成"""
        
        analysis_prompt = f"""
以下のWebページのSEO情報を分析し、SEOの観点から改善案を提案してください。

=== ページ情報 ===
URL: {page_info['url']}
タイトル: {page_info['title']}
メタディスクリプション: {page_info['meta_description']}
H1タグ: {page_info['h1_tags']}
H2タグ: {page_info['h2_tags'][:5]}  # 最初の5個のみ表示
H3タグ: {page_info['h3_tags'][:5]}  # 最初の5個のみ表示
画像数: {page_info['total_images']}（alt属性なし: {page_info['images_without_alt']}）
内部リンク数: {page_info['internal_links']}
外部リンク数: {page_info['external_links']}
カノニカルURL: {page_info['canonical_url']}
メタロボッツ: {page_info['meta_robots']}
OGタグ: {page_info['og_tags']}
Twitterカード: {page_info['twitter_tags']}
構造化データ: {'あり' if page_info['json_ld'] else 'なし'}
コンテンツ文字数: {page_info['content_length']}

=== 分析要件 ===
1. 現状の良い点と問題点を整理
2. SEO的観点での具体的な改善案を優先度付きで提示
3. 各改善案について実装方法も含めて詳細に説明
4. モバイルファーストインデックス、Core Web Vitalsも考慮
5. 日本語SEOの特性も踏まえた提案

回答は構造化された形式で、実行可能なアクションアイテムとして提示してください。
"""

        try:
            response = self.client.messages.create(
                model="claude-3-5-sonnet-20241022",
                max_tokens=4000,
                temperature=0.3,
                messages=[
                    {
                        "role": "user", 
                        "content": analysis_prompt
                    }
                ]
            )
            
            return response.content[0].text
            
        except Exception as e:
            raise Exception(f"Claude API呼び出しエラー: {e}")
    
    def analyze_url(self, url):
        """URLを分析してSEO改善案を生成"""
        print(f"URL取得中: {url}")
        html_content = self.fetch_url_content(url)
        
        print("ページ情報抽出中...")
        page_info = self.extract_page_info(html_content, url)
        
        print("Claude APIでSEO分析中...")
        analysis = self.analyze_with_claude(page_info)
        
        return {
            'page_info': page_info,
            'seo_analysis': analysis
        }

def main():
    analyzer = SEOAnalyzer()
    url = "https://yamalog.flow-t.net/gear/tiger-bottle/"
    
    try:
        result = analyzer.analyze_url(url)
        
        print("\n" + "="*80)
        print("SEO分析結果")
        print("="*80)
        print(result['seo_analysis'])
        print("\n" + "="*80)
        
        # 結果をファイルに保存
        with open('seo_analysis_result.txt', 'w', encoding='utf-8') as f:
            f.write(f"URL: {url}\n")
            f.write("="*80 + "\n")
            f.write(result['seo_analysis'])
            f.write("\n" + "="*80 + "\n")
            f.write(f"詳細データ: {json.dumps(result['page_info'], ensure_ascii=False, indent=2)}")
        
        print("分析結果をseo_analysis_result.txtに保存しました")
        
    except Exception as e:
        print(f"エラーが発生しました: {e}")

if __name__ == "__main__":
    main()