const CACHE_NAME = 'enews-cache-v1';

// Các URL tài nguyên cốt lõi cần được cache sẵn để giao diện không bị giật lag khi rớt mạng
const CORE_ASSETS = [
    '/',
    '/offline',
    '/manifest.json',
    '/images/logo.png'
    // Bỏ qua CSS/JS của Vite ở đây vì tên file luôn thay đổi (có hàm băm),
    // Chúng ta sẽ tự động lưu chúng qua sự kiện fetch.
];

// Sự kiện cài đặt (Install)
self.addEventListener('install', event => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('Opened cache');
                return cache.addAll(CORE_ASSETS);
            })
    );
});

// Sự kiện kích hoạt (Activate) - Dọn dẹp cache cũ nếu có
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

// Sự kiện lấy dữ liệu (Fetch)
self.addEventListener('fetch', event => {
    // Không chặn các request không phải GET hoặc request API hệ thống admin
    if (event.request.method !== 'GET' || 
        event.request.url.includes('/admin') ||
        event.request.url.includes('/login') ||
        event.request.url.includes('/livewire/')) {
        return;
    }

    const isHtmlRequest = event.request.headers.get('accept').includes('text/html');

    if (isHtmlRequest) {
        // [CHIẾN LƯỢC 1 cho Bài viết / Trang web]: Network First, fallback to Cache. 
        // Luôn cố gắng tải trang mới nhất từ mạng. Nếu mất mạng, kiểm tra xem nó đã có trong Cache chưa.
        // Nếu chưa từng đọc (chưa hề có trong Cache), trả về trang Lỗi Mất mạng (/offline).
        event.respondWith(
            fetch(event.request)
                .then(response => {
                    // Cập nhật trang này vào bộ đệm để đọc offline lần sau
                    const respClone = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(event.request, respClone));
                    return response;
                })
                .catch(async () => {
                    // Mất mạng -> Lấy từ Cache
                    const cachedResponse = await caches.match(event.request);
                    if (cachedResponse) return cachedResponse;
                    
                    // Chưa từng cache -> Load trang offline
                    return caches.match('/offline');
                })
        );
    } else {
        // [CHIẾN LƯỢC 2 cho Hình ảnh / CSS / JS / Fonts]: Cache First, fallback to Network.
        // Xử lý cực nhanh, tải ngay từ máy người dùng nếu có.
        event.respondWith(
            caches.match(event.request)
                .then(cachedResponse => {
                    // Trả về bộ đệm nếu có
                    if (cachedResponse) {
                        // Vẫn fetch ngầm để cập nhật bản mới nhất cho lần sau (Stale While Revalidate)
                        fetch(event.request).then(response => {
                            if (response && response.status === 200) {
                                caches.open(CACHE_NAME).then(cache => cache.put(event.request, response));
                            }
                        }).catch(() => {});
                        return cachedResponse;
                    }
                    
                    // Nếu không có trong đệm, tải từ mạng và đưa vào đệm
                    return fetch(event.request).then(response => {
                        // Không cache các response lỗi
                        if(!response || response.status !== 200 || response.type !== 'basic') {
                            // Đối với các ảnh resource ngoài (như UI avatar, cors images), type sẽ là 'opaque' - ta cũng có thể cache nó.
                            if(response && response.type === 'opaque') {
                                const respClone = response.clone();
                                caches.open(CACHE_NAME).then(cache => cache.put(event.request, respClone));
                            }
                            return response;
                        }

                        const respClone = response.clone();
                        caches.open(CACHE_NAME).then(cache => cache.put(event.request, respClone));
                        return response;
                    });
                })
        );
    }
});
