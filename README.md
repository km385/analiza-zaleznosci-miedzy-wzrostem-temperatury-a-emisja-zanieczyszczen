# Wstępny opis
Aplikacja zestawia dane statystyczne z zakresu różnego typu emisji wyemitowanych przez kraje na
całym świecie wraz z oszacowaniem zmiany średniej temperatury względem okresu 1951-1980. Aby móc korzystać z aplikacji, 
użytkownik musi założyć konto i zalogować się. Aplikacja wykorzystuje tokeny JWT (JSON Web Token) 
w celu uwierzytelnienia i autoryzacji użytkowników. Po pomyślnym zalogowaniu użytkownik ma dostęp 
do przeglądania danych statystycznych. Tabele z danymi można filtrować według krajów, rodzajów zanieczyszczeń 
oraz źródeł emisji. Dla każdego zestawu danych automatycznie generowany jest wykres, który ułatwia interpretację 
zgromadzonych informacji.

Użytkownik ma możliwość eksportu danych z bazy do plików XML (Extensible Markup Language) oraz 
JSON (JavaScript Object Notation). W przypadku posiadania uprawnień administratora, użytkownik 
ma również możliwość importowania danych do bazy. Aplikacja wykorzystuje techniki ORM (Object-Relational Mapping) 
do dostępu do bazy danych. W celu zapewnienia bezpieczeństwa i integralności danych, baza danych korzysta z technik
izolacji transakcji. Aplikacja obsługuje zapytania API zgodnie z architekturą REST (Representational State Transfer).
Cała aplikacja została w pełni skonteneryzowana.


Aplikacja do funkcjonowania wykorzystuje dane zbiory danych:

* Roczne oszacowania zmian średniej temperatury powierzchniowej mierzonej w odniesieniu do klimatologii bazowej, odpowiadającej okresowi 1951-1980 (Annual Surface Temperature Change)

* Informacje dotyczące krajowych emisji tradycyjnych zanieczyszczeń powietrza (Emissions of air pollutants)
# Środowisko pracy
Aplikacja przedstawiona w ramach tego projektu wykorzystuje następujące technologie:

* Ubuntu 22.04
* PHP 8.1.2
* Laravel 10
* Moduł Laravel-sail 1.22
* MySQL
* Composer 2.2.6
* Docker 24.0.2
* Docker Compose v2.18.1

# Procedura uruchamiania
Wsl2 wymagane do poprawnego działania laravel sail.

W celu uruchomienia aplikacji należy wykonać dane komendy:
```sh 
composer update
```
```sh 
composer install
```
```sh 
php artisan sail:install # Wybrać mysql
```
```sh 
./vendor/bin/sail up
```
```sh 
./vendor/bin/sail artisan migrate
```
```sh
cp AIR_EMISSIONS.csv FAOSTAT.csv storage/app/ 
```
```sh 
./vendor/bin/sail artisan db:seed --class=DatabaseSeeder
```
```sh 
Aplikacja znajduje się pod adresem: localhost:80
```
