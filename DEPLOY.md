# Đưa Toplist Hải Phòng lên mạng miễn phí

Có hai bản, đều không mua hosting hay tên miền:

| Bản | Địa chỉ | Nền tảng |
|---|---|---|
| **Render + Supabase + GitHub** (đang chạy) | https://toplist-haiphong.onrender.com | Render chạy WordPress từ repo GitHub, dữ liệu và ảnh trên Supabase |
| InfinityFree (dự phòng) | http://toplisthaiphong.free.nf | Hosting PHP/MySQL miễn phí |

## Bản Render + Supabase + GitHub

- **Mã nguồn:** https://github.com/thuyduongclinh99-ui/toplist-haiphong (công khai, không chứa mật khẩu hay khóa).
- **Render** (Web Service miễn phí, Docker): chạy WordPress 6.5.13 + PHP 8.2 theo [deploy/render/Dockerfile](deploy/render/Dockerfile), cấu hình trong [render.yaml](render.yaml).
- **Supabase** (project `toplisthaiphong`, Singapore):
  - PostgreSQL lưu toàn bộ dữ liệu WordPress, qua lớp chuyển đổi PG4WP.
  - Storage, bucket công khai `media`, lưu ảnh qua giao thức S3 ([mu-plugin](deploy/render/mu-plugins/supabase-media.php)).
- **Quản trị:** https://toplist-haiphong.onrender.com/wp-admin. Tên đăng nhập là `toplisthp`, mật khẩu là giá trị bạn đặt ở biến `WP_ADMIN_PASSWORD` trên Render.

**Khi khởi động** ([render-entrypoint.sh](deploy/render/render-entrypoint.sh)), mỗi lần:
- Cài WordPress nếu database còn trống.
- Tải gói tiếng Việt.
- Bật Row Level Security để API công khai của Supabase không đọc được bảng WordPress.
- Tải lại những ảnh chưa có trên Storage.

Lần đầu còn nạp thêm 28 bài song ngữ.

**Biến môi trường trên Render** (Render → dịch vụ → *Environment*):

