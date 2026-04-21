# Task 05: Privater Bereich - Nutzer-Verwaltung

## Ziel
Verwaltung der Benutzer, die Zugriff auf das Backend haben.

## Anforderungen
- CRUD-Interface für Benutzer (Name, E-Mail, Passwort).
- Integration von Fortify zur Passwort-Änderung und Profil-Verwaltung.
- Absicherung der Admin-Routen durch Middleware (auth).

## Akzeptanzkriterien
- Admin kann weitere Benutzer anlegen oder löschen.
- Passwort-Reset-Funktion (via Fortify Standard).
- Zugriff auf /admin ist nur für authentifizierte Nutzer möglich.
