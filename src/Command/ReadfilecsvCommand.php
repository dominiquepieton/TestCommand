<?php

namespace App\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReadfilecsvCommand extends Command
{
    private string $csvDirectory;
    
    protected static $defaultName = 'ReadFileCsv';
    protected static $defaultDescription = 'Add a short description for your command';

    
    public function ___construct(string $csvDirectory)
    {
        $this->csvDirectory = $csvDirectory;
        parent::__construct(self::$defaultName);
    }

    
    protected function configure(): void
    {
        $this->setDescription("Afficher les données d'un fichier CSV")
            ->addArgument('fichier', InputArgument::REQUIRED, 'le nom du fichier.')
            ->addArgument('Json', InputArgument::OPTIONAL, 'Afficher en format Json')
            ->setHelp("La commande pour lire le fichier : ReadFileCsv [Argument: nom du fichier.extension] [option: Json]")
        ;
    }


    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Le fichier <fg=green>'.$input->getArgument('fichier').'</> contient :',
            '=========================================================',
            '',
        ]);
        
        if($input->getArgument('Json'))
        {
            $output->writeln($this->csvToJson($output,$input));
        }else {
            $this->readCsv($output,$input);
        }

        $output->writeln([
            '',
            '=========================================================',
            '',
        ]);
         
    }

    /**
     * Permet de lire un fichier csv et d'afficher les donnée dans un tableau
     *
     * @param [type] $output
     * @return void
     */
    private function readCsv($output, $input): void
    {
        $io = new SymfonyStyle($input, $output);
        // Les extension autorisées
        $extensionValid = ['csv'];

        // Chemin du fichier
        $pathfile = dirname(dirname(__DIR__))."\csv\\".$input->getArgument('fichier');

        // Récupération de l'extension
        $extension = explode(".", $input->getArgument('fichier'));

        // Vérification de l'extension
        if(in_array($extension[1], $extensionValid))
        {
            // Vérification si le fichier existe dans le dossier
            if(file_exists($pathfile)){
                // Ouverture et lecture du fichier .csv
                $file = fopen($pathfile, 'r');

                while (!feof($file) ) {
                    $line[] = fgetcsv($file, 1024);
                }

                // Récuperation des datas deuxieme et troisieme ligne
                $firstline = explode(";",implode(';',$line[1]));
                $secondline = explode(";",implode(';',$line[2]));

                // Convertion du status
                if($firstline[3] == "1"){
                    $statusfirst = "Enable";
                }else {
                    $statusfirst = "Disable";
                }

                if($secondline[2] == "1"){
                    $statussecond = "Enable";
                }else {
                    $statussecond = "Disable";
                }

                // Convertion du point en virgule pour le price et arrondir au chiffre supérieur
                $pricefirst = round(floatval($firstline[4]),1, PHP_ROUND_HALF_EVEN);
                $pricefirstline = str_replace(".",",",$pricefirst.$firstline[5]);
                $pricesecondline = str_replace(".",",",$secondline[3].$secondline[4]);

                // Traitement de la description pour interpréter les balise html
                $first_description = $firstline[6].$firstline[7];
                $second_description = $secondline[5].$secondline[6];

                // formatage de la date 
                $first_date = date('l, d-M-Y H:i:s e', strtotime($firstline[8]));
                $second_date = date('l, d-M-Y H:i:s e', strtotime($secondline[7]));;

                // Création du slug par rapport au titre
                $first_slug = str_replace(" ", "-", $firstline[1].$firstline[2]);
                $second_slug = str_replace(" ", "-", $secondline[1]);

                // Création du tableau des données récupérées
                $data = [
                    [$firstline[0], $statusfirst, $pricefirstline, $first_description, $first_date, $first_slug ],
                    [$secondline[0], $statussecond, $pricesecondline, $second_description, $second_date, $second_slug ]
                ];
            
                // Fermeture du fichier
                fclose($file);

                // Creation du tableau 
                $table = new Table($output);
                $table->setHeaders(['Sku', 'Status', 'Price', 'Description', 'Created At', 'Slug'])
                    ->setRows($data);

                // Rendu du tableau
                $table->render();
                $io->success('Le fichier est correct...');
            
            } else {
                // message d'erreur si le fichier n'existe pas.
                $output->writeln("<error>Le fichier n'existe pas dans le dossier.</error>");
            }
        } 
        else {
            // message d'erreur si l'extension n'existe pas'.
            $output->writeln("<error>L'extension du fichier n'est pas valide seul le csv est autorisé.</error>");
        }   
    }

    /**
     * Permet la création en format Json
     *
     * @param [type] $output
     * @param [type] $input
     * @return void
     */
    private function csvToJson($output,$input)
    {
        $io = new SymfonyStyle($input, $output);
        // Les extension autorisées
        $extensionValid = ['csv'];

        // Chemin du fichier
        $pathfile = dirname(dirname(__DIR__))."\csv\\".$input->getArgument('fichier');

        // Récupération de l'extension
        $extension = explode(".", $input->getArgument('fichier'));

        // Vérification de l'extension
        if(in_array($extension[1], $extensionValid))
        {
            // Vérification si le fichier existe dans le dossier
            if(file_exists($pathfile)){
                // Ouverture et lecture du fichier .csv
                $file = fopen($pathfile, 'r');

                while (!feof($file) ) {
                    $line[] = fgetcsv($file, 1024);
                }

                // Récuperation des datas deuxieme et troisieme ligne
                $firstline = explode(";",implode(';',$line[1]));
                $secondline = explode(";",implode(';',$line[2]));

                // Convertion du status
                if($firstline[3] == "1"){
                    $statusfirst = "Enable";
                }else {
                    $statusfirst = "Disable";
                }

                if($secondline[2] == "1"){
                    $statussecond = "Enable";
                }else {
                    $statussecond = "Disable";
                }

                // Convertion du point en virgule pour le price et arrondir au chiffre supérieur
                $pricefirst = round(floatval($firstline[4]),1, PHP_ROUND_HALF_EVEN);
                $pricefirstline = str_replace(".",",",$pricefirst.$firstline[5]);
                $pricesecondline = str_replace(".",",",$secondline[3].$secondline[4]);

                // Traitement de la description pour interpréter les balise html
                $first_description = $firstline[6].$firstline[7];
                $second_description = $secondline[5].$secondline[6];

                // formatage de la date 
                $first_date = date('l, d-M-Y H:i:s e', strtotime($firstline[8]));
                $second_date = date('l, d-M-Y H:i:s e', strtotime($secondline[7]));;

                // Création du slug par rapport au titre
                $first_slug = str_replace(" ", "-", $firstline[1].$firstline[2]);
                $second_slug = str_replace(" ", "-", $secondline[1]);

                // Création du tableau des données récupérées
                $data = [
                    [
                        "Sku" => $firstline[0],
                        "Status" => $statusfirst,
                        "Price" => $pricefirstline,
                        "Description" => $first_description,
                        "Created At" => $first_date,
                        "Slug" => $first_slug
                    ],
                    [
                        "Sku" => $secondline[0],
                        "Status" => $statussecond,
                        "Price" => $pricesecondline,
                        "Description" => $second_description,
                        "Created At" => $second_date,
                        "Slug" => $second_slug
                    ]
                ];
            
                // Fermeture du fichier
                fclose($file);

                // Creation du JSON 
                $io->success('Le fichier est correct...');
                return json_encode($data);
                
            } else {
                // message d'erreur si le fichier n'existe pas.
                $output->writeln("<error>Le fichier n'existe pas dans le dossier.</error>");
            }
        } 
        else {
            // message d'erreur si l'extension n'existe pas'.
            $output->writeln("<error>L'extension du fichier n'est pas valide seul le csv est autorisé.</error>");
        }
    }
}
