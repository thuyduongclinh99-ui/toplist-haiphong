# Đưa Toplist Hải Phòng lên mạng miễn phí (InfinityFree)

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
