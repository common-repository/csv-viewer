=== Plugin Name ===
Name: Csv Viewer
Tags: csv, viewer, products, productlist, lists, view, overview, settings, style, images, upload, edit, product
Version: 1.0.2
Requires at least: Wordpress 3 or higher
Tested up to: 3.0.1
Contributors: Jansen CWS
Stable tag: 1.0.2

== Description  ==
With the Csv Viewer plugin you can display your CSV-files on your WordPress-page. You can simply upload your CSV-file, change them settings and style, upload images for in the file.
For example, if you want to show a productlist whit images, build a CSV-file, place your information, upload file, change what you want, and put it on your WP-page.

NL: Met de Csv Viewer plugin kun je CSV-bestanden weergeven in je WordPress-pagina. Je kunt eenvoudig je CSV-bestanden uploaden, instellingen en opmaak aanpassen en afbeeldingen uploaden.
Bijvoorbeeld, je wilt een productoverzicht weergegeven met afbeeldingen erin. Maak een csv-bestand aan, plaats de informatie erin, upload het bestand, pas de dingen aan die je wil en plaatst het overzicht op de pagina.

== Manual ==

1. Create on your workstation a CSV File, for example with Microsoft Excel or Open Office Math.
2. You can place headers into the file, and when you want to place colums whit images, type on that colums the name of the image. For example image.jpg
3. Upload your CSV-file.
4. In 'Settings' you can see the Id, and change the name of the file, delimitor, if you use headers, and the number of the imagecolum. When you don't have an imagecolum, set it to 0 (null).
5. In 'Style' you can see and change the style of de view. Change it if you want and save it.
6. You can upload images for your CSV-file. The names of the images have to match with the imagenames in your CSV-file. You can upload multiple images at once.
7. You can see in an overview the images you've uploaded per file. It's also possible to make a selection and to remove it.
8. To show a preview of the view, press 'Preview'.
9. Go to your wordpress page (not post) and place the tag: [csv id=..]. Set the value from 'id' to the id from your CSV-file.
10. If you want to remove the CSV-file, press 'Remove'.
11. If you want to remove the CSV-viewer, you have at first to remove all the csv-documents. Click after it on Settings -> CSV Viewer. You see the button 'Uninstall plugin'. Press it, confirm the message, deactivate the plugin and remove it.

NL:
1. Maak op je werkstation een CSV-bestand aan, bijvoorbeeld met Microsoft Excel of Open Office Math.
2. Je kunt kiezen om titels in het bestand te plaatsen. Wanneer je afbeeldingen wilt weergeven, zet dan in de kolom de naam van de afbeelding, bijvoorbeeld image.jpg.
3. Upload het CSV-bestand.
4. Bij 'Instellingen' kun je de unieke code (id) bekijken, de bestandsnaam wijzigen, het scheidingsteken aanpassen, kiezen of je titels wilt gebruiken en het de afbeeldingskolom instellen. Wanneer je geen afbeeldingen gebruikt, zet deze dan op 0 (nul).
5. Bij 'Opmaak' kun je de opmaak van het overzicht bepalen. Wijzig ze naar je eigen keuze  en sla ze op.
6. Je kunt afbeeldingen uploaden voor je CSV-bestand. Zorg dat de afbeeldingsnamen overeenkomen met de afbeeldingsnamen in het CSV-bestand.
7. In het overzicht van de afbeeldingen zie je een lijst van de afbeeldingen die bij het betreffende CSV-bestand horen.
8. Klik op 'Voorbeeld' om het bestand te bekijken.
9. Ga naar de WordPress pagina (geen Post) en plaats de verkorte code: [csv id=..]. Geef bij de waarde van id de unieke code aan van het CSV-bestand wat je wilt weergeven.
10. Wil je een CSV-bestand verwijderen, klik dan op 'Verwijderen'.
11. Wil je de plugin verwijderen, zorg dan dat alle CSV-bestanden uit de lijst gewist zijn, klik daarna op Instellingen -> CSV Viewer. De knop 'Deinstalleer plugin' komt tevoorschijn. Klik erop, bevestig het berichtje, deactiveerd de plugin en verwijder hem.

== Changelog ==

= 1.0.2 =

- Debugged: function strstr() with optional parameter replaced. Optional parameter not supported until PHP 5.3.0
- Debugged: Whitespaces cleaned. By actiovation no warning anymore about 'headers already sent' and 'unexpected output'.

= 1.0.1 =

- English en German languages added

== Installation ==
1. Download Csv Viewer
2. Unzip csv_viewer.zip in wp-content/plugins
3. IMPORTANT: make the file 'style.css' in plugins/csv_viewer/css/ writable for the webserver
4. Activate plugin
5. Go in Wordpress to Settings -> CSV Viewer
6. Press Install
7. That's it

NL: 
1. Download Csv Viewer
2. Pak csv_viewer.zip uit in wp-content/plugins
3: BELANGRIJK: maak het bestand 'style.css' in plugins/csv_viewer/css/ beschrijfbaar voor de webserver
4. Activeer de plugin
5. Ga in WordPress naar Instellingen -> CSV Viewer
6. Klik op Installeren
7. Dat was het