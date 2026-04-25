# Task 09: Öffentlicher Bereich - Anfrage-Formular (Checkout)

## Ziel
Erfassung der Kontaktdaten und Absenden der Anfrage.

## Anforderungen
- Route: `/anfrage-absenden` oder Integration in `/anfrageliste`.
- Formular-Felder:
  - Anrede, Vorname, Nachname.
  - E-Mail, Telefonnummer.
  - Firma, Adresse (Straße, PLZ, Ort).
  - Anfragezeitraum mit Startdatum und Enddatum (Pflichtfeld).
  - Nachrichtentext (optional).
- Validierung der Eingaben (E-Mail Format, Pflichtfelder, Enddatum >= Startdatum).
- Speichern der Anfrage in der Datenbank (Zusammenführung mit Session-Warenkorb).
- Versand einer Bestätigungs-E-Mail an den Kunden und Benachrichtigung an den Admin.
- Weiterleitung auf eine "Danke-Seite".

## Akzeptanzkriterien
- Anfrage wird korrekt in der Datenbank persistiert.
- Anfragezeitraum (Start- und Enddatum) wird mit der Anfrage gespeichert.
- Session wird nach erfolgreichem Absenden geleert.
- E-Mails werden korrekt ausgelöst (Mailtrap oder lokaler SMTP).
