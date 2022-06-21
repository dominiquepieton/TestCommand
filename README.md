# TestCommand

Permettre l'éxécution d'une commande qui affichera un tableau d'information à partir d'un flux de données venant d'un fichier CSV.

## Affichage des données :

![Capture tableau1](https://user-images.githubusercontent.com/51318506/174809601-a83b40fa-c0bc-4899-af57-db4ca67b8852.png)

Affichage du Json :

![Capture Json](https://user-images.githubusercontent.com/51318506/174814204-dcd03eee-2146-45fc-b6b9-8702b6531243.png)

### Utilisation des commandes

Pour lancer la commande depuis le terminal, Vous devez taper :
- symfony console ReadFileCsv (argument le nom du fichier.csv) (option Json)
    
Si vous voulez affichez le tableau du dessus la commande sera :
- symfony console ReadFileCsv fichier.csv
    
Si vous voulez afficher du Json à la place du tableau, la commande sera :
- symfony console ReadFileCsv fichier.csv Json

Si vous voulez avoir l'aide :
- symfony console ReadFileCsv --help

#### Technologie utilisée :

  - Symfony 4.4
  - Version PHP supèrieur à 7.1.3
  
