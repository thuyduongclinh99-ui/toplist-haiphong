# Kế hoạch: WordPress trên Render + Supabase + GitHub

**Trạng thái:** hoàn thành (27/10/2026). Site: https://toplist-haiphong.onrender.com. Repo: https://github.com/thuyduongclinh99-ui/toplist-haiphong.

**Thay đổi so với kế hoạch:** ảnh đi qua API S3 của Supabase Storage thay vì REST, vì REST từ chối khóa `sb_secret_` mới (lỗi `Invalid Compact JWS`). Bucket `media` được tạo trên dashboard.
**Mục tiêu:** bản Toplist Hải Phòng chạy công khai tại `https://<ten>.onrender.com`, không mua hosting hay tên miền. Mã nguồn trên GitHub, dữ liệu và ảnh trên Supabase. Bản InfinityFree giữ làm dự phòng.

## Kiến trúc

| Thành phần | Dùng để | Gói |
|---|---|---|
| GitHub (repo riêng tư) | Mã nguồn: Dockerfile, theme, plugin; tự động "đánh thức" site định kỳ (GitHub Actions) | Miễn phí |
| Render – Web Service (Docker) | Chạy WordPress 6.5 + PHP 8.2 + Apache | Free |
| Supabase – PostgreSQL | Cơ sở dữ liệu (bài viết, cài đặt, người dùng) qua PG4WP | Free (500 MB) |
| Supabase – Storage (bucket công khai `media`) | Ảnh tải lên, vì ổ đĩa Render bị xóa mỗi lần khởi động lại | Free (1 GB) |

Trong ảnh Docker đã cài sẵn: theme `toplist-haiphong`, Polylang, plugin dữ liệu mẫu, PG4WP, và một mu-plugin nhỏ đẩy ảnh lên Supabase Storage. Lần khởi động đầu, container tự cài WordPress, kích hoạt mọi thứ và nạp 28 bài song ngữ.

Bí mật (mật khẩu database, khóa Supabase, mật khẩu quản trị WordPress) **không đưa lên GitHub**: bạn tự dán vào mục Environment trên Render.

## Giai đoạn

| # | Việc | Ai làm | Xong khi |
|---|---|---|---|
| 0 | Tạo tài khoản GitHub, Supabase, Render; tạo project Supabase | Bạn | Có 3 tài khoản và 1 project Supabase vùng Singapore |
| 1 | Viết Dockerfile, script khởi động, mu-plugin ảnh, `render.yaml`; đẩy lên repo GitHub riêng tư | Tôi (bạn xác nhận đăng nhập `gh` trên trình duyệt) | Repo có code, không chứa bí mật |
| 2 | **Thử sớm:** deploy bản tối thiểu (WordPress + PG4WP + Supabase), kiểm tra cài đặt và trang chủ | Tôi; bạn dán biến môi trường vào Render | WordPress chạy trên Supabase, không lỗi database |
| 3 | Bật theme, Polylang, nạp dữ liệu, ảnh lên Supabase Storage | Tôi | Đủ 28 bài × 2 ngôn ngữ, ảnh hiển thị, menu và bộ lọc chạy |
| 4 | GitHub Actions gọi site mỗi 10 phút để Render không ngủ và Supabase không bị tạm dừng | Tôi | Workflow chạy xanh |
| 5 | Kiểm tra toàn bộ và cập nhật DEPLOY.md | Tôi | Trang chủ, bài, danh mục, tìm kiếm, `/en/` đều chạy; có hướng dẫn |

## Rủi ro và cách xử lý

- **PG4WP không tương thích** (chỉ mới thử tới WP 6.5): khóa WordPress 6.5; giai đoạn 2 thử sớm trước khi làm tiếp. Nếu Polylang hoặc trình nạp lỗi trên PostgreSQL thì sửa truy vấn hoặc báo lại bạn trước khi đổi hướng.
- **Render free ngủ sau 15 phút, lần vào đầu chậm khoảng 1 phút:** giai đoạn 4 giữ site thức (trong 750 giờ miễn phí mỗi tháng).
- **Supabase free tạm dừng sau 7 ngày không hoạt động:** cũng nhờ giai đoạn 4; nếu vẫn bị dừng thì bấm "Restore" trong Supabase, không mất dữ liệu.
- **WordPress 6.5 cũ hơn bản mới nhất:** chấp nhận cho bản demo; không cài thêm plugin lạ.
- **Plugin cài từ trang quản trị sẽ mất khi Render khởi động lại:** mọi plugin phải thêm vào Dockerfile.

## Không làm

Không mua gì. Không đổi bản InfinityFree. Không dùng tên miền riêng.
