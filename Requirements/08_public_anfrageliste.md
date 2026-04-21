# Task 08: Öffentlicher Bereich - Anfrageliste (Session-basiert)

## Ziel
Verwaltung der Produkte, die ein Nutzer für eine Anfrage vorgemerkt hat.

## Anforderungen
- Implementierung eines Session-basierten Warenkorbs ("Anfrageliste").
- Livewire-Komponente zur Anzeige der Liste (`/anfrageliste`).
- Funktionen:
  - Hinzufügen von Produkten (mit Auswahl des Merkmalswerts).
  - Ändern der Menge.
  - Entfernen von Positionen.
- Anzeige der Gesamtsumme (optional, falls gewünscht).

## Akzeptanzkriterien
- Nutzer können Produkte hinzufügen, ohne eingeloggt zu sein.
- Die Liste bleibt beim Navigieren durch die Seite erhalten.
- Echtzeit-Updates der Liste via Livewire bei Änderungen.
