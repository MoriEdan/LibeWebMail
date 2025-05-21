# 🚀 LibeWebMail

<div align="center">

![LibeWebMail Logo](https://raw.githubusercontent.com/MoriEdan/LibeWebMail/main/assets/logo.png)

[![GitHub license](https://img.shields.io/github/license/MoriEdan/LibeWebMail?color=blue)](https://github.com/MoriEdan/LibeWebMail/blob/main/LICENSE)
[![Stars](https://img.shields.io/github/stars/MoriEdan/LibeWebMail?style=flat-square&color=yellow)](https://github.com/MoriEdan/LibeWebMail/stargazers)
[![Forks](https://img.shields.io/github/forks/MoriEdan/LibeWebMail?style=flat-square&color=orange)](https://github.com/MoriEdan/LibeWebMail/network/members)
[![Issues](https://img.shields.io/github/issues/MoriEdan/LibeWebMail?style=flat-square&color=red)](https://github.com/MoriEdan/LibeWebMail/issues)
[![Pull Requests](https://img.shields.io/github/issues-pr/MoriEdan/LibeWebMail?style=flat-square&color=purple)](https://github.com/MoriEdan/LibeWebMail/pulls)

**Özgür, hızlı, ve modern bir web tabanlı e-posta istemcisi**

[🌐 Demo](#) | [📖 Dökümantasyon](#) | [🤝 Katkıda Bulunma](#katkıda-bulunma) | [📜 Lisans](#lisans)

</div>

---

## ✨ Özellikler

<div align="center">
  <table>
    <tr>
      <td align="center">🔒<br><b>Güvenli</b></td>
      <td align="center">🚄<br><b>Hızlı</b></td>
      <td align="center">💻<br><b>Responsive</b></td>
      <td align="center">🎨<br><b>Özelleştirilebilir</b></td>
    </tr>
    <tr>
      <td align="center">🔌<br><b>Genişletilebilir</b></td>
      <td align="center">🌙<br><b>Karanlık Mod</b></td>
      <td align="center">🔍<br><b>Hızlı Arama</b></td>
      <td align="center">📱<br><b>Mobil Uyumlu</b></td>
    </tr>
  </table>
</div>

LibeWebMail, modern ve kullanıcı dostu bir e-posta deneyimi sunmak için tasarlanmış açık kaynaklı bir web tabanlı e-posta istemcisidir. Güçlü özellikleri, özelleştirilebilir arayüzü ve hızlı performansı ile öne çıkar.

### Neden LibeWebMail?

- **Özgür ve Açık**: Tamamen açık kaynaklı ve ücretsizdir
- **Gizlilik Odaklı**: Verilerinizi korumak için uçtan uca şifreleme
- **Modern Arayüz**: Basit, şık ve kullanımı kolay tasarım
- **Çoklu Hesap Desteği**: Tüm e-posta hesaplarınızı tek yerden yönetin
- **Hızlı ve Hafif**: Modern teknolojilerle geliştirilmiş, performans odaklı

## 🖼️ Ekran Görüntüleri

<div align="center">
  <img src="https://raw.githubusercontent.com/MoriEdan/LibeWebMail/main/assets/screenshot1.png" alt="Ana Ekran" width="45%">
  <img src="https://raw.githubusercontent.com/MoriEdan/LibeWebMail/main/assets/screenshot2.png" alt="Posta Kutusu" width="45%">
</div>

## 🚀 Kurulum

```bash
# Repo'yu klonlayın
git clone https://github.com/MoriEdan/LibeWebMail.git

# Klasöre gidin
cd LibeWebMail

# Bağımlılıkları yükleyin
npm install

# Geliştirme sunucusunu başlatın
npm run dev
```

## 🔧 Yapılandırma

LibeWebMail'i özelleştirmek ve ihtiyaçlarınıza göre yapılandırmak kolaydır:

```javascript
// config.js

module.exports = {
  theme: 'light', // 'light', 'dark', 'auto'
  defaultView: 'inbox',
  notificationsEnabled: true,
  refreshInterval: 60, // saniye cinsinden
  maxAttachmentSize: 25, // MB cinsinden
  supportedLanguages: ['tr', 'en', 'fr', 'de', 'es']
};
```

## 📚 API Kullanımı

```javascript
// Mail göndermek için
await LibeWebMail.sendMail({
  to: 'alici@ornek.com',
  subject: 'Merhaba Dünya',
  body: 'Bu bir test e-postasıdır.',
  attachments: []
});

// Mail almak için
const messages = await LibeWebMail.getMessages({
  folder: 'inbox',
  limit: 50,
  offset: 0
});
```

## 📋 Yol Haritası

- [x] Temel e-posta işlevselliği
- [x] Duyarlı arayüz tasarımı
- [x] Çoklu hesap desteği
- [ ] E-posta şablonları 
- [ ] Gelişmiş arama özellikleri
- [ ] Çevrimdışı modu
- [ ] End-to-end şifreleme
- [ ] Takvim entegrasyonu

## 🤝 Katkıda Bulunma

Katkılarınızı bekliyoruz! Nasıl katkıda bulunabileceğiniz hakkında daha fazla bilgi için [CONTRIBUTING.md](CONTRIBUTING.md) dosyasına göz atın.

## 👥 Katkıda Bulunanlar

<a href="https://github.com/MoriEdan/LibeWebMail/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=MoriEdan/LibeWebMail" />
</a>

## 📜 Lisans

Bu proje [MIT Lisansı](LICENSE) altında lisanslanmıştır.

---

<div align="center">
  
### ⭐ Bu projeyi beğendiyseniz yıldız vermeyi unutmayın! ⭐

</div>

<div align="center">
  <sub>❤️ ile <a href="https://github.com/MoriEdan">MoriEdan</a> tarafından yapıldı</sub>
</div>
