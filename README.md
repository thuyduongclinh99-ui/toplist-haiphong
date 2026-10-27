# Toplist Hải Phòng – theme WordPress

Theme cho cổng toplist du lịch, ẩm thực và lưu trú Hải Phòng, dựng theo thiết kế Stitch (`stitch_hai_phong_toplist_portal.zip`).

## Cấu trúc

| Đường dẫn | Nội dung |
|---|---|
| `theme/toplist-haiphong/` | Mã nguồn theme (PHP, JS, CSS đã build) |
| `src/tailwind.css`, `tailwind.config.js` | Nguồn CSS và bảng màu, font, cỡ chữ của thiết kế |
| `plugins/toplist-demo-content/` | Plugin nạp dữ liệu mẫu: bài tiếng Việt (`data/posts.php`) và tiếng Anh (`data/posts-en.php`), ảnh đại diện, các bước nạp (`includes/importer.php`) |
| `blueprint.json` | Cấu hình WordPress Playground để chạy thử trên máy (dùng chính plugin trên để nạp dữ liệu) |
| `dist/toplist-haiphong.zip`, `dist/toplist-demo-content.zip` | File theme và plugin dữ liệu mẫu để cài lên hosting (tạo bằng `npm run zip`) |
| `DEPLOY.md` | Hướng dẫn đưa site lên mạng miễn phí |

## Chạy thử trên máy

Cần Node.js. Lần đầu chạy `npm install`, sau đó:

```bash
npm run dev
```

Mở http://127.0.0.1:9400 (tiếng Việt) hoặc http://127.0.0.1:9400/en/ (tiếng Anh). Mỗi lần khởi động là một site mới: theme đã kích hoạt, plugin Polylang, 28 bài mẫu trong 11 danh mục bằng cả hai thứ tiếng, menu và các trang Giới thiệu, Gửi đề xuất, Tất cả danh mục. Lần đầu chạy cần mạng để tải WordPress, gói tiếng Việt và Polylang; nếu bước tải gói ngôn ngữ báo lỗi thì chạy lại lệnh.

Sửa giao diện: chỉnh file trong `theme/toplist-haiphong/`. Nếu có thêm class Tailwind thì build lại CSS:

```bash
npm run build:css
```

## Cài lên hosting

1. Tạo file theme: `npm run zip`, kết quả nằm ở `dist/toplist-haiphong.zip`.
2. Trong trang quản trị WordPress: **Giao diện → Giao diện → Thêm mới → Tải giao diện lên**, chọn file zip rồi kích hoạt.
3. **Giao diện → Menu**: gán menu cho các vị trí *Menu chính*, *Menu chân trang: Khám phá*, *Liên hệ*, *Pháp lý*. Nếu chưa gán, menu chính tự hiện 5 danh mục nhiều bài nhất, các danh mục còn lại nằm trong mục xổ xuống "Danh mục khác".
   - Để tự tạo mục **Danh mục khác** trong menu chính: thêm một *Liên kết tự tạo* tên "Danh mục khác" (đường dẫn trỏ tới trang `danh-muc`), rồi kéo các danh mục Y Tế, Giáo Dục, Thể Thao, Làm Đẹp, Mua Sắm vào ngay dưới nó và thụt vào một bậc để thành mục con. Menu chính hiển thị tối đa 2 cấp.
4. **Giao diện → Tùy biến → Toplist Hải Phòng**: sửa khối giới thiệu, link "Gửi đề xuất", địa chỉ, hotline, email, địa điểm bản đồ. Logo đổi ở **Nhận dạng site**.
5. Tạo trang có đường dẫn `danh-muc` để có trang "Tất cả danh mục" (theme tự dùng mẫu `page-danh-muc.php`, kể cả cho bản dịch của trang này). Trang có đường dẫn `gui-de-xuat` là đích mặc định của nút "Gửi đề xuất".

## Song ngữ Việt – Anh

Theme dùng plugin miễn phí **Polylang**. Không cài Polylang thì site chạy bình thường bằng tiếng Việt.

