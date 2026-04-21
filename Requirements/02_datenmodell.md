# Task 02: Datenmodell & Migrationen

## Ziel
Definition der Datenbankstruktur für Kategorien, Produkte und Anfragen.

## Anforderungen
- **Migration: Categories**
  - Name, Slug, Bild (optional).
- **Migration: Products**
  - category_id (FK), Name, Slug, Beschreibung, Keywords (JSON oder String), Bild-Pfad, Preis (Decimal).
  - Feature-Name (String, optional), Feature-Values (JSON, optional).
- **Migration: Inquiries**
  - Kunden-Details (Vorname, Nachname, E-Mail, Telefon, Nachricht, Firma, Adresse).
  - Status (z.B. neu, in Bearbeitung, abgeschlossen).
- **Migration: Inquiry_Items** (Pivot für Produkte in einer Anfrage)
  - inquiry_id, product_id, Menge.
- **Models & Factories**
  - Erstellung der Models mit entsprechenden Relations (BelongsTo, HasMany).
  - Factories für Testdaten.

## Akzeptanzkriterien
- Alle Migrationen laufen fehlerfrei durch.
- Models haben die korrekten Eloquent-Beziehungen definiert.
- Testdaten können via Seeder generiert werden.
