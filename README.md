PHP PROJELERİMİZİ AYAĞA KALDIRMAK VE DATABASEDEN YARARLANMAK İÇİN BİR SUNUCU BAŞLATMAMIZ GEREK
BEN XAMPP KULLANDIĞIM İÇİN APACHE VE MYSQL SUNUCULARIMI ÇALIŞTIRARAK BU KOMUTLARI ÇALIŞTIRACAĞIM

PROJEYİ AYAĞA KALDIRMAK İÇİN GEREKLİ OLAN KURULUMLAR

composer install  composer için gerekli dosyaların kurulumu

cp .env.example .env  .env dosyasını oluşturur
.env dosyasını açın ve "DB_CONNECTİON = sqlite" kısmını "DB_CONNECTİON = mysql" olarak değiştirip altındaki # kısımlarını silin

php artisan key:generate  .env dosyanızdaki eksik yerleri doldurur

npm install  bu kod ile gerekli nodemodule dosyalarını kurabilirsiniz

php artisan migrate  migration dosyalarından databaseinizi oluşturur

php artisan db:seed  databasemizin içinde seeder ve factoryleri yani önceden belirlenmiş ve random dataları yerleştirmemize yarar

php artisan serve  projeyi ayağa kaldırır

KURULUMLAR BU KADAR PROJE DOSYALARININ İÇİNDE BULUNAN POSTMAN COLLECTİONU KENDİ POSTMANİNİZE İMPORT EDEREK API ENDPOİNTLERİN ÇALIŞIP ÇALIŞMADIĞINI KONTROL EDEBİLİRSİNİZ
