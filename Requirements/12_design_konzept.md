# Design-Konzept: Green Temper Coffee

Dieses Dokument beschreibt das visuelle Konzept für die Anwendung, basierend auf einem "Apple-Like" Stil und den Farben des Logos.

## 1. Visuelle Identität & Stil
Der Stil orientiert sich an der Design-Philosophie von Apple: **Minimalismus, Klarheit und Hochwertigkeit.**

*   **Clean Design:** Viel Whitespace (Leerraum), um den Fokus auf den Inhalt zu lenken.
*   **Abgerundete Ecken:** Großzügige `border-radius` (z.B. `12px` bis `24px`) für Karten und Container.
*   **Sanfte Schatten:** Dezente `box-shadows` statt harter Linien, um Tiefe zu erzeugen (Glassmorphism-Ansätze für Overlays).
*   **Hochwertige Typografie:** Klare, serifenlose Schriften mit optimierten Zeilenabständen.

## 2. Farbpalette
Basierend auf dem Logo ("Green Temper Coffee") wird eine helle, naturverbundene Farbwelt geschaffen.

| Farbe | Hex-Code (Vorschlag) | Verwendung |
| :--- | :--- | :--- |
| **Primär (Deep Forest)** | `#0B6645` | Logo-Farbe, Navigation, Primäre Buttons, Headings |
| **Sekundär (Soft Mint)** | `#E8F5E9` | Hintergründe für Sektionen, Hover-Zustände |
| **Akzent (Fresh Leaf)** | `#4CAF50` | Erfolgsmeldungen, interaktive Elemente |
| **Hintergrund (Off-White)**| `#FBFBFD` | Haupt-Hintergrund (Apple-typisch fast weiß) |
| **Text (Ink)** | `#1D1D1F` | Haupttext (sehr dunkles Grau, kein reines Schwarz) |
| **Text (Muted)** | `#86868B` | Nebentext, Beschreibungen |

## 3. UI-Komponenten (Apple-Stil)

### Karten (Cards)
*   Hintergrund: Weiß oder sehr helles Grau.
*   Schatten: `shadow-sm` oder `shadow-md` von Tailwind.
*   Interaktion: Leichte Skalierung beim Hover (`hover:scale-[1.02]`).

### Buttons
*   **Primary:** Abgerundet, Hintergrund Primärgrün, weißer Text.
*   **Secondary:** Umrandet (Outline) oder hellgrauer Hintergrund mit grünem Text.
*   **Ecken:** Stark abgerundet (`rounded-full` oder `rounded-2xl`).

### Navigation
*   Semi-transparent (`backdrop-blur-md` mit `bg-white/80`).
*   Fixiert am oberen Rand mit dezentem Schatten nach dem Scrollen.

## 4. Bildsprache
*   Hochauflösende Fotos mit natürlichem Licht.
*   Freigestellte Produktbilder für die Kategorien-Kacheln (ähnlich wie im Apple Store).
*   Fokus auf Details und Qualität.

## 5. Umsetzung in Tailwind CSS (Konfiguration)
Um diesen Stil konsequent umzusetzen, sollten folgende Werte in der `tailwind.config.js` hinterlegt werden:

```javascript
// Beispiel-Erweiterung
theme: {
    extend: {
        colors: {
            'gtc-green': '#0B6645',
            'gtc-light': '#FBFBFD',
            'gtc-mint': '#E8F5E9',
        },
        borderRadius: {
            'apple': '18px',
        }
    }
}
```
