# Takeda Backend Project

## Goals

Backend kiszolgálókörnyezet futtatásához Debian 10 operációs rendszert használunk. A webes kiszolgálószerver az Apache2, a konfigurációkat erre optimalizáljuk. A backend szkriptek PHP nyelven íródnak, a szervernek képesnek kell lennie minimum a PHP 7.4 kiszolgálására. A későbbi esetleges terméktámogatási időszak alatt ügyelünk arra, hogy a PHP 7.x minden további alverzióját telepítsük, hiszen ezek fontos biztonsági megoldásokat, frissítéseket tartalmaznak. A backend rendszert úgy tervezzük meg, hogy azok a lehető legpontosabb kompatibilitást nyújtsák a frissebb rendszerkiadásokkal. A backend adatbáziskiszolgálója a MySQL, ebből telepítéskor az aktuálisan legfrissebb verziót fogjuk használni, a későbbiekben pedig frissíteni.
A minősített, titkosításra szoruló elemeket AES-256-al kódoljuk, amennyiben későbbi visszafejtés szükséges (például bejelentkezési token az azonosításhoz, vagy olyan egyedi adatok, melyekhez csak a felhasználó férhet hozzá). BCRYPT-el kódoljuk azokat, amennyiben nincsen szükség az eredeti változó ismeretére (például jelszó) a felhasználó számára sem.
A teljes backend architechtúra dockerizált környezetben fog üzemelni, melynek a laradock biztosít alapot. A PHP esetén a legelterjedtebb keretrendszert, a Laravel-t alkalmazzuk.
Minden backend egység komponensekre bomlik a rendszerben, melyek közül vannak külső komponensek (dependency) és belső, saját komponensnek, modulok.

### TODOS (Part 1)

- [x] DB diagram
- [ ] Docker fájl készítése
- [ ] Modellek megnevezése
- [ ] Model struktúra tervezése
- [ ] Modellek megvalósítása

### Külső linkek

 - DbDiagram: [https://dbdiagram.io/d/62471f40d043196e39dc6477](https://dbdiagram.io/d/62471f40d043196e39dc6477)