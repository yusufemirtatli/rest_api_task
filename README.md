<p style="color: #ff2f1a">PHP PROJELERİMİZİ AYAĞA KALDIRMAK VE DATABASEDEN YARARLANMAK İÇİN BİR SUNUCU BAŞLATMAMIZ GEREK
<br>BEN XAMPP KULLANDIĞIM İÇİN APACHE VE MYSQL SUNUCULARIMI ÇALIŞTIRARAK BU KOMUTLARI ÇALIŞTIRACAĞIM</p>

**PROJEYİ AYAĞA KALDIRMAK İÇİN GEREKLİ OLAN KURULUMLAR**

`composer install
`
 **composer için gerekli dosyaların kurulumu**

`cp .env.example .env
`
.env dosyasını oluşturur
<br>.env dosyasını açın ve **"DB_CONNECTİON = sqlite"** kısmını **"DB_CONNECTİON = mysql"** olarak değiştirip altındaki # kısımlarını silin

`php artisan key:generate
`
**.env dosyanızdaki eksik yerleri doldurur**

`npm install
`
**bu kod ile gerekli nodemodule dosyalarını kurabilirsiniz**

`php artisan migrate
`
**migration dosyalarından databaseinizi oluşturur**

`php artisan db:seed
`
**databasemizin içinde seeder ve factoryleri yani önceden belirlenmiş ve random dataları yerleştirmemize yarar**

`php artisan serve
`
**projeyi ayağa kaldırır**

`php artisan schedule:work
`
**projedeki zamanlanmış görevlerin çalışmasını sağlar

<p style="color: #ff2f1a">KURULUMLAR BU KADAR PROJE DOSYALARININ İÇİNDE BULUNAN POSTMAN COLLECTİONU KENDİ POSTMANİNİZE İMPORT EDEREK API ENDPOİNTLERİN ÇALIŞIP ÇALIŞMADIĞINI KONTROL EDEBİLİRSİNİZ
</p>

**PROJE İŞLEMLERİ**

1. **AUTH İŞLEMLERİ**: Projede kullanıcı oluşturma ve kullanıcı giriş yapabilmek için endpointler oluşturdum. Bu kullanıcı siteye giriş yaptığı zaman JWT sayesinde bir sonraki girişlerinde şifre ve parola istemeden hızlıca giriş sağlayabilecek

2. **EVENT İŞLEMLERİ**: Projede eventleri listelemek ve oluşturmak için enpointler oluşturdum. Bunların arasında sadece adminlerin erişebileceği endpointler var. Bu işlemi bir rol tablosu oluşturarark ve user tablomun içine role_id sütununu ekleyerek bir doğrulama sağladım.

3.**VENUE İŞLEMLERİ**: Mekan bilgileri için uygun seederlar oluşturdum bu seederlar sayesinde php artisan db:seed komutunu kullandığınızda databasenize otomatik olarak venue dataları eklenecek

4.**SEAT İŞLEMLERİ**: Her mekanın ve her eventin koltuklarını ayrı ayrı sıralayabileceğim 2 adet endpoint ve koltukları manuel olarak blocklayıp boşaltabileceğim 2 adet endpoint oluşturdum

5.**RESERVATION İŞLEMLERİ**: Burada rezervasyon oluşturma rezervasyon listeleme, rezervasyon onaylama ve rezervasyon iptali gibi endpointlerim var. Ayrıca rezervazyon onaylandıktan sonra otomatik olarak bütün reservation items için birer adet benzersiz koda sahip bilet üretiliyor. Rezervasyon iptali de eventin başlamasına 24 saatten az kaldıysa iptal edilemiyor. Rezervasyon onayı da 15 dakika içinde olması gerekiyor.
Aynı anda aynı koltuklar rezerve edilemeyip 15 dakika dolduğunda eğer onaylanmamışsa koltuklar tekrardan serbest bırakılıyor.

**TİCKET İŞLEMLERİ**: Oluşturulan biletleri listeleyip, pdf formatında indirip, başka kullanıcılara transfer edebileceğiniz endpointler oluşturdum. Burada ticketları pdf halinde indirmek için ekstra bir kütüphane kullandım. Bilet iptali için onaylanmış rezervasyonu iptal edilmesi gerek otomatik olarak o rezervasyondaki biletler de iptal ediliyor. Bilet transferi sadece kullanılmamış biletler için geçerli.

**MİMARİ HAKKINDA**

1. **CONTROLLER**: Controllerde endpointlerimin neler yapacağını ayarlayabiliyoruz.

2. **ENUM KULLANIMI**: Tablolarımmın statülerini ayarlamada yardımcı olması için kullanıldı.

3. **DATABASE DOSYALARI**: Migration dosyaları database şemamızı tutmakta ve database oluşturmaya yardımcı olmakta. Seederlar oluşturduğumuz database e hızlı bir şekilde veri girişi yapmaya yardımcı olur.

4. **ROUTİNG**: api.php içinde projedeki bütün endpointleri bulabilirsiniz.