| Biến | Lấy ở đâu |
|---|---|
| `WORDPRESS_DB_HOST`, `WORDPRESS_DB_USER` | Supabase → **Connect → Direct → Session pooler** (host kèm `:5432`, user `postgres.<mã project>`) |
| `WORDPRESS_DB_PASSWORD` | Mật khẩu database Supabase (chỉ chữ và số) |
| `SUPABASE_URL` | `https://<mã project>.supabase.co` |
| `SUPABASE_S3_ACCESS_KEY_ID`, `SUPABASE_S3_SECRET_ACCESS_KEY` | Supabase → **Storage → S3 Configuration → New access key** |
| `SUPABASE_S3_REGION`, `SUPABASE_BUCKET` | `ap-southeast-1`, `media` |
| `WP_ADMIN_USER`, `WP_ADMIN_PASSWORD`, `WP_ADMIN_EMAIL` | Tài khoản quản trị tạo ở lần khởi động đầu |
| `AI_API_KEY` | Khóa API cho chatbot du lịch: [openrouter.ai](https://openrouter.ai) → **Keys → Create Key**. Bỏ trống thì chatbot chỉ gợi ý bài viết liên quan |
| `AI_MODEL` | Mặc định `openrouter/free` (miễn phí, 50 câu hỏi/ngày cho cả site). Có thể đổi sang model trả phí của OpenRouter mà không phải sửa code |

**Chatbot du lịch** ([inc/chatbot.php](theme/toplist-haiphong/inc/chatbot.php)): nút chat ở góc phải mọi trang, chỉ trả lời câu hỏi về du lịch, gợi ý kèm link bài trên site. Mỗi khách hỏi tối đa 15 câu trong 10 phút. Khi AI hết lượt miễn phí hoặc lỗi, chatbot gợi ý các bài viết khớp với câu hỏi. Muốn dùng nhà cung cấp khác có API kiểu OpenAI, đặt thêm `AI_API_BASE` là địa chỉ API của nhà cung cấp đó (phần trước `/chat/completions`).

**Cập nhật site:** sửa code rồi đẩy lên nhánh `main`, Render tự build và deploy lại (khoảng 5 phút). Thêm `[skip render]` vào nội dung commit nếu chỉ sửa tài liệu.

**Giới hạn của gói miễn phí:**
- Render tự ngủ sau 15 phút không có người vào, lần vào sau chờ khoảng 1 phút.
- Supabase tạm dừng project sau 7 ngày không hoạt động.
- GitHub Actions [keepalive.yml](.github/workflows/keepalive.yml) gọi site mỗi 10 phút để tránh cả hai việc trên (biến repo `SITE_URL`). Nếu Supabase vẫn bị tạm dừng, vào dashboard bấm **Restore**, dữ liệu không mất.
- **Không cài plugin hay theme từ trang quản trị:** ổ đĩa Render bị xóa mỗi lần khởi động lại, nên trang quản trị đã khóa chức năng này (`DISALLOW_FILE_MODS`). Muốn thêm plugin thì thêm vào Dockerfile.
- Ảnh tải lên từ thư viện Media được tự đẩy sang Supabase Storage nên không mất.

---

# Bản dự phòng: InfinityFree

Không cần mua hosting hay tên miền. Site sẽ có địa chỉ dạng `toplisthaiphong.rf.gd` hoặc `toplisthaiphong.free.nf` (tên miền phụ miễn phí), có HTTPS.

**Vì sao chọn InfinityFree:** miễn phí, chạy PHP + MySQL nên dùng được WordPress đầy đủ (cài theme, plugin riêng), có trình cài WordPress 1 chạm và SSL miễn phí.

**Giới hạn cần biết:**
- Mỗi file tối đa 10 MB. Theme (khoảng 100 KB) và plugin dữ liệu (khoảng 2,5 MB) đều nằm trong giới hạn.
- Tài nguyên dùng chung nên tốc độ vừa phải; hợp với site giới thiệu, không hợp với lượng truy cập lớn.
- Điều khoản của host miễn phí có thể thay đổi. Khi site có nhiều người xem, nên chuyển sang hosting trả phí: cách chuyển y hệt các bước dưới đây.

Chuẩn bị 2 file trong thư mục `dist/` (tạo lại bằng `npm run zip` nếu cần):

- `toplist-haiphong.zip`: theme
- `toplist-demo-content.zip`: plugin nạp dữ liệu mẫu

---

## Bước 1: Tạo tài khoản và hosting

1. Vào https://www.infinityfree.com, bấm **Register**, đăng ký bằng email và xác nhận email.
2. Trong trang quản lý, bấm **Create Account** (tạo hosting).
3. Chọn **Subdomain**, gõ tên muốn dùng (ví dụ `toplisthaiphong`), chọn đuôi (`.rf.gd`, `.free.nf`…), rồi bấm tạo. Đợi vài phút để tài khoản hoạt động.

## Bước 2: Cài WordPress

1. Mở hosting vừa tạo, bấm **Control Panel**, tìm **Softaculous Apps Installer** rồi chọn **WordPress**, bấm **Install**.
2. Điền:
   - **Choose Domain**: tên miền phụ vừa tạo, để trống ô thư mục (*In Directory*).
   - **Site Name**: Toplist Hải Phòng
   - **Admin Username / Password / Email**: tự đặt và **ghi lại**. Không dùng `admin` làm tên đăng nhập.
   - **Select Language**: Vietnamese.
3. Bấm **Install** và đợi xong.
4. Mở `https://<tên-miền-của-bạn>/wp-admin` và đăng nhập.

## Bước 3: Bật HTTPS

Trong trang quản lý InfinityFree, vào **SSL Certificates** và cấp chứng chỉ miễn phí cho tên miền phụ, làm theo hướng dẫn trên màn hình. Sau khi có SSL, vào **Cài đặt → Tổng quan** trong WordPress và đổi cả hai địa chỉ sang `https://…` nếu đang là `http://`.

## Bước 4: Cài theme

1. **Giao diện → Giao diện → Thêm mới → Tải giao diện lên**.
2. Chọn `toplist-haiphong.zip`, bấm **Cài đặt**, rồi **Kích hoạt**.

## Bước 5: Cài Polylang (song ngữ Việt – Anh)

1. **Plugin → Cài mới**, tìm **Polylang**, bấm **Cài đặt**, rồi **Kích hoạt**.
2. Polylang sẽ mở trình hướng dẫn cài đặt. Bấm **Bỏ qua / Not now** ở cuối trang, vì plugin dữ liệu ở bước sau sẽ tự tạo hai ngôn ngữ. Nếu lỡ làm hết trình hướng dẫn với Tiếng Việt và English thì cũng không sao.

> Muốn site chỉ có tiếng Việt thì bỏ qua bước này.

## Bước 6: Nạp nội dung mẫu

1. **Plugin → Cài mới → Tải plugin lên**, chọn `toplist-demo-content.zip`, bấm **Cài đặt**, rồi **Kích hoạt**.
2. Vào **Công cụ → Nạp dữ liệu Toplist**. Kiểm tra cả hai dòng đều có dấu ✅, rồi bấm **Nạp dữ liệu mẫu**.
3. Trang sẽ tự chuyển qua nhiều bước (khoảng 1–2 phút). Đừng đóng tab cho đến khi thấy **"Đã nạp xong"**.
   - Nếu lỡ bị dừng giữa chừng (mất mạng, host báo lỗi tạm thời), cứ bấm nạp lại: các bước tự bỏ qua phần đã làm và không tạo trùng.
4. Mở trang chủ để xem. Phiên bản tiếng Anh nằm ở `/en/`.
5. Nạp xong có thể **tắt và xóa** plugin "Toplist Hải Phòng – Dữ liệu mẫu". Nội dung đã nạp vẫn giữ nguyên.

Plugin sẽ:
- tạo 11 danh mục, 28 bài (kèm ảnh và thông tin địa điểm), 3 trang, menu;
- đặt tên site, múi giờ Việt Nam, định dạng ngày `dd/mm/yyyy`, đường dẫn dạng `/ten-bai-viet/`;
- tạo bản tiếng Anh cho mọi bài, danh mục, trang và menu (khi có Polylang).

## Bước 7: Hoàn thiện

- **Giao diện → Tùy biến → Toplist Hải Phòng**: sửa giới thiệu, địa chỉ, hotline, email.
- **Ngôn ngữ → Bản dịch**: dịch các chữ bạn tự nhập trong Tùy biến sang tiếng Anh.
- 12 bài thuộc Mua sắm, Thể thao, Vận tải, Y tế, Giáo dục, Làm đẹp chưa có ảnh, điểm và số lượt đánh giá: bổ sung trong trang sửa bài (hộp *Thông tin địa điểm* và *Ảnh đại diện*).
- Muốn có tên miền riêng (ví dụ `toplisthaiphong.com` bạn đang có) thì InfinityFree cho phép trỏ tên miền đã mua vào (**Addon Domains**), không mất thêm phí hosting.

## Khi cập nhật theme sau này

Sửa code, chạy `npm run zip`, rồi vào **Giao diện → Thêm mới → Tải giao diện lên** và chọn file zip mới. WordPress sẽ hỏi có thay thế bản đang dùng không: chọn **Thay thế bản hiện tại bằng bản đã tải lên**.
