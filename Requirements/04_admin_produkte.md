# Task 04: Privater Bereich - Produkte-CRUD

## Ziel
Umfassende Verwaltung der Produkte im Administrationsbereich.

## Anforderungen
- Livewire-Komponente zur Produktverwaltung (Tabelle mit Suche/Filter).
- Erstellungs- und Bearbeitungsmaske:
  - Auswahl der Kategorie (Dropdown).
  - Name, Beschreibung (Textarea/Editor).
  - Keywords (Suchmaschinenoptimierung).
  - Preis-Eingabe.
  - Bild-Upload mit Vorschau.
- **Optionale Merkmale**:
  - Dynamisches Hinzufügen eines Merkmals-Namens (z.B. "Farbe").
  - Liste von möglichen Werten (z.B. "Schwarz, Silber, Weiß").
- Validierung der Pflichtfelder.

## Akzeptanzkriterien
- Admin kann Produkte vollständig verwalten.
- Bilder werden korrekt im Storage gespeichert und verlinkt.
- Optionale Merkmale werden als JSON in der Datenbank persistiert.
- Flux UI Komponenten werden für alle Eingaben verwendet.
