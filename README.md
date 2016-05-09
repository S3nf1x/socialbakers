#Interview task
Cílem úlohy je vytvořit PHP/Node.js script, který bude přijímat jeden vstupní parametr (string) obsahující čárkami oddělené IDs Facebookových stránek (může jich být několik).
```sh
#PHP
php script.php "207251779638,11081890741,517762121588320,10196659501,6597757578,118428791504396,23680604925"
#Node.js
node script.js "207251779638,11081890741,517762121588320,10196659501,6597757578,118428791504396,23680604925"
```
Script musí stáhnout dostupná data o této stránce z Facebook graph API a seřadit je podle vzdušné vzdálenosti od Prahy. Výsledný seznam zobrazit do tabulky, která bude mít následující sloupce:

- název stránky
- město a stát
- počet fanoušků
- počet checkinu


K získání dat z FB API můžeš použít náš access_token:
```
EAABe5eNwsAkBAOipRO75fsERyNDmHgw5BL0GwPNpxaZBnWtLZBoGsi0LZBHeQLTEgXcqTxZBYhuTAchvoJvq6zu5itZCyVz1cu1i4nnC83iFlnxSB2VYsmeTB5VXkEoYSZC1761UFtZA4A0YsNZAiv9ME8bClOSthMAZD
```

Dokončenou úlohu pak odevzdej v git repozitáři (BitBucket, Github, ..).
