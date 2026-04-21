# Task 09: Öffentlicher Bereich - Anfrage-Formular (Checkout)

## Ziel
Erfassung der Kontaktdaten und Absenden der unverbindlichen Anfrage.

## Anforderungen
- Route: `/anfrage-absenden` oder Integration in `/anfrageliste`.
- Formular-Felder:
  - Anrede, Vorname, Nachname.
  - E-Mail, Telefonnummer.
  - Firma, Adresse (Straße, PLZ, Ort).
  - Nachrichtentext (optional).
- Validierung der Eingaben (E-Mail Format, Pflichtfelder).
- Speichern der Anfrage in der Datenbank (Zusammenführung mit Session-Warenkorb).
- Versand einer Bestätigungs-E-Mail an den Kunden und Benachrichtigung an den Admin.
- Weiterleitung auf eine "Danke-Seite".

## Akzeptanzkriterien
- Anfrage wird korrekt in der Datenbank persistiert.
- Session wird nach erfolgreichem Absenden geleert.
- E-Mails werden korrekt ausgelöst (Mailtrap oder lokaler SMTP).
