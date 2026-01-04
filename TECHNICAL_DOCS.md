# CSDashWoo v1.0 - Teknik Dokümantasyon

## 1. Mimari Şema

CSDashWoo eklentisi, WordPress admin panelini özelleştirmek ve iş odaklı hale getirmek amacıyla modüler bir yapı ile geliştirilmiştir. Temel mimari bileşenler:

- **Core Bootstrap**: csdashwoo.php
- **Admin Menü Yönetimi**: includes/admin/
- **Ayarlar Sistemi**: includes/settings/
- **Bildirim Sistemi**: includes/notifications/ (gelecek sürümler için planlanan)
- **Dashboard Widget'ları**: includes/dashboard/ (gelecek sürümler için planlanan)

## 2. Klasör & Dosya Yapısı

```
csdashwoo/
├── csdashwoo.php (ana bootstrap dosyası)
├── assets/
│   └── menu-ui.js (menü yöneticisi JS)
├── includes/
│   ├── admin/
│   │   ├── class-menu-applier.php (menü uygulama mantığı)
│   │   ├── class-menu-labels.php (menü etiketleri)
│   │   ├── class-menu-layout.php (menü düzeni)
│   │   ├── class-menu-others.php (diğer ayarlar menüsü)
│   │   ├── class-menu-reader.php (menü okuma)
│   │   └── class-menu-ui.php (menü yöneticisi arayüzü)
│   └── settings/
│       └── class-settings.php (ayarlar sayfası)
└── README.md
```

## 3. Modül Modül Sorumluluklar

### Core (csdashwoo.php)
- Eklenti başlatma ve sabit tanımlamaları
- Gerekli sınıfların yüklenmesi
- WordPress hook'larının tanımlanması

### Admin Menü Modülü
- **Menu_Applier**: Menü uygulama ve rol bazlı filtreleme
- **Menu_Labels**: Menü etiketlerinin çevirisini sağlar
- **Menu_Layout**: Menü düzenini tanımlar ve kaydeder
- **Menu_Others**: "Diğer Ayarlar" menüsünü oluşturur
- **Menu_Reader**: Mevcut WordPress menü yapısını okur
- **Menu_UI**: Drag & drop menü yöneticisi arayüzü

### Settings Modülü
- **Settings**: Eklenti ayarları için admin sayfası oluşturur

## 4. Hook / Filter Listesi

### Actions
- `admin_menu`: Admin menü sayfalarını oluşturma
- `admin_enqueue_scripts`: Admin panelinde script/css yükleme
- `wp_ajax_csdashwoo_save_menu`: AJAX menü kaydetme işlemi
- `admin_init`: Admin başlatma işlemleri

### Filters
- `csdashwoo_menu_layout`: Menü düzenini özelleştirme
- `csdashwoo_menu_labels`: Menü etiketlerini özelleştirme

## 5. Bildirim Lifecycle (Gelecek Sürümler için Planlanan)

Bildirim sistemi için planlanan yaşam döngüsü:
1. Bildirim tetiklenir (örn. yeni sipariş)
2. Bildirim veritabanına kaydedilir
3. Bildirim listesine eklenir
4. Kullanıcıya gösterilir
5. Kullanıcı etkileşimine göre durum güncellenir

## 6. Dashboard Veri Akışı (Gelecek Sürümler için Planlanan)

Dashboard widget'ları için planlanan veri akışı:
1. Widget'lar için veri toplama
2. Verilerin WordPress hook'ları ile dashboard'a eklenmesi
3. Widget'ların kullanıcı tercihlerine göre gösterilmesi

## 7. Menü Mimarisi

CSDashWoo, WordPress core menü yapısını bozmadan çalışır:
- Mevcut menü öğeleri silinmez, sadece yeniden sıralanır
- Gerekli menü öğeleri "Diğer Ayarlar" altına taşınır
- Kullanıcı rollerine göre menü erişimi filtrelenir
- Menü düzeni kullanıcı bazlı değil, eklenti seviyesinde yönetilir

## 8. Sıfırdan Yazma Rehberi (Geliştirme Notları)

### 1. Hafta: Temel Altyapı
- csdashwoo.php dosyası oluşturulur
- Gerekli sabitler tanımlanır
- Ana sınıf yapısı kurulur

### 2. Hafta: Menü Yönetimi
- Menü okuma ve uygulama sınıfları yazılır
- Temel menü yeniden sıralama işlevi eklenir
- Menü etiketleme sistemi kurulur

### 3. Hafta: Rol Bazlı Erişim
- Kullanıcı rollerine göre menü filtreleme eklenir
- Farklı kullanıcı türleri için erişim kontrolleri yapılır

### 4. Hafta: Arayüz ve Kullanıcı Deneyimi
- Drag & drop menü yöneticisi arayüzü
- Ayarlar sayfası
- Kullanıcı dostu UI/UX

### 5. Hafta: Entegrasyonlar ve Geliştirmeler
- WooCommerce entegrasyonları
- Bildirim sistemi (gelecek sürümler)
- Dashboard widget'ları (gelecek sürümler)

## 9. Geliştirme İlkeleri

- WordPress kodlama standartlarına uyulması
- Güvenlik önlemlerinin alınması (nonce, capability check)
- Çoklu dil desteğinin sağlanması
- Performans etkilerinin minimum tutulması
- Mevcut WordPress iş akışlarını bozmama