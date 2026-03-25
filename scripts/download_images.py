"""
Script download ảnh từ Joomla enews.agu.edu.vn về storage local.
Chạy từ thư mục gốc Laravel: python scripts/download_images.py --cookie "sl-session=OA1OPYuNw2l9XVATzPbNbw=="
"""
import argparse
import os
import sys
import time
import urllib.request
import sqlite3
import pymysql
import json

# ── Cấu hình DB (đọc từ .env) ──────────────────────────────────────────────
def read_env(env_path='.env'):
    config = {}
    with open(env_path, 'r', encoding='utf-8') as f:
        for line in f:
            line = line.strip()
            if '=' in line and not line.startswith('#'):
                k, _, v = line.partition('=')
                config[k.strip()] = v.strip().strip('"').strip("'")
    return config

def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('--cookie', required=True, help='sl-session cookie value (e.g. sl-session=abc123==)')
    parser.add_argument('--limit', type=int, default=0, help='Limit số ảnh (0=tất cả)')
    args = parser.parse_args()

    env = read_env('.env')
    db_host = env.get('DB_HOST', '127.0.0.1')
    db_port = int(env.get('DB_PORT', 3306))
    db_name = env.get('DB_DATABASE', 'enews')
    db_user = env.get('DB_USERNAME', 'root')
    db_pass = env.get('DB_PASSWORD', '')

    print(f"DB: {db_user}@{db_host}:{db_port}/{db_name}")

    try:
        conn = pymysql.connect(host=db_host, port=db_port, db=db_name,
                               user=db_user, password=db_pass, charset='utf8mb4')
        cursor = conn.cursor()
    except Exception as e:
        print(f"❌ Kết nối DB thất bại: {e}")
        sys.exit(1)

    # Lấy posts có thumbnail Joomla
    query = "SELECT id, thumbnail FROM posts WHERE thumbnail IS NOT NULL AND thumbnail != '' AND thumbnail NOT LIKE 'http%' AND thumbnail LIKE 'images/%'"
    if args.limit > 0:
        query += f" LIMIT {args.limit}"
    cursor.execute(query)
    posts = cursor.fetchall()
    total = len(posts)
    print(f"📊 Tổng số bài cần tải: {total}")

    # Thư mục lưu ảnh
    storage_dir = os.path.join('storage', 'app', 'public', 'joomla-images')
    os.makedirs(storage_dir, exist_ok=True)

    cookie = args.cookie  # ví dụ: "sl-session=OA1OPYuNw2l9XVATzPbNbw=="
    base_url = 'https://enews.agu.edu.vn'

    success = fail = skip = 0

    for i, (post_id, thumbnail) in enumerate(posts):
        remote_path = thumbnail.lstrip('/')
        remote_url  = f"{base_url}/{remote_path}"
        local_name  = os.path.basename(remote_path)
        local_path  = os.path.join(storage_dir, local_name)
        db_path     = f"joomla-images/{local_name}"

        # In progress
        pct = int((i+1)/total*100)
        print(f"\r[{i+1}/{total}] {pct}% — {local_name[:40]}", end='', flush=True)

        # Skip nếu đã tồn tại
        if os.path.exists(local_path) and os.path.getsize(local_path) > 100:
            cursor.execute("UPDATE posts SET thumbnail=%s WHERE id=%s", (db_path, post_id))
            conn.commit()
            skip += 1
            continue

        # Download
        try:
            req = urllib.request.Request(
                remote_url,
                headers={
                    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/122.0.0.0 Safari/537.36',
                    'Accept': 'image/avif,image/webp,image/apng,image/*,*/*;q=0.8',
                    'Referer': 'https://enews.agu.edu.vn/',
                    'Cookie': cookie,
                }
            )
            with urllib.request.urlopen(req, timeout=15) as resp:
                content = resp.read()

            if len(content) < 100:
                fail += 1
                continue

            with open(local_path, 'wb') as f:
                f.write(content)

            cursor.execute("UPDATE posts SET thumbnail=%s WHERE id=%s", (db_path, post_id))
            conn.commit()
            success += 1

        except Exception as e:
            fail += 1

    print(f"\n\n✅ Thành công : {success}")
    print(f"⏭️  Đã tồn tại : {skip}")
    print(f"❌ Thất bại   : {fail}")

    cursor.close()
    conn.close()

if __name__ == '__main__':
    main()