1. **Plugin → Cài mới**, tìm "Polylang", cài và kích hoạt.
2. **Ngôn ngữ → Ngôn ngữ**: thêm *Tiếng Việt* (đặt làm mặc định) và *English*. Bài cũ chưa có ngôn ngữ thì gán vào Tiếng Việt khi Polylang hỏi.
3. Dịch danh mục (**Bài viết → Danh mục**, cột dấu +), rồi dịch từng bài và trang. Hộp *Thông tin địa điểm* ở bản tiếng Anh cần nhập lại (hoặc bật đồng bộ trường tùy chỉnh trong **Ngôn ngữ → Cài đặt → Đồng bộ hóa**).
4. **Giao diện → Menu**: mỗi vị trí menu có một ô cho từng ngôn ngữ, chọn menu tiếng Anh cho ô *English*.
5. Chữ của giao diện (nút, tiêu đề khối…) tự đổi theo ngôn ngữ, bảng dịch nằm ở `inc/strings-en.php`. Chữ nhập trong Tùy biến được dịch ở **Ngôn ngữ → Bản dịch**.

Theme tìm 3 khối bài trên trang chủ và trang "Tất cả danh mục" theo đường dẫn tiếng Việt (`du-lich`, `am-thuc`, `luu-tru`, `danh-muc`), rồi tự lấy bản dịch tương ứng.

## Tìm kiếm và lọc theo danh mục

Làm theo kiểu Shopee: gõ từ khóa trước, bộ lọc mới hiện ra.

- **Ô tìm kiếm** nằm ở đầu thanh bên (trên điện thoại còn có trong menu ☰). Chỉ tìm trong bài viết, không tìm trong các trang tĩnh.
- **Trang kết quả tìm kiếm** có khung **Bộ lọc tìm kiếm** ở cột trái, gồm 2 nhóm ô tích: **Theo địa điểm** (phường/xã, chính là các *Thẻ* của bài viết) và **Theo danh mục**.
  - Mỗi nhóm hiện 4 mục nhiều bài nhất, số còn lại nằm sau nút **Thêm**.
  - Tích được nhiều mục cùng lúc. Trong một nhóm, bài khớp *bất kỳ* mục nào đã tích; giữa hai nhóm, bài phải khớp cả hai (ví dụ Ẩm Thực hoặc Lưu Trú, tại Phường Hồng Bàng).
  - Nút **Xóa tất cả** bỏ mọi bộ lọc nhưng giữ từ khóa.
  - Trên điện thoại, khung lọc thu gọn thành nút "Bộ lọc tìm kiếm" phía trên kết quả.

Muốn bài xuất hiện khi lọc theo địa điểm, hãy gắn thẻ phường/xã cho bài.

## Dữ liệu mỗi bài viết

Trong trang soạn bài có hộp **Thông tin địa điểm**: điểm đánh giá (0–5), số lượt đánh giá, địa chỉ, khoảng giá, số điện thoại. Các trường này được lưu trong meta `_thp_rating`, `_thp_review_count`, `_thp_address`, `_thp_price`, `_thp_phone`.

- **Bảng Top 10** ở thanh bên: xếp theo điểm, rồi theo số lượt đánh giá. Chỉ bài có cả hai trường mới được đưa vào.
- **Slide đầu trang chủ**: lấy các bài được *ghim* (Sticky) có ảnh đại diện; nếu chưa đủ 4 bài thì thêm bài mới nhất.
- **Dải ảnh "Điểm đến du lịch Hải Phòng"** dưới slide đầu trang chủ: tự chạy ngang, lấy ảnh đại diện của tối đa 12 bài mới nhất trong danh mục `du-lich` (bài không có ảnh đại diện sẽ không hiện). Trên ảnh chỉ ghi tên địa điểm, lấy từ phần tiêu đề bài đứng trước dấu hai chấm (ví dụ "Hải đăng Long Châu: hành trình…" hiện thành "Hải đăng Long Châu"), nên hãy đặt tiêu đề theo dạng "Tên địa điểm: mô tả". Rê chuột vào thì dừng. Muốn thêm ảnh vào dải này, chỉ cần đăng thêm bài Du lịch có ảnh đại diện.
- **3 khối bài trên trang chủ**: lấy theo danh mục `du-lich`, `am-thuc`, `luu-tru`. Muốn đổi thì dùng filter `thp_home_sections`.
- **Lượt xem**: tự đếm khi khách mở bài (không tính lúc bạn đang đăng nhập).

> 12 bài thuộc 6 danh mục Mua sắm, Thể thao, Vận tải, Y tế, Giáo dục, Làm đẹp là bài viết mới (site gốc chưa có bài ở các mục này): chưa có ảnh, điểm và số lượt đánh giá. Hãy bổ sung trước khi đăng thật.

> Site toplisthaiphong.com hiện tại dùng theme khác với các trường dữ liệu riêng. Nếu chuyển site đó sang theme này, cần chép điểm, địa chỉ, giá… sang các meta ở trên thì Top 10 và các nhãn đánh giá mới hiện ra.
