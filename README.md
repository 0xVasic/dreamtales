# DreamTales

DreamTales je mala PHP web aplikacija koja generiše personalizovane priče pomoću **Google Gemini API-ja**.  
Korisnici mogu uneti podatke o detetu (ime, uzrast, ton priče, temu...) i dobiti unikatnu priču, sa mogućnošću deljenja i pregleda prethodnih priča.

![GUI](https://i.imgur.com/6zOOAco.png)
![GUI](https://i.imgur.com/z3B369P.png)
---

## Funkcionalnosti
- Generisanje priča pomoću **Google Gemini API-ja**
- Čuvanje i pregled podeljenih priča
- Paginacija podeljenih priča
- Deljenje priča sa prijateljima ili porodicom
- Ocena priče i poruka za roditelje

---

## Kako pokrenuti projekat lokalno

### 1. Preuzmite projekat
```bash
git clone https://github.com/0xVasic/dreamtales.git
cd dreamtales
```

### 2. Instalirajte XAMPP ili WAMP
Pokrenite Apache server.

### 3. Postavite Google Gemini API ključ
1. Registrujte se na [Google AI Studio](https://makersuite.google.com/)  
2. Kreirajte API ključ  
3. U fajlu gde se poziva API (generate.php) dodajte svoj API ključ.

### 4. Testiranje
Nakon što ste pokrenuli Apache server, dodali API ključ, možete pokrenuti sajt.
Ukoliko je sve kako treba, pojaviće vam se početna stranica gde možete uneti potrebne podatke.
Kako bi proverili da li aplikacija komunicira sa Google Gemini, kliknite na dugme "Napravi priču".
Priča bi trebala da se pojavi kao i rezime za roditelje sa strane ukoliko je podešen odgovarajući API ključ.

