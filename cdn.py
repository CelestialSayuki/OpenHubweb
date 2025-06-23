import os
import re
import requests
import time
from urllib.parse import urlparse
def get_all_cdn_urls(base_path):
    print(f"--- STEP 1: Extracting URLs from local static files ---")
    print(f"Scanning files in: {base_path}...")
    cdn_urls = set()
    pattern = re.compile(r'href="(http://cdn\.hubweb\.cn/[^"]+)"|"url":\s*"(http://cdn\.hubweb\.cn/[^"]+)"')
    for root, _, files in os.walk(base_path):
        for file_name in files:
            if file_name.endswith(('.html', '.php')):
                file_path = os.path.join(root, file_name)
                try:
                    with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
                        content = f.read()
                        if not content.strip():
                            continue
                        matches = pattern.findall(content)
                        if not matches:
                            pass
                        for match_tuple in matches:
                            for url in match_tuple:
                                if url:
                                    cdn_urls.add(url)
                except Exception as e:
                    print(f"    Error reading file {file_path}: {e}")
    print(f"Found {len(cdn_urls)} unique CDN URLs from local files.")
    return sorted(list(cdn_urls))
def download_cdn_files(urls, download_dir="downloaded_cdn_files"):
    script_dir = os.path.dirname(os.path.abspath(__file__))
    output_base_dir = os.path.join(script_dir, download_dir)
    if not os.path.exists(output_base_dir):
        os.makedirs(output_base_dir)
        print(f"Created download directory: {output_base_dir}")
    for i, url in enumerate(urls):
        print(f"Downloading {i+1}/{len(urls)}: {url}")
        try:
            parsed_url = urlparse(url)
            path_components = parsed_url.path.lstrip('/').split('/')
            local_dir_path = os.path.join(output_base_dir, *path_components[:-1])
            local_file_path = os.path.join(local_dir_path, path_components[-1])
            if not os.path.exists(local_dir_path):
                os.makedirs(local_dir_path)
            if os.path.exists(local_file_path):
                print(f"  File already exists: {local_file_path}, skipping download.")
                continue
            with requests.get(url, stream=True, timeout=30) as r:
                r.raise_for_status()
                with open(local_file_path, 'wb') as f:
                    for chunk in r.iter_content(chunk_size=8192):
                        f.write(chunk)
            print(f"  Successfully downloaded to: {local_file_path}")
        except requests.exceptions.RequestException as req_err:
            print(f"  Network error downloading {url}: {req_err}")
        except Exception as e:
            print(f"  An unexpected error occurred for {url}: {e}")
if __name__ == "__main__":
    local_website_root = "/Users/zhr/Desktop/hubweb.cn/"
    download_destination_folder = os.path.join(os.path.expanduser("~/Desktop"), "downloaded_cdn_files")
    extracted_urls = get_all_cdn_urls(local_website_root)
    print(f"\nTotal unique CDN URLs found from local files: {len(extracted_urls)}")
    if extracted_urls:
        print("\n--- STEP 2: Starting download of all extracted CDN files ---")
        download_cdn_files(extracted_urls, download_destination_folder)
        print("\nAll accessible CDN files processed.")
    else:
        print("No CDN URLs found to download from local files.")
