# MyChat

MyChat to prosty komunikator internetowy, umożliwiający użytkownikom wymianę wiadomości w czasie rzeczywistym. Projekt został stworzony w języku **PHP** z wykorzystaniem **AJAX**, **MySQL** oraz biblioteki **PHPMailer** do obsługi resetowania hasła.

## 🛠 Technologie:
- PHP
- MySQL (MariaDB)
- AJAX
- JavaScript (jQuery)
- Bootstrap
- PHPMailer (do obsługi resetowania hasła)

## 📥 Instalacja

### 1️⃣ **Sklonuj repozytorium**:
   ```sh
   git clone https://github.com/rafaraf75/MyChat.git
   cd MyChat
2️⃣ Import bazy danych:
W folderze database znajdziesz plik mychat.sql.
Zaimportuj go do swojej bazy danych za pomocą phpMyAdmin lub komendy:
mysql -u root -p mychat < database/mychat.sql
3️⃣ Konfiguracja PHPMailer (Resetowanie hasła):
Aby resetowanie hasła działało poprawnie, musisz dodać swój adres Gmail i hasło aplikacji do bazy danych.

Otwórz phpMyAdmin i przejdź do tabeli email_config.
Wstaw nowy rekord z Twoim adresem Gmail oraz hasłem aplikacji Gmail:
INSERT INTO email_config (email, password) VALUES ('twój-email@gmail.com', 'twoje-hasło-aplikacji');
4️⃣ Uruchomienie aplikacji:
Uruchom lokalny serwer (np. XAMPP, MAMP).
Otwórz przeglądarkę i przejdź do:
http://localhost/MyChat/signup.php
