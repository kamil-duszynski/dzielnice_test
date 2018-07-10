# Aplikacja testowa "Dzielnice"

1. Ściagnij repozytorium do środowiska lokalnego za pomocą standardowego polecenia `git clone`,
2. Przejdź do katalogu projektu i uruchom polecenie `composer install` (zakładam, że masz zaintalowany menager pluginów Composer),
3. Utwórz bazę danych o nazwie "zadanie_testowe" (kodowanie: `utf-8-general-ci`), przypisz do niej użytkownika,
4. W katalogu głównym projektu zlokalizuj plik .env i nadpisz parametr `DATABASE_URL` podstawiając własną nazwę użytkownika mającego dostep do bazy danych i jego hasło,
5. Będąc nadal w linii komend uruchom polecenie: `php bin/console doctrine:migrations:migrate`, postępuj według poleceń kreatora, akceptujac wszystkie komunikaty - utworzony zostanie schemat bazy danych,
6. Uruchom polecenie: `php bin/console server:start` - wyświetlony zostanie adres lokalny projektu,
7. Otwórz przeglądarkę i przejdź pod wskazany w poprzednim punkcie adres a następnie pod `/district/`

---
# Aby zaktualizować listę dzielnic w linii komend
1. Uruchom polecenie: `php bin/cosnole import:districts` (dopisując nazwę miasta po spacji uruchomisz import tylko dla tego wybranego miasta)
